<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required_without:nisn|email',
            'nisn'     => 'required_without:email',
            'password' => 'required'
        ]);
    
        if ($request->filled('nisn')) {
            $user = User::where('nisn', $request->nisn)->first();
        } else {
            $user = User::where('email', $request->email)->first();
        }
    
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    
        $token = $user->createToken('api-token')->plainTextToken;
    
        return response()->json([
            'user'  => $user,
            'token' => $token,
            'role'  => $user->role->name,
        ]);
    }
    
    

    // Logout
    public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete(); // Hapus token yang sedang aktif saja
    return response()->json(['message' => 'Logged out']);
}
}
