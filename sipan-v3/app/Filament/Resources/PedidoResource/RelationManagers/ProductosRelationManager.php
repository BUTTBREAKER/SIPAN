<?php

namespace App\Filament\Resources\PedidoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Producto;

class ProductosRelationManager extends RelationManager
{
    protected static string $relationship = 'productos';
    protected static ?string $title = 'Productos del Pedido';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_producto')
                    ->label('Producto')
                    ->options(Producto::where('activo', true)->pluck('nombre', 'id'))
                    ->required()
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('precio_unitario', Producto::find($state)?->precio_venta ?? 0)),
                Forms\Components\TextInput::make('cantidad')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('subtotal', $state * $get('precio_unitario'));
                    }),
                Forms\Components\TextInput::make('precio_unitario')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('subtotal', $state * $get('cantidad'));
                    }),
                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->readOnly(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('producto.nombre')
            ->columns([
                Tables\Columns\TextColumn::make('producto.nombre')
                    ->label('Producto'),
                Tables\Columns\TextColumn::make('cantidad')
                    ->label('Cantidad'),
                Tables\Columns\TextColumn::make('precio_unitario')
                    ->label('Precio Unit.')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('USD'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($livewire) {
                        // Actualizar total del pedido al agregar
                        $pedido = $livewire->ownerRecord;
                        $total = $pedido->productos()->sum('subtotal');
                        $pedido->update([
                            'subtotal' => $total,
                            'total' => $total - $pedido->descuento,
                            'monto_deuda' => ($total - $pedido->descuento) - $pedido->monto_pagado
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($livewire) {
                        $pedido = $livewire->ownerRecord;
                        $total = $pedido->productos()->sum('subtotal');
                        $pedido->update([
                            'subtotal' => $total,
                            'total' => $total - $pedido->descuento,
                            'monto_deuda' => ($total - $pedido->descuento) - $pedido->monto_pagado
                        ]);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($livewire) {
                        $pedido = $livewire->ownerRecord;
                        $total = $pedido->productos()->sum('subtotal');
                        $pedido->update([
                            'subtotal' => $total,
                            'total' => $total - $pedido->descuento,
                            'monto_deuda' => ($total - $pedido->descuento) - $pedido->monto_pagado
                        ]);
                    }),
            ])
            ->bulkActions([
                //
            ]);
    }
}
