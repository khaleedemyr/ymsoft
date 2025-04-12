<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class MaintenanceCommentController extends Controller
{
    public function getComments($taskId)
    {
        try {
            $comments = DB::table('maintenance_comments as mc')
                ->select('mc.*', 'u.nama_lengkap as user_name')
                ->leftJoin('users as u', 'mc.user_id', '=', 'u.id')
                ->where('mc.task_id', $taskId)
                ->orderBy('mc.created_at', 'desc')
                ->get();
                
            // Get attachments for each comment
            foreach ($comments as $comment) {
                $comment->attachments = DB::table('maintenance_comment_attachments')
                    ->where('comment_id', $comment->id)
                    ->select('id', 'file_name', 'file_path', 'file_type')
                    ->get();
            }
                
            return response()->json($comments);
        } catch (\Exception $e) {
            \Log::error('Error getting comments: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        return DB::transaction(function() use ($request) {
            try {
                // Debug lengkap
                \Log::info('=== DEBUG COMMENT REQUEST ===');
                \Log::info('Request Headers:', $request->headers->all());
                \Log::info('Request Method: ' . $request->method());
                \Log::info('Content Type: ' . $request->header('Content-Type'));
                \Log::info('Request All:', $request->all());
                \Log::info('Request Files:', [
                    'hasFile photos' => $request->hasFile('photos'),
                    'hasFile videos' => $request->hasFile('videos'),
                    'hasFile documents' => $request->hasFile('documents'),
                    'allFiles' => $request->allFiles()
                ]);
                
                $request->validate([
                    'task_id' => 'required|exists:maintenance_tasks,id',
                    'comment' => 'string'
                ]);
                
                // 1. Simpan komentar
                $commentId = DB::table('maintenance_comments')->insertGetId([
                    'task_id' => $request->task_id,
                    'user_id' => auth()->id(),
                    'comment' => $request->comment ?? 'Media comment',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                \Log::info('Comment created', ['comment_id' => $commentId]);
                
                // 2. Proses file dengan pendekatan baru
                $attachmentCount = 0;
                $attachmentData = [];
                
                // Handle photos array
                if ($request->hasFile('photos')) {
                    $photos = $request->file('photos');
                    \Log::info('Processing photos:', ['count' => count($photos)]);
                    
                    foreach ($photos as $index => $photo) {
                        $fileName = time() . '_photo_' . $index . '_' . $photo->getClientOriginalName();
                        $fileType = $photo->getClientMimeType();
                        $fileSize = $photo->getSize();
                        
                        \Log::info('Processing photo:', [
                            'name' => $fileName,
                            'type' => $fileType,
                            'size' => $fileSize
                        ]);
                        
                        // Simpan file ke storage
                        $filePath = $photo->storeAs('maintenance/comments', $fileName, 'public');
                        
                        // Verifikasi file tersimpan
                        if (Storage::disk('public')->exists($filePath)) {
                            \Log::info('File saved successfully to: ' . $filePath);
                            
                            // Tambahkan ke data attachment
                            $attachmentData[] = [
                                'comment_id' => $commentId,
                                'file_name' => $fileName,
                                'file_path' => $filePath,
                                'file_type' => $fileType,
                                'file_size' => $fileSize,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                            
                            $attachmentCount++;
                        } else {
                            \Log::error('Failed to save file: ' . $filePath);
                        }
                    }
                }
                
                // Handle videos array
                if ($request->hasFile('videos')) {
                    $videos = $request->file('videos');
                    \Log::info('Processing videos:', ['count' => count($videos)]);
                    
                    foreach ($videos as $index => $video) {
                        $fileName = time() . '_video_' . $index . '_' . $video->getClientOriginalName();
                        if (!strstr($fileName, '.')) {
                            $fileName .= '.webm'; // Tambahkan ekstensi jika tidak ada
                        }
                        
                        $fileType = $video->getClientMimeType();
                        $fileSize = $video->getSize();
                        
                        \Log::info('Processing video:', [
                            'name' => $fileName,
                            'type' => $fileType,
                            'size' => $fileSize
                        ]);
                        
                        // Simpan file ke storage
                        $filePath = $video->storeAs('maintenance/comments', $fileName, 'public');
                        
                        // Verifikasi file tersimpan
                        if (Storage::disk('public')->exists($filePath)) {
                            \Log::info('Video file saved successfully to: ' . $filePath);
                            
                            // Tambahkan ke data attachment
                            $attachmentData[] = [
                                'comment_id' => $commentId,
                                'file_name' => $fileName,
                                'file_path' => $filePath,
                                'file_type' => $fileType,
                                'file_size' => $fileSize,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                            
                            $attachmentCount++;
                        } else {
                            \Log::error('Failed to save video file: ' . $filePath);
                        }
                    }
                }
                
                // Handle documents array
                if ($request->hasFile('documents')) {
                    $documents = $request->file('documents');
                    \Log::info('Processing documents:', ['count' => count($documents)]);
                    
                    foreach ($documents as $index => $document) {
                        $fileName = time() . '_doc_' . $index . '_' . $document->getClientOriginalName();
                        $fileType = $document->getClientMimeType();
                        $fileSize = $document->getSize();
                        
                        \Log::info('Processing document:', [
                            'name' => $fileName,
                            'type' => $fileType,
                            'size' => $fileSize
                        ]);
                        
                        // Simpan file ke storage
                        $filePath = $document->storeAs('maintenance/comments', $fileName, 'public');
                        
                        // Verifikasi file tersimpan
                        if (Storage::disk('public')->exists($filePath)) {
                            \Log::info('Document file saved successfully to: ' . $filePath);
                            
                            // Tambahkan ke data attachment
                            $attachmentData[] = [
                                'comment_id' => $commentId,
                                'file_name' => $fileName,
                                'file_path' => $filePath,
                                'file_type' => $fileType,
                                'file_size' => $fileSize,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                            
                            $attachmentCount++;
                        } else {
                            \Log::error('Failed to save document file: ' . $filePath);
                        }
                    }
                }
                
                // Insert attachment data
                if (!empty($attachmentData)) {
                    DB::table('maintenance_comment_attachments')->insert($attachmentData);
                    \Log::info('Saved attachments:', ['count' => count($attachmentData)]);
                }
                
                // 3. Log activity
                DB::table('maintenance_activity_logs')->insert([
                    'task_id' => $request->task_id,
                    'user_id' => auth()->id(),
                    'activity_type' => 'COMMENT',
                    'description' => 'Added a comment' . ($attachmentCount > 0 ? " with {$attachmentCount} attachments" : ''),
                    'created_at' => now()
                ]);
                
                // Send notification
                NotificationService::sendTaskNotification(
                    $request->task_id,
                    'COMMENT_ADDED',
                    auth()->user()->nama_lengkap . ' menambahkan komentar: ' . (substr($request->comment, 0, 50) . (strlen($request->comment) > 50 ? '...' : '')),
                    $commentId
                );
                
                \Log::info('=== END DEBUG COMMENT REQUEST ===');
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Comment added successfully',
                    'comment_id' => $commentId,
                    'attachments' => $attachmentCount
                ]);
            } catch (\Exception $e) {
                \Log::error('=== ERROR COMMENT REQUEST ===');
                \Log::error('Error adding comment: ' . $e->getMessage());
                \Log::error('Error trace: ' . $e->getTraceAsString());
                \Log::error('=== END ERROR COMMENT REQUEST ===');
                
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
        });
    }
    
    // Helper method untuk menyimpan file komentar
    private function saveCommentFile($file, $commentId)
    {
        try {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fileType = $file->getClientMimeType();
            $fileSize = $file->getSize();
            
            // Simpan file ke storage
            $filePath = $file->storeAs('maintenance/comments', $fileName, 'public');
            
            // Buat data untuk database
            return [
                'comment_id' => $commentId,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'created_at' => now(),
                'updated_at' => now()
            ];
        } catch (\Exception $e) {
            \Log::error('Error saving comment file: ' . $e->getMessage());
            return null;
        }
    }
    
    // Helper method untuk menyimpan file base64
    private function saveBase64File($base64Data, $commentId)
    {
        try {
            // Extract tipe file dan data
            list($type, $data) = explode(';', $base64Data);
            list(, $data) = explode(',', $data);
            $type = str_replace('data:', '', $type);
            
            // Generate nama file
            $extension = $this->getExtensionFromMime($type);
            $fileName = time() . '_' . uniqid() . '.' . $extension;
            $fileData = base64_decode($data);
            $fileSize = strlen($fileData);
            
            // Simpan file ke storage
            $storage = \Storage::disk('public');
            $filePath = 'maintenance/comments/' . $fileName;
            $storage->put($filePath, $fileData);
            
            // Buat data untuk database
            return [
                'comment_id' => $commentId,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_type' => $type,
                'file_size' => $fileSize,
                'created_at' => now(),
                'updated_at' => now()
            ];
        } catch (\Exception $e) {
            \Log::error('Error saving base64 file: ' . $e->getMessage());
            return null;
        }
    }
    
    // Helper untuk mendapatkan ekstensi dari mime type
    private function getExtensionFromMime($mimeType)
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx'
        ];
        
        return $map[$mimeType] ?? 'file';
    }

    /**
     * Delete a comment and its attachments
     * 
     * @param int $commentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($commentId)
    {
        return DB::transaction(function() use ($commentId) {
            try {
                \Log::info('=== DELETING COMMENT ===');
                \Log::info('Comment ID: ' . $commentId);
                \Log::info('User ID: ' . auth()->id());
                
                // Cek apakah komentar ada dan dimiliki oleh user yang login
                $comment = DB::table('maintenance_comments')
                    ->where('id', $commentId)
                    ->first();
                    
                if (!$comment) {
                    \Log::error('Comment not found: ' . $commentId);
                    return response()->json(['error' => 'Komentar tidak ditemukan'], 404);
                }
                
                \Log::info('Comment found:', (array)$comment);
                \Log::info('Comment user_id: ' . $comment->user_id);
                \Log::info('Auth user_id: ' . auth()->id());
                
                // Hanya boleh dihapus oleh pemilik komentar atau admin
                if ($comment->user_id != auth()->id() && !in_array(auth()->user()->id_role, ['5af56935b011a'])) {
                    \Log::error('Unauthorized delete attempt');
                    return response()->json(['error' => 'Tidak diizinkan menghapus komentar orang lain'], 403);
                }
                
                // Ambil semua attachment komentar
                $attachments = DB::table('maintenance_comment_attachments')
                    ->where('comment_id', $commentId)
                    ->get(['id', 'file_path']);
                    
                \Log::info('Found ' . count($attachments) . ' attachments to delete');
                    
                // Hapus file fisik di storage
                foreach ($attachments as $attachment) {
                    if (Storage::disk('public')->exists($attachment->file_path)) {
                        Storage::disk('public')->delete($attachment->file_path);
                        \Log::info('Deleted file: ' . $attachment->file_path);
                    } else {
                        \Log::warning('File not found in storage: ' . $attachment->file_path);
                    }
                }
                
                // Hapus data attachment dari database
                $deletedAttachmentsCount = DB::table('maintenance_comment_attachments')
                    ->where('comment_id', $commentId)
                    ->delete();
                    
                \Log::info('Deleted ' . $deletedAttachmentsCount . ' attachment records from database');
                    
                // Log aktivitas penghapusan komentar
                DB::table('maintenance_activity_logs')->insert([
                    'task_id' => $comment->task_id,
                    'user_id' => auth()->id(),
                    'activity_type' => 'COMMENT_DELETED',
                    'description' => 'Deleted a comment',
                    'created_at' => now()
                ]);
                
                // Send notification
                NotificationService::sendTaskNotification(
                    $comment->task_id,
                    'COMMENT_DELETED',
                    auth()->user()->nama_lengkap . ' menghapus komentar'
                );
                
                \Log::info('Activity log recorded');
                
                // Hapus komentar
                $deletedCommentCount = DB::table('maintenance_comments')
                    ->where('id', $commentId)
                    ->delete();
                    
                \Log::info('Deleted comment record from database: ' . $deletedCommentCount);
                \Log::info('=== END DELETING COMMENT ===');
                    
                return response()->json([
                    'success' => true,
                    'message' => 'Komentar berhasil dihapus'
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Error deleting comment: ' . $e->getMessage());
                \Log::error('Error trace: ' . $e->getTraceAsString());
                return response()->json(['error' => $e->getMessage()], 500);
            }
        });
    }
}
