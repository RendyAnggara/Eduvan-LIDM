<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Content; // Pastikan Model ini ada
use App\Models\Progress; // Pastikan Model ini ada
use Illuminate\Support\Facades\Hash;

class DashboardDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat 5 Kursus Berbeda
        $courseData = [
            ['title' => 'Belajar Laravel Dasar', 'price' => 150000],
            ['title' => 'Mastering Vue.js 3', 'price' => 200000],
            ['title' => 'Ionic Mobile App', 'price' => 250000],
            ['title' => 'UI/UX Design for Web', 'price' => 100000],
            ['title' => 'Database MySQL Advanced', 'price' => 180000],
        ];

        $createdCourses = [];
        foreach ($courseData as $c) {
            $course = Course::firstOrCreate(
                ['title' => $c['title']],
                [
                    'description' => 'Kursus intensif ' . $c['title'],
                    'price' => $c['price'],
                    'image' => null
                ]
            );

            // TAMBAHAN: Buat 1 materi dummy agar sistem bisa menghitung progres
            Content::firstOrCreate(
                ['course_id' => $course->id, 'title' => 'Pengenalan ' . $c['title']],
                ['content_url' => 'https://youtube.com', 'type' => 'video']
            );

            $createdCourses[] = $course;
        }

        // 2. Data Student Dummy
        $students = [
            ['name' => 'Budi Santoso', 'email' => 'budi@example.com'],
            ['name' => 'Siti Aminah', 'email' => 'siti@example.com'],
            ['name' => 'Rian Ardianto', 'email' => 'rian@example.com'],
        ];

        foreach ($students as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'student'
                ]
            );

            foreach ($createdCourses as $index => $course) {
                Enrollment::firstOrCreate(
                    ['user_id' => $user->id, 'course_id' => $course->id],
                    ['price_bought' => $course->price, 'status' => 'success']
                );

                // LOGIC AGAR MUNCUL DI TABEL CERTIFICATE:
                // Kita buat Budi Santoso (index student 0) lulus di Kursus Laravel (index course 0)
                if ($data['email'] === 'budi@example.com' && $index === 0) {
                    $materi = Content::where('course_id', $course->id)->first();

                    Progress::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'course_id' => $course->id,
                            'content_id' => $materi->id
                        ],
                        ['is_completed' => true]
                    );

                    // Tambahkan baris Progress untuk kelulusan total (is_completed = true global)
                    Progress::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'course_id' => $course->id,
                            'content_id' => null // Biasanya quiz/final progress
                        ],
                        ['is_completed' => true, 'score' => 100]
                    );
                }
            }
        }
    }
}
