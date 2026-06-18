<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Filament\Resources\EventResource\RelationManagers\GuestsRelationManager;
use App\Models\Event;
use App\Models\Guest;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Entries';

    protected static ?int $navigationSort = 2;

    protected static function isCustomCategory($categoryId): bool
    {
        if (!$categoryId) {
            return false;
        }

        $category = \App\Models\Category::find($categoryId);
        return $category && $category->name === 'Custom';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    Forms\Components\Select::make('customer_id')
                        ->label('Customer Name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->relationship('customer', 'first_name')
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->title} {$record->first_name} {$record->last_name}"),
                    Forms\Components\TextInput::make('name')
                        ->label('Event Name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('category_id')
                        ->label('Category')
                        ->required()
                        ->options(function () {
                            return \App\Models\Category::whereNull('parent_id')->pluck('name', 'id');
                        })
                        ->reactive()
                        ->afterStateUpdated(fn(callable $set) => $set('subcategory_id', null))
                        ->searchable()
                        ->native(false),
                    Forms\Components\Group::make([
                        Forms\Components\Select::make('subcategory_id')
                            ->label('Subcategory')
                            ->required()
                            ->options(function (callable $get) {
                                $categoryId = $get('category_id');
                                if (!$categoryId) {
                                    return [];
                                }
                                return \App\Models\Category::where('parent_id', $categoryId)->pluck('name', 'id');
                            })
                            ->reactive()
                            ->disabled(fn(callable $get) => !$get('category_id'))
                            ->searchable()
                            ->native(false)
                            ->hidden(fn(callable $get) => static::isCustomCategory($get('category_id'))),
                        Forms\Components\TextInput::make('custom_subcategory')
                            ->label('Subcategory')
                            ->required()
                            ->maxLength(255)
                            ->visible(fn(callable $get) => static::isCustomCategory($get('category_id'))),
                    ]),
                    Forms\Components\DatePicker::make('event_date')
                        ->required(),
                    Forms\Components\TextInput::make('location')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('aso_ebi_color')
                        ->label('Aso Ebi Color')
                        ->hint('Optional'),
                    Forms\Components\FileUpload::make('logo')
                        ->label('Event Logo')
                        ->hint('Optional - Displayed on RSVP form')
                        ->image()
                        ->imageEditor()
                        ->directory('events/logos')
                        ->maxSize(2048)
                        ->columnSpan(1),
                    Forms\Components\Textarea::make('description')
                        ->label('Event Description')
                        ->hint('Optional - Displayed under logo on RSVP form')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('estimated_number_of_guest')
                        ->label('Estimated Guests')
                        ->numeric()
                        ->required()
                        ->minValue(50)
                        ->helperText('Minimum 50 guests required'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Activate Event')
                        ->required()
                        ->inline(false)
                        ->default(true),
                ])->columnSpan(3)->columns(3),
                Forms\Components\RichEditor::make('notes')
                    ->columnSpanFull(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subcategory.name')
                    ->label('Event Type')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return $record->display_subcategory;
                    }),
                Tables\Columns\TextColumn::make('location')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('estimated_number_of_guest')
                    ->label('Guests')
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active'),
                Tables\Columns\TextColumn::make('aso_ebi_color')
                    ->searchable(),
            ])
            ->filters([
                //
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
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            GuestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
