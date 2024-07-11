<?php

namespace App\Filament\Clusters\Products\Resources;

use App\Enums\VariantAttributeDisplayType;
use App\Filament\Clusters\Products;
use App\Filament\Clusters\Products\Resources\VariantAttributeResource\Pages;
use App\Filament\Clusters\Products\Resources\VariantAttributeResource\RelationManagers;
use App\Models\VariantAttribute;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VariantAttributeResource extends Resource
{
    protected static ?string $model = VariantAttribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Products::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('attribute')
                    ->required()
                    ->columnSpanFull(),
                Radio::make('display_type')
                    ->label('Display Type')
                    ->options(array_combine(
                        array_column(
                            array_filter(VariantAttributeDisplayType::cases(), fn ($case) => $case !== VariantAttributeDisplayType::Color),
                            'value'
                        ),
                        array_map(fn ($case) => $case->name,
                            array_filter(VariantAttributeDisplayType::cases(), fn ($case) => $case !== VariantAttributeDisplayType::Color)
                        )
                    ))
                    ->default(VariantAttributeDisplayType::Radio),
                Forms\Components\Repeater::make('values')
                    ->label('Values')
                    ->relationship('values')
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->required(),
                        Forms\Components\ColorPicker::make('color_code')
                        ->hidden(true),
                        FileUpload::make('image')
                            ->image()
                            ->imageCropAspectRatio('1:1')
                            ->hidden(true),
                        Forms\Components\TextInput::make('default_extra_price')
                            ->numeric()
                            ->inputMode('decimal')
                            ->default(0.00),
                    ])
                    ->reorderable()
                    ->cloneable()
                    ->columns(2)
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attribute')
                    ->searchable(),
                Tables\Columns\TextColumn::make('display_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('values.value')
                    ->label('Values')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListVariantAttributes::route('/'),
            'create' => Pages\CreateVariantAttribute::route('/create'),
            'view' => Pages\ViewVariantAttribute::route('/{record}'),
            'edit' => Pages\EditVariantAttribute::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
