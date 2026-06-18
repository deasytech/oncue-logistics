<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Models\Package;
use App\Models\PackageColor;
use App\Models\PackageFont;
use App\Models\PackageMaterial;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PackageResource extends Resource
{
  protected static ?string $model = Package::class;

  protected static ?string $navigationIcon = 'heroicon-o-gift';
  protected static ?string $navigationGroup = 'Products';
  protected static ?int $navigationSort = 1;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        // ------------------------
        // PACKAGE DETAILS SECTION
        // ------------------------
        Section::make('Package Details')->schema([
          Grid::make(12)->schema([
            Forms\Components\TextInput::make('name')
              ->label('Package Name')
              ->required()
              ->maxLength(255)
              ->placeholder('e.g., Aso Ebi Delivery Box')
              ->columnSpan(10),

            Forms\Components\Toggle::make('is_active')
              ->label('Active')
              ->inline(false)
              ->default(true)
              ->columnSpan(2),
          ]),

          Grid::make(2)->schema([
            Forms\Components\Textarea::make('description')
              ->label('Description')
              ->rows(3)
              ->placeholder('Describe the package features and benefits'),

            Forms\Components\FileUpload::make('cover_image')
              ->label('Cover Image')
              ->image()
              ->maxSize(2048)
              ->disk('public')
              ->directory('package-images')
              ->required(),
          ]),

          Grid::make(3)->schema([
            Forms\Components\TextInput::make('base_price')
              ->label('Base Price (₦)')
              ->required()
              ->numeric()
              ->prefix('₦')
              ->default(0)
              ->step(0.01),

            Forms\Components\TextInput::make('sku')
              ->label('SKU')
              ->unique(ignoreRecord: true)
              ->readonly()
              ->default(fn() => 'PKG-' . strtoupper(substr(md5(uniqid()), 0, 8))),

            Forms\Components\KeyValue::make('metadata')
              ->label('Additional Settings')
              ->keyLabel('Setting Name')
              ->valueLabel('Setting Value')
              ->columnSpan(1),
          ]),
        ])->columnSpan(2),

        // ------------------------
        // MATERIALS SECTION
        // ------------------------
        Section::make('Package Materials')->schema([
          Repeater::make('materials')
            ->relationship('materials')
            ->schema([
              Select::make('material_id')
                ->label('Select Existing Material')
                ->options(PackageMaterial::pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->helperText('Choose from existing materials or leave blank to add new.')
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                  if ($state) {
                    $material = PackageMaterial::find($state);
                    if ($material) {
                      $set('name', $material->name);
                      $set('price_modifier', $material->price_modifier);
                      $set('options', $material->options);
                    }
                  }
                }),

              Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->placeholder('e.g., Leather, Wood, Carton'),

              Forms\Components\TextInput::make('price_modifier')
                ->label('Price Modifier (₦)')
                ->numeric()
                ->prefix('₦')
                ->default(0)
                ->step(0.01),

              Forms\Components\KeyValue::make('options')
                ->label('Material Options')
                ->keyLabel('Option Name')
                ->valueLabel('Option Value')
                ->nullable()
                ->columnSpanFull(),
            ])
            ->columns(2)
            ->cloneable()
            ->collapsible()
            ->addActionLabel('Add Material'),
        ])->columnSpan(2),

        // ------------------------
        // FONTS SECTION
        // ------------------------
        Section::make('Package Fonts')->schema([
          Repeater::make('fonts')
            ->relationship('fonts')
            ->schema([
              Select::make('font_id')
                ->label('Select Existing Font')
                ->options(PackageFont::pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->helperText('Choose from existing fonts or create a new one.')
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                  if ($state) {
                    $font = PackageFont::find($state);
                    if ($font) {
                      $set('name', $font->name);
                      $set('google_font_family', $font->google_font_family);
                      $set('price_modifier', $font->price_modifier);
                    }
                  }
                }),

              Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->placeholder('e.g., Roboto, Playfair'),

              Forms\Components\TextInput::make('google_font_family')
                ->label('Google Font Family')
                ->maxLength(255)
                ->placeholder('e.g., Roboto, Open Sans'),

              Forms\Components\TextInput::make('price_modifier')
                ->label('Price Modifier (₦)')
                ->numeric()
                ->prefix('₦')
                ->default(0)
                ->step(0.01),
            ])
            ->columns(3)
            ->cloneable()
            ->collapsible()
            ->addActionLabel('Add Font'),
        ]),

        // ------------------------
        // COLORS SECTION
        // ------------------------
        Section::make('Package Colors')->schema([
          Repeater::make('colors')
            ->relationship('colors')
            ->schema([
              Select::make('color_id')
                ->label('Select Existing Color')
                ->options(PackageColor::pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->helperText('Pick from saved colors or add new below.')
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                  if ($state) {
                    $color = PackageColor::find($state);
                    if ($color) {
                      $set('name', $color->name);
                      $set('hex', $color->hex);
                      $set('price_modifier', $color->price_modifier);
                    }
                  }
                }),

              Forms\Components\TextInput::make('name')
                ->required()
                ->placeholder('e.g., Royal Blue'),

              Forms\Components\ColorPicker::make('hex')
                ->label('Hex Color')
                ->placeholder('#123456'),

              Forms\Components\TextInput::make('price_modifier')
                ->label('Price Modifier (₦)')
                ->numeric()
                ->prefix('₦')
                ->default(0)
                ->step(0.01),
            ])
            ->columns(3)
            ->cloneable()
            ->collapsible()
            ->addActionLabel('Add Color'),
        ]),
      ])->columns(1);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')->label('Package Name')->searchable()->sortable(),
        Tables\Columns\TextColumn::make('base_price')->label('Base Price')->money('NGN')->sortable(),
        Tables\Columns\TextColumn::make('materials_count')->counts('materials')->label('Materials'),
        Tables\Columns\TextColumn::make('fonts_count')->counts('fonts')->label('Fonts'),
        Tables\Columns\TextColumn::make('colors_count')->counts('colors')->label('Colors'),
        Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
      ])
      ->actions([
        ActionGroup::make([
          Tables\Actions\ViewAction::make(),
          Tables\Actions\EditAction::make(),
          Tables\Actions\DeleteAction::make(),
        ])
      ]);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListPackages::route('/'),
      'create' => Pages\CreatePackage::route('/create'),
      'edit' => Pages\EditPackage::route('/{record}/edit'),
      'view' => Pages\ViewPackage::route('/{record}/view'),
    ];
  }
}
