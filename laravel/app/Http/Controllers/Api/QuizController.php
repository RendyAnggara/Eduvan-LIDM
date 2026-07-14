<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Progress;
use App\Models\Content;
use App\Models\QuizResult;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index($course_id)
    {
        $userId = Auth::id();

        $totalContent = Content::where('course_id', $course_id)->count();
        $completedContent = Progress::where('user_id', $userId)
            ->whereIn('content_id', Content::where('course_id', $course_id)->pluck('id'))
            ->where('is_completed', true)
            ->count();

        if ($completedContent < $totalContent) {
            return response()->json([
                'success' => false,
                'message' => 'Eitss! Selesaikan semua video materi dulu baru bisa buka Quiz.',
                'debug' => [
                    'total_materi' => $totalContent,
                    'materi_selesai' => $completedContent
                ]
            ], 403);
        }

        $quizzes = Quiz::where('course_id', $course_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar soal quiz terbuka!',
            'data' => $quizzes
        ], 200);
    }

    public function submit(Request $request, $course_id)
    {
        $userId = Auth::id();
        $userAnswers = $request->input('answers');
        $correctCount = 0;

        $quizzes = Quiz::where('course_id', $course_id)->get();
        $totalQuestions = $quizzes->count();

        if ($totalQuestions === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada soal kuis yang terdaftar untuk mata pelajaran ini.'
            ], 404);
        }

        if (empty($userAnswers) || !is_array($userAnswers)) {
            return response()->json([
                'success' => false,
                'message' => 'Jawaban kuis tidak boleh kosong.'
            ], 400);
        }

        foreach ($userAnswers as $userAns) {
            $quiz = $quizzes->where('id', $userAns['quiz_id'])->first();
            if ($quiz && strtoupper($quiz->correct_answer) === strtoupper($userAns['answer'])) {
                $correctCount++;
            }
        }

        $finalScore = ($correctCount / $totalQuestions) * 100;

        $status = ($finalScore >= 70) ? 'passed' : 'failed';

        QuizResult::updateOrCreate(
            ['user_id' => $userId, 'course_id' => $course_id],
            [
                'score' => round($finalScore, 2),
                'status' => $status
            ]
        );

        Progress::updateOrCreate(
            ['user_id' => $userId, 'course_id' => $course_id, 'content_id' => null],
            ['is_completed' => true]
        );

        return response()->json([
            'success' => true,
            'message' => 'Quiz berhasil dikirim!',
            'data' => [
                'total_soal' => $totalQuestions,
                'jawaban_benar' => $correctCount,
                'nilai' => round($finalScore, 2),
                'status' => $status
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D,a,b,c,d'
        ]);

        $quiz = Quiz::create([
            'course_id' => $request->course_id,
            'question_text' => $request->question_text,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'correct_answer' => strtoupper($request->correct_answer),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Soal quiz berhasil ditambahkan!',
            'data' => $quiz
        ], 201);
    }
}
