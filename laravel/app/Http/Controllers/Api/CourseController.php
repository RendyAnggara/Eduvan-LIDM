<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    // public function index()
    // {
    //     $student = Auth::user();
    //     $studentSchoolId = $student->school_id;
    //     $studentClass = $student->class;

    //     $courses = Course::withCount(['chapters'])
    //         ->where(function ($query) use ($studentSchoolId, $studentClass) {
    //             $query->where('course_type', 'school')
    //                   ->where('grade_level', $studentClass)
    //                   ->whereHas('teachers', function ($q) use ($studentSchoolId) {
    //                       $q->where('users.school_id', $studentSchoolId);
    //                   });
    //         })
    //         ->orWhere(function ($query) {
    //             $query->where('course_type', 'premium');
    //         })
    //         ->latest()
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Daftar mata pelajaran gratis sekolah Anda dan kelas premium berhasil dimuat.',
    //         'data'    => $courses
    //     ], 200);
    // }
    public function index()
    {
        $student = Auth::user();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau Anda belum login!'
            ], 401);
        }

        $studentSchoolId = $student->school_id; // Hasilnya: 1
        $rawClass = $student->class; // Hasilnya: "Kelas 7"

        // Ambil angka 7 saja dari string "Kelas 7"
        $studentClass = (string) filter_var($rawClass, FILTER_SANITIZE_NUMBER_INT);

        // Ambil data course dan join ke pivot untuk menyaring sekolah guru
        $courses = Course::withCount(['chapters'])
            ->leftJoin('course_user', 'courses.id', '=', 'course_user.course_id')
            ->leftJoin('users as creators', 'course_user.user_id', '=', 'creators.id')
            ->where(function ($query) use ($studentSchoolId, $studentClass) {

                // 🪙 KONDISI 1: Tipe Premium (Pilihan) -> Bebas muncul global
                $query->where('courses.course_type', 'premium')

                    // 🏢 KONDISI 2: Tipe School (Wajib) -> Harus klop kelas dan sekolah gurunya[cite: 2]
                    ->orWhere(function ($subQuery) use ($studentSchoolId, $studentClass) {
                        $subQuery->where('courses.course_type', 'school')
                            ->where('courses.grade_level', $studentClass)
                            ->where('creators.school_id', $studentSchoolId)
                            ->where('creators.role', 'teacher');
                    });
            })
            ->select('courses.*') // Kunci agar hanya mengambil kolom milik tabel courses
            ->distinct() // Biar datanya tidak ganda
            ->latest('courses.created_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar mata pelajaran berhasil dimuat.',
            'data'    => $courses
        ], 200);
    }
    public function show($id)
    {
        $student = Auth::user();
        $course = Course::with(['chapters.lessons'])->withCount(['chapters'])->findOrFail($id); //

        // Cek apakah siswa sudah membeli kursus premium ini
        $isEnrolled = false;
        if ($student) {
            $isEnrolled = \App\Models\Enrollment::where('user_id', $student->id)
                ->where('course_id', $id)
                ->where('status', 'active') // Pastikan statusnya sudah sukses/aktif
                ->exists();
        }

        return response()->json([
            'success' => true,
            'data' => $course,
            'is_enrolled' => $isEnrolled // 🟢 Kunci utama untuk Ionic
        ], 200);
    }
    public function rate(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5'
        ]);

        $course = Course::findOrFail($id);

        if ($course->course_type === 'school') {
            return response()->json([
                'success' => false,
                'message' => 'Fitur rating hanya tersedia untuk kelas komersial/premium.'
            ], 403);
        }

        $user = $request->user();

        $hasEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $id)
            ->where('status', 'success')
            ->exists();

        if (!$hasEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu belum membeli atau melunasi kursus ini, tidak bisa kasih rating!'
            ], 403);
        }

        $course->update([
            'rating' => $request->rating
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih atas rating bintang ' . $request->rating . ' yang Anda berikan.',
            'current_average' => $course->rating
        ], 200);
    }

    public function dashboard()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_courses' => Course::count(),
                'total_students' => User::where('role', 'student')->count(),
                'total_revenue' => Enrollment::where('status', 'success')
                    ->join('courses', 'enrollments.course_id', '=', 'courses.id')
                    ->sum('courses.price')
            ]
        ], 200);
    }
}
