<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefillObat extends Model
{
    // Tambahkan baris ini agar Laravel tidak mencari 'refill_obats'
    protected $table = 'refill_obat';

    // Tambahkan baris ini agar seeder bisa memasukkan data
    protected $fillable = [
        'pasien_id',
        'tanggal_refill',
        'siklus_ke',
        'status'
    ];
}