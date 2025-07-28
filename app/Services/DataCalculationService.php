<?php

namespace App\Services;

use App\Models\pelabuhan_merak;
use App\Models\pelabuhan_bakauheni;
use App\Models\market_lintasan;
use App\Models\komposisi_segmen;
use Illuminate\Support\Facades\Log; // Import kelas Log

class DataCalculationService
{
    /**
     * Memulai semua perhitungan data untuk tahun tertentu.
     *
     * @param int $tahun Tahun data yang akan dihitung.
     * @return void
     */
    public function calculateAllForYear($tahun)
    {
        Log::info("Memulai DataCalculationService untuk tahun: {$tahun}"); // Log awal proses
        
        try {
            $this->calculateKomposisiSegmen($tahun);
            Log::info("Perhitungan Komposisi Segmen selesai berhasil untuk tahun: {$tahun}");
        } catch (\Exception $e) {
            Log::error("Kesalahan dalam perhitungan Komposisi Segmen: " . $e->getMessage());
        }

        try {
            $this->calculateMarketLintasan($tahun);
            Log::info("Perhitungan Market Lintasan selesai berhasil untuk tahun: {$tahun}");
        } catch (\Exception $e) {
            Log::error("Kesalahan dalam perhitungan Market Lintasan: " . $e->getMessage());
        }
    }

    /**
     * Mengatur urutan perhitungan untuk data Komposisi Segmen.
     *
     * @param int $tahun Tahun data.
     * @return void
     */
    private function calculateKomposisiSegmen($tahun)
    {
        // Perhitungan untuk Pelabuhan Merak
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
        
        Log::info("Perhitungan Komposisi Segmen (Merak) untuk tahun {$tahun} selesai.");

        // Perhitungan untuk Pelabuhan Bakauheni
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

        Log::info("Perhitungan Komposisi Segmen (Bakauheni) untuk tahun {$tahun} selesai.");

        // Perhitungan untuk Gabungan (Merak + Bakauheni)
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

        Log::info("Perhitungan Komposisi Segmen (Gabungan) untuk tahun {$tahun} selesai.");
    }
    
    /**
     * Menyimpan data Komposisi Segmen untuk Merak Golongan IVA.
     * @param int $tahun Tahun data.
     */
    private function simpanDataMerakIVA($tahun)
    {
        $ifcsIVA = pelabuhan_merak::where('golongan', 'IVA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIVA = pelabuhan_merak::where('golongan', 'IVA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIVA = $ifcsIVA + $redeemIVA;
        $nonifcsIVA = pelabuhan_merak::where('golongan', 'IVA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemIVA + $nonifcsIVA;

        Log::info("Merak IVA -> IFCS+REDEEM: {$totalIFCSRedeemIVA}, NONIFCS: {$nonifcsIVA}, TOTAL: {$total}");

        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'IVA', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemIVA,
                'nonifcs' => $nonifcsIVA,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Merak Golongan IVB.
     * @param int $tahun Tahun data.
     */
    private function simpanDataMerakIVB($tahun)
    {
        $ifcsIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIVB = $ifcsIVB + $redeemIVB;
        $nonifcsIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemIVB + $nonifcsIVB;

        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'IVB', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemIVB,
                'nonifcs' => $nonifcsIVB,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Merak Golongan VA.
     * @param int $tahun Tahun data.
     */
    private function simpanDataMerakVA($tahun)
    {
        $ifcsVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVA = $ifcsVA + $redeemVA;
        $nonifcsVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVA + $nonifcsVA;

        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VA', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVA,
                'nonifcs' => $nonifcsVA,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Merak Golongan VB.
     * @param int $tahun Tahun data.
     */
    private function simpanDataMerakVB($tahun)
    {
        $ifcsVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVB = $ifcsVB + $redeemVB;
        $nonifcsVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVB + $nonifcsVB;

        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VB', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVB,
                'nonifcs' => $nonifcsVB,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Merak Golongan VIA.
     * @param int $tahun Tahun data.
     */
    private function simpanDataMerakVIA($tahun)
    {
        $ifcsVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIA = $ifcsVIA + $redeemVIA;
        $nonifcsVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVIA + $nonifcsVIA;

        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VIA', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVIA,
                'nonifcs' => $nonifcsVIA,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Merak Golongan VIB.
     * @param int $tahun Tahun data.
     */
    private function simpanDataMerakVIB($tahun)
    {
        $ifcsVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIB = $ifcsVIB + $redeemVIB;
        $nonifcsVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVIB + $nonifcsVIB;

        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VIB', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVIB,
                'nonifcs' => $nonifcsVIB,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Merak Golongan VII.
     * @param int $tahun Tahun data.
     */
    private function simpanDataMerakVII($tahun)
    {
        $ifcsVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVII = $ifcsVII + $redeemVII;
        $nonifcsVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVII + $nonifcsVII;

        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VII', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVII,
                'nonifcs' => $nonifcsVII,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Merak Golongan VIII.
     * @param int $tahun Tahun data.
     */
    private function simpanDataMerakVIII($tahun)
    {
        $ifcsVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIII = $ifcsVIII + $redeemVIII;
        $nonifcsVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVIII + $nonifcsVIII;

        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VIII', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVIII,
                'nonifcs' => $nonifcsVIII,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Merak Golongan IX.
     * @param int $tahun Tahun data.
     */
    private function simpanDataMerakIX($tahun)
    {
        $ifcsIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIX = $ifcsIX + $redeemIX;
        $nonifcsIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemIX + $nonifcsIX;

        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'IX', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemIX,
                'nonifcs' => $nonifcsIX,
                'total' => $total,
            ]
        );
    }
    
    /**
     * Menyimpan data total Komposisi Segmen untuk Merak.
     * @param int $tahun Tahun data.
     */
    private function simpanDataTotalMerak($tahun)
    {
        $totalifcs = komposisi_segmen::whereIn('golongan', ['IVA', 'IVB', 'VA', 'VB', 'VIA', 'VIB', 'VII', 'VIII', 'IX'])
            ->where('jenis', 'merak')
            ->where('tahun', $tahun)
            ->sum('ifcs_redeem');
        
        $totalnonifcs = komposisi_segmen::whereIn('golongan', ['IVA', 'IVB', 'VA', 'VB', 'VIA', 'VIB', 'VII', 'VIII', 'IX'])
            ->where('jenis', 'merak')
            ->where('tahun', $tahun)
            ->sum('nonifcs');
            
        $total = $totalifcs + $totalnonifcs;

        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'Total', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalifcs,
                'nonifcs' => $totalnonifcs,
                'total' => $total,
            ]
        );
    }

    //Bagian Bakauheni (Logika serupa dengan Merak)

    /**
     * Menyimpan data Komposisi Segmen untuk Bakauheni Golongan IVA.
     * @param int $tahun Tahun data.
     */
    private function simpanDataBakauheniIVA($tahun)
    {
        $ifcsIVA = pelabuhan_bakauheni::where('golongan', 'IVA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIVA = pelabuhan_bakauheni::where('golongan', 'IVA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIVA = $ifcsIVA + $redeemIVA;
        $nonifcsIVA = pelabuhan_bakauheni::where('golongan', 'IVA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemIVA + $nonifcsIVA;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IVA', 'jenis' => 'bakauheni','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemIVA,
                'nonifcs' => $nonifcsIVA,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Bakauheni Golongan IVB.
     * @param int $tahun Tahun data.
     */
    private function simpanDataBakauheniIVB($tahun)
    {
        $ifcsIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIVB = $ifcsIVB + $redeemIVB;
        $nonifcsIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemIVB + $nonifcsIVB;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IVB', 'jenis' => 'bakauheni','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemIVB,
                'nonifcs' => $nonifcsIVB,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Bakauheni Golongan VA.
     * @param int $tahun Tahun data.
     */
    private function simpanDataBakauheniVA($tahun)
    {
        $ifcsVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVA = $ifcsVA + $redeemVA;
        $nonifcsVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVA + $nonifcsVA;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VA', 'jenis' => 'bakauheni','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVA,
                'nonifcs' => $nonifcsVA,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Bakauheni Golongan VB.
     * @param int $tahun Tahun data.
     */
    private function simpanDataBakauheniVB($tahun)
    {
        $ifcsVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVB = $ifcsVB + $redeemVB;
        $nonifcsVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVB + $nonifcsVB;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VB', 'jenis' => 'bakauheni','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVB,
                'nonifcs' => $nonifcsVB,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Bakauheni Golongan VIA.
     * @param int $tahun Tahun data.
     */
    private function simpanDataBakauheniVIA($tahun)
    {
        $ifcsVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIA = $ifcsVIA + $redeemVIA;
        $nonifcsVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVIA + $nonifcsVIA;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIA', 'jenis' => 'bakauheni','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVIA,
                'nonifcs' => $nonifcsVIA,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Bakauheni Golongan VIB.
     * @param int $tahun Tahun data.
     */
    private function simpanDataBakauheniVIB($tahun)
    {
        $ifcsVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIB = $ifcsVIB + $redeemVIB;
        $nonifcsVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVIB + $nonifcsVIB;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIB', 'jenis' => 'bakauheni','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVIB,
                'nonifcs' => $nonifcsVIB,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Bakauheni Golongan VII.
     * @param int $tahun Tahun data.
     */
    private function simpanDataBakauheniVII($tahun)
    {
        $ifcsVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVII = $ifcsVII + $redeemVII;
        $nonifcsVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVII + $nonifcsVII;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VII', 'jenis' => 'bakauheni','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVII,
                'nonifcs' => $nonifcsVII,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Bakauheni Golongan VIII.
     * @param int $tahun Tahun data.
     */
    private function simpanDataBakauheniVIII($tahun)
    {
        $ifcsVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIII = $ifcsVIII + $redeemVIII;
        $nonifcsVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemVIII + $nonifcsVIII;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIII', 'jenis' => 'bakauheni','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVIII,
                'nonifcs' => $nonifcsVIII,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Bakauheni Golongan IX.
     * @param int $tahun Tahun data.
     */
    private function simpanDataBakauheniIX($tahun)
    {
        $ifcsIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIX = $ifcsIX + $redeemIX;
        $nonifcsIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $total = $totalIFCSRedeemIX + $nonifcsIX;
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IX', 'jenis' => 'bakauheni','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemIX,
                'nonifcs' => $nonifcsIX,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data total Komposisi Segmen untuk Bakauheni.
     * @param int $tahun Tahun data.
     */
    private function simpanDataTotalBakauheni($tahun)
    {
        $totalifcs = komposisi_segmen::whereIn('golongan', ['IVA', 'IVB', 'VA', 'VB', 'VIA', 'VIB', 'VII', 'VIII', 'IX'])
            ->where('jenis', 'bakauheni')
            ->where('tahun', $tahun)
            ->sum('ifcs_redeem');
        
        $totalnonifcs = komposisi_segmen::whereIn('golongan', ['IVA', 'IVB', 'VA', 'VB', 'VIA', 'VIB', 'VII', 'VIII', 'IX'])
            ->where('jenis', 'bakauheni')
            ->where('tahun', $tahun)
            ->sum('nonifcs');
            
        $total = $totalifcs + $totalnonifcs;

        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'Total', 'jenis' => 'bakauheni','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalifcs,
                'nonifcs' => $totalnonifcs,
                'total' => $total,
            ]
        );
    }

    //Bagian Gabungan (Logika untuk menjumlahkan Merak dan Bakauheni)

    /**
     * Menyimpan data Komposisi Segmen untuk Gabungan Golongan IVA.
     * @param int $tahun Tahun data.
     */
    private function simpanDataGabunganIVA($tahun)
    {
        $IFCSmerakIVA = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem');
        $IFCSbakauheniIVA = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem');
        $IFCSgabunganIVA = ($IFCSmerakIVA ?? 0) + ($IFCSbakauheniIVA ?? 0);

        $NONIFCSmerakIVA = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs');
        $NONIFCSbakauheniIVA = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs');
        $NONIFCSgabunganIVA = ($NONIFCSmerakIVA ?? 0) + ($NONIFCSbakauheniIVA ?? 0);
        
        $total = $IFCSgabunganIVA + $NONIFCSgabunganIVA;

        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IVA', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [
                'ifcs_redeem' => $IFCSgabunganIVA,
                'nonifcs' => $NONIFCSgabunganIVA,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Gabungan Golongan IVB.
     * @param int $tahun Tahun data.
     */
    private function simpanDataGabunganIVB($tahun)
    {
        $IFCSmerakIVB = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem'); 
        $IFCSbakauheniIVB = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem');
        $IFCSgabunganIVB = ($IFCSmerakIVB ?? 0) + ($IFCSbakauheniIVB ?? 0);

        $NONIFCSmerakIVB = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs');  
        $NONIFCSbakauheniIVB = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs'); 
        $NONIFCSgabunganIVB = ($NONIFCSmerakIVB ?? 0) + ($NONIFCSbakauheniIVB ?? 0);

        $total = $IFCSgabunganIVB + $NONIFCSgabunganIVB;

        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IVB', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [
                'ifcs_redeem' => $IFCSgabunganIVB,
                'nonifcs' => $NONIFCSgabunganIVB,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Gabungan Golongan VA.
     * @param int $tahun Tahun data.
     */
    private function simpanDataGabunganVA($tahun)
    {
        $IFCSmerakVA = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem'); 
        $IFCSbakauheniVA = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem');
        $IFCSgabunganVA = ($IFCSmerakVA ?? 0) + ($IFCSbakauheniVA ?? 0);

        $NONIFCSmerakVA = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs');  
        $NONIFCSbakauheniVA = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs'); 
        $NONIFCSgabunganVA = ($NONIFCSmerakVA ?? 0) + ($NONIFCSbakauheniVA ?? 0);

        $total = $IFCSgabunganVA + $NONIFCSgabunganVA;

        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VA', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [
                'ifcs_redeem' => $IFCSgabunganVA,
                'nonifcs' => $NONIFCSgabunganVA,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Gabungan Golongan VB.
     * @param int $tahun Tahun data.
     */
    private function simpanDataGabunganVB($tahun)
    {
        $IFCSmerakVB = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem'); 
        $IFCSbakauheniVB = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem');
        $IFCSgabunganVB = ($IFCSmerakVB ?? 0) + ($IFCSbakauheniVB ?? 0);

        $NONIFCSmerakVB = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs');  
        $NONIFCSbakauheniVB = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs'); 
        $NONIFCSgabunganVB = ($NONIFCSmerakVB ?? 0) + ($NONIFCSbakauheniVB ?? 0);

        $total = $IFCSgabunganVB + $NONIFCSgabunganVB;

        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VB', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [
                'ifcs_redeem' => $IFCSgabunganVB,
                'nonifcs' => $NONIFCSgabunganVB,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Gabungan Golongan VIA.
     * @param int $tahun Tahun data.
     */
    private function simpanDataGabunganVIA($tahun)
    {
        $IFCSmerakVIA = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem'); 
        $IFCSbakauheniVIA = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem');
        $IFCSgabunganVIA = ($IFCSmerakVIA ?? 0) + ($IFCSbakauheniVIA ?? 0);

        $NONIFCSmerakVIA = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs');  
        $NONIFCSbakauheniVIA = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs'); 
        $NONIFCSgabunganVIA = ($NONIFCSmerakVIA ?? 0) + ($NONIFCSbakauheniVIA ?? 0);

        $total = $IFCSgabunganVIA + $NONIFCSgabunganVIA;

        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIA', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [
                'ifcs_redeem' => $IFCSgabunganVIA,
                'nonifcs' => $NONIFCSgabunganVIA,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Gabungan Golongan VIB.
     * @param int $tahun Tahun data.
     */
    private function simpanDataGabunganVIB($tahun)
    {
        $IFCSmerakVIB = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem'); 
        $IFCSbakauheniVIB = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem');
        $IFCSgabunganVIB = ($IFCSmerakVIB ?? 0) + ($IFCSbakauheniVIB ?? 0);

        $NONIFCSmerakVIB = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs');  
        $NONIFCSbakauheniVIB = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs'); 
        $NONIFCSgabunganVIB = ($NONIFCSmerakVIB ?? 0) + ($NONIFCSbakauheniVIB ?? 0);

        $total = $IFCSgabunganVIB + $NONIFCSgabunganVIB;

        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIB', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [
                'ifcs_redeem' => $IFCSgabunganVIB,
                'nonifcs' => $NONIFCSgabunganVIB,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Gabungan Golongan VII.
     * @param int $tahun Tahun data.
     */
    private function simpanDataGabunganVII($tahun)
    {
        $IFCSmerakVII = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem'); 
        $IFCSbakauheniVII = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem');
        $IFCSgabunganVII = ($IFCSmerakVII ?? 0) + ($IFCSbakauheniVII ?? 0);

        $NONIFCSmerakVII = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs');  
        $NONIFCSbakauheniVII = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs'); 
        $NONIFCSgabunganVII = ($NONIFCSmerakVII ?? 0) + ($NONIFCSbakauheniVII ?? 0);

        $total = $IFCSgabunganVII + $NONIFCSgabunganVII;

        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VII', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [
                'ifcs_redeem' => $IFCSgabunganVII,
                'nonifcs' => $NONIFCSgabunganVII,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Gabungan Golongan VIII.
     * @param int $tahun Tahun data.
     */
    private function simpanDataGabunganVIII($tahun)
    {
        $IFCSmerakVIII = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem'); 
        $IFCSbakauheniVIII = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem');
        $IFCSgabunganVIII = ($IFCSmerakVIII ?? 0) + ($IFCSbakauheniVIII ?? 0);

        $NONIFCSmerakVIII = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs');  
        $NONIFCSbakauheniVIII = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs'); 
        $NONIFCSgabunganVIII = ($NONIFCSmerakVIII ?? 0) + ($NONIFCSbakauheniVIII ?? 0);

        $total = $IFCSgabunganVIII + $NONIFCSgabunganVIII;

        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIII', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [
                'ifcs_redeem' => $IFCSgabunganVIII,
                'nonifcs' => $NONIFCSgabunganVIII,
                'total' => $total,
            ]
        );
    }

    /**
     * Menyimpan data Komposisi Segmen untuk Gabungan Golongan IX.
     * @param int $tahun Tahun data.
     */
    private function simpanDataGabunganIX($tahun)
    {
        $IFCSmerakIX = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'merak')->where('tahun', $tahun)->value('ifcs_redeem'); 
        $IFCSbakauheniIX = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('ifcs_redeem');
        $IFCSgabunganIX = ($IFCSmerakIX ?? 0) + ($IFCSbakauheniIX ?? 0);

        $NONIFCSmerakIX = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'merak')->where('tahun', $tahun)->value('nonifcs');  
        $NONIFCSbakauheniIX = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'bakauheni')->where('tahun', $tahun)->value('nonifcs'); 
        $NONIFCSgabunganIX = ($NONIFCSmerakIX ?? 0) + ($NONIFCSbakauheniIX ?? 0);

        $total = $IFCSgabunganIX + $NONIFCSgabunganIX;

        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IX', 'jenis' => 'gabungan', 'tahun' => $tahun],
            [
                'ifcs_redeem' => $IFCSgabunganIX,
                'nonifcs' => $NONIFCSgabunganIX,
                'total' => $total,
            ]
        );
    }
    
    /**
     * Menyimpan data total Komposisi Segmen untuk Gabungan.
     * @param int $tahun Tahun data.
     */
    private function simpanDataTotalGabungan($tahun)
    {
        $totalifcs = komposisi_segmen::whereIn('golongan', ['IVA', 'IVB', 'VA', 'VB', 'VIA', 'VIB', 'VII', 'VIII', 'IX'])
            ->where('jenis', 'gabungan')
            ->where('tahun', $tahun)
            ->sum('ifcs_redeem');
        
        $totalnonifcs = komposisi_segmen::whereIn('golongan', ['IVA', 'IVB', 'VA', 'VB', 'VIA', 'VIB', 'VII', 'VIII', 'IX'])
            ->where('jenis', 'gabungan')
            ->where('tahun', $tahun)
            ->sum('nonifcs');
            
        $total = $totalifcs + $totalnonifcs;

        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'Total', 'jenis' => 'gabungan','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalifcs,
                'nonifcs' => $totalnonifcs,
                'total' => $total,
            ]
        );
    }
    
    /**
     * Mengatur urutan perhitungan untuk data Market Lintasan.
     * @param int $tahun Tahun data.
     * @return void
     */
    private function calculateMarketLintasan($tahun)
    {
        // Perhitungan untuk IFCS
        $this->simpanDataEksekutifIFCS($tahun);
        $this->simpanDataLogistikEksekutifIFCS($tahun);
        $this->simpanDataRedeemEksekutifIFCS($tahun);
        $this->simpanDataLogistikRedeemEksekutifIFCS($tahun);
        $this->simpanDataTotalIFCS($tahun);
        
        Log::info("Perhitungan Market Lintasan (IFCS) untuk tahun {$tahun} selesai.");
    
        // Perhitungan untuk INDUSTRI
        $this->simpanDataBusReguler($tahun);
        $this->simpanDataLogistikReguler($tahun);
        $this->simpanDataEksekkutifNonIFCS($tahun);
        $this->simpanDataEksekutifNonIFCS($tahun);
        $this->simpanDataTotalINDUSTRI($tahun);
        
        Log::info("Perhitungan Market Lintasan (INDUSTRI) untuk tahun {$tahun} selesai.");
    }
    
    //Bagian IFCS Market Lintasan

    /**
     * Menyimpan data Market Lintasan untuk Eksekutif IFCS.
     * @param int $tahun Tahun data.
     */
    private function simpanDataEksekutifIFCS($tahun)
    {
        $merakVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $merakVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');

        $totalMerak = $merakVA + $merakVIA;
        $totalBakauheni = $bakauheniVA + $bakauheniVIA;
        $totalGabungan = $totalMerak + $totalBakauheni;

        Log::info("Market Lintasan Eksekutif IFCS -> Merak: {$totalMerak}, Bakauheni: {$totalBakauheni}, Gabungan: {$totalGabungan}");

        market_lintasan::updateOrCreate(
            ['golongan' => 'Logistik IFCS', 'jenis' => 'ifcs', 'tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }

    /**
     * Menyimpan data Market Lintasan untuk Logistik Eksekutif IFCS.
     * @param int $tahun Tahun data.
     */
    private function simpanDataLogistikEksekutifIFCS($tahun)
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

    /**
     * Menyimpan data Market Lintasan untuk Redeem Eksekutif IFCS.
     * @param int $tahun Tahun data.
     */
    private function simpanDataRedeemEksekutifIFCS($tahun)
    {
        $merakVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $merakVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $bakauheniVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $bakauheniVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');

        $totalMerak = $merakVA + $merakVIA;
        $totalBakauheni = $bakauheniVA + $bakauheniVIA;
        $totalGabungan = $totalMerak + $totalBakauheni;

        market_lintasan::updateOrCreate(
            ['golongan' => 'Redeem Eksekutif IFCS', 'jenis' => 'ifcs','tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }
    
    /**
     * Menyimpan data Market Lintasan untuk Logistik Redeem Eksekutif IFCS.
     * @param int $tahun Tahun data.
     */
    private function simpanDataLogistikRedeemEksekutifIFCS($tahun)
    {
        $merakIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $merakVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $merakVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $merakVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $merakVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $merakIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');

        $bakauheniIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $bakauheniVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $bakauheniVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $bakauheniVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $bakauheniVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $bakauheniIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');

        $totalMerak = $merakIVB + $merakVB + $merakVIB + $merakVII + $merakVIII + $merakIX;
        $totalBakauheni = $bakauheniIVB + $bakauheniVB + $bakauheniVIB + $bakauheniVII + $bakauheniVIII + $bakauheniIX;
        $totalGabungan = $totalMerak + $totalBakauheni;

        market_lintasan::updateOrCreate(
            ['golongan' => 'Logistik Redeem Eksekutif IFCS','jenis' => 'ifcs','tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }

    /**
     * Menyimpan data total Market Lintasan untuk IFCS.
     * @param int $tahun Tahun data.
     */
    private function simpanDataTotalIFCS($tahun)
    {
        $totalMerak = market_lintasan::whereIn('golongan', ['Logistik IFCS', 'Logistik Eksekutif IFCS', 'Redeem Eksekutif IFCS', 'Logistik Redeem Eksekutif IFCS'])
            ->where('tahun', $tahun)
            ->where('jenis', 'ifcs')
            ->sum('merak');
        
        $totalBakauheni = market_lintasan::whereIn('golongan', ['Logistik IFCS', 'Logistik Eksekutif IFCS', 'Redeem Eksekutif IFCS', 'Logistik Redeem Eksekutif IFCS'])
            ->where('tahun', $tahun)
            ->where('jenis', 'ifcs')
            ->sum('bakauheni');
            
        $totalGabungan = market_lintasan::whereIn('golongan', ['Logistik IFCS', 'Logistik Eksekutif IFCS', 'Redeem Eksekutif IFCS', 'Logistik Redeem Eksekutif IFCS'])
            ->where('tahun', $tahun)
            ->where('jenis', 'ifcs')
            ->sum('gabungan');

        market_lintasan::updateOrCreate(
            ['golongan' => 'Total', 'jenis' => 'ifcs', 'tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }    
 
    //Bagian INDUSTRI Market Lintasan

    /**
     * Menyimpan data Market Lintasan untuk Kendaraan Bus Reguler.
     * @param int $tahun Tahun data.
     */
    private function simpanDataBusReguler($tahun)
    {
        $merakVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $merakVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');

        $totalMerak = $merakVA + $merakVIA;
        $totalBakauheni = $bakauheniVA + $bakauheniVIA;
        $totalGabungan = $totalMerak + $totalBakauheni;

        market_lintasan::updateOrCreate(
            ['golongan' => 'Kendaraan Bus Reguler', 'jenis' => 'industri','tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }

    /**
     * Menyimpan data Market Lintasan untuk Logistik Reguler.
     * @param int $tahun Tahun data.
     */
    private function simpanDataLogistikReguler($tahun)
    {
        $merakIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $merakVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $merakVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $merakVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $merakVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $merakIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');

        $bakauheniIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');

        $totalMerak = $merakIVB + $merakVB + $merakVIB + $merakVII + $merakVIII + $merakIX;
        $totalBakauheni = $bakauheniIVB + $bakauheniVB + $bakauheniVIB + $bakauheniVII + $bakauheniVIII + $bakauheniIX;
        $totalGabungan = $totalMerak + $totalBakauheni;

        market_lintasan::updateOrCreate(
            ['golongan' => 'Logistik Reguler','jenis' => 'industri','tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }

    /**
     * Menyimpan data Market Lintasan untuk Logistik Eksekutif Non IFCS.
     * @param int $tahun Tahun data.
     */
    private function simpanDataEksekkutifNonIFCS($tahun)
    {
        $merakIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $merakVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $merakVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $merakVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $merakVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $merakIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        $bakauheniIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        $totalMerak = $merakIVB + $merakVB + $merakVIB + $merakVII + $merakVIII + $merakIX;
        $totalBakauheni = $bakauheniIVB + $bakauheniVB + $bakauheniVIB + $bakauheniVII + $bakauheniVIII + $bakauheniIX;
        $totalGabungan = $totalMerak + $totalBakauheni;

        market_lintasan::updateOrCreate(
            ['golongan' => 'Logistik Eksekutif Non IFCS','jenis' => 'industri','tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }
    
    /**
     * Menyimpan data Market Lintasan untuk Eksekutif Non IFCS.
     * @param int $tahun Tahun data.
     */
    private function simpanDataEksekutifNonIFCS($tahun)
    {
        $merakVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $merakVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'reguler')->where('tahun', $tahun)->sum('total');
        $bakauheniVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');
        $bakauheniVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        $totalMerak = $merakVA + $merakVIA;
        $totalBakauheni = $bakauheniVA + $bakauheniVIA;
        $totalGabungan = $totalMerak + $totalBakauheni;

        market_lintasan::updateOrCreate(
            ['golongan' => 'Eksekutif Non IFCS', 'jenis' => 'industri','tahun' => $tahun],
            [
                'merak' => $totalMerak,
                'bakauheni' => $totalBakauheni,
                'gabungan' => $totalGabungan,
            ]
        );
    }

    /**
     * Menyimpan data total Market Lintasan untuk INDUSTRI.
     * @param int $tahun Tahun data.
     */
    private function simpanDataTotalINDUSTRI($tahun)
    {
        $totalMerak = market_lintasan::whereIn('golongan', ['Kendaraan Bus Reguler', 'Logistik Reguler', 'Logistik Eksekutif Non IFCS', 'Eksekutif Non IFCS'])
            ->where('tahun', $tahun)
            ->where('jenis', 'industri')
            ->sum('merak');
        
        $totalBakauheni = market_lintasan::whereIn('golongan', ['Kendaraan Bus Reguler', 'Logistik Reguler', 'Logistik Eksekutif Non IFCS', 'Eksekutif Non IFCS'])
            ->where('tahun', $tahun)
            ->where('jenis', 'industri')
            ->sum('bakauheni');
            
        $totalGabungan = market_lintasan::whereIn('golongan', ['Kendaraan Bus Reguler', 'Logistik Reguler', 'Logistik Eksekutif Non IFCS', 'Eksekutif Non IFCS'])
            ->where('tahun', $tahun)
            ->where('jenis', 'industri')
            ->sum('gabungan');

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
