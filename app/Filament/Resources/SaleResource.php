<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockBalance;
use App\Models\Student;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Notifications\Action;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Voucher')
                        ->columns([
                            'sm' => 3,
                            'xl' => 3,
                            '2xl' => 3,
                        ])
                        ->schema([
                            Forms\Components\TextInput::make('voucher_no')
                                ->default(fn() => Sale::generateVoucherNo())
                                ->readOnly()
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Select::make('customer_id')
                                ->label('Customer')
                                ->native(false)
                                ->createOptionAction(
                                    fn (Action $action) => $action->modalWidth('md'),
                                )
                                ->options(Contact::where('type', 'customer')->get()->mapWithKeys(function ($table) {
                                    return [$table->id => $table->name];
                                }))
                                ->default(1)

                                ->searchable(['name'])
                                ->preload()
                                ->required(),
                            Forms\Components\Select::make('table_id')
                                ->label('Table')
                                ->native(false)
                                ->searchable(['name'])
                                ->options(\App\Models\Table::all()->mapWithKeys(function ($table) {
                                    $status = $table->is_use ? "Used" : "Free";
                                    return [$table->id => $table->name . " (" . $status . ")"];
                                }))

                                ->preload(),
                            Forms\Components\DateTimePicker::make('sale_date')
                                ->default(now())
                                ->required(),
                            Forms\Components\Select::make('status')
                                ->default('ordered')
                                ->options([
                                    'ordered' => 'Ordered',
                                    'preparing' => 'Preparing',
                                    'served' => 'Served',
                                    'completed' => 'Completed',
                                    'canceled' => 'Canceled',
                                ])
                                ->native(false)
                                ->required()
                                ->reactive(),

                            Forms\Components\Textarea::make('remark')
                                ->columnSpanFull(),

                        ]),
                    Wizard\Step::make('Sale Items')
                        ->schema([
                            TableRepeater::make('details')
                                ->label(false)
                                ->addActionLabel('Add Product')
                                ->cloneable()
                                ->headers([
                                    Header::make('Product'),
                                    Header::make('Quantity')->width('150px'),
                                    Header::make('Selling Price')->width('150px'),
                                    Header::make('Unit')->width('150px'),
                                    Header::make('Subtotal')->width('150px'),
                                ])
                                ->relationship('details')
                                ->schema([
                                    Forms\Components\Select::make('product_id')
                                        ->label('Product')
                                        ->native(false)
                                        ->relationship('product', 'name')
                                        ->options(Product::all()->mapWithKeys(function ($product) {
                                            return [$product->id => sprintf('%s (Ks%s)', $product->name, $product->sale_price)];

                                        }))

                                        // Disable options that are already selected in other rows
                                        ->disableOptionWhen(function ($value, $state, Get $get) {
                                            return collect($get('../*.product_id'))
                                                ->reject(fn($id) => $id == $state)
                                                ->filter()
                                                ->contains($value);
                                        })
                                        ->searchable(['name'])
                                        ->preload()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $product = Product::find($state);
                                            $set('unit', $product ? $product->unit : null);

                                            $currentQty = StockBalance::where('product_id', $state)->sum('current_quantity');
                                            $set('current_qty', $currentQty);
                                            $set('sale_price', $product->sale_price);
                                            $set('subtotal_price', $product->sale_price);
                                        }),
                                    Forms\Components\TextInput::make('quantity')
                                        ->required()
                                        ->rules([
                                            fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                                $currentQty = StockBalance::where('product_id', $get('product_id'))->sum('current_quantity');
                                                if ($value > $currentQty) {
                                                    $fail("Can't sale more than $currentQty");
                                                }
                                            },
                                        ])
                                        ->afterStateUpdated(function ($state, callable $set, $get) {
                                            $currentQty = $get('current_qty');

                                                $set('subtotal_price', $state * $get('sale_price'));


                                        }),
                                    TextInput::make('sale_price')
                                    ->readOnly(),
                                    Forms\Components\TextInput::make('unit')
                                        ->required()
                                        ->readOnly(),
                                    TextInput::make('subtotal_price')
                                    ->readOnly(),
                                ])
                                ->columnSpan('full')
                                // Repeatable field is live so that it will trigger the state update on each change
                                ->live()
                                // After adding a new row, we need to update the totals
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::updateTotals($get, $set);
                                })


                        ]),
                    Wizard\Step::make('Payment')
                        ->schema([
                            Forms\Components\TextInput::make('total_price')
                                ->default(0)
                                ->reactive()
                                ->required()
                                ->numeric(),
                            Forms\Components\Select::make('payment_status')
                                ->default('pending')
                                ->options([
                                    'pending' => 'Pending',
                                    'paid' => 'Paid',
                                    'refunded' => 'Refunded',
                                ])
                                ->reactive()
                                ->native(false)
                                ->required(),
                            Forms\Components\Select::make('payment_type')
                                ->default('cash')
                                ->required()
                                ->options([
                                    'cash' => 'Cash',
                                    'online' => 'Online Payment',
                                ])
                                ->native(false),
                            TextInput::make('paid_amount')
                                ->reactive()
                                ->afterStateUpdated(fn ($state, callable $set, $get) => $set('balance_amount', max(0, $get('total_price') - $state))),
                            TextInput::make('balance_amount')
                            ->reactive()
                            ->readOnly(),
                        ]),
                ]) ->columnSpanFull(),
            ]);
    }

// This function updates totals based on the selected products and quantities
    public static function updateTotals(Get $get, Set $set): void
    {
        // Retrieve all selected products and remove empty rows
        $selectedProducts = collect($get('details'))->filter(fn($item) => !empty($item['product_id']) && !empty($item['quantity']));

        // Retrieve prices for all selected products
        $prices = Product::find($selectedProducts->pluck('product_id'))->pluck('sale_price', 'id');

        // Calculate subtotal based on the selected products and quantities
        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) use ($prices) {
            return $subtotal + ($prices[$product['product_id']] * $product['quantity']);
        }, 0);

        // Update the state with the new values
//        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('total_price', number_format($subtotal + ($subtotal * ($get('taxes') / 100)), 2, '.', ''));
        $set('balance_amount', number_format($subtotal + ($subtotal * ($get('taxes') / 100)), 2, '.', ''));

    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('voucher_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('table.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ordered' => 'warning',
                        'preparing' => 'gray',
                        'served' => 'info',
                        'completed' => 'success',
                        'canceled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'info',
                        'paid' => 'success',
                        'refunded' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_type'),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }

    public static function getDetailsFormSchema(): array
    {
        return [

            Forms\Components\TextInput::make('voucher_no')
                ->default(fn() => Sale::generateVoucherNo())
                ->readOnly()
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('customer_id')
                ->label('Customer')
                ->default('Walk-In Customer')
                ->native(false)
                ->createOptionAction(
                    fn (Action $action) => $action->modalWidth('md'),
                )
                ->relationship(name: 'customer', titleAttribute: 'name')
                ->searchable(['name'])
                ->preload()
                ->required(),
            Forms\Components\Select::make('floor_id')
                ->label('Floor')
                ->native(false)
                ->relationship(name: 'floor', titleAttribute: 'name')
                ->searchable(['name'])
                ->preload(),
            Forms\Components\Select::make('table_id')
                ->label('Table')
                ->native(false)
                ->relationship(name: 'table', titleAttribute: 'name')
                ->searchable(['name'])
                ->preload(),
            Forms\Components\DateTimePicker::make('sale_date')
                ->default(now())
                ->required(),
            Forms\Components\Select::make('status')
                ->default('ordered')
                ->options([
                    'ordered' => 'Ordered',
                    'preparing' => 'Preparing',
                    'served' => 'Served',
                    'completed' => 'Completed',
                    'canceled' => 'Canceled',
                ])
                ->native(false)
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if (in_array($state, ['ordered', 'preparing', 'served'])) {
                        $set('payment_status', 'pending');
                    } elseif ($state === 'completed') {
                        $set('payment_status', 'paid');
                    } elseif ($state === 'canceled') {
                        $set('payment_status', 'refunded');
                    }
                }),
            Forms\Components\Select::make('payment_status')
                ->default('pending')
                ->options([
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'refunded' => 'Refunded',
                ])
                ->reactive()
                ->native(false)
                ->required(),
            Forms\Components\Textarea::make('remark')
                ->columnSpanFull(),
                Forms\Components\TextInput::make('total_price')
                    ->required()
                    ->numeric(),
            Forms\Components\Select::make('payment_type')
                ->default('cash')
                ->required()
                ->options([
                    'cash' => 'Cash',
                    'online' => 'Online Payment',
                ])
                ->native(false),
        ];
    }


    public static function getItemsRepeater(): TableRepeater
    {
     return TableRepeater::make('details')
             ->label(false)
             ->addActionLabel('Add Product')
             ->cloneable()
             ->headers([
                 Header::make('Product'),
                 Header::make('Current Quantity')->width('150px'),
                 Header::make('Price')->width('150px'),
                 Header::make('Quantity')->width('150px'),
                 Header::make('Unit')->width('200px'),
                 Header::make('Total')->width('150px'),
             ])
             ->relationship('details')
             ->schema([
                 Forms\Components\Select::make('product_id')
                     ->label('Product')
                     ->native(false)
                     ->relationship(name: 'product', titleAttribute: 'name')
                     ->searchable(['name'])
                     ->preload()
                     ->reactive()
                     ->afterStateUpdated(function ($state, callable $set) {
                         $product = Product::find($state);
                         $set('sale_price', $product ? $product->sale_price : 0);
                         $set('quantity', 1);
                         $set('unit', $product ? $product->unit : null);
                         $set('subtotal_price', $product ? $product->purchase_price : null);
                         $set('total_price', $product->purchase_price);

                         $currentQty = StockBalance::where('product_id', $state)->sum('current_quantity');
                         $set('current_qty', $currentQty);
                     }),
                 TextInput::make('current_qty')
                     ->reactive()
                     ->readOnly(),
                 TextInput::make('sale_price')
                     ->label('Sale Price')
                     ->reactive()
                     ->afterStateUpdated(fn ($state, callable $set, $get) => $set('subtotal_price', $state * $get('quantity'))),
                 Forms\Components\TextInput::make('quantity')
                     ->reactive()
                     ->afterStateUpdated(function ($state, callable $set, $get) {
                         $currentQty = $get('current_qty');
                         if ($state > $currentQty) {
                             $set('quantity', $currentQty); // Reset to max available quantity

                         } else {
                             $set('subtotal_price', $state * $get('purchase_price'));
                         }
                     }),
                 Forms\Components\Select::make('unit')
                     ->required()
                     ->options([
                         'piece' => 'Piece',
                         'kg' => 'KG',
                         'gram' => 'Gram',
                         'liter' => 'Liter',
                         'meter' => 'Meter',
                         'box' => 'Box',
                     ])
                     ->native(false),
                 Forms\Components\TextInput::make('subtotal_price')
                     ->label('Subtotal Price')
                     ->readOnly(),
             ])
             ->columnSpan('full');
    }
}
