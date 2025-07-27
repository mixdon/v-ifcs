<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimKapal extends Model
{
    use HasFactory;

    protected $table = 'dim_kapal';
    protected $primaryKey = 'kapal_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'kapal_id',
        'nama_kapal',
        'tipe_kapal',
        'kapasitas'
    ];
}
