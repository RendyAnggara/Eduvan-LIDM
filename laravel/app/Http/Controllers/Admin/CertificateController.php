<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Certificate; // Perbaikan typo spasi di kode lo tadi
use Illuminate\Support\Str;
use App\Models\Progress;
use App\Models\Content;

class CertificateController extends Controller
{
    public function index()
    {
        // 1. Ambil semua progress yang is_completed-nya true
        $allProgress = Progress::with(['user', 'course'])
            ->where('is_completed', true)
            ->get();

        // 2. Filter hanya student yang progresnya bener-bener 100%
        $pendingCertificates = $allProgress->filter(function ($progress) {
            // Hitung total materi yang ada di kursus tersebut
            $totalMateri = Content::where('course_id', $progress->course_id)->count();

            // Hitung berapa materi yang sudah diselesaikan student ini di kursus tersebut
            $userCompleted = Progress::where('user_id', $progress->user_id)
                ->where('course_id', $progress->course_id)
                ->where('is_completed', true)
                ->whereNotNull('content_id') // Menghitung materi saja
                ->count();

            // Hanya kembalikan true jika jumlahnya sama (100%)
            // Jika total materi 0, kita anggap belum layak (mencegah error)
            return $totalMateri > 0 && $userCompleted >= $totalMateri;
        })->unique(function ($item) {
            return $item->user_id . $item->course_id;
        });

        return view('admin.certificates.index', compact('pendingCertificates'));
    }

    public function show($id)
    {
        $course = Course::with(['users.progress' => function ($query) use ($id) {
            $query->where('course_id', $id);
        }])->findOrFail($id);

        return view('admin.quiz.show', compact('course'));
    }

    public function issue($userId, $courseId)
    {
        $existing = Certificate::where('user_id', $userId)->where('course_id', $courseId)->first();

        if (!$existing) {
            Certificate::create([
                'user_id' => $userId,
                'course_id' => $courseId,
                'certificate_number' => 'EV-' . date('Y') . '-' . strtoupper(Str::random(8)), // Gue tambah jadi 8 digit biar lebih unik
                'issued_at' => now(),
            ]);
        }

        return back()->with('success', 'Sertifikat berhasil divalidasi!');
    }

    public function preview($id)
    {
        $certificate = Certificate::with(['user', 'course'])->findOrFail($id);
        return view('admin.certificates.preview', compact('certificate'));
    }
}
