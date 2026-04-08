<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $fillable = [
        'kode_pembelian',
        'user_id',
        'supplier_id',
        'tanggal_pembelian',
        'tanggal_jatuh_tempo',
        'total_harga',
        'tarif_pajak',
        'jumlah_pajak',
        'diskon',
        'jumlah_diskon',
        'total_bayar',
        'status',
        'status_pembayaran',
        'metode_pembayaran'
    ];
}
