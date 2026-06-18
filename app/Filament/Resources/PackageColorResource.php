<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageColorResource\Pages;
use App\Filament\Resources\PackageColorResource\RelationManagers;
use App\Models\PackageColor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackageColorResource extends Resource
{
  protected static ?string $model = PackageColor::class;

  protected static ?string $navigationIcon = 'heroicon-o-swatch';

  protected static ?string $navigationGroup = 'Products';

  protected static ?int $navigationSort = 4;

  protected static ?string $navigationLabel = 'Package Colors';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('Package Color Details')->schema([
          Forms\Components\Select::make('package_id')
            ->label('Package')
            ->relationship('package', 'name')
            ->required()
            ->searchable()
            ->preload(),

          Forms\Components\TextInput::make('name')
            ->label('Color Name')
            ->required()
            ->maxLength(255)
            ->placeholder('e.g., Royal Blue, Gold, Silver'),

          Forms\Components\ColorPicker::make('hex')
            ->label('Hex Color Code')
            ->required()
            ->helperText('Select the color or enter hex code (e.g., #123456)'),

          Forms\Components\TextInput::make('price_modifier')
            ->label('Price Modifier (₦)')
            ->required()
            ->numeric()
            ->prefix('₦')
            ->default(0)
            ->step(0.01)
            ->helperText('Additional cost for this color option'),
        ])->columns(4),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('package.name')
          ->label('Package')
          ->searchable()
          ->sortable()
          ->limit(30),

        Tables\Columns\TextColumn::make('name')
          ->label('Color Name')
          ->searchable()
          ->sortable()
          ->limit(30),

        Tables\Columns\ColorColumn::make('hex')
          ->label('Color')
          ->copyable(),

        Tables\Columns\TextColumn::make('hex')
          ->label('Hex Code')
          ->copyable()
          ->toggleable()
          ->toggleable(isToggledHiddenByDefault: true),

        Tables\Columns\TextColumn::make('price_modifier')
          ->label('Price Modifier')
          ->money('NGN')
          ->sortable(),

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
        Tables\Filters\SelectFilter::make('package')
          ->relationship('package', 'name')
          ->searchable()
          ->preload(),
      ])
      ->actions([
        ActionGroup::make([
          Tables\Actions\ViewAction::make(),
          Tables\Actions\EditAction::make(),
          Tables\Actions\DeleteAction::make(),
        ])
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
      'index' => Pages\ListPackageColors::route('/'),
      'create' => Pages\CreatePackageColor::route('/create'),
      'edit' => Pages\EditPackageColor::route('/{record}/edit'),
      'view' => Pages\ViewPackageColor::route('/{record}/view'),
    ];
  }
}
