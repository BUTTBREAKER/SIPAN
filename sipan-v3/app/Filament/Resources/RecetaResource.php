<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecetaResource\Pages;
use App\Models\Receta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RecetaResource extends Resource
{
    protected static ?string $model = Receta::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Recetas';

    protected static ?string $modelLabel = 'Receta';

    protected static ?string $pluralModelLabel = 'Recetas';

    protected static ?string $navigationGroup = 'Producción';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Receta')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(150),

                        Forms\Components\Select::make('id_producto')
                            ->label('Producto Resultante')
                            ->relationship('producto', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('rendimiento')
                            ->label('Rendimiento (unidades producidas)')
                            ->numeric()
                            ->required()
                            ->default(1),

                        Forms\Components\Select::make('id_sucursal')
                            ->label('Sucursal')
                            ->relationship('sucursal', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Textarea::make('instrucciones')
                            ->label('Instrucciones de Preparación')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),

                // ─── Repeater de Insumos ──────────────────────────────────
                Forms\Components\Section::make('Insumos Requeridos')
                    ->description('Agrega los insumos necesarios para esta receta')
                    ->schema([
                        Forms\Components\Repeater::make('insumos')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('id_insumo')
                                    ->label('Insumo')
                                    ->relationship('insumo', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('cantidad')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0.0001)
                                    ->default(1),

                                Forms\Components\Select::make('unidad_medida')
                                    ->label('Unidad')
                                    ->options([
                                        'kg'     => 'kg',
                                        'g'      => 'g',
                                        'L'      => 'L',
                                        'ml'     => 'ml',
                                        'unidad' => 'und',
                                    ])
                                    ->default('kg')
                                    ->required(),
                            ])
                            ->columns(4)
                            ->addActionLabel('+ Agregar Insumo')
                            ->reorderable()
                            ->collapsible()
                            ->defaultItems(1)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rendimiento')
                    ->label('Rendimiento')
                    ->numeric()
                    ->suffix(' und')
                    ->sortable(),

                Tables\Columns\TextColumn::make('insumos_count')
                    ->label('Insumos')
                    ->counts('insumos')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
            ->defaultSort('nombre');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRecetas::route('/'),
            'create' => Pages\CreateReceta::route('/create'),
            'edit'   => Pages\EditReceta::route('/{record}/edit'),
        ];
    }
}
