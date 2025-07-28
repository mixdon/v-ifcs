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
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $startYear = 2020;
        $validYears = range($startYear, $currentYear);
        $tahun = $request->input('tahun', null);

        $years = $tahun && in_array($tahun, $validYears) ? [$tahun] : $validYears;

        try {
            foreach ($years as $year) {
                // Merak
                $this->simpanDataMerakIVA($year);
                $this->simpanDataMerakIVB($year);
                $this->simpanDataMerakVA($year);
                $this->simpanDataMerakVB($year);
                $this->simpanDataMerakVIA($year);
                $this->simpanDataMerakVIB($year);
                $this->simpanDataMerakVII($year);
                $this->simpanDataMerakVIII($year);
                $this->simpanDataMerakIX($year);
                $this->simpanDataTotalMerak($year);

                // Bakauheni
                $this->simpanDataBakauheniIVA($year);
                $this->simpanDataBakauheniIVB($year);
                $this->simpanDataBakauheniVA($year);
                $this->simpanDataBakauheniVB($year);
                $this->simpanDataBakauheniVIA($year);
                $this->simpanDataBakauheniVIB($year);
                $this->simpanDataBakauheniVII($year);
                $this->simpanDataBakauheniVIII($year);
                $this->simpanDataBakauheniIX($year);
                $this->simpanDataTotalBakauheni($year);

                // Gabungan
                $this->simpanDataGabunganIVA($year);
                $this->simpanDataGabunganIVB($year);
                $this->simpanDataGabunganVA($year);
                $this->simpanDataGabunganVB($year);
                $this->simpanDataGabunganVIA($year);
                $this->simpanDataGabunganVIB($year);
                $this->simpanDataGabunganVII($year);
                $this->simpanDataGabunganVIII($year);
                $this->simpanDataGabunganIX($year);
                $this->simpanDataTotalGabungan($year);
            }
        } catch (QueryException $e) {
            return redirect()->route('komposisi.index')->with('error', 'Terjadi error database: ' . $e->getMessage());
        }

        $komposisi_segmen = komposisi_segmen::whereIn('tahun', $years)->get();

        return view('ifcs.komposisi-segmen', [
            'komposisi_segmen' => $komposisi_segmen,
            'years' => $validYears,
            'selectedYear' => $tahun 
        ]);
    }

    //Merak
    public function simpanDataMerakIVA($tahun)
    {
        $ifcsIVA = pelabuhan_merak::where('golongan', 'IVA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIVA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
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
        $redeem1 = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'merak')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem2 = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'merak')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem3 = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'merak')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem4 = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'merak')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem5 = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'merak')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem6 = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'merak')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem7 = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'merak')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem8 = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'merak')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem9 = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'merak')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $nonifcs1 = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'merak')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs2 = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'merak')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs3 = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'merak')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs4 = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'merak')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs5 = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'merak')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs6 = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'merak')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs7 = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'merak')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs8 = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'merak')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs9 = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'merak')->where('tahun', $tahun)->sum('nonifcs');
        $totalifcs = $redeem1 + $redeem2 + $redeem3 + $redeem4 + $redeem5 + $redeem6 + $redeem7 + $redeem8 + $redeem9;
        $totalnonifcs = $nonifcs1 + $nonifcs2 + $nonifcs3 + $nonifcs4 + $nonifcs5 + $nonifcs6 + $nonifcs7 + $nonifcs8 + $nonifcs9;
        $total = $totalifcs + $totalnonifcs ;
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'Total', 'jenis' => 'merak','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalifcs, 'nonifcs' => $totalnonifcs, 'total' => $total]
        );
    }  
    public function simpanDataBakauheniIVA($tahun)
    {
        $ifcsIVA = pelabuhan_bakauheni::where('golongan', 'IVA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIVA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
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
        $redeem1 = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem2 = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem3 = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem4 = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem5 = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem6 = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem7 = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem8 = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem9 = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $nonifcs1 = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs2 = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs3 = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs4 = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs5 = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs6 = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs7 = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs8 = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs9 = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $totalifcs = $redeem1 + $redeem2 + $redeem3 + $redeem4 + $redeem5 + $redeem6 + $redeem7 + $redeem8 + $redeem9;
        $totalnonifcs = $nonifcs1 + $nonifcs2 + $nonifcs3 + $nonifcs4 + $nonifcs5 + $nonifcs6 + $nonifcs7 + $nonifcs8 + $nonifcs9;
        $total = $totalifcs + $totalnonifcs ;
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'Total', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalifcs, 'nonifcs' => $totalnonifcs, 'total' => $total]
        );
    }  
    public function simpanDataGabunganIVA($tahun)
    {
        $IFCSmerakIVA = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'IVA')->where('jenis', 'merak')->where('tahun', $tahun)->first();
        $IFCSbakauheniIVA = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'IVA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first();
        $IFCSgabunganIVA = ($IFCSmerakIVA ? $IFCSmerakIVA->ifcs_redeem : 0) + 
                           ($IFCSbakauheniIVA ? $IFCSbakauheniIVA->ifcs_redeem : 0);
        $NONIFCSmerakIVA = komposisi_segmen::select('nonifcs')->where('golongan', 'IVA')->where('jenis', 'merak')->where('tahun', $tahun)->first();
        $NONIFCSbakauheniIVA = komposisi_segmen::select('nonifcs')->where('golongan', 'IVA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first();
        $NONIFCSgabunganIVA = ($NONIFCSmerakIVA ? $NONIFCSmerakIVA->nonifcs : 0) + 
                              ($NONIFCSbakauheniIVA ? $NONIFCSbakauheniIVA->nonifcs : 0);
        $total = $IFCSgabunganIVA + $NONIFCSgabunganIVA;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IVA', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganIVA, 'nonifcs' => $NONIFCSgabunganIVA, 'total' => $total]
        );
    }    
    public function simpanDataGabunganIVB($tahun)
    {
        $IFCSmerakIVB = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'IVB')->where('jenis', 'merak')->where('tahun', $tahun)->first(); 
        $IFCSbakauheniIVB = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'IVB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first();
        $IFCSgabunganIVB = ($IFCSmerakIVB ? $IFCSmerakIVB->ifcs_redeem : 0) + 
                           ($IFCSbakauheniIVB ? $IFCSbakauheniIVB->ifcs_redeem : 0);
        $NONIFCSmerakIVB = komposisi_segmen::select('nonifcs')->where('golongan', 'IVB')->where('jenis', 'merak')->where('tahun', $tahun)->first();  
        $NONIFCSbakauheniIVB = komposisi_segmen::select('nonifcs')->where('golongan', 'IVB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first(); 
        $NONIFCSgabunganIVB = ($NONIFCSmerakIVB ? $NONIFCSmerakIVB->nonifcs : 0) + 
                              ($NONIFCSbakauheniIVB ? $NONIFCSbakauheniIVB->nonifcs : 0);
        $total = $IFCSgabunganIVB + $NONIFCSgabunganIVB;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IVB', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganIVB, 'nonifcs' => $NONIFCSgabunganIVB, 'total' => $total]
        );
    }
    public function simpanDataGabunganVA($tahun)
    {
        $IFCSmerakVA = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'VA')->where('jenis', 'merak')->where('tahun', $tahun)->first(); 
        $IFCSbakauheniVA = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'VA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first();
        $IFCSgabunganVA = ($IFCSmerakVA ? $IFCSmerakVA->ifcs_redeem : 0) + 
                           ($IFCSbakauheniVA ? $IFCSbakauheniVA->ifcs_redeem : 0);
        $NONIFCSmerakVA = komposisi_segmen::select('nonifcs')->where('golongan', 'VA')->where('jenis', 'merak')->where('tahun', $tahun)->first();  
        $NONIFCSbakauheniVA = komposisi_segmen::select('nonifcs')->where('golongan', 'VA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first(); 
        $NONIFCSgabunganVA = ($NONIFCSmerakVA ? $NONIFCSmerakVA->nonifcs : 0) + 
                              ($NONIFCSbakauheniVA ? $NONIFCSbakauheniVA->nonifcs : 0);
        $total = $IFCSgabunganVA + $NONIFCSgabunganVA;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VA', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganVA, 'nonifcs' => $NONIFCSgabunganVA, 'total' => $total]
        );
    }
    public function simpanDataGabunganVB($tahun)
    {
        $IFCSmerakVB = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'VB')->where('jenis', 'merak')->where('tahun', $tahun)->first(); 
        $IFCSbakauheniVB = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'VB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first();
        $IFCSgabunganVB = ($IFCSmerakVB ? $IFCSmerakVB->ifcs_redeem : 0) + 
                           ($IFCSbakauheniVB ? $IFCSbakauheniVB->ifcs_redeem : 0);
        $NONIFCSmerakVB = komposisi_segmen::select('nonifcs')->where('golongan', 'VB')->where('jenis', 'merak')->where('tahun', $tahun)->first();  
        $NONIFCSbakauheniVB = komposisi_segmen::select('nonifcs')->where('golongan', 'VB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first(); 
        $NONIFCSgabunganVB = ($NONIFCSmerakVB ? $NONIFCSmerakVB->nonifcs : 0) + 
                              ($NONIFCSbakauheniVB ? $NONIFCSbakauheniVB->nonifcs : 0);
        $total = $IFCSgabunganVB + $NONIFCSgabunganVB;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VB', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganVB, 'nonifcs' => $NONIFCSgabunganVB, 'total' => $total]
        );
    }
    public function simpanDataGabunganVIA($tahun)
    {
        $IFCSmerakVIA = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'VIA')->where('jenis', 'merak')->where('tahun', $tahun)->first(); 
        $IFCSbakauheniVIA = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'VIA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first();
        $IFCSgabunganVIA = ($IFCSmerakVIA ? $IFCSmerakVIA->ifcs_redeem : 0) + 
                           ($IFCSbakauheniVIA ? $IFCSbakauheniVIA->ifcs_redeem : 0);
        $NONIFCSmerakVIA = komposisi_segmen::select('nonifcs')->where('golongan', 'VIA')->where('jenis', 'merak')->where('tahun', $tahun)->first();  
        $NONIFCSbakauheniVIA = komposisi_segmen::select('nonifcs')->where('golongan', 'VIA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first(); 
        $NONIFCSgabunganVIA = ($NONIFCSmerakVIA ? $NONIFCSmerakVIA->nonifcs : 0) + 
                              ($NONIFCSbakauheniVIA ? $NONIFCSbakauheniVIA->nonifcs : 0);
        $total = $IFCSgabunganVIA + $NONIFCSgabunganVIA;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIA', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganVIA, 'nonifcs' => $NONIFCSgabunganVIA, 'total' => $total]
        );
    }
    public function simpanDataGabunganVIB($tahun)
    {
        $IFCSmerakVIB = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'VIB')->where('jenis', 'merak')->where('tahun', $tahun)->first(); 
        $IFCSbakauheniVIB = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'VIB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first();
        $IFCSgabunganVIB = ($IFCSmerakVIB ? $IFCSmerakVIB->ifcs_redeem : 0) + 
                           ($IFCSbakauheniVIB ? $IFCSbakauheniVIB->ifcs_redeem : 0);
        $NONIFCSmerakVIB = komposisi_segmen::select('nonifcs')->where('golongan', 'VIB')->where('jenis', 'merak')->where('tahun', $tahun)->first();  
        $NONIFCSbakauheniVIB = komposisi_segmen::select('nonifcs')->where('golongan', 'VIB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first(); 
        $NONIFCSgabunganVIB = ($NONIFCSmerakVIB ? $NONIFCSmerakVIB->nonifcs : 0) + 
                              ($NONIFCSbakauheniVIB ? $NONIFCSbakauheniVIB->nonifcs : 0);
        $total = $IFCSgabunganVIB + $NONIFCSgabunganVIB;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIB', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganVIB, 'nonifcs' => $NONIFCSgabunganVIB, 'total' => $total]
        );
    }
    public function simpanDataGabunganVII($tahun)
    {
        $IFCSmerakVII = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'VII')->where('jenis', 'merak')->where('tahun', $tahun)->first(); 
        $IFCSbakauheniVII = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'VII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first();
        $IFCSgabunganVII = ($IFCSmerakVII ? $IFCSmerakVII->ifcs_redeem : 0) + 
                           ($IFCSbakauheniVII ? $IFCSbakauheniVII->ifcs_redeem : 0);
        $NONIFCSmerakVII = komposisi_segmen::select('nonifcs')->where('golongan', 'VII')->where('jenis', 'merak')->where('tahun', $tahun)->first();  
        $NONIFCSbakauheniVII = komposisi_segmen::select('nonifcs')->where('golongan', 'VII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first(); 
        $NONIFCSgabunganVII = ($NONIFCSmerakVII ? $NONIFCSmerakVII->nonifcs : 0) + 
                              ($NONIFCSbakauheniVII ? $NONIFCSbakauheniVII->nonifcs : 0);
        $total = $IFCSgabunganVII + $NONIFCSgabunganVII;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VII', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganVII, 'nonifcs' => $NONIFCSgabunganVII, 'total' => $total]
        );
    }
    public function simpanDataGabunganVIII($tahun)
    {
        $IFCSmerakVIII = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'VIII')->where('jenis', 'merak')->where('tahun', $tahun)->first(); 
        $IFCSbakauheniVIII = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'VIII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first();
        $IFCSgabunganVIII = ($IFCSmerakVIII ? $IFCSmerakVIII->ifcs_redeem : 0) + 
                           ($IFCSbakauheniVIII ? $IFCSbakauheniVIII->ifcs_redeem : 0);
        $NONIFCSmerakVIII = komposisi_segmen::select('nonifcs')->where('golongan', 'VIII')->where('jenis', 'merak')->where('tahun', $tahun)->first();  
        $NONIFCSbakauheniVIII = komposisi_segmen::select('nonifcs')->where('golongan', 'VIII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first(); 
        $NONIFCSgabunganVIII = ($NONIFCSmerakVIII ? $NONIFCSmerakVIII->nonifcs : 0) + 
                              ($NONIFCSbakauheniVIII ? $NONIFCSbakauheniVIII->nonifcs : 0);
        $total = $IFCSgabunganVIII + $NONIFCSgabunganVIII;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIII', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganVIII, 'nonifcs' => $NONIFCSgabunganVIII, 'total' => $total]
        );
    }
    public function simpanDataGabunganIX($tahun)
    {
        $IFCSmerakIX = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'IX')->where('jenis', 'merak')->where('tahun', $tahun)->first(); 
        $IFCSbakauheniIX = komposisi_segmen::select('ifcs_redeem')->where('golongan', 'IX')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first();
        $IFCSgabunganIX = ($IFCSmerakIX ? $IFCSmerakIX->ifcs_redeem : 0) + 
                           ($IFCSbakauheniIX ? $IFCSbakauheniIX->ifcs_redeem : 0);
        $NONIFCSmerakIX = komposisi_segmen::select('nonifcs')->where('golongan', 'IX')->where('jenis', 'merak')->where('tahun', $tahun)->first();  
        $NONIFCSbakauheniIX = komposisi_segmen::select('nonifcs')->where('golongan', 'IX')->where('jenis', 'bakauheni')->where('tahun', $tahun)->first(); 
        $NONIFCSgabunganIX = ($NONIFCSmerakIX ? $NONIFCSmerakIX->nonifcs : 0) + 
                              ($NONIFCSbakauheniIX ? $NONIFCSbakauheniIX->nonifcs : 0);
        $total = $IFCSgabunganIX + $NONIFCSgabunganIX;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IX', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [ 'ifcs_redeem' => $IFCSgabunganIX, 'nonifcs' => $NONIFCSgabunganIX, 'total' => $total]
        );
    }
    public function simpanDataTotalGabungan($tahun)
    {
        $redeem1 = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem2 = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem3 = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem4 = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem5 = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem6 = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem7 = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem8 = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $redeem9 = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_Redeem');
        $nonifcs1 = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs2 = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs3 = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs4 = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs5 = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs6 = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs7 = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs8 = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs9 = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $totalifcs = $redeem1 + $redeem2 + $redeem3 + $redeem4 + $redeem5 + $redeem6 + $redeem7 + $redeem8 + $redeem9;
        $totalnonifcs = $nonifcs1 + $nonifcs2 + $nonifcs3 + $nonifcs4 + $nonifcs5 + $nonifcs6 + $nonifcs7 + $nonifcs8 + $nonifcs9;
        $total = $totalifcs + $totalnonifcs ;
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'Total', 'jenis' => 'gabungan','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalifcs, 'nonifcs' => $totalnonifcs, 'total' => $total]
        );
    }  
}