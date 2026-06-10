<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
    protected $table = 'konsultasi';

    protected $fillable = ['pasien_id', 'nakes_id', 'tanggal', 'waktu', 'status', 'chat_status', 'kategori'];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function nakes()
    {
        return $this->belongsTo(Nakes::class);
    }

    /**
     * Semua pesan chat dalam sesi konsultasi ini.
     */
    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    /**
     * Pesan chat terakhir dalam sesi konsultasi.
     */
    public function latestChat()
    {
        return $this->hasOne(Chat::class)->latestOfMany();
    }
}