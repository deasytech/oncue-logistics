<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FabricTypeResource\Pages;
use App\Filament\Resources\FabricTypeResource\RelationManagers;
use App\Models\FabricType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FabricTypeResource extends Resource
{
  protected static ?string $model = FabricType::class;

  protected static ?string $navigationIcon = 'heroicon-o-gift';

  protected static ?string $navigationGroup = 'System';

  protected static ?int $navigationSort = 3;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make()
          ->schema([
            Forms\Components\TextInput::make('name')
              ->required()
              ->maxLength(255),
            Forms\Components\Textarea::make('description')
              ->maxLength(65535)
              ->columnSpanFull(),
            Forms\Components\TextInput::make('base_price')
              ->required()
              ->numeric()
              ->prefix('₦')
              ->default(0.00),
            Forms\Components\Toggle::make('is_active')
              ->required()
              ->default(true),
          ])
          ->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->searchable(),
        Tables\Columns\TextColumn::make('description')
          ->limit(50)
          ->searchable(),
        Tables\Columns\TextColumn::make('base_price')
          ->money('NGN')
          ->sortable(),
        Tables\Columns\IconColumn::make('is_active')
          ->boolean(),
        Tables\Columns\TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\TrashedFilter::make(),
        Tables\Filters\TernaryFilter::make('is_active')
          ->label('Active'),
      ])
      ->actions([
        ActionGroup::make([
          Tables\Actions\ViewAction::make(),
          Tables\Actions\EditAction::make(),
          Tables\Actions\DeleteAction::make(),
        ]),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
          Tables\Actions\ForceDeleteBulkAction::make(),
          Tables\Actions\RestoreBulkAction::make(),
        ]),
      ]);
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
      'index' => Pages\ListFabricTypes::route('/'),
      'create' => Pages\CreateFabricType::route('/create'),
      'edit' => Pages\EditFabricType::route('/{record}/edit'),
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->withoutGlobalScopes([
        SoftDeletingScope::class,
      ]);
  }
}
