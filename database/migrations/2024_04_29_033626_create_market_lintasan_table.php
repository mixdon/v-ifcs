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
        Schema::create('market_lintasan', function (Blueprint $table) {
            $table->id();
            $table->string('golongan');
            $table->string('jenis');
            $table->bigInteger('merak')->default(0);
            $table->bigInteger('bakauheni')->default(0);
            $table->bigInteger('gabungan')->default(0);
            $table->bigInteger('tahun')->default(0);
            $table->timestamps();
        });
    }
    
     public function down(): void
    {
        // Menghapus semua data dari tabel
        DB::table('market_lintasan')->delete();
        
        // Menghapus tabel
        Schema::dropIfExists('market_lintasan');
    }
};
