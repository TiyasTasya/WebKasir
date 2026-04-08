<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BaseUnit extends Model
{
    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    public function uom():HasMany
    {
        return $this->hasMany(Uom::class);
    }
}
