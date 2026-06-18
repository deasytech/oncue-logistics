<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class GuestsRelationManager extends RelationManager
{
    protected static string $relationship = 'guests';
    protected static ?string $recordTitleAttribute = 'full_name';

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Set the customer_id from the event's customer
        $event = $this->getOwnerRecord();
        $data['customer_id'] = $event->customer_id;

        // Create the guest record
        $guest = static::getModel()::create($data);

        return $guest;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Guest Information')->schema([
                    Forms\Components\TextInput::make('title')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->maxLength(255),
                ])->columns(2),
                Section::make('Event Details')->schema([
                    Forms\Components\Select::make('attendance_status')
                        ->label('Attendance Status')
                        ->options([
                            'pending' => 'Pending',
                            'confirmed' => 'Confirmed',
                            'declined' => 'Declined',
                        ])
                        ->default('pending'),
                    Forms\Components\Toggle::make('plus_one')
                        ->label('Plus One'),
                    Forms\Components\TextInput::make('rsvp_token')
                        ->label('RSVP Token')
                        ->default(fn() => (string) Str::uuid())
                        ->readOnly(),
                ])->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->label('Guest'),
                Tables\Columns\TextColumn::make('pivot.attendance_status')->label('Status'),
                Tables\Columns\IconColumn::make('pivot.plus_one')->boolean()->label('Plus One'),
                Tables\Columns\TextColumn::make('pivot.rsvp_token')->label('RSVP Token')->copyable(),
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
