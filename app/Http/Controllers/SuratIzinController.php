<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratIzin;
use App\Models\AbsensiMurid;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SuratIzinController extends Controller
{public function store(Request $request)
    {
        $request->validate([
            'absensi_id' => 'required|exists:absensi_murids,id',
            'file_surat' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $filePath = $request->file('file_surat')->store('surat_izin', 'public');

        $absensi = AbsensiMurid::findOrFail($request->absensi_id);

        $izin = SuratIzin::create([
            'absensi_id' => $absensi->id,
            'murid_id' => $absensi->murid_id,
            'file_path' => $filePath,
            'status' => 'Menunggu',
        ]);

        return response()->json([
            'message' => 'Surat izin berhasil dikirim',
            'data' => [
                'id' => $izin->id,
                'absensi_id' => $izin->absensi_id,
                'murid_id' => $izin->murid_id,
                'file_url' => Storage::url($izin->file_path), // 🟢 Inilah URL publik
                'status' => $izin->status,
                'created_at' => $izin->created_at,
            ]
        ]);
    }

    // 🟢 Guru: Lihat semua surat izin (bisa filter hanya miliknya)
    public function index()
    {
        $izin = SuratIzin::with(['murid', 'absensi.jadwal'])->latest()->get();
    
        // Ubah menjadi list lengkap
        $data = $izin->map(function ($i) {
            return [
                'id' => $i->id,
                'absensi_id' => $i->absensi_id,
                'murid_id' => $i->murid_id,
                'file_url' => Storage::url($i->file_path),
                'status' => $i->status,
                'created_at' => $i->created_at,
            ];
        });
    
        return response()->json([
            'message' => 'Daftar surat izin',
            'data' => $data
        ]);
    }
    

    // 🟢 Guru: Approve / reject surat izin
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
        ]);

        $izin = SuratIzin::findOrFail($id);

        $izin->status = $request->status;
        $izin->save();

        // Update status absensi juga
        $absen = AbsensiMurid::find($izin->absensi_id);
        if ($izin->status === 'Disetujui') {
            $absen->status = 'Izin';
        } else {
            $absen->status = 'Alpha';
        }
        $absen->save();

        return response()->json(['message' => 'Status surat izin diperbarui']);
    }

    // 🟢 Guru: Lihat detail 1 surat izin
    public function show($id)
    {
        $izin = SuratIzin::with(['murid', 'absensi.jadwal'])->findOrFail($id);
        return response()->json([
            'id' => $izin->id,
            'absensi_id' => $izin->absensi_id,
            'murid_id' => $izin->murid_id,
            'file_url' => Storage::url($izin->file_path), // 🟢 Pastikan file_url bukan path mentah
            'status' => $izin->status,
            'murid' => $izin->murid,
            'absensi' => $izin->absensi,
            'created_at' => $izin->created_at,
        ]);
    }
}