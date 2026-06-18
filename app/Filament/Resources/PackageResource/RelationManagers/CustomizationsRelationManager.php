<?php

namespace App\Filament\Resources\PackageResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CustomizationsRelationManager extends RelationManager
{
  protected static string $relationship = 'customizations';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('customer_id')
          ->label('Customer')
          ->relationship('customer', 'first_name')
          ->searchable()
          ->preload()
          ->required()
          ->getOptionLabelFromRecordUsing(fn($record) => "{$record->title} {$record->first_name} {$record->last_name}"),

        Forms\Components\Select::make('material_id')
          ->label('Material')
          ->relationship('material', 'name')
          ->searchable()
          ->preload()
          ->nullable(),

        Forms\Components\Select::make('font_id')
          ->label('Font')
          ->relationship('font', 'name')
          ->searchable()
          ->preload()
          ->nullable(),

        Forms\Components\Select::make('color_id')
          ->label('Color')
          ->relationship('color', 'name')
          ->searchable()
          ->preload()
          ->nullable(),

        Forms\Components\TextInput::make('message')
          ->maxLength(255)
          ->placeholder('Custom message or text'),

        Forms\Components\TextInput::make('location')
          ->maxLength(255)
          ->placeholder('Delivery/pickup location'),

        Forms\Components\TextInput::make('quantity')
          ->required()
          ->numeric()
          ->default(1)
          ->minValue(1),

        Forms\Components\TextInput::make('unit_price')
          ->label('Unit Price (₦)')
          ->required()
          ->numeric()
          ->prefix('₦')
          ->default(0)
          ->step(0.01),

        Forms\Components\TextInput::make('total_price')
          ->label('Total Price (₦)')
          ->required()
          ->numeric()
          ->prefix('₦')
          ->default(0)
          ->step(0.01),

        Forms\Components\Select::make('status')
          ->required()
          ->options([
            'draft' => 'Draft',
            'in_cart' => 'In Cart',
            'ordered' => 'Ordered',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
          ])
          ->default('draft'),
      ]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('customer.full_name')
          ->label('Customer')
          ->searchable()
          ->sortable(),

        Tables\Columns\TextColumn::make('material.name')
          ->label('Material')
          ->toggleable(),

        Tables\Columns\TextColumn::make('font.name')
          ->label('Font')
          ->toggleable(),

        Tables\Columns\TextColumn::make('color.name')
          ->label('Color')
          ->toggleable(),

        Tables\Columns\TextColumn::make('quantity')
          ->numeric()
          ->sortable(),

        Tables\Columns\TextColumn::make('unit_price')
          ->label('Unit Price')
          ->money('NGN')
          ->sortable(),

        Tables\Columns\TextColumn::make('total_price')
          ->label('Total Price')
          ->money('NGN')
          ->sortable(),

        Tables\Columns\TextColumn::make('status')
          ->badge()
          ->color(fn(string $state): string => match ($state) {
            'draft' => 'gray',
            'in_cart' => 'warning',
            'ordered' => 'info',
            'paid' => 'success',
            'cancelled' => 'danger',
          })
          ->sortable(),

        Tables\Columns\TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('status')
          ->options([
            'draft' => 'Draft',
            'in_cart' => 'In Cart',
            'ordered' => 'Ordered',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
          ]),

        Tables\Filters\Filter::make('has_material')
          ->label('Has Material')
          ->query(fn($query) => $query->whereNotNull('material_id')),

        Tables\Filters\Filter::make('has_font')
          ->label('Has Font')
          ->query(fn($query) => $query->whereNotNull('font_id')),

        Tables\Filters\Filter::make('has_color')
          ->label('Has Color')
          ->query(fn($query) => $query->whereNotNull('color_id')),
      ])
      ->headerActions([
        Tables\Actions\CreateAction::make(),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }
}
