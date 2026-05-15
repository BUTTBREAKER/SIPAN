<?php

namespace App\Filament\Resources\InsumoResource\Pages;

use App\Filament\Resources\InsumoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInsumo extends EditRecord
{
    protected static string $resource = InsumoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
