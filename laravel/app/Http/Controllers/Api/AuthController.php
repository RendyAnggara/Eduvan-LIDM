<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'Format email atau password tidak valid.'
            ], 422);
        }

        $user = User::where('email', $request->email)->where('role', 'student')->first();

        if (!$user || !Hash::check($request->password, $user->password))
        {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password salah. Periksa kembali data Anda.'
            ], 401);
        }

        if (!$user->email_verified_at)
        {
            $user->email_verified_at = now();
            $user->save();
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        $enrollmentsCount = DB::table('enrollments')->where('user_id', $user->id)->count();
        $certificatesCount = DB::table('certificates')->where('user_id', $user->id)->count();

        $userArray = $user->toArray();
        $userArray['enrollments_count'] = $enrollmentsCount;
        $userArray['certificates_count'] = $certificatesCount;

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'user' => $userArray
        ], 200);
    }
    public function createStudentByTeacher(Request $request)
    {
        $teacher = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:users',
            'nisn_or_nip' => 'nullable|string|max:20',
            'class' => 'required|in:Kelas 7,Kelas 8,Kelas 9'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $generatedPassword = 'edulearn' . rand(1000, 9999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($generatedPassword),
            'role' => 'student',
            'nisn_or_nip' => $request->nisn_or_nip,
            'school_id' => $teacher->school_id,
            'class' => $request->class,
            'email_verified_at' => now(),
            'avatar' => 'assets/icon/avatar-neutral.png',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Akun siswa berhasil disimpan ke database!',
            'user' => $user
        ], 201);
    }

    public function me(Request $request)
    {
        $user = Auth::user();

        if (!$user)
        {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan atau belum login.'
            ], 401);
        }

        $enrollmentsCount = DB::table('enrollments')->where('user_id', $user->id)->count();
        $certificatesCount = DB::table('certificates')->where('user_id', $user->id)->count();

        $userArray = $user->toArray();
        $userArray['enrollments_count'] = $enrollmentsCount;
        $userArray['certificates_count'] = $certificatesCount;

        return response()->json($userArray, 200);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini salah. Perubahan ditolak.'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Password akun Anda berhasil diperbarui!'
        ], 200);
    }

    public function logout(Request $request)
    {
        if ($request->user())
        {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Token akses mobile berhasil dicabut dari Server!'
        ], 200);
    }
}
