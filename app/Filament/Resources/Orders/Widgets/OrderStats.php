<?php

namespace App\Filament\Resources\Orders\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pesanan Baru', Order::where('status', 'baru')->count())
                ->description('New Order Waiting To Be Processed')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info'),

            Stat::make('Pesanan Dalam Proses', Order::where('status', 'diproses')->count())
                ->description('Orders Currently Being Processed')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('warning'),

            Stat::make('Pesanan Telah Selesai', Order::where('status', 'selesai')->count())
                ->description('Orders Successfully Completed ')
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Total Pendapatan', 'Rp.'.number_format(Order::where('status', 'selesai')->sum('total_pembayaran'),0))
                ->description('Total Payment From Completed Orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger'),
        ];
    }
}
