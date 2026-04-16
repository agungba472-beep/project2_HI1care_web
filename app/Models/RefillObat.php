<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefillObat extends Model
{
    protected $table = 'refill_obat';

    protected $fillable = [
        'pasien_id',
        'tanggal_refill',
        'siklus_ke',
        'status'
    ];

    // INI TAMBAHANNYA
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }
}