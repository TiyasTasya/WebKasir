<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Supplier')
                    ->autofocus()
                    ->required(),
                TextInput::make('no_telp')
                    ->label('Telepon')
                    ->tel()
                    ->required(),
                TextInput::make('alamat')
                    ->label('Alamat')
                    ->required(),
                TextInput::make('cp_nama')
                    ->label('Nama Sales')
                    ->required(),
                TextInput::make('cp_telephone')
                    ->label('Nomer Telepon')
                    ->tel()
                    ->required(),
                TextInput::make('cp_email')
                    ->label('Email')
                    ->email()
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
