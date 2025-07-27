<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DimGolongan;
use App\Models\pelabuhan_merak;
use App\Models\kinerja_ifcs;
use Illuminate\Support\Facades\Log; 

class DimGolonganSeeder extends Seeder
{
    /**
     * Jalankan seed database.
     */
    public function run(): void
    {
        DimGolongan::truncate(); // Hapus data lama

        // Pastikan entri 'Total' selalu ada
        DimGolongan::create([
            'golongan_id' => 'GLN_TOT', // ID khusus untuk 'Total'
            'nama_golongan' => 'Total',
            'deskripsi' => 'Total Keseluruhan Golongan'
        ]);
        Log::info("DimGolonganSeeder: Entri 'Total' dibuat.");

        // Daftar golongan standar yang Anda harapkan (untuk memastikan semua ada)
        $standardGolonganNames = [
            'IVA', 'IVB', 'VA', 'VB', 'VIA', 'VIB', 'VII', 'VIII', 'IX'
        ];

        $extractedGolonganNames = [];

        // Ambil golongan unik dari pelabuhan_merak
        $golonganMerak = pelabuhan_merak::select('golongan')->distinct()->pluck('golongan')->toArray();
        $extractedGolonganNames = array_merge($extractedGolonganNames, $golonganMerak);
        Log::info("DimGolonganSeeder: Golongan dari pelabuhan_merak: " . implode(', ', $golonganMerak));

        // Ambil golongan unik dari kinerja_ifcs
        $golonganKinerja = kinerja_ifcs::select('golongan')->distinct()->pluck('golongan')->toArray();
        $extractedGolonganNames = array_merge($extractedGolonganNames, $golonganKinerja);
        Log::info("DimGolonganSeeder: Golongan dari kinerja_ifcs: " . implode(', ', $golonganKinerja));

        // Gabungkan golongan standar dengan yang diekstrak, filter duplikasi, null, dan 'Total'
        $allUniqueGolonganNames = array_unique(array_merge($standardGolonganNames, $extractedGolonganNames));
        $allUniqueGolonganNames = array_filter($allUniqueGolonganNames, function($value) {
            return !is_null($value) && trim($value) !== '' && strtoupper(trim($value)) !== 'TOTAL';
        });

        // --- PERBAIKAN DI SINI: Urutkan secara kustom ---
        usort($allUniqueGolonganNames, function($a, $b) {
            // Logika pengurutan kustom untuk golongan (misal: IVA, IVB, VA, VB, ...)
            // Ini akan menguraikan string seperti 'IVA' menjadi angka untuk perbandingan
            $order = ['IVA', 'IVB', 'VA', 'VB', 'VIA', 'VIB', 'VII', 'VIII', 'IX'];
            $posA = array_search($a, $order);
            $posB = array_search($b, $order);

            // Jika salah satu tidak ditemukan di $order (misalnya ada golongan baru yang tidak terduga),
            // biarkan sort default PHP menanganinya atau tambahkan logika penanganan error.
            if ($posA === false && $posB === false) return 0;
            if ($posA === false) return 1; // B lebih dulu
            if ($posB === false) return -1; // A lebih dulu

            return $posA <=> $posB; // Operator perbandingan spaceship (PHP 7+)
        });
        // --- AKHIR PERBAIKAN ---

        Log::info("DimGolonganSeeder: Golongan unik yang akan di-seed (setelah urut): " . implode(', ', $allUniqueGolonganNames));

        $i = 1;
        foreach ($allUniqueGolonganNames as $golonganName) {
            $golonganId = 'GLN' . str_pad($i, 2, '0', STR_PAD_LEFT);
            DimGolongan::create([
                'golongan_id' => $golonganId,
                'nama_golongan' => $golonganName,
                'deskripsi' => 'Golongan ' . $golonganName
            ]);
            Log::info("DimGolonganSeeder: Golongan '{$golonganName}' (ID: {$golonganId}) dibuat.");
            $i++;
        }
    }
}
