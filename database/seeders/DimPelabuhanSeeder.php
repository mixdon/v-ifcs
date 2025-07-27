<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DimPelabuhan;

class DimPelabuhanSeeder extends Seeder
{
    public function run(): void
    {
        DimPelabuhan::truncate();
        DimPelabuhan::create(['pelabuhan_id' => 'PLB01', 'nama_pelabuhan' => 'Merak', 'lokasi_geografis' => 'Banten, Indonesia']);
        DimPelabuhan::create(['pelabuhan_id' => 'PLB02', 'nama_pelabuhan' => 'Bakauheni', 'lokasi_geografis' => 'Lampung, Indonesia']);
    }
}