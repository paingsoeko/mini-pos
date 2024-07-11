<?php

namespace App\Filament\Clusters\Products\Resources;

use App\Enums\Product\Type;
use App\Filament\Clusters\Products;
use App\Filament\Clusters\Products\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use function Symfony\Component\Translation\t;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Products::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                Forms\Components\Select::make('type')
                                    ->options(Type::class)
                                    ->default(Type::storable)
                                    ->native(false)
                                    ->required(),
                                Forms\Components\Toggle::make('can_be_sold')
                                    ->default(true)
                                    ->required(),
                                Forms\Components\Toggle::make('can_be_purchased')
                                    ->default(true)
                                    ->required(),
                                Forms\Components\MarkdownEditor::make('internal_notes')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Pricing and Units')
                            ->schema([
                                Forms\Components\Select::make('unit_id')
                                    ->required()
                                    ->native(false)
                                    ->relationship(name: 'unit', titleAttribute: 'name')
                                    ->default(1)
                                    ->searchable(['name'])
                                    ->preload(),
                                Forms\Components\Select::make('purchase_unit_id')
                                    ->required()
                                    ->native(false)
                                    ->relationship(name: 'purchaseUnit', titleAttribute: 'name')
                                    ->default(1)
                                    ->searchable(['name'])
                                    ->preload(),
                                Forms\Components\TextInput::make('sales_price')
                                    ->numeric()
                                    ->default(0.00)
                                    ->inputMode('decimal')
//                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->prefix('$'),

                                Forms\Components\TextInput::make('cost')
                                    ->label('Cost per item')
                                    ->helperText('Customers won\'t see this price.')
                                    ->numeric()
                                    ->default(0.00)
                                    ->inputMode('decimal')
//                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->prefix('$'),
                            ])
                            ->columns(2),
                        Forms\Components\Section::make('Inventory')
                            ->schema([
                                Forms\Components\TextInput::make('sku')
                                    ->label('SKU (Stock Keeping Unit)')
                                    ->helperText("If you don't use suk, it will be generated automatically")
                                    ->unique(Product::class, 'sku', ignoreRecord: true)
                                    ->maxLength(255),
//                            ->label('Barcode (ISBN, UPC, GTIN, etc.)')
                                Forms\Components\TextInput::make('product_code')
                                    ->label('Product Code')
                                    ->unique(Product::class, 'product_code', ignoreRecord: true)
                                    ->maxLength(255),
//
//                                Forms\Components\TextInput::make('qty')
//                                    ->label('Quantity')
//                                    ->numeric()
//                                    ->rules(['integer', 'min:0'])
//                                    ->required(),

                                Forms\Components\TextInput::make('security_stock')
                                    ->helperText('The safety stock is the limit stock for your products which alerts you if the product stock will soon be out of stock.')
                                    ->numeric()
                                    ->rules(['integer', 'min:0']),
                            ])
                            ->columns(2),
//
//                        Forms\Components\Section::make('Shipping')
//                            ->schema([
//                                Forms\Components\Checkbox::make('backorder')
//                                    ->label('This product can be returned'),
//
//                                Forms\Components\Checkbox::make('requires_shipping')
//                                    ->label('This product will be shipped'),
//                            ])
//                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Image')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->image()
                                    ->hiddenLabel(),
                            ]),

                        Forms\Components\Section::make('Associations')
                            ->schema([

//                                Select::make('tags')
//                                    ->multiple()
//                                    ->options(Tag::all()->pluck('name', 'id'))
//                                    ->label('Tags'),
//                                Forms\Comp    TagsInput::make('tags')
//                                ->relationonents\Select::make('product_category_id')
//                                    ->relationship('categories', 'name')
//                                    ->multiple(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sales_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_category_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_unit_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('can_be_sold')
                    ->boolean(),
                Tables\Columns\IconColumn::make('can_be_purchased')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_by')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
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
