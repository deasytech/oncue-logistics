<?php

namespace App\Filament\Resources\GuestResource\Pages;

use App\Filament\Exports\GuestExporter;
use App\Filament\Imports\GuestImporter;
use App\Filament\Resources\GuestResource;
use Filament\Actions;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Resources\Pages\ListRecords;

class ListGuests extends ListRecords
{
    protected static string $resource = GuestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
