<?php

namespace App\Filament\Widgets;

use App\Models\Insumo;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class StockBajoWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = '⚠️ Insumos con Stock Bajo';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Insumo::query()->stockBajo()->orderBy('stock_actual', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Insumo')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('stock_actual')
                    ->label('Stock Actual')
                    ->numeric(2)
                    ->badge()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('stock_minimo')
                    ->label('Stock Mínimo')
                    ->numeric(2),

                Tables\Columns\TextColumn::make('unidad_medida')
                    ->label('Unidad'),

                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->badge()
                    ->color('info'),
            ])
            ->paginated([5]);
    }
}
