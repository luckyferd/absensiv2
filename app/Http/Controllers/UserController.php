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
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(['auth:sanctum']);

        // Cek role hanya Admin
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
            ->paginate($request->get('per_page', 10));

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'nullable|email|unique:users',
            'nisn'     => 'nullable|string|unique:users',
            'password' => 'required|string|min:6',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'nisn'     => $request->nisn,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
            'email_verified_at' => $request->email ? now() : null,
        ]);

        return response()->json([
            'message' => 'User created',
            'user'    => $user,
        ], 201);
    }

    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'sometimes|string',
            'email'    => 'nullable|email|unique:users,email,' . $id,
            'nisn'     => 'nullable|string|unique:users,nisn,' . $id,
            'password' => 'nullable|string|min:6',
            'role_id'  => 'sometimes|exists:roles,id',
        ]);

        $user->update([
            'name'     => $request->name ?? $user->name,
            'email'    => $request->email ?? $user->email,
            'nisn'     => $request->nisn ?? $user->nisn,
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

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() == $user->id) {
            return response()->json(['message' => 'Tidak bisa menghapus akun sendiri'], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted'
        ]);
    }
}
