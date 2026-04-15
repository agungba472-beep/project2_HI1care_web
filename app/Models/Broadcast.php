<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    protected $fillable = ['admin_id','pesan'];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
