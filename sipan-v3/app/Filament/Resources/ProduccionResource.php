<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProduccionResource\Pages;
use App\Models\Produccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProduccionResource extends Resource
{
    use \App\Traits\FiltrablePorSucursal;

    protected static ?string $model = Produccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Producción';

    protected static ?string $modelLabel = 'Producción';

    protected static ?string $pluralModelLabel = 'Producciones';

    protected static ?string $navigationGroup = 'Producción';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Orden de Producción')
                    ->schema([
                        Forms\Components\Select::make('id_producto')
                            ->label('Producto a Producir')
                            ->relationship('producto', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('id_receta')
                            ->label('Receta a Usar')
                            ->relationship('receta', 'nombre')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('cantidad_producida')
                            ->label('Cantidad a Producir')
                            ->numeric()
                            ->required()
                            ->default(1),

                        Forms\Components\DateTimePicker::make('fecha_produccion')
                            ->label('Fecha de Producción')
                            ->required()
                            ->default(now()),

                        Forms\Components\Select::make('id_sucursal')
                            ->label('Sucursal')
                            ->relationship('sucursal', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('estado')
                            ->options([
                                'completada' => 'Completada',
                                'en_proceso' => 'En Proceso',
                                'cancelada'  => 'Cancelada',
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

                Tables\Columns\TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('cantidad_producida')
                    ->label('Cantidad')
                    ->numeric(2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completada' => 'success',
                        'en_proceso' => 'warning',
                        'cancelada'  => 'danger',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('fecha_produccion')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'completada' => 'Completada',
                        'en_proceso' => 'En Proceso',
                        'cancelada'  => 'Cancelada',
                    ]),

                Tables\Filters\SelectFilter::make('id_sucursal')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre'),
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
            ->defaultSort('fecha_produccion', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProduccions::route('/'),
            'create' => Pages\CreateProduccion::route('/create'),
            'edit'   => Pages\EditProduccion::route('/{record}/edit'),
        ];
    }
}
