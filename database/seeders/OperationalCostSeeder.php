<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OperationalCost;
use App\Models\DimGolongan;
use Illuminate\Support\Facades\Log;

class OperationalCostSeeder extends Seeder
{
    /**
     * Jalankan seed database.
     */
    public function run(): void
    {
        OperationalCost::truncate(); // Hapus semua data lama

        $costsDataByGolongan = [
            'IVA' => 500000,
            'IVB' => 450000,
            'VA' => 1000000,
            'VB' => 850000,
            'VIA' => 1600000,
            'VIB' => 1300000,
            'VII' => 1200000,
            'VIII' => 1250000,
            'IX' => 1500000,
        ];

        $relevantServiceTypes = ['ifcs', 'redeem', 'nonifcs', 'reguler'];

        $startYear = 2020;         // Tahun sekarang
        $endYear = $startYear + 9;       // 10 tahun ke depan (termasuk tahun ini)

        foreach ($costsDataByGolongan as $golonganName => $biayaPerUnit) {
            $golonganDim = DimGolongan::where('nama_golongan', $golonganName)->first();

            if ($golonganDim) {
                foreach ($relevantServiceTypes as $serviceType) {
                    for ($year = $startYear; $year <= $endYear; $year++) {
                        OperationalCost::updateOrCreate(
                            [
                                'golongan_id' => $golonganDim->golongan_id,
                                'jenis_layanan_terkait' => $serviceType,
                                'tahun' => $year,
                            ],
                            [
                                'biaya_per_unit' => $biayaPerUnit,
                            ]
                        );
                    }
                }
            } else {
                Log::warning("OperationalCostSeeder: Golongan '{$golonganName}' tidak ditemukan di DimGolongan. Melewatkan biaya ini.");
            }
        }
    }
}
