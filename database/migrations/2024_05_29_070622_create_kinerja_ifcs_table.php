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
        Schema::create('kinerja_ifcs', function (Blueprint $table) {
            $table->id();
            $table->string('golongan')->nullable();
            $table->bigInteger('januari')->nullable();
            $table->bigInteger('februari')->nullable();
            $table->bigInteger('maret')->nullable();
            $table->bigInteger('april')->nullable();
            $table->bigInteger('mei')->nullable();
            $table->bigInteger('juni')->nullable();
            $table->bigInteger('juli')->nullable();
            $table->bigInteger('agustus')->nullable();
            $table->bigInteger('september')->nullable();
            $table->bigInteger('oktober')->nullable();
            $table->bigInteger('november')->nullable();
            $table->bigInteger('desember')->nullable();
            $table->bigInteger('total')->nullable();
            $table->integer('tahun')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus semua data dari tabel
        DB::table('kinerja_ifcs')->delete();
        
        // Menghapus tabel
        Schema::dropIfExists('kinerja_ifcs');
    }
};
