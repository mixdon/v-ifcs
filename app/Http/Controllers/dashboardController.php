<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\FactKinerjaIFCS;
use App\Models\DimWaktu;
use App\Models\DimLayanan;
use App\Models\DimPelabuhan;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class dashboardController extends Controller
{
    private function getDashboardData($request, $forecast = null)
    {
        $currentYear = date('Y');
        
        $validYears = DimWaktu::select('tahun')
                            ->where('tahun', '<=', $currentYear)
                            ->distinct()
                            ->orderBy('tahun', 'asc')
                            ->pluck('tahun')
                            ->toArray();

        $selectedYear = $request->input('tahun', $currentYear);
        if (!in_array($selectedYear, $validYears)) {
            $selectedYear = $currentYear;
        }

        $revenueViewMode = $request->input('revenue_view_mode', 'yearly_summary');
        $productionViewMode = $request->input('production_view_mode', 'yearly_summary');
        
        $merakId = DimPelabuhan::where('nama_pelabuhan', 'Merak')->value('pelabuhan_id');
        $bakauheniId = DimPelabuhan::where('nama_pelabuhan', 'Bakauheni')->value('pelabuhan_id');
        $pelabuhanIds = [$merakId, $bakauheniId];

        $ifcsLayananId = DimLayanan::where('jenis_layanan', 'ifcs')->value('layanan_id');
        $nonifcsLayananId = DimLayanan::where('jenis_layanan', 'nonifcs')->value('layanan_id');
        $regulerLayananId = DimLayanan::where('jenis_layanan', 'reguler')->value('layanan_id');
        $redeemLayananId = DimLayanan::where('jenis_layanan', 'redeem')->value('layanan_id');

        // --- Data untuk Kartu Ringkasan (Top 4 Cards) ---
        $totalRevenueIfcsAllYears = FactKinerjaIFCS::select(
                DB::raw('SUM(fact_kinerja_ifcs.total_pendapatan) as total_revenue')
            )
            ->whereIn('fact_kinerja_ifcs.pelabuhan_id', $pelabuhanIds)
            ->where('fact_kinerja_ifcs.layanan_id', $ifcsLayananId)
            ->value('total_revenue') ?? 0;

        $totalRevenueIfcsCurrentYear = FactKinerjaIFCS::select(
                DB::raw('SUM(fact_kinerja_ifcs.total_pendapatan) as total_revenue')
            )
            ->join('dim_waktu', 'fact_kinerja_ifcs.waktu_id', '=', 'dim_waktu.waktu_id')
            ->whereIn('fact_kinerja_ifcs.pelabuhan_id', $pelabuhanIds)
            ->where('fact_kinerja_ifcs.layanan_id', $ifcsLayananId)
            ->where('dim_waktu.tahun', $selectedYear)
            ->value('total_revenue') ?? 0;

        $totalProductionIfcsAllYears = FactKinerjaIFCS::select(
                DB::raw('SUM(fact_kinerja_ifcs.jumlah_produksi) as total_production')
            )
            ->whereIn('fact_kinerja_ifcs.pelabuhan_id', $pelabuhanIds)
            ->where('fact_kinerja_ifcs.layanan_id', $ifcsLayananId)
            ->value('total_production') ?? 0;

        $totalProductionIfcsCurrentYear = FactKinerjaIFCS::select(
                DB::raw('SUM(fact_kinerja_ifcs.jumlah_produksi) as total_production')
            )
            ->join('dim_waktu', 'fact_kinerja_ifcs.waktu_id', '=', 'dim_waktu.waktu_id')
            ->whereIn('fact_kinerja_ifcs.pelabuhan_id', $pelabuhanIds)
            ->where('fact_kinerja_ifcs.layanan_id', $ifcsLayananId)
            ->where('dim_waktu.tahun', $selectedYear)
            ->value('total_production') ?? 0;

        // --- Data untuk Chart Total Pendapatan Layanan IFCS (Per Tahun) ---
        $ifcsRevenuePerYear = FactKinerjaIFCS::select(
                'dim_waktu.tahun',
                DB::raw('SUM(fact_kinerja_ifcs.total_pendapatan) as total_revenue')
            )
            ->join('dim_waktu', 'fact_kinerja_ifcs.waktu_id', '=', 'dim_waktu.waktu_id')
            ->where('fact_kinerja_ifcs.layanan_id', $ifcsLayananId)
            ->whereIn('fact_kinerja_ifcs.pelabuhan_id', $pelabuhanIds)
            ->groupBy('dim_waktu.tahun')
            ->orderBy('dim_waktu.tahun')
            ->get();
        $ifcsRevenueChartData = [];
        foreach ($ifcsRevenuePerYear as $item) {
            $ifcsRevenueChartData[$item->tahun] = $item->total_revenue;
        }

        // --- Data untuk Chart Total Pendapatan Layanan IFCS (Per Bulan, Hanya Tahun Terpilih) ---
        $ifcsMonthlyRevenueData = FactKinerjaIFCS::select(
                'dim_waktu.bulan_numerik',
                'dim_waktu.bulan',
                DB::raw('SUM(fact_kinerja_ifcs.total_pendapatan) as total_revenue')
            )
            ->join('dim_waktu', 'fact_kinerja_ifcs.waktu_id', '=', 'dim_waktu.waktu_id')
            ->where('fact_kinerja_ifcs.layanan_id', $ifcsLayananId)
            ->whereIn('fact_kinerja_ifcs.pelabuhan_id', $pelabuhanIds)
            ->where('dim_waktu.tahun', $selectedYear)
            ->groupBy('dim_waktu.bulan_numerik', 'dim_waktu.bulan')
            ->orderBy('dim_waktu.bulan_numerik')
            ->get();
        $ifcsMonthlyRevenueChartData = [];
        $ifcsMonthlyRevenueChartLabels = [];
        foreach ($ifcsMonthlyRevenueData as $item) {
            $ifcsMonthlyRevenueChartData[] = $item->total_revenue;
            $ifcsMonthlyRevenueChartLabels[] = $item->bulan;
        }

        // --- Data untuk Chart Total Produksi Layanan IFCS (Per Tahun) ---
        $ifcsProductionPerYear = FactKinerjaIFCS::select(
                'dim_waktu.tahun',
                DB::raw('SUM(fact_kinerja_ifcs.jumlah_produksi) as total_production')
            )
            ->join('dim_waktu', 'fact_kinerja_ifcs.waktu_id', '=', 'dim_waktu.waktu_id')
            ->where('fact_kinerja_ifcs.layanan_id', $ifcsLayananId)
            ->whereIn('fact_kinerja_ifcs.pelabuhan_id', $pelabuhanIds)
            ->groupBy('dim_waktu.tahun')
            ->orderBy('dim_waktu.tahun')
            ->get();
        $ifcsProductionChartData = [];
        foreach ($ifcsProductionPerYear as $item) {
            $ifcsProductionChartData[$item->tahun] = $item->total_production;
        }

        // --- Data untuk Chart Total Produksi Layanan IFCS (Per Bulan, Hanya Tahun Terpilih) ---
        $ifcsMonthlyProductionData = FactKinerjaIFCS::select(
                'dim_waktu.bulan_numerik',
                'dim_waktu.bulan',
                DB::raw('SUM(fact_kinerja_ifcs.jumlah_produksi) as total_production')
            )
            ->join('dim_waktu', 'fact_kinerja_ifcs.waktu_id', '=', 'dim_waktu.waktu_id')
            ->where('fact_kinerja_ifcs.layanan_id', $ifcsLayananId)
            ->whereIn('fact_kinerja_ifcs.pelabuhan_id', $pelabuhanIds)
            ->where('dim_waktu.tahun', $selectedYear)
            ->groupBy('dim_waktu.bulan_numerik', 'dim_waktu.bulan')
            ->orderBy('dim_waktu.bulan_numerik')
            ->get();
        $ifcsMonthlyProductionChartData = [];
        $ifcsMonthlyProductionChartLabels = [];
        foreach ($ifcsMonthlyProductionData as $item) {
            $ifcsMonthlyProductionChartData[] = $item->total_production;
            $ifcsMonthlyProductionChartLabels[] = $item->bulan;
        }

        // --- Data untuk Chart Market Lintasan (IFCS vs Industri) - Semua Tahun (Stacked Bar) ---
        $results1 = DB::table('market_lintasan')
            ->select('tahun', 'jenis', DB::raw('SUM(gabungan) as total'))
            ->where('golongan', '=', 'Total')
            ->where('tahun', '<=', $currentYear)
            ->groupBy('tahun', 'jenis')
            ->get();
        $marketData_temp = [];
        foreach ($results1 as $result1) {
            $marketData_temp[$result1->tahun][$result1->jenis] = $result1->total;
        }
        $marketLintasanData = [];
        foreach ($marketData_temp as $year => $data) {
            $totalIfcs = $data['ifcs'] ?? 0;
            $totalIndustri = $data['industri'] ?? 0;
            $total = $totalIfcs + $totalIndustri;
            $marketLintasanData[] = [
                'tahun' => $year,
                'ifcs_value' => $totalIfcs,
                'industri_value' => $totalIndustri,
                'ifcs_percentage' => ($total != 0) ? ($totalIfcs / $total) * 100 : 0,
                'industri_percentage' => ($total != 0) ? ($totalIndustri / $total) * 100 : 0,
                'total_production' => $total
            ];
        }
        usort($marketLintasanData, function($a, $b) {
            return $a['tahun'] <=> $b['tahun'];
        });

        // --- Data untuk Chart Komposisi Produksi Tahunan (IFCS, NonIFCS, Reguler) - Semua Tahun (Stacked Bar) ---
        $results0 = DB::table('komposisi_segmen')
            ->select('tahun', 'ifcs_redeem', 'nonifcs')
            ->where('golongan', '=', 'Total')
            ->where('jenis', 'gabungan')
            ->where('tahun', '<=', $currentYear)
            ->get();
        $komposisiData_temp = [];
        foreach ($results0 as $result0) {
            $komposisiData_temp[$result0->tahun] = [
                'ifcs_redeem' => $result0->ifcs_redeem,
                'nonifcs' => $result0->nonifcs
            ];
        }
        $annualProductionCompositionData = [];
        foreach ($komposisiData_temp as $year => $data) {
            $totalifcsredeem = $data['ifcs_redeem'] ?? 0;
            $totalnonifcs = $data['nonifcs'] ?? 0;
            $total = $totalifcsredeem + $totalnonifcs;
            $annualProductionCompositionData[] = [
                'tahun' => $year,
                'ifcs_value' => $totalifcsredeem,
                'nonifcs_value' => $totalnonifcs,
                'reguler_value' => 0,
                'ifcs_percentage' => ($total != 0) ? ($totalifcsredeem / $total) * 100 : 0,
                'nonifcs_percentage' => ($total != 0) ? ($totalnonifcs / $total) * 100 : 0,
                'reguler_percentage' => 0,
                'total_production' => $total
            ];
        }
        usort($annualProductionCompositionData, function($a, $b) {
            return $a['tahun'] <=> $b['tahun'];
        });

        return compact(
            'selectedYear',
            'validYears',
            'totalRevenueIfcsAllYears',
            'totalRevenueIfcsCurrentYear',
            'totalProductionIfcsAllYears',
            'totalProductionIfcsCurrentYear',
            'ifcsRevenueChartData',
            'ifcsMonthlyRevenueChartData',
            'ifcsMonthlyRevenueChartLabels',
            'ifcsProductionChartData',
            'ifcsMonthlyProductionChartData',
            'ifcsMonthlyProductionChartLabels',
            'annualProductionCompositionData',
            'marketLintasanData',
            'revenueViewMode',
            'productionViewMode',
            'forecast',
        );
    }

    public function index(Request $request)
    {
        $data = $this->getDashboardData($request);
        return view('dashboard', $data);
    }
    
    public function triggerEtl()
    {
        try {
            Artisan::call('etl:run');
            return redirect()->route('dashboard')->with('success', 'Proses ETL berhasil dijalankan!');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Gagal menjalankan proses ETL: ' . $e->getMessage());
        }
    }

    public function runForecast(Request $request)
    {
        try {
            // Ambil data dari tabel `fact_kinerja_ifcs` dan format sebagai ds, y
            $data = DB::table('fact_kinerja_ifcs')
                ->selectRaw("STR_TO_DATE(waktu_id, '%Y%m%d') as ds, SUM(jumlah_produksi) as y")
                ->groupBy('waktu_id')
                ->orderBy('waktu_id')
                ->get();

            // Simpan data jadi CSV untuk Python
            $csvPath = storage_path('app/forecast.csv');
            $file = fopen($csvPath, 'w');
            fputcsv($file, ['ds', 'y']);
            foreach ($data as $row) {
                fputcsv($file, [$row->ds, $row->y]);
            }
            fclose($file);

            // Jalankan script Python dengan path absolut
            $python = 'C:\\Users\\NITRO 5\\AppData\\Local\\Programs\\Python\\Python310\\python.exe';
            $script = 'D:\\Git\\V-IFCS\\python_scripts\\forecast.py'; 
            
            // Menggunakan Symfony Process dengan variabel lingkungan yang diperbarui
            // Tambahkan variabel HOME atau USERPROFILE untuk mengatasi error "Could not determine home directory"
            $process = new Process(
                [$python, $script, $csvPath], 
                null, 
                [
                    'SYSTEMROOT' => getenv('SYSTEMROOT'),
                    'PATH' => getenv('PATH'),
                    // Tambahkan variabel USERPROFILE untuk Windows atau HOME untuk Linux
                    'USERPROFILE' => getenv('USERPROFILE') // Pastikan ini sesuai dengan OS Anda
                ]
            );
            $process->run();

            // Eksekusi jika gagal
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Ambil hasil forecast dari file CSV
            $forecastPath = storage_path('app/forecast_result.csv');
            $forecast = [];
            if (file_exists($forecastPath)) {
                $rows = array_map('str_getcsv', file($forecastPath));
                if (count($rows) > 0) {
                    $header = array_shift($rows);
                    foreach ($rows as $row) {
                        if (count($row) === count($header)) {
                             $forecast[] = array_combine($header, $row);
                        }
                    }
                }
            }

            $dashboardData = $this->getDashboardData($request, $forecast);
            return view('dashboard', $dashboardData);

        } catch (ProcessFailedException $exception) {
            // Penanganan error jika skrip Python gagal
            return redirect()->route('dashboard')->with('error', 'Gagal menjalankan skrip Python: ' . $exception->getMessage() . ' | Output: ' . $exception->getProcess()->getOutput() . ' | Error Output: ' . $exception->getProcess()->getErrorOutput());
        } catch (\Exception $e) {
            // Penanganan error umum
            return redirect()->route('dashboard')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}