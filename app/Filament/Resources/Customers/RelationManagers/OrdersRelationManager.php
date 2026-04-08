<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'Orders';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                    ->label('OrderID'),
                TextColumn::make('total_harga')
                    ->prefix('Rp.')
                    ->numeric()
                    ->sortable(),


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


                TextColumn::make('tanggal_order')
                    ->date()
                    ->sortable(),
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
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
