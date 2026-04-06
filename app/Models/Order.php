<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    protected $fillable = [
        'customer_id',
        'tanggal_order',
        'total_harga',
        'diskon',
        'jumlah_diskon',
        'total_pembayaran',
        'status',
        'metode_pembayaran',
        'status_pembayaran',
        'tax_rate',
        'tax_amount'
    ];

    public function orderdetail(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    protected static function booted()
    {
        static::updated(function ($order) {

            $originalStatus = $order->getOriginal('status');

            if ($order->isDirty('status') && $order->status === 'selesai') {
                foreach ($order->orderdetail as $detail) {
                    $product = $detail->product;

                    if ($product) {
                        $product->decrement('stok', $detail->qty);
                    }
                }
            }

            if ($order->isDirty('status') && $originalStatus === 'selesai' && $order->status === 'cancel') {
                foreach ($order->orderdetail as $detail) {
                    $product = $detail->product;


                    if ($product) {
                        $product->increment('stok', $detail->qty);
                    }
                }
            }
        });
    }
}
