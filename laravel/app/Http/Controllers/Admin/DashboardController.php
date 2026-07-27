<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\School;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPaidCourses = Course::where('course_type', 'premium')->count();

        $stats = [
            'total_courses' => $totalPaidCourses,
            'total_schools' => School::count(),
        ];

        $schoolsData = School::withCount([
            'users as total_teachers' => function ($query) {
                $query->where('role', 'teacher');
            },
            'users as total_students' => function ($query) {
                $query->where('role', 'student');
            }
        ])->latest()->get();

        $recentTransactions = Enrollment::with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'schoolsData', 'recentTransactions'));
    }
}
