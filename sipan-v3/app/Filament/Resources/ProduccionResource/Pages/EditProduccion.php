<?php

namespace App\Filament\Resources\ProduccionResource\Pages;

use App\Filament\Resources\ProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduccion extends EditRecord
{
    protected static string $resource = ProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
