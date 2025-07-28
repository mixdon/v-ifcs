<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\pelabuhan_merak;
use App\Models\pelabuhan_bakauheni;
use App\Models\market_lintasan;

class marketLintasanController extends Controller
{        
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $startYear = 2020;
        $validYears = range($startYear, $currentYear);
        $tahun = $request->input('tahun', null);

        $years = $tahun && in_array($tahun, $validYears) ? [$tahun] : $validYears;

        foreach ($years as $year) {
            // IFCS
            $this->simpanDataEksekutifIFCS($year);
            $this->simpanDataLogistikEksekutifIFCS($year);
            $this->simpanDataRedeemEksekutifIFCS($year);
            $this->simpanDataLogistikRedeemEksekutifIFCS($year);
            $this->simpanDataTotalIFCS($year);
        
            // INDUSTRI
            $this->simpanDataBusReguler($year);
            $this->simpanDataLogistikReguler($year);
            $this->simpanDataEksekkutifNonIFCS($year);
            $this->simpanDataEksekutifNonIFCS($year);
            $this->simpanDataTotalINDUSTRI($year);
        }
        
        // Perbaikan: Mengambil dan mengelompokkan data
        $market_lintasan = market_lintasan::whereIn('tahun', $years)
            ->select('jenis', 'golongan',
                DB::raw('SUM(merak) as merak'),
                DB::raw('SUM(bakauheni) as bakauheni'),
                DB::raw('SUM(gabungan) as gabungan')
            )
            ->groupBy('jenis', 'golongan')
            ->orderBy('jenis')
            ->orderBy('golongan')
            ->get();
        
        return view('ifcs.market-lintasan', [
            'market_lintasan' => $market_lintasan,
            'years' => $validYears,
            'selectedYear' => $tahun 
        ]);
    }  
    
    //IFCS
    public function simpanDataEksekutifIFCS($tahun)
    {
        // Ambil data dari pelabuhan_merak
        $merakVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $merakVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');

        // Ambil data dari pelabuhan_bakauheni
        $bakauheniVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total untuk masing-masing pelabuhan
        $totalMerak = $merakVA + $merakVIA;
        $totalBakauheni = $bakauheniVA + $bakauheniVIA;

        // Hitung total gabungan
        $totalGabungan = $totalMerak + $totalBakauheni;

        // Simpan ke tabel market_lintasan
        market_lintasan::updateOrCreate(
            ['golongan' => 'Eksekutif IFCS', 'jenis' => 'ifcs', 'tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }

    public function simpanDataLogistikEksekutifIFCS($tahun)
    {
        $merakIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $merakVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $merakVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $merakVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $merakVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $merakIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');

        $bakauheniIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');

        $totalMerak = $merakIVB + $merakVB + $merakVIB + $merakVII + $merakVIII + $merakIX;
        $totalBakauheni = $bakauheniIVB + $bakauheniVB + $bakauheniVIB + $bakauheniVII + $bakauheniVIII + $bakauheniIX;

        $totalGabungan = $totalMerak + $totalBakauheni;

        market_lintasan::updateOrCreate(
            ['golongan' => 'Logistik Eksekutif IFCS','jenis' => 'ifcs','tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }

    public function simpanDataRedeemEksekutifIFCS($tahun)
    {
        // Ambil data dari pelabuhan_merak
        $merakVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $merakVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');

        // Ambil data dari pelabuhan_bakauheni
        $bakauheniVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $bakauheniVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');

        // Hitung total untuk masing-masing pelabuhan
        $totalMerak = $merakVA + $merakVIA;
        $totalBakauheni = $bakauheniVA + $bakauheniVIA;

        // Hitung total gabungan
        $totalGabungan = $totalMerak + $totalBakauheni;


        // Simpan ke tabel market_lintasan
        market_lintasan::updateOrCreate(
            ['golongan' => 'Redeem Eksekutif IFCS', 'jenis' => 'ifcs','tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }
    
    public function simpanDataLogistikRedeemEksekutifIFCS($tahun)
    {
        // Ambil data dari pelabuhan_merak
        $merakIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $merakVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $merakVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $merakVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $merakVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $merakIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');

        // Ambil data dari pelabuhan_bakauheni
        $bakauheniIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $bakauheniVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $bakauheniVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $bakauheniVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $bakauheniVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $bakauheniIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');

        // Hitung total untuk masing-masing pelabuhan
        $totalMerak = $merakIVB + $merakVB + $merakVIB + $merakVII + $merakVIII + $merakIX;
        $totalBakauheni = $bakauheniIVB + $bakauheniVB + $bakauheniVIB + $bakauheniVII + $bakauheniVIII + $bakauheniIX;

        // Hitung total gabungan
        $totalGabungan = $totalMerak + $totalBakauheni;


        // Simpan ke tabel market_lintasan
        market_lintasan::updateOrCreate(
            ['golongan' => 'Logistik Redeem Eksekutif IFCS','jenis' => 'ifcs','tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }

    public function simpanDataTotalIFCS($tahun)
    {
        // Ambil data untuk kolom 'merak'
        $merak1 = market_lintasan::where('golongan', 'Eksekutif IFCS')->where('tahun', $tahun)->sum('merak');
        $merak2 = market_lintasan::where('golongan', 'Logistik Eksekutif IFCS')->where('tahun', $tahun)->sum('merak');
        $merak3 = market_lintasan::where('golongan', 'Redeem Eksekutif IFCS')->where('tahun', $tahun)->sum('merak');
        $merak4 = market_lintasan::where('golongan', 'Logistik Redeem Eksekutif IFCS')->where('tahun', $tahun)->sum('merak');
    
        // Ambil data untuk kolom 'bakauheni'
        $bakauheni1 = market_lintasan::where('golongan', 'Eksekutif IFCS')->where('tahun', $tahun)->sum('bakauheni');
        $bakauheni2 = market_lintasan::where('golongan', 'Logistik Eksekutif IFCS')->where('tahun', $tahun)->sum('bakauheni');
        $bakauheni3 = market_lintasan::where('golongan', 'Redeem Eksekutif IFCS')->where('tahun', $tahun)->sum('bakauheni');
        $bakauheni4 = market_lintasan::where('golongan', 'Logistik Redeem Eksekutif IFCS')->where('tahun', $tahun)->sum('bakauheni');
    
        // Ambil data untuk kolom 'gabungan'
        $gabungan1 = market_lintasan::where('golongan', 'Eksekutif IFCS')->where('tahun', $tahun)->sum('gabungan');
        $gabungan2 = market_lintasan::where('golongan', 'Logistik Eksekutif IFCS')->where('tahun', $tahun)->sum('gabungan');
        $gabungan3 = market_lintasan::where('golongan', 'Redeem Eksekutif IFCS')->where('tahun', $tahun)->sum('gabungan');
        $gabungan4 = market_lintasan::where('golongan', 'Logistik Redeem Eksekutif IFCS')->where('tahun', $tahun)->sum('gabungan');
    
        // Hitung total untuk masing-masing kolom
        $totalMerak = $merak1 + $merak2 + $merak3 + $merak4;
        $totalBakauheni = $bakauheni1 + $bakauheni2 + $bakauheni3 + $bakauheni4;
        $totalGabungan = $gabungan1 + $gabungan2 + $gabungan3 + $gabungan4;

    
        // Simpan ke tabel market_lintasan
        market_lintasan::updateOrCreate(
            ['golongan' => 'Total', 'jenis' => 'ifcs', 'tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }    
 
    //INDUSTRI
    public function simpanDataBusReguler($tahun)
    {
        // Ambil data dari pelabuhan_merak
        $merakVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $merakVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');

        // Ambil data dari pelabuhan_bakauheni
        $bakauheniVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');

        // Hitung total untuk masing-masing pelabuhan
        $totalMerak = $merakVA + $merakVIA;
        $totalBakauheni = $bakauheniVA + $bakauheniVIA;

        // Hitung total gabungan
        $totalGabungan = $totalMerak + $totalBakauheni;


        // Simpan ke tabel market_lintasan
        market_lintasan::updateOrCreate(
            ['golongan' => 'Kendaraan Bus Reguler', 'jenis' => 'industri','tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }

    public function simpanDataLogistikReguler($tahun)
    {
        // Ambil data dari pelabuhan_merak
        $merakIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $merakVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $merakVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $merakVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $merakVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $merakIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');

        // Ambil data dari pelabuhan_bakauheni
        $bakauheniIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');

        // Hitung total untuk masing-masing pelabuhan
        $totalMerak = $merakIVB + $merakVB + $merakVIB + $merakVII + $merakVIII + $merakIX;
        $totalBakauheni = $bakauheniIVB + $bakauheniVB + $bakauheniVIB + $bakauheniVII + $bakauheniVIII + $bakauheniIX;

        // Hitung total gabungan
        $totalGabungan = $totalMerak + $totalBakauheni;


        // Simpan ke tabel market_lintasan
        market_lintasan::updateOrCreate(
            ['golongan' => 'Logistik Reguler','jenis' => 'industri','tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }

    public function simpanDataEksekkutifNonIFCS($tahun)
    {
        // Ambil data dari pelabuhan_merak
        $merakIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $merakVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $merakVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $merakVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $merakVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $merakIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Ambil data dari pelabuhan_bakauheni
        $bakauheniIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total untuk masing-masing pelabuhan
        $totalMerak = $merakIVB + $merakVB + $merakVIB + $merakVII + $merakVIII + $merakIX;
        $totalBakauheni = $bakauheniIVB + $bakauheniVB + $bakauheniVIB + $bakauheniVII + $bakauheniVIII + $bakauheniIX;

        // Hitung total gabungan
        $totalGabungan = $totalMerak + $totalBakauheni;


        // Simpan ke tabel market_lintasan
        market_lintasan::updateOrCreate(
            ['golongan' => 'Logistik Eksekutif Non IFCS','jenis' => 'industri','tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }
    
    public function simpanDataEksekutifNonIFCS($tahun)
    {
        // Ambil data dari pelabuhan_merak
        $merakVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $merakVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Ambil data dari pelabuhan_bakauheni
        $bakauheniVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total untuk masing-masing pelabuhan
        $totalMerak = $merakVA + $merakVIA;
        $totalBakauheni = $bakauheniVA + $bakauheniVIA;

        // Hitung total gabungan
        $totalGabungan = $totalMerak + $totalBakauheni;

        
        // Simpan ke tabel market_lintasan
        market_lintasan::updateOrCreate(
            ['golongan' => 'Eksekutif Non IFCS', 'jenis' => 'industri','tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }

    public function simpanDataTotalINDUSTRI($tahun)
    {
        // Ambil data untuk kolom 'merak'
        $merak1 = market_lintasan::where('golongan', 'Kendaraan Bus Reguler')->where('tahun', $tahun)->sum('merak');
        $merak2 = market_lintasan::where('golongan', 'Logistik Reguler')->where('tahun', $tahun)->sum('merak');
        $merak3 = market_lintasan::where('golongan', 'Logistik Eksekutif Non IFCS')->where('tahun', $tahun)->sum('merak');
        $merak4 = market_lintasan::where('golongan', 'Eksekutif Non IFCS')->where('tahun', $tahun)->sum('merak');
    
        // Ambil data untuk kolom 'bakauheni'
        $bakauheni1 = market_lintasan::where('golongan', 'Kendaraan Bus Reguler')->where('tahun', $tahun)->sum('bakauheni');
        $bakauheni2 = market_lintasan::where('golongan', 'Logistik Reguler')->where('tahun', $tahun)->sum('bakauheni');
        $bakauheni3 = market_lintasan::where('golongan', 'Logistik Eksekutif Non IFCS')->where('tahun', $tahun)->sum('bakauheni');
        $bakauheni4 = market_lintasan::where('golongan', 'Eksekutif Non IFCS')->where('tahun', $tahun)->sum('bakauheni');
    
        // Ambil data untuk kolom 'gabungan'
        $gabungan1 = market_lintasan::where('golongan', 'Kendaraan Bus Reguler')->where('tahun', $tahun)->sum('gabungan');
        $gabungan2 = market_lintasan::where('golongan', 'Logistik Reguler')->where('tahun', $tahun)->sum('gabungan');
        $gabungan3 = market_lintasan::where('golongan', 'Logistik Eksekutif Non IFCS')->where('tahun', $tahun)->sum('gabungan');
        $gabungan4 = market_lintasan::where('golongan', 'Eksekutif Non IFCS')->where('tahun', $tahun)->sum('gabungan');
    
        // Hitung total untuk masing-masing kolom
        $totalMerak = $merak1 + $merak2 + $merak3 + $merak4;
        $totalBakauheni = $bakauheni1 + $bakauheni2 + $bakauheni3 + $bakauheni4;
        $totalGabungan = $gabungan1 + $gabungan2 + $gabungan3 + $gabungan4;

    
        // Simpan ke tabel market_lintasan
        market_lintasan::updateOrCreate(
            ['golongan' => 'Total', 'jenis' => 'industri', 'tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }  
}