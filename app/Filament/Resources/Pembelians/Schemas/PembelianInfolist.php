<?php

namespace App\Filament\Resources\Pembelians\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PembelianInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('kode_pembelian'),
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('supplier_id')
                    ->numeric(),
                TextEntry::make('tanggal_pembelian')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('tanggal_jatuh_tempo')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('total_harga')
                    ->numeric(),
                TextEntry::make('tarif_pajak')
                    ->numeric(),
                TextEntry::make('jumlah_pajak')
                    ->numeric(),
                TextEntry::make('diskon')
                    ->numeric(),
                TextEntry::make('jumlah_diskon')
                    ->numeric(),
                TextEntry::make('total_bayar')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('status_pembayaran')
                    ->badge(),
                TextEntry::make('metode_pembayaran')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
