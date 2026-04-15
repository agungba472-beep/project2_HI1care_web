<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class PasienMaster extends Model
{
    use HasFactory;

    protected $table = 'pasien_master';

    protected $fillable = [
        'no_reg_hiv',
        'nama',
        'tgl_lahir',
        'alamat',
        'is_registered'
    ];

    public function pasien()
    {
        return $this->hasOne(Pasien::class);
    }
}
