<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DimKapal;
use App\Models\laba_kapal;
use Illuminate\Support\Facades\Log; // Tambahkan ini

class DimKapalSeeder extends Seeder
{
    /**
     * Jalankan seed database.
     */
    public function run(): void
    {
        DimKapal::truncate(); // Hapus data lama

        // Pastikan entri 'Total' selalu ada
        DimKapal::create([
            'kapal_id' => 'KPL_TOT',
            'nama_kapal' => 'Total',
            'tipe_kapal' => 'Agregasi',
            'kapasitas' => null, // Biarkan null untuk total jika tidak ada makna kapasitas agregat
        ]);
        Log::info("DimKapalSeeder: Entri 'Total' dibuat.");


        // Data detail kapal
        // Kunci di sini DISESUAIKAN agar cocok dengan nama kapal dari laba_kapal (tanpa 'KMP ')
        $kapalDetails = [
            'BATU MANDI' => [ // Diubah dari 'KMP BATUMANDI'
                'penumpang' => 1000,
                'kendaraan' => 200,
                'tipe_kapal' => 'Ferry'
            ],
            'JATRA III' => [ // Diubah dari 'KMP JATRA III'
                'penumpang' => 525,
                'kendaraan' => 110,
                'tipe_kapal' => 'Ferry'
            ],
            'LEGUNDI' => [ // Diubah dari 'KMP LEGUNDI'
                'penumpang' => 812,
                'kendaraan' => 142,
                'tipe_kapal' => 'Ferry'
            ],
            'PORT LINK I' => [ // Diubah dari 'KMP PORT LINK I'
                'penumpang' => 1154,
                'kendaraan' => 371,
                'tipe_kapal' => 'Ferry'
            ],
            'PORT LINK III' => [ // Diubah dari 'KMP PORT LINK III'
                'penumpang' => 1500,
                'kendaraan' => 550,
                'tipe_kapal' => 'Ferry'
            ],
            'SEBUKU' => [ // Diubah dari 'KMP SEBUKU'
                'penumpang' => 812,
                'kendaraan' => 200,
                'tipe_kapal' => 'Ferry'
            ],
        ];

        // Ambil nama kapal unik dari tabel operasional laba_kapal
        $uniqueKapalNamesFromLabaKapal = laba_kapal::select('kapal')->distinct()->pluck('kapal')->toArray();
        Log::info("DimKapalSeeder: Nama kapal unik dari laba_kapal: " . implode(', ', $uniqueKapalNamesFromLabaKapal));


        $i = 1;
        foreach ($uniqueKapalNamesFromLabaKapal as $kapalName) {
            if ($kapalName === 'Total') {
                continue;
            }

            // Normalisasi nama kapal untuk pencarian di $kapalDetails
            // Cukup uppercase dan trim, karena kunci $kapalDetails sudah disesuaikan
            $normalizedKapalName = strtoupper(trim($kapalName));
            Log::info("DimKapalSeeder: Memproses kapal: '{$kapalName}' -> Normalized: '{$normalizedKapalName}'");


            if (array_key_exists($normalizedKapalName, $kapalDetails)) {
                $detail = $kapalDetails[$normalizedKapalName];
                $kapalId = 'KPL' . str_pad($i, 3, '0', STR_PAD_LEFT);

                DimKapal::create([
                    'kapal_id' => $kapalId,
                    'nama_kapal' => $kapalName, // Simpan nama asli dari laba_kapal
                    'tipe_kapal' => $detail['tipe_kapal'],
                    'kapasitas' => $detail['kendaraan'], // Mengisi kolom 'kapasitas' dengan kapasitas kendaraan
                ]);
                Log::info("DimKapalSeeder: Kapal '{$kapalName}' ditemukan detailnya. Kapasitas: {$detail['kendaraan']}");
                $i++;
            } else {
                // Masukkan kapal yang tidak memiliki detail spesifik
                $kapalId = 'KPL' . str_pad($i, 3, '0', STR_PAD_LEFT);
                DimKapal::create([
                    'kapal_id' => $kapalId,
                    'nama_kapal' => $kapalName,
                    'tipe_kapal' => 'Ferry',
                    'kapasitas' => null, // Default null jika tidak ada info
                ]);
                Log::warning("DimKapalSeeder: Kapal '{$kapalName}' TIDAK ditemukan detailnya. Kapasitas diset NULL.");
                $i++;
            }
        }
    }
}