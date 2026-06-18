<?php

namespace App\Filament\Resources\PackageResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MaterialsRelationManager extends RelationManager
{
  protected static string $relationship = 'materials';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('name')
          ->required()
          ->maxLength(255)
          ->placeholder('e.g., Leather, Wood, Corrugated Carton'),

        Forms\Components\TextInput::make('price_modifier')
          ->label('Price Modifier (₦)')
          ->required()
          ->numeric()
          ->prefix('₦')
          ->default(0)
          ->step(0.01),

        Forms\Components\KeyValue::make('options')
          ->label('Material Options')
          ->keyLabel('Option Name')
          ->valueLabel('Option Value')
          ->nullable(),
      ])->columns(3);
  }

  public function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('Material Name')
          ->searchable()
          ->sortable(),

        Tables\Columns\TextColumn::make('price_modifier')
          ->label('Price Modifier')
          ->money('NGN')
          ->sortable(),

        Tables\Columns\TextColumn::make('options')
          ->label('Options')
          ->formatStateUsing(fn($state) => is_array($state) ? count($state) . ' options' : 'No options')
          ->toggleable(),

        Tables\Columns\TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        //
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
