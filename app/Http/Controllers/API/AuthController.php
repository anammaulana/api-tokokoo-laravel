<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Membuat pengguna baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User registered successfully!',
            'user' => $user
        ], 201);
    }

    // Login Method
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Cek apakah user ada dan cocok dengan password
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials!'], 401);
        }

        // Membuat token untuk user
        $token = $user->createToken('YourAppName')->plainTextToken;

        return response()->json([
            'message' => 'Login successful!',
            'token' => $token
        ]);
    }

    public function logout(Request $request)
{
    // Menghapus token yang digunakan saat ini
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logged out successfully!'
    ]);
}

}
