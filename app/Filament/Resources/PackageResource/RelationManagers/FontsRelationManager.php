<?php

namespace App\Filament\Resources\PackageResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FontsRelationManager extends RelationManager
{
  protected static string $relationship = 'fonts';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('name')
          ->required()
          ->maxLength(255)
          ->placeholder('e.g., Serif, Script, Bold'),

        Forms\Components\TextInput::make('google_font_family')
          ->label('Google Font Family')
          ->maxLength(255)
          ->placeholder('e.g., Roboto, Open Sans')
          ->helperText('Optional: For frontend display'),

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
          ->label('Font Name')
          ->searchable()
          ->sortable(),

        Tables\Columns\TextColumn::make('google_font_family')
          ->label('Google Font Family')
          ->searchable()
          ->toggleable(),

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
