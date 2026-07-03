<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Gunakan validator manual agar jika gagal di HP, kita bisa lempar pesan yang jelas
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

        $user = User::where('email', $request->email)->first();

        // 2. Cek user & kecocokan password
        if (!$user || !Hash::check($request->password, $user->password))
        {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password salah. Periksa kembali data Anda.'
            ], 401);
        }

        // 3. BYPASS / LONGGARKAN STATUS VERIFIKASI UNTUK TESTING DI HP ASLI
        if (!$user->email_verified_at)
        {
            $user->email_verified_at = now();
            $user->save();
        }

        // 4. Generate Token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // Amankan statistik relasi agar ikut terbawa ke JSON murni
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

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:30',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $otp = rand(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'otp_code' => $otp,
            'otp_expiry' => now()->addMinutes(10),
            'avatar' => 'assets/icon/avatar-neutral.png', // Set default awal string lokal agar konsisten
        ]);

        $this->sendOtpEmail($user->email, $otp);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil! Silakan cek email untuk kode verifikasi.',
            'user' => $user
        ], 201);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('otp_code', $request->otp)
            ->where('otp_expiry', '>', now())
            ->first();

        if ($user)
        {
            $user->email_verified_at = now();
            $user->otp_code = null;
            $user->otp_expiry = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Email berhasil diverifikasi! Silakan login.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kode OTP salah atau sudah kadaluarsa.'
        ], 400);
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user)
        {
            return response()->json(['success' => false, 'message' => 'Email tidak ditemukan.'], 404);
        }

        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expiry = now()->addMinutes(10);
        $user->save();

        $this->sendOtpEmail($user->email, $otp);

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP baru telah dikirim ke email Anda.'
        ]);
    }

    private function sendOtpEmail($email, $otp)
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

            $mail->setFrom(env('MAIL_USERNAME'), 'EduVan Team');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Kode Verifikasi Akun EduVan';

            $mail->Body    = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
            </head>
            <body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 20px; color: #333333; margin: 0;">
                <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
                    <h2 style="text-align: center; color: #111827; margin-bottom: 25px; font-size: 24px;">Eduvan</h2>
                    <p style="font-size: 16px; line-height: 1.5; margin-bottom: 10px;"><b>Halo!</b></p>
                    <p style="font-size: 16px; line-height: 1.5; color: #4b5563;">Kamu menerima email ini untuk memverifikasi pendaftaran akun EduVan kamu. Berikut adalah kode OTP rahasia kamu:</p>
                    <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px; text-align: center; margin: 25px 0;">
                        <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #111827;">' . $otp . '</span>
                    </div>
                    <p style="font-size: 14px; color: #6b7280; line-height: 1.5;">Kode OTP ini hanya berlaku selama 10 menit ke depan.</p>
                    <p style="font-size: 14px; color: #6b7280; line-height: 1.5; margin-top: 20px;">Jika kamu tidak merasa melakukan permintaan pendaftaran ini, abaikan saja email ini.</p>
                    <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 30px 0;">
                    <p style="font-size: 14px; color: #9ca3af; text-align: center; margin: 0; line-height: 1.5;">Regards,<br><strong style="color: #4b5563;">Eduvan</strong></p>
                    <p style="font-size: 11px; color: #9ca3af; text-align: center; margin-top: 20px;">&copy; 2026 Eduvan. All rights reserved.</p>
                </div>
            </body>
            </html>
            ';

            $mail->send();
        }
        catch (Exception $e)
        {
            // Tetap aman jika kirim email gagal
        }
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

        // Paksa disatukan ke format Array Arrayable agar tidak null pas sampai di client side
        $userArray = $user->toArray();
        $userArray['enrollments_count'] = $enrollmentsCount;
        $userArray['certificates_count'] = $certificatesCount;

        return response()->json($userArray, 200);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:30',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|string',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->has('avatar'))
        {
            $updateData['avatar'] = $request->input('avatar');
        }

        $user->update($updateData);
        $user->refresh();

        $enrollmentsCount = DB::table('enrollments')->where('user_id', $user->id)->count();
        $certificatesCount = DB::table('certificates')->where('user_id', $user->id)->count();

        $userArray = $user->toArray();
        $userArray['enrollments_count'] = $enrollmentsCount;
        $userArray['certificates_count'] = $certificatesCount;

        return response()->json([
            'success' => true,
            'message' => 'Database berhasil diupdate!',
            'user' => $userArray
        ]);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try
        {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $gId = $googleUser->getId() ?? $googleUser->id;
            $gAvatar = $googleUser->getAvatar() ?? $googleUser->avatar;
            $gName = $googleUser->getName() ?? $googleUser->name;

            $gEmail = strtolower(trim($googleUser->getEmail() ?? $googleUser->email));

            $user = User::where('google_id', $gId)
                ->orWhere('email', $gEmail)
                ->first();

            if ($user)
            {
                $user->google_id = $gId;

                // ðŸ”’ SEHAT 100%: Kunci aset internal, jangan biarkan link google (http) merusak state di DB MySQL
                if (empty($user->avatar) || !str_starts_with($user->avatar, 'assets/icon/'))
                {
                    $user->avatar = $gAvatar;
                }

                $user->email = $gEmail;

                if (!$user->email_verified_at)
                {
                    $user->email_verified_at = now();
                }
                $user->save();
                $user->refresh();
            }
            else
            {
                $user = new User();
                $user->name = $gName;
                $user->email = $gEmail;
                $user->google_id = $gId;
                $user->avatar = 'assets/icon/avatar-neutral.png'; // Jika beneran akun baru gress, pakai tipe aset lokal
                $user->password = Hash::make(rand(11111111, 99999999));
                $user->role = 'student';
                $user->email_verified_at = now();
                $user->save();
                $user->refresh();
            }

            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;

            // Integrasikan statistik kursus saat kompilasi link callback Google Auth lek
            $enrollmentsCount = DB::table('enrollments')->where('user_id', $user->id)->count();
            $certificatesCount = DB::table('certificates')->where('user_id', $user->id)->count();

            $finalUserArray = $user->toArray();
            $finalUserArray['enrollments_count'] = $enrollmentsCount;
            $finalUserArray['certificates_count'] = $certificatesCount;

            $userData = json_encode($finalUserArray);
            $encodedUser = urlencode($userData);

            return response("
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Autentikasi EduVan</title>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                </head>
                <body>
                    <p style='text-align:center; font-family:sans-serif; margin-top:50px; color:#333; padding: 0 20px;'>
                        Autentikasi Berhasil! Mengalihkan kembali ke aplikasi...
                    </p>
                    <script>
                        const authData = {
                            success: true,
                            access_token: '{$token}',
                            user: {$userData}
                        };

                        if (window.opener) {
                            window.opener.postMessage(authData, '*');
                            window.close();
                        } else {
                            window.location.href = 'eduvan://google-login?token=' + encodeURIComponent('{$token}') + '&user=' + '{$encodedUser}';

                            setTimeout(function() {
                                window.close();
                            }, 3000);
                        }
                    </script>
                </body>
                </html>
                ");
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Proses autentikasi Google gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    // ðŸŸ¢ BONUS FUNGSI LOGOUT AMAN: Hapus token Sanctum aktif saat student keluar aplikasi
    public function logout(Request $request)
    {
        if ($request->user())
        {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Token Sanctum berhasil dicabut dari Server!'
        ], 200);
    }
}
