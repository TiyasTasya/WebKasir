<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn; // Tambahkan import ini
use Filament\Widgets\TableWidget as BaseWidget;

class LastOrder extends BaseWidget
{
    // Opsional: Menentukan lebar widget agar lebih proporsional di dashboard
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()->latest()->limit(5) // Gunakan limit(5) untuk widget
            )
            ->columns([
                TextColumn::make('id')
                    ->label('OrderID'),

                TextColumn::make('customer.nama')
                    ->sortable(),

                TextColumn::make('total_pembayaran')
                    ->money('IDR'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) { // Perbaikan sintaks match
                        'baru' => 'info',
                        'diproses' => 'warning',
                        'cancel' => 'danger',
                        'selesai' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('tanggal_order')
                    ->date()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
