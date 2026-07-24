<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Content;
use App\Models\School;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $typeFilter = $request->input('course_type');
        $gradeFilter = $request->input('grade_level');

        $query = Course::query();

        // 1. Pencarian Teks Terintegrasi (Judul, Deskripsi, & Nama Sekolah via Guru)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  // Ambil nama sekolah dari relasi guru pengampu
                  ->orWhereHas('teachers.school', function ($ts) use ($search) {
                      $ts->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Filter Tipe Kursus (premium / school)
        if (!empty($typeFilter) && $typeFilter !== 'all') {
            $query->where('course_type', $typeFilter);
        }

        // 3. Filter Tingkat Kelas SMP (7, 8, 9)
        if (!empty($gradeFilter) && $gradeFilter !== 'all') {
            $query->where('grade_level', $gradeFilter);
        }

        // Load relasi teachers.school tanpa memanggil relasi 'school' langsung
        $courses = $query->with(['teachers.school'])->latest()->get();

        return view('admin.courses.index', compact(
            'courses',
            'search',
            'typeFilter',
            'gradeFilter'
        ));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'price' => 'required|integer',
            'category' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $base64Image = null;
        if ($request->hasFile('image'))
        {
            $image = $request->file('image');
            $imageData = base64_encode(file_get_contents($image));
            $imageMime = $image->getClientMimeType();

            $base64Image = 'data:' . $imageMime . ';base64,' . $imageData;
        }

        Course::create([
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
            'price' => $request->price,
            'rating' => 0,
            'image' => $base64Image,
            'course_type' => 'premium'
        ]);

        return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil ditambahkan!');
    }

    public function show($id)
    {
        $course = Course::with(['contents', 'school', 'teachers.school'])->findOrFail($id);
        return view('admin.courses.show', compact('course'));
    }

    public function storeContent(Request $request, $course_id)
    {
        $course = Course::findOrFail($course_id);
        if ($course->course_type === 'school') {
            return redirect()->route('admin.courses.show', $course_id)
                ->with('error', 'Akses ditolak! Materi kursus instansi sekolah dikelola langsung oleh Guru.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'required|string',
            'order' => 'required|integer'
        ]);

        Content::create([
            'course_id'   => $course_id,
            'title'       => $request->title,
            'content_url' => $request->video_url,
            'type'        => 'video',
            'order'       => $request->order,
        ]);

        return redirect()->route('admin.courses.show', $course_id)
            ->with('success', 'Materi pembelajaran berhasil ditambahkan, bre!');
    }

    public function destroyContent($content_id)
    {
        $content = Content::findOrFail($content_id);
        $course = Course::findOrFail($content->course_id);

        if ($course->course_type === 'school') {
            return redirect()->route('admin.courses.show', $course->id)
                ->with('error', 'Akses ditolak! Materi kursus instansi sekolah dikelola langsung oleh Guru.');
        }

        $content->delete();

        return redirect()->route('admin.courses.show', $course->id)
            ->with('success', 'Materi pembelajaran berhasil dihapus, bre!');
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        if ($course->course_type === 'school') {
            return redirect()->route('admin.courses.index')
                ->with('error', 'Akses ditolak! Kursus instansi sekolah hanya dapat diubah oleh Guru pengampu.');
        }

        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        if ($course->course_type === 'school') {
            return redirect()->route('admin.courses.index')
                ->with('error', 'Akses ditolak! Kursus instansi sekolah hanya dapat diubah oleh Guru pengampu.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'price' => 'required|integer',
            'category' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240'
        ]);

        $data = [
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
            'price' => $request->price,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageData = base64_encode(file_get_contents($image));
            $imageMime = $image->getClientMimeType();

            $data['image'] = 'data:' . $imageMime . ';base64,' . $imageData;
        }

        $course->update($data);

        return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil dihapus!');
    }
}
