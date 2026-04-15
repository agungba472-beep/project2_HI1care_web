<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['pasien_id','nakes_id','sender','pesan'];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function nakes()
    {
        return $this->belongsTo(Nakes::class);
    }
}
