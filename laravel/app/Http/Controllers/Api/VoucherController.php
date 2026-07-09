<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    /**
     * Fitur Siswa untuk melakukan klaim/redeem kode voucher kelas
     */
    public function redeemVoucher(Request $request)
    {
        // 1. Validasi input kode voucher harus diisi
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Cari vouchernya di database
        $voucher = Voucher::where('code', strtoupper($request->code))->first();

        // 3. SELEKSI KONDISI KEAMANAN:
        // Jika voucher kagak ketemu
        if (!$voucher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode voucher tidak valid atau tidak terdaftar!'
            ], 404);
        }

        // Jika voucher ternyata sudah pernah dipakai orang lain
        if ($voucher->is_used) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode voucher ini sudah hangus atau sudah pernah digunakan!'
            ], 400);
        }

        $userId = $request->user()->id; // Ambil ID siswa yang lagi login

        // Jika siswa ternyata sudah terdaftar di kelas ini sebelumnya
        $alreadyEnrolled = DB::table('course_user')
            ->where('user_id', $userId)
            ->where('course_id', $voucher->course_id)
            ->exists();

        if ($alreadyEnrolled) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kamu sudah terdaftar di kelas Informatika ini sebelumnya!'
            ], 400);
        }

        // 4. EKSEKUSI PROSES JIKA LOLOS SEMUA VALIDASI
        DB::beginTransaction();
        try {
            // Ubah status voucher jadi terpakai
            $voucher->is_used = true;
            $voucher->save();

            // Daftarkan ID siswa dan ID kelas ke tabel pivot course_user
            DB::table('course_user')->insert([
                'user_id' => $userId,
                'course_id' => $voucher->course_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Selamat! Kode voucher berhasil diklaim. Kamu sekarang punya akses penuh ke kelas Informatika!',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengklaim voucher: ' . $e->getMessage()
            ], 500);
        }
    }
}
