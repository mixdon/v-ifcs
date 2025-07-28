<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\market_lintasan;

class marketLintasanController extends Controller
{        
    public function index(Request $request)
    {
    $currentYear = date('Y');
    $startYear = 2020;
    $validYears = range($startYear, $currentYear);
    $tahun = $request->input('tahun', null);

    $years = $tahun && in_array($tahun, $validYears) ? [$tahun] : $validYears;

    // Tidak ada lagi logika perhitungan di sini
    $market_lintasan = market_lintasan::whereIn('tahun', $years)->get();

    return view('ifcs.market-lintasan', [
        'market_lintasan' => $market_lintasan,
        'years' => $validYears,
        'selectedYear' => $tahun 
    ]);
    }      
}