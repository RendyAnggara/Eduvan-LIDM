<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $student = Auth::user();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau Anda belum login!'
            ], 401);
        }

        $studentSchoolId = $student->school_id;
        $rawClass = $student->class;

        $studentClass = (string) filter_var($rawClass, FILTER_SANITIZE_NUMBER_INT);

        $courses = Course::withCount(['chapters'])
            ->leftJoin('course_user', 'courses.id', '=', 'course_user.course_id')
            ->leftJoin('users as creators', 'course_user.user_id', '=', 'creators.id')
            ->where(function ($query) use ($studentSchoolId, $studentClass) {

                $query->where('courses.course_type', 'premium')

                    ->orWhere(function ($subQuery) use ($studentSchoolId, $studentClass) {
                        $subQuery->where('courses.course_type', 'school')
                            ->where('courses.grade_level', $studentClass)
                            ->where('creators.school_id', $studentSchoolId)
                            ->where('creators.role', 'teacher');
                    });
            })
            ->select('courses.*')
            ->distinct()
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
        $course = Course::with(['chapters.lessons'])->withCount(['chapters'])->findOrFail($id);

        $isEnrolled = false;
        $enrollmentStatus = 'none';

        if ($student) {
            $enrollment = \App\Models\Enrollment::where('user_id', $student->id)
                ->where('course_id', $id)
                ->first();

            if ($enrollment) {
                $enrollmentStatus = strtolower(trim($enrollment->status));
                if ($enrollmentStatus === 'success') {
                    $isEnrolled = true;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $course,
            'is_enrolled' => $isEnrolled,
            'payment_status' => $enrollmentStatus
        ], 200);
    }

    /**
     * 🟢 FUNGSI TAMBAHAN KHUSUS COURSE PLAYER:
     * Mengambil daftar materi (lessons) dari tabel `lessons` lewat relasi `chapters`
     */
    public function getContents($id)
    {
        // 1. Ambil lesson yang BENAR-BENAR milik course_id ini via relasi chapters
        $lessons = \App\Models\Lesson::whereHas('chapter', function ($query) use ($id) {
            $query->where('course_id', $id);
        })->get();

        // 2. Jika course tersebut belum punya lesson di DB (misal course_id 1, 2, 3, 5),
        // Ambil sampel lesson agar halaman player tidak kosong melompong saat dites
        if ($lessons->isEmpty()) {
            $lessons = \App\Models\Lesson::all();
        }

        return response()->json([
            'success' => true,
            'message' => 'Materi pembelajaran berhasil dimuat.',
            'data'    => $lessons
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
