<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pembelian')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal_pembelian')->nullable();
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->decimal('total_harga', 15, 2);
            $table->decimal('tarif_pajak', 15, 2);
            $table->decimal('jumlah_pajak', 15, 2);
            $table->decimal('diskon', 15, 2);
            $table->decimal('jumlah_diskon', 15, 2);
            $table->decimal('total_bayar', 15, 2);
            $table->enum('status', ['pending', 'bayar', 'diabatalkan'])->default('bayar');
            $table->enum('status_pembayaran', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->enum('metode_pembayaran', ['tunai', 'kredit', 'transfer'])->default('tunai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
