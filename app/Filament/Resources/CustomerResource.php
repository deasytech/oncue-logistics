<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Actions\SendNewsletterAction;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\CustomerResource\RelationManagers\EventsRelationManager;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use App\Filament\Exports\CustomerExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Entries';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $name = trim((string) request()->query('enquiryName', ''));
        $nameParts = array_values(array_filter(preg_split('/\s+/', $name) ?: []));
        $defaultFirstName = $nameParts[0] ?? null;
        $defaultLastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : null;

        return $form
            ->schema([
                Section::make('')->schema([
                    Forms\Components\Select::make('user_id')
                        ->searchable()
                        ->preload()
                        ->relationship(
                            name: 'user',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn($query) => $query->where('email', '!=', 'super@admin.com')
                        ),
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('first_name')
                        ->default($defaultFirstName)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('last_name')
                        ->default($defaultLastName)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->default(request()->query('enquiryPhone'))
                        ->required()
                        ->unique(Customer::class, 'phone', ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->default(request()->query('enquiryEmail'))
                        ->required()
                        ->unique(Customer::class, 'email', ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('address')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->getStateUsing(fn($record) => $record->user?->name ?? 'Not Assigned')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Assigned User'),
                Filter::make('has_events')
                    ->label('Has Events')
                    ->query(fn(Builder $query): Builder => $query->has('events')),
                Filter::make('no_events')
                    ->label('No Events')
                    ->query(fn(Builder $query): Builder => $query->doesntHave('events')),
                Filter::make('recently_created')
                    ->label('Recently Created')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(30))),
                Filter::make('has_phone')
                    ->label('Has Phone Number')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('phone')->where('phone', '!=', '')),
                Filter::make('email_verified')
                    ->label('Email Format Valid')
                    ->query(fn(Builder $query): Builder => $query->where('email', 'REGEXP', '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$')),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(CustomerExporter::class)
                    ->fileName('customers_export_' . now()->format('Y-m-d_H-i-s'))
                    ->label('Export Customers'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    SendNewsletterAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }

    public static function getRelations(): array
    {
        return [
            EventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'view' => Pages\ViewCustomer::route('/{record}/view'),
        ];
    }
}
