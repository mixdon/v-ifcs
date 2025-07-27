<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dim_kapal', function (Blueprint $table) {
            $table->char('kapal_id', 8)->primary(); // e.g., KPL001
            $table->string('nama_kapal')->unique();
            $table->string('tipe_kapal')->nullable();
            $table->integer('kapasitas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dim_kapal');
    }
};