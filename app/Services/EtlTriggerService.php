<?php

namespace App\Services;

use App\Models\pelabuhan_merak;
use App\Models\pelabuhan_bakauheni;
use App\Services\DataWarehouseService;
use Illuminate\Support\Facades\Log;

class EtlTriggerService
{
    protected $dataWarehouseService;

    public function __construct(DataWarehouseService $dataWarehouseService)
    {
        $this->dataWarehouseService = $dataWarehouseService;
    }

    public function checkAndTriggerEtl(int $tahun): bool
    {
        Log::info("Memeriksa data untuk pemicu ETL untuk tahun {$tahun}...");

        // Memeriksa apakah ada data untuk pelabuhan Merak pada tahun ini
        $merakDataExists = pelabuhan_merak::where('tahun', $tahun)->exists();
        // Memeriksa apakah ada data untuk pelabuhan Bakauheni pada tahun ini
        $bakauheniDataExists = pelabuhan_bakauheni::where('tahun', $tahun)->exists();

        if ($merakDataExists && $bakauheniDataExists) {
            Log::info("Data Merak dan Bakauheni untuk tahun {$tahun} sudah lengkap. Memicu proses ETL.");
            try {
                // Panggil layanan ETL utama
                $this->dataWarehouseService->runEtlForYear($tahun);
                Log::info("Proses ETL untuk tahun {$tahun} berhasil dipicu oleh EtlTriggerService.");
                return true;
            } catch (\Exception $e) {
                Log::error("Gagal memicu ETL dari EtlTriggerService untuk tahun {$tahun}: " . $e->getMessage());
                return false;
            }
        } else {
            if (!$merakDataExists) {
                Log::warning("Data Pelabuhan Merak untuk tahun {$tahun} belum ditemukan. ETL tidak dipicu.");
            }
            if (!$bakauheniDataExists) {
                Log::warning("Data Pelabuhan Bakauheni untuk tahun {$tahun} belum ditemukan. ETL tidak dipicu.");
            }
            return false;
        }
    }
}
