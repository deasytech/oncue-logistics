<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentReceiptResource\Pages;
use App\Filament\Resources\PaymentReceiptResource\RelationManagers;
use App\Models\PaymentReceipt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class PaymentReceiptResource extends Resource
{
  protected static ?string $model = PaymentReceipt::class;

  protected static ?string $navigationIcon = 'heroicon-o-document-text';

  protected static ?string $navigationGroup = 'Payments';

  protected static ?int $navigationSort = 3;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('Receipt Information')
          ->schema([
            Forms\Components\TextInput::make('customer.full_name')
              ->label('Customer')
              ->disabled()
              ->default(fn($record) => $record?->customer?->full_name),

            Forms\Components\TextInput::make('original_name')
              ->label('File Name')
              ->disabled(),

            Forms\Components\TextInput::make('file_size_formatted')
              ->label('File Size')
              ->disabled()
              ->default(fn($record) => $record?->file_size_formatted),

            Forms\Components\TextInput::make('description')
              ->label('Description')
              ->disabled()
              ->columnSpanFull(),

            Forms\Components\FileUpload::make('file_path')
              ->label('Receipt File')
              ->disabled()
              ->downloadable()
              ->openable()
              ->previewable(true)
              ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/jpg', 'image/png']),

            Forms\Components\Select::make('status')
              ->label('Status')
              ->options([
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
              ])
              ->required()
              ->reactive(),

            Forms\Components\Textarea::make('admin_notes')
              ->label('Admin Notes')
              ->placeholder('Add notes about the approval/rejection decision')
              ->columnSpanFull()
              ->maxLength(1000),
          ])
          ->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('customer.full_name')
          ->label('Customer')
          ->searchable()
          ->sortable(),

        Tables\Columns\TextColumn::make('original_name')
          ->label('File Name')
          ->searchable()
          ->limit(30),

        Tables\Columns\TextColumn::make('file_size_formatted')
          ->label('File Size')
          ->sortable(),

        Tables\Columns\TextColumn::make('status')
          ->badge()
          ->colors([
            'warning' => 'pending',
            'success' => 'approved',
            'danger' => 'rejected',
          ]),

        Tables\Columns\TextColumn::make('created_at')
          ->label('Uploaded')
          ->dateTime()
          ->sortable(),

        Tables\Columns\TextColumn::make('admin_notes')
          ->limit(30)
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('status')
          ->options([
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
          ]),

        Tables\Filters\Filter::make('has_admin_notes')
          ->label('Has Admin Notes')
          ->query(fn(Builder $query): Builder => $query->whereNotNull('admin_notes')->where('admin_notes', '!=', '')),
      ])
      ->actions([
        ActionGroup::make([
          Tables\Actions\EditAction::make()
            ->label('Review')
            ->icon('heroicon-o-eye')
            ->color('primary'),

          Tables\Actions\Action::make('download')
            ->label('Download')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->action(function (PaymentReceipt $record) {
              return Storage::disk('public')->download($record->file_path, $record->original_name);
            }),

          Tables\Actions\DeleteAction::make(),
        ])
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\BulkAction::make('approve')
            ->label('Approve Selected')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
              foreach ($records as $record) {
                $record->update(['status' => 'approved']);
              }
            })
            ->requiresConfirmation()
            ->deselectRecordsAfterCompletion(),

          Tables\Actions\BulkAction::make('reject')
            ->label('Reject Selected')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
              foreach ($records as $record) {
                $record->update(['status' => 'rejected']);
              }
            })
            ->requiresConfirmation()
            ->deselectRecordsAfterCompletion(),

          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ])
      ->defaultSort('created_at', 'desc');
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()->with(['customer']);
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
      'index' => Pages\ListPaymentReceipts::route('/'),
      'edit' => Pages\EditPaymentReceipt::route('/{record}/edit'),
    ];
  }
}
