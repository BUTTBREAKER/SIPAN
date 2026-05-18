<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NegocioResource\Pages;
use App\Filament\Resources\NegocioResource\RelationManagers;
use App\Models\Negocio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NegocioResource extends Resource
{
    protected static ?string $model = Negocio::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Negocios';
    protected static ?string $modelLabel = 'Negocio';
    protected static ?string $pluralModelLabel = 'Negocios';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Principal')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\Select::make('id_sucursal')
                            ->label('Sucursal Principal')
                            ->relationship('sucursal', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make('Contacto y Ubicación')
                    ->schema([
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20)
                            ->default(null),
                        Forms\Components\TextInput::make('correo')
                            ->label('Correo Electrónico')
                            ->email()
                            ->maxLength(100)
                            ->default(null),
                        Forms\Components\Textarea::make('direccion')
                            ->label('Dirección')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Branding')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('logos')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular()
                    ->defaultImageUrl(url('/assets/img/default-logo.png')),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal Principal')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('correo')
                    ->searchable(),
            ])
            ->filters([
                //
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
            RelationManagers\SucursalesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNegocios::route('/'),
            'create' => Pages\CreateNegocio::route('/create'),
            'edit' => Pages\EditNegocio::route('/{record}/edit'),
        ];
    }
}
