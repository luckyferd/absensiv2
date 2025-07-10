<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
     // Pastikan hanya Admin yang boleh akses
     use AuthorizesRequests, ValidatesRequests;
     public function __construct()
     {
         $this->middleware(['auth:sanctum']);
          // Tambahan cek role di semua method
    $this->middleware(function ($request, $next) {
        if (auth()->user()->role->name !== 'Admin') {  
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    });
     }

     public function index(Request $request)
     {
         $users = User::with('role')
             ->whereHas('role', function ($q) {
                 $q->whereIn('name', ['Guru', 'Siswa']);
             })
             ->paginate($request->get('per_page', 10)); // default 10 per page
     
         return response()->json($users);
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
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
            'email_verified_at'  => now(), // <- ini tambahan biar langsung aktif
        ]);

        return response()->json([
            'message' => 'User created',
            'user'    => $user,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);
        return response()->json($user);
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
         $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'sometimes|string',
            'email'    => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role_id'  => 'sometimes|exists:roles,id',
        ]);

        $user->update([
            'name'     => $request->name ?? $user->name,
            'email'    => $request->email ?? $user->email,
            'role_id'  => $request->role_id ?? $user->role_id,
        ]);

        if ($request->password) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json([
            'message' => 'User updated',
            'user'    => $user,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        $user = User::findOrFail($id);

        // 🔒 Cegah admin menghapus akun sendiri
        if (auth()->id() == $user->id) { //diubah dari id() pakai tanda kurung
            return response()->json(['message' => 'Tidak bisa menghapus akun sendiri'], 403);
        }
    
        $user->delete();
    
        return response()->json([
            'message' => 'User deleted'
        ]);
    }
   
}
