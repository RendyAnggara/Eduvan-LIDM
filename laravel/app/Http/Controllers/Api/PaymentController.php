<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
            'amount' => 'required|numeric',
            'payment_method' => 'required|string' // qris, va, atau wallet
        ]);

        $user = $request->user();
        $referenceId = 'EV-' . time() . '-' . Str::upper(Str::random(5));
        $method = strtolower($request->payment_method);

        // Dapatkan data spesifik dari DompetX berdasarkan metode bayar
        $paymentData = [];

        if ($method === 'qris') {
            // 1. String QRIS Standar Nasional (EMVCo Standard)
            // Jika ada API DompetX asli, ambil dari $responseDompetX['qris_string']
            $qrisPayload = "00020101021226680016ID.CO.DOMPETX.WWW01189360091430000000005204581253033605802ID5911EduVan Class6007Jakarta61051234562070703A016304A1B2";

            // 2. Buat URL gambar QR dari string QRIS di atas (URL encoded)
            $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrisPayload);

            $paymentData = [
                'type' => 'qris',
                'qr_content' => $qrisPayload,
                'qr_image_url' => $qrImageUrl
            ];
        } elseif ($method === 'va') {
            // Contoh: Nomor Virtual Account
            $paymentData = [
                'type' => 'va',
                'bank_name' => 'Bank Mandiri',
                'va_number' => '88012' . rand(10000000, 99999999)
            ];
        } else {
            $paymentData = [
                'type' => 'wallet',
                'wallet_name' => 'DompetX Pay',
                'pay_code' => 'DX-' . rand(1000, 9999)
            ];
        }

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'course_id' => $request->course_id,
            'reference_id' => $referenceId,
            'amount' => $request->amount,
            'status' => 'pending',
            'payment_url' => $paymentData['qr_image_url'] ?? null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibuat',
            'reference_id' => $referenceId,
            'payment_info' => $paymentData
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
