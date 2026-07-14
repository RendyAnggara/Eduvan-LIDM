<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $schoolId = Auth::user()->school_id;
        $schoolName = Auth::user()->school ? Auth::user()->school->name : 'Sekolah Terdaftar';
        $nisn = $row['nisn'] ?? $row['nisn_siswa'] ?? null;
        $name = $row['nama'] ?? $row['nama_lengkap'] ?? null;
        $email = $row['email'] ?? $row['email_siswa'] ?? null;
        $class = $row['kelas'] ?? $row['tingkat_kelas'] ?? null;
        $plainPassword = $row['password'] ?? $row['password_awal'] ?? null;

        if (!$nisn || !$email || !$name || !$plainPassword) {
            return null;
        }

        $existingUser = User::where('nisn_or_nip', $nisn)->orWhere('email', $email)->first();
        if ($existingUser) {
            return null;
        }

        $student = new User([
            'nisn_or_nip' => $nisn,
            'name'        => $name,
            'email'       => $email,
            'class'       => $class,
            'role'        => 'student',
            'school_id'   => $schoolId,
            'password'    => Hash::make($plainPassword),
        ]);

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = env('MAIL_PORT', 465);
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME', 'EduLearn'));

            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = '[AKUN BARU] Informasi Aktivasi Akun Belajar Edulearn Anda';

            $mail->Body    = "
                <div style='font-family: sans-serif; background-color: #f1f5f9; padding: 20px; color: #334155;'>
                    <div style='max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 16px; border: 1px solid #e2e8f0;'>
                        <h2 style='color: #0d9488; margin-bottom: 5px;'>" . env('MAIL_FROM_NAME', 'EduLearn') . "</h2>
                        <p style='font-size: 12px; color: #94a3b8; margin-top: 0;'>Pendidikan Inklusif Berdiferensiasi</p>
                        <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>

                        <p>Halo <strong>{$name}</strong>,</p>
                        <p>Akun belajar Edulearn Anda telah berhasil didaftarkan oleh Guru untuk tingkat <strong>{$class}</strong> di <strong>{$schoolName}</strong>.</p>
                        <p>Berikut rincian akun Anda untuk masuk ke aplikasi mobile:</p>

                        <div style='background-color: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #cbd5e1; margin: 20px 0;'>
                            <p style='margin: 5px 0;'><strong>Email Login:</strong> {$email}</p>
                            <p style='margin: 5px 0;'><strong>Password Awal:</strong> {$plainPassword}</p>
                        </div>

                        <p style='margin-top: 30px;'>Selamat Belajar,<br><strong>Tim Informasi Edulearn</strong></p>
                    </div>
                </div>
            ";

            $mail->send();
        } catch (\Exception $e) {
            \Log::error("PHPMailer Mass Import Row Error: " . $e->getMessage());
        }

        return $student;
    }
}
