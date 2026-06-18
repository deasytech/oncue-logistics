<?php

namespace App\Filament\Resources;

use App\Filament\Exports\GuestExporter;
use App\Filament\Forms\Components\GooglePlacesAutocomplete;
use App\Filament\Imports\GuestImporter;
use App\Filament\Resources\GuestResource\Pages;
use App\Models\Event;
use App\Models\Guest;
use App\Models\State;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GuestResource extends Resource
{
    protected static ?string $model = Guest::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Entries';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')->schema([
                    Grid::make(4)->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('customer', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->title} {$record->first_name} {$record->last_name}"),

                        Forms\Components\TextInput::make('title')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                    ]),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->maxLength(255),
                ])->columns(3),

                Section::make('Location Details')
                    ->schema([
                        Grid::make(1)->schema([
                            GooglePlacesAutocomplete::make('address')
                                ->label('Address')
                                ->required()
                                ->columnSpanFull()
                                ->country('ng')
                                ->restrictToCountry(true)
                                ->placeholder('Start typing to search for an address...')
                                ->helperText('Please select an address from the dropdown suggestions.')
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    // Auto-populate state based on selected address
                                    if ($state) {
                                        // Extract state from address and find matching state in database
                                        $addressParts = explode(',', $state);
                                        foreach ($addressParts as $part) {
                                            $part = trim($part);
                                            // Try to find state by name (case insensitive)
                                            $foundState = State::whereRaw('LOWER(name) = ?', [strtolower($part)])
                                                ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($part) . '%'])
                                                ->where('is_active', true)
                                                ->first();

                                            if ($foundState) {
                                                $set('state_id', $foundState->id);
                                                // Trigger city options update
                                                $set('city_id', null);
                                                break;
                                            }
                                        }
                                    }
                                }),
                        ]),

                        Grid::make(2)->schema([
                            Forms\Components\Select::make('state_id')
                                ->label('State')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->options(\App\Models\State::where('is_active', true)->pluck('name', 'id'))
                                ->reactive()
                                ->live()
                                ->afterStateUpdated(fn(callable $set) => $set('city_id', null)),

                            Forms\Components\Select::make('city_id')
                                ->label('City')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->options(function (callable $get) {
                                    $stateId = $get('state_id');
                                    if (!$stateId) {
                                        return [];
                                    }
                                    return \App\Models\City::where('state_id', $stateId)
                                        ->where('is_active', true)
                                        ->pluck('name', 'id');
                                })
                                ->disabled(fn(callable $get): bool => !$get('state_id')),
                        ]),
                    ]),

                Section::make('Event Assignment')->schema([
                    Forms\Components\Select::make('events')
                        ->label('Attach to Events')
                        ->multiple()
                        ->preload()
                        ->relationship('events', 'name')
                        ->searchable()
                ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.full_name')
                    ->label('Customer')
                    ->sortable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Guest Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('city.name')
                    ->label('City')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('state.name')
                    ->label('State')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('events.name')
                    ->label('Events')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(GuestExporter::class)
                    ->formats([ExportFormat::Csv]),
                ImportAction::make('import')
                    ->label('Import Guests')
                    ->importer(GuestImporter::class)
                    ->form(fn(ImportAction $action): array => [
                        \Filament\Forms\Components\FileUpload::make('file')
                            ->label(__('filament-actions::import.modal.form.file.label'))
                            ->placeholder(__('filament-actions::import.modal.form.file.placeholder'))
                            ->acceptedFileTypes(['text/csv', 'text/x-csv', 'application/csv', 'application/x-csv', 'text/comma-separated-values', 'text/x-comma-separated-values', 'text/plain', 'application/vnd.ms-excel'])
                            ->rules(['extensions:csv,txt'])
                            ->afterStateUpdated(function (\Filament\Forms\Components\FileUpload $component, \Livewire\Component $livewire, \Filament\Forms\Set $set, ?\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $state) use ($action) {
                                if (! $state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                    return;
                                }

                                try {
                                    $livewire->validateOnly($component->getStatePath());
                                } catch (\Illuminate\Validation\ValidationException $exception) {
                                    $component->state([]);

                                    throw $exception;
                                }

                                $csvStream = $action->getUploadedFileStream($state);

                                if (! $csvStream) {
                                    return;
                                }

                                $csvReader = \League\Csv\Reader::createFromStream($csvStream);

                                if (filled($csvDelimiter = $action->getCsvDelimiter($csvReader))) {
                                    $csvReader->setDelimiter($csvDelimiter);
                                }

                                $csvReader->setHeaderOffset($action->getHeaderOffset() ?? 0);

                                $csvColumns = $csvReader->getHeader();

                                $lowercaseCsvColumnValues = array_map(\Illuminate\Support\Str::lower(...), $csvColumns);
                                $lowercaseCsvColumnKeys = array_combine(
                                    $lowercaseCsvColumnValues,
                                    $csvColumns,
                                );

                                $set('columnMap', array_reduce($action->getImporter()::getColumns(), function (array $carry, \Filament\Actions\Imports\ImportColumn $column) use ($lowercaseCsvColumnKeys, $lowercaseCsvColumnValues) {
                                    $carry[$column->getName()] = $lowercaseCsvColumnKeys[\Illuminate\Support\Arr::first(
                                        array_intersect(
                                            $lowercaseCsvColumnValues,
                                            $column->getGuesses(),
                                        ),
                                    )] ?? null;

                                    return $carry;
                                }, []));
                            })
                            ->storeFiles(false)
                            ->visibility('private')
                            ->required(),
                        \Filament\Forms\Components\Fieldset::make(__('filament-actions::import.modal.form.columns.label'))
                            ->columns(1)
                            ->inlineLabel()
                            ->schema(function (\Filament\Forms\Get $get) use ($action): array {
                                $csvFile = \Illuminate\Support\Arr::first((array) ($get('file') ?? []));

                                if (! $csvFile instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                    return [];
                                }

                                $csvStream = $action->getUploadedFileStream($csvFile);

                                if (! $csvStream) {
                                    return [];
                                }

                                $csvReader = \League\Csv\Reader::createFromStream($csvStream);

                                if (filled($csvDelimiter = $action->getCsvDelimiter($csvReader))) {
                                    $csvReader->setDelimiter($csvDelimiter);
                                }

                                $csvReader->setHeaderOffset($action->getHeaderOffset() ?? 0);

                                $csvColumns = $csvReader->getHeader();
                                $csvColumnOptions = array_combine($csvColumns, $csvColumns);

                                return array_map(
                                    fn(\Filament\Actions\Imports\ImportColumn $column): \Filament\Forms\Components\Select => $column->getSelect()->options($csvColumnOptions),
                                    $action->getImporter()::getColumns(),
                                );
                            })
                            ->statePath('columnMap')
                            ->visible(fn(\Filament\Forms\Get $get): bool => \Illuminate\Support\Arr::first((array) ($get('file') ?? [])) instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile),
                        \Filament\Forms\Components\Select::make('customer_id')
                            ->label('Select Customer')
                            ->options(function () {
                                return \App\Models\Customer::all()->mapWithKeys(function ($customer) {
                                    return [$customer->id => "{$customer->title} {$customer->first_name} {$customer->last_name}"];
                                })->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        \Filament\Forms\Components\Select::make('event_id')
                            ->label('Select Event (Optional)')
                            ->options(Event::pluck('name', 'id'))
                            ->searchable()
                            ->placeholder('Leave empty to import guests without attaching to an event')
                            ->live(),
                        \Filament\Forms\Components\Radio::make('send_notifications')
                            ->label('Send email and SMS to imported guests?')
                            ->options([
                                1 => 'Yes, send email and SMS',
                                0 => 'No, do not send',
                            ])
                            ->descriptions([
                                1 => 'Imported guests attached to the selected event will receive RSVP notifications.',
                                0 => 'Guests will be imported and attached to the event without sending notifications.',
                            ])
                            ->default(1)
                            ->inline()
                            ->visible(fn(Get $get): bool => filled($get('event_id')))
                            ->required(fn(Get $get): bool => filled($get('event_id'))),
                    ])
                    ->options(fn(array $data): array => [
                        'customer_id' => $data['customer_id'] ?? null,
                        'event_id' => $data['event_id'] ?? null,
                        'send_notifications' => (bool) ($data['send_notifications'] ?? true),
                    ]),
            ])
            ->filters([
                SelectFilter::make('customer')
                    ->relationship('customer', 'first_name')
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->title} {$record->first_name} {$record->last_name}")
                    ->label('Customer'),
                SelectFilter::make('state')
                    ->relationship('state', 'name')
                    ->searchable()
                    ->preload()
                    ->label('State'),
                SelectFilter::make('city')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->label('City'),
                SelectFilter::make('events')
                    ->relationship('events', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->label('Events'),
                Filter::make('has_address')
                    ->label('Has Address')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('address')->where('address', '!=', '')),
                Filter::make('has_email')
                    ->label('Has Email')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('email')->where('email', '!=', '')),
                Filter::make('has_phone')
                    ->label('Has Phone')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('phone')->where('phone', '!=', '')),
                Filter::make('has_location')
                    ->label('Has Location')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('state_id')->whereNotNull('city_id')),
                Filter::make('has_events')
                    ->label('Has Events')
                    ->query(fn(Builder $query): Builder => $query->has('events')),
                Filter::make('no_events')
                    ->label('No Events')
                    ->query(fn(Builder $query): Builder => $query->doesntHave('events')),
                Filter::make('recently_created')
                    ->label('Recently Created')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(30))),
                Filter::make('complete_profile')
                    ->label('Complete Profile')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('email')->whereNotNull('phone')->where('email', '!=', '')->where('phone', '!=', '')),
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
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuests::route('/'),
            'create' => Pages\CreateGuest::route('/create'),
            'edit' => Pages\EditGuest::route('/{record}/edit'),
        ];
    }
}
