<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function requestPayment(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = $request->user();

        // 1. Ambil data course murni langsung dari database
        $course = Course::findOrFail($request->course_id);

        // 2. Bersihkan karakter titik/koma/spasi jika harga di DB tersimpan sebagai string (contoh: "50.000")
        $cleanAmount = (int) str_replace(['.', ',', ' '], '', $course->price);

        $referenceId = 'EV-' . time() . '-' . Str::upper(Str::random(5));

        // URL Web Simulator Checkout DompetX
        $paymentUrl = 'https://checkout.dompetx.com/pay/' . $referenceId;

        // 3. GENERATE QR CODE DINAMIS DARI PAYMENT URL / REF ID
        // Menggunakan API QR Server gratis untuk merender gambar QRIS secara otomatis
        $qrImage = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($paymentUrl);

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'reference_id' => $referenceId,
            'amount' => $cleanAmount,
            'status' => 'pending',
            'payment_url' => $paymentUrl
        ]);

        // 4. Return JSON lengkap dengan 'qrData' & 'expiresAt' agar dibaca sempurna oleh Ionic
        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibuat',
            'data' => [
                'id' => $transaction->id,
                'amount' => $transaction->amount,
                'reference_id' => $transaction->reference_id,
                'status' => $transaction->status,
                'payment_url' => $transaction->payment_url,
                'expiresAt' => date('c', strtotime('+1 hour')), // Expire 1 jam dari sekarang
                'qrData' => [
                    'qrImage' => $qrImage,
                    'qrString' => $paymentUrl,
                    'refId' => $referenceId,
                ]
            ]
        ]);
    }

    public function handleCallback(Request $request)
    {
        $refId = $request->input('reference_id')
            ?? $request->input('reference')
            ?? $request->input('transaction_id');

        $status = strtoupper($request->input('status', ''));

        if (!$refId) {
            return response()->json(['message' => 'Parameter reference_id wajib diisi'], 400);
        }

        DB::beginTransaction();

        try {
            $transaction = Transaction::where('reference_id', $refId)->first();

            if (!$transaction) {
                return response()->json(['message' => 'Transaksi tidak ditemukan: ' . $refId], 404);
            }

            if (in_array(strtoupper($transaction->status), ['SUCCESS', 'PAID', 'SETTLED'])) {
                return response()->json(['message' => 'Transaksi ini sudah sukses sebelumnya']);
            }

            if (in_array($status, ['SUCCESS', 'PAID', 'SETTLED'])) {
                $transaction->status = 'success';
                $transaction->save();

                $isEnrolled = Enrollment::where('user_id', $transaction->user_id)
                    ->where('course_id', $transaction->course_id)
                    ->exists();

                if (!$isEnrolled) {
                    Enrollment::create([
                        'user_id' => $transaction->user_id,
                        'course_id' => $transaction->course_id,
                        'progress' => 0,
                        'is_quiz_unlocked' => 0,
                        'status' => 'active',
                        'price_bought' => $transaction->amount
                    ]);
                }
            } elseif (in_array($status, ['EXPIRED', 'FAILED', 'CANCELLED'])) {
                $transaction->status = strtolower($status);
                $transaction->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Callback DompetX berhasil diproses!',
                'data' => [
                    'reference_id' => $transaction->reference_id,
                    'status' => $transaction->status
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
