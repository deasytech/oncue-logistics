<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    public function form(Form $form): Form
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
                        ->options(\App\Models\Category::whereNull('parent_id')->pluck('name', 'id'))
                        ->reactive()
                        ->afterStateUpdated(fn(callable $set) => $set('subcategory_id', null))
                        ->searchable()
                        ->native(false),
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
                        ->native(false),
                    Forms\Components\DatePicker::make('event_date')
                        ->required(),
                    Forms\Components\TextInput::make('location')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('aso_ebi_color')
                        ->label('Aso Ebi Color')
                        ->required(),
                    Forms\Components\TextInput::make('estimated_number_of_guest')
                        ->label('Estimated Guests')
                        ->numeric()
                        ->required(),
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subcategory.name')
                    ->label('Event Type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('estimated_number_of_guest')
                    ->label('Guests')
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('aso_ebi_color')
                    ->searchable(),
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
