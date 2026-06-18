<?php

namespace App\Filament\Resources\PackageResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ColorsRelationManager extends RelationManager
{
  protected static string $relationship = 'colors';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('name')
          ->required()
          ->maxLength(255)
          ->placeholder('e.g., Royal Blue'),

        Forms\Components\TextInput::make('hex')
          ->label('Hex Color Code')
          ->maxLength(7)
          ->placeholder('#123456')
          ->regex('/^#[0-9A-Fa-f]{6}$/')
          ->helperText('Format: #RRGGBB'),

        Forms\Components\TextInput::make('price_modifier')
          ->label('Price Modifier (₦)')
          ->required()
          ->numeric()
          ->prefix('₦')
          ->default(0)
          ->step(0.01),
      ]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('Color Name')
          ->searchable()
          ->sortable(),

        Tables\Columns\ColorColumn::make('hex')
          ->label('Color')
          ->copyable(),

        Tables\Columns\TextColumn::make('price_modifier')
          ->label('Price Modifier')
          ->money('NGN')
          ->sortable(),

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
