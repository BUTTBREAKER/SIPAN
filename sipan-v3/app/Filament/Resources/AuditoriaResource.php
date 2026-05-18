<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditoriaResource\Pages;
use Spatie\Activitylog\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditoriaResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Auditoría';
    protected static ?string $modelLabel = 'Registro de Auditoría';
    protected static ?string $pluralModelLabel = 'Auditoría';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 10;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('log_name')
                    ->label('Módulo')
                    ->disabled(),
                Forms\Components\TextInput::make('event')
                    ->label('Evento')
                    ->disabled(),
                Forms\Components\TextInput::make('subject_type')
                    ->label('Tipo de Entidad')
                    ->disabled(),
                Forms\Components\TextInput::make('subject_id')
                    ->label('ID de Entidad')
                    ->disabled(),
                Forms\Components\TextInput::make('causer.name')
                    ->label('Usuario (Causante)')
                    ->disabled(),
                Forms\Components\KeyValue::make('properties.old')
                    ->label('Valores Anteriores')
                    ->disabled(),
                Forms\Components\KeyValue::make('properties.attributes')
                    ->label('Valores Nuevos')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y h:i:s A')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('causer.email')
                    ->label('Usuario (Email)')
                    ->searchable()
                    ->sortable()
                    ->default('Sistema'),
                Tables\Columns\TextColumn::make('log_name')
                    ->label('Módulo')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('event')
                    ->label('Acción')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Entidad')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject_id')
                    ->label('ID')
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label('Acción')
                    ->options([
                        'created' => 'Creación',
                        'updated' => 'Actualización',
                        'deleted' => 'Eliminación',
                    ]),
                Tables\Filters\SelectFilter::make('log_name')
                    ->label('Módulo')
                    ->options(fn () => Activity::select('log_name')->distinct()->pluck('log_name', 'log_name')->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Sin bulk actions para proteger los logs
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditorias::route('/'),
            'view' => Pages\ViewAuditoria::route('/{record}'),
        ];
    }
}
