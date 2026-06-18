<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackagePaymentResource\Pages;
use App\Models\PaymentRecord;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;

class PackagePaymentResource extends Resource
{
  protected static ?string $model = PaymentRecord::class;

  protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

  protected static ?string $navigationGroup = 'Payments';

  protected static ?int $navigationSort = 1;

  protected static ?string $slug = 'payments';

  public static function getNavigationLabel(): string
  {
    return 'Payments';
  }

  public static function getPluralModelLabel(): string
  {
    return 'Payments';
  }

  public static function getModelLabel(): string
  {
    return 'Payment';
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Payment Information')
          ->schema([
            Forms\Components\TextInput::make('type')
              ->disabled()
              ->formatStateUsing(fn(?string $state) => ucfirst((string) $state)),
            Forms\Components\TextInput::make('payer_name')
              ->label('Payer Name')
              ->disabled(),
            Forms\Components\TextInput::make('payer_email')
              ->label('Payer Email')
              ->disabled(),
            Forms\Components\TextInput::make('reference')
              ->label('Payment Reference')
              ->disabled(),
            Forms\Components\TextInput::make('amount')
              ->prefix('₦')
              ->label('Amount (₦)')
              ->disabled(),
            Forms\Components\Select::make('status')
              ->options([
                'pending' => 'Pending',
                'success' => 'Success',
                'failed' => 'Failed',
              ])
              ->label('Payment Status')
              ->disabled(),
            Forms\Components\TextInput::make('payment_method')
              ->label('Payment Method')
              ->disabled()
              ->formatStateUsing(fn(?string $state) => ucfirst((string) $state)),
          ])
          ->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('id')
          ->label('ID')
          ->getStateUsing(fn($record) => $record->source_id)
          ->sortable(),

        Tables\Columns\TextColumn::make('type')
          ->label('Type')
          ->formatStateUsing(fn(string $state) => ucfirst($state))
          ->colors([
            'primary' => 'package',
            'warning' => 'fabric',
          ])
          ->sortable(),

        Tables\Columns\TextColumn::make('payer_name')
          ->label('Payer')
          ->searchable()
          ->sortable(),

        Tables\Columns\TextColumn::make('payer_email')
          ->label('Email')
          ->searchable()
          ->sortable(),

        Tables\Columns\TextColumn::make('reference')
          ->label('Reference')
          ->searchable()
          ->copyable()
          ->tooltip('Click to copy'),

        Tables\Columns\TextColumn::make('amount')
          ->label('Amount')
          ->money('NGN')
          ->sortable(),

        Tables\Columns\TextColumn::make('status')
          ->label('Status')
          ->colors([
            'warning' => 'pending',
            'success' => 'success',
            'danger' => 'failed',
          ])
          ->sortable(),

        Tables\Columns\TextColumn::make('created_at')
          ->label('Created At')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),

        Tables\Columns\TextColumn::make('updated_at')
          ->label('Updated At')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('type')
          ->options([
            'package' => 'Package Payment',
            'fabric' => 'Fabric Payment',
          ])
          ->label('Type'),

        Tables\Filters\SelectFilter::make('status')
          ->options([
            'pending' => 'Pending',
            'success' => 'Success',
            'failed' => 'Failed',
          ])
          ->label('Payment Status'),

        Tables\Filters\SelectFilter::make('payment_method')
          ->options([
            'online' => 'Online',
            'offline' => 'Offline',
          ])
          ->label('Payment Method'),
      ])
      ->actions([
        ActionGroup::make([
          Tables\Actions\ViewAction::make(),
          Tables\Actions\DeleteAction::make(),
        ])
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
      'index' => Pages\ListPackagePayments::route('/'),
    ];
  }
}
