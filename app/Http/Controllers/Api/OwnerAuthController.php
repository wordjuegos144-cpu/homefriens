<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OwnerAuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:owners,email',
            'password' => 'required|min:6'
        ]);

        $owner = Owner::create([
            'name' => $data['name'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        $token = Str::random(60);
        DB::table('owner_tokens')->insert([
            'owner_id' => $owner->id,
            'token' => $token,
            'created_at' => now()
        ]);

        return response()->json(['owner' => $owner, 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $owner = Owner::where('email', $data['email'])->first();
        if (!$owner || !Hash::check($data['password'], $owner->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $token = Str::random(60);
        DB::table('owner_tokens')->insert([
            'owner_id' => $owner->id,
            'token' => $token,
            'created_at' => now()
        ]);

        return response()->json(['owner' => $owner, 'token' => $token], 200);
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        if ($token) {
            DB::table('owner_tokens')->where('token', $token)->delete();
        }
        return response()->json(['message' => 'Sesión cerrada'], 200);
    }
}
