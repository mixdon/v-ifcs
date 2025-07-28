<?php

namespace App\Http\Controllers;

use App\Models\kinerja_ifcs; // Pastikan menggunakan nama model yang benar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Untuk transaksi database
use Illuminate\Support\Facades\Validator; // Untuk validasi manual
use Exception; // Untuk menangani exception umum

class KinerjaIfcsController extends Controller
{
    /**
     * Menampilkan daftar data Kinerja IFCS.
     * Dapat difilter berdasarkan tahun.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Mendapatkan semua tahun unik dari data kinerja_ifcs untuk dropdown filter
        $years = kinerja_ifcs::select(DB::raw('YEAR(created_at) as year'))
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year');
        
        // Mengambil tahun yang dipilih dari request, defaultnya null (tampilkan semua tahun)
        $selectedYear = $request->input('tahun');

        // Membangun query dasar untuk model kinerja_ifcs
        $query = kinerja_ifcs::query();

        // Jika tahun dipilih, tambahkan kondisi filter
        if ($selectedYear) {
            $query->where(DB::raw('YEAR(created_at)'), $selectedYear);
        }

        // Mendapatkan data kinerja IFCS berdasarkan query
        $kinerja_ifcs = $query->get();

        // Mengembalikan view 'kinerja-ifcs' dengan data yang diperlukan
        return view('ifcs/kinerja-ifcs', compact('kinerja_ifcs', 'years', 'selectedYear'));
    }

    /**
     * Menangani unggahan file CSV untuk data Kinerja IFCS.
     * Akan menghitung total secara otomatis setelah data diimpor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadcsv(Request $request)
    {
        // Validasi input: pastikan ada file CSV dan ukurannya tidak terlalu besar
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt|max:2048', // Max 2MB
        ], [
            'csv_file.required' => 'File CSV harus diunggah.',
            'csv_file.mimes' => 'File harus berformat CSV atau TXT.',
            'csv_file.max' => 'Ukuran file CSV tidak boleh melebihi 2MB.',
        ]);

        // Jika validasi gagal, kembalikan ke halaman sebelumnya dengan pesan error
        if ($validator->fails()) {
            return redirect()->back()->with('upload_kinerja_ifcs_fail', $validator->errors()->first());
        }

        // Mulai transaksi database untuk memastikan integritas data
        DB::beginTransaction();

        try {
            // Mendapatkan path lengkap dari file yang diunggah
            $path = $request->file('csv_file')->getRealPath();
            // Membaca file CSV menjadi array baris, setiap baris dipecah menjadi array kolom
            $data = array_map('str_getcsv', file($path));

            // Lewati baris header CSV (asumsi baris pertama adalah header)
            // $header = array_shift($data); // Uncomment jika perlu header

            // Mengecek apakah ada data yang diunggah (selain header)
            if (empty($data)) {
                DB::rollBack(); // Batalkan transaksi jika tidak ada data
                return redirect()->back()->with('upload_kinerja_ifcs_fail', 'File CSV kosong atau tidak ada data yang valid.');
            }

            // Iterasi setiap baris data dari CSV
            foreach ($data as $row) {
                // Pastikan baris memiliki jumlah kolom yang diharapkan (misal: Golongan + 12 bulan)
                // Sesuaikan '13' dengan jumlah kolom yang Anda harapkan
                if (count($row) < 13) { 
                    // Log error atau skip baris jika tidak sesuai format
                    continue; 
                }

                $golongan = $row[0];
                $tahun_data = date('Y'); // Menggunakan tahun saat ini sebagai default, sesuaikan jika tahun ada di CSV

                // Ekstrak data bulanan dan konversi ke integer (menghilangkan karakter non-angka seperti 'Rp ' atau '.')
                $januari = (int) preg_replace('/[^0-9]/', '', $row[1]);
                $februari = (int) preg_replace('/[^0-9]/', '', $row[2]);
                $maret = (int) preg_replace('/[^0-9]/', '', $row[3]);
                $april = (int) preg_replace('/[^0-9]/', '', $row[4]);
                $mei = (int) preg_replace('/[^0-9]/', '', $row[5]);
                $juni = (int) preg_replace('/[^0-9]/', '', $row[6]);
                $juli = (int) preg_replace('/[^0-9]/', '', $row[7]);
                $agustus = (int) preg_replace('/[^0-9]/', '', $row[8]);
                $september = (int) preg_replace('/[^0-9]/', '', $row[9]);
                $oktober = (int) preg_replace('/[^0-9]/', '', $row[10]);
                $november = (int) preg_replace('/[^0-9]/', '', $row[11]);
                $desember = (int) preg_replace('/[^0-9]/', '', $row[12]);

                // Hitung total dari semua bulan
                $total = $januari + $februari + $maret + $april + $mei + $juni +
                         $juli + $agustus + $september + oktober + $november + $desember;

                // Membuat atau memperbarui record di database
                // updateOrCreate akan mencoba mencari record berdasarkan 'golongan' dan 'tahun'
                // Jika ditemukan, akan diupdate. Jika tidak, akan dibuat baru.
                kinerja_ifcs::updateOrCreate(
                    [
                        'golongan' => $golongan,
                        'tahun' => $tahun_data 
                    ],
                    [
                        'januari' => $januari,
                        'februari' => $februari,
                        'maret' => $maret,
                        'april' => $april,
                        'mei' => $mei,
                        'juni' => $juni,
                        'juli' => $juli,
                        'agustus' => $agustus,
                        'september' => $september,
                        'oktober' => $oktober,
                        'november' => $november,
                        'desember' => $desember,
                        'total' => $total, // Total sudah dihitung di sini
                    ]
                );
            }

            // Commit transaksi jika semua operasi berhasil
            DB::commit();
            return redirect()->back()->with('upload_kinerja_ifcs_success', 'Data Kinerja IFCS berhasil diunggah.');

        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            // Kembalikan ke halaman sebelumnya dengan pesan error
            return redirect()->back()->with('upload_kinerja_ifcs_fail', 'Terjadi kesalahan saat mengunggah data: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui data Kinerja IFCS yang sudah ada.
     * Akan menghitung ulang total secara otomatis.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validasi input untuk form edit
        $validator = Validator::make($request->all(), [
            'golongan' => 'required|string|max:255',
            'januari' => 'nullable|numeric|min:0',
            'februari' => 'nullable|numeric|min:0',
            'maret' => 'nullable|numeric|min:0',
            'april' => 'nullable|numeric|min:0',
            'mei' => 'nullable|numeric|min:0',
            'juni' => 'nullable|numeric|min:0',
            'juli' => 'nullable|numeric|min:0',
            'agustus' => 'nullable|numeric|min:0',
            'september' => 'nullable|numeric|min:0',
            'oktober' => 'nullable|numeric|min:0',
            'november' => 'nullable|numeric|min:0',
            'desember' => 'nullable|numeric|min:0',
            'tahun' => 'required|numeric|min:2000|max:' . date('Y'), // Batasi tahun
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Mencari record kinerja_ifcs berdasarkan ID, jika tidak ditemukan akan melempar 404
            $kinerja = kinerja_ifcs::findOrFail($id);

            // Hitung ulang total dari nilai bulan yang baru di-submit
            $total = (int) $request->januari + (int) $request->februari + (int) $request->maret +
                     (int) $request->april + (int) $request->mei + (int) $request->juni +
                     (int) $request->juli + (int) $request->agustus + (int) $request->september +
                     (int) $request->oktober + (int) $request->november + (int) $request->desember;
            
            // Perbarui data di database
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

            // Redirect dengan pesan sukses
            return redirect()->back()->with('update_kinerja_ifcs_success', 'Data Kinerja IFCS berhasil diperbarui.');

        } catch (Exception $e) {
            // Tangani kesalahan saat memperbarui data
            return redirect()->back()->with('update_kinerja_ifcs_fail', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data Kinerja IFCS dari database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        try {
            // Mencari record dan menghapusnya
            $kinerja = kinerja_ifcs::findOrFail($id);
            $kinerja->delete();

            // Redirect dengan pesan sukses
            return redirect()->back()->with('delete_kinerja_ifcs_success', 'Data Kinerja IFCS berhasil dihapus.');
        } catch (Exception $e) {
            // Tangani kesalahan saat menghapus data
            return redirect()->back()->with('delete_kinerja_ifcs_fail', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
