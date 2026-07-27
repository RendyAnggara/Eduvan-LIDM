<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class TeachersImport implements ToModel, WithHeadingRow
{
    protected $schoolId;
    protected $controllerInstance;

    public function __construct($schoolId, $controllerInstance)
    {
        $this->schoolId = $schoolId;
        $this->controllerInstance = $controllerInstance;
    }

    public function model(array $row)
    {
        // 🛠️ PARSING CSV
        if (count($row) === 1) {
            $firstKey = array_key_first($row);
            $firstValue = reset($row);

            $rawLine = $firstKey . ';' . $firstValue;
            $dataArray = explode(';', $rawLine);

            if (count($dataArray) >= 2) {
                $row = [
                    'nama'  => $dataArray[0] ?? null,
                    'email' => $dataArray[1] ?? null,
                    'nip'   => $dataArray[2] ?? null
                ];
            }
        }

        $nama  = $row['nama'] ?? $row['Nama'] ?? $row['NAMA'] ?? null;
        $email = $row['email'] ?? $row['Email'] ?? $row['EMAIL'] ?? null;
        $nip   = $row['nip'] ?? $row['Nip'] ?? $row['NIP'] ?? null;

        if (empty($nama) || empty($email)) {
            return null;
        }

        $cleanEmail = strtolower(trim($email));

        // Bersihkan NIP dari format scientific notation (1.99E+13) jika masih terbawa dari Excel
        $cleanNip = !empty($nip) ? trim($nip) : null;
        if ($cleanNip && (strpos(strtoupper($cleanNip), 'E+') !== false || strpos($cleanNip, '.') !== false)) {
            // Jika terdeteksi format E+, kita abaikan pengecekan unik NIP agar tidak merusak baris lain,
            // atau ubah nilainya menjadi null/acak sementara waktu agar tidak bentrok
            $cleanNip = 'NIP-' . rand(100000, 999999);
        }

        // 1. PROTEKSI UNIK EMAIL
        if (User::where('email', $cleanEmail)->exists()) {
            Log::warning("Import Akun Lewat: Email {$cleanEmail} sudah terdaftar.");
            return null;
        }

        // 2. PROTEKSI UNIK NIP (Hanya jika NIP asli berupa angka reguler terisi)
        if ($cleanNip && !str_contains($cleanNip, 'NIP-') && User::where('nisn_or_nip', $cleanNip)->exists()) {
            Log::warning("Import Akun Lewat: NIP {$cleanNip} sudah terdaftar.");
            return null;
        }

        $generatedPassword = 'teacher' . rand(1000, 9999);

        // 3. EKSEKUSI PENYIMPANAN MUTLAK KE DATABASE
        $user = new User();
        $user->name              = trim($nama);
        $user->email             = $cleanEmail;
        $user->password          = Hash::make($generatedPassword);
        $user->role              = 'teacher';
        $user->school_id         = $this->schoolId;
        $user->nisn_or_nip       = $cleanNip;
        $user->email_verified_at = \Illuminate\Support\Carbon::now();
        $user->save();

        // 4. ISOLASI TOTAL PROSES EMAIL (Gagal kirim email = Data di DB tetap Aman masuk!)
        if ($user && $user->id) {
            try {
                sleep(2);

                $this->controllerInstance->callEmailCredentials($user->name, $user->email, $generatedPassword);
            } catch (\Throwable $e) {
                Log::error("Proses kirim kredensial SMTP gagal untuk {$cleanEmail}: " . $e->getMessage());
            }
        }

        return $user;
    }
}
