<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaryHarian extends Model
{
    protected $table = 'diary_harian';

    protected $fillable = ['pasien_id','tanggal','kondisi','gejala','catatan'];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }
}
