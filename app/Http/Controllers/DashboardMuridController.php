<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsensiMurid;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardMuridController extends Controller
{
    public function index()
    {
        $murid = Auth::user();
        $hariIni = Carbon::now()->toDateString();

        $absenHariIni = AbsensiMurid::with('jadwal')
            ->where('murid_id', $murid->id)
            ->where('tanggal', $hariIni)
            ->get();

            $riwayat = AbsensiMurid::with('jadwal')
                ->where('murid_id', $murid->id)
                ->orderBy('tanggal', 'desc')
                ->take(10)
                ->get();

                
        return response()->json([
            'message' => 'Dashboard Murid',
            'data' => [
                'absen_hari_ini' => $absenHariIni,
                'riwayat'        => $riwayat
            ]]);
    }
}
