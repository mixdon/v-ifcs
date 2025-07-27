<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimWaktu extends Model
{
    use HasFactory;

    protected $table = 'dim_waktu';
    protected $primaryKey = 'waktu_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'waktu_id', 'tanggal', 'hari', 'bulan', 'bulan_numerik', 'tahun', 'kuartal', 'semester'
    ];
}