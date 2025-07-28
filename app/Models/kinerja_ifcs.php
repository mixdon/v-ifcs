<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kinerja_ifcs extends Model
{
    use HasFactory;

    protected $table = 'kinerja_ifcs';

    protected $fillable = [
        'golongan',
        'januari',
        'februari',
        'maret',
        'april',
        'mei',
        'juni',
        'juli',
        'agustus',
        'september',
        'oktober',
        'november',
        'desember',
        'total',
        'tahun',
    ];
}