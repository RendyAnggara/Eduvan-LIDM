<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\School; // Pastikan ini ada
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Sekolah Contoh
        $sekolah1 = School::create(['name' => 'SMPN 1 Karawang Barat']);
        $sekolah2 = School::create(['name' => 'SMPN 2 Karawang Barat']);

        // 2. Akun Admin Utama
        User::create([
            'name' => 'Admin Utama Eduvan',
            'email' => 'admin@eduvan.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'school_id' => null,
            'email_verified_at' => now(),
        ]);

        // 3. Guru dari SMPN 1 Karawang Barat
        User::create([
            'name' => 'Budi Setiawan, S.Pd.',
            'email' => 'guru1@eduvan.com', // <-- Pastikan guru1
            'password' => Hash::make('guru123'),
            'role' => 'teacher',
            'school_id' => $sekolah1->id, // Mengikat ke ID Sekolah 1
            'email_verified_at' => now(),
        ]);

        // 4. Guru dari SMPN 2 Karawang Barat
        User::create([
            'name' => 'Dewi Lestari, S.Pd.',
            'email' => 'guru2@eduvan.com', // <-- Pastikan guru2
            'password' => Hash::make('guru123'),
            'role' => 'teacher',
            'school_id' => $sekolah2->id, // Mengikat ke ID Sekolah 2
            'email_verified_at' => now(),
        ]);

        $this->call([
            StudentSeeder::class,
        ]);
    }
}
