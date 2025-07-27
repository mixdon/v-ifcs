<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DimLayanan; 

class DimLayananSeeder extends Seeder
{

    public function run(): void
    {
        
        DimLayanan::truncate();

        DimLayanan::create([
            'layanan_id' => 'LYN01',
            'jenis_layanan' => 'ifcs',
            'deskripsi' => 'Indonesia Ferry Corporate Scheme'
        ]);

        DimLayanan::create([
            'layanan_id' => 'LYN02',
            'jenis_layanan' => 'redeem',
            'deskripsi' => 'Layanan Redeem'
        ]);

        DimLayanan::create([
            'layanan_id' => 'LYN03',
            'jenis_layanan' => 'nonifcs',
            'deskripsi' => 'Layanan Non Indonesia Ferry Corporate Scheme'
        ]);

        DimLayanan::create([
            'layanan_id' => 'LYN04',
            'jenis_layanan' => 'reguler',
            'deskripsi' => 'Layanan Reguler'
        ]);

        DimLayanan::create([
            'layanan_id' => 'LYN05',
            'jenis_layanan' => 'pendapatan',
            'deskripsi' => 'Data Pendapatan Kapal'
        ]);

        DimLayanan::create([
            'layanan_id' => 'LYN06',
            'jenis_layanan' => 'labarugi',
            'deskripsi' => 'Data Laba Rugi Operasional Kapal'
        ]);
        
    }
}
