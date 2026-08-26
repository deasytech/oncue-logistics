<?php

namespace App\Filament\Resources\BulkMessageResource\Pages;

use App\Filament\Resources\BulkMessageResource;
use Filament\Resources\Pages\ViewRecord;

class ViewBulkMessage extends ViewRecord
{
    protected static string $resource = BulkMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
