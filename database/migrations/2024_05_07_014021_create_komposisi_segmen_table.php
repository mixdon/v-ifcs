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
        Schema::create('komposisi_segmen', function (Blueprint $table) {
            $table->id();
            $table->string('golongan');
            $table->string('jenis');
            $table->bigInteger('ifcs_redeem')->default(0);
            $table->bigInteger('nonifcs')->default(0);
            $table->bigInteger('total')->default(0);
            $table->bigInteger('tahun')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus semua data dari tabel
        DB::table('komposisi_segmen')->delete();
        
        // Menghapus tabel
        Schema::dropIfExists('komposisi_segmen');
    }
};
