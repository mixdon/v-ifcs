<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->bigInteger('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('role')->default('tamu');
            $table->string('about_me')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert([
            'id' => 1, 
            'name' => 'komersial',
            'email' => 'komersial@gmail.com',
            'password' => Hash::make('komersial'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Periksa apakah tabel users ada sebelum mencoba menghapusnya
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Hapus kolom 'role' dari tabel 'users'
                $table->dropColumn('role');
            });
            
            // Hapus tabel 'users' jika masih ada
            Schema::dropIfExists('users');
        }
    }
    
}
