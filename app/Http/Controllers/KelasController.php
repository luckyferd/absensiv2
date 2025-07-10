<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class KelasController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }
    public function index()
    {
        $kelas = Kelas::with('guru')->get();
        return response()->json($kelas);
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
            'name' => 'required|string',
            'guru_id' => 'required|exists:users,id', // Validasi guru_id
        ]);

        $kelas = Kelas::create([
            'name' => $request->name,
            'guru_id' => $request->guru_id,
            'kode_kelas' => strtoupper(Str::slug($request->name . '-' . date('Y')))

        ]);
        $kelas->load('guru');
        return response()->json([
            'message' => 'Kelas created',
            'kelas' => $kelas
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kelas = Kelas::with('guru')->findOrFail($id);
        return response()->json($kelas);
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
        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string',
            'guru_id' => 'sometimes|exists:users,id',
        ]);
    
        $data = $request->only(['name', 'guru_id']);
    
        // Jika ada perubahan pada 'name', generate ulang kode_kelas
        if ($request->has('name')) {
            $data['kode_kelas'] = strtoupper(Str::slug($request->name . '-' . date('Y')));
        }
    
        $kelas->update($data);
        $kelas->load('guru');
    
        return response()->json([
            'message' => 'Kelas updated',
            'kelas' => $kelas
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();
        
        return response()->json([
            'message' => 'Kelas deleted'
        ]);
    }
}
