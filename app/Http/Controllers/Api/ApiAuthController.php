<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Ambil kredensial
        $credentials = $request->only('email', 'password');
        
        // Cek user
        $user = User::where('email', $credentials['email'])->where('status', 'A')->first();
        
        // Cek password dan user status
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial tidak valid atau akun tidak aktif'
            ], 401);
        }
        
        // Buat token untuk API
        $token = $user->createToken('auth_token')->plainTextToken;
        
        // Kirim response
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
    }
    
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }
    
    public function checkStatus(Request $request)
    {
        return response()->json([
            'success' => true,
            'authenticated' => true,
            'user' => $request->user()
        ]);
    }
}