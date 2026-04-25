<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kelas;
use App\Models\BoxWadah;
use App\Models\TransaksiMbg;
use App\Models\LogAktivitas;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Buat Data User
        $admin = User::create([
            'nama' => 'Admin Utama',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        $petugas = User::create([
            'nama' => 'Petugas Makanan',
            'username' => 'petugas',
            'password' => Hash::make('password'),
            'role' => 'petugas'
        ]);

        // Buat beberapa user petugas acak
        $usersIds = [$admin->id, $petugas->id];
        for ($i = 0; $i < 3; $i++) {
            $user = User::create([
                'nama' => $faker->name,
                'username' => $faker->unique()->userName,
                'password' => Hash::make('password'),
                'role' => 'petugas'
            ]);
            $usersIds[] = $user->id;
        }

        // 2. Buat Data Kelas
        $jurusans = ['RPL', 'TKJ', 'Multimedia', 'Akuntansi', 'Perhotelan'];
        $kelasIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $kelas = Kelas::create([
                'nama_kelas' => 'X ' . $faker->randomElement($jurusans) . ' ' . $faker->numberBetween(1, 4),
                'jurusan' => $faker->randomElement($jurusans),
                'jumlah_siswa' => $faker->numberBetween(25, 40)
            ]);
            $kelasIds[] = $kelas->id;
        }

        // 3. Buat Data Box Wadah
        $boxIds = [];
        for ($i = 1; $i <= 15; $i++) {
            $box = BoxWadah::create([
                'jumlah_box' => $faker->numberBetween(25, 45),
                'status_box' => $faker->randomElement(['tersedia', 'dipakai'])
            ]);
            $boxIds[] = $box->id;
        }

        // 4. Buat Data Transaksi MBG
        for ($i = 0; $i < 20; $i++) {
            $status = $faker->randomElement(['diambil', 'dikembalikan']);
            $tanggal = $faker->dateTimeThisMonth()->format('Y-m-d');
            
            TransaksiMbg::create([
                'user_id' => $faker->randomElement($usersIds),
                'kelas_id' => $faker->randomElement($kelasIds),
                'box_id' => $faker->randomElement($boxIds),
                'jumlah_porsi' => $faker->numberBetween(20, 40),
                'tanggal' => $tanggal,
                'jam_ambil' => '07:30:00',
                'jam_kembali' => $status == 'dikembalikan' ? '14:00:00' : null,
                'status' => $status
            ]);
        }

        // 5. Buat Data Log Aktivitas
        for ($i = 0; $i < 10; $i++) {
            LogAktivitas::create([
                'user_id' => $faker->randomElement($usersIds),
                'aktivitas' => $faker->sentence(3),
                'tanggal' => date('Y-m-d'),
                'waktu' => date('H:i:s')
            ]);
        }
    }
}
