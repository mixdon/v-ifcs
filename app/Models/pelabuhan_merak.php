<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class pelabuhan_merak extends Model
{
    protected $table = 'pelabuhan_merak';
    protected $primaryKey = 'id'; 

    protected $fillable = [
        'golongan',
        'jenis',
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

    public function calculateAndSaveTotal()
    {
        $total = $this->januari + $this->februari + $this->maret + $this->april + $this->mei + $this->juni + $this->juli + $this->agustus + $this->september + $this->oktober + $this->november + $this->desember;

        $this->total = $total;
        $this->save();
    }
}
