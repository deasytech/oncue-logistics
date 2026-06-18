<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Mail\InvoiceNotification;
use App\Models\Customer;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class InvoiceResource extends Resource
{
  protected static ?string $model = Invoice::class;

  protected static ?string $navigationIcon = 'heroicon-o-document-text';

  protected static ?string $navigationGroup = 'Payments';

  protected static ?int $navigationSort = 2;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Customer Information')
          ->schema([
            Forms\Components\Select::make('customer_id')
              ->label('Customer')
              ->options(Customer::all()->pluck('full_name', 'id'))
              ->searchable()
              ->required()
              ->live()
              ->afterStateUpdated(function ($state, callable $set) {
                if ($state) {
                  $customer = Customer::find($state);
                  if ($customer) {
                    $set('customer_name', $customer->full_name);
                    $set('customer_email', $customer->email);
                    $set('customer_phone', $customer->phone);
                    $set('customer_address', $customer->address);
                  }
                }
              }),
            Forms\Components\TextInput::make('customer_name')
              ->label('Customer Name')
              ->required(),
            Forms\Components\TextInput::make('customer_email')
              ->label('Customer Email')
              ->email()
              ->required(),
            Forms\Components\TextInput::make('customer_phone')
              ->label('Customer Phone')
              ->tel(),
            Forms\Components\Textarea::make('customer_address')
              ->label('Customer Address')
              ->columnSpanFull(),
          ])
          ->columns(2),

        Section::make('Invoice Details')
          ->schema([
            Forms\Components\TextInput::make('invoice_number')
              ->label('Invoice Number')
              ->default(fn() => 'INV-' . strtoupper(uniqid()))
              ->required()
              ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('title')
              ->label('Invoice Title')
              ->placeholder('e.g., Event Logistics Services'),
            Forms\Components\Textarea::make('description')
              ->label('Description')
              ->placeholder('Brief description of the invoice')
              ->columnSpanFull(),
            Forms\Components\DateTimePicker::make('due_date')
              ->label('Due Date')
              ->native(false),
          ])
          ->columns(2),

        Section::make('Invoice Items')
          ->schema([
            Repeater::make('items')
              ->relationship()
              ->schema([
                Forms\Components\Textarea::make('description')
                  ->label('Description')
                  ->required()
                  ->rows(2),
                Forms\Components\TextInput::make('quantity')
                  ->label('Quantity')
                  ->numeric()
                  ->default(1)
                  ->required()
                  ->live(onBlur: true)
                  ->afterStateUpdated(function ($state, Set $set, Get $get) {
                    $unitPrice = $get('unit_price') ?? 0;
                    $set('amount', $state * $unitPrice);
                    // Recalculate parent totals
                    $items = $get('../../items');
                    $subtotal = collect($items)->sum('amount');
                    $set('../../subtotal', $subtotal);
                    $tax = $get('../../tax_amount') ?? 0;
                    $discount = $get('../../discount_amount') ?? 0;
                    $set('../../total_amount', $subtotal + $tax - $discount);
                  }),
                Forms\Components\TextInput::make('unit_price')
                  ->label('Unit Price (₦)')
                  ->numeric()
                  ->prefix('₦')
                  ->required()
                  ->live(onBlur: true)
                  ->afterStateUpdated(function ($state, Set $set, Get $get) {
                    $quantity = $get('quantity') ?? 1;
                    $set('amount', $state * $quantity);
                    // Recalculate parent totals
                    $items = $get('../../items');
                    $subtotal = collect($items)->sum('amount');
                    $set('../../subtotal', $subtotal);
                    $tax = $get('../../tax_amount') ?? 0;
                    $discount = $get('../../discount_amount') ?? 0;
                    $set('../../total_amount', $subtotal + $tax - $discount);
                  }),
                Forms\Components\TextInput::make('amount')
                  ->label('Amount (₦)')
                  ->numeric()
                  ->prefix('₦')
                  ->disabled()
                  ->dehydrated(),
              ])
              ->columns(4)
              ->addActionLabel('Add Item')
              ->reorderableWithButtons()
              ->collapsible()
              ->defaultItems(1)
              ->live(onBlur: true)
              ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $subtotal = collect($state)->sum('amount');
                $set('subtotal', $subtotal);
                $tax = $get('tax_amount') ?? 0;
                $discount = $get('discount_amount') ?? 0;
                $set('total_amount', $subtotal + $tax - $discount);
              }),
          ]),

        Section::make('Invoice Summary')
          ->schema([
            Forms\Components\TextInput::make('subtotal')
              ->label('Subtotal (₦)')
              ->numeric()
              ->prefix('₦')
              ->disabled()
              ->dehydrated(),
            Forms\Components\TextInput::make('tax_amount')
              ->label('Tax Amount (₦)')
              ->numeric()
              ->prefix('₦')
              ->default(0)
              ->live(onBlur: true)
              ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $subtotal = $get('subtotal') ?? 0;
                $discount = $get('discount_amount') ?? 0;
                $set('total_amount', $subtotal + $state - $discount);
              }),
            Forms\Components\TextInput::make('discount_amount')
              ->label('Discount Amount (₦)')
              ->numeric()
              ->prefix('₦')
              ->default(0)
              ->live(onBlur: true)
              ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $subtotal = $get('subtotal') ?? 0;
                $tax = $get('tax_amount') ?? 0;
                $set('total_amount', $subtotal + $tax - $state);
              }),
            Forms\Components\TextInput::make('total_amount')
              ->label('Total Amount (₦)')
              ->numeric()
              ->prefix('₦')
              ->disabled()
              ->dehydrated(),
          ])
          ->columns(4),

        Section::make('Additional Information')
          ->schema([
            Forms\Components\Textarea::make('notes')
              ->label('Notes')
              ->placeholder('Additional notes for the customer')
              ->columnSpanFull(),
            Forms\Components\Textarea::make('footer_notes')
              ->label('Footer Notes')
              ->placeholder('Notes to appear at the bottom of the invoice')
              ->columnSpanFull(),
          ]),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('invoice_number')
          ->label('Invoice #')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('customer_name')
          ->label('Customer')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('customer_email')
          ->label('Email')
          ->searchable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('title')
          ->label('Title')
          ->searchable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('total_amount')
          ->label('Total')
          ->money('NGN')
          ->sortable(),
        Tables\Columns\TextColumn::make('amount_paid')
          ->label('Paid')
          ->money('NGN')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('balance_due')
          ->label('Balance Due')
          ->money('NGN')
          ->sortable(),
        Tables\Columns\BadgeColumn::make('status')
          ->colors([
            'gray' => 'draft',
            'warning' => 'sent',
            'success' => 'paid',
            'danger' => 'overdue',
            'secondary' => 'cancelled',
          ]),
        Tables\Columns\BadgeColumn::make('payment_status')
          ->label('Payment')
          ->colors([
            'warning' => 'pending',
            'primary' => 'processing',
            'success' => 'completed',
            'danger' => 'failed',
            'secondary' => 'refunded',
          ]),
        Tables\Columns\TextColumn::make('due_date')
          ->label('Due Date')
          ->date()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('sent_at')
          ->label('Sent At')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Created')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('status')
          ->options([
            'draft' => 'Draft',
            'sent' => 'Sent',
            'paid' => 'Paid',
            'overdue' => 'Overdue',
            'cancelled' => 'Cancelled',
          ]),
        Tables\Filters\SelectFilter::make('payment_status')
          ->label('Payment Status')
          ->options([
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
          ]),
      ])
      ->actions([
        ActionGroup::make([
          Tables\Actions\ViewAction::make(),
          Tables\Actions\EditAction::make(),
          Tables\Actions\Action::make('send')
            ->label('Send Invoice')
            ->icon('heroicon-o-paper-airplane')
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading('Send Invoice to Customer')
            ->modalDescription('This will email the invoice to the customer with a secure payment link.')
            ->action(function (Invoice $record) {
              // Generate payment token
              $record->generatePaymentToken();

              // Mark as sent
              $record->markAsSent();

              // Send email
              Mail::to($record->customer_email)->send(new InvoiceNotification($record));

              Notification::make()
                ->title('Invoice sent successfully')
                ->body("The invoice has been sent to {$record->customer_email}")
                ->success()
                ->send();
            })
            ->visible(fn(Invoice $record) => $record->status === 'draft'),
          Tables\Actions\Action::make('resend')
            ->label('Resend Invoice')
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Resend Invoice')
            ->modalDescription('Resend the invoice email to the customer.')
            ->action(function (Invoice $record) {
              // Regenerate token if expired
              if (!$record->isTokenValid($record->payment_token)) {
                $record->generatePaymentToken();
              }

              // Send email
              Mail::to($record->customer_email)->send(new InvoiceNotification($record));

              Notification::make()
                ->title('Invoice resent successfully')
                ->body("The invoice has been resent to {$record->customer_email}")
                ->success()
                ->send();
            })
            ->visible(fn(Invoice $record) => $record->status !== 'draft'),
          Tables\Actions\Action::make('markPaid')
            ->label('Mark as Paid')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Mark Invoice as Paid')
            ->modalDescription('Manually mark this invoice as paid.')
            ->form([
              Forms\Components\TextInput::make('transaction_id')
                ->label('Transaction ID (Optional)')
                ->placeholder('Enter payment transaction ID'),
            ])
            ->action(function (Invoice $record, array $data) {
              $record->markAsPaid($data['transaction_id'] ?? null);

              Notification::make()
                ->title('Invoice marked as paid')
                ->success()
                ->send();
            })
            ->visible(fn(Invoice $record) => $record->payment_status !== 'completed'),
          Tables\Actions\DeleteAction::make(),
        ]),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ])
      ->defaultSort('created_at', 'desc');
  }

  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListInvoices::route('/'),
      'create' => Pages\CreateInvoice::route('/create'),
      'view' => Pages\ViewInvoice::route('/{record}'),
      'edit' => Pages\EditInvoice::route('/{record}/edit'),
    ];
  }
}
