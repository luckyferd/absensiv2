<?php

namespace App\Http\Controllers;

use App\Models\AbsensiGuru;
use App\Models\JadwalKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiGuruController extends Controller
{
    public function index()
    {
        $guruId = Auth::id();
        return AbsensiGuru::where('guru_id', $guruId)
            ->with('jadwal')
            ->get();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_kelas,id',
            'tanggal'   => 'required|date',
            'status'    => 'required|in:Hadir,Alpha,Izin',
        ]);

        $guruId = Auth::id();

        // Validasi jadwal milik guru login
        $jadwal = JadwalKelas::where('id', $request->jadwal_id)
            ->where('guru_id', $guruId)
            ->first();

        if (!$jadwal) {
            return response()->json(['message' => 'Jadwal tidak valid'], 403);
        }

        $absensi = AbsensiGuru::updateOrCreate(
            [
                'guru_id'   => $guruId,
                'jadwal_id' => $request->jadwal_id,
                'tanggal'   => $request->tanggal,
            ],
            [
                'status' => $request->status,
            ]
        );

        return response()->json(['message' => 'Absensi guru tersimpan', 'data' => $absensi]);
    }

    public function show($id)
    {
        $absensi = AbsensiGuru::where('guru_id', Auth::id())->findOrFail($id);
        return response()->json($absensi);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $absensi = AbsensiGuru::where('guru_id', Auth::id())->findOrFail($id);

        $request->validate([
            'status' => 'required|in:Hadir,Alpha,Izin',
        ]);

        $absensi->update(['status' => $request->status]);

        return response()->json(['message' => 'Absensi guru diperbarui', 'data' => $absensi]);
    }

    public function destroy($id)
    {
        $absensi = AbsensiGuru::where('guru_id', Auth::id())->findOrFail($id);
        $absensi->delete();

        return response()->json(['message' => 'Absensi guru dihapus']);
    }
}
