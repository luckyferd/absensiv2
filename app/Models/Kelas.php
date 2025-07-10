<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory;
    protected $table = 'kelas';

    protected $fillable = ['name', 'guru_id'];
    protected static function booted()
    {
        static::addGlobalScope('orderByName', function ($query) {
            $query->orderBy('name');
        });
    }
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalKelas::class);
    }
    public function semester()
{
    return $this->belongsTo(Semester::class);
}
}
