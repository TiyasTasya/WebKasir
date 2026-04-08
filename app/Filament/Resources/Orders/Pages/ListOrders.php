<?php

namespace App\Filament\Resources\Orders\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Widgets\OrderStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            OrderStats::class
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('Semua'),
            'Baru' => Tab::make()->query(fn($query) => $query->where('status', 'baru')),
            'Diproses' => Tab::make()->query(fn($query) => $query->where('status', 'diproses')),
            'Dicancel' => Tab::make()->query(fn($query) => $query->where('status', 'cancel')),
            'Selesai' => Tab::make()->query(fn($query) => $query->where('status', 'selesai'))

        ];
    }
}
