<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tariff;
use App\Models\DimGolongan; 

class TariffSeeder extends Seeder
{
    /**
     * Jalankan seed database.
     */
    public function run(): void
    {
        Tariff::truncate(); // Hapus data lama

        $tariffsData = [
            // Tarif Reguler (ASDP)
            ['golongan' => 'IVA', 'jenis_layanan' => 'reguler', 'tarif_amount' => 481800],
            ['golongan' => 'IVB', 'jenis_layanan' => 'reguler', 'tarif_amount' => 447800],
            ['golongan' => 'VA', 'jenis_layanan' => 'reguler', 'tarif_amount' => 963800],
            ['golongan' => 'VB', 'jenis_layanan' => 'reguler', 'tarif_amount' => 835300],
            ['golongan' => 'VIA', 'jenis_layanan' => 'reguler', 'tarif_amount' => 1594800],
            ['golongan' => 'VIB', 'jenis_layanan' => 'reguler', 'tarif_amount' => 1285200],
            ['golongan' => 'VII', 'jenis_layanan' => 'reguler', 'tarif_amount' => 1860400],
            ['golongan' => 'VIII', 'jenis_layanan' => 'reguler', 'tarif_amount' => 2452400],
            ['golongan' => 'IX', 'jenis_layanan' => 'reguler', 'tarif_amount' => 3755000],

            // Tarif Express (Eksekutif)
            ['golongan' => 'IVA', 'jenis_layanan' => 'express', 'tarif_amount' => 749128],
            ['golongan' => 'IVB', 'jenis_layanan' => 'express', 'tarif_amount' => 491800],
            ['golongan' => 'VA', 'jenis_layanan' => 'express', 'tarif_amount' => 1225928],
            ['golongan' => 'VB', 'jenis_layanan' => 'express', 'tarif_amount' => 904923],
            ['golongan' => 'VIA', 'jenis_layanan' => 'express', 'tarif_amount' => 2015985],
            ['golongan' => 'VIB', 'jenis_layanan' => 'express', 'tarif_amount' => 1366620],
            ['golongan' => 'VII', 'jenis_layanan' => 'express', 'tarif_amount' => 1975580],
            ['golongan' => 'VIII', 'jenis_layanan' => 'express', 'tarif_amount' => 2619845],
            ['golongan' => 'IX', 'jenis_layanan' => 'express', 'tarif_amount' => 3998000],
        ];

        // Asumsi tarif ini berlaku untuk semua tahun yang Anda proses, misalnya 2020-2025
        // Anda bisa menyesuaikan rentang tahun ini jika tarif berubah per tahun
        $startYear = 2020;
        $endYear = date('Y') + 2; // Hingga 2 tahun ke depan dari tahun saat ini

        foreach ($tariffsData as $data) {
            $golonganDim = DimGolongan::where('nama_golongan', $data['golongan'])->first();

            if ($golonganDim) {
                // Masukkan tarif untuk setiap tahun dalam rentang yang ditentukan
                for ($year = $startYear; $year <= $endYear; $year++) {
                    Tariff::create([
                        'golongan_id' => $golonganDim->golongan_id,
                        'jenis_layanan' => $data['jenis_layanan'],
                        'tarif_amount' => $data['tarif_amount'],
                        'tahun' => $year, // Menyimpan tarif per tahun
                    ]);
                }
            } else {
                Log::warning("TariffSeeder: Golongan '{$data['golongan']}' tidak ditemukan di DimGolongan. Melewatkan tarif ini.");
            }
        }
    }
}
