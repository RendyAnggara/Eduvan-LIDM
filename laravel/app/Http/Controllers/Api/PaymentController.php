<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // 1. Fungsi ketika siswa menekan tombol "Beli Kursus" di aplikasi
    public function requestPayment(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'amount' => 'required|numeric'
        ]);

        $user = $request->user();
        $referenceId = 'EV-' . time() . '-' . Str::upper(Str::random(5));

        // Simpan data ke database dengan status awal 'pending'
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'course_id' => $request->course_id,
            'reference_id' => $referenceId,
            'amount' => $request->amount,
            'status' => 'pending',
            'payment_url' => 'https://checkout.dompetx.com/simulate-link/' . $referenceId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibuat',
            'data' => $transaction
        ]);
    }

    // 2. Fungsi Webhook/Callback untuk menerima laporan lunas dari DompetX (Simulasi Postman)
    public function handleCallback(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required',
            'status' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $transaction = Transaction::where('reference_id', $request->transaction_id)->first();

            if (!$transaction) {
                return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
            }

            if ($transaction->status === 'success') {
                return response()->json(['message' => 'Transaksi ini sudah sukses sebelumnya']);
            }

            if ($request->status === 'SUCCESS') {
                // Ubah transaksi menjadi sukses
                $transaction->status = 'success';
                $transaction->save();

                // Buka akses kursus secara otomatis di tabel enrollments
                $isEnrolled = Enrollment::where('user_id', $transaction->user_id)
                    ->where('course_id', $transaction->course_id)
                    ->exists();

                // ... kode di atasnya sama ...

                if (!$isEnrolled) {
                    Enrollment::create([
                        'user_id' => $transaction->user_id,
                        'course_id' => $transaction->course_id,
                        'progress' => 0,
                        'is_quiz_unlocked' => 0,
                        'status' => 'active',
                        'price_bought' => $transaction->amount // 🟢 TAMBAHKAN BARIS INI
                    ]);
                }

                // ... kode di bawahnya sama ...
            }

            DB::commit();
            return response()->json(['message' => 'Callback DompetX berhasil diproses!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
