<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
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

    public function calculateAndSaveTotal()
    {
        $total = $this->januari + $this->februari + $this->maret + $this->april + $this->mei + $this->juni + $this->juli + $this->agustus + $this->september + $this->oktober + $this->november + $this->desember;

        $this->total = $total;
        $this->save();
    }

}