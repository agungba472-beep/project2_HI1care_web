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
        'status_kepatuhan',
        'nada_dering',
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
    public function kepatuhan()
    {
        return $this->hasMany(Kepatuhan::class);
    }

    public function alarm()
    {
        return $this->hasMany(AlarmArv::class);
    }

    public function diary()
    {
        return $this->hasMany(DiaryHarian::class);
    }

    public function diaryHarian()
    {
        return $this->hasMany(DiaryHarian::class);
    }

    public function refill()
    {
        return $this->hasMany(RefillObat::class);
    }

    public function refillObat()
    {
        return $this->hasMany(RefillObat::class);
    }

    public function chat()
    {
        return $this->hasMany(Chat::class);
    }

    /**
     * Hitung otomatis Fase Pengobatan berdasarkan lama bergabung
     * - < 6 bulan = Inisiasi
     * - >= 6 bulan = Lanjutan
     */
    public function getFasePengobatanAttribute()
    {
        if (!$this->created_at) {
            return 'Inisiasi';
        }

        $months = $this->created_at->diffInMonths(now());
        
        if ($months >= 6) {
            if ($this->status_kepatuhan === 'hijau') {
                return 'Lanjutan (Maintenance)';
            }
            return 'Lanjutan (Pemantauan Ketat)';
        }

        return 'Inisiasi';
    }
}

