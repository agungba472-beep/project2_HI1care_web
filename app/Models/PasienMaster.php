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
        'is_registered',
        'jenis_kelamin',
        'berat_badan',
        'tinggi_badan',
    ];

    public function pasien()
    {
        return $this->hasOne(Pasien::class);
    }

    /**
     * Hitung umur otomatis berdasarkan tgl_lahir
     */
    public function getUmurFormattedAttribute()
    {
        if (!$this->tgl_lahir) {
            return '-';
        }
        $dob = \Carbon\Carbon::parse($this->tgl_lahir);
        $diff = $dob->diff(now());
        
        if ($diff->y > 0) {
            return "{$diff->y} Tahun" . ($diff->m > 0 ? ", {$diff->m} Bulan" : "");
        } elseif ($diff->m > 0) {
            return "{$diff->m} Bulan";
        }
        return "{$diff->d} Hari";
    }

    /**
     * Hitung BMI (Body Mass Index) secara otomatis
     * Mengembalikan array berisi nilai BMI, label, dan warna
     */
    public function getBmiDataAttribute()
    {
        if (!$this->berat_badan || !$this->tinggi_badan) {
            return null;
        }

        $tinggiMeter = $this->tinggi_badan / 100;
        if ($tinggiMeter <= 0) return null;

        $bmi = $this->berat_badan / ($tinggiMeter * $tinggiMeter);
        $bmi = round($bmi, 1);

        if ($bmi < 18.5) {
            $label = 'Underweight';
            $color = '#d97706'; // warning
        } elseif ($bmi < 25) {
            $label = 'Normal';
            $color = '#059669'; // success
        } elseif ($bmi < 30) {
            $label = 'Overweight';
            $color = '#d97706'; // warning
        } else {
            $label = 'Obese';
            $color = '#dc2626'; // danger
        }

        return [
            'value' => $bmi,
            'label' => $label,
            'color' => $color
        ];
    }
}
