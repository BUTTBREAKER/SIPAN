<?php

namespace App\Filament\Resources\SugerenciaCompraResource\Pages;

use App\Filament\Resources\SugerenciaCompraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSugerenciaCompras extends ListRecords
{
    protected static string $resource = SugerenciaCompraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
