<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsensiMurid extends Model
{
    use HasFactory;

    protected $table = 'absensi_murids';

    protected $fillable = [
        'murid_id',
        'jadwal_id',
        'tanggal',
        'status',
        'kode_unik',
        'waktu_absen',
        'file_surat',
        'latitude',
        'longitude',
    ];

    public function murid()
    {
        return $this->belongsTo(User::class, 'murid_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalKelas::class, 'jadwal_id');
    }

    public function suratIzin()
{
    return $this->hasOne(SuratIzin::class, 'absensi_id');
}


    public function gpsLogs()
    {
        return $this->hasMany(GPSLog::class, 'absensi_id');
    }
}
