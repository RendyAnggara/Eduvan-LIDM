<?php

namespace App\Http\Controllers\Web\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Course;
use App\Models\Question;
use App\Models\User;
use App\Models\QuizResult;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $quizzes = Quiz::whereHas('course.teachers', function ($query) use ($schoolId) {
                $query->where('users.school_id', $schoolId);
            })
            ->with('course')
            ->withCount('questions')
            ->get();
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
            'type' => 'required|in:multiple_choice,essay',
            'question_text' => 'required|string',
        ]);

        $type = $request->input('type', 'multiple_choice');

        if ($type === 'multiple_choice') {
            $request->validate([
                'option_a' => 'required|string',
                'option_b' => 'required|string',
                'option_c' => 'required|string',
                'option_d' => 'required|string',
                'correct_answer' => 'required|in:A,B,C,D',
            ]);
        }

        $quiz = Quiz::whereHas('course.teachers', function ($query) {
            $query->where('users.school_id', Auth::user()->school_id);
        })->findOrFail($id);

        Question::create([
            'quiz_id' => $quiz->id,
            'type' => $type,
            'question_text' => $request->question_text,
            'option_a' => $type === 'multiple_choice' ? $request->option_a : null,
            'option_b' => $type === 'multiple_choice' ? $request->option_b : null,
            'option_c' => $type === 'multiple_choice' ? $request->option_c : null,
            'option_d' => $type === 'multiple_choice' ? $request->option_d : null,
            'correct_answer' => $type === 'multiple_choice' ? $request->correct_answer : null,
        ]);

        $labelTipe = $type === 'essay' ? 'Essay' : 'Pilihan Ganda';

        return redirect()->back()->with('success', "Soal ({$labelTipe}) berhasil ditambahkan ke kuis ini!");
    }

    public function destroyQuestion($id)
    {
        $question = Question::whereHas('quiz.course.teachers', function ($query) {
            $query->where('users.school_id', Auth::user()->school_id);
        })->findOrFail($id);

        $question->delete();

        return redirect()->back()->with('success', 'Soal berhasil dihapus dari kuis!');
    }

    public function reviewStudentAnswers($student_id, $quiz_result_id)
    {
        $student = User::where('role', 'student')
            ->where('school_id', Auth::user()->school_id)
            ->findOrFail($student_id);

        $quizResult = QuizResult::with([
            'course',
            'answers.question'
        ])->where('user_id', $student->id)->findOrFail($quiz_result_id);

        $studentAnswers = $quizResult->answers;

        return view('teacher.students.review_quiz', compact('student', 'quizResult', 'studentAnswers'));
    }

    public function gradeEssay(Request $request, $student_id, $quiz_result_id)
    {
        $student = User::where('role', 'student')
            ->where('school_id', Auth::user()->school_id)
            ->findOrFail($student_id);

        $quizResult = QuizResult::where('user_id', $student->id)->findOrFail($quiz_result_id);

        $request->validate([
            'essay_scores' => 'required|array',
            'essay_scores.*' => 'numeric|min:0|max:100',
            'teacher_notes' => 'nullable|array',
        ]);

        foreach ($request->essay_scores as $answerId => $score) {
            $answer = StudentAnswer::where('quiz_result_id', $quizResult->id)->find($answerId);
            if ($answer) {
                $note = $request->teacher_notes[$answerId] ?? null;
                $answer->update([
                    'score' => (float) $score,
                    'teacher_note' => $note,
                ]);
            }
        }

        $freshAnswers = StudentAnswer::where('quiz_result_id', $quizResult->id)
            ->with('question')
            ->get();

        $totalQuestions = $freshAnswers->count();

        if ($totalQuestions > 0) {
            $totalAccumulatedScore = 0;

            foreach ($freshAnswers as $ans) {
                if ($ans->question && $ans->question->type === 'essay') {

                    $totalAccumulatedScore += (float) $ans->score;
                } else {
                    $totalAccumulatedScore += ($ans->is_correct ? 100.0 : 0.0);
                }
            }

            $finalScore = round($totalAccumulatedScore / $totalQuestions, 1);

            $quizResult->update([
                'score' => $finalScore,
                'status' => 'Sudah Dinilai'
            ]);
        }

        return redirect()->back()->with('success', 'Penilaian soal essay berhasil disimpan dan skor akhir siswa telah diperbarui!');
    }
}
