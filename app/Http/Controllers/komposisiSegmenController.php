<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\komposisi_segmen;

class komposisiSegmenController extends Controller
{
    public function index(Request $request)
    {
    $currentYear = date('Y');
    $startYear = 2020;
    $validYears = range($startYear, $currentYear);
    $tahun = $request->input('tahun', null);

    $years = $tahun && in_array($tahun, $validYears) ? [$tahun] : $validYears;

    $komposisi_segmen = komposisi_segmen::whereIn('tahun', $years)->get();

    return view('ifcs.komposisi-segmen', [
        'komposisi_segmen' => $komposisi_segmen,
        'years' => $validYears,
        'selectedYear' => $tahun 
    ]);
    }
}