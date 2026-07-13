<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Progress;
use App\Models\Content;
use App\Models\QuizResult;
use App\Models\Enrollment;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
       public function submitQuiz(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'score' => 'required|numeric|min:0|max:100'
        ]);

        $user_id = Auth::id();
        $course_id = $request->course_id;
        $score = $request->score;

        $totalVideo = Content::where('course_id', $course_id)->count();

        $videoWatched = Progress::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->whereNotNull('content_id')
            ->where('is_completed', true)
            ->count();

        if ($videoWatched < $totalVideo)
        {
            return response()->json([
                'success' => false,
                'message' => 'Eitss! Selesaikan semua video materi terlebih dahulu sebelum mengumpulkan kuis!'
            ], 403);
        }

        $progress = Progress::updateOrCreate(
            [
                'user_id' => $user_id,
                'course_id' => $course_id,
                'content_id' => null
            ],
            [
                'is_completed' => true
            ]
        );

        $status = ($score >= 70) ? 'passed' : 'failed';

        QuizResult::updateOrCreate(
            [
                'user_id'   => $user_id,
                'course_id' => $course_id,
            ],
            [
                'score'     => round($score, 2),
                'status'    => $status
            ]
        );

        $this->calculateAndSaveEnrollmentProgress($user_id, $course_id);

        return response()->json([
            'success' => true,
            'message' => 'Nilai kuis berhasil dikirim dan disinkronkan ke dasbor guru!',
            'data' => [
                'score' => round($score, 2),
                'status' => $status
            ]
        ], 200);
    }

    public function markAsCompleted(Request $request)
    {
        $request->validate([
            'content_id' => 'required|exists:contents,id'
        ]);

        $content = Content::findOrFail($request->content_id);
        $user_id = Auth::id();
        $course_id = $content->course_id;

        $progress = Progress::updateOrCreate(
            [
                'user_id' => $user_id,
                'content_id' => $request->content_id
            ],
            [
                'course_id' => $course_id,
                'is_completed' => true
            ]
        );

        $this->calculateAndSaveEnrollmentProgress($user_id, $course_id);

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil diselesaikan! Progress belajar diperbarui.',
            'data' => $progress
        ], 200);
    }

    public function getProgress($course_id)
    {
        $user_id = Auth::id();
        $totalContents = Content::where('course_id', $course_id)->count();

        $completedContents = Progress::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->whereNotNull('content_id')
            ->where('is_completed', true)
            ->count();

        $percentage = $totalContents > 0 ? round(($completedContents / $totalContents) * 100) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'course_id' => (int)$course_id,
                'total_materi' => $totalContents,
                'materi_selesai' => $completedContents,
                'persentase' => $percentage . '%'
            ]
        ], 200);
    }

    public function getStudentProgress($course_id, $user_id)
    {
        $total = Content::where('course_id', $course_id)->count();

        $completed = Progress::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->whereNotNull('content_id')
            ->where('is_completed', true)
            ->count();

        $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => (int)$user_id,
                'course_id' => (int)$course_id,
                'persentase_selesai' => $percentage . '%'
            ]
        ], 200);
    }
    private function calculateAndSaveEnrollmentProgress($user_id, $course_id)
    {
        $enrollment = Enrollment::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->first();

        if (!$enrollment) {
            return;
        }

        $totalMateri = Content::where('course_id', $course_id)->count();
        $totalItemWajib = $totalMateri + 1;

        $materiSelesai = Progress::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->whereNotNull('content_id')
            ->where('is_completed', true)
            ->count();

        $quizSelesai = Progress::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->whereNull('content_id')
            ->where('is_completed', true)
            ->count();

        if ($quizSelesai > 1) {
            $quizSelesai = 1;
        }

        $totalSelesai = $materiSelesai + $quizSelesai;
        $finalPercentage = $totalItemWajib > 0 ? (int) round(($totalSelesai / $totalItemWajib) * 100) : 0;

        if ($finalPercentage > 100) {
            $finalPercentage = 100;
        }
        $enrollment->update([
            'progress' => $finalPercentage
        ]);
    }
}
