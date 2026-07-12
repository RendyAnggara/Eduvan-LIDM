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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:users',
            'nisn_or_nip' => 'nullable|string|max:20'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        $generatedPassword = 'eduvan' . rand(1000, 9999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($generatedPassword),
            'role' => 'student',
            'nisn_or_nip' => $request->nisn_or_nip,
            'email_verified_at' => now(),
            'avatar' => 'assets/icon/avatar-neutral.png',
        ]);
        $this->sendStudentCredentialsEmail($user->name, $user->email, $generatedPassword);

        return response()->json([
            'success' => true,
            'message' => 'Akun siswa berhasil dibuat! Kredensial telah dikirim ke email siswa.',
            'user' => $user
        ], 201);
    }
    private function sendStudentCredentialsEmail($name, $email, $password)
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
            $mail->Subject = 'Akses Login Aplikasi EduVan';

            $mail->Body    = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
            </head>
            <body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 20px; color: #333333; margin: 0;">
                <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
                    <h2 style="text-align: center; color: #00A896; margin-bottom: 25px; font-size: 24px;">Eduvan</h2>
                    <p style="font-size: 16px; line-height: 1.5; margin-bottom: 10px;"><b>Halo ' . htmlspecialchars($name) . ',</b></p>
                    <p style="font-size: 16px; line-height: 1.5; color: #4b5563;">Akun pembelajaran EduVan kamu telah berhasil dibuat oleh Guru. Silakan gunakan informasi di bawah ini untuk masuk ke dalam aplikasi:</p>

                    <div style="background-color: #f8fafc; border-left: 4px solid #00A896; border-radius: 4px; padding: 15px; margin: 25px 0;">
                        <p style="margin: 5px 0; font-size: 16px;"><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                        <p style="margin: 5px 0; font-size: 16px;"><strong>Password:</strong> <span style="color: #003366; font-weight: bold; letter-spacing: 1px;">' . $password . '</span></p>
                    </div>

                    <p style="font-size: 14px; color: #ef4444; line-height: 1.5;"><em>Mohon jangan berikan password ini kepada siapapun demi keamanan akun kamu.</em></p>

                    <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 30px 0;">
                    <p style="font-size: 14px; color: #9ca3af; text-align: center; margin: 0; line-height: 1.5;">Semangat Belajar!<br><strong style="color: #4b5563;">Tim Guru Eduvan</strong></p>
                    <p style="font-size: 11px; color: #9ca3af; text-align: center; margin-top: 20px;">&copy; 2026 Eduvan. All rights reserved.</p>
                </div>
            </body>
            </html>
            ';

            $mail->send();
        }
        catch (Exception $e)
        {
        
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
                $user->avatar = 'assets/icon/avatar-neutral.png';
                $user->password = Hash::make(rand(11111111, 99999999));
                $user->role = 'student';
                $user->email_verified_at = now();
                $user->save();
                $user->refresh();
            }

            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;

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
