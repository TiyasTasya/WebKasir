<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use BladeUI\Icons\Components\Icon;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Order', Order::count())
                ->description('Jumlah Total Order Yang Telah Dibuat.')
                ->descriptionIcon('heroicon-o-shopping-cart', IconPosition::Before)
                ->chart([1, 3, 5, 6, 8, 10, 12])
                ->color('success'),
            Stat::make('Total Produk', Product::count())
                ->description('Jumlah Total Produk Yang Tersedia.')
                ->descriptionIcon('heroicon-o-squares-2x2', IconPosition::Before)
                ->chart([1, 3, 5, 6, 8, 10, 12])
                ->color('success'),
            Stat::make('Total Customer', Customer::count())
                ->description('Jumlah Total Customer Yang Terdaftar.')
                ->descriptionIcon('heroicon-o-user-group', IconPosition::Before)
                ->chart([1, 3, 5, 6, 8, 10, 12])
                ->color('danger'),
            Stat::make('Total Pendapatan', 'Rp.' . number_format(Order::where('status', 'selesai')->sum('total_pembayaran')))
                ->description('Total Pembayaran Dari Semua Order Yang Telah Selesai.')
                ->descriptionIcon('heroicon-m-banknotes', IconPosition::Before)
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger'),
        ];
    }
}
