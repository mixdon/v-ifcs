<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataWarehouseService;

class RunEtlDataWarehouse extends Command
{
    protected $signature = 'etl:run {--year= : The year to process (optional)}';
    protected $description = 'Runs the ETL process for the data warehouse.';

    public function handle(DataWarehouseService $dataWarehouseService)
    {
        $year = $this->option('year');
        $currentYear = date('Y');
        $yearsToProcess = [];

        if ($year) {
            $yearsToProcess = [$year];
        } else {
            // Proses dari tahun terlama data Anda hingga tahun saat ini
            // Ganti 2020 dengan tahun awal data Anda yang sebenarnya
            for ($y = 2020; $y <= $currentYear; $y++) {
                $yearsToProcess[] = $y;
            }
        }

        foreach ($yearsToProcess as $y) {
            try {
                $this->info("Processing ETL for year: $y...");
                $dataWarehouseService->runEtlForYear($y);
                $this->info("ETL for year $y completed successfully.");
            } catch (\Exception $e) {
                $this->error("ETL failed for year $y: " . $e->getMessage());
                return Command::FAILURE;
            }
        }

        $this->info('All specified ETL processes completed.');
        return Command::SUCCESS;
    }
}