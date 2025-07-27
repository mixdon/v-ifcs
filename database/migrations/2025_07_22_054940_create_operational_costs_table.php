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
        Schema::create('operational_costs', function (Blueprint $table) {
            $table->id();
            $table->char('golongan_id', 7); // Foreign key ke dim_golongan
            // Mengubah ENUM agar hanya mencakup jenis layanan/transaksi yang relevan dari data sumber
            $table->enum('jenis_layanan_terkait', ['ifcs', 'redeem', 'nonifcs', 'reguler']); // Jenis layanan dari data sumber
            $table->decimal('biaya_per_unit', 18, 2); // Estimasi biaya per unit kendaraan
            $table->integer('tahun'); // Tahun berlaku biaya

            $table->timestamps();

            // Unique constraint untuk mencegah duplikasi biaya per golongan, jenis layanan, dan tahun
            $table->unique(['golongan_id', 'jenis_layanan_terkait', 'tahun'], 'unique_operational_cost');
            $table->foreign('golongan_id')->references('golongan_id')->on('dim_golongan')->onDelete('cascade');
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_costs');
    }
};
