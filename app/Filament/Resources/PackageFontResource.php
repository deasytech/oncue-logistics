<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageFontResource\Pages;
use App\Filament\Resources\PackageFontResource\RelationManagers;
use App\Models\PackageFont;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackageFontResource extends Resource
{
  protected static ?string $model = PackageFont::class;

  protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

  protected static ?string $navigationGroup = 'Products';

  protected static ?int $navigationSort = 6;

  protected static ?string $navigationLabel = 'Package Fonts';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('Package Font Details')->schema([
          Forms\Components\Select::make('package_id')
            ->label('Package')
            ->relationship('package', 'name')
            ->required()
            ->searchable()
            ->preload()
            ->columnSpan(3),

          Forms\Components\TextInput::make('name')
            ->label('Font Name')
            ->required()
            ->maxLength(255)
            ->placeholder('e.g., Serif, Script, Bold, Modern')
            ->columnSpan(4),

          Forms\Components\TextInput::make('google_font_family')
            ->label('Google Font Family')
            ->maxLength(255)
            ->placeholder('e.g., Roboto, Open Sans, Playfair Display')
            ->helperText('Optional: Google Fonts family name for frontend display')
            ->columnSpan(3),

          Forms\Components\TextInput::make('price_modifier')
            ->label('Price Modifier (₦)')
            ->required()
            ->numeric()
            ->prefix('₦')
            ->default(0)
            ->step(0.01)
            ->helperText('Additional cost for this font option')
            ->columnSpan(2),
        ])->columns(12),
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
          ->label('Font Name')
          ->searchable()
          ->sortable()
          ->limit(30),

        Tables\Columns\TextColumn::make('google_font_family')
          ->label('Google Font Family')
          ->searchable()
          ->sortable()
          ->limit(30)
          ->toggleable(),

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
      'index' => Pages\ListPackageFonts::route('/'),
      'create' => Pages\CreatePackageFont::route('/create'),
      'edit' => Pages\EditPackageFont::route('/{record}/edit'),
      'view' => Pages\ViewPackageFont::route('/{record}/view'),
    ];
  }
}
