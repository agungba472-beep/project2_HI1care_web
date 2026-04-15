<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefillObat extends Model
{
    // Tambahkan baris ini agar Laravel tidak mencari 'refill_obats'
    protected $table = 'refill_obat';
}
