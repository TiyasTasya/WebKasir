<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Uom extends Model
{
    protected $fillable = [
        'code',
        'nama',
        'base_unit_id',
        'simbol',
        'deskripsi',
        'is_active',
    ];

    public function base_unit():BelongsTo
    {
        return $this->belongsTo(BaseUnit::class);
    }
}
