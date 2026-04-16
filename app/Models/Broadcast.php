<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    // INI TAMBAHANNYA
    protected $table = 'broadcast';

    protected $fillable = ['admin_id', 'judul', 'pesan'];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}