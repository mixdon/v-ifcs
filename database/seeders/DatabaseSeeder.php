<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Nonaktifkan pemeriksaan foreign key sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Kosongkan tabel 
        if (Schema::hasTable('fact_kinerja_ifcs')) {
            DB::table('fact_kinerja_ifcs')->truncate();
        }
        // Baris untuk forecasting_results dihapus karena belum dibuat
        // if (Schema::hasTable('forecasting_results')) {
        //     DB::table('forecasting_results')->truncate();
        // }
        if (Schema::hasTable('national_holidays')) {
            DB::table('national_holidays')->truncate(); 
        }


        // Panggil semua seeder 
        $this->call([
            DimWaktuSeeder::class,
            DimPelabuhanSeeder::class,
            DimLayananSeeder::class,
            DimKapalSeeder::class,
            DimGolonganSeeder::class,
            NationalHolidaySeeder::class,
            TariffSeeder::class,
            OperationalCostSeeder::class,
        ]);

        // Aktifkan kembali pemeriksaan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
