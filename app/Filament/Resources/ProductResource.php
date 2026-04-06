<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $activeNavigationIcon = 'heroicon-s-squares-2x2';
    protected static ?string $navigationGroup = 'Management Product';
    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah Product Dengan Status Aktif';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'nama',
            'harga',
            'stok',
            'sku',
            'barcode',
            'brand.nama',
            'category.nama',
            'subcategory.nama'
        ];
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Nama' => $record->nama  ?? 'N/A',
            'Harga' => $record->harga ?? 'N/A',
            'Stok' => $record->stok  ?? 'N/A',
            'SKU' => $record->sku  ?? 'N/A',
            'Barcode' => $record->barcode  ?? 'N/A',
            'Brand' => $record->brand?->nama  ?? 'N/A',
            'Category' => $record->category?->nama  ?? 'N/A',
            'Subcategory' => $record->subcategory?->nama  ?? 'N/A',
        ];
    }

    public static function generateSku(Get $get, Set $set): void
    {
        // 1. Perbaikan Typo: dari 'drand_id' menjadi 'brand_id'
        $brandId = $get('brand_id');
        $categoryId = $get('category_id');
        $subcategoryId = $get('subcategory_id');

        // 2. Pastikan semua ID sudah terpilih sebelum lanjut
        if (!$brandId || !$categoryId || !$subcategoryId) {
            return;
        }

        $brand = Brand::find($brandId);
        $category = Category::find($categoryId);
        $subcategory = SubCategory::find($subcategoryId);

        // 3. Pastikan data model benar-benar ditemukan di database
        if (!$category || !$subcategory || !$brand) {
            return;
        }

        // Ambil 3 huruf pertama dan ubah ke huruf kapital
        $catCode = strtoupper(substr($category->nama, 0, 3));
        $subCode = strtoupper(substr($subcategory->nama, 0, 3));
        $brandCode = strtoupper(substr($brand->nama, 0, 3));

        // 4. Cari SKU terakhir untuk kombinasi ini
        $lastProduct = Product::where('category_id', $categoryId)
            ->where('subcategory_id', $subcategoryId)
            ->where('brand_id', $brandId)
            ->latest('id') // Lebih efisien daripada orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;

        if ($lastProduct && $lastProduct->sku) {
            $parts = explode('-', $lastProduct->sku);
            $lastNumber = intval(end($parts));
            $nextNumber = $lastNumber + 1;
        }

        // 5. Generate SKU format: CAT-SUB-BRA-001
        $sku = sprintf('%s-%s-%s-%03d', $catCode, $subCode, $brandCode, $nextNumber);

        // Set nilai ke field SKU
        $set('sku', $sku);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Section::make([
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('harga_dasar')
                            ->numeric()
                            ->prefix('Rp.'),
                        Forms\Components\TextInput::make('harga')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('stok')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('sku'),

                        Forms\Components\TextInput::make('barcode'),

                        Group::make([
                            Forms\Components\Toggle::make('is_active')
                                ->required(),
                            Forms\Components\Toggle::make('in_stock')
                                ->required(),

                        ]),
                        Forms\Components\RichEditor::make('deskripsi')
                            ->columnSpanFull(),

                    ])->columns(3)
                        ->description('Detail Product'),
                ])->columnSpan(2),

                Section::make([
                    Forms\Components\Select::make('brand_id')
                        ->relationship('brand', 'nama', fn($query) => $query->where('is_active', true))
                        ->reactive()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            static::generateSku($get, $set);
                        })
                        ->createOptionForm([
                            TextInput::make('nama'),
                            FileUpload::make('image'),
                            Toggle::make('is_active')
                        ]),

                    Forms\Components\Select::make('category_id')
                        ->relationship('category', 'nama', fn($query) => $query->where('is_active', true))
                        ->reactive()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            static::generateSku($get, $set);
                        })
                        ->createOptionForm([
                            TextInput::make('nama'),
                            FileUpload::make('image'),
                            Toggle::make('is_active')
                        ]),

                    Forms\Components\Select::make('subcategory_id')
                        ->label('Sub Category')
                        ->options(function (Get $get) {
                            $CategoryId = $get('category_id');

                            if (!$CategoryId) return [];

                            return SubCategory::where('category_id', $CategoryId)
                                ->pluck('nama', 'id');
                        })->reactive()
                        ->disabled(fn(callable $get) => $get('category_id') === null)
                        ->dehydrated()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            static::generateSku($get, $set);
                        })
                        ->createOptionForm([
                            Select::make('category_id')
                                ->options(Category::pluck('nama', 'id')),
                            TextInput::make('nama'),
                            FileUpload::make('image'),
                            Toggle::make('is_active')
                            
                         ])->createOptionUsing(function (array $data): int {
                            return SubCategory::create($data)->getKey('id');
                        }),

                    Forms\Components\FileUpload::make('image')
                        ->image(),

                ])->columnSpan(1)
                    ->description('Association'),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga_dasar')
                    ->money('Rp.')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('harga')
                    ->money('Rp.')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stok')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('barcode')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('brand.nama')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('category.nama')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('subcategory.nama')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('in_stock')
                    ->boolean(),
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
}
