<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    // 🔹 REGISTER
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Register user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nama","username","password","role"},
     *             @OA\Property(property="nama", type="string"),
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="role", type="string", example="siswa")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,petugas,siswa'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Register berhasil',
        ]);
    }

    // 🔹 LOGIN (PAKAI USERNAME 🔥)
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Username atau password salah'
            ], 401);
        }

        if ($request->filled('role')) {
            $expected = strtolower($request->role);
            if ($expected === 'administrator') {
                $expected = 'admin';
            }
            if ($user->role !== $expected) {
                return response()->json([
                    'message' => 'Akun ini bukan akun ' . $request->role
                ], 403);
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $this->formatUser($request->user()),
        ]);
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'nama' => $user->nama,
            'username' => $user->username,
            'role' => $user->role,
            'email' => $user->email ?? null,
            'nis' => $user->nis,
            'kelas_id' => $user->kelas_id,
            'kelas' => $user->kelas ? $user->kelas->nama_kelas : null,
            'foto' => $user->foto,
            'gender' => $user->gender,
            'phone' => $user->phone,
        ];
    }

    // 🔹 LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}