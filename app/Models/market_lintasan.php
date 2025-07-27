<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class market_lintasan extends Model
{
    protected $table = 'market_lintasan';

    protected $fillable = [
        'golongan',
        'jenis',
        'merak',
        'bakauheni',
        'gabungan',
        'tahun',
    ];
    
    public function getPriceFormattedAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }
}
