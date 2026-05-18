<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoResource\Pages;
use App\Filament\Resources\PedidoResource\RelationManagers;
use App\Models\Pedido;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PedidoResource extends Resource
{
    use \App\Traits\FiltrablePorSucursal;

    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Pedidos';
    protected static ?string $modelLabel = 'Pedido';
    protected static ?string $pluralModelLabel = 'Pedidos';
    protected static ?string $navigationGroup = 'Ventas';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Pedido')
                    ->schema([
                        Forms\Components\Select::make('id_cliente')
                            ->label('Cliente')
                            ->relationship('cliente', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Hidden::make('id_sucursal')
                            ->default(fn () => Auth::user()->id_sucursal ?? 1),
                        Forms\Components\Select::make('id_usuario')
                            ->label('Repartidor Asignado')
                            ->relationship('usuario', 'primer_nombre', fn (Builder $query) => $query->role('repartidor'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('fecha_entrega')
                            ->label('Fecha de Entrega Estimada'),
                        Forms\Components\Select::make('estado_pedido')
                            ->label('Estado del Pedido')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'en_proceso' => 'En Proceso',
                                'completado' => 'Completado',
                                'entregado' => 'Entregado',
                                'cancelado' => 'Cancelado',
                            ])
                            ->default('pendiente')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Finanzas')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->readOnly(),
                        Forms\Components\TextInput::make('descuento')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $total = max(0, $get('subtotal') - $state);
                                $set('total', $total);
                                $set('monto_deuda', max(0, $total - $get('monto_pagado')));
                            }),
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->readOnly(),
                        Forms\Components\TextInput::make('monto_pagado')
                            ->label('Abonado')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->readOnly(),
                        Forms\Components\TextInput::make('monto_deuda')
                            ->label('Deuda')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->readOnly(),
                        Forms\Components\Select::make('estado_pago')
                            ->label('Estado de Pago')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'abonado' => 'Abonado',
                                'pagado' => 'Pagado',
                            ])
                            ->default('pendiente')
                            ->disabled(),
                    ])->columns(3),

                Forms\Components\Section::make('Observaciones')
                    ->schema([
                        Forms\Components\Textarea::make('observaciones')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_pedido')
                    ->label('Nro. Pedido')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('fecha_pedido')
                    ->label('Fecha')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('USD')
                    ->sortable()
                    ->color('success'),
                Tables\Columns\TextColumn::make('monto_deuda')
                    ->label('Deuda')
                    ->money('USD')
                    ->color('danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado_pedido')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completado', 'entregado' => 'success',
                        'en_proceso' => 'info',
                        'pendiente' => 'warning',
                        'cancelado' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('estado_pago')
                    ->label('Pago')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pagado' => 'success',
                        'abonado' => 'info',
                        'pendiente' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('usuario.primer_nombre')
                    ->label('Repartidor')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado_pedido')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_proceso' => 'En Proceso',
                        'completado' => 'Completado',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado',
                    ]),
                Tables\Filters\SelectFilter::make('estado_pago')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'abonado' => 'Abonado',
                        'pagado' => 'Pagado',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_pedido', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProductosRelationManager::class,
            RelationManagers\PagosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'edit' => Pages\EditPedido::route('/{record}/edit'),
        ];
    }
}
