<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Nakes extends Model
{
    use HasFactory;

    protected $table = 'nakes';

    protected $fillable = [
        'user_id',
        'nama',
        'profesi',
        'no_hp'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function konsultasi()
    {
        return $this->hasMany(Konsultasi::class);
    }
}

