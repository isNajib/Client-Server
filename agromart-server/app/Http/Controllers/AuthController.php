<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        // Validasi data input termasuk password dan password_confirmation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed', // Menjaga kecocokan password dan password_confirmation
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
        ]);

        // Jika validasi gagal, kembalikan error
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Buat pengguna baru, pastikan password di-hash sebelum disimpan
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash password
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        // Membuat token untuk pengguna yang baru terdaftar
        $token = $user->createToken('auth_token')->plainTextToken;

        // Mengembalikan response dengan data pengguna dan token
        return response()->json(
            [
                'message' => 'Registration successful',
                'user' => $user,
                'token' => $token,
            ],
            201,
        );
    }

    /**
     * Login user and return token.
     */
    public function login(Request $request)
    {
        // Validasi data input email dan password
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Jika validasi gagal, kembalikan error
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Cek kredensial pengguna
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Email atau password tidak sesuai',
                ],
                401,
            );
        }

        // Ambil data pengguna setelah berhasil login
        $user = Auth::user();

        // Buat token untuk pengguna
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(
            [
                'status' => true,
                'message' => 'Berhasil login',
                'token' => $token,
            ],
            200,
        );
    }

    /**
     * Logout user and revoke token.
     */
    public function logout(Request $request)
    {
        // Revoke all tokens for the user
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
