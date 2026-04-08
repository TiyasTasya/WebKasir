<?php

namespace App\Filament\Widgets;

use App\Models\OrderDetail;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model; // Tambahkan ini

class BestSelling extends BaseWidget
{

    protected static ? int $sort =4;
        protected static ?string $heading = 'Produk Terlaris';
    public function getTableRecordKey(Model|array $record): string
    {
        return (string) $record->product_id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderDetail::query()
                    ->select('product_id', DB::raw('SUM(qty) as total_sold'))
                    ->with('product')
                    ->groupBy('product_id')
                    ->orderByDesc('total_sold')
                    ->limit(5)
            )
            ->columns([
                ImageColumn::make('product.image')
                    ->label('Gambar')
                    ->circular(),

                TextColumn::make('product.nama')
                    ->label('Nama Produk'),

                TextColumn::make('total_sold')
                    ->label('Total Terjual')
                    ->badge()
                    ->color('success'),
            ])
            ->paginated(false);
    }
}