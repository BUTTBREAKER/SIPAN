<?php

namespace App\Filament\Resources\SugerenciaCompraResource\Pages;

use App\Filament\Resources\SugerenciaCompraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSugerenciaCompra extends EditRecord
{
    protected static string $resource = SugerenciaCompraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
