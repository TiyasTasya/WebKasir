<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'Orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                    ->label('OrderID'),
                Tables\Columns\TextColumn::make('total_harga')
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
