<?php

namespace App\Filament\Resources\NegocioResource\Pages;

use App\Filament\Resources\NegocioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNegocio extends EditRecord
{
    protected static string $resource = NegocioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
