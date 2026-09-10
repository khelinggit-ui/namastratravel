<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        $user = Auth::user();
        if ($user->is_admin) {
            Auth::logout();
            return response()->json(['message' => 'Gunakan halaman login admin.'], 403);
        }

        return response()->json([
            'token' => $user->createToken('customer-web')->plainTextToken,
            'user' => $this->userData($user),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($this->userData($request->user()));
    }

    public function bookings(Request $request)
    {
        return response()->json($request->user()->bookings()->latest()->get());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['message' => 'Logout berhasil.']);
    }

    private function userData($user): array
    {
        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
    }
}
