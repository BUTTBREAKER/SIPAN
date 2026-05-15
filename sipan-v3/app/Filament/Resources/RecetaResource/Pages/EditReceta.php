<?php

namespace App\Filament\Resources\RecetaResource\Pages;

use App\Filament\Resources\RecetaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReceta extends EditRecord
{
    protected static string $resource = RecetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
