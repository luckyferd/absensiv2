<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tahun_ajaran',
        'is_active',
    ];

    public function kelass()
    {
        return $this->hasMany(Kelas::class);
    }
}
