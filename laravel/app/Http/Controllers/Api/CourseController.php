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
    public function index()
    {
        $student = Auth::user();
        $studentSchoolId = $student->school_id;
        $studentClass = $student->class;

        $courses = Course::withCount(['chapters'])
            ->where(function ($query) use ($studentSchoolId, $studentClass) {
                $query->where('course_type', 'school')
                      ->where('grade_level', $studentClass)
                      ->whereHas('teachers', function ($q) use ($studentSchoolId) {
                          $q->where('users.school_id', $studentSchoolId);
                      });
            })
            ->orWhere(function ($query) {
                $query->where('course_type', 'premium');
            })
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar mata pelajaran gratis sekolah Anda dan kelas premium berhasil dimuat.',
            'data'    => $courses
        ], 200);
    }
    public function show($id)
    {
        $student = Auth::user();
        $studentSchoolId = $student->school_id;
        $studentClass = $student->class;

        $course = Course::withCount('users')
            ->where(function ($query) use ($id, $studentSchoolId, $studentClass) {
                $query->where('id', $id)
                      ->where('course_type', 'school')
                      ->where('grade_level', $studentClass)
                      ->whereHas('teachers', function ($q) use ($studentSchoolId) {
                          $q->where('users.school_id', $studentSchoolId);
                      });
            })
            ->orWhere(function ($query) use ($id) {
                $query->where('id', $id)
                      ->where('course_type', 'premium');
            })
            ->first();

        if (!$course)
        {
            return response()->json([
                'success' => false,
                'message' => 'Mata pelajaran tidak ditemukan, tingkatan kelas berbeda, atau Anda tidak memiliki akses!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Data Kursus Berhasil Dimuat',
            'data'    => $course
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

        if (!$hasEnrolled)
        {
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
