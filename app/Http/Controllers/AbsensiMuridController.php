<?php

namespace App\Http\Controllers;

use App\Models\AbsensiMurid;
use App\Models\JadwalKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsensiMuridController extends Controller
{
    public function guruIndex()
{
    $guruId = Auth::id();

    $absensi = AbsensiMurid::whereHas('jadwal', fn($q) =>
        $q->where('guru_id', $guruId)
    )
    ->with(['murid', 'jadwal'])
    ->orderByDesc('tanggal')
    ->get();

    return response()->json($absensi);
}

    public function index()
    {
        $muridId = Auth::id();
        return AbsensiMurid::where('murid_id', $muridId)
            ->with('jadwal')
            ->orderByDesc('tanggal')
            ->get();
    }

    public function show($id)
    {
        $muridId = Auth::id();
        $absensi = AbsensiMurid::where('murid_id', $muridId)->findOrFail($id);
        return response()->json($absensi);
    }
   
    
    public function store(Request $request)
    {
        $request->validate([
            'kode_unik'  => 'required|string',
            'jadwal_id'  => 'required|exists:jadwal_kelas,id',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $murid = Auth::user();

        // Cek apakah jadwal valid
        $jadwal = JadwalKelas::findOrFail($request->jadwal_id);

        // Validasi kode unik
        if ($request->kode_unik !== $jadwal->kode_unik) {
            return response()->json(['message' => 'Kode unik salah!'], 422);
        }

        $tanggalHariIni = now()->toDateString();

        // Cek apakah sudah absen hari ini untuk jadwal tersebut
        $already = AbsensiMurid::where('murid_id', $murid->id)
            ->where('jadwal_id', $jadwal->id)
            ->where('tanggal', $tanggalHariIni)
            ->exists();

        if ($already) {
            return response()->json([
                'message' => 'Anda sudah melakukan absensi untuk jadwal ini hari ini.'
            ], 409);
        }

        $now = now();
        $status = 'Hadir';

        // Jika lewat dari jam mulai, tandai Alpha
        if ($now->format('H:i') > $jadwal->jam_mulai) {
            $status = 'Alpha';
        }

        // Jika ada file surat izin, set sebagai Izin
        $fileSurat = null;
        if ($request->hasFile('file_surat')) {
            $status = 'Izin';
            $fileSurat = $request->file('file_surat')->store('surat_izin', 'public');
        }

        // Simpan absensi
        $absensi = AbsensiMurid::create([
            'murid_id'    => $murid->id,
            'jadwal_id'   => $jadwal->id,
            'tanggal'     => $tanggalHariIni,
            'status'      => $status,
            'kode_unik'   => $request->kode_unik,
            'waktu_absen' => $now->format('H:i:s'),
            'file_surat'  => $fileSurat,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
        ]);

        return response()->json([
            'message' => 'Absensi berhasil direkam.',
            'data'    => $absensi
        ]);
    }
    public function guruStoreBulk(Request $request)
{
    $request->validate([
        'jadwal_id' => 'required|exists:jadwal_kelas,id',
        'status'    => 'required|array',
    ]);

    $jadwal = JadwalKelas::findOrFail($request->jadwal_id);

    // Validasi: guru hanya bisa absen di jadwal miliknya
    if ($jadwal->guru_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $absensiTercatat = [];

    foreach ($request->status as $muridId => $status) {
        // Cek sudah absen atau belum
        $sudahAbsen = AbsensiMurid::where('murid_id', $muridId)
            ->where('jadwal_id', $request->jadwal_id)
            ->where('tanggal', now()->toDateString())
            ->exists();

        if ($sudahAbsen) {
            continue;
        }

        $absen = AbsensiMurid::create([
            'murid_id'    => $muridId,
            'jadwal_id'   => $request->jadwal_id,
            'tanggal'     => now()->toDateString(),
            'status'      => $status,
            'waktu_absen' => now()->format('H:i:s'),
        ]);

        $absensiTercatat[] = $absen;
    }

    return response()->json([
        'message' => 'Absensi murid berhasil dicatat oleh guru.',
        'data'    => $absensiTercatat
    ]);
}

}
