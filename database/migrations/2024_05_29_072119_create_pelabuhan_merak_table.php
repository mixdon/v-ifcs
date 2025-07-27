<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('pelabuhan_merak', function (Blueprint $table) {
            $table->id();
            $table->string('golongan');
            $table->enum('jenis', ['ifcs', 'redeem', 'nonifcs', 'reguler']);
            $table->bigInteger('januari')->default(0);
            $table->bigInteger('februari')->default(0);
            $table->bigInteger('maret')->default(0);
            $table->bigInteger('april')->default(0);
            $table->bigInteger('mei')->default(0);
            $table->bigInteger('juni')->default(0);
            $table->bigInteger('juli')->default(0);
            $table->bigInteger('agustus')->default(0);
            $table->bigInteger('september')->default(0);
            $table->bigInteger('oktober')->default(0);
            $table->bigInteger('november')->default(0);
            $table->bigInteger('desember')->default(0);
            $table->bigInteger('total')->default(0);
            $table->bigInteger('tahun')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelabuhan_merak');
    }
};
