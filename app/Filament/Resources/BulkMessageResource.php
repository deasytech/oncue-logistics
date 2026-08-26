<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BulkMessageResource\Pages;
use App\Filament\Resources\BulkMessageResource\RelationManagers\DeliveriesRelationManager;
use App\Jobs\SendBulkGuestMessageJob;
use App\Mail\BulkGuestMessageMail;
use App\Models\BulkMessage;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Guest;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class BulkMessageResource extends Resource
{
    protected static ?string $model = BulkMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Bulk Messages';

    protected static ?string $navigationGroup = 'Entries';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')->disabled(),
                Forms\Components\TextInput::make('customer.full_name')->label('Customer')->disabled(),
                Forms\Components\TextInput::make('event.name')->label('Event')->disabled(),
                Forms\Components\TextInput::make('total_recipients')->disabled(),
                Forms\Components\DateTimePicker::make('created_at')->disabled(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Message')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('customer.full_name')->label('Customer'),
                        TextEntry::make('event.name')->label('Event')->placeholder('All guests for this customer'),
                        TextEntry::make('createdBy.name')->label('Sent By')->placeholder('—'),
                        TextEntry::make('channels')
                            ->label('Channels')
                            ->badge()
                            ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                        TextEntry::make('created_at')->label('Sent At')->dateTime(),
                        TextEntry::make('body')->label('Message Body')->html()->columnSpanFull(),
                    ]),
                InfolistSection::make('Delivery Summary')
                    ->columns(5)
                    ->schema([
                        TextEntry::make('total_recipients')->label('Targeted Guests'),
                        TextEntry::make('sent_count')
                            ->label('Sent')
                            ->state(fn(BulkMessage $record) => $record->deliveries()->where('status', 'sent')->count())
                            ->color('success'),
                        TextEntry::make('failed_count')
                            ->label('Failed')
                            ->state(fn(BulkMessage $record) => $record->deliveries()->where('status', 'failed')->count())
                            ->color('danger'),
                        TextEntry::make('skipped_count')
                            ->label('Skipped')
                            ->state(fn(BulkMessage $record) => $record->deliveries()->where('status', 'skipped')->count())
                            ->color('gray'),
                        TextEntry::make('pending_count')
                            ->label('Pending')
                            ->state(fn(BulkMessage $record) => $record->deliveries()->where('status', 'pending')->count())
                            ->color('warning'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('customer.full_name')
                    ->label('Customer')
                    ->sortable(),
                Tables\Columns\TextColumn::make('event.name')
                    ->label('Event')
                    ->placeholder('All guests')
                    ->sortable(),
                Tables\Columns\TextColumn::make('channels')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->colors([
                        'success' => 'email',
                        'primary' => 'whatsapp',
                        'gray' => 'sms',
                    ]),
                Tables\Columns\TextColumn::make('total_recipients')
                    ->label('Guests')
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Sent By')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('customer')
                    ->relationship('customer', 'first_name')
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->title} {$record->first_name} {$record->last_name}")
                    ->label('Customer'),
                SelectFilter::make('event')
                    ->relationship('event', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Event'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Shared form schema for the "Compose Message" action, used from the list page
     * header rather than the default create flow, since sending targets a
     * customer/event-scoped set of guests instead of creating a single record.
     */
    public static function composeFormSchema(): array
    {
        return [
            Section::make('')->schema([
                Select::make('customer_id')
                    ->label('Customer')
                    ->options(fn() => Customer::query()->get()->mapWithKeys(
                        fn(Customer $customer) => [$customer->id => trim("{$customer->title} {$customer->first_name} {$customer->last_name}")]
                    ))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn(Set $set) => $set('event_id', null)),

                Select::make('event_id')
                    ->label('Event (optional)')
                    ->helperText("Leave blank to message every guest of this customer, or pick an event to scope it to that event's guest list.")
                    ->options(fn(Get $get) => $get('customer_id')
                        ? Event::query()->where('customer_id', $get('customer_id'))->pluck('name', 'id')
                        : [])
                    ->searchable()
                    ->disabled(fn(Get $get) => !$get('customer_id')),
            ])->columns(2),
            CheckboxList::make('channels')
                ->label('Send Via')
                ->options([
                    'email' => 'Email',
                    'sms' => 'SMS',
                    'whatsapp' => 'WhatsApp',
                ])
                ->default(['email', 'sms'])
                ->columns(3)
                ->required()
                ->live(),

            TextInput::make('title')
                ->label('Title')
                ->helperText('Used as the email subject line.')
                ->required()
                ->maxLength(255),

            RichEditor::make('body')
                ->label('Message')
                ->required()
                ->toolbarButtons([
                    'bold',
                    'italic',
                    'underline',
                    'strike',
                    'link',
                    'orderedList',
                    'unorderedList',
                    'h2',
                    'h3',
                    'blockquote',
                ])
                ->helperText("Applies to Email and SMS. WhatsApp uses your separately configured Twilio template — this text isn't sent to WhatsApp recipients.")
                ->hintAction(
                    FormAction::make('insertFirstName')
                        ->label('Insert first name')
                        ->icon('heroicon-o-user')
                        ->action(fn(Set $set, Get $get) => $set('body', rtrim((string) $get('body')) . ' {{first_name}}'))
                ),

            Toggle::make('send_test')
                ->label('Send test email to myself first')
                ->helperText('Sends the email version to your own account email before queuing it for guests.')
                ->default(true)
                ->visible(fn(Get $get) => in_array('email', $get('channels') ?? [], true)),
        ];
    }

    public static function handleComposeAction(array $data): void
    {
        $channels = array_values($data['channels']);
        $sendTest = (bool) ($data['send_test'] ?? false);

        if ($sendTest && in_array('email', $channels, true)) {
            try {
                Mail::to(filament()->auth()->user()->email)->send(new BulkGuestMessageMail(
                    $data['title'],
                    str_ireplace('{{first_name}}', 'Guest', $data['body']),
                ));
            } catch (\Throwable $e) {
                Notification::make()
                    ->title('Test email failed')
                    ->body('Error: ' . $e->getMessage())
                    ->danger()
                    ->send();

                return;
            }
        }

        $guestsQuery = Guest::query()->where('customer_id', $data['customer_id']);

        if (!empty($data['event_id'])) {
            $guestsQuery->whereHas('events', fn($query) => $query->where('events.id', $data['event_id']));
        }

        $guestsQuery->where(function ($query) use ($channels) {
            if (in_array('email', $channels, true)) {
                $query->orWhereNotNull('email');
            }
            if (in_array('sms', $channels, true) || in_array('whatsapp', $channels, true)) {
                $query->orWhereNotNull('phone');
            }
        });

        $totalRecipients = (clone $guestsQuery)->count();

        $bulkMessage = BulkMessage::create([
            'customer_id' => $data['customer_id'],
            'event_id' => $data['event_id'] ?? null,
            'created_by' => filament()->auth()->id(),
            'title' => $data['title'],
            'body' => $data['body'],
            'channels' => $channels,
            'total_recipients' => $totalRecipients,
        ]);

        $guestsQuery->select('id')->chunkById(200, function ($guests) use ($bulkMessage) {
            foreach ($guests as $guest) {
                SendBulkGuestMessageJob::dispatch($bulkMessage->id, $guest->id);
            }
        });

        Notification::make()
            ->title('Message queued')
            ->body("Queued for {$totalRecipients} guests. Sends will go out gradually in the background.")
            ->success()
            ->send();
    }

    public static function getRelations(): array
    {
        return [
            DeliveriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBulkMessages::route('/'),
            'view' => Pages\ViewBulkMessage::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }
}
