<?php

namespace App\Services;

use App\Models\MaintenanceTask;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Mengirim notifikasi ke semua user yang terkait dengan task
     */
    public static function sendTaskNotification($taskId, $type, $message)
    {
        try {
            // Dapatkan task
            $task = DB::table('maintenance_tasks')->where('id', $taskId)->first();
            if (!$task) {
                Log::error("Task dengan ID {$taskId} tidak ditemukan");
                return false;
            }
            
            // Kumpulkan semua user yang terkait dengan task
            $userIds = [];
            
            // 1. Pembuat task
            if ($task->created_by) {
                $userIds[] = $task->created_by;
            }
            
            // 2. Member yang di-assign ke task
            $memberIds = DB::table('maintenance_members')
                ->where('task_id', $taskId)
                ->pluck('user_id')
                ->toArray();
            
            $userIds = array_merge($userIds, $memberIds);
            
            // 3. User yang pernah comment di task
            $commentatorIds = DB::table('maintenance_comments')
                ->where('task_id', $taskId)
                ->pluck('user_id')
                ->toArray();
                
            $userIds = array_merge($userIds, $commentatorIds);
            
            // Hapus duplikasi dan user yang sedang login (pengirim notifikasi)
            $userIds = array_unique($userIds);
            $currentUserId = auth()->id();
            $userIds = array_filter($userIds, function($userId) use ($currentUserId) {
                return $userId != $currentUserId;
            });
            
            if (empty($userIds)) {
                Log::info("Tidak ada user yang akan menerima notifikasi");
                return false;
            }
            
            // Buat URL untuk link ke task
            $url = "/maintenance/tasks/{$taskId}";
            
            // Buat notifikasi untuk semua user terkait
            $notifications = [];
            foreach ($userIds as $userId) {
                $notifications[] = [
                    'user_id' => $userId,
                    'task_id' => $taskId,
                    'type' => $type,
                    'message' => $message,
                    'url' => $url,
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            // Simpan semua notifikasi
            DB::table('notifications')->insert($notifications);
            
            Log::info("Mengirim notifikasi ke " . count($userIds) . " user untuk task #{$taskId}");
            return true;
        } catch (\Exception $e) {
            Log::error("Error saat mengirim notifikasi: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Menandai notifikasi sebagai dibaca
     */
    public static function markAsRead($notificationId)
    {
        try {
            DB::table('notifications')
                ->where('id', $notificationId)
                ->update(['is_read' => true]);
                
            return true;
        } catch (\Exception $e) {
            Log::error("Error saat menandai notifikasi dibaca: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Menandai semua notifikasi user sebagai dibaca
     */
    public static function markAllAsRead($userId)
    {
        try {
            DB::table('notifications')
                ->where('user_id', $userId)
                ->update(['is_read' => true]);
                
            return true;
        } catch (\Exception $e) {
            Log::error("Error saat menandai semua notifikasi dibaca: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengirim notifikasi ke pengguna tertentu berdasarkan job_id
     */
    public static function sendNotificationToSpecificJobs($taskId, $type, $message, $jobIds = [])
    {
        try {
            // Dapatkan task
            $task = DB::table('maintenance_tasks')->where('id', $taskId)->first();
            if (!$task) {
                Log::error("Task dengan ID {$taskId} tidak ditemukan");
                return false;
            }
            
            // Jika tidak ada job_ids yang diberikan, return false
            if (empty($jobIds)) {
                Log::info("Tidak ada job_ids yang diberikan untuk notifikasi");
                return false;
            }
            
            // Cari semua user dengan job_id yang telah ditentukan dan status A
            $users = DB::table('users')
                ->whereIn('id_jabatan', $jobIds)
                ->where('status', 'A')
                ->get();
                
            if ($users->isEmpty()) {
                Log::info("Tidak ada user aktif dengan job_id yang ditentukan: " . implode(',', $jobIds));
                return false;
            }
            
            // Buat URL untuk link ke task
            $url = "/maintenance/tasks/{$taskId}";
            
            // Gunakan pesan yang diberikan langsung
            $notifications = [];
            foreach ($users as $user) {
                $notifications[] = [
                    'user_id' => $user->id,
                    'task_id' => $taskId,
                    'type' => $type,
                    'message' => $message,
                    'url' => $url,
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            // Simpan semua notifikasi
            DB::table('notifications')->insert($notifications);
            
            $userIds = $users->pluck('id')->toArray();
            Log::info("Mengirim notifikasi ke pengguna dengan job_id tertentu: " . implode(',', $jobIds) . 
                     ", user_ids: " . implode(',', $userIds) . 
                     " untuk task #{$taskId}");
            return true;
        } catch (\Exception $e) {
            Log::error("Error saat mengirim notifikasi ke job_id tertentu: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mengirim notifikasi ke semua user terkait task DAN pengguna dengan jabatan tertentu sekaligus
     * Metode ini menggabungkan fungsionalitas sendTaskNotification dan sendNotificationToSpecificJobs
     */
    public static function sendTaskAndJobNotification($taskId, $type, $message, $jobIds = [])
    {
        try {
            // Dapatkan task
            $task = DB::table('maintenance_tasks')->where('id', $taskId)->first();
            if (!$task) {
                Log::error("Task dengan ID {$taskId} tidak ditemukan");
                return false;
            }
            
            // Kumpulkan semua user
            $allUserIds = [];
            
            // 1. Kumpulkan user yang terkait dengan task
            // 1a. Pembuat task
            if ($task->created_by) {
                $allUserIds[] = $task->created_by;
            }
            
            // 1b. Member yang di-assign ke task
            $memberIds = DB::table('maintenance_members')
                ->where('task_id', $taskId)
                ->pluck('user_id')
                ->toArray();
            
            $allUserIds = array_merge($allUserIds, $memberIds);
            
            // 1c. User yang pernah comment di task
            $commentatorIds = DB::table('maintenance_comments')
                ->where('task_id', $taskId)
                ->pluck('user_id')
                ->toArray();
                
            $allUserIds = array_merge($allUserIds, $commentatorIds);
            
            // 2. Kumpulkan user berdasarkan jabatan (job_ids)
            if (!empty($jobIds)) {
                $usersByJobs = DB::table('users')
                    ->whereIn('id_jabatan', $jobIds)
                    ->where('status', 'A')
                    ->pluck('id')
                    ->toArray();
                
                $allUserIds = array_merge($allUserIds, $usersByJobs);
            }
            
            // Hapus duplikasi dan user yang sedang login (pengirim notifikasi)
            $allUserIds = array_unique($allUserIds);
            $currentUserId = auth()->id();
            $allUserIds = array_filter($allUserIds, function($userId) use ($currentUserId) {
                return $userId != $currentUserId;
            });
            
            if (empty($allUserIds)) {
                Log::info("Tidak ada user yang akan menerima notifikasi");
                return false;
            }
            
            // Buat URL untuk link ke task
            $url = "/maintenance/tasks/{$taskId}";
            
            // Buat notifikasi untuk semua user terkait
            $notifications = [];
            foreach ($allUserIds as $userId) {
                $notifications[] = [
                    'user_id' => $userId,
                    'task_id' => $taskId,
                    'type' => $type,
                    'message' => $message,
                    'url' => $url,
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            // Simpan semua notifikasi dalam sekali jalan
            DB::table('notifications')->insert($notifications);
            
            Log::info("Mengirim notifikasi ke " . count($allUserIds) . " user (task dan jabatan tertentu) untuk task #{$taskId}");
            return true;
        } catch (\Exception $e) {
            Log::error("Error saat mengirim notifikasi ke task dan jabatan tertentu: " . $e->getMessage());
            return false;
        }
    }
} 