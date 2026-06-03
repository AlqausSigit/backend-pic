<?php

namespace Database\Seeders;

use App\Models\BoxWadah;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\LogAktivitas;
use App\Models\TransaksiMbg;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(
            file_get_contents(database_path('seeders/data/atlas_frontend.json')),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $kelasByFrontendId = $this->seedKelas($data['kelas']);
        $boxByFrontendId = $this->seedBoxWadah($data['box_wadah']);
        $userByFrontendId = $this->seedUsers($data['users']);
        $this->seedSiswa($data['siswa'], $kelasByFrontendId);
        $this->seedTransaksi($data['transaksi_mbgs'], $userByFrontendId, $kelasByFrontendId, $boxByFrontendId);
        $this->seedLogAktivitas($data['log_aktivitas'], $userByFrontendId);
        $this->seedRekap($data['rekap']);
        $this->seedJadwalMenu($data['jadwal_menu']);
        $this->seedGuru($data['guru']);
    }

    private function seedUsers(array $rows): array
    {
        $map = [];

        foreach ($rows as $row) {
            $user = User::updateOrCreate(
                ['username' => $row['username']],
                [
                    'nama' => $row['nama'],
                    'email' => $row['email'] ?? null,
                    'password' => Hash::make($row['password'] ?? 'password123'),
                    'role' => $row['role'],
                ]
            );

            $map[$row['id']] = $user->id;
        }

        return $map;
    }

    private function seedKelas(array $rows): array
    {
        $map = [];

        foreach ($rows as $row) {
            $kelas = Kelas::updateOrCreate(
                ['nama_kelas' => $row['nama_kelas']],
                [
                    'jurusan' => $row['jurusan'] ?? 'TJKT',
                    'jumlah_siswa' => $row['jumlah_siswa'] ?? 0,
                    'wali_kelas' => $row['waliKelas'] ?? null,
                ]
            );

            $map[$row['id']] = $kelas->id;
        }

        return $map;
    }

    private function seedBoxWadah(array $rows): array
    {
        $map = [];

        foreach ($rows as $row) {
            $box = BoxWadah::updateOrCreate(
                ['kode_box' => $row['kode_unik']],
                [
                    'jumlah_box' => $row['jumlah_box'] ?? 0,
                    'status' => $row['status_box'] ?? 'tersedia',
                ]
            );

            $map[$row['id']] = $box->id;
        }

        return $map;
    }

    private function seedSiswa(array $rows, array $kelasByFrontendId): void
    {
        $kelasByName = Kelas::pluck('id', 'nama_kelas');

        foreach ($rows as $row) {
            $kelasId = $kelasByName[$row['kelas']] ?? $kelasByFrontendId[1] ?? null;

            User::updateOrCreate(
                ['nis' => $row['nis']],
                [
                    'nama' => $row['name'],
                    'username' => $row['nis'],
                    'password' => Hash::make($row['nis']),
                    'role' => 'siswa',
                    'kelas_id' => $kelasId,
                    'gender' => $row['gender'] ?? null,
                    'phone' => $row['phone'] ?? null,
                ]
            );
        }
    }

    private function seedTransaksi(array $rows, array $userByFrontendId, array $kelasByFrontendId, array $boxByFrontendId): void
    {
        foreach ($rows as $row) {
            TransaksiMbg::updateOrCreate(
                [
                    'tanggal' => $row['tanggal'],
                    'kelas_id' => $kelasByFrontendId[$row['kelas_id']],
                    'box_id' => $boxByFrontendId[$row['box_id']],
                ],
                [
                    'user_id' => $userByFrontendId[$row['user_id']],
                    'jumlah_porsi' => $row['jumlah_porsi'],
                    'jam_ambil' => $row['jam_ambil'] ?? null,
                    'jam_kembali' => $row['jam_kembali'] ?? null,
                    'status' => $row['status'],
                ]
            );
        }
    }

    private function seedLogAktivitas(array $rows, array $userByFrontendId): void
    {
        foreach ($rows as $row) {
            LogAktivitas::updateOrCreate(
                [
                    'aktivitas' => $row['aktivitas'],
                    'tanggal' => $row['tanggal'],
                    'waktu' => $row['waktu'],
                ],
                [
                    'user_id' => $userByFrontendId[$row['user_id']] ?? reset($userByFrontendId),
                ]
            );
        }
    }

    private function seedRekap(array $rows): void
    {
        foreach ($rows as $row) {
            DB::table('rekap')->updateOrInsert(
                ['tanggal' => $row['tanggal']],
                [
                    'total_porsi' => $row['total_porsi'],
                    'total_kelas' => $row['total_kelas'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    private function seedJadwalMenu(array $rows): void
    {
        foreach ($rows as $row) {
            DB::table('jadwal_menu')->updateOrInsert(
                ['tanggal' => $row['date']],
                [
                    'nama_menu' => $row['menu'],
                    'kalori' => (int) preg_replace('/\D+/', '', $row['kalori'] ?? '0'),
                    'waste_limit' => $row['wasteLimit'] ?? '5%',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    private function seedGuru(array $rows): void
    {
        foreach ($rows as $row) {
            Guru::updateOrCreate(
                ['kode' => $row['kode']],
                [
                    'nama' => $row['nama'],
                    'kategori' => $row['kategori'] ?? 'Guru',
                    'status' => $row['status'] ?? 'Aktif',
                ]
            );
        }
    }
}
