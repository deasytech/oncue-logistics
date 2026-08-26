<?php

namespace App\Filament\Resources\BulkMessageResource\Pages;

use App\Filament\Resources\BulkMessageResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListBulkMessages extends ListRecords
{
    protected static string $resource = BulkMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('composeBulkMessage')
                ->label('Compose Message')
                ->icon('heroicon-o-paper-airplane')
                ->modalHeading('Compose Bulk Message')
                ->modalWidth('4xl')
                ->modalSubmitActionLabel('Queue Message')
                ->form(BulkMessageResource::composeFormSchema())
                ->action(fn(array $data) => BulkMessageResource::handleComposeAction($data)),
        ];
    }
}
