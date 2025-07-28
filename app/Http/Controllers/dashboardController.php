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

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        
        // --- BARIS YANG DISESUAIKAN ---
        // Mengambil tahun yang valid dari tabel DimWaktu, tetapi membatasi hanya sampai tahun saat ini.
        $validYears = DimWaktu::select('tahun')
                            ->where('tahun', '<=', $currentYear) // Filter untuk membatasi tahun
                            ->distinct()
                            ->orderBy('tahun', 'asc')
                            ->pluck('tahun')
                            ->toArray();

        $selectedYear = $request->input('tahun', $currentYear);

        // Jika tahun yang dipilih tidak ada di daftar tahun yang valid, gunakan tahun saat ini.
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

        // Card 1: Total Pendapatan IFCS (Semua Tahun)
        $totalRevenueIfcsAllYears = FactKinerjaIFCS::select(
                DB::raw('SUM(fact_kinerja_ifcs.total_pendapatan) as total_revenue')
            )
            ->whereIn('fact_kinerja_ifcs.pelabuhan_id', $pelabuhanIds)
            ->where('fact_kinerja_ifcs.layanan_id', $ifcsLayananId)
            ->value('total_revenue') ?? 0;


        // Card 2: Total Pendapatan IFCS (Tahun Saat Ini)
        $totalRevenueIfcsCurrentYear = FactKinerjaIFCS::select(
                DB::raw('SUM(fact_kinerja_ifcs.total_pendapatan) as total_revenue')
            )
            ->join('dim_waktu', 'fact_kinerja_ifcs.waktu_id', '=', 'dim_waktu.waktu_id')
            ->whereIn('fact_kinerja_ifcs.pelabuhan_id', $pelabuhanIds)
            ->where('fact_kinerja_ifcs.layanan_id', $ifcsLayananId)
            ->where('dim_waktu.tahun', $selectedYear)
            ->value('total_revenue') ?? 0;


        // Card 3: Total Produksi IFCS (Semua Tahun)
        $totalProductionIfcsAllYears = FactKinerjaIFCS::select(
                DB::raw('SUM(fact_kinerja_ifcs.jumlah_produksi) as total_production')
            )
            ->whereIn('fact_kinerja_ifcs.pelabuhan_id', $pelabuhanIds)
            ->where('fact_kinerja_ifcs.layanan_id', $ifcsLayananId)
            ->value('total_production') ?? 0;


        // Card 4: Total Produksi IFCS (Tahun Saat Ini)
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
            ->where('tahun', '<=', $currentYear) // Menambahkan filter tahun
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
            ->where('tahun', '<=', $currentYear) // Menambahkan filter tahun
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

        return view('dashboard', compact(
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
        ));
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
}