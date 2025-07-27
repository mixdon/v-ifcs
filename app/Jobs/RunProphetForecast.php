<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use App\Models\FactKinerjaIFCS;
use App\Models\ForecastingResult;
use Carbon\Carbon;

class RunProphetForecast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $forecastMetric;
    protected int $forecastMonths;

    /**
     * Create a new job instance.
     */
    public function __construct(string $forecastMetric, int $forecastMonths = 12)
    {
        $this->forecastMetric = $forecastMetric;
        $this->forecastMonths = $forecastMonths;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Memulai forecasting Prophet untuk metrik: {$this->forecastMetric}");

        // Ambil data dan agregasi bulanan
        $dataForProphet = FactKinerjaIFCS::select(
            DB::raw("DATE_FORMAT(tanggal, '%Y-%m-%d') as ds"),
            DB::raw("SUM(CASE
                WHEN '{$this->forecastMetric}' = 'jumlah_produksi' THEN jumlah_produksi
                WHEN '{$this->forecastMetric}' = 'total_pendapatan' THEN total_pendapatan
                WHEN '{$this->forecastMetric}' = 'total_laba' THEN total_laba
                ELSE 0
            END) as y")
        )
        ->join('dim_waktu', 'fact_kinerja_ifcs.waktu_id', '=', 'dim_waktu.waktu_id')
        ->groupBy('ds')
        ->orderBy('ds')
        ->get();

        if ($dataForProphet->isEmpty()) {
            Log::warning("Tidak ada data ditemukan untuk metrik forecasting: {$this->forecastMetric}. Melewatkan proses forecasting.");
            return;
        }

        // Path file
        $storagePath = storage_path('app/prophet_data');
        $forecastOutputPath = storage_path('app/prophet_forecasts');

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        if (!file_exists($forecastOutputPath)) {
            mkdir($forecastOutputPath, 0777, true);
        }

        $csvDataPath = "{$storagePath}/{$this->forecastMetric}_data.csv";
        $forecastCsvPath = "{$forecastOutputPath}/{$this->forecastMetric}_forecast.csv";
        $metricsJsonPath = "{$forecastOutputPath}/{$this->forecastMetric}_metrics.json";

        // Simpan CSV
        $file = fopen($csvDataPath, 'w');
        fputcsv($file, ['ds', 'y']);
        foreach ($dataForProphet as $row) {
            fputcsv($file, [$row->ds, $row->y]);
        }
        fclose($file);
        Log::info("Data untuk Prophet disimpan ke: {$csvDataPath}");

        // Eksekusi skrip Python
        $pythonScript = base_path('python_scripts/prophet_forecast.py');
        $pythonExecutable = base_path('venv_prophet/Scripts/python.exe'); 

        $command = [
            $pythonExecutable,
            $pythonScript,
            $csvDataPath,
            $forecastCsvPath,
            $metricsJsonPath,
            (string) $this->forecastMonths
        ];

        Log::info("Menjalankan perintah Python: " . implode(' ', $command));

        $process = new Process($command);
        $process->setTimeout(3600);
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error("Forecasting Prophet gagal untuk {$this->forecastMetric}. Error: " . $process->getErrorOutput());
            return;
        }

        Log::info("Output skrip Prophet: " . $process->getOutput());

        // Baca hasil
        if (file_exists($forecastCsvPath) && file_exists($metricsJsonPath)) {
            $forecastData = array_map('str_getcsv', file($forecastCsvPath));
            $forecastHeaders = array_shift($forecastData);

            $forecastResults = [];
            foreach ($forecastData as $row) {
                if (count($row) === count($forecastHeaders)) {
                    $forecastResults[] = array_combine($forecastHeaders, $row);
                }
            }

            $metrics = json_decode(file_get_contents($metricsJsonPath), true);
            Log::info("Metrik model yang dibaca: " . json_encode($metrics));

            // Simpan ke database
            DB::beginTransaction();
            try {
                ForecastingResult::where('metric_name', $this->forecastMetric)->delete();

                foreach ($forecastResults as $item) {
                    ForecastingResult::create([
                        'metric_name' => $this->forecastMetric,
                        'forecast_date' => $item['ds'],
                        'predicted_value' => round($item['yhat'], 2),
                        'lower_bound' => round($item['yhat_lower'] ?? 0, 2),
                        'upper_bound' => round($item['yhat_upper'] ?? 0, 2),
                        'actual_value' => round($item['y'] ?? 0, 2),
                        'model_metrics' => json_encode($metrics),
                    ]);
                }

                DB::commit();
                Log::info("Hasil forecasting Prophet untuk {$this->forecastMetric} disimpan ke DB.");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Gagal menyimpan hasil forecasting ke DB: " . $e->getMessage());
            }

            // Bersihkan file sementara
            unlink($csvDataPath);
            unlink($forecastCsvPath);
            unlink($metricsJsonPath);
            Log::info("File sementara forecasting dihapus.");
        } else {
            Log::error("File output Prophet tidak ditemukan untuk {$this->forecastMetric}.");
        }
    }
}