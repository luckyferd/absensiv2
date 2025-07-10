<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GPSLog extends Model
{
    use HasFactory;

    protected $table = 'gps_logs';

    protected $fillable = [
        'absensi_id',
        'latitude',
        'longitude',
    ];

    public function absensi()
    {
        return $this->belongsTo(AbsensiMurid::class, 'absensi_id');
    }
}
