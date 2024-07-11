<?php
//
//namespace App\Filament\Resources;
//
//use App\Filament\Resources\ProductResource\Pages;
//use App\Filament\Resources\ProductResource\RelationManagers;
//use App\Filament\Resources\PurchaseResource\RelationManagers\PurchaseDetailsRelationManager;
//use App\Filament\Resources\PurchaseResource\RelationManagers\PurchasesRelationManager;
//use App\Filament\Resources\SaleResource\RelationManagers\SaleDetailsRelationManager;
//use App\Models\Product;
//use Filament\Forms;
//use Filament\Forms\Form;
//use Filament\Resources\Resource;
//use Filament\Support\RawJs;
//use Filament\Tables;
//use Filament\Tables\Table;
//use Illuminate\Database\Eloquent\Builder;
//use Illuminate\Database\Eloquent\SoftDeletingScope;
//
//class ProductResource extends Resource
//{
//    protected static ?string $model = Product::class;
//
//    protected static ?string $navigationIcon = 'heroicon-o-cube';
//    protected static ?int $navigationSort = 3;
//
//    public static function form(Form $form): Form
//    {
//        return $form
//            ->schema([
//                Forms\Components\TextInput::make('name')
//                    ->required()
//                    ->maxLength(255),
//                Forms\Components\TextInput::make('sku')
//                    ->label('SKU')
//                    ->helperText('Product sku will auto generate. No need to fill')
//                    ->default(fn() => Product::generateSku())
//                    ->readOnly()
//                    ->maxLength(255),
//                Forms\Components\Textarea::make('description')
//                    ->columnSpanFull(),
//                Forms\Components\TextInput::make('purchase_price')
//                    ->stripCharacters(',')
//                    ->default(100)
//                    ->required()
//                    ->numeric()
//                    ->prefix('Ks'),
//                Forms\Components\TextInput::make('sale_price')
//                    ->stripCharacters(',')
//                    ->default(110)
//                    ->required()
//                    ->numeric()
//                    ->prefix('Ks'),
//                Forms\Components\Select::make('unit')
//                    ->required()
//                    ->default('piece')
//                    ->options([
//                        'piece' => 'Piece',
//                        'kg' => 'KG',
//                        'gram' => 'Gram',
//                        'liter' => 'Liter',
//                        'meter' => 'Meter',
//                        'box' => 'Box',
//                    ])
//                    ->native(false),
//
//            ]);
//    }
//
//    public static function table(Table $table): Table
//    {
//        return $table
//            ->columns([
//                Tables\Columns\TextColumn::make('sku')
//                    ->label('SKU')
//                    ->searchable(),
//                Tables\Columns\TextColumn::make('name')
//                    ->searchable(),
//                Tables\Columns\TextColumn::make('sale_price')
//                    ->money()
//                    ->sortable(),
//                Tables\Columns\TextColumn::make('stock')
//                    ->numeric()
//                    ->sortable(),
//                Tables\Columns\TextColumn::make('unit'),
//                Tables\Columns\TextColumn::make('creator.name')
//                    ->numeric()
//                    ->sortable(),
//                Tables\Columns\TextColumn::make('updater.name')
//                    ->numeric()
//                    ->sortable(),
//                Tables\Columns\TextColumn::make('created_at')
//                    ->dateTime()
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
//                Tables\Columns\TextColumn::make('updated_at')
//                    ->dateTime()
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
//            ])
//            ->filters([
//                //
//            ])
//            ->actions([
//                Tables\Actions\EditAction::make(),
//            ])
//            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
//            ]);
//    }
//
//    public static function getRelations(): array
//    {
//        return [
//            PurchaseDetailsRelationManager::class,
//            SaleDetailsRelationManager::class
//        ];
//    }
//
//    public static function getPages(): array
//    {
//        return [
//            'index' => Pages\ListProducts::route('/'),
//            'create' => Pages\CreateProduct::route('/create'),
//            'edit' => Pages\EditProduct::route('/{record}/edit'),
//        ];
//    }
//}
