<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SugerenciaCompraResource\Pages;
use App\Models\SugerenciaCompra;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SugerenciaCompraResource extends Resource
{
    protected static ?string $model = SugerenciaCompra::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationLabel = 'Sugerencias de Compra';

    protected static ?string $modelLabel = 'Sugerencia';

    protected static ?string $pluralModelLabel = 'Sugerencias de Compra';

    protected static ?string $navigationGroup = 'Compras';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_insumo')
                    ->label('Insumo')
                    ->relationship('insumo', 'nombre')
                    ->required()
                    ->disabled(),
                    
                Forms\Components\Select::make('id_sucursal')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre')
                    ->required()
                    ->disabled(),
                    
                Forms\Components\TextInput::make('cantidad_sugerida')
                    ->label('Cantidad Sugerida')
                    ->numeric()
                    ->required()
                    ->disabled(),
                    
                Forms\Components\Textarea::make('motivo')
                    ->label('Motivo / Análisis Predictivo')
                    ->columnSpanFull()
                    ->disabled(),
                    
                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobada' => 'Aprobada',
                        'rechazada' => 'Rechazada',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('insumo.nombre')
                    ->label('Insumo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('cantidad_sugerida')
                    ->label('Cantidad Sugerida')
                    ->numeric()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'aprobada' => 'success',
                        'rechazada' => 'danger',
                        default => 'secondary',
                    }),
                    
                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Sugerencia')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobada' => 'Aprobada',
                        'rechazada' => 'Rechazada',
                    ])
                    ->default('pendiente'),
                    
                Tables\Filters\SelectFilter::make('id_sucursal')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Gestionar'),
                Tables\Actions\Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (SugerenciaCompra $record): bool => $record->estado === 'pendiente')
                    ->action(function (SugerenciaCompra $record) {
                        $record->update(['estado' => 'aprobada']);
                        // Logica para crear orden de compra aqui...
                    }),
                Tables\Actions\Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (SugerenciaCompra $record): bool => $record->estado === 'pendiente')
                    ->action(function (SugerenciaCompra $record) {
                        $record->update(['estado' => 'rechazada']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSugerenciaCompras::route('/'),
            // 'create' => Pages\CreateSugerenciaCompra::route('/create'), // Las sugerencias son automáticas
            'edit' => Pages\EditSugerenciaCompra::route('/{record}/edit'),
        ];
    }
}
