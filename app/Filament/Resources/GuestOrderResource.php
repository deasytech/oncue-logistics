<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuestOrderResource\Pages;
use App\Models\Event;
use App\Models\EventGuest;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GuestOrderResource extends Resource
{
    protected static ?string $model = EventGuest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Guest Orders';

    protected static ?string $modelLabel = 'Guest Order';

    protected static ?string $pluralModelLabel = 'Guest Orders';

    protected static ?string $navigationGroup = 'Entries';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Guest Information')
                    ->icon('heroicon-o-user')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('guest_full_name')
                            ->label('Full Name')
                            ->getStateUsing(fn(EventGuest $record): string =>
                            trim(collect([$record->guest?->title, $record->guest?->first_name, $record->guest?->last_name])->filter()->join(' '))),

                        TextEntry::make('guest.email')
                            ->label('Email')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),

                        TextEntry::make('guest.phone')
                            ->label('Phone')
                            ->icon('heroicon-o-phone')
                            ->copyable(),

                        TextEntry::make('guest.address')
                            ->label('Address')
                            ->columnSpanFull()
                            ->placeholder('No address on file'),

                        TextEntry::make('guest.state.name')
                            ->label('State'),

                        TextEntry::make('guest.city.name')
                            ->label('City'),
                    ]),

                InfolistSection::make('Event Information')
                    ->icon('heroicon-o-calendar')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('event.name')
                            ->label('Event Name')
                            ->weight('bold'),

                        TextEntry::make('event.customer.full_name')
                            ->label('Host / Customer'),

                        TextEntry::make('event.event_date')
                            ->label('Event Date')
                            ->dateTime('d M Y'),

                        TextEntry::make('event.location')
                            ->label('Venue / Location')
                            ->columnSpanFull()
                            ->placeholder('No location set'),
                    ]),

                InfolistSection::make('RSVP Details')
                    ->icon('heroicon-o-check-circle')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('attendance_status')
                            ->label('Attendance Status')
                            ->badge()
                            ->color(fn(?string $state): string => match ($state) {
                                'confirmed' => 'success',
                                'declined'  => 'danger',
                                default     => 'warning',
                            })
                            ->formatStateUsing(fn(?string $state): string => match ($state) {
                                'confirmed' => 'Confirmed',
                                'declined'  => 'Declined',
                                default     => 'Pending',
                            }),

                        TextEntry::make('plus_one')
                            ->label('Plus One (Name)')
                            ->placeholder('None'),

                        TextEntry::make('rsvp_responded_at')
                            ->label('Responded At')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('Not yet responded'),

                        TextEntry::make('rsvp_sent_at')
                            ->label('RSVP Sent At')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('Not sent yet'),
                    ]),

                InfolistSection::make('Fabric Order Details')
                    ->icon('heroicon-o-shopping-bag')
                    ->columns(3)
                    ->visible(fn(EventGuest $record): bool => $record->relationLoaded('fabricOrder') && $record->fabricOrder !== null)
                    ->schema([
                        TextEntry::make('fabricOrder.payment_status')
                            ->label('Payment Status')
                            ->badge()
                            ->color(fn(?string $state): string => match ($state) {
                                'paid'    => 'success',
                                'failed'  => 'danger',
                                'pending' => 'warning',
                                default   => 'gray',
                            })
                            ->formatStateUsing(fn(?string $state): string => ucfirst($state ?? 'pending')),

                        TextEntry::make('fabricOrder.payment_method')
                            ->label('Payment Method')
                            ->formatStateUsing(fn(?string $state): string => ucfirst($state ?? 'N/A')),

                        TextEntry::make('fabricOrder.payment_reference')
                            ->label('Payment Reference')
                            ->placeholder('N/A')
                            ->copyable(),

                        TextEntry::make('fabricOrder.total_fabric_cost')
                            ->label('Fabric Subtotal')
                            ->money('NGN'),

                        TextEntry::make('fabricOrder.delivery_cost')
                            ->label('Delivery Cost')
                            ->money('NGN'),

                        TextEntry::make('fabricOrder.total_amount')
                            ->label('Total Amount')
                            ->money('NGN')
                            ->weight('bold')
                            ->size(TextEntry\TextEntrySize::Large),

                        TextEntry::make('fabricOrder.paid_at')
                            ->label('Paid At')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('Not yet paid'),

                        TextEntry::make('fabricOrder.external_order_id')
                            ->label('External Order ID')
                            ->placeholder('N/A'),

                        RepeatableEntry::make('fabricOrder.fabric_selections')
                            ->label('Selected Fabrics')
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('name')->label('Fabric Name'),
                                TextEntry::make('price')
                                    ->label('Unit Price')
                                    ->money('NGN'),
                                TextEntry::make('quantity')->label('Qty'),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('guest_name')
                    ->label('Guest Name')
                    ->getStateUsing(fn(EventGuest $record): string =>
                    trim(collect([$record->guest?->title, $record->guest?->first_name, $record->guest?->last_name])->filter()->join(' ')))
                    ->searchable(query: fn(Builder $query, string $search): Builder =>
                    $query->whereHas('guest', fn($q) => $q
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")))
                    ->sortable(query: fn(Builder $query, string $direction): Builder =>
                    $query->orderBy('guests.last_name', $direction)),

                Tables\Columns\TextColumn::make('guest.email')
                    ->label('Email')
                    ->searchable(query: fn(Builder $query, string $search): Builder =>
                    $query->where('guests.email', 'like', "%{$search}%"))
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('guest.phone')
                    ->label('Phone')
                    ->searchable(query: fn(Builder $query, string $search): Builder =>
                    $query->where('guests.phone', 'like', "%{$search}%"))
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('event.name')
                    ->label('Event')
                    ->searchable(query: fn(Builder $query, string $search): Builder =>
                    $query->where('events.name', 'like', "%{$search}%"))
                    ->sortable(query: fn(Builder $query, string $direction): Builder =>
                    $query->orderBy('events.name', $direction)),

                Tables\Columns\TextColumn::make('event.customer.full_name')
                    ->label('Customer / Host')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('attendance_status')
                    ->label('RSVP Status')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'declined'  => 'danger',
                        default     => 'warning',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'confirmed' => 'Confirmed',
                        'declined'  => 'Declined',
                        default     => 'Pending',
                    }),

                Tables\Columns\TextColumn::make('rsvp_responded_at')
                    ->label('Responded At')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('fabric_payment_status')
                    ->label('Fabric Payment')
                    ->badge()
                    ->getStateUsing(fn(EventGuest $record): string =>
                    $record->fabric_payment_status ?? 'No Order')
                    ->color(fn(string $state): string => match ($state) {
                        'paid'     => 'success',
                        'pending'  => 'warning',
                        'failed'   => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'No Order' => 'No Order',
                        default    => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('fabric_total_amount')
                    ->label('Amount (₦)')
                    ->getStateUsing(fn(EventGuest $record): ?string => $record->fabric_total_amount)
                    ->money('NGN')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('fabric_paid_at')
                    ->label('Paid At')
                    ->getStateUsing(fn(EventGuest $record): ?string =>
                    $record->fabric_paid_at
                        ? Carbon::parse($record->fabric_paid_at)->format('d M Y')
                        : null)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('rsvp_created_at')
                    ->label('RSVP Date')
                    ->getStateUsing(fn(EventGuest $record): string =>
                    Carbon::parse($record->rsvp_created_at ?? $record->created_at)->format('d M Y'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('rsvp_created_at', 'desc')
            ->filters([
                SelectFilter::make('event_id')
                    ->label('Event')
                    ->options(fn(): array => Event::orderBy('name')->pluck('name', 'id')->all())
                    ->searchable(),

                SelectFilter::make('attendance_status')
                    ->label('RSVP Status')
                    ->options([
                        'confirmed' => 'Confirmed',
                        'declined'  => 'Declined',
                    ]),

                Filter::make('pending_rsvp')
                    ->label('Pending Response')
                    ->query(fn(Builder $q): Builder => $q->whereNull('event_guest.attendance_status')),

                Filter::make('has_fabric_order')
                    ->label('Has Fabric Order')
                    ->query(fn(Builder $q): Builder => $q->whereNotNull('gfs.id')),

                Filter::make('no_fabric_order')
                    ->label('No Fabric Order')
                    ->query(fn(Builder $q): Builder => $q->whereNull('gfs.id')),

                Filter::make('fabric_paid')
                    ->label('Fabric Paid')
                    ->query(fn(Builder $q): Builder => $q->where('gfs.payment_status', 'paid')),

                Filter::make('fabric_pending_payment')
                    ->label('Fabric Payment Pending')
                    ->query(fn(Builder $q): Builder => $q->where('gfs.payment_status', 'pending')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['guest.city', 'guest.state', 'event.customer'])
            ->join('guests', 'event_guest.guest_id', '=', 'guests.id')
            ->join('events', 'event_guest.event_id', '=', 'events.id')
            ->leftJoin('guest_fabric_selections as gfs', function ($join) {
                $join->on('gfs.guest_id', '=', 'event_guest.guest_id')
                    ->on('gfs.event_id', '=', 'event_guest.event_id');
            })
            ->select([
                'event_guest.*',
                \Illuminate\Support\Facades\DB::raw('event_guest.created_at as rsvp_created_at'),
                'gfs.id as fabric_selection_id',
                'gfs.payment_status as fabric_payment_status',
                'gfs.total_amount as fabric_total_amount',
                'gfs.paid_at as fabric_paid_at',
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuestOrders::route('/'),
            'view'  => Pages\ViewGuestOrder::route('/{record}'),
        ];
    }
}
