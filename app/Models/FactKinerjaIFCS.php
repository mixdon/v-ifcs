<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactKinerjaIFCS extends Model
{
    use HasFactory;

    protected $table = 'fact_kinerja_ifcs';
    protected $fillable = [
        'waktu_id', 'pelabuhan_id', 'layanan_id', 'kapal_id', 'golongan_id',
        'jumlah_produksi', 'total_pendapatan', 'total_laba'
    ];

    public function waktu()
    {
        return $this->belongsTo(DimWaktu::class, 'waktu_id');
    }

    public function pelabuhan()
    {
        return $this->belongsTo(DimPelabuhan::class, 'pelabuhan_id');
    }

    public function layanan()
    {
        return $this->belongsTo(DimLayanan::class, 'layanan_id');
    }

    public function kapal()
    {
        return $this->belongsTo(DimKapal::class, 'kapal_id');
    }

    public function golongan()
    {
        return $this->belongsTo(DimGolongan::class, 'golongan_id');
    }
}