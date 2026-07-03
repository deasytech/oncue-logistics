<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TwilioMessageLogResource\Pages;
use App\Models\TwilioMessageLog;
use App\Services\TwilioService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TwilioMessageLogResource extends Resource
{
    protected static ?string $model = TwilioMessageLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'WhatsApp/SMS Logs';

    protected static ?string $modelLabel = 'Message Log';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

    // Known Twilio error codes worth surfacing by name in the filter dropdown; anything
    // else still shows up in the table, just without a friendly label here.
    public const KNOWN_ERROR_CODES = [
        63049 => '63049 — Template blocked (Marketing category)',
        63018 => '63018 — Rate limit exceeded',
        21211 => '21211 — Invalid \'To\' number',
        63032 => '63032 — Opt-in / session window restriction',
        63024 => '63024 — Invalid/unreachable WhatsApp recipient',
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('channel')->disabled(),
                        Forms\Components\TextInput::make('status')->disabled(),
                        Forms\Components\TextInput::make('to')->disabled(),
                        Forms\Components\TextInput::make('to_country')->disabled(),
                        Forms\Components\TextInput::make('context')->disabled(),
                        Forms\Components\TextInput::make('content_sid')->disabled(),
                        Forms\Components\TextInput::make('message_sid')->disabled(),
                        Forms\Components\TextInput::make('error_code')->disabled(),
                        Forms\Components\Textarea::make('error_message')->disabled()->columnSpanFull(),
                        Forms\Components\KeyValue::make('payload')->disabled()->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('retried_at')->disabled(),
                        Forms\Components\TextInput::make('retry_of_id')->disabled(),
                        Forms\Components\DateTimePicker::make('created_at')->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->tooltip(fn(TwilioMessageLog $record) => $record->created_at?->toDateTimeString()),
                Tables\Columns\TextColumn::make('channel')
                    ->badge()
                    ->colors([
                        'success' => 'sms',
                        'primary' => 'whatsapp_template',
                        'gray' => 'whatsapp',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('to')
                    ->label('To')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('context')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'queued', 'sent', 'delivered', 'read' => 'success',
                        'failed', 'undelivered' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('error_code')
                    ->label('Error')
                    ->badge()
                    ->color(fn(?int $state) => $state ? 'danger' : 'gray')
                    ->formatStateUsing(fn(?int $state) => $state ?: '—')
                    ->tooltip(fn(?int $state) => $state ? (self::KNOWN_ERROR_CODES[$state] ?? 'Twilio error ' . $state) : null)
                    ->sortable(),
                Tables\Columns\TextColumn::make('guest.full_name')
                    ->label('Guest')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('event.name')
                    ->label('Event')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('content_sid')
                    ->label('Content SID')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
                Tables\Columns\TextColumn::make('message_sid')
                    ->label('Message SID')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
                Tables\Columns\IconColumn::make('retried_at')
                    ->label('Retried')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('channel')
                    ->options([
                        'sms' => 'SMS',
                        'whatsapp_template' => 'WhatsApp (Template)',
                        'whatsapp' => 'WhatsApp (Free-form)',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'queued' => 'Queued',
                        'sent' => 'Sent',
                        'delivered' => 'Delivered',
                        'read' => 'Read',
                        'failed' => 'Failed',
                        'undelivered' => 'Undelivered',
                    ]),
                Tables\Filters\SelectFilter::make('error_code')
                    ->label('Error Code')
                    ->options(self::KNOWN_ERROR_CODES),
                Tables\Filters\SelectFilter::make('context')
                    ->options(fn() => TwilioMessageLog::query()
                        ->whereNotNull('context')
                        ->distinct()
                        ->pluck('context', 'context')
                        ->all()),
                Tables\Filters\Filter::make('has_error')
                    ->label('Has error')
                    ->query(fn($query) => $query->whereNotNull('error_code')),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn($query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('retry')
                        ->label('Retry')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Retry this message?')
                        ->modalDescription(fn(TwilioMessageLog $record) => $record->error_code === 63049
                            ? 'This failed because the WhatsApp template is categorized as Marketing, which Meta blocks for US recipients. Retrying will very likely fail again until the content SID is re-categorized in the Twilio Content Editor.'
                            : "This will re-send the original message to {$record->to}.")
                        ->modalSubmitActionLabel('Retry send')
                        ->visible(fn(TwilioMessageLog $record) => $record->isRetryable())
                        ->action(function (TwilioMessageLog $record) {
                            $service = app(TwilioService::class);
                            $payload = $record->payload ?? [];
                            $meta = [
                                'guest_id' => $record->guest_id,
                                'event_id' => $record->event_id,
                                'retry_of_id' => $record->id,
                            ];

                            $success = match ($record->channel) {
                                'whatsapp_template' => $service->sendWhatsAppTemplate(
                                    $record->to,
                                    $payload['guest_name'] ?? '',
                                    $payload['event_name'] ?? '',
                                    $payload['event_date'] ?? '',
                                    $payload['rsvp_token'] ?? '',
                                    $payload['customer_name'] ?? '',
                                    $record->context ?? 'retry',
                                    $meta,
                                )->success,
                                'whatsapp' => $service->sendWhatsApp(
                                    $record->to,
                                    $payload['message'] ?? '',
                                    $record->context ?? 'retry',
                                    $meta,
                                )->success,
                                'sms' => $service->sendSms(
                                    $record->to,
                                    $payload['message'] ?? '',
                                    $record->context ?? 'retry',
                                    $meta,
                                ),
                                default => false,
                            };

                            Notification::make()
                                ->title($success ? 'Retry sent successfully' : 'Retry failed')
                                ->body($success
                                    ? "Message re-sent to {$record->to}."
                                    : "The retry attempt to {$record->to} also failed — check the new log entry below for details.")
                                ->color($success ? 'success' : 'danger')
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete (prune old logs)'),
                ]),
            ])
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTwilioMessageLogs::route('/'),
            'view' => Pages\ViewTwilioMessageLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'failed')
            ->where('created_at', '>=', now()->subDay())
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Failed messages in the last 24 hours';
    }
}
