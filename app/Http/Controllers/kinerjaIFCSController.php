<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\kinerja_ifcs; // Menggunakan model kinerja_ifcs
use League\Csv\Reader; 
use League\Csv\Statement;

class KinerjaIfcsController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $startYear = 2020;
        $validYears = range($startYear, $currentYear);
        $tahun = $request->input('tahun', null);
    
        $yearsToFetch = $tahun && in_array($tahun, $validYears) ? [$tahun] : $validYears;
    
        foreach ($yearsToFetch as $y) {
            $this->simpanDataTotalKinerja($y);
        }
    
        $kinerja_ifcs = kinerja_ifcs::whereIn('tahun', $yearsToFetch)->get();
        
        return view('ifcs.kinerja-ifcs', [
            'kinerja_ifcs' => $kinerja_ifcs,
            'years' => $validYears,
            'selectedYear' => $tahun 
        ]);
    }    

    public function edit($id)
    {
        $record = kinerja_ifcs::findOrFail($id);
        return view('ifcs.edit-kinerja', compact('record')); 
    }

    public function updatePost(Request $request, $id)
    {
        $validatedData = $request->validate([
            'golongan' => 'required|string|max:255',
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
    
        $record = kinerja_ifcs::findOrFail($id);
        $record->update($validatedData);

        $this->simpanDataTotalKinerja($record->tahun);
    
        $tahunRedirect = $record->tahun; 

        return redirect()->route('kinerja-ifcs.index', ['tahun' => $tahunRedirect])
                         ->with('update_kinerja_ifcs_success', 'Data berhasil diperbarui.');
    }

    public function uploadcsvKinerja(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);
        if (!$request->hasFile('csv_file')) {
            return redirect()->back()->with('upload_kinerja_ifcs_fail', 'Tidak ada file yang terpilih.');
        }
        try {
            $path = $request->file('csv_file')->getRealPath();
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0);
            $csv->setDelimiter(';');
            $records = (new Statement())->process($csv);

            $uploadedYears = [];
            foreach ($records as $record) {
                $kinerjaIfcsRecord = kinerja_ifcs::updateOrCreate(
                    ['tahun' => $record['tahun'], 'golongan' => $record['golongan']],
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
                $uploadedYears[] = $kinerjaIfcsRecord->tahun;
            }

            $uniqueUploadedYears = array_unique($uploadedYears);
            foreach ($uniqueUploadedYears as $tahun) {
                $this->simpanDataTotalKinerja($tahun);
            }

            return redirect()->back()->with('upload_kinerja_ifcs_success', 'File CSV berhasil diupload');
        } catch (\Exception $e) {
            return redirect()->back()->with('upload_kinerja_ifcs_fail', 'Terjadi kesalahan saat mengupload file CSV: ' . $e->getMessage());
        }
    }

    public function simpanDataTotalKinerja($tahun)
    {
        if (!$tahun) {
            return;
        }

        $months = [
            'januari', 'februari', 'maret', 'april', 'mei', 
            'juni', 'juli', 'agustus', 'september', 'oktober', 
            'november', 'desember'
        ];

        $golongans = ['IVA', 'IVB', 'VA', 'VB', 'VIA', 'VIB', 'VII', 'VIII', 'IX'];

        $totals = [];
        foreach ($months as $month) {
            $totals[$month] = kinerja_ifcs::where('tahun', $tahun)
                ->whereIn('golongan', $golongans)
                ->sum($month);
        }

        $totalIfcs = array_sum($totals);

        kinerja_ifcs::updateOrCreate(
            ['golongan' => 'Total', 'tahun' => $tahun],
            array_merge($totals, ['total' => $totalIfcs])
        );
    }

    public function delete(Request $request, $id)
    {
        try {
            $record = kinerja_ifcs::findOrFail($id);
            $tahunAffected = $record->tahun;

            if ($record->delete()) {
                $this->simpanDataTotalKinerja($tahunAffected);
                
                return redirect()->route('kinerja-ifcs.index', ['tahun' => $tahunAffected])
                                 ->with('delete_kinerja_ifcs_success', 'Data berhasil dihapus.');
            } else {
                return redirect()->back()->with('delete_kinerja_ifcs_fail', 'Gagal menghapus data.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('delete_kinerja_ifcs_fail', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}