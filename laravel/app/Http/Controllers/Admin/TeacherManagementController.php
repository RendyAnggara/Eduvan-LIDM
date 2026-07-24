<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Imports\TeachersImport;
use Maatwebsite\Excel\Facades\Excel;

class TeacherManagementController extends Controller
{
    public function index()
    {
        $teachers = User::where('role', 'teacher')->with('school')->latest()->get();

        $schools = School::all();

        $recentActivities = User::where('role', 'teacher')
            ->with('school')
            ->latest()
            ->take(4)
            ->get();

        return view('admin.teachers.index', compact('teachers', 'schools', 'recentActivities'));
    }

    public function store(Request $request)
    {
        if ($request->has('email')) {
            $cleanedEmail = strtolower(trim($request->email));
            $request->merge(['email' => $cleanedEmail]);
        }

        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:users,email',
            'school_id' => 'required|exists:schools,id',
            'nisn_or_nip' => 'nullable|string|max:20'
        ]);

        $emailExist = User::where('email', $request->email)->exists();
        if ($emailExist) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['email' => 'Email ini sudah terdaftar di sistem sebagai siswa atau guru lain!']);
        }

     $generatedPassword = 'teacher' . rand(1000, 9999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($generatedPassword),
            'role' => 'teacher',
            'school_id' => $request->school_id,
            'nisn_or_nip' => $request->nisn_or_nip,
            'email_verified_at' => now(),
            'avatar' => 'assets/icon/avatar-neutral.png',
        ]);

        $this->sendTeacherCredentialsEmail($user->name, $user->email, $generatedPassword);

        return redirect()->route('admin.teachers.index')->with('success', 'Akun Guru berhasil dibuat dan informasi login telah dikirim ke email!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'school_id' => 'required|exists:schools,id',
            'nisn_or_nip' => 'nullable|string|max:20'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'school_id' => $request->school_id,
            'nisn_or_nip' => $request->nisn_or_nip
        ]);

        return redirect()->route('admin.teachers.index')->with('success', 'Data Guru berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Akun Guru berhasil dihapus dari sistem!');
    }

    private function sendTeacherCredentialsEmail($name, $email, $password)
    {
        $mail = new PHPMailer(true);
        try
        {
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = (env('MAIL_PORT') == 465) ? 'ssl' : 'tls';
            $mail->Port       = env('MAIL_PORT');

            $mail->setFrom(env('MAIL_USERNAME'), 'EduLearn Admin Pusat');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Akses Login Akun Guru EduLearn';

            $mail->Body    = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
            </head>
            <body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 20px; color: #333333; margin: 0;">
                <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
                    <h2 style="text-align: center; color: #4F46E5; margin-bottom: 25px; font-size: 24px;">EduLearn</h2>
                    <p style="font-size: 16px; line-height: 1.5; margin-bottom: 10px;"><b>Halo Bapak/Ibu ' . htmlspecialchars($name) . ',</b></p>
                    <p style="font-size: 16px; line-height: 1.5; color: #4b5563;">Akun pengajar/guru Anda di platform EduLearn telah berhasil didaftarkan oleh Admin Pusat. Silakan gunakan informasi di bawah ini untuk mengakses dashboard manajemen kelas Anda:</p>

                    <div style="background-color: #f8fafc; border-left: 4px solid #4F46E5; border-radius: 4px; padding: 15px; margin: 25px 0;">
                        <p style="margin: 5px 0; font-size: 16px;"><strong>Email Login:</strong> ' . htmlspecialchars($email) . '</p>
                        <p style="margin: 5px 0; font-size: 16px;"><strong>Password Default:</strong> <span style="color: #4F46E5; font-weight: bold; letter-spacing: 1px;">' . $password . '</span></p>
                    </div>

                    <p style="font-size: 14px; color: #ef4444; line-height: 1.5;"><em>Catatan: Demi keamanan data instansi sekolah Anda, dimohon segera mengubah password default Anda di halaman profil setelah berhasil login pertama kali.</em></p>

                    <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 30px 0;">
                    <p style="font-size: 14px; color: #9ca3af; text-align: center; margin: 0; line-height: 1.5;">Selamat Mengajar!<br><strong style="color: #4b5563;">Tim Administrasi EduLearn Pusat</strong></p>
                    <p style="font-size: 11px; color: #9ca3af; text-align: center; margin-top: 20px;">&copy; 2026 EduLearn. All rights reserved.</p>
                </div>
            </body>
            </html>
            ';

            $mail->send();
        }
        catch (\Exception $e)
        {
            \Illuminate\Support\Facades\Log::error("Gagal kirim email: " . $e->getMessage());
        }
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            Excel::import(new TeachersImport($request->school_id, $this), $request->file('excel_file'));

            return redirect()->route('admin.teachers.index')->with('success', 'Data massal akun pengajar berhasil di-import dan antrean email kredensial login telah dikirim!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membaca file Excel: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=template_import_guru_edulearn.csv',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['nama', 'email', 'nip']);
            fputcsv($file, ['Budi Setiawan, S.Pd', 'budiguru@gmail.com', '19881234567890']);
            fputcsv($file, ['Dewi Lestari, M.Pd', 'dewiguru@edulearn.com', '19920987654321']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function callEmailCredentials($name, $email, $password)
    {
        return $this->sendTeacherCredentialsEmail($name, $email, $password);
    }
}
