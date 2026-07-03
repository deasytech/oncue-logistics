<?php

namespace App\Filament\Resources\TwilioMessageLogResource\Pages;

use App\Filament\Resources\TwilioMessageLogResource;
use Filament\Resources\Pages\ListRecords;

class ListTwilioMessageLogs extends ListRecords
{
    protected static string $resource = TwilioMessageLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
