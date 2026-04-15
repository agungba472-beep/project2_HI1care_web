<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlarmArv extends Model
{
    protected $table = 'alarm_arv';

    protected $fillable = ['pasien_id','waktu','status','tanggal'];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }
}