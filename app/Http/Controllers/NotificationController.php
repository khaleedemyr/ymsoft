<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Mendapatkan notifikasi untuk user yang sedang login
     */
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();
            
        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
            
        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }
    
    /**
     * Menandai satu notifikasi sebagai dibaca
     */
    public function markAsRead($notificationId)
    {
        // Validasi notifikasi milik user yang sedang login
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', auth()->id())
            ->first();
            
        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
        }
        
        $notification->is_read = true;
        $notification->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil ditandai dibaca'
        ]);
    }
    
    /**
     * Menandai semua notifikasi sebagai dibaca
     */
    public function markAllAsRead()
    {
        NotificationService::markAllAsRead(auth()->id());
        
        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi berhasil ditandai dibaca'
        ]);
    }
    
    /**
     * Menghapus notifikasi yang dipilih
     */
    public function delete(Request $request)
    {
        $notificationIds = $request->notification_ids;
        
        // Validasi notifikasi milik user yang sedang login
        $count = Notification::whereIn('id', $notificationIds)
            ->where('user_id', auth()->id())
            ->delete();
            
        return response()->json([
            'success' => true,
            'message' => $count . ' notifikasi berhasil dihapus'
        ]);
    }
    
    /**
     * Cek notifikasi baru sejak ID tertentu
     */
    public function checkNew(Request $request)
    {
        $lastId = $request->input('last_id', 0);
        
        $newNotifications = Notification::where('user_id', auth()->id())
            ->where('id', '>', $lastId)
            ->orderBy('id', 'desc')
            ->get();
            
        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
            
        return response()->json([
            'success' => true,
            'new_notifications' => $newNotifications,
            'unread_count' => $unreadCount
        ]);
    }
    
    /**
     * Dapatkan ID notifikasi terakhir
     */
    public function getLastId()
    {
        $lastNotification = Notification::where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->first();
            
        return response()->json([
            'success' => true,
            'last_id' => $lastNotification ? $lastNotification->id : 0
        ]);
    }
} 