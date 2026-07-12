<?php

namespace App\Http\Controllers\Web\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $selectedClass = $request->input('class_level');
        $coursesQuery = Course::where('course_type', 'school');
        if (!empty($selectedClass)) {
            $coursesQuery->where('grade_level', $selectedClass);
        }
        $coursesGrouped = $coursesQuery->withCount(['chapters'])
            ->latest()
            ->get()
            ->groupBy('grade_level');
        $allSelectCourses = Course::where('course_type', 'school')->latest()->get();

        return view('teacher.material.index', compact('coursesGrouped', 'allSelectCourses', 'selectedClass'));
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'grade_level' => 'required|in:7,8,9',
            'description' => 'nullable|string',
        ]);

        Course::create([
            'title' => $request->title,
            'grade_level' => $request->grade_level,
            'course_type' => 'school',
            'category' => 'Sekolah',
            'description' => $request->description ?? '-',
            'price' => 0,
            'rating' => 0.0,
            'image' => ''
        ]);

        return redirect()->back()->with('success', 'Mata Pelajaran baru berhasil didaftarkan!');
    }

    public function storeChapter(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
        ]);

        Chapter::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
        ]);

        return redirect()->back()->with('success', 'Bab baru berhasil ditambahkan ke dalam kurikulum!');
    }

    public function manage($id)
    {
        $course = Course::where('course_type', 'school')->with(['chapters.lessons'])->findOrFail($id);

        return view('teacher.material.manage', compact('course'));
    }

    public function destroyChapter($id)
    {
        $chapter = Chapter::findOrFail($id);
        $chapter->delete();

        return redirect()->back()->with('success', 'Bab beserta seluruh data pertemuan di dalamnya berhasil dihapus!');
    }

    public function destroyLesson($id)
    {
        $lesson = \App\Models\Lesson::findOrFail($id);
        $lesson->delete();

        return redirect()->back()->with('success', 'Pertemuan berhasil dihapus dari daftar bab!');
    }

    public function storeLesson(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'title' => 'required|string|max:255',
        ]);

        $lesson = \App\Models\Lesson::create([
            'chapter_id' => $request->chapter_id,
            'title' => $request->title,
            'video_url' => null,
            'content_text' => null,
        ]);

        return redirect()->route('teacher.material.edit_content', $lesson->id)
            ->with('success', 'Pertemuan berhasil terdaftar! Silakan lengkapi materi diferensiasi di bawah ini.');
    }

    public function editContent($id)
    {
        $lesson = \App\Models\Lesson::with(['chapter.course'])->findOrFail($id);

        return view('teacher.material.edit_content', compact('lesson'));
    }

    public function updateContent(Request $request, $id)
    {
        $request->validate([
            'video_url' => 'nullable|url|max:255',
            'content_text' => 'nullable|string',
        ]);

        $lesson = \App\Models\Lesson::findOrFail($id);
        $lesson->update([
            'video_url' => $request->video_url,
            'content_text' => $request->content_text,
        ]);

        return redirect()->route('teacher.material.manage', $lesson->chapter->course_id)
            ->with('success', 'Materi pembelajaran diferensiasi berhasil diperbarui!');
    }

    public function updateCourse(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'grade_level' => 'required|in:7,8,9',
            'description' => 'nullable|string',
        ]);

        $course = Course::where('course_type', 'school')->findOrFail($id);
        $course->update([
            'title' => $request->title,
            'grade_level' => $request->grade_level,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Mata pelajaran berhasil diperbarui!');
    }
}
