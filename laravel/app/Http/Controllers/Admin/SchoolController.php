<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::withCount([
            'users as total_teachers' => function ($query) {
                $query->where('role', 'teacher');
            },
            'users as total_students' => function ($query) {
                $query->where('role', 'student');
            },
        ])
            ->latest()
            ->get();

        $recentActivities = User::whereNotNull('school_id')->with('school')->latest()->take(4)->get();

        return view('admin.schools.index', compact('schools', 'recentActivities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:schools,name',
            'address' => 'nullable|string|max:255',
        ]);

        School::create([
            'name' => $request->name,
            'address' => $request->address,
        ]);

        return redirect()->route('admin.schools.index')->with('success', 'Instansi Sekolah baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $school = School::findOrFail($id);

        $request->validate([
            // Abaikan validasi unique jika nama sekolah tidak diubah
            'name' => 'required|string|max:100|unique:schools,name,' . $school->id,
            'address' => 'nullable|string|max:255',
        ]);

        $school->update([
            'name' => $request->name,
            'address' => $request->address,
        ]);

        return redirect()->route('admin.schools.index')->with('success', 'Data Instansi Sekolah berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $school = School::findOrFail($id);

        if ($school->users()->exists()) {
            return redirect()->route('admin.schools.index')->with('error', 'Gagal menghapus! Masih ada akun guru/siswa yang terikat dengan sekolah ini.');
        }

        $school->delete();

        return redirect()->route('admin.schools.index')->with('success', 'Instansi Sekolah berhasil dihapus!');
    }
}
