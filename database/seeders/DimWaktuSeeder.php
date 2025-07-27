<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DimWaktu;
use Carbon\Carbon; 

class DimWaktuSeeder extends Seeder
{
    public function run(): void
    {
        DimWaktu::truncate(); 

        $startDate = Carbon::create(2020, 1, 1);
        $endDate = Carbon::create(date('Y') + 2, 12, 31);

        while ($startDate->lte($endDate)) {
            $waktuId = $startDate->format('Ymd');
            $tanggal = $startDate->toDateString();
            $hari = $startDate->translatedFormat('l'); 
            $bulan = $startDate->translatedFormat('F'); 
            $bulanNumerik = $startDate->month;
            $tahun = $startDate->year;
            $kuartal = $startDate->quarter;
            $semester = ($bulanNumerik <= 6) ? 1 : 2;

            DimWaktu::updateOrCreate(
                ['waktu_id' => $waktuId],
                [
                    'tanggal' => $tanggal,
                    'hari' => $hari,
                    'bulan' => $bulan,
                    'bulan_numerik' => $bulanNumerik,
                    'tahun' => $tahun,
                    'kuartal' => $kuartal,
                    'semester' => $semester,
                ]
            );

            $startDate->addDay();
        }
    }
}