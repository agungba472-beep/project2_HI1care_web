<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
    protected $fillable = ['pasien_id','nakes_id','tanggal','waktu','status'];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function nakes()
    {
        return $this->belongsTo(Nakes::class);
    }
}