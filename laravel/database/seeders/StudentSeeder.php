<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. BUAT DATA SISWA KELAS 7 (15 Anak)
        for ($i = 0; $i < 15; $i++) {
            User::create([
                'nisn_or_nip' => $faker->unique()->numerify('00######'),
                'name'        => $faker->name,
                'email'       => $faker->unique()->safeEmail,
                'role'        => 'student',
                'school_id'   => 2,
                'class'       => 'Kelas 7',
                'password'    => Hash::make('password123'),
            ]);
        }

        // 2. BUAT DATA SISWA KELAS 8 (12 Anak)
        for ($i = 0; $i < 12; $i++) {
            User::create([
                'nisn_or_nip' => $faker->unique()->numerify('00######'),
                'name'        => $faker->name,
                'email'       => $faker->unique()->safeEmail,
                'role'        => 'student',
                'school_id'   => 2,
                'class'       => 'Kelas 8',
                'password'    => Hash::make('password123'),
            ]);
        }

        // 3. BUAT DATA SISWA KELAS 9 (10 Anak)
        for ($i = 0; $i < 10; $i++) {
            User::create([
                'nisn_or_nip' => $faker->unique()->numerify('00######'),
                'name'        => $faker->name,
                'email'       => $faker->unique()->safeEmail,
                'role'        => 'student',
                'school_id'   => 2,
                'class'       => 'Kelas 9',
                'password'    => Hash::make('password123'),
            ]);
        }
    }
}
