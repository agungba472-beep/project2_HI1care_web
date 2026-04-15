<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModulEdukasi extends Model
{
    protected $table = 'modul_edukasi';

    protected $fillable = ['judul','konten'];
}
