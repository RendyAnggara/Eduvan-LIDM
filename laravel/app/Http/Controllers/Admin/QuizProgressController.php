<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Progress;
use App\Models\Content;

class QuizProgressController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $courses = Course::where('price', '>', 0)
            ->withCount([
                'users',
                'quizzes',
                'progress as completed_count' => function ($query) {
                    $query->where('is_completed', true)->whereNull('content_id');
                }
            ])
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        $totalCompleted = Progress::where('is_completed', true)
            ->whereNull('content_id')
            ->distinct('user_id')
            ->count('user_id');

        $totalQuizDoneCount = Progress::where('is_completed', true)
            ->whereNull('content_id')
            ->count();

        return view('admin.quiz.index', compact('courses', 'totalCompleted', 'totalQuizDoneCount'));
    }

    public function show($id)
    {
        $course = Course::with(['users.progress' => function ($query) use ($id) {
            $query->where('course_id', $id);
        }])->findOrFail($id);

        $totalVideo = Content::where('course_id', $id)->count();

        $course->users->each(function ($user) use ($id, $totalVideo) {
            $quiz = $user->progress->where('course_id', $id)->whereNull('content_id')->first();
            $user->nilai_quiz_asli = $quiz ? ($quiz->score ?? '-') : '-';

            $completedVideoCount = $user->progress->where('course_id', $id)
                ->whereNotNull('content_id')
                ->where('is_completed', true)
                ->count();

            $user->persentase_asli = $totalVideo > 0
                ? round(($completedVideoCount / $totalVideo) * 100)
                : 0;
        });

        return view('admin.quiz.show', compact('course'));
    }

    public function manage(Course $course)
    {
        $quiz = Quiz::firstOrCreate(
            ['course_id' => $course->id],
            ['title' => 'Quiz ' . $course->title, 'time_limit' => 30]
        );

        $questions = Question::where('quiz_id', $quiz->id)->oldest()->get();

        return view('admin.quiz.manage', compact('course', 'quiz', 'questions'));
    }

    public function storeQuiz(Request $request, Course $course)
    {
        $request->validate([
            'question' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'answer'   => 'required|in:a,b,c,d',
        ]);

        $quiz = Quiz::firstOrCreate(
            ['course_id' => $course->id],
            ['title' => 'Quiz ' . $course->title, 'time_limit' => 30]
        );

        Question::create([
            'quiz_id'        => $quiz->id,
            'question_text'  => $request->question,
            'option_a'       => $request->option_a,
            'option_b'       => $request->option_b,
            'option_c'       => $request->option_c,
            'option_d'       => $request->option_d,
            'correct_answer' => strtolower($request->answer),
        ]);

        return redirect()->back()->with('success', 'Soal kuis berhasil ditambahkan!');
    }

    public function destroyQuiz($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return redirect()->back()->with('success', 'Soal kuis berhasil dihapus!');
    }
}
