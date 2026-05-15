<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoteResource\Pages;
use App\Models\Lote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LoteResource extends Resource
{
    protected static ?string $model = Lote::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Lotes';

    protected static ?string $modelLabel = 'Lote';

    protected static ?string $pluralModelLabel = 'Lotes';

    protected static ?string $navigationGroup = 'Inventario';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Lote')
                    ->schema([
                        Forms\Components\TextInput::make('codigo_lote')
                            ->label('Código del Lote')
                            ->required()
                            ->maxLength(50),

                        Forms\Components\Select::make('tipo')
                            ->options([
                                'insumo'   => 'Insumo',
                                'producto' => 'Producto',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('id_item')
                            ->label('ID del Ítem')
                            ->numeric()
                            ->required(),

                        Forms\Components\Select::make('id_sucursal')
                            ->label('Sucursal')
                            ->relationship('sucursal', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make('Cantidades y Fechas')
                    ->schema([
                        Forms\Components\TextInput::make('cantidad_inicial')
                            ->label('Cantidad Inicial')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('cantidad_actual')
                            ->label('Cantidad Actual')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('costo_unitario')
                            ->label('Costo Unitario ($)')
                            ->numeric()
                            ->prefix('$'),

                        Forms\Components\DatePicker::make('fecha_entrada')
                            ->label('Fecha de Entrada')
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('fecha_vencimiento')
                            ->label('Fecha de Vencimiento'),

                        Forms\Components\Select::make('estado')
                            ->options([
                                'activo'  => 'Activo',
                                'agotado' => 'Agotado',
                            ])
                            ->default('activo')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo_lote')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('tipo')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'insumo' ? 'info' : 'success'),

                Tables\Columns\TextColumn::make('cantidad_actual')
                    ->label('Stock Actual')
                    ->numeric(2)
                    ->sortable()
                    ->badge()
                    ->color(fn (Lote $record): string =>
                        $record->cantidad_actual <= 0 ? 'danger' :
                        ($record->cantidad_actual < $record->cantidad_inicial * 0.2 ? 'warning' : 'success')
                    ),

                Tables\Columns\TextColumn::make('costo_unitario')
                    ->label('Costo ($)')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_vencimiento')
                    ->label('Vencimiento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (Lote $record): string =>
                        $record->fecha_vencimiento && $record->fecha_vencimiento->isPast() ? 'danger' :
                        ($record->fecha_vencimiento && $record->fecha_vencimiento->diffInDays(now()) <= 30 ? 'warning' : 'gray')
                    ),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'activo' ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('por_vencer')
                    ->label('Por vencer (30 días)')
                    ->query(fn (Builder $query): Builder => $query->porVencer(30))
                    ->toggle(),

                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'insumo'   => 'Insumo',
                        'producto' => 'Producto',
                    ]),

                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'activo'  => 'Activo',
                        'agotado' => 'Agotado',
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
            ->defaultSort('fecha_vencimiento', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLotes::route('/'),
            'create' => Pages\CreateLote::route('/create'),
            'edit'   => Pages\EditLote::route('/{record}/edit'),
        ];
    }
}
