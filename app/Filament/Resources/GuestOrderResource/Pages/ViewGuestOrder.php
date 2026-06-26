<?php

namespace App\Filament\Resources\GuestOrderResource\Pages;

use App\Filament\Resources\GuestOrderResource;
use App\Models\EventGuest;
use App\Models\GuestFabricSelection;
use Filament\Resources\Pages\ViewRecord;

class ViewGuestOrder extends ViewRecord
{
    protected static string $resource = GuestOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function resolveRecord(int|string $key): EventGuest
    {
        $record = EventGuest::with([
            'guest.city',
            'guest.state',
            'event.customer',
        ])->findOrFail($key);

        $fabricOrder = GuestFabricSelection::where('guest_id', $record->guest_id)
            ->where('event_id', $record->event_id)
            ->first();

        $record->setRelation('fabricOrder', $fabricOrder);

        return $record;
    }
}
