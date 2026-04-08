<?php

namespace App\Filament\Resources\Pembelians\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PembeliansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_pembelian')
                    ->searchable(),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('supplier_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tanggal_pembelian')
                    ->date()
                    ->sortable(),
                TextColumn::make('tanggal_jatuh_tempo')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_harga')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tarif_pajak')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jumlah_pajak')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('diskon')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jumlah_diskon')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_bayar')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('status_pembayaran')
                    ->badge(),
                TextColumn::make('metode_pembayaran')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
