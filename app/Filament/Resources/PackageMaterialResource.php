<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageMaterialResource\Pages;
use App\Filament\Resources\PackageMaterialResource\RelationManagers;
use App\Models\PackageMaterial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;

class PackageMaterialResource extends Resource
{
  protected static ?string $model = PackageMaterial::class;

  protected static ?string $navigationIcon = 'heroicon-o-cube';

  protected static ?string $navigationGroup = 'Products';

  protected static ?int $navigationSort = 5;

  protected static ?string $navigationLabel = 'Package Materials';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('Package Material Details')->schema([
          Forms\Components\Select::make('package_id')
            ->label('Package')
            ->relationship('package', 'name')
            ->required()
            ->searchable()
            ->preload(),

          Forms\Components\TextInput::make('name')
            ->label('Material Name')
            ->required()
            ->maxLength(255)
            ->placeholder('e.g., Leather, Wood, Corrugated Carton'),

          Forms\Components\TextInput::make('price_modifier')
            ->label('Price Modifier (₦)')
            ->required()
            ->numeric()
            ->prefix('₦')
            ->default(0)
            ->step(0.01)
            ->helperText('Additional cost for this material option'),

          Forms\Components\KeyValue::make('options')
            ->label('Material Options')
            ->keyLabel('Option Name')
            ->valueLabel('Option Value')
            ->nullable()
            ->helperText('Additional properties for this material (e.g., thickness: 2mm, finish: glossy)')
            ->columnSpanFull(),

          FileUpload::make('image')
            ->label('Material Image')
            ->image()
            ->directory('package-materials')
            ->maxSize(2048)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
            ->helperText('Upload an image that will be applied as texture to the 3D box')
            ->columnSpanFull(),
        ])->columns(3),
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
          ->label('Material Name')
          ->searchable()
          ->sortable()
          ->limit(30),

        ImageColumn::make('image')
          ->label('Material Image')
          ->circular()
          ->size(40)
          ->toggleable(),

        Tables\Columns\TextColumn::make('price_modifier')
          ->label('Price Modifier')
          ->money('NGN')
          ->sortable(),

        Tables\Columns\TextColumn::make('options')
          ->label('Options')
          ->badge()
          ->formatStateUsing(fn($state) => is_array($state) ? count($state) . ' options' : 'No options')
          ->color('info')
          ->toggleable(),

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
      'index' => Pages\ListPackageMaterials::route('/'),
      'create' => Pages\CreatePackageMaterial::route('/create'),
      'edit' => Pages\EditPackageMaterial::route('/{record}/edit'),
      'view' => Pages\ViewPackageMaterial::route('/{record}/view'),
    ];
  }
}
