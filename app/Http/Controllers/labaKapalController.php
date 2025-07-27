<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\laba_kapal; // Corrected model name
use League\Csv\Reader;
use League\Csv\Statement;

class labaKapalController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $startYear = 2020;
        $validYears = range($startYear, $currentYear);
    
        $tahun = $request->input('tahun', null);
        // Memastikan $selectedTab selalu terdefinisi dengan nilai default 'Pendapatan'
        $selectedTab = $request->input('tab', 'Pendapatan'); 

        if ($tahun && in_array($tahun, $validYears)) {
            $this->simpanDataTotalPendapatan($tahun);
            $this->simpanDataTotalLabaRugi($tahun);
            $years = [$tahun];
        } else {
            $years = $validYears;
        }
    
        $laba_kapal = laba_kapal::whereIn('tahun', $years)->get();
    
        // Memanggil calculateAndSaveTotal di model untuk setiap record
        foreach ($laba_kapal as $record) {
            $record->calculateAndSaveTotal();
        }
    
        return view('ifcs.laba-kapal', [
            'laba_kapal' => $laba_kapal,
            'years' => $validYears,
            'selectedYear' => $tahun,
            'selectedTab' => $selectedTab
        ]);
    }    

    public function edit($id)
    {
        $record = laba_kapal::findOrFail($id);
        return view('ifcs.edit-laba', compact('record'));
    }

    public function updatePost(Request $request, $id)
    {
        $validatedData = $request->validate([
            'kapal' => 'required|string|max:255',
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
    
        $record = laba_kapal::findOrFail($id);
        
        if ($record->update($validatedData)) {
            switch (strtolower($record->jenis)) {
                case 'pendapatan':
                    $this->simpanDataTotalPendapatan($record->tahun);
                    break;
                case 'labarugi':
                    $this->simpanDataTotalLabaRugi($record->tahun);
                    break;
            }

            $tahunRedirect = $record->tahun; 
            $jenisRedirect = (strtolower($record->jenis) === 'pendapatan') ? 'Pendapatan' : 'LabaRugiReal';
        
            return redirect()->route('laba-kapal.index', [
                'tahun' => $tahunRedirect,
                'tab' => $jenisRedirect
            ])->with('laba_kapal_update_success', 'Data berhasil diperbarui.');
        } else {
            return redirect()->back()->with('laba_kapal_update_fail', 'Gagal memperbarui data.');
        }
    }

    public function uploadcsvKapal(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        if (!$request->hasFile('csv_file')) {
            return redirect()->back()->with('laba_kapal_upload_fail', 'Tidak ada file yang terpilih.');
        }

        try {
            $path = $request->file('csv_file')->getRealPath();
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0);
            $csv->setDelimiter(';');

            $records = (new Statement())->process($csv);

            $uploadedYears = [];
            foreach ($records as $record) {
                $labaKapalRecord = laba_kapal::updateOrCreate(
                    ['tahun' => $record['tahun'], 'kapal' => $record['kapal'], 'jenis' => $record['jenis']],
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
                $uploadedYears[] = $labaKapalRecord->tahun;
            }

            $uniqueUploadedYears = array_unique($uploadedYears);
            foreach ($uniqueUploadedYears as $tahun) {
                $this->simpanDataTotalPendapatan($tahun);
                $this->simpanDataTotalLabaRugi($tahun);
            }
        
            return redirect()->back()->with('laba_kapal_upload_success', 'File CSV berhasil diupload');
        } catch (\Exception $e) {
            return redirect()->back()->with('laba_kapal_upload_fail', 'Terjadi kesalahan saat mengupload file CSV: ' . $e->getMessage());
        }
    }

    public function simpanDataTotalPendapatan($tahun)
    {
        if (!$tahun) {
            return;
        }

        $months = [
            'januari', 'februari', 'maret', 'april', 'mei',
            'juni', 'juli', 'agustus', 'september', 'oktober',
            'november', 'desember'
        ];

        $kapal = ['BATU MANDI', 'JATRA III', 'LEGUNDI', 'PORT LINK I', 'PORT LINK III', 'SEBUKU'];

        $totals = [];
        foreach ($months as $month) {
            $totals[$month] = laba_kapal::where('tahun', $tahun)
                ->where('jenis', 'pendapatan')
                ->whereIn('kapal', $kapal) 
                ->sum($month);
        }

        $totalPendapatan = array_sum($totals);

        laba_kapal::updateOrCreate(
            ['kapal' => 'Total', 'jenis'=>'pendapatan' ,'tahun' => $tahun], 
            array_merge($totals, ['total' => $totalPendapatan])
        );
    }

    public function simpanDataTotalLabaRugi($tahun)
    {
        if (!$tahun) {
            return;
        }

        $months = [
            'januari', 'februari', 'maret', 'april', 'mei',
            'juni', 'juli', 'agustus', 'september', 'oktober',
            'november', 'desember'
        ];

        $kapal = ['BATU MANDI', 'JATRA III', 'LEGUNDI', 'PORT LINK I', 'PORT LINK III', 'SEBUKU'];

        $totals = [];
        foreach ($months as $month) {
            $totals[$month] = laba_kapal::where('tahun', $tahun)
                ->where('jenis', 'labarugi')
                ->whereIn('kapal', $kapal) 
                ->sum($month);
        }

        $totalLabaRugi = array_sum($totals);

        laba_kapal::updateOrCreate(
            ['kapal' => 'Total', 'jenis'=>'labarugi' ,'tahun' => $tahun], 
            array_merge($totals, ['total' => $totalLabaRugi])
        );
    }

    public function delete(Request $request, $id)
    {
        try {
            $record = laba_kapal::findOrFail($id);
            $tahunAffected = $record->tahun;
            $jenisAffected = $record->jenis;

            if ($record->delete()) {
                switch (strtolower($jenisAffected)) {
                    case 'pendapatan':
                        $this->simpanDataTotalPendapatan($tahunAffected);
                        break;
                    case 'labarugi':
                        $this->simpanDataTotalLabaRugi($tahunAffected);
                        break;
                }
                
                $jenisRedirect = (strtolower($jenisAffected) === 'pendapatan') ? 'Pendapatan' : 'LabaRugiReal';

                return redirect()->route('laba-kapal.index', [
                    'tahun' => $tahunAffected,
                    'tab' => $jenisRedirect
                ])->with('laba_kapal_delete_success', 'Data berhasil dihapus.');
            } else {
                return redirect()->back()->with('laba_kapal_delete_fail', 'Gagal menghapus data.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('laba_kapal_delete_fail', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}