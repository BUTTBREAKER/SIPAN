<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InsumoResource\Pages;
use App\Models\Insumo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InsumoResource extends Resource
{
    protected static ?string $model = Insumo::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Insumos';

    protected static ?string $modelLabel = 'Insumo';

    protected static ?string $pluralModelLabel = 'Insumos';

    protected static ?string $navigationGroup = 'Inventario';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Insumo')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(100),
                        
                        Forms\Components\TextInput::make('codigo')
                            ->label('Código')
                            ->maxLength(50),

                        Forms\Components\Select::make('unidad_medida')
                            ->label('Unidad de Medida')
                            ->options([
                                'kg' => 'Kilogramos (kg)',
                                'g' => 'Gramos (g)',
                                'L' => 'Litros (L)',
                                'ml' => 'Mililitros (ml)',
                                'unidad' => 'Unidades',
                            ])
                            ->required()
                            ->default('kg'),

                        Forms\Components\TextInput::make('precio_unitario')
                            ->label('Precio Unitario ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00),
                            
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Inventario y Ubicación')
                    ->schema([
                        Forms\Components\TextInput::make('stock_actual')
                            ->label('Stock Actual')
                            ->numeric()
                            ->default(0.00)
                            ->required(),

                        Forms\Components\TextInput::make('stock_minimo')
                            ->label('Stock Mínimo (Alerta)')
                            ->numeric()
                            ->default(0.00)
                            ->required(),

                        Forms\Components\Select::make('id_sucursal')
                            ->label('Sucursal')
                            ->relationship('sucursal', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true),
                    ])->columns(2),
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
                
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('stock_actual')
                    ->label('Stock')
                    ->numeric(2)
                    ->sortable()
                    ->badge()
                    ->color(fn (Insumo $record): string => $record->stock_actual <= $record->stock_minimo ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('unidad_medida')
                    ->label('Unidad'),

                Tables\Columns\TextColumn::make('precio_unitario')
                    ->label('Precio ($)')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\IconColumn::make('activo')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('stock_bajo')
                    ->label('Stock Bajo')
                    ->query(fn (Builder $query): Builder => $query->stockBajo())
                    ->toggle(),
                
                Tables\Filters\SelectFilter::make('id_sucursal')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre'),
            ])
            ->actions([
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInsumos::route('/'),
            'create' => Pages\CreateInsumo::route('/create'),
            'edit' => Pages\EditInsumo::route('/{record}/edit'),
        ];
    }
}
