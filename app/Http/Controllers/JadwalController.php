<?php

namespace App\Http\Controllers;


use App\Models\JadwalKelas;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jadwal = JadwalKelas::with(['kelas', 'guru'])->get();
        return response()->json($jadwal);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'    => 'required|exists:kelas,id',
            'guru_id'     => 'required|exists:users,id',
            'hari'        => 'nullable|string',
            'tanggal'     => 'nullable|date',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
            'deskripsi'   => 'nullable|string',
        ]);
    
        // Generate kode unik yang dijamin tidak duplikat
        do {
            $kodeUnik = strtoupper(Str::random(6));
        } while (JadwalKelas::where('kode_unik', $kodeUnik)->exists());
    
        // Simpan jadwal baru
        $jadwal = JadwalKelas::create([
            'kelas_id'    => $request->kelas_id,
            'guru_id'     => $request->guru_id,
            'hari'        => $request->hari,
            'tanggal'     => $request->tanggal,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'deskripsi'   => $request->deskripsi,
            'kode_unik'   => $kodeUnik,
        ]);
    
        return response()->json([
            'message' => 'Jadwal dibuat',
            'jadwal'  => $jadwal
        ], 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $jadwal = JadwalKelas::with(['kelas', 'guru'])->findOrFail($id);

        // Bisa bikin check auth → kalau user = guru dari jadwal → show
        
        if (auth()->id() !== $jadwal->guru_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
    
        return response()->json($jadwal);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $jadwal = JadwalKelas::findOrFail($id);
    
        $request->validate([
            'kelas_id'    => 'required|exists:kelas,id',
            'guru_id'     => 'required|exists:users,id',
            'hari'        => 'nullable|string',
            'tanggal'     => 'nullable|date',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
            'deskripsi'   => 'nullable|string',
        ]);
    
        $jadwal->update([
            'kelas_id'    => $request->kelas_id,
            'guru_id'     => $request->guru_id,
            'hari'        => $request->hari,
            'tanggal'     => $request->tanggal,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'deskripsi'   => $request->deskripsi,
            // kode_unik tidak diubah saat update
        ]);
    
        return response()->json([
            'message' => 'Jadwal updated',
            'jadwal'  => $jadwal
        ]);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        $jadwal = JadwalKelas::findOrFail($id);
        $jadwal->delete();

        return response()->json([
            'message' => 'Jadwal deleted'
        ]);
    }
}
