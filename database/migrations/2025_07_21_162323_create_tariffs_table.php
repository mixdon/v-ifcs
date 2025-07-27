<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->char('golongan_id', 7); // Foreign key ke dim_golongan
            $table->enum('jenis_layanan', ['reguler', 'express']); // 'Reguler' atau 'Eksekutif'
            $table->decimal('tarif_amount', 18, 2); // Jumlah tarif
            $table->integer('tahun')->nullable(); // Jika tarif bisa berubah per tahun
            $table->timestamps();

            $table->unique(['golongan_id', 'jenis_layanan', 'tahun']); // Tarif unik per golongan, jenis, tahun
            $table->foreign('golongan_id')->references('golongan_id')->on('dim_golongan')->onDelete('cascade');
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariffs');
    }
};
