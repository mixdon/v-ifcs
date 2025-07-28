<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\pelabuhan_merak;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use League\Csv\Reader;
use League\Csv\Statement;
use App\Services\DataWarehouseService;
use App\Services\DataCalculationService;

class pelabuhanMerakController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $startYear = 2020;
        $validYears = range($startYear, $currentYear);
    
        $tahun = $request->input('tahun', null);
        $selectedTab = $request->input('tab', 'IFCS'); 
    
        if ($tahun && in_array($tahun, $validYears)) {
            $dataCalculationService = new DataCalculationService();
            $dataCalculationService->calculateAllForYear($tahun);
            $years = [$tahun];
        } else {
            $years = $validYears;
        }
    
        $pelabuhan_merak = pelabuhan_merak::whereIn('tahun', $years)->get();
        
        return view('pelabuhan.merak', [
            'pelabuhan_merak' => $pelabuhan_merak,
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
            return redirect()->back()->with('etl_merak_gagal', 'Tahun tidak valid.');
        }

        try {
            $etlService = new DataWarehouseService();
            $etlService->runEtlForYear($targetYear);
            
            $dataCalculationService = new DataCalculationService();
            $dataCalculationService->calculateAllForYear($targetYear);

            return redirect()->back()->with('etl_merak_sukses', "Proses ETL dan perhitungan data untuk tahun {$targetYear} berhasil dijalankan.");
        } catch (\Exception $e) {
            return redirect()->back()->with('etl_merak_gagal', "Terjadi kesalahan saat menjalankan ETL: " . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $record = pelabuhan_merak::findOrFail($id);
        return view('pelabuhan.edit-merak', compact('record'));
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
    
        $record = pelabuhan_merak::findOrFail($id);
        
        if ($record->update($validatedData)) {
            $tahunRedirect = $record->tahun; 
            $jenisRedirect = strtoupper($record->jenis);

            $dataCalculationService = new DataCalculationService();
            $dataCalculationService->calculateAllForYear($tahunRedirect);

            return redirect()->route('pelabuhan.merak.index', [
                'tahun' => $tahunRedirect,
                'tab' => $jenisRedirect
            ])->with('update_merak_success', 'Data berhasil diperbarui.');
        } else {
             return redirect()->back()->with('update_merak_fail', 'Gagal memperbarui data.');
        }
    }

    public function uploadcsvPelabuhanMerak(Request $request)
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
                pelabuhan_merak::updateOrCreate(
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
        
            return redirect()->back()->with('upload_merak_success', 'File CSV berhasil diupload');
        } catch (\Exception $e) {
            return redirect()->back()->with('upload_merak_fail', 'Terjadi kesalahan saat mengupload file CSV: ' . $e->getMessage());
        }
    } 

    public function delete(Request $request, $id)
    {
        try {
            $record = pelabuhan_merak::findOrFail($id);
            $tahunAffected = $record->tahun;
            $jenisAffected = $record->jenis;

            if ($record->delete()) {
                $dataCalculationService = new DataCalculationService();
                $dataCalculationService->calculateAllForYear($tahunAffected);
                
                return redirect()->route('pelabuhan.merak.index', [
                    'tahun' => $tahunAffected,
                    'tab' => strtoupper($jenisAffected)
                ])->with('delete_merak_success', 'Data berhasil dihapus.');
            } else {
                return redirect()->back()->with('delete_merak_fail', 'Gagal menghapus data.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('delete_merak_fail', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}