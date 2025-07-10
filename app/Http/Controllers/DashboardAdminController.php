<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\AbsensiGuru;
use App\Models\AbsensiMurid;
use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $totalGuru = User::whereHas('role', fn($q) => $q->where('name', 'Guru'))->count();
        $totalMurid = User::whereHas('role', fn($q) => $q->where('name', 'Siswa'))->count();
        $totalKelas = Kelas::count();
        $totalAbsensiGuru = AbsensiGuru::count();
        $totalAbsensiMurid = AbsensiMurid::count();

        return response()->json([
            'total_guru' => $totalGuru,
            'total_murid' => $totalMurid,
            'total_kelas' => $totalKelas,
            'total_absensi_guru' => $totalAbsensiGuru,
            'total_absensi_murid' => $totalAbsensiMurid,
        ]);
    }
}
