<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefillObat extends Model
{
    protected $table = 'refill_obat';

    protected $fillable = [
        'pasien_id',
        'tanggal_refill',
        'tanggal_diambil',
        'siklus_ke',
        'status',
        'foto_bukti',
        'admin_id'
    ];

    // INI TAMBAHANNYA
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }
}