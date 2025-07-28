<?php

namespace App\Http\Controllers;

use App\Models\kinerja_ifcs; // Menggunakan nama model 'kinerja_ifcs'
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class KinerjaIfcsController extends Controller
{
    /**
     * Menampilkan halaman data Kinerja IFCS dengan filter tahun.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil daftar tahun unik dari database untuk filter
        $years = kinerja_ifcs::select(DB::raw('YEAR(created_at) as year'))
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year');
        
        $selectedYear = $request->input('tahun');

        $query = kinerja_ifcs::query();

        if ($selectedYear) {
            $query->where(DB::raw('YEAR(created_at)'), $selectedYear);
        }

        $kinerja_ifcs = $query->get();

        return view('kinerja-ifcs', compact('kinerja_ifcs', 'years', 'selectedYear'));
    }

    /**
     * Menangani proses unggah file CSV dan menghitung total.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadcsv(Request $request)
    {
        // Validasi bahwa file yang diunggah adalah CSV
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('upload_kinerja_ifcs_fail', 'File harus berformat CSV dan tidak boleh melebihi 2MB.');
        }

        try {
            $path = $request->file('csv_file')->getRealPath();
            $data = array_map('str_getcsv', file($path));

            // Lewati header CSV
            $header = array_shift($data);

            DB::beginTransaction();

            foreach ($data as $row) {
                // Pastikan baris data memiliki 13 kolom (Golongan + 12 bulan)
                if (count($row) < 13) {
                    continue; 
                }

                $golongan = $row[0];
                $monthlyData = array_slice($row, 1, 12);
                $total = 0;

                // Hitung total dari data bulanan
                foreach ($monthlyData as $value) {
                    // Konversi nilai ke integer sebelum menjumlahkan
                    $total += (int) preg_replace('/[^0-9]/', '', $value);
                }

                // Buat atau perbarui record
                kinerja_ifcs::updateOrCreate(
                    [
                        'golongan' => $golongan,
                        'tahun' => date('Y') // Asumsi tahun saat ini
                    ],
                    [
                        'januari' => (int) preg_replace('/[^0-9]/', '', $row[1]),
                        'februari' => (int) preg_replace('/[^0-9]/', '', $row[2]),
                        'maret' => (int) preg_replace('/[^0-9]/', '', $row[3]),
                        'april' => (int) preg_replace('/[^0-9]/', '', $row[4]),
                        'mei' => (int) preg_replace('/[^0-9]/', '', $row[5]),
                        'juni' => (int) preg_replace('/[^0-9]/', '', $row[6]),
                        'juli' => (int) preg_replace('/[^0-9]/', '', $row[7]),
                        'agustus' => (int) preg_replace('/[^0-9]/', '', $row[8]),
                        'september' => (int) preg_replace('/[^0-9]/', '', $row[9]),
                        'oktober' => (int) preg_replace('/[^0-9]/', '', $row[10]),
                        'november' => (int) preg_replace('/[^0-9]/', '', $row[11]),
                        'desember' => (int) preg_replace('/[^0-9]/', '', $row[12]),
                        'total' => $total, // Total sudah dihitung di atas
                    ]
                );
            }

            DB::commit();
            return redirect()->back()->with('upload_kinerja_ifcs_success', 'Data Kinerja IFCS berhasil diunggah.');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('upload_kinerja_ifcs_fail', 'Terjadi kesalahan saat mengunggah data: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui data Kinerja IFCS dan menghitung ulang total.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'golongan' => 'required|string|max:255',
            'januari' => 'nullable|numeric',
            'februari' => 'nullable|numeric',
            'maret' => 'nullable|numeric',
            'april' => 'nullable|numeric',
            'mei' => 'nullable|numeric',
            'juni' => 'nullable|numeric',
            'juli' => 'nullable|numeric',
            'agustus' => 'nullable|numeric',
            'september' => 'nullable|numeric',
            'oktober' => 'nullable|numeric',
            'november' => 'nullable|numeric',
            'desember' => 'nullable|numeric',
            'tahun' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $kinerja = kinerja_ifcs::findOrFail($id);

            // Hitung ulang total dari data bulanan yang baru
            $total = (int) $request->januari + (int) $request->februari + (int) $request->maret +
                     (int) $request->april + (int) $request->mei + (int) $request->juni +
                     (int) $request->juli + (int) $request->agustus + (int) $request->september +
                     (int) $request->oktober + (int) $request->november + (int) $request->desember;
            
            // Perbarui data
            $kinerja->update([
                'golongan' => $request->golongan,
                'januari' => $request->januari,
                'februari' => $request->februari,
                'maret' => $request->maret,
                'april' => $request->april,
                'mei' => $request->mei,
                'juni' => $request->juni,
                'juli' => $request->juli,
                'agustus' => $request->agustus,
                'september' => $request->september,
                'oktober' => $request->oktober,
                'november' => $request->november,
                'desember' => $request->desember,
                'tahun' => $request->tahun,
                'total' => $total, // Gunakan total yang sudah dihitung
            ]);

            return redirect()->back()->with('update_kinerja_ifcs_success', 'Data Kinerja IFCS berhasil diperbarui.');

        } catch (Exception $e) {
            return redirect()->back()->with('update_kinerja_ifcs_fail', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data Kinerja IFCS.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        try {
            $kinerja = kinerja_ifcs::findOrFail($id);
            $kinerja->delete();

            return redirect()->back()->with('delete_kinerja_ifcs_success', 'Data Kinerja IFCS berhasil dihapus.');
        } catch (Exception $e) {
            return redirect()->back()->with('delete_kinerja_ifcs_fail', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}