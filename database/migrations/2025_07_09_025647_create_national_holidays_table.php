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
        Schema::create('national_holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique(); // Tanggal hari libur
            $table->string('name'); // Nama hari libur (misal: Tahun Baru)
            $table->boolean('is_collective_leave')->default(false); // Cuti bersama
            $table->timestamps();
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('national_holidays');
    }
};