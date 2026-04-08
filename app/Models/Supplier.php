<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'nama',
        'no_telp',
        'alamat',
        'cp_nama',
        'cp_telephone',
        'cp_email',
        'is_active',
    ];
}
