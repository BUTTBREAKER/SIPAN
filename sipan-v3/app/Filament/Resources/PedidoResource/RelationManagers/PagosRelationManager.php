<?php

namespace App\Filament\Resources\PedidoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PagosRelationManager extends RelationManager
{
    protected static string $relationship = 'pagos';
    protected static ?string $title = 'Pagos Parciales';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('id_usuario')
                    ->default(fn () => Auth::id()),
                Forms\Components\TextInput::make('monto')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\Select::make('metodo_pago')
                    ->required()
                    ->options([
                        'efectivo' => 'Efectivo',
                        'tarjeta' => 'Tarjeta',
                        'transferencia' => 'Transferencia',
                        'yape' => 'Yape',
                        'plin' => 'Plin',
                        'otro' => 'Otro',
                    ])
                    ->default('efectivo'),
                Forms\Components\TextInput::make('referencia')
                    ->maxLength(100),
                Forms\Components\DateTimePicker::make('fecha_pago')
                    ->required()
                    ->default(now()),
                Forms\Components\Textarea::make('observaciones')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('fecha_pago')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('metodo_pago'),
                Tables\Columns\TextColumn::make('usuario.primer_nombre')
                    ->label('Registrado por'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($livewire) {
                        $pedido = $livewire->ownerRecord;
                        $pagado = $pedido->pagos()->sum('monto');
                        $deuda = $pedido->total - $pagado;
                        
                        $estado_pago = 'pendiente';
                        if ($pagado >= $pedido->total) {
                            $estado_pago = 'pagado';
                        } elseif ($pagado > 0) {
                            $estado_pago = 'abonado';
                        }

                        $pedido->update([
                            'monto_pagado' => $pagado,
                            'monto_deuda' => max(0, $deuda),
                            'estado_pago' => $estado_pago
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->after(function ($livewire) {
                        $pedido = $livewire->ownerRecord;
                        $pagado = $pedido->pagos()->sum('monto');
                        $deuda = $pedido->total - $pagado;
                        
                        $estado_pago = 'pendiente';
                        if ($pagado >= $pedido->total) {
                            $estado_pago = 'pagado';
                        } elseif ($pagado > 0) {
                            $estado_pago = 'abonado';
                        }

                        $pedido->update([
                            'monto_pagado' => $pagado,
                            'monto_deuda' => max(0, $deuda),
                            'estado_pago' => $estado_pago
                        ]);
                    }),
            ])
            ->bulkActions([
                //
            ]);
    }
}
