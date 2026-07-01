<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
        'status_akun',
        'photo',
        'no_hp',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    protected $appends = ['photo_url'];

    /**
     * Accessor: URL foto profil lengkap.
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return url('file/' . $this->photo);
        }
        return null;
    }

    // RELASI
    public function pasien()
    {
        return $this->hasOne(Pasien::class);
    }

    public function nakes()
    {
        return $this->hasOne(Nakes::class);
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class);
    }
}