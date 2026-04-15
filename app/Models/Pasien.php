<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Pasien extends Model
{
    use HasFactory;
    

    protected $table = 'pasien';

    protected $fillable = [
        'user_id',
        'pasien_master_id',
        'status_kepatuhan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function master()
    {
        return $this->belongsTo(PasienMaster::class, 'pasien_master_id');
    }

    // RELASI FITUR
    public function alarm()
    {
        return $this->hasMany(AlarmArv::class);
    }

    public function diary()
    {
        return $this->hasMany(DiaryHarian::class);
    }

    public function refill()
    {
        return $this->hasMany(RefillObat::class);
    }

    public function chat()
    {
        return $this->hasMany(Chat::class);
    }
}

