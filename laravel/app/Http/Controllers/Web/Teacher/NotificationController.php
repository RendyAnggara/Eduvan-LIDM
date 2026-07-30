<?php

namespace App\Http\Controllers\Web\Teacher; 

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UmumNotification;
use Google\Client as GoogleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        return view('teacher.notifications.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:150',
            'message' => 'required|string',
            'type'    => 'required|string|in:info,pengumuman,promo,alert,success'
        ]);

        try {
            $teacher = Auth::user();

            $students = User::where('role', 'student')
                ->where('school_id', $teacher->school_id)
                ->get();

            if ($students->isEmpty()) {
                return redirect()->back()->with('error', 'Gagal blast, tidak ada data siswa di sekolah Anda.');
            }

            Notification::send($students, new UmumNotification(
                $request->title,
                $request->message,
                $request->type
            ));

            $fcmTokens = $students->pluck('fcm_token')->filter()->values()->toArray();

            if (!empty($fcmTokens)) {
                $this->sendFcmNotification($fcmTokens, $request->title, $request->message, $request->type);
            }

            return redirect()->back()->with('success', 'Notifikasi berhasil dikirim ke seluruh siswa di sekolah Anda.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function sendFcmNotification(array $tokens, string $title, string $message, string $type)
    {
        $credentialsPath = storage_path('app/firebase-credentials.json');

        if (!file_exists($credentialsPath)) {
            return;
        }

        try {
            $client = new GoogleClient();
            $client->setAuthConfig($credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $accessTokenData = $client->fetchAccessTokenWithAssertion();
            $accessToken = $accessTokenData['access_token'] ?? null;

            if (!$accessToken) {
                return;
            }

            $credentialsData = json_decode(file_get_contents($credentialsPath), true);
            $projectId = $credentialsData['project_id'] ?? null;

            if (!$projectId) {
                return;
            }

            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            foreach ($tokens as $token) {
                Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/json',
                ])->post($url, [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body'  => $message,
                        ],
                        'data' => [
                            'type' => $type,
                        ],
                    ],
                ]);
            }
        } catch (\Exception $e) {
            return;
        }
    }
}
