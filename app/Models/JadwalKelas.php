<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalKelas extends Model
{
    use HasFactory;
    protected $table = 'jadwal_kelas';

    protected $fillable = [
        'kelas_id',
        'guru_id',
        'hari',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'deskripsi',
        'deskripsi',
        'kode_unik',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function absensiGuru()
    {
        return $this->hasMany(AbsensiGuru::class, 'jadwal_id');
    }

    public function absensiMurid()
    {
        return $this->hasMany(AbsensiMurid::class, 'jadwal_id');
    }
}
