<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    use HasFactory;

    protected $fillable = [
        'golongan_id',
        'jenis_layanan',
        'tarif_amount',
        'tahun',
    ];

    protected $casts = [
        'tarif_amount' => 'decimal:2',
    ];

    public function golongan()
    {
        return $this->belongsTo(DimGolongan::class, 'golongan_id');
    }
}
