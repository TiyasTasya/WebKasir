<?php

namespace App\Filament\Resources;

use App\Filament\Exports\OrderExporter;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\OrderDetailRelationManager;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Dom\Text;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Actions\ExportAction;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $activeNavigationIcon = 'heroicon-s-shopping-cart';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'baru')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 0 ? 'info' : 'primary';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah Order Dengan Status Baru';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'id',
            'customer.nama'
        ];
    }


    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Order ID' => $record->id  ?? 'N/A',
            'Nama Customer' => $record->customer?->nama  ?? 'N/A',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('tanggal_order')
                    ->default(now())
                    ->required()
                    ->disabled()
                    ->hiddenLabel()
                    ->dehydrated()
                    ->prefix('Date:')
                    ->columnSpanFull(),
                Group::make()
                    ->schema([
                        Section::make()
                            ->description('Informasi Customer')
                            ->schema([
                                Select::make('customer_id')
                                    ->relationship('customer', 'nama')
                                    ->label('Nama')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        $customer = Customer::find($state);
                                        $set('no_handphone', $customer->no_handphone ?? null);
                                        $set('alamat', $customer->alamat ?? null);
                                    })
                                    ->createOptionForm([
                                        TextInput::make('nama'),
                                        TextInput::make('no_handphone'),
                                        TextInput::make('alamat'),
                                    ]),
                                Placeholder::make('no_handphone')
                                    ->content(fn(Get $get) => Customer::find($get('customer_id'))?->no_handphone ?? '-'),
                                Placeholder::make('alamat')
                                    ->content(fn(Get $get) => Customer::find($get('customer_id'))?->alamat ?? '-'),
                            ])->columns(3),


                        Section::make()
                            ->description('Detail Order')
                            ->schema([
                                Repeater::make('orderdetail')
                                    ->relationship()
                                    ->schema([
                                        Select::make('product_id')
                                            ->relationship('product', 'nama', modifyQueryUsing: fn(\Illuminate\Database\Eloquent\Builder $query)
                                            => $query->where('is_active', true))
                                            ->reactive()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $product = Product::find($state);
                                                $harga = $product->harga ?? 0;
                                                $set('harga', $harga);
                                                $qty = $get('qty') ?? 1;
                                                $set('qty', $qty);
                                                $subtotal = $harga * $qty;
                                                $set('subtotal', $subtotal);


                                                $items = $get('../../orderdetail') ?? [];
                                                $total = collect($items)->sum(fn($item) => $item['subtotal'] ?? 0);
                                                $set('../../total_harga', $total);

                                                $tax_rate = $get('../../tax_rate');
                                                $taxt_amount = $total * ($tax_rate / 100);
                                                $set('../../tax_amount', $taxt_amount);
                                                $set('../../total_pembayaran', $total + $taxt_amount);

                                                $diskon = $get('../../diskon');
                                                $jumlah_diskon = $total * $diskon / 100;
                                                $set('../../jumlah_diskon', $total - $jumlah_diskon);
                                                $set('../../total_pembayaran', $total - $jumlah_diskon);
                                            })
                                            ->searchable()
                                            ->columnSpanFull(),
                                        TextInput::make('harga')
                                            ->readOnly()
                                            ->numeric()
                                            ->prefix('Rp.')
                                            ->formatStateUsing(fn($state, Get $get)
                                            => $state ?? Product::find($get('product_id'))?->harga ?? 0),

                                        TextInput::make('qty')
                                            ->numeric()
                                            ->default(1)
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $harga = $get('harga') ?? 0;
                                                $set('subtotal', $harga * $state);

                                                $items = $get('../../orderdetail') ?? [];
                                                $total = collect($items)->sum(fn($item) => $item['subtotal'] ?? 0);
                                                $set('../../total_harga', $total);

                                                $tax_rate = $get('../../tax_rate');
                                                $taxt_amount = $total * ($tax_rate / 100);
                                                $set('../../tax_amount', $taxt_amount);


                                                $diskon = $get('../../diskon');
                                                $jumlah_diskon = $total * $diskon / 100;
                                                $set('../../jumlah_diskon', $total - $jumlah_diskon);
                                                $set('../../total_pembayaran', $total - $jumlah_diskon + $taxt_amount);
                                            })
                                            ->minValue(1)
                                            ->maxValue(function (Get $get) {
                                                $productID = $get('product_id');
                                                $product = Product::find($productID);
                                                return $product?->stok ?? 0;
                                            }),
                                        TextInput::make('subtotal')
                                            ->numeric()
                                            ->readOnly()
                                            ->default(0)
                                            ->prefix('Rp.')
                                    ])->columns(3)
                                    ->hiddenLabel()
                                    ->addAction(
                                        fn(Forms\Components\Actions\Action $action) => $action
                                            ->label('Tambah Produk')
                                            ->color('primary')
                                            ->icon('heroicon-o-plus')
                                    )
                            ]),


                    ])->columnSpan(2),

                Section::make()
                    ->description('Informasi Pembayaran')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'baru' => 'Baru',
                                'diproses' => 'Disposes',
                                'cancel' => 'Cancel',
                                'selesai' => "Selesai"
                            ])->default('baru')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('total_harga')
                            ->required()
                            ->numeric()
                            ->readOnly()
                            ->columnSpanFull()
                            ->prefix('Rp.')
                            ->default(0),

                        TextInput::make('tax_rate')
                            ->columnSpan(2)
                            ->default(11)
                            ->suffix('%')
                            ->readOnly(),
                        TextInput::make('tax_amount')
                            ->columnSpan(2)
                            ->prefix('Rp.')
                            ->default(0),
                        TextInput::make('diskon')
                            ->columnSpan(2)
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $diskon = floatval($state) ?? 0;

                                $totalHarga = floatval($get('total_harga')) ?? 0;

                                $nominalDiskon = ($totalHarga * $diskon) / 100;

                                $totalPembayaran = $totalHarga - $nominalDiskon;

                                $set('jumlah_diskon', $nominalDiskon);
                                $set('total_pembayaran', $totalPembayaran);
                            })
                            ->suffix('%')
                            ->default(0)
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                        TextInput::make('jumlah_diskon')
                            ->columnSpan(2)
                            ->readOnly()
                            ->prefix('Rp.')
                            ->default(0),
                        TextInput::make('total_pembayaran')
                            ->columnSpanFull()
                            ->readOnly()
                            ->prefix('Rp.')
                            ->default(0),

                        Select::make('metode_pembayaran')
                            ->columnSpan(2)
                            ->options([
                                'cash' => 'Cash',
                                'debit' => 'Debit',
                                'credit' => 'Credit',
                                'qris' => 'Qris'
                            ])->default('cash'),
                        Select::make('status_pembayaran')
                            ->columnSpan(2)
                            ->options([
                                'belum dibayar' => 'Belum Dibayar',
                                'dibayar' => 'Dibayar'
                            ])->default('belum dibayar')
                    ])->columnSpan(1)
                    ->columns(4),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('OrderID'),
                Tables\Columns\TextColumn::make('customer.nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_harga')
                    ->prefix('Rp.')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('tax_amount')
                    ->prefix('Rp.')
                    ->numeric(),

                TextColumn::make('diskon')
                    ->suffix('%'),

                TextColumn::make('jumlah_diskon')
                    ->prefix('Rp.')
                    ->numeric(),

                TextColumn::make('total_pembayaran')
                    ->numeric()
                    ->prefix('Rp.'),

                TextColumn::make('metode_pembayaran')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status_pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'dibayar' => 'success',
                        'belum dibayar' => 'danger',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'baru' => 'info',
                        'diproses' => 'warning',
                        'cancel' => 'danger',
                        'selesai' => 'success',
                    }),

                Tables\Columns\TextColumn::make('tanggal_order')
                    ->date()
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
                ActionsActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(OrderExporter::class)
                    ->label('Download Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
