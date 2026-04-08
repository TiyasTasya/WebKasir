<?php

namespace App\Filament\Resources\Suppliers\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->striped()
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Supplier')
                    ->searchable(),
                TextColumn::make('no_telp')
                    ->label('No. Telepon')
                    ->searchable(),
                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->searchable(),
                ColumnGroup::make('Contact Person', [
                     TextColumn::make('cp_nama')
                        ->label('Nama Kontak')
                        ->searchable(),
                    TextColumn::make('cp_telephone')
                        ->label('Telepon Kontak')
                        ->searchable(),
                    TextColumn::make('cp_email')
                        ->label('Email Kontak')
                        ->searchable(),
                ]),
                IconColumn::make('is_active')
                    ->label('Aktif/Tidak Aktif')
                    ->boolean(),
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
                ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
