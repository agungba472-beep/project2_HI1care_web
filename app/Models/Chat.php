<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chat';

    protected $fillable = ['pasien_id', 'nakes_id', 'konsultasi_id', 'sender', 'pesan', 'file_url', 'file_type'];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function nakes()
    {
        return $this->belongsTo(Nakes::class);
    }

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class);
    }
}
