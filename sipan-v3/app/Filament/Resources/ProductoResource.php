<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductoResource extends Resource
{
    use \App\Traits\FiltrablePorSucursal;

    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';

    protected static ?string $navigationLabel = 'Productos';

    protected static ?string $modelLabel = 'Producto';

    protected static ?string $pluralModelLabel = 'Productos';

    protected static ?string $navigationGroup = 'Inventario';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Producto')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(100),
                        
                        Forms\Components\TextInput::make('codigo')
                            ->label('Código')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('categoria')
                            ->label('Categoría')
                            ->maxLength(100),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Precios y Costos')
                    ->schema([
                        Forms\Components\TextInput::make('precio_venta')
                            ->label('Precio de Venta ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00)
                            ->required(),

                        Forms\Components\TextInput::make('precio_costo')
                            ->label('Precio de Costo ($)')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00)
                            ->helperText('Puede calcularse automáticamente desde la Receta'),
                    ])->columns(2),

                Forms\Components\Section::make('Inventario')
                    ->schema([
                        Forms\Components\TextInput::make('stock_actual')
                            ->label('Stock Actual')
                            ->numeric()
                            ->default(0.00)
                            ->required(),

                        Forms\Components\TextInput::make('stock_minimo')
                            ->label('Stock Mínimo')
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
                
                Tables\Columns\TextColumn::make('categoria')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('precio_venta')
                    ->label('Precio ($)')
                    ->money('USD')
                    ->sortable()
                    ->color('success'),

                Tables\Columns\TextColumn::make('stock_actual')
                    ->label('Stock')
                    ->numeric(2)
                    ->sortable()
                    ->badge()
                    ->color(fn (Producto $record): string => $record->stock_actual <= $record->stock_minimo ? 'danger' : 'success'),

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
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}
