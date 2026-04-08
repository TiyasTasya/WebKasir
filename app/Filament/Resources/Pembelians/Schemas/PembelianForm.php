<?php

namespace App\Filament\Resources\Pembelians\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class PembelianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FusedGroup::make([
                    TextInput::make('kode_pembelian')
                        ->hiddenLabel()
                        ->disabled()
                        ->dehydrated()
                        ->columnSpanFull()
                        ->prefix('Kode Pembelian')
                        ->required(),
                    TextInput::make('user_id')
                        ->required()
                        ->hiddenLabel()
                        ->disabled()
                        ->dehydrated()
                        ->columnSpanFull()
                        ->prefix('Dibuat Oleh'),
                ])->columnSpanFull(),

                Group::make([
                    Fieldset::make('Rincian Pembelian')
                        ->schema([
                            TextInput::make('suplier_id ')
                                ->label('Nama Supplier'),
                            DatePicker::make('tanggal_pembelian')
                                ->label('Tanggal Pembelian'),
                            DatePicker::make('tanggal_jatuh_tempo')
                                ->label('Tanggal Jatuh Tempo'),
                        ])->columns(3),
                ])->columnSpan(2),

                Fieldset::make('Rincian Pembayaran')
                    ->schema([
                        Select::make('status')
                            ->options(['pending' => 'Pending', 'bayar' => 'Bayar', 'diabatalkan' => 'Diabatalkan'])
                            ->default('bayar')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('total_harga')
                            ->default(0)
                            ->prefix('Rp.')
                            ->required()
                            ->numeric()
                            ->columnSpanFull(),

                        FusedGroup::make([
                            TextInput::make('tarif_pajak')
                                ->default(0)
                                ->suffix('%')
                                ->required()
                                ->numeric(),
                            TextInput::make('jumlah_pajak')
                                ->default(0)
                                ->prefix('Rp.')
                                ->required()
                                ->numeric(),
                        ])->columns(2)
                            ->label('Pajak')
                             ->columnSpanFull(),

                        FusedGroup::make([
                            TextInput::make('diskon')
                                ->default(0)
                                ->suffix('%')
                                ->required()
                                ->numeric(),
                            TextInput::make('jumlah_diskon')
                                ->default(0)
                                ->prefix('Rp.')
                                ->required()
                                ->numeric(),
                        ])->columns(2)
                            ->label('Diskon')
                            ->columnSpanFull(),

                        TextInput::make('total_bayar')
                            ->default(0)
                            ->prefix('Rp.')
                            ->required()
                            ->numeric(),

                        Select::make('status_pembayaran')
                            ->options(['lunas' => 'Lunas', 'belum_lunas' => 'Belum lunas'])
                            ->default('belum_lunas')
                            ->required(),
                        Select::make('metode_pembayaran')
                            ->options(['tunai' => 'Tunai', 'kredit' => 'Kredit', 'transfer' => 'Transfer'])
                            ->default('tunai')
                            ->required(),
                    ])->columnSpan(1),
            ])->columns(3);
    }
}
