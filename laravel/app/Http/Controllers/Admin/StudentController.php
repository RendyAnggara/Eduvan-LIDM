<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\School;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterStatus = $request->input('filter');
        $gradeFilter = $request->input('grade_level');
        $query = User::whereIn('role', ['student', 'Student'])
                     ->whereNotIn('role', ['teacher', 'Teacher', 'guru', 'Guru', 'admin', 'Admin']);
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('school', function ($s) use ($search) {
                      $s->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($filterStatus === 'bought') {
            $query->has('enrollments');
        } elseif ($filterStatus === 'not_bought') {
            $query->doesntHave('enrollments');
        }

        if (!empty($gradeFilter) && $gradeFilter !== 'all') {
            $query->where(function ($q) use ($gradeFilter) {
                $q->where('class', $gradeFilter)
                  ->orWhere('class', 'like', "%{$gradeFilter}%")
                  ->orWhereHas('enrollments.course', function ($c) use ($gradeFilter) {
                      $c->where('grade_level', $gradeFilter);
                  });
            });
        }

        $students = $query->with(['school', 'enrollments.course'])->latest()->paginate(10);
        $stats = [
            'total_students'  => User::whereIn('role', ['student', 'Student'])->count(),
            'active_students' => User::whereIn('role', ['student', 'Student'])->has('enrollments')->count(),
            'no_course'       => User::whereIn('role', ['student', 'Student'])->doesntHave('enrollments')->count(),
            'total_schools'   => School::count(),
        ];

        return view('admin.students.index', compact('students', 'stats', 'search', 'filterStatus', 'gradeFilter'));
    }

    public function show($id)
    {
        $student = User::with(['school', 'enrollments.course.contents', 'progress'])->findOrFail($id);
        return view('admin.students.show', compact('student'));
    }

    public function apiShow($id)
    {
        $student = User::with('enrollments.course')->findOrFail($id);

        return response()->json([
            'name'     => $student->name,
            'school'   => $student->school->name ?? 'Tanpa Instansi',
            'courses'  => $student->enrollments->map(fn($e) => $e->course->title),
            'progress' => $student->enrollments->map(fn($e) => method_exists($e, 'calculateProgress') ? $e->calculateProgress() : 0),
        ]);
    }

    public function destroy($id)
    {
        $student = User::findOrFail($id);
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Data student berhasil dihapus.');
    }

    public function showQuiz(Course $course)
    {
        $userId = Auth::id();

        $isPurchased = Enrollment::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->exists();

        if (!$isPurchased) {
            return abort(403, 'Kamu harus membeli kursus ini untuk mengakses Quiz.');
        }

        $quizzes = $course->quizzes;
        return view('student.quiz.show', compact('course', 'quizzes'));
    }
}
