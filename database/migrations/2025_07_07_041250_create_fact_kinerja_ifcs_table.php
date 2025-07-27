<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fact_kinerja_ifcs', function (Blueprint $table) {
            $table->id(); 

            $table->char('waktu_id', 8);
            $table->char('pelabuhan_id', 5)->nullable();
            $table->char('layanan_id', 5)->nullable();
            $table->char('kapal_id', 8)->nullable(); 
            $table->char('golongan_id', 7)->nullable();

            $table->bigInteger('jumlah_produksi')->default(0); 
            $table->decimal('total_pendapatan', 18, 2)->default(0);
            $table->decimal('total_laba', 18, 2)->default(0);

            $table->timestamps();

            $table->foreign('waktu_id')->references('waktu_id')->on('dim_waktu')->onDelete('cascade');
            $table->foreign('pelabuhan_id')->references('pelabuhan_id')->on('dim_pelabuhan')->onDelete('cascade');
            $table->foreign('layanan_id')->references('layanan_id')->on('dim_layanan')->onDelete('cascade');
            $table->foreign('kapal_id')->references('kapal_id')->on('dim_kapal')->onDelete('cascade');
            $table->foreign('golongan_id')->references('golongan_id')->on('dim_golongan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fact_kinerja_ifcs');
    }
};