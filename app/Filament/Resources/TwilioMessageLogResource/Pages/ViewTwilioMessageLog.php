<?php

namespace App\Filament\Resources\TwilioMessageLogResource\Pages;

use App\Filament\Resources\TwilioMessageLogResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTwilioMessageLog extends ViewRecord
{
    protected static string $resource = TwilioMessageLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
