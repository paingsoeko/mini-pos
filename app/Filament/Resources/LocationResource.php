<?php

namespace App\Filament\Resources;

use App\Enums\Location\StockFlow;
use Filament\Forms;
use Filament\Tables;
use App\Helper\Helper;
use App\Models\Location;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\Location\Type;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LocationResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\LocationResource\RelationManagers;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Location type')
                    ->default(Type::internal)
                    ->options(Type::class)
                    ->native(false)
                    ->required(),
                Forms\Components\Select::make('parent_location_id')
                    ->label('Parent Location')
                    ->relationship(name: 'parent_location', titleAttribute: 'name', ignoreRecord: true)
                    ->searchable(['name'])
                    ->native(false)
                    ->noSearchResultsMessage('No Locations found.')
                    ->preload(),
                Forms\Components\Select::make('stock_flow')
                    ->label('Stock Flow')
                    ->default(StockFlow::fifo)
                    ->options(StockFlow::class)
                    ->native(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent_location.name')
                    ->default('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ]);
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
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
