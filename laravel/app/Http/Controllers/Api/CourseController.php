<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        return response()->json([
            'success' => true,
            'data'    => $courses
        ]);
    }
    public function show($id)
    {
        // ðŸŸ¢ 1. Cari data kursus SEKALIGUS hitung jumlah relasi user yang mendaftar (withCount)
        $course = Course::withCount('users')->find($id);

        // 2. Jika kursus tidak ditemukan, kirim respon error 404
        if (!$course)
        {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan!'
            ], 404);
        }

        // 3. Jika ditemukan, kirim datanya (sekarang di dalam $course sudah ada field 'users_count')
        return response()->json([
            'success' => true,
            'message' => 'Detail Data Kursus Berhasil Dimuat',
            'data'    => $course
        ]);
    }
    public function rate(Request $request, $id)
    {
        // 1. Validasi input bintang wajib angka 1 sampai 5
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5'
        ]);

        $course = Course::findOrFail($id);
        $user = $request->user();

        // 2. Cek apakah student ini beneran udah beli kelasnya
        $hasEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $id)
            ->where('status', 'success')
            ->exists();

        if (!$hasEnrolled)
        {
            return response()->json([
                'success' => false,
                'message' => 'Kamu belum beli atau melunasi kursus ini, tidak bisa kasih rating!'
            ], 403);
        }

        // 3. LANGSUNG UPDATE: Gak perlu simpan ke course_ratings, langsung timpa kolom rating di tabel courses
        $course->update([
            'rating' => $request->rating
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih atas rating bintang ' . $request->rating . ' yang Anda berikan.',
            'current_average' => $course->rating
        ]);
    }
    public function dashboard()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_courses' => Course::count(),
                'total_students' => User::where('role', 'student')->count(), // Pastikan ada kolom role
                'total_revenue' => Enrollment::join('courses', 'enrollments.course_id', '=', 'courses.id')->sum('courses.price')
            ]
        ]);
    }
}
