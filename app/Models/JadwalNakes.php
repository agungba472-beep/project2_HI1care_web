<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalNakes extends Model
{
    use HasFactory;

    protected $table = 'jadwal_nakes';

    protected $fillable = [
        'nakes_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'kuota_pasien'
    ];

    public function nakes()
    {
        return $this->belongsTo(Nakes::class);
    }
}
