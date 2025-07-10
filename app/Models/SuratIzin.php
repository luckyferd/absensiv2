<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuratIzin extends Model
{
    use HasFactory;

    protected $table = 'surat_izins';

    protected $fillable = [
        'murid_id',
        'absensi_id',
        'file_path',
        'keterangan',
        'status',
    ];

    public function murid()
    {
        return $this->belongsTo(User::class, 'murid_id');
    }

    public function absensi()
    {
        return $this->belongsTo(AbsensiMurid::class, 'absensi_id');
    }
}
