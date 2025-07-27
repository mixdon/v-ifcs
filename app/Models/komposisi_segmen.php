<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class komposisi_segmen extends Model
{
    protected $table = 'komposisi_segmen';

    protected $fillable = [
        'golongan',
        'jenis',
        'ifcs_redeem',
        'nonifcs',
        'total',
        'tahun',
    ];
}
