<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\pelabuhan_merak;
use App\Models\pelabuhan_bakauheni;
use App\Models\market_lintasan;
use Illuminate\Database\QueryException;

class marketLintasanController extends Controller
{
    // Fungsi index sekarang hanya mengambil dan menampilkan data yang sudah ada.
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $startYear = 2020;
        $validYears = range($startYear, $currentYear);
        $tahun = $request->input('tahun', null);
        $yearsToFetch = $tahun && in_array($tahun, $validYears) ? [$tahun] : $validYears;

        try {
            // Data diambil langsung dari tabel market_lintasan
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

    // Fungsi untuk memicu semua perhitungan (gunakan ini secara manual atau via artisan command)
    public function runCalculationsForYear($tahun)
    {
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
    }
}