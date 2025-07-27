<?php

namespace App\Services;

use App\Models\pelabuhan_merak;
use App\Models\pelabuhan_bakauheni;
use App\Models\kinerja_ifcs;
use App\Models\laba_kapal;
use App\Models\FactKinerjaIFCS;
use App\Models\DimWaktu;
use App\Models\DimPelabuhan;
use App\Models\DimLayanan;
use App\Models\DimGolongan;
use App\Models\DimKapal;
use App\Models\Tariff;
use App\Models\NationalHoliday;
use App\Models\OperationalCost;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataWarehouseService
{
    // Bobot distribusi
    protected $weights = [
        'weekday' => 1,
        'weekend' => 1.5,
        'holiday' => 2,     
    ];

    public function runEtlForYear(int $targetYear)
    {
        DB::beginTransaction();
        try {
            Log::info("Memulai proses ETL untuk tahun target: {$targetYear}");

            // Hapus data fakta yang sudah ada untuk tahun ini
            FactKinerjaIFCS::whereHas('waktu', function ($query) use ($targetYear) {
                $query->where('tahun', $targetYear);
            })->delete();
            Log::info("Data fact_kinerja_ifcs untuk tahun {$targetYear} telah dikosongkan.");


            // Ambil semua hari libur untuk tahun target (tahun yang sedang diproses)
            $holidays = NationalHoliday::whereYear('date', $targetYear)->pluck('date')->map(fn($date) => $date->toDateString())->toArray();
            Log::info("Ditemukan " . count($holidays) . " hari libur untuk tahun {$targetYear}.");


            // Iterasi untuk setiap bulan dalam tahun target
            for ($monthNum = 1; $monthNum <= 12; $monthNum++) {
                $currentMonth = Carbon::create($targetYear, $monthNum, 1);
                $daysInMonth = $currentMonth->daysInMonth;

                Log::info("Memproses bulan {$monthNum} untuk tahun target {$targetYear}");

                // Dapatkan semua tanggal di bulan target
                $datesInTargetMonth = collect([]);
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::create($targetYear, $monthNum, $day);
                    $datesInTargetMonth->push($date);
                }

                // --- Proses Data Produksi Pelabuhan (Merak & Bakauheni) ---
                $this->distributePelabuhanData($targetYear, $monthNum, $datesInTargetMonth, $holidays, $daysInMonth);

                // --- Proses Data Kinerja IFCS (Pendapatan) ---
                $this->distributeKinerjaIFCSData($targetYear, $monthNum, $datesInTargetMonth, $holidays, $daysInMonth);

                // --- Proses Data Laba Kapal ---
                $this->distributeLabaKapalData($targetYear, $monthNum, $datesInTargetMonth, $holidays, $daysInMonth);
            }

            DB::commit();
            Log::info("Proses ETL untuk tahun target {$targetYear} berhasil diselesaikan.");
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Proses ETL gagal untuk tahun target {$targetYear}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Menentukan tipe hari dan bobotnya.
     */
    protected function getDayTypeAndWeight(Carbon $date, array $holidays): array
    {
        $dateString = $date->toDateString();
        $isHoliday = in_array($dateString, $holidays);
        $isWeekend = $date->isWeekend(); // Sabtu atau Minggu

        if ($isHoliday) {
            return ['type' => 'holiday', 'weight' => $this->weights['holiday']];
        } elseif ($isWeekend) {
            return ['type' => 'weekend', 'weight' => $this->weights['weekend']];
        } else {
            return ['type' => 'weekday', 'weight' => $this->weights['weekday']];
        }
    }

    /**
     * Helper untuk mendistribusikan total bulanan ke nilai harian berdasarkan bobot.
     */
    protected function distributeMonthlyTotal(float $monthlyTotal, \Illuminate\Support\Collection $datesInTargetMonth, array $holidays): array
    {
        $dailyValues = [];
        $totalWeight = 0;

        // Hitung total bobot untuk bulan target
        foreach ($datesInTargetMonth as $date) {
            $dayInfo = $this->getDayTypeAndWeight($date, $holidays);
            $totalWeight += $dayInfo['weight'];
        }

        if ($totalWeight === 0) {
            Log::warning("Total bobot nol untuk bulan. Tidak dapat mendistribusikan total {$monthlyTotal}.");
            return [];
        }

        $valuePerWeightUnit = $monthlyTotal / $totalWeight;

        // Distribusikan nilai ke setiap hari
        foreach ($datesInTargetMonth as $date) {
            $dayInfo = $this->getDayTypeAndWeight($date, $holidays);
            $dailyValue = $dayInfo['weight'] * $valuePerWeightUnit;
            $dailyValues[$date->toDateString()] = $dailyValue;
        }
        return $dailyValues;
    }

    /**
     * Proses data pelabuhan (merak/bakauheni) dan distribusikan ke harian per golongan.
     */
    protected function distributePelabuhanData(int $currentYear, int $monthNum, \Illuminate\Support\Collection $datesInTargetMonth, array $holidays, int $daysInMonth)
    {
        $monthName = strtolower(Carbon::create(null, $monthNum, 1)->translatedFormat('F'));

        // Ambil SEMUA baris produksi bulanan untuk Merak (bukan hanya 'Total')
        $merakRecords = pelabuhan_merak::where('tahun', $currentYear)->get();
        // Ambil SEMUA baris produksi bulanan untuk Bakauheni (bukan hanya 'Total')
        $bakauheniRecords = pelabuhan_bakauheni::where('tahun', $currentYear)->get();

        $pelabuhanDimMerak = DimPelabuhan::where('nama_pelabuhan', 'Merak')->first();
        $pelabuhanDimBakauheni = DimPelabuhan::where('nama_pelabuhan', 'Bakauheni')->first();

        if (!$pelabuhanDimMerak) { Log::error("Dimensi Pelabuhan 'Merak' tidak ditemukan."); return; }
        if (!$pelabuhanDimBakauheni) { Log::error("Dimensi Pelabuhan 'Bakauheni' tidak ditemukan."); return; }

        // Proses Merak
        foreach ($merakRecords as $record) {
            // Lewati baris 'Total' di tabel operasional jika tidak ingin memprosesnya dua kali
            if (strtoupper(trim($record->golongan)) === 'TOTAL') {
                Log::info("Skipping 'Total' row for Merak Production in {$monthName} {$currentYear}.");
                continue;
            }

            $totalMonthlyProduction = $record->$monthName ?? 0;
            Log::info("Merak Production for Golongan '{$record->golongan}' and Jenis '{$record->jenis}' for {$monthName} {$currentYear}: {$totalMonthlyProduction}");

            if ($totalMonthlyProduction > 0) {
                $dailyDistributedValues = $this->distributeMonthlyTotal($totalMonthlyProduction, $datesInTargetMonth, $holidays);

                $golonganDim = DimGolongan::where('nama_golongan', $record->golongan)->first();
                $layananDim = DimLayanan::where('jenis_layanan', $record->jenis)->first();

                if (!$golonganDim) { Log::error("Dimensi Golongan '{$record->golongan}' tidak ditemukan."); continue; }
                if (!$layananDim) { Log::error("Dimensi Layanan '{$record->jenis}' tidak ditemukan."); continue; }

                // Tentukan jenis layanan untuk mencari tarif berdasarkan klarifikasi Anda
                $tariffLayananJenis = null;
                if (in_array($record->jenis, ['ifcs', 'redeem'])) {
                    $tariffLayananJenis = 'express';
                } elseif (in_array($record->jenis, ['nonifcs', 'reguler'])) {
                    $tariffLayananJenis = 'reguler';
                }

                $foundTariffAmount = 0;
                if ($tariffLayananJenis) {
                    $tariffRecord = Tariff::where('golongan_id', $golonganDim->golongan_id)
                                        ->where('jenis_layanan', $tariffLayananJenis)
                                        ->where('tahun', $currentYear)
                                        ->first();
                    if ($tariffRecord) {
                        $foundTariffAmount = $tariffRecord->tarif_amount;
                        Log::info("Tariff found for Merak {$record->golongan} ({$tariffLayananJenis}) for {$currentYear}: {$foundTariffAmount}");
                    } else {
                        Log::warning("Tarif tidak ditemukan untuk Golongan '{$record->golongan}', Jenis Layanan '{$tariffLayananJenis}', Tahun {$currentYear}. Pendapatan akan 0.");
                    }
                } else {
                    Log::warning("Jenis layanan '{$record->jenis}' dari data produksi tidak cocok dengan jenis tarif yang dikenal. Pendapatan akan 0.");
                }

                // Ambil biaya operasional per unit untuk golongan ini
                $operationalCostPerUnit = 0;
                $opCostServiceType = $record->jenis;
                $opCostRecord = OperationalCost::where('golongan_id', $golonganDim->golongan_id)
                                            ->where('jenis_layanan_terkait', $opCostServiceType)
                                            ->where('tahun', $currentYear)
                                            ->first();
                if ($opCostRecord) {
                    $operationalCostPerUnit = $opCostRecord->biaya_per_unit;
                    Log::info("Operational Cost per Unit found for Merak {$record->golongan} ({$opCostServiceType}) for {$currentYear}: {$operationalCostPerUnit}");
                } else {
                    Log::warning("Biaya operasional per unit tidak ditemukan untuk Golongan '{$record->golongan}', Jenis Layanan '{$opCostServiceType}', Tahun {$currentYear}. Biaya akan 0.");
                }


                foreach ($dailyDistributedValues as $dateString => $dailyValue) {
                    $waktuDim = DimWaktu::where('tanggal', $dateString)->first();
                    if (!$waktuDim) { Log::error("Dimensi Waktu untuk {$dateString} tidak ditemukan."); continue; }

                    $roundedDailyProduction = round($dailyValue);

                    $dailyCalculatedRevenue = 0; // Inisialisasi
                    $dailyCalculatedCost = 0;    // Inisialisasi
                    $dailyCalculatedProfit = 0;  // Inisialisasi

                    // Jika jumlah_produksi harian adalah 0, maka pendapatan dan laba juga 0
                    if ($roundedDailyProduction === 0) {
                        Log::info("Produksi harian 0 untuk {$dateString}. Pendapatan/Laba diset 0.");
                    } else {
                        $dailyCalculatedRevenue = $roundedDailyProduction * $foundTariffAmount;
                        $dailyCalculatedCost = $roundedDailyProduction * $operationalCostPerUnit;
                        $dailyCalculatedProfit = $dailyCalculatedRevenue - $dailyCalculatedCost;
                    }

                    // Log warning jika ada inkonsistensi
                    if ($roundedDailyProduction === 0 && ($dailyCalculatedRevenue != 0 || $dailyCalculatedProfit != 0)) {
                        Log::warning("Inconsistent data: produksi 0 tapi revenue/laba tidak nol untuk tanggal {$dateString} (Merak).");
                    }

                    // --- FILTER UTAMA: Hanya simpan jika pelabuhan_id TIDAK NULL ---
                    // Untuk distributePelabuhanData, pelabuhan_id selalu ada, jadi ini akan selalu true
                    if ($pelabuhanDimMerak->pelabuhan_id !== null) { // Kondisi ini akan selalu true untuk data pelabuhan
                        FactKinerjaIFCS::create([
                            'waktu_id' => $waktuDim->waktu_id,
                            'pelabuhan_id' => $pelabuhanDimMerak->pelabuhan_id,
                            'layanan_id' => $layananDim->layanan_id,
                            'golongan_id' => $golonganDim->golongan_id,
                            'jumlah_produksi' => $roundedDailyProduction,
                            'total_pendapatan' => $dailyCalculatedRevenue,
                            'total_laba' => $dailyCalculatedProfit,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        Log::info("Melewatkan entri karena pelabuhan_id NULL untuk Merak pada tanggal {$dateString}.");
                    }
                }
            } else {
                Log::info("Merak Production for Golongan '{$record->golongan}' and Jenis '{$record->jenis}' for {$monthName} {$currentYear} is zero. Skipping distribution.");
            }
        }

        // Proses Bakauheni (logika yang sama diterapkan)
        foreach ($bakauheniRecords as $record) {
            if (strtoupper(trim($record->golongan)) === 'TOTAL') {
                Log::info("Skipping 'Total' row for Bakauheni Production in {$monthName} {$currentYear}.");
                continue;
            }

            $totalMonthlyProduction = $record->$monthName ?? 0;
            Log::info("Bakauheni Production for Golongan '{$record->golongan}' and Jenis '{$record->jenis}' for {$monthName} {$currentYear}: {$totalMonthlyProduction}");

            if ($totalMonthlyProduction > 0) {
                $dailyDistributedValues = $this->distributeMonthlyTotal($totalMonthlyProduction, $datesInTargetMonth, $holidays);

                $golonganDim = DimGolongan::where('nama_golongan', $record->golongan)->first();
                $layananDim = DimLayanan::where('jenis_layanan', $record->jenis)->first();

                if (!$golonganDim) { Log::error("Dimensi Golongan '{$record->golongan}' tidak ditemukan."); continue; }
                if (!$layananDim) { Log::error("Dimensi Layanan '{$record->jenis}' tidak ditemukan."); continue; }

                // Tentukan jenis layanan untuk mencari tarif
                $tariffLayananJenis = null;
                if (in_array($record->jenis, ['ifcs', 'redeem'])) {
                    $tariffLayananJenis = 'express';
                } elseif (in_array($record->jenis, ['nonifcs', 'reguler'])) {
                    $tariffLayananJenis = 'reguler';
                }

                $foundTariffAmount = 0;
                if ($tariffLayananJenis) {
                    $tariffRecord = Tariff::where('golongan_id', $golonganDim->golongan_id)
                                        ->where('jenis_layanan', $tariffLayananJenis)
                                        ->where('tahun', $currentYear)
                                        ->first();
                    if ($tariffRecord) {
                        $foundTariffAmount = $tariffRecord->tarif_amount;
                        Log::info("Tariff found for Bakauheni {$record->golongan} ({$tariffLayananJenis}) for {$currentYear}: {$foundTariffAmount}");
                    } else {
                        Log::warning("Tarif tidak ditemukan untuk Golongan '{$record->golongan}', Jenis Layanan '{$tariffLayananJenis}', Tahun {$currentYear}. Pendapatan akan 0.");
                    }
                } else {
                    Log::warning("Jenis layanan '{$record->jenis}' dari data produksi tidak cocok dengan jenis tarif yang dikenal. Pendapatan akan 0.");
                }

                // Ambil biaya operasional per unit untuk golongan ini
                $operationalCostPerUnit = 0;
                $opCostServiceType = $record->jenis;
                $opCostRecord = OperationalCost::where('golongan_id', $golonganDim->golongan_id)
                                            ->where('jenis_layanan_terkait', $opCostServiceType)
                                            ->where('tahun', $currentYear)
                                            ->first();
                if ($opCostRecord) {
                    $operationalCostPerUnit = $opCostRecord->biaya_per_unit;
                    Log::info("Operational Cost per Unit found for Bakauheni {$record->golongan} ({$opCostServiceType}) for {$currentYear}: {$operationalCostPerUnit}");
                } else {
                    Log::warning("Biaya operasional per unit tidak ditemukan untuk Golongan '{$record->golongan}', Jenis Layanan '{$opCostServiceType}', Tahun {$currentYear}. Biaya akan 0.");
                }

                foreach ($dailyDistributedValues as $dateString => $dailyValue) {
                    $waktuDim = DimWaktu::where('tanggal', $dateString)->first();
                    if (!$waktuDim) { Log::error("Dimensi Waktu untuk {$dateString} tidak ditemukan."); continue; }

                    $roundedDailyProduction = round($dailyValue);

                    $dailyCalculatedRevenue = 0; // Inisialisasi
                    $dailyCalculatedCost = 0;    // Inisialisasi
                    $dailyCalculatedProfit = 0;  // Inisialisasi

                    // Jika jumlah_produksi harian adalah 0, maka pendapatan dan laba juga 0
                    if ($roundedDailyProduction === 0) {
                        Log::info("Produksi harian 0 untuk {$dateString}. Pendapatan/Laba diset 0.");
                    } else {
                        $dailyCalculatedRevenue = $roundedDailyProduction * $foundTariffAmount;
                        $dailyCalculatedCost = $roundedDailyProduction * $operationalCostPerUnit;
                        $dailyCalculatedProfit = $dailyCalculatedRevenue - $dailyCalculatedCost;
                    }

                    // Log warning jika ada inkonsistensi
                    if ($roundedDailyProduction === 0 && ($dailyCalculatedRevenue != 0 || $dailyCalculatedProfit != 0)) {
                        Log::warning("Inconsistent data: produksi 0 tapi revenue/laba tidak nol untuk tanggal {$dateString} (Bakauheni).");
                    }

                    // --- FILTER UTAMA: Hanya simpan jika pelabuhan_id TIDAK NULL ---
                    if ($pelabuhanDimBakauheni->pelabuhan_id !== null) { // Kondisi ini akan selalu true untuk data pelabuhan
                        FactKinerjaIFCS::create([
                            'waktu_id' => $waktuDim->waktu_id,
                            'pelabuhan_id' => $pelabuhanDimBakauheni->pelabuhan_id,
                            'layanan_id' => $layananDim->layanan_id,
                            'golongan_id' => $golonganDim->golongan_id,
                            'jumlah_produksi' => $roundedDailyProduction,
                            'total_pendapatan' => $dailyCalculatedRevenue,
                            'total_laba' => $dailyCalculatedProfit,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        Log::info("Melewatkan entri karena pelabuhan_id NULL untuk Bakauheni pada tanggal {$dateString}.");
                    }
                }
            } else {
                Log::info("Bakauheni Production for Golongan '{$record->golongan}' and Jenis '{$record->jenis}' for {$monthName} {$currentYear} is zero. Skipping distribution.");
            }
        }
    }

    /**
     * Proses data kinerja IFCS (pendapatan) dan distribusikan ke harian per golongan.
     */
    protected function distributeKinerjaIFCSData(int $currentYear, int $monthNum, \Illuminate\Support\Collection $datesInTargetMonth, array $holidays, int $daysInMonth)
    {
        $monthName = strtolower(Carbon::create(null, $monthNum, 1)->translatedFormat('F'));

        // Ambil SEMUA baris pendapatan IFCS (bukan hanya 'Total')
        $kinerjaRecords = kinerja_ifcs::where('tahun', $currentYear)->get();
        $layananIfcsDim = DimLayanan::where('jenis_layanan', 'ifcs')->first();

        if (!$layananIfcsDim) { Log::error("Dimensi Layanan 'ifcs' tidak ditemukan."); return; }

        foreach ($kinerjaRecords as $record) {
            if (strtoupper(trim($record->golongan)) === 'TOTAL') {
                Log::info("Skipping 'Total' row for Kinerja IFCS Revenue in {$monthName} {$currentYear}.");
                continue;
            }

            $totalMonthlyRevenue = $record->$monthName ?? 0;
            Log::info("Kinerja IFCS Revenue for Golongan '{$record->golongan}' for {$monthName} {$currentYear}: {$totalMonthlyRevenue}");

            if ($totalMonthlyRevenue > 0) {
                $dailyDistributedValues = $this->distributeMonthlyTotal($totalMonthlyRevenue, $datesInTargetMonth, $holidays);

                $golonganDim = DimGolongan::where('nama_golongan', $record->golongan)->first();
                if (!$golonganDim) { Log::error("Dimensi Golongan '{$record->golongan}' tidak ditemukan untuk Kinerja IFCS."); continue; }

                // Ambil biaya operasional per unit untuk golongan ini
                $operationalCostPerUnit = 0;
                $opCostServiceType = 'ifcs'; // Kinerja IFCS terkait dengan layanan 'ifcs'
                $opCostRecord = OperationalCost::where('golongan_id', $golonganDim->golongan_id)
                                            ->where('jenis_layanan_terkait', $opCostServiceType)
                                            ->where('tahun', $currentYear)
                                            ->first();
                if ($opCostRecord) {
                    $operationalCostPerUnit = $opCostRecord->biaya_per_unit;
                    Log::info("Operational Cost per Unit found for Kinerja IFCS {$record->golongan} ({$opCostServiceType}) for {$currentYear}: {$operationalCostPerUnit}");
                } else {
                    Log::warning("Biaya operasional per unit tidak ditemukan untuk Kinerja IFCS Golongan '{$record->golongan}', Jenis Layanan '{$opCostServiceType}', Tahun {$currentYear}. Biaya akan 0.");
                }


                foreach ($dailyDistributedValues as $dateString => $dailyValue) {
                    $waktuDim = DimWaktu::where('tanggal', $dateString)->first();
                    if (!$waktuDim) { Log::error("Dimensi Waktu untuk {$dateString} tidak ditemukan (Kinerja IFCS)."); continue; }

                    $dailyCalculatedRevenue = round($dailyValue); // Ini adalah pendapatan yang sudah didistribusikan

                    $dailyCalculatedCost = 0;    // Inisialisasi
                    $dailyCalculatedProfit = 0;  // Inisialisasi

                    // Jika pendapatan harian adalah 0, maka biaya dan laba juga 0
                    if ($dailyCalculatedRevenue === 0) { // Menggunakan dailyCalculatedRevenue sebagai basis
                        Log::info("Pendapatan harian 0 untuk {$dateString}. Biaya/Laba diset 0 (Kinerja IFCS).");
                    } else {
                        $dailyCalculatedCost = $dailyCalculatedRevenue * $operationalCostPerUnit;
                        $dailyCalculatedProfit = $dailyCalculatedRevenue - $dailyCalculatedCost;
                    }

                    // Log warning jika ada inkonsistensi
                    if ($dailyCalculatedRevenue === 0 && ($dailyCalculatedCost != 0 || $dailyCalculatedProfit != 0)) {
                        Log::warning("Inconsistent data: pendapatan 0 tapi biaya/laba tidak nol untuk tanggal {$dateString} (Kinerja IFCS).");
                    }

                    // --- FILTER UTAMA: Hanya simpan jika pelabuhan_id TIDAK NULL ---
                    // Karena data Kinerja IFCS tidak punya pelabuhan_id, ini akan selalu NULL.
                    // Oleh karena itu, baris ini TIDAK akan disimpan jika filter diterapkan.
                    // Jika Anda ingin menyimpan data Kinerja IFCS, JANGAN tambahkan filter ini di sini.
                    // Untuk saat ini, saya akan menambahkan filter yang selalu mengembalikan false untuk Kinerja IFCS
                    // karena pelabuhan_id-nya NULL.
                    $pelabuhanIdForKinerjaIfcs = null; // Kinerja IFCS tidak punya pelabuhan_id
                    if ($pelabuhanIdForKinerjaIfcs !== null) { // Kondisi ini akan selalu false
                        FactKinerjaIFCS::updateOrCreate(
                            [
                                'waktu_id' => $waktuDim->waktu_id,
                                'layanan_id' => $layananIfcsDim->layanan_id,
                                'golongan_id' => $golonganDim->golongan_id,
                                'pelabuhan_id' => null, // Tetap NULL
                                'kapal_id' => null,
                            ],
                            [
                                'total_pendapatan' => DB::raw("total_pendapatan + " . $dailyCalculatedRevenue),
                                'total_laba' => DB::raw("total_laba + " . $dailyCalculatedProfit),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                    } else {
                        Log::info("Melewatkan entri Kinerja IFCS karena pelabuhan_id NULL pada tanggal {$dateString}.");
                    }
                }
            } else {
                Log::info("Kinerja IFCS Revenue for Golongan '{$record->golongan}' for {$monthName} {$currentYear} is zero. Skipping distribution.");
            }
        }
    }

    /**
     * Proses data laba kapal dan distribusikan ke harian per kapal dan jenis.
     */
    protected function distributeLabaKapalData(int $currentYear, int $monthNum, \Illuminate\Support\Collection $datesInTargetMonth, array $holidays, int $daysInMonth)
    {
        $monthName = strtolower(Carbon::create(null, $monthNum, 1)->translatedFormat('F'));

        // Ambil SEMUA baris laba/pendapatan kapal (bukan hanya 'Total')
        $labaKapalRecords = laba_kapal::where('tahun', $currentYear)->get();
        Log::info("Laba Kapal Records for {$monthName} {$currentYear} (Total rows): " . $labaKapalRecords->count());

        foreach ($labaKapalRecords as $record) {
            if (strtoupper(trim($record->kapal)) === 'TOTAL') {
                Log::info("Skipping 'Total' row for Laba Kapal in {$monthName} {$currentYear}.");
                continue;
            }

            $kapalDim = DimKapal::where('nama_kapal', $record->kapal)->first();
            $layananDim = DimLayanan::where('jenis_layanan', $record->jenis)->first();

            if (!$kapalDim) { Log::error("Dimensi Kapal '{$record->kapal}' tidak ditemukan."); continue; }
            if (!$layananDim) { Log::error("Dimensi Layanan '{$record->jenis}' tidak ditemukan."); continue; }

            $totalMonthlyValue = $record->$monthName ?? 0;
            Log::info("Laba Kapal Monthly Value for '{$record->kapal}' ({$record->jenis}) for {$monthName} {$currentYear}: {$totalMonthlyValue}");

            if ($totalMonthlyValue > 0) {
                $dailyDistributedValues = $this->distributeMonthlyTotal($totalMonthlyValue, $datesInTargetMonth, $holidays);

                foreach ($dailyDistributedValues as $dateString => $dailyValue) {
                    $waktuDim = DimWaktu::where('tanggal', $dateString)->first();
                    if (!$waktuDim) { Log::error("Dimensi Waktu untuk {$dateString} tidak ditemukan (Laba Kapal)."); continue; }

                    $roundedDailyValue = round($dailyValue);

                    $dailyCalculatedRevenue = 0; // Inisialisasi
                    $dailyCalculatedProfit = 0;  // Inisialisasi

                    // Jika nilai harian adalah 0, maka pendapatan dan laba juga 0
                    if ($roundedDailyValue === 0) {
                        Log::info("Nilai harian 0 untuk {$dateString}. Pendapatan/Laba diset 0 (Laba Kapal).");
                    } else {
                        // Untuk data laba_kapal, kita asumsikan total_pendapatan dan total_laba sudah disediakan
                        // oleh data sumber, jadi kita hanya mendistribusikan dan mengakumulasi.
                        $dailyCalculatedRevenue = ($record->jenis === 'pendapatan') ? $roundedDailyValue : 0;
                        $dailyCalculatedProfit = ($record->jenis === 'labarugi') ? $roundedDailyValue : 0;
                    }

                    // Log warning jika ada inkonsistensi
                    if ($roundedDailyValue === 0 && ($dailyCalculatedRevenue != 0 || $dailyCalculatedProfit != 0)) {
                        Log::warning("Inconsistent data: nilai harian 0 tapi revenue/laba tidak nol untuk tanggal {$dateString} (Laba Kapal).");
                    }

                    // --- FILTER UTAMA: Hanya simpan jika pelabuhan_id TIDAK NULL ---
                    // Karena data Laba Kapal tidak punya pelabuhan_id, ini akan selalu NULL.
                    // Oleh karena itu, baris ini TIDAK akan disimpan jika filter diterapkan.
                    // Untuk saat ini, saya akan menambahkan filter yang selalu mengembalikan false untuk Laba Kapal
                    // karena pelabuhan_id-nya NULL.
                    $pelabuhanIdForLabaKapal = null; // Laba Kapal tidak punya pelabuhan_id
                    if ($pelabuhanIdForLabaKapal !== null) { // Kondisi ini akan selalu false
                        FactKinerjaIFCS::updateOrCreate(
                            [
                                'waktu_id' => $waktuDim->waktu_id,
                                'kapal_id' => $kapalDim->kapal_id,
                                'layanan_id' => $layananDim->layanan_id,
                                'pelabuhan_id' => null, // Tetap NULL
                                'golongan_id' => null,
                            ],
                            [
                                'total_pendapatan' => DB::raw("total_pendapatan + " . $dailyCalculatedRevenue),
                                'total_laba' => DB::raw("total_laba + " . $dailyCalculatedProfit),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                    } else {
                        Log::info("Melewatkan entri Laba Kapal karena pelabuhan_id NULL pada tanggal {$dateString}.");
                    }
                }
            } else {
                Log::info("Laba Kapal Monthly Value for '{$record->kapal}' ({$record->jenis}) for {$monthName} {$currentYear} is zero. Skipping distribution.");
            }
        }
    }
}
