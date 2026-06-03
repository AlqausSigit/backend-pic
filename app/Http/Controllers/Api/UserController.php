<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use OpenApi\Annotations as OA;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     summary="Ambil semua user",
     *     operationId="getUsers",
     *     @OA\Response(
     *         response=200,
     *         description="List user"
     *     )
     * )
     */


    public function index()
    {
        return response()->json(
            User::whereIn('role', ['admin', 'petugas'])->get()
        );
    }

    /**
     * @OA\Get(
     *     path="/api/cek-siswa",
     *     tags={"ATLAS"},
     *     summary="Cek Data Siswa",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Berhasil")
     * )
     */
    public function cekSiswa()
    {
        $siswa = User::with('kelas')
            ->where('role', 'siswa')
            ->orderBy('nis')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'nis' => $user->nis,
                    'kelas' => $user->kelas?->nama_kelas,
                    'gender' => $user->gender,
                    'phone' => $user->phone,
                ];
            });

        return response()->json($siswa);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json($user);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"ATLAS"},
     *     summary="Tambah User",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=201, description="Berhasil")
     * )
     */
    public function store(Request $request)
    {
        if ($request->role === 'siswa' && $request->filled('nis')) {
            $request->merge([
                'username' => $request->nis,
                'password' => $request->nis,
            ]);
        }

        if ($request->filled('kelas') && !$request->filled('kelas_id')) {
            $kelasObj = \App\Models\Kelas::where('nama_kelas', $request->kelas)->first();
            if ($kelasObj) {
                $request->merge(['kelas_id' => $kelasObj->id]);
            }
        }

        $request->validate([
            'nama' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required',
            'role' => 'required|in:admin,petugas,siswa',
            'email' => 'nullable|email|unique:users,email',
            'nis' => $request->role === 'siswa' ? 'required|string|unique:users,nis' : 'nullable|string|unique:users,nis',
            'kelas_id' => 'nullable|exists:kelas,id',
            'gender' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'nis' => $request->nis,
            'kelas_id' => $request->kelas_id,
            'gender' => $request->gender,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'message' => 'User berhasil ditambahkan',
            'data' => $user
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        if ($request->role === 'siswa' && $request->filled('nis')) {
            $request->merge([
                'username' => $request->nis,
            ]);
        }

        if ($request->filled('kelas') && !$request->filled('kelas_id')) {
            $kelasObj = \App\Models\Kelas::where('nama_kelas', $request->kelas)->first();
            if ($kelasObj) {
                $request->merge(['kelas_id' => $kelasObj->id]);
            }
        }

        $user->update([
            'nama' => $request->nama ?? $user->nama,
            'username' => $request->username ?? $user->username,
            'email' => $request->email ?? $user->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'role' => $request->role ?? $user->role,
            'nis' => $request->nis ?? $user->nis,
            'kelas_id' => $request->kelas_id ?? $user->kelas_id,
            'gender' => $request->gender ?? $user->gender,
            'phone' => $request->phone ?? $user->phone
        ]);

        return response()->json([
            'message' => 'User berhasil diupdate',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }
}
