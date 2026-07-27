<?php

namespace App\Http\Controllers\Web\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Course;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;

        // Ambil kuis yang mapelnya terikat dengan guru satu instansi sekolah yang sama
        $quizzes = Quiz::whereHas('course.teachers', function ($query) use ($schoolId) {
                $query->where('users.school_id', $schoolId);
            })
            ->with('course')
            ->withCount('questions')
            ->get();

        // Ambil mapel yang dimiliki oleh sekolah yang sama lewat relasi guru
        $courses = Course::where('course_type', 'school')
            ->whereHas('teachers', function ($query) use ($schoolId) {
                $query->where('users.school_id', $schoolId);
            })
            ->get();

        return view('teacher.quiz.index', compact('quizzes', 'courses'));
    }

    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $request->validate([
            'course_id' => [
                'required',
                'exists:courses,id',
                function ($attribute, $value, $fail) use ($schoolId) {
                    $exists = Course::where('id', $value)
                        ->whereHas('teachers', function ($q) use ($schoolId) {
                            $q->where('users.school_id', $schoolId);
                        })->exists();
                    if (!$exists) {
                        $fail('Mata pelajaran yang dipilih tidak valid untuk otoritas instansi sekolah Anda.');
                    }
                },
            ],
            'title' => 'required|string|max:255',
            'time_limit' => 'required|integer|min:1',
        ]);

        Quiz::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'time_limit' => $request->time_limit,
        ]);

        return redirect()->back()->with('success', 'Paket kuis baru berhasil dibuat! Silakan klik kelola soal untuk mengisi pertanyaan.');
    }

    public function destroy($id)
    {
        $quiz = Quiz::whereHas('course.teachers', function ($query) {
            $query->where('users.school_id', Auth::user()->school_id);
        })->findOrFail($id);

        $quiz->delete();

        return redirect()->back()->with('success', 'Paket kuis berhasil dihapus!');
    }

    public function manageQuestions($id)
    {
        $quiz = Quiz::whereHas('course.teachers', function ($query) {
                $query->where('users.school_id', Auth::user()->school_id);
            })
            ->with(['course', 'questions'])
            ->findOrFail($id);

        return view('teacher.quiz.questions', compact('quiz'));
    }

    public function storeQuestion(Request $request, $id)
    {
        $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
        ]);

        $quiz = Quiz::whereHas('course.teachers', function ($query) {
            $query->where('users.school_id', Auth::user()->school_id);
        })->findOrFail($id);

        Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => $request->question_text,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'correct_answer' => $request->correct_answer,
        ]);

        return redirect()->back()->with('success', 'Butir soal baru berhasil ditambahkan ke kuis ini!');
    }

    public function destroyQuestion($id)
    {
        $question = Question::whereHas('quiz.course.teachers', function ($query) {
            $query->where('users.school_id', Auth::user()->school_id);
        })->findOrFail($id);

        $question->delete();

        return redirect()->back()->with('success', 'Soal berhasil dihapus dari kuis!');
    }
}
