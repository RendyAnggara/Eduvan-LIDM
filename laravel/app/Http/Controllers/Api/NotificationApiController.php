<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    public function getNotifUser(Request $request)
    {
        try {
            $user = $request->user();

            $unreadCount = $user->unreadNotifications()->count();

            $notifications = $user->notifications()->latest()->get()->map(function ($notif) {
                return [
                    'id'         => $notif->id,
                    'title'      => $notif->data['title'] ?? 'Pemberitahuan',
                    'message'    => $notif->data['message'] ?? '',
                    'type'       => $notif->data['type'] ?? 'info',
                    'is_read'    => !is_null($notif->read_at),
                    'read_at'    => $notif->read_at ? $notif->read_at->timezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB' : null,
                    'created_at' => $notif->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB',
                ];
            });

            return response()->json([
                'status'       => 'success',
                'unread_count' => $unreadCount,
                'data'         => $notifications
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAsRead(Request $request, $id)
    {
        try {
            $notification = $request->user()->notifications()->where('id', $id)->first();

            if ($notification) {
                $notification->markAsRead();
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Notifikasi berhasil dibaca'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengupdate status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAllAsRead(Request $request)
    {
        try {
            $request->user()->unreadNotifications->markAsRead();

            return response()->json([
                'status'  => 'success',
                'message' => 'Semua notifikasi berhasil ditandai telah dibaca'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengupdate status: ' . $e->getMessage()
            ], 500);
        }
    }
}
