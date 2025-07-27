<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimPelabuhan extends Model
{
    use HasFactory;

    protected $table = 'dim_pelabuhan';
    protected $primaryKey = 'pelabuhan_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'pelabuhan_id', 'nama_pelabuhan', 'lokasi_geografis'
    ];
}
