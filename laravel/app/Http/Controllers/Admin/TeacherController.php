<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    public function storeStudent(Request $request)
    {
        // 1. Validasi utama: Input harus berupa array bernama 'students'
        $validator = Validator::make($request->all(), [
            'students' => 'required|array|min:1',
            'students.*.name' => 'required|string|max:255',
            'students.*.email' => 'required|email|unique:users,email',
            'students.*.nisn_or_nip' => 'required|string|unique:users,nisn_or_nip',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, ada email atau NISN yang sudah terdaftar atau format salah.',
                'errors' => $validator->errors()
            ], 422);
        }

        $createdCount = 0;
        $studentData = $request->input('students');

        try {
            // 2. Looping data siswa menggunakan metode manual objek yang terbukti sukses tadi
            foreach ($studentData as $data) {
                $newUser = new User();
                $newUser->name = $data['name'];
                $newUser->email = $data['email'];
                $newUser->role = 'student';
                $newUser->nisn_or_nip = $data['nisn_or_nip'];
                $newUser->password = Hash::make('eduvan123'); // Password bawaan anak SMP
                $newUser->email_verified_at = now();
                $newUser->save(); // Langsung eksekusi ke DB

                $createdCount++;
            }

            return response()->json([
                'status' => 'success',
                'message' => "Berhasil mendaftarkan sebanyak {$createdCount} siswa SMP ke EduVan!",
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data massal: ' . $e->getMessage()
            ], 500);
        }
    }
}
