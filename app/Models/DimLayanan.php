<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimLayanan extends Model
{
    use HasFactory;

    protected $table = 'dim_layanan';
    protected $primaryKey = 'layanan_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'layanan_id',
        'jenis_layanan',
        'deskripsi'
    ];
}
