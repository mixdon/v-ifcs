<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimGolongan extends Model
{
    use HasFactory;

    protected $table = 'dim_golongan';
    protected $primaryKey = 'golongan_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'golongan_id', 'nama_golongan', 'deskripsi'
    ];
}