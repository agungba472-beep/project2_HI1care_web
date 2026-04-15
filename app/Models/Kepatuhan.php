<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kepatuhan extends Model
{
    public $timestamps = false;

    protected $fillable = ['pasien_id','status','last_update'];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }
}
