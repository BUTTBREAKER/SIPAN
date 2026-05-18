<?php

namespace App\Filament\Resources\ClienteResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VentasRelationManager extends RelationManager
{
    protected static string $relationship = 'ventas';

    protected static ?string $title = 'Historial de Compras';

    public function form(Form $form): Form
    {
        return $form->schema([]); // Read-only from here
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('numero_venta')
            ->columns([
                Tables\Columns\TextColumn::make('numero_venta')
                    ->label('Nro. Venta')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('fecha_venta')
                    ->label('Fecha')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->color('success'),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completada' => 'success',
                        'pendiente' => 'warning',
                        'cancelada' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                // We could add an action to view the sale if we had a VentaResource
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('fecha_venta', 'desc');
    }
}
