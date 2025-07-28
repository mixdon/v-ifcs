<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\pelabuhan_merak;
use App\Models\pelabuhan_bakauheni;
use App\Models\komposisi_segmen;
use Illuminate\Database\QueryException;

class komposisiSegmenController extends Controller
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
            $komposisi_segmen = komposisi_segmen::whereIn('tahun', $yearsToFetch)->get();

            return view('ifcs.komposisi-segmen', [
                'komposisi_segmen' => $komposisi_segmen,
                'years' => $validYears,
                'selectedYear' => $tahun 
            ]);
        } catch (QueryException $e) {
            return redirect()->route('komposisi.index')->with('error', 'Terjadi error database: ' . $e->getMessage());
        }
    }

    // Fungsi untuk memicu semua perhitungan dan menyimpan hasilnya
    public function runCalculationsForYear($tahun)
    {
        try {
            // Merak
            $this->simpanDataMerakIVA($tahun);
            $this->simpanDataMerakIVB($tahun);
            $this->simpanDataMerakVA($tahun);
            $this->simpanDataMerakVB($tahun);
            $this->simpanDataMerakVIA($tahun);
            $this->simpanDataMerakVIB($tahun);
            $this->simpanDataMerakVII($tahun);
            $this->simpanDataMerakVIII($tahun);
            $this->simpanDataMerakIX($tahun);
            $this->simpanDataTotalMerak($tahun);

            // Bakauheni
            $this->simpanDataBakauheniIVA($tahun);
            $this->simpanDataBakauheniIVB($tahun);
            $this->simpanDataBakauheniVA($tahun);
            $this->simpanDataBakauheniVB($tahun);
            $this->simpanDataBakauheniVIA($tahun);
            $this->simpanDataBakauheniVIB($tahun);
            $this->simpanDataBakauheniVII($tahun);
            $this->simpanDataBakauheniVIII($tahun);
            $this->simpanDataBakauheniIX($tahun);
            $this->simpanDataTotalBakauheni($tahun);

            // Gabungan
            $this->simpanDataGabunganIVA($tahun);
            $this->simpanDataGabunganIVB($tahun);
            $this->simpanDataGabunganVA($tahun);
            $this->simpanDataGabunganVB($tahun);
            $this->simpanDataGabunganVIA($tahun);
            $this->simpanDataGabunganVIB($tahun);
            $this->simpanDataGabunganVII($tahun);
            $this->simpanDataGabunganVIII($tahun);
            $this->simpanDataGabunganIX($tahun);
            $this->simpanDataTotalGabungan($tahun);

            return redirect()->route('komposisi.index', ['tahun' => $tahun])
                             ->with('success', "Perhitungan data Komposisi Segmen untuk tahun {$tahun} berhasil.");
        } catch (QueryException $e) {
            return redirect()->route('komposisi.index', ['tahun' => $tahun])
                             ->with('error', "Terjadi error database saat perhitungan: " . $e->getMessage());
        }
    }
    
    // Merak
    public function simpanDataMerakIVA($tahun)
    {
        $ifcsIVA = pelabuhan_merak::where('golongan', 'IVA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIVA = pelabuhan_merak::where('golongan', 'IVA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIVA = $ifcsIVA + $redeemIVA;
        $nonifcsIVA = pelabuhan_merak::where('golongan', 'IVA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemIVA + $nonifcsIVA;

        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'IVA', 'jenis' => 'merak','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemIVA, 'nonifcs' => $nonifcsIVA, 'total' => $total]
        );
    }
    public function simpanDataMerakIVB($tahun)
    {
        $ifcsIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIVB = $ifcsIVB + $redeemIVB;
        $nonifcsIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemIVB + $nonifcsIVB;
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'IVB', 'jenis' => 'merak','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemIVB, 'nonifcs' => $nonifcsIVB, 'total' => $total]
        );
    }
    public function simpanDataMerakVA($tahun)
    {
        $ifcsVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVA = $ifcsVA + $redeemVA;
        $nonifcsVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVA + $nonifcsVA;
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VA', 'jenis' => 'merak','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVA, 'nonifcs' => $nonifcsVA, 'total' => $total]
        );
    }
    public function simpanDataMerakVB($tahun)
    {
        $ifcsVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVB = $ifcsVB + $redeemVB;
        $nonifcsVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVB + $nonifcsVB;
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VB', 'jenis' => 'merak','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVB, 'nonifcs' => $nonifcsVB, 'total' => $total]
        );
    }
    public function simpanDataMerakVIA($tahun)
    {
        $ifcsVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIA = $ifcsVIA + $redeemVIA;
        $nonifcsVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVIA + $nonifcsVIA;
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VIA', 'jenis' => 'merak','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVIA, 'nonifcs' => $nonifcsVIA, 'total' => $total]
        );
    }
    public function simpanDataMerakVIB($tahun)
    {
        $ifcsVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIB = $ifcsVIB + $redeemVIB;
        $nonifcsVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVIB + $nonifcsVIB;
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VIB', 'jenis' => 'merak','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVIB, 'nonifcs' => $nonifcsVIB, 'total' => $total]
        );
    }
    public function simpanDataMerakVII($tahun)
    {
        $ifcsVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVII = $ifcsVII + $redeemVII;
        $nonifcsVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVII + $nonifcsVII;
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VII', 'jenis' => 'merak','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVII, 'nonifcs' => $nonifcsVII, 'total' => $total]
        );
    }
    public function simpanDataMerakVIII($tahun)
    {
        $ifcsVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIII = $ifcsVIII + $redeemVIII;
        $nonifcsVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVIII + $nonifcsVIII;
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VIII', 'jenis' => 'merak','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVIII, 'nonifcs' => $nonifcsVIII, 'total' => $total]
        );
    }
    public function simpanDataMerakIX($tahun)
    {
        $ifcsIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIX = $ifcsIX + $redeemIX;
        $nonifcsIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemIX + $nonifcsIX;
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'IX', 'jenis' => 'merak','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemIX, 'nonifcs' => $nonifcsIX, 'total' => $total]
        );
    }
    public function simpanDataTotalMerak($tahun)
    {
        $golongans = ['IVA', 'IVB', 'VA', 'VB', 'VIA', 'VIB', 'VII', 'VIII', 'IX'];
        $totalifcs = komposisi_segmen::whereIn('golongan', $golongans)->where('jenis', 'merak')->where('tahun', $tahun)->sum('ifcs_redeem');
        $totalnonifcs = komposisi_segmen::whereIn('golongan', $golongans)->where('jenis', 'merak')->where('tahun', $tahun)->sum('nonifcs');
        $total = $totalifcs + $totalnonifcs ;
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'Total', 'jenis' => 'merak','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalifcs, 'nonifcs' => $totalnonifcs, 'total' => $total]
        );
    }  
    
    // Bakauheni
    public function simpanDataBakauheniIVA($tahun)
    {
        $ifcsIVA = pelabuhan_bakauheni::where('golongan', 'IVA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIVA = pelabuhan_bakauheni::where('golongan', 'IVA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIVA = $ifcsIVA + $redeemIVA;
        $nonifcsIVA = pelabuhan_bakauheni::where('golongan', 'IVA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemIVA + $nonifcsIVA;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IVA', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemIVA, 'nonifcs' => $nonifcsIVA, 'total' => $total]
        );
    }
    public function simpanDataBakauheniIVB($tahun)
    {
        $ifcsIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIVB = $ifcsIVB + $redeemIVB;
        $nonifcsIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemIVB + $nonifcsIVB;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IVB', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemIVB, 'nonifcs' => $nonifcsIVB, 'total' => $total]
        );
    }
    public function simpanDataBakauheniVA($tahun)
    {
        $ifcsVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVA = $ifcsVA + $redeemVA;
        $nonifcsVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVA + $nonifcsVA;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VA', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVA, 'nonifcs' => $nonifcsVA, 'total' => $total]
        );
    }
    public function simpanDataBakauheniVB($tahun)
    {
        $ifcsVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVB = $ifcsVB + $redeemVB;
        $nonifcsVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVB + $nonifcsVB;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VB', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVB, 'nonifcs' => $nonifcsVB, 'total' => $total]
        );
    }
    public function simpanDataBakauheniVIA($tahun)
    {
        $ifcsVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIA = $ifcsVIA + $redeemVIA;
        $nonifcsVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVIA + $nonifcsVIA;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIA', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVIA, 'nonifcs' => $nonifcsVIA, 'total' => $total]
        );
    }
    public function simpanDataBakauheniVIB($tahun)
    {
        $ifcsVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIB = $ifcsVIB + $redeemVIB;
        $nonifcsVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVIB + $nonifcsVIB;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIB', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVIB, 'nonifcs' => $nonifcsVIB, 'total' => $total]
        );
    }
    public function simpanDataBakauheniVII($tahun)
    {
        $ifcsVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVII = $ifcsVII + $redeemVII;
        $nonifcsVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVII + $nonifcsVII;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VII', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVII, 'nonifcs' => $nonifcsVII, 'total' => $total]
        );
    }
    public function simpanDataBakauheniVIII($tahun)
    {
        $ifcsVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIII = $ifcsVIII + $redeemVIII;
        $nonifcsVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVIII + $nonifcsVIII;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIII', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVIII, 'nonifcs' => $nonifcsVIII, 'total' => $total]
        );
    }
    public function simpanDataBakauheniIX($tahun)
    {
        $ifcsIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIX = $ifcsIX + $redeemIX;
        $nonifcsIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemIX + $nonifcsIX;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IX', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemIX, 'nonifcs' => $nonifcsIX, 'total' => $total]
        );
    }
    public function simpanDataTotalBakauheni($tahun)
    {
        $golongans = ['IVA', 'IVB', 'VA', 'VB', 'VIA', 'VIB', 'VII', 'VIII', 'IX'];
        $totalifcs = komposisi_segmen::whereIn('golongan', $golongans)->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_redeem');
        $totalnonifcs = komposisi_segmen::whereIn('golongan', $golongans)->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $total = $totalifcs + $totalnonifcs ;
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'Total', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalifcs, 'nonifcs' => $totalnonifcs, 'total' => $total]
        );
    }  

    // Gabungan
    public function simpanDataGabunganIVA($tahun)
    {
        $IFCSmerakIVA = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSbakauheniIVA = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSgabunganIVA = $IFCSmerakIVA + $IFCSbakauheniIVA;

        $NONIFCSmerakIVA = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSbakauheniIVA = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSgabunganIVA = $NONIFCSmerakIVA + $NONIFCSbakauheniIVA;
        
        $total = $IFCSgabunganIVA + $NONIFCSgabunganIVA;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IVA', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganIVA, 'nonifcs' => $NONIFCSgabunganIVA, 'total' => $total]
        );
    }    
    public function simpanDataGabunganIVB($tahun)
    {
        $IFCSmerakIVB = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSbakauheniIVB = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSgabunganIVB = $IFCSmerakIVB + $IFCSbakauheniIVB;

        $NONIFCSmerakIVB = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSbakauheniIVB = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSgabunganIVB = $NONIFCSmerakIVB + $NONIFCSbakauheniIVB;
        
        $total = $IFCSgabunganIVB + $NONIFCSgabunganIVB;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IVB', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganIVB, 'nonifcs' => $NONIFCSgabunganIVB, 'total' => $total]
        );
    }
    public function simpanDataGabunganVA($tahun)
    {
        $IFCSmerakVA = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSbakauheniVA = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSgabunganVA = $IFCSmerakVA + $IFCSbakauheniVA;

        $NONIFCSmerakVA = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSbakauheniVA = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSgabunganVA = $NONIFCSmerakVA + $NONIFCSbakauheniVA;
        
        $total = $IFCSgabunganVA + $NONIFCSgabunganVA;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VA', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganVA, 'nonifcs' => $NONIFCSgabunganVA, 'total' => $total]
        );
    }
    public function simpanDataGabunganVB($tahun)
    {
        $IFCSmerakVB = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSbakauheniVB = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSgabunganVB = $IFCSmerakVB + $IFCSbakauheniVB;

        $NONIFCSmerakVB = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSbakauheniVB = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSgabunganVB = $NONIFCSmerakVB + $NONIFCSbakauheniVB;
        
        $total = $IFCSgabunganVB + $NONIFCSgabunganVB;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VB', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganVB, 'nonifcs' => $NONIFCSgabunganVB, 'total' => $total]
        );
    }
    public function simpanDataGabunganVIA($tahun)
    {
        $IFCSmerakVIA = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSbakauheniVIA = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSgabunganVIA = $IFCSmerakVIA + $IFCSbakauheniVIA;

        $NONIFCSmerakVIA = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSbakauheniVIA = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSgabunganVIA = $NONIFCSmerakVIA + $NONIFCSbakauheniVIA;
        
        $total = $IFCSgabunganVIA + $NONIFCSgabunganVIA;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIA', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganVIA, 'nonifcs' => $NONIFCSgabunganVIA, 'total' => $total]
        );
    }
    public function simpanDataGabunganVIB($tahun)
    {
        $IFCSmerakVIB = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSbakauheniVIB = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSgabunganVIB = $IFCSmerakVIB + $IFCSbakauheniVIB;

        $NONIFCSmerakVIB = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSbakauheniVIB = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSgabunganVIB = $NONIFCSmerakVIB + $NONIFCSbakauheniVIB;
        
        $total = $IFCSgabunganVIB + $NONIFCSgabunganVIB;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIB', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganVIB, 'nonifcs' => $NONIFCSgabunganVIB, 'total' => $total]
        );
    }
    public function simpanDataGabunganVII($tahun)
    {
        $IFCSmerakVII = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSbakauheniVII = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSgabunganVII = $IFCSmerakVII + $IFCSbakauheniVII;

        $NONIFCSmerakVII = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSbakauheniVII = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSgabunganVII = $NONIFCSmerakVII + $NONIFCSbakauheniVII;
        
        $total = $IFCSgabunganVII + $NONIFCSgabunganVII;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VII', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganVII, 'nonifcs' => $NONIFCSgabunganVII, 'total' => $total]
        );
    }
    public function simpanDataGabunganVIII($tahun)
    {
        $IFCSmerakVIII = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSbakauheniVIII = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSgabunganVIII = $IFCSmerakVIII + $IFCSbakauheniVIII;

        $NONIFCSmerakVIII = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSbakauheniVIII = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSgabunganVIII = $NONIFCSmerakVIII + $NONIFCSbakauheniVIII;
        
        $total = $IFCSgabunganVIII + $NONIFCSgabunganVIII;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIII', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganVIII, 'nonifcs' => $NONIFCSgabunganVIII, 'total' => $total]
        );
    }
    public function simpanDataGabunganIX($tahun)
    {
        $IFCSmerakIX = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSbakauheniIX = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem') ?? 0;
        $IFCSgabunganIX = $IFCSmerakIX + $IFCSbakauheniIX;

        $NONIFCSmerakIX = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSbakauheniIX = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs') ?? 0;
        $NONIFCSgabunganIX = $NONIFCSmerakIX + $NONIFCSbakauheniIX;
        
        $total = $IFCSgabunganIX + $NONIFCSgabunganIX;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IX', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganIX, 'nonifcs' => $NONIFCSgabunganIX, 'total' => $total]
        );
    }
    public function simpanDataTotalGabungan($tahun)
    {
        $golongans = ['IVA', 'IVB', 'VA', 'VB', 'VIA', 'VIB', 'VII', 'VIII', 'IX'];
        $totalifcs = komposisi_segmen::whereIn('golongan', $golongans)->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_redeem');
        $totalnonifcs = komposisi_segmen::whereIn('golongan', $golongans)->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $total = $totalifcs + $totalnonifcs ;
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'Total', 'jenis' => 'gabungan','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalifcs, 'nonifcs' => $totalnonifcs, 'total' => $total]
        );
    }  
}