<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\pelabuhan_bakauheni;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use League\Csv\Statement;
use App\Services\DataWarehouseService;
use App\Services\DataCalculationService;

class pelabuhanBakauheniController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $startYear = 2020;
        $validYears = range($startYear, $currentYear);

        $tahun = $request->input('tahun', null);
        $selectedTab = $request->input('tab', 'IFCS');

        if ($tahun && in_array($tahun, $validYears)) {
            // Perhitungan sekarang dipicu oleh tombol "Proses Data"
            $years = [$tahun];
        } else {
            $years = $validYears;
        }

        $pelabuhan_bakauheni = pelabuhan_bakauheni::whereIn('tahun', $years)->get();
        
        return view('pelabuhan.bakauheni', [
            'pelabuhan_bakauheni' => $pelabuhan_bakauheni,
            'years' => $validYears,
            'selectedYear' => $tahun,
            'selectedTab' => $selectedTab
        ]);
    }

    public function runEtl(Request $request, $tahun)
    {
        $targetYear = (int) $tahun;
        $currentYear = date('Y');
        $startYear = 2020;
        $validYears = range($startYear, $currentYear);
        if (!in_array($targetYear, $validYears)) {
            return redirect()->back()->with('etl_bakauheni_gagal', 'Tahun tidak valid.');
        }

        try {
            $etlService = new DataWarehouseService();
            $etlService->runEtlForYear($targetYear);

            // Panggil service untuk melakukan semua perhitungan data gabungan
            $dataCalculationService = new DataCalculationService();
            $dataCalculationService->calculateAllForYear($targetYear);

            return redirect()->back()->with('etl_bakauheni_sukses', "Proses ETL dan perhitungan data untuk tahun {$targetYear} berhasil dijalankan.");
        } catch (\Exception $e) {
            return redirect()->back()->with('etl_bakauheni_gagal', "Terjadi kesalahan saat menjalankan ETL: " . $e->getMessage());
        }
    }

    public function updatePost(Request $request, $id)
    {
        $validatedData = $request->validate([
            'golongan' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
            'januari' => 'required|numeric',
            'februari' => 'required|numeric',
            'maret' => 'required|numeric',
            'april' => 'required|numeric',
            'mei' => 'required|numeric',
            'juni' => 'required|numeric',
            'juli' => 'required|numeric',
            'agustus' => 'required|numeric',
            'september' => 'required|numeric',
            'oktober' => 'required|numeric',
            'november' => 'required|numeric',
            'desember' => 'required|numeric',
            'tahun' => 'required|numeric',
        ]);
    
        $record = pelabuhan_bakauheni::findOrFail($id);
        $record->update($validatedData);
    
        $tahunRedirect = $record->tahun; 
        $jenisRedirect = strtoupper($record->jenis);

        $dataCalculationService = new DataCalculationService();
        $dataCalculationService->calculateAllForYear($tahunRedirect);

        return redirect()->route('pelabuhan.bakauheni.index', [
            'tahun' => $tahunRedirect,
            'tab' => $jenisRedirect
        ])->with('update_bakauheni_success', 'Data berhasil diperbarui.');
    }

    public function uploadcsvPelabuhanBakauheni(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        
        try {
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0);
            $csv->setDelimiter(';'); 
        
            $records = (new Statement())->process($csv);
            
            $uploadedYears = [];
            foreach ($records as $record) {
                pelabuhan_bakauheni::updateOrCreate(
                    ['tahun' => $record['tahun'], 'golongan' => $record['golongan'], 'jenis' => $record['jenis']],
                    [
                        'januari' => $record['januari'],
                        'februari' => $record['februari'],
                        'maret' => $record['maret'],
                        'april' => $record['april'],
                        'mei' => $record['mei'],
                        'juni' => $record['juni'],
                        'juli' => $record['juli'],
                        'agustus' => $record['agustus'],
                        'september' => $record['september'],
                        'oktober' => $record['oktober'],
                        'november' => $record['november'],
                        'desember' => $record['desember'],
                    ]
                );
                $uploadedYears[] = $record['tahun'];
            }

            $uniqueUploadedYears = array_unique($uploadedYears);
            foreach ($uniqueUploadedYears as $tahun) {
                $dataCalculationService = new DataCalculationService();
                $dataCalculationService->calculateAllForYear($tahun);
            }
        
            return redirect()->back()->with('upload_bakauheni_berhasil', 'File CSV berhasil diupload');
        } catch (\Exception $e) {
            return redirect()->back()->with('upload_bakauheni_gagal', 'Terjadi kesalahan saat mengupload file CSV: ' . $e->getMessage());
        }
    } 

    public function delete(Request $request, $id)
    {
        try {
            $record = pelabuhan_bakauheni::findOrFail($id);
            $tahunAffected = $record->tahun;
            $jenisAffected = $record->jenis;

            if ($record->delete()) {
                $dataCalculationService = new DataCalculationService();
                $dataCalculationService->calculateAllForYear($tahunAffected);
                
                return redirect()->route('pelabuhan.bakauheni.index', [
                    'tahun' => $tahunAffected,
                    'tab' => strtoupper($jenisAffected)
                ])->with('delete_bakauheni_success', 'Data berhasil dihapus.');
            } else {
                return redirect()->back()->with('delete_bakauheni_gagal', 'Gagal menghapus data.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('delete_bakauheni_gagal', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}