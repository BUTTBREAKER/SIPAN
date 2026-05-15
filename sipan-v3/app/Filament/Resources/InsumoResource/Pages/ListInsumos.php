<?php

namespace App\Filament\Resources\InsumoResource\Pages;

use App\Filament\Resources\InsumoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInsumos extends ListRecords
{
    protected static string $resource = InsumoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
