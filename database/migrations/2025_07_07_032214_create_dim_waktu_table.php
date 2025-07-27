<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dim_waktu', function (Blueprint $table) {
            $table->char('waktu_id', 8)->primary(); 
            $table->date('tanggal')->unique();
            $table->string('hari', 10);
            $table->string('bulan', 20);
            $table->integer('bulan_numerik'); 
            $table->integer('tahun');
            $table->integer('kuartal');
            $table->integer('semester');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dim_waktu');
    }
};