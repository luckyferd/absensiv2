<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\SuratIzin;
use App\Models\JadwalKelas;
use App\Models\AbsensiMurid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardGuruController extends Controller
{
    public function index()
{
    $guru = Auth::user();
    $hariIni = Carbon::now()->toDateString();

    $semesterAktif = Semester::where('is_active', 1)->first();
    if (!$semesterAktif) {
        return response()->json([
            'message' => 'Semester aktif tidak ditemukan',
            'data' => []
        ], 404);
    }
    $absensiHariIni = AbsensiMurid::with(['murid', 'jadwal'])
        ->whereHas('jadwal', fn($q) => $q->where('guru_id', $guru->id))
        ->where('tanggal', $hariIni)
        ->get();

    $izinMenunggu = SuratIzin::where('status', 'Menunggu')
        ->whereHas('absensi.jadwal', fn($q) => $q->where('guru_id', $guru->id))
        ->with(['absensi.murid'])
        ->get();

        $kelasDiajar = Kelas::select('kelas.*')
        ->join('jadwal_kelas', 'kelas.id', '=', 'jadwal_kelas.kelas_id')
        ->where('jadwal_kelas.guru_id', $guru->id)
        ->where('kelas.semester_id', $semesterAktif->id)
        ->groupBy('kelas.id')
        ->get();
    
        // Nama hari disesuaikan dengan format yang dipakai di DB (misal: Monday, Tuesday)
        $todayName = now()->format('l'); // jika pakai bahasa Inggris
        $hariIni = now()->toDateString();

        $jadwalHariIni = JadwalKelas::where('guru_id', $guru->id)
        ->whereHas('kelas', fn($q) => $q->where('semester_id', $semesterAktif->id))
        ->where(function ($q) use ($hariIni, $todayName) {
            $q->where('tanggal', $hariIni)
            ->orWhere('hari', $todayName);
        })
        ->with('kelas')
        ->get();

    return response()->json([
        'message' => 'Dashboard Guru',
        'data' => [
            'absensi_hari_ini' => $absensiHariIni,
            'izin_menunggu'    => $izinMenunggu,
            'kelas_diajar'     => $kelasDiajar,
            'jadwal_hari_ini'  => $jadwalHariIni,
            'semester_aktif'   => $semesterAktif
        ]
        
    ]);
}

}
