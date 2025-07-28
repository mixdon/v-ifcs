<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\pelabuhan_merak;
use App\Models\pelabuhan_bakauheni;
use App\Models\market_lintasan;
use Illuminate\Database\QueryException;

// Impor controller yang diperlukan
use App\Http\Controllers\pelabuhanMerakController;
use App\Http\Controllers\pelabuhanBakauheniController;

class marketLintasanController extends Controller
{
    // Fungsi index hanya untuk menampilkan data yang sudah ada.
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $startYear = 2020;
        $validYears = range($startYear, $currentYear);
        $tahun = $request->input('tahun', null);
        $yearsToFetch = $tahun && in_array($tahun, $validYears) ? [$tahun] : $validYears;

        try {
            $market_lintasan = market_lintasan::whereIn('tahun', $yearsToFetch)->get();

            return view('ifcs.market-lintasan', [
                'market_lintasan' => $market_lintasan,
                'years' => $validYears,
                'selectedYear' => $tahun 
            ]);
        } catch (QueryException $e) {
            return redirect()->route('market-lintasan.index')->with('error', 'Terjadi error database: ' . $e->getMessage());
        }
    }
    
    // Fungsi untuk memicu semua perhitungan dan menyimpan hasilnya
    public function runCalculationsForYear($tahun)
    {
        try {
            // Buat instance dari controller lain
            $merakController = new pelabuhanMerakController();
            $bakauheniController = new pelabuhanBakauheniController();

            // PENTING: Jalankan perhitungan total data sumber terlebih dahulu
            $merakController->simpanDataTotalIFCS($tahun);
            $merakController->simpanDataTotalREDEEM($tahun);
            $merakController->simpanDataTotalNONIFCS($tahun);
            $merakController->simpanDataTotalREGULER($tahun);

            $bakauheniController->simpanDataTotalIFCS($tahun);
            $bakauheniController->simpanDataTotalREDEEM($tahun);
            $bakauheniController->simpanDataTotalNONIFCS($tahun);
            $bakauheniController->simpanDataTotalREGULER($tahun);

            // Setelah data sumber dihitung, baru lakukan perhitungan Market Lintasan
            // IFCS
            $this->simpanDataEksekutifIFCS($tahun);
            $this->simpanDataLogistikEksekutifIFCS($tahun);
            $this->simpanDataRedeemEksekutifIFCS($tahun);
            $this->simpanDataLogistikRedeemEksekutifIFCS($tahun);
            $this->simpanDataTotalIFCS($tahun);
        
            // INDUSTRI
            $this->simpanDataBusReguler($tahun);
            $this->simpanDataLogistikReguler($tahun);
            $this->simpanDataEksekutifNonIFCS($tahun);
            $this->simpanDataLogistikEksekutifNonIFCS($tahun);
            $this->simpanDataTotalINDUSTRI($tahun);

            return redirect()->route('market-lintasan.index', ['tahun' => $tahun])
                             ->with('success', "Perhitungan data Market Lintasan untuk tahun {$tahun} berhasil.");
        } catch (QueryException $e) {
            return redirect()->route('market-lintasan.index', ['tahun' => $tahun])
                             ->with('error', "Terjadi error database saat perhitungan: " . $e->getMessage());
        }
    }
    
    // ... (Fungsi-fungsi simpanData... lainnya tetap sama seperti sebelumnya)
    // ... (Logika sudah diperbaiki di balasan terakhir)
    
    // IFCS
    public function simpanDataEksekutifIFCS($tahun)
    {
        $golongans = ['VA', 'VIA'];
        $merakTotal = pelabuhan_merak::whereIn('golongan', $golongans)->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniTotal = pelabuhan_bakauheni::whereIn('golongan', $golongans)->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        
        market_lintasan::updateOrCreate(
            ['golongan' => 'Eksekutif IFCS', 'jenis' => 'ifcs', 'tahun' => $tahun],
            [
                'merak' => $merakTotal,
                'bakauheni' => $bakauheniTotal,
                'gabungan' => $merakTotal + $bakauheniTotal,
            ]
        );
    }
    
    public function simpanDataLogistikEksekutifIFCS($tahun)
    {
        $golongans = ['IVB', 'VB', 'VIB', 'VII', 'VIII', 'IX'];
        $merakTotal = pelabuhan_merak::whereIn('golongan', $golongans)->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniTotal = pelabuhan_bakauheni::whereIn('golongan', $golongans)->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        
        market_lintasan::updateOrCreate(
            ['golongan' => 'Logistik Eksekutif IFCS', 'jenis' => 'ifcs', 'tahun' => $tahun],
            [
                'merak' => $merakTotal,
                'bakauheni' => $bakauheniTotal,
                'gabungan' => $merakTotal + $bakauheniTotal,
            ]
        );
    }
    
    public function simpanDataRedeemEksekutifIFCS($tahun)
    {
        $golongans = ['VA', 'VIA'];
        $merakTotal = pelabuhan_merak::whereIn('golongan', $golongans)->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $bakauheniTotal = pelabuhan_bakauheni::whereIn('golongan', $golongans)->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        
        market_lintasan::updateOrCreate(
            ['golongan' => 'Redeem Eksekutif IFCS', 'jenis' => 'ifcs', 'tahun' => $tahun],
            [
                'merak' => $merakTotal,
                'bakauheni' => $bakauheniTotal,
                'gabungan' => $merakTotal + $bakauheniTotal,
            ]
        );
    }
    
    public function simpanDataLogistikRedeemEksekutifIFCS($tahun)
    {
        $golongans = ['IVB', 'VB', 'VIB', 'VII', 'VIII', 'IX'];
        $merakTotal = pelabuhan_merak::whereIn('golongan', $golongans)->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $bakauheniTotal = pelabuhan_bakauheni::whereIn('golongan', $golongans)->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        
        market_lintasan::updateOrCreate(
            ['golongan' => 'Logistik Redeem Eksekutif IFCS', 'jenis' => 'ifcs', 'tahun' => $tahun],
            [
                'merak' => $merakTotal,
                'bakauheni' => $bakauheniTotal,
                'gabungan' => $merakTotal + $bakauheniTotal,
            ]
        );
    }
    
    public function simpanDataTotalIFCS($tahun)
    {
        $golongans = ['Eksekutif IFCS', 'Logistik Eksekutif IFCS', 'Redeem Eksekutif IFCS', 'Logistik Redeem Eksekutif IFCS'];
        $merakTotal = market_lintasan::whereIn('golongan', $golongans)->where('tahun', $tahun)->where('jenis', 'ifcs')->sum('merak');
        $bakauheniTotal = market_lintasan::whereIn('golongan', $golongans)->where('tahun', $tahun)->where('jenis', 'ifcs')->sum('bakauheni');
        $gabunganTotal = market_lintasan::whereIn('golongan', $golongans)->where('tahun', $tahun)->where('jenis', 'ifcs')->sum('gabungan');
    
        market_lintasan::updateOrCreate(
            ['golongan' => 'Total', 'jenis' => 'ifcs', 'tahun' => $tahun],
            [
                'merak' => $merakTotal,
                'bakauheni' => $bakauheniTotal,
                'gabungan' => $gabunganTotal,
            ]
        );
    }    
    
    // INDUSTRI
    public function simpanDataBusReguler($tahun)
    {
        $golongans = ['VA', 'VIA'];
        $merakTotal = pelabuhan_merak::whereIn('golongan', $golongans)->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniTotal = pelabuhan_bakauheni::whereIn('golongan', $golongans)->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        
        market_lintasan::updateOrCreate(
            ['golongan' => 'Kendaraan Bus Reguler', 'jenis' => 'industri', 'tahun' => $tahun],
            [
                'merak' => $merakTotal,
                'bakauheni' => $bakauheniTotal,
                'gabungan' => $merakTotal + $bakauheniTotal,
            ]
        );
    }

    public function simpanDataLogistikReguler($tahun)
    {
        $golongans = ['IVB', 'VB', 'VIB', 'VII', 'VIII', 'IX'];
        $merakTotal = pelabuhan_merak::whereIn('golongan', $golongans)->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniTotal = pelabuhan_bakauheni::whereIn('golongan', $golongans)->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        
        market_lintasan::updateOrCreate(
            ['golongan' => 'Logistik Reguler', 'jenis' => 'industri', 'tahun' => $tahun],
            [
                'merak' => $merakTotal,
                'bakauheni' => $bakauheniTotal,
                'gabungan' => $merakTotal + $bakauheniTotal,
            ]
        );
    }
    
    public function simpanDataEksekutifNonIFCS($tahun)
    {
        $golongans = ['VA', 'VIA'];
        $merakTotal = pelabuhan_merak::whereIn('golongan', $golongans)->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniTotal = pelabuhan_bakauheni::whereIn('golongan', $golongans)->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        
        market_lintasan::updateOrCreate(
            ['golongan' => 'Eksekutif Non IFCS', 'jenis' => 'industri', 'tahun' => $tahun],
            [
                'merak' => $merakTotal,
                'bakauheni' => $bakauheniTotal,
                'gabungan' => $merakTotal + $bakauheniTotal,
            ]
        );
    }

    public function simpanDataLogistikEksekutifNonIFCS($tahun)
    {
        $golongans = ['IVB', 'VB', 'VIB', 'VII', 'VIII', 'IX'];
        $merakTotal = pelabuhan_merak::whereIn('golongan', $golongans)->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniTotal = pelabuhan_bakauheni::whereIn('golongan', $golongans)->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        
        market_lintasan::updateOrCreate(
            ['golongan' => 'Logistik Eksekutif Non IFCS', 'jenis' => 'industri', 'tahun' => $tahun],
            [
                'merak' => $merakTotal,
                'bakauheni' => $bakauheniTotal,
                'gabungan' => $merakTotal + $bakauheniTotal,
            ]
        );
    }

    public function simpanDataTotalINDUSTRI($tahun)
    {
        $golongans = ['Kendaraan Bus Reguler', 'Logistik Reguler', 'Eksekutif Non IFCS', 'Logistik Eksekutif Non IFCS'];
        $merakTotal = market_lintasan::whereIn('golongan', $golongans)->where('tahun', $tahun)->where('jenis', 'industri')->sum('merak');
        $bakauheniTotal = market_lintasan::whereIn('golongan', $golongans)->where('tahun', $tahun)->where('jenis', 'industri')->sum('bakauheni');
        $gabunganTotal = market_lintasan::whereIn('golongan', $golongans)->where('tahun', $tahun)->where('jenis', 'industri')->sum('gabungan');
        
        market_lintasan::updateOrCreate(
            ['golongan' => 'Total', 'jenis' => 'industri', 'tahun' => $tahun],
            [
                'merak' => $merakTotal,
                'bakauheni' => $bakauheniTotal,
                'gabungan' => $gabunganTotal,
            ]
        );
    }
}