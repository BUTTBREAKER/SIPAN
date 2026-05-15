<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompraResource\Pages;
use App\Models\Compra;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompraResource extends Resource
{
    protected static ?string $model = Compra::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Compras';

    protected static ?string $modelLabel = 'Compra';

    protected static ?string $pluralModelLabel = 'Compras';

    protected static ?string $navigationGroup = 'Compras';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos de la Compra')
                    ->schema([
                        Forms\Components\Select::make('id_proveedor')
                            ->label('Proveedor')
                            ->relationship('proveedor', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('numero_factura')
                            ->label('Nro. Factura')
                            ->maxLength(50),

                        Forms\Components\DateTimePicker::make('fecha_compra')
                            ->label('Fecha de Compra')
                            ->required()
                            ->default(now()),

                        Forms\Components\TextInput::make('total')
                            ->label('Total ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),

                        Forms\Components\Select::make('id_sucursal')
                            ->label('Sucursal')
                            ->relationship('sucursal', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('estado')
                            ->options([
                                'completada' => 'Completada',
                                'pendiente'  => 'Pendiente',
                                'anulada'    => 'Anulada',
                            ])
                            ->default('completada')
                            ->required(),

                        Forms\Components\Textarea::make('notas')
                            ->label('Notas')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('proveedor.nombre')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('numero_factura')
                    ->label('Factura')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->color('success'),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completada' => 'success',
                        'pendiente'  => 'warning',
                        'anulada'    => 'danger',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('fecha_compra')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_proveedor')
                    ->label('Proveedor')
                    ->relationship('proveedor', 'nombre'),

                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'completada' => 'Completada',
                        'pendiente'  => 'Pendiente',
                        'anulada'    => 'Anulada',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_compra', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCompras::route('/'),
            'create' => Pages\CreateCompra::route('/create'),
            'edit'   => Pages\EditCompra::route('/{record}/edit'),
        ];
    }
}
