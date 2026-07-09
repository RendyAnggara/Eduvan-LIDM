<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Order; // Pastikan lu udah bikin Model Order ya!
use App\Models\Voucher; // Pastikan lu udah bikin Model Voucher ya!

class OrderController extends Controller
{
    /**
     * Simulasi Pembelian Kuota Kelas oleh Guru & Auto-Generate Voucher
     */
    public function checkoutSimulated(Request $request)
    {
        // 1. Validasi input pembelian dari Guru
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id', // Harus course yang beneran ada
            'quantity' => 'required|integer|min:1|max:100', // Jumlah kuota siswa (misal 5 s.d 100)
            'gross_amount' => 'required|integer', // Total harga simulasi
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Gunakan DB Transaction biar kalau salah satu proses gagal, database gak berantakan
        DB::beginTransaction();

        try {
            // 2. Generate Order ID unik tiruan
            $orderId = 'EDUVAN-' . now()->format('YmdHis') . '-' . rand(100, 999);

            // 3. Buat data transaksi di tabel orders (Simulasi langsung SUCCESS)
            // Di sini kita pakai instansiasi objek manual seperti kemarin biar anti mass-assignment
            $order = new Order();
            $order->order_id = $orderId;
            $order->user_id = $request->user()->id; // ID Guru yang lagi login
            $order->course_id = $request->course_id;
            $order->quantity = $request->quantity;
            $order->gross_amount = $request->gross_amount;
            $order->status = 'success'; // 🟢 Kita paksa langsung sukses buat simulasi
            $order->save();

            // 4. ROBOT LOOPING: Lahirkan Kode Voucher Sebanyak Quantity yang Dibeli
            $vouchersCreated = [];
            for ($i = 0; $i < $request->quantity; $i++) {

                // Bikin kombinasi kode unik acak sepanjang 8 karakter kapital, misal: EDV-78AX92
                $uniqueCode = 'EDV-' . strtoupper(Str::random(6));

                // Pastikan kode gak kembar di database (jaga-jaga)
                while (Voucher::where('code', $uniqueCode)->exists()) {
                    $uniqueCode = 'EDV-' . strtoupper(Str::random(6));
                }

                $voucher = new Voucher();
                $voucher->order_id = $order->id; // Hubungkan ke ID order barusan
                $voucher->course_id = $request->course_id;
                $voucher->code = $uniqueCode;
                $voucher->is_used = false; // Default belum terpakai siswa
                $voucher->save();

                $vouchersCreated[] = $uniqueCode; // Masukin ke array buat ditampilin di response
            }

            // Jika semua lancar, simpan permanen ke database
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Simulasi pembelian sukses! Berhasil membuat {$request->quantity} kode voucher baru untuk Guru.",
                'order_id' => $orderId,
                'total_kuota' => $request->quantity,
                'daftar_voucher' => $vouchersCreated // Ivan bisa nampilin daftar ini di frontend
            ], 201);
        } catch (\Exception $e) {
            // Jika ada eror di tengah jalan, batalkan semua inputan tabel biar gak corrupt
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses simulasi transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
}
