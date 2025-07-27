<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'golongan_id',
        'jenis_layanan_terkait',
        'biaya_per_unit',
        'tahun',
    ];

    protected $casts = [
        'biaya_per_unit' => 'decimal:2',
    ];

    public function golongan()
    {
        return $this->belongsTo(DimGolongan::class, 'golongan_id');
    }
}