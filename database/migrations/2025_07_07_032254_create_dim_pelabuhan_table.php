<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dim_pelabuhan', function (Blueprint $table) {
            $table->char('pelabuhan_id', 5)->primary(); 
            $table->string('nama_pelabuhan')->unique();
            $table->string('lokasi_geografis')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dim_pelabuhan');
    }
};