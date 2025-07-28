<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\pelabuhan_merak;
use App\Models\pelabuhan_bakauheni;
use App\Models\komposisi_segmen;
use Illuminate\Database\QueryException;

// Import controller yang diperlukan untuk memanggil fungsi perhitungan total pelabuhan
use App\Http\Controllers\pelabuhanMerakController;
use App\Http\Controllers\pelabuhanBakauheniController;

class komposisiSegmenController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $startYear = 2020;
        $validYears = range($startYear, $currentYear);
        $tahun = $request->input('tahun', null);

        // Tentukan tahun mana yang akan diambil/dihitung
        $yearsToProcess = $tahun && in_array($tahun, $validYears) ? [$tahun] : $validYears;

        try {
            // Inisialisasi controller lain untuk memicu perhitungan total pelabuhan
            $merakController = new pelabuhanMerakController();
            $bakauheniController = new pelabuhanBakauheniController();

            // Lakukan perhitungan untuk setiap tahun yang relevan
            foreach ($yearsToProcess as $year) {
                // PENTING: Pastikan data total Pelabuhan Merak dihitung terlebih dahulu
                $merakController->simpanDataTotalIFCS($year);
                $merakController->simpanDataTotalREDEEM($year);
                $merakController->simpanDataTotalNONIFCS($year);
                $merakController->simpanDataTotalREGULER($year); // Asumsi ini juga dibutuhkan

                // PENTING: Pastikan data total Pelabuhan Bakauheni dihitung terlebih dahulu
                $bakauheniController->simpanDataTotalIFCS($year);
                $bakauheniController->simpanDataTotalREDEEM($year);
                $bakauheniController->simpanDataTotalNONIFCS($year);
                $bakauheniController->simpanDataTotalREGULER($year); // Asumsi ini juga dibutuhkan

                // Setelah data total Merak dan Bakauheni tersedia, baru hitung Komposisi Segmen
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
            // Tangkap dan tampilkan error database jika terjadi
            return redirect()->route('komposisi.index')->with('error', 'Terjadi error database saat perhitungan: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Tangkap error umum lainnya (misal: kelas tidak ditemukan jika controller lain belum ada)
            return redirect()->route('komposisi.index')->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }

        // Ambil data untuk ditampilkan setelah semua perhitungan (atau jika terjadi error, tampilkan yang ada)
        $komposisi_segmen = komposisi_segmen::whereIn('tahun', $yearsToProcess)->get();

        return view('ifcs.komposisi-segmen', [
            'komposisi_segmen' => $komposisi_segmen,
            'years' => $validYears,
            'selectedYear' => $tahun 
        ]);
    }

    // Merak
    public function simpanDataMerakIVA($tahun)
    {
        // ifcs_redeem
        $ifcsIVA = pelabuhan_merak::where('golongan', 'IVA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        // PERBAIKAN: redeemIVA sekarang mengambil dari golongan IVA
        $redeemIVA = pelabuhan_merak::where('golongan', 'IVA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIVA = $ifcsIVA + $redeemIVA;
        
        // nonifcs
        $nonifcsIVA = pelabuhan_merak::where('golongan', 'IVA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemIVA + $nonifcsIVA;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'IVA', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemIVA,
                'nonifcs' => $nonifcsIVA,
                'total' => $total,
            ]
        );
    }

    public function simpanDataMerakIVB($tahun)
    {
        // ifcs_redeem
        $ifcsIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIVB = $ifcsIVB + $redeemIVB;
        
        // nonifcs
        $nonifcsIVB = pelabuhan_merak::where('golongan', 'IVB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemIVB + $nonifcsIVB;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'IVB', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemIVB,
                'nonifcs' => $nonifcsIVB,
                'total' => $total,
            ]
        );
    }

    public function simpanDataMerakVA($tahun)
    {
        // ifcs_redeem
        $ifcsVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVA = $ifcsVA + $redeemVA;
        
        // nonifcs
        $nonifcsVA = pelabuhan_merak::where('golongan', 'VA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemVA + $nonifcsVA;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VA', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVA,
                'nonifcs' => $nonifcsVA,
                'total' => $total,
            ]
        );
    }

    public function simpanDataMerakVB($tahun)
    {
        // ifcs_redeem
        $ifcsVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVB = $ifcsVB + $redeemVB;
        
        // nonifcs
        $nonifcsVB = pelabuhan_merak::where('golongan', 'VB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemVB + $nonifcsVB;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VB', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVB,
                'nonifcs' => $nonifcsVB,
                'total' => $total,
            ]
        );
    }

    public function simpanDataMerakVIA($tahun)
    {
        // ifcs_redeem
        $ifcsVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIA = $ifcsVIA + $redeemVIA;
        
        // nonifcs
        $nonifcsVIA = pelabuhan_merak::where('golongan', 'VIA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemVIA + $nonifcsVIA;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VIA', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVIA,
                'nonifcs' => $nonifcsVIA,
                'total' => $total,
            ]
        );
    }

    public function simpanDataMerakVIB($tahun)
    {
        // ifcs_redeem
        $ifcsVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIB = $ifcsVIB + $redeemVIB;
        
        // nonifcs
        $nonifcsVIB = pelabuhan_merak::where('golongan', 'VIB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemVIB + $nonifcsVIB;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VIB', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVIB,
                'nonifcs' => $nonifcsVIB,
                'total' => $total,
            ]
        );
    }

    public function simpanDataMerakVII($tahun)
    {
        // ifcs_redeem
        $ifcsVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVII = $ifcsVII + $redeemVII;
        
        // nonifcs
        $nonifcsVII = pelabuhan_merak::where('golongan', 'VII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemVII + $nonifcsVII;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VII', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVII,
                'nonifcs' => $nonifcsVII,
                'total' => $total,
            ]
        );
    }

    public function simpanDataMerakVIII($tahun)
    {
        // ifcs_redeem
        $ifcsVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIII = $ifcsVIII + $redeemVIII;
        
        // nonifcs
        $nonifcsVIII = pelabuhan_merak::where('golongan', 'VIII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemVIII + $nonifcsVIII;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'VIII', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemVIII,
                'nonifcs' => $nonifcsVIII,
                'total' => $total,
            ]
        );
    }

    public function simpanDataMerakIX($tahun)
    {
        // ifcs_redeem
        $ifcsIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIX = $ifcsIX + $redeemIX;
        
        // nonifcs
        $nonifcsIX = pelabuhan_merak::where('golongan', 'IX')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemIX + $nonifcsIX;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'IX', 'jenis' => 'merak','tahun' => $tahun],
            [
                'ifcs_redeem' => $totalIFCSRedeemIX,
                'nonifcs' => $nonifcsIX,
                'total' => $total,
            ]
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
        // ifcs_redeem
        $ifcsIVA = pelabuhan_bakauheni::where('golongan', 'IVA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        // PERBAIKAN: redeemIVA sekarang mengambil dari golongan IVA
        $redeemIVA = pelabuhan_bakauheni::where('golongan', 'IVA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIVA = $ifcsIVA + $redeemIVA;
        
        // nonifcs
        $nonifcsIVA = pelabuhan_bakauheni::where('golongan', 'IVA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemIVA + $nonifcsIVA;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IVA', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemIVA, 'nonifcs' => $nonifcsIVA, 'total' => $total]
        );
    }

    public function simpanDataBakauheniIVB($tahun)
    {
        // ifcs_redeem
        $ifcsIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIVB = $ifcsIVB + $redeemIVB;
        
        // nonifcs
        $nonifcsIVB = pelabuhan_bakauheni::where('golongan', 'IVB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemIVB + $nonifcsIVB;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IVB', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemIVB, 'nonifcs' => $nonifcsIVB, 'total' => $total]
        );
    }

    public function simpanDataBakauheniVA($tahun)
    {
        // ifcs_redeem
        $ifcsVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVA = $ifcsVA + $redeemVA;
        
        // nonifcs
        $nonifcsVA = pelabuhan_bakauheni::where('golongan', 'VA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemVA + $nonifcsVA;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VA', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVA, 'nonifcs' => $nonifcsVA, 'total' => $total]
        );
    }

    public function simpanDataBakauheniVB($tahun)
    {
        // ifcs_redeem
        $ifcsVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVB = $ifcsVB + $redeemVB;
        
        // nonifcs
        $nonifcsVB = pelabuhan_bakauheni::where('golongan', 'VB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemVB + $nonifcsVB;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VB', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVB, 'nonifcs' => $nonifcsVB, 'total' => $total]
        );
    }

    public function simpanDataBakauheniVIA($tahun)
    {
        // ifcs_redeem
        $ifcsVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIA = $ifcsVIA + $redeemVIA;
        
        // nonifcs
        $nonifcsVIA = pelabuhan_bakauheni::where('golongan', 'VIA')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemVIA + $nonifcsVIA;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIA', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVIA, 'nonifcs' => $nonifcsVIA, 'total' => $total]
        );
    }

    public function simpanDataBakauheniVIB($tahun)
    {
        // ifcs_redeem
        $ifcsVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIB = $ifcsVIB + $redeemVIB;
        
        // nonifcs
        $nonifcsVIB = pelabuhan_bakauheni::where('golongan', 'VIB')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemVIB + $nonifcsVIB;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIB', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVIB, 'nonifcs' => $nonifcsVIB, 'total' => $total]
        );
    }

    public function simpanDataBakauheniVII($tahun)
    {
        // ifcs_redeem
        $ifcsVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVII = $ifcsVII + $redeemVII;
        
        // nonifcs
        $nonifcsVII = pelabuhan_bakauheni::where('golongan', 'VII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemVII + $nonifcsVII;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VII', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVII, 'nonifcs' => $nonifcsVII, 'total' => $total]
        );
    }

    public function simpanDataBakauheniVIII($tahun)
    {
        // ifcs_redeem
        $ifcsVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemVIII = $ifcsVIII + $redeemVIII;
        
        // nonifcs
        $nonifcsVIII = pelabuhan_bakauheni::where('golongan', 'VIII')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemVIII + $nonifcsVIII;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'VIII', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemVIII, 'nonifcs' => $nonifcsVIII, 'total' => $total]
        );
    }

    public function simpanDataBakauheniIX($tahun)
    {
        // ifcs_redeem
        $ifcsIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'ifcs')->where('tahun', $tahun)->sum('total');
        $redeemIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'redeem')->where('tahun', $tahun)->sum('total');
        $totalIFCSRedeemIX = $ifcsIX + $redeemIX;
        
        // nonifcs
        $nonifcsIX = pelabuhan_bakauheni::where('golongan', 'IX')->where('jenis', 'nonifcs')->where('tahun', $tahun)->sum('total');

        // Hitung total 
        $total = $totalIFCSRedeemIX + $nonifcsIX;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            ['golongan' => 'IX', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalIFCSRedeemIX, 'nonifcs' => $nonifcsIX, 'total' => $total]
        );
    }

    public function simpanDataTotalBakauheni($tahun)
    {
        // IFCS REDEEM
        $redeem1 = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem2 = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem3 = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem4 = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem5 = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem6 = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem7 = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem8 = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem9 = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('ifcs_redeem');

        // NON IFCS
        $nonifcs1 = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs2 = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs3 = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs4 = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs5 = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs6 = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs7 = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs8 = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs9 = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'bakauheni')->where('tahun', $tahun)->sum('nonifcs');
    
        // Hitung total untuk masing-masing kolom
        $totalifcs = $redeem1 + $redeem2 + $redeem3 + $redeem4 + $redeem5 + $redeem6 + $redeem7 + $redeem8 + $redeem9;
        $totalnonifcs = $nonifcs1 + $nonifcs2 + $nonifcs3 + $nonifcs4 + $nonifcs5 + $nonifcs6 + $nonifcs7 + $nonifcs8 + $nonifcs9;
        $total = $totalifcs + $totalnonifcs ;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'Total', 'jenis' => 'bakauheni','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalifcs, 'nonifcs' => $totalnonifcs, 'total' => $total]
        );
    }

    //Gabungan
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
            [
                'ifcs_redeem' => $IFCSgabunganIVA,
                'nonifcs' => $NONIFCSgabunganIVA,
                'total' => $total,
            ]
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
            [
                'ifcs_redeem' => $IFCSgabunganIVB,
                'nonifcs' => $NONIFCSgabunganIVB,
                'total' => $total,
            ]
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
            [
                'ifcs_redeem' => $IFCSgabunganVA,
                'nonifcs' => $NONIFCSgabunganVA,
                'total' => $total,
            ]
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
            [
                'ifcs_redeem' => $IFCSgabunganVB,
                'nonifcs' => $NONIFCSgabunganVB,
                'total' => $total,
            ]
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
            [
                'ifcs_redeem' => $IFCSgabunganVIA,
                'nonifcs' => $NONIFCSgabunganVIA,
                'total' => $total,
            ]
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
            [
                'ifcs_redeem' => $IFCSgabunganVIB,
                'nonifcs' => $NONIFCSgabunganVIB,
                'total' => $total,
            ]
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
            [
                'ifcs_redeem' => $IFCSgabunganVII,
                'nonifcs' => $NONIFCSgabunganVII,
                'total' => $total,
            ]
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
            [
                'ifcs_redeem' => $IFCSgabunganVIII,
                'nonifcs' => $NONIFCSgabunganVIII,
                'total' => $total,
            ]
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
            [
                'ifcs_redeem' => $IFCSgabunganIX,
                'nonifcs' => $NONIFCSgabunganIX,
                'total' => $total,
            ]
        );
    }

    public function simpanDataTotalGabungan($tahun)
    {
        // IFCS REDEEM
        $redeem1 = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem2 = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem3 = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem4 = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem5 = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem6 = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem7 = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem8 = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_redeem');
        $redeem9 = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('ifcs_redeem');

        // NON IFCS
        $nonifcs1 = komposisi_segmen::where('golongan', 'IVA')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs2 = komposisi_segmen::where('golongan', 'IVB')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs3 = komposisi_segmen::where('golongan', 'VA')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs4 = komposisi_segmen::where('golongan', 'VB')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs5 = komposisi_segmen::where('golongan', 'VIA')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs6 = komposisi_segmen::where('golongan', 'VIB')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs7 = komposisi_segmen::where('golongan', 'VII')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs8 = komposisi_segmen::where('golongan', 'VIII')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
        $nonifcs9 = komposisi_segmen::where('golongan', 'IX')->where('jenis', 'gabungan')->where('tahun', $tahun)->sum('nonifcs');
    
        // Hitung total untuk masing-masing kolom
        $totalifcs = $redeem1 + $redeem2 + $redeem3 + $redeem4 + $redeem5 + $redeem6 + $redeem7 + $redeem8 + $redeem9;
        $totalnonifcs = $nonifcs1 + $nonifcs2 + $nonifcs3 + $nonifcs4 + $nonifcs5 + $nonifcs6 + $nonifcs7 + $nonifcs8 + $nonifcs9;
        $total = $totalifcs + $totalnonifcs ;

        // Simpan ke tabel komposisi_segmen
        komposisi_segmen::updateOrCreate(
            [ 'golongan' => 'Total', 'jenis' => 'gabungan','tahun' => $tahun],
            [ 'ifcs_redeem' => $totalifcs, 'nonifcs' => $totalnonifcs, 'total' => $total]
        );
    }
}