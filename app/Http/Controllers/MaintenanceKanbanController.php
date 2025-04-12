<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\MaintenanceTask;
use App\Models\MaintenanceMember;
use App\Models\MaintenanceMedia;
use App\Models\MaintenanceDocument;
use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\MaintenanceTaskItem;
use App\Models\MaintenancePurchaseRequisitionItem;
use App\Models\MaintenancePurchaseRequisition;
use App\Models\MaintenancePurchaseOrder;
use App\Models\Supplier;
use App\Models\PurchaseRequisition;

class MaintenanceKanbanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $canSelectOutlet = false;
        $outlets = collect();
        $selectedOutlet = null;

        // Cek apakah user bisa memilih outlet
        if ($user->id_role === '5af56935b011a' || $user->id_outlet == 1) {
            $canSelectOutlet = true;
            $outlets = DB::table('tbl_data_outlet')
                ->select('id_outlet', 'nama_outlet')
                ->orderBy('nama_outlet')
                ->get();
        } else {
            // User hanya bisa lihat outletnya sendiri
            $selectedOutlet = DB::table('tbl_data_outlet')
                ->select('id_outlet', 'nama_outlet')
                ->where('id_outlet', $user->id_outlet)
                ->first();
        }

        return view('maintenance.kanban.index', compact('outlets', 'canSelectOutlet', 'selectedOutlet'));
    }

    public function getRuko($outletId)
    {
        if ($outletId != 1) {
            return response()->json([]);
        }

        $rukos = DB::table('tbl_data_ruko')
            ->select('id_ruko', 'nama_ruko')
            ->where('id_outlet', $outletId)
            ->orderBy('nama_ruko')
            ->get();

        return response()->json($rukos);
    }

    public function getMembers()
    {
        $members = DB::table('users')
            ->select('id', 'nama_lengkap')
            ->where('division_id', 20)
            ->where('status', 'A')
            ->orderBy('nama_lengkap')
            ->get();

        return response()->json($members);
    }

    public function getTasks(Request $request)
    {
        try {
            $outletId = $request->input('outlet_id');
            $rukoId = $request->input('ruko_id');

            $query = DB::table('maintenance_tasks as mt')
                ->select(
                    'mt.*',
                    'mp.priority as priority_name',
                    'ml.name as label_name',
                    'ml.color as label_color',
                    'u.nama_lengkap as created_by_name'
                )
                ->leftJoin('maintenance_priorities as mp', 'mt.priority_id', '=', 'mp.id')
                ->leftJoin('maintenance_labels as ml', 'mt.label_id', '=', 'ml.id')
                ->leftJoin('users as u', 'mt.created_by', '=', 'u.id')
                ->where('mt.id_outlet', $outletId);

            if ($rukoId) {
                $query->where('mt.id_ruko', $rukoId);
            }

            $tasks = $query->get();

            // Tambahkan kolom completed_at jika tidak ada
            $tasks->each(function($task) {
                // Jika completed_at null untuk task DONE, set nilai default
                if ($task->status === 'DONE' && !$task->completed_at) {
                    $task->completed_at = $task->updated_at;
                }
            });

            // Ambil media, dokumen dan member untuk setiap task
            foreach ($tasks as $task) {
                // Ambil foto
                $task->photos = DB::table('maintenance_media')
                    ->where('task_id', $task->id)
                    ->where('file_type', 'like', 'image/%') // Hanya ambil file image
                    ->select('id', 'file_name', 'file_path', 'file_type')
                    ->get();
                    
                // Ambil video
                $task->videos = DB::table('maintenance_media')
                    ->where('task_id', $task->id)
                    ->where('file_type', 'like', 'video/%') // Hanya ambil file video
                    ->select('id', 'file_name', 'file_path', 'file_type')
                    ->get();
                
                // Ambil dokumen
                $task->documents = DB::table('maintenance_documents')
                    ->where('task_id', $task->id)
                    ->select('id', 'file_name', 'file_path', 'file_type')
                    ->get();

                // Ambil members
                $task->members = DB::table('maintenance_members as mm')
                    ->join('users as u', 'mm.user_id', '=', 'u.id')
                    ->where('mm.task_id', $task->id)
                    ->select('u.id', 'u.nama_lengkap as name')
                    ->get();

                // Hitung jumlah komentar
                $task->comment_count = DB::table('maintenance_comments')
                    ->where('task_id', $task->id)
                    ->count();
            }

            // Output debugging log untuk task dengan status DONE
            foreach ($tasks as $task) {
                if ($task->status === 'DONE') {
                    \Log::info('DONE task details:', [
                        'task_id' => $task->id,
                        'task_title' => $task->title,
                        'completed_at' => $task->completed_at ?? 'NOT SET'
                    ]);
                }
            }

            return response()->json($tasks);
        } catch (\Exception $e) {
            \Log::error('Error getting tasks: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        return DB::transaction(function() use ($request) {
            try {
                \Log::info('=== START DEBUG STORE ===');
                \Log::info('Request data:', $request->all());
                
                // 1. Proses data task seperti biasa
                $priorityId = DB::table('maintenance_priorities')
                    ->where('priority', $request->priority_id)
                    ->value('id');
                    
                $labelId = DB::table('maintenance_labels')
                    ->where('name', $request->label_id)
                    ->value('id');
                
                $taskData = [
                    'task_number' => $request->task_number,
                    'title' => $request->title,
                    'description' => $request->description,
                    'priority_id' => $priorityId,
                    'label_id' => $labelId,
                    'status' => 'TASK',
                    'id_outlet' => $request->id_outlet,
                    'id_ruko' => $request->id_ruko,
                    'due_date' => $request->due_date,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $taskId = DB::table('maintenance_tasks')->insertGetId($taskData);
                \Log::info('Created task with ID: ' . $taskId);
                
                // 2. Simpan members
                // Inisialisasi array untuk menyimpan data member
                $memberData = [];
                
                // Tambahkan creator sebagai member default
                $memberData[] = [
                    'task_id' => $taskId,
                    'user_id' => auth()->id(),
                    'role' => 'ASSIGNEE',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                // Mendapatkan semua ID member yang dipilih
                if ($request->has('member_ids')) {
                    $memberIds = is_array($request->member_ids) ? 
                                $request->member_ids : 
                                explode(',', $request->member_ids);
                    
                    \Log::info('Processing member_ids:', ['member_ids' => $memberIds]);
                    
                    foreach ($memberIds as $memberId) {
                        if (is_numeric($memberId) && intval($memberId) > 0 && intval($memberId) != auth()->id()) {
                            // Verifikasi bahwa user ID benar-benar ada dalam database
                            $userExists = DB::table('users')->where('id', intval($memberId))->exists();
                            
                            if ($userExists) {
                                $memberData[] = [
                                    'task_id' => $taskId,
                                    'user_id' => intval($memberId),
                                    'role' => 'ASSIGNEE',
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ];
                                
                                \Log::info('Added member:', ['user_id' => intval($memberId)]);
                            } else {
                                \Log::warning('User not found:', ['user_id' => intval($memberId)]);
                            }
                        }
                    }
                }
                
                // Insert data member ke database
                if (!empty($memberData)) {
                    // Hapus semua data yang lama untuk menghindari duplikasi
                    DB::table('maintenance_members')->where('task_id', $taskId)->delete();
                    
                    // Insert data member baru
                    DB::table('maintenance_members')->insert($memberData);
                    \Log::info('Inserted members:', ['count' => count($memberData), 'data' => $memberData]);
                }
                
                // 3. Proses media dan documents dari file yang diupload
                // Array untuk menyimpan data media dan dokumen
                $mediaData = [];
                $docData = [];
                
                // Proses foto dari request (photos[])
                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $photo) {
                        $mediaData[] = $this->saveUploadedFile($photo, $taskId);
                    }
                    \Log::info('Processed photos:', ['count' => count($request->file('photos'))]);
                }
                
                // Proses video dari request (videos[])
                if ($request->hasFile('videos')) {
                    foreach ($request->file('videos') as $video) {
                        $mediaData[] = $this->saveUploadedFile($video, $taskId);
                    }
                    \Log::info('Processed videos:', ['count' => count($request->file('videos'))]);
                }
                
                // Proses media upload (media[])
                if ($request->hasFile('media')) {
                    foreach ($request->file('media') as $media) {
                        $mediaData[] = $this->saveUploadedFile($media, $taskId);
                    }
                    \Log::info('Processed media:', ['count' => count($request->file('media'))]);
                }
                
                // Proses base64 captures
                foreach ($request->all() as $key => $value) {
                    if (strpos($key, 'capture') === 0 && !empty($value) && is_string($value) && strpos($value, 'data:') === 0) {
                        $mediaData[] = $this->saveBase64File($value, $taskId);
                    }
                }
                
                // Proses dokumen
                if ($request->hasFile('documents')) {
                    foreach ($request->file('documents') as $document) {
                        $docData[] = $this->saveUploadedFile($document, $taskId);
                    }
                    \Log::info('Processed documents:', ['count' => count($request->file('documents'))]);
                }
                
                // Filter out null values dari media dan dokumen
                $mediaData = array_filter($mediaData);
                $docData = array_filter($docData);
                
                // Insert data ke database
                if (!empty($mediaData)) {
                    DB::table('maintenance_media')->insert($mediaData);
                    \Log::info('Inserted media records:', ['count' => count($mediaData)]);
                }
                
                if (!empty($docData)) {
                    DB::table('maintenance_documents')->insert($docData);
                    \Log::info('Inserted document records:', ['count' => count($docData)]);
                }
                
                // 4. Insert ke activity logs
                DB::table('maintenance_activity_logs')->insert([
                    'task_id' => $taskId,
                    'user_id' => auth()->id(),
                    'activity_type' => 'CREATED',
                    'description' => 'Task created',
                    'created_at' => now()
                ]);
                
                // Setelah insert ke activity logs
                // Ambil nama outlet dari tbl_data_outlet
                $outlet = DB::table('tbl_data_outlet')
                    ->where('id_outlet', $request->id_outlet)
                    ->first();
                
                \Log::info('Outlet Data:', [
                    'id_outlet' => $request->id_outlet,
                    'outlet' => $outlet
                ]);
                
                $outletName = $outlet ? $outlet->nama_outlet : 'Unknown Outlet';
                $notificationMessage = 'Task baru #' . $request->task_number . ' dibuat: ' . $request->title . ' untuk outlet ' . $outletName;

                \Log::info('Notification Message:', [
                    'message' => $notificationMessage
                ]);

                NotificationService::sendTaskNotification(
                    $taskId,
                    'TASK_CREATED',
                    $notificationMessage
                );
                
                // Kirim notifikasi ke user dengan job ID tertentu (165, 209, 263)
                NotificationService::sendNotificationToSpecificJobs(
                    $taskId,
                    'TASK_CREATED',
                    $notificationMessage,
                    [165, 209, 263]  // Job IDs yang ditentukan
                );
                
                // Notifikasi ke member task
                $task = DB::table('maintenance_tasks')->where('id', $taskId)->first();

                // Member task
                $taskMembers = DB::table('maintenance_members')
                    ->where('task_id', $taskId)
                    ->where('user_id', '!=', auth()->id())
                    ->get();

                foreach ($taskMembers as $member) {
                    DB::table('notifications')->insert([
                        'user_id' => $member->user_id,
                        'task_id' => $taskId,
                        'message' => 'Task baru ' . $task->task_number . ' telah dibuat dan Anda ditugaskan sebagai member',
                        'url' => '/maintenance/kanban/task/' . $taskId,
                        'type' => 'info',
                        'is_read' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                
                \Log::info('=== END DEBUG STORE ===');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Task berhasil dibuat',
                    'data' => [
                        'id' => $taskId,
                        'task_number' => $request->task_number
                    ]
                ]);
                
            } catch (\Exception $e) {
                \Log::error('=== ERROR DEBUG ===');
                \Log::error('Error Message: ' . $e->getMessage());
                \Log::error('Error Code: ' . $e->getCode());
                \Log::error('Error File: ' . $e->getFile() . ':' . $e->getLine());
                \Log::error('Error Trace: ' . $e->getTraceAsString());
                
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        });
    }

    // Helper method untuk menyimpan file yang diupload
    private function saveUploadedFile($file, $taskId)
    {
        try {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fileType = $file->getClientMimeType();
            $fileSize = $file->getSize();
            
            // Tentukan apakah file ini media atau dokumen
            $isMedia = $this->isMediaFile($fileType);
            $folderPath = $isMedia ? 'maintenance/media' : 'maintenance/documents';
            
            // Simpan file ke storage
            $filePath = $file->storeAs($folderPath, $fileName, 'public');
            
            // Buat data untuk database
            if ($isMedia) {
                return [
                    'task_id' => $taskId,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'file_size' => $fileSize,
                    'uploaded_by' => auth()->id(),
                    'created_at' => now()
                ];
            } else {
                return [
                    'task_id' => $taskId,
                    'document_type' => 'OTHER', // Default document type
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'file_size' => $fileSize,
                    'uploaded_by' => auth()->id(),
                    'created_at' => now()
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Error saving uploaded file: ' . $e->getMessage());
            return null;
        }
    }

    // Helper method untuk menyimpan file dari base64
    private function saveBase64File($base64Data, $taskId)
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
            
            // Tentukan jenis file
            $isMedia = $this->isMediaFile($type);
            $folderPath = $isMedia ? 'maintenance/media/' : 'maintenance/documents/';
            
            // Simpan file ke storage
            $storage = \Storage::disk('public');
            $filePath = $folderPath . $fileName;
            $storage->put($filePath, $fileData);
            
            // Buat data untuk database
            if ($isMedia) {
                return [
                    'task_id' => $taskId,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => $type,
                    'file_size' => $fileSize,
                    'uploaded_by' => auth()->id(),
                    'created_at' => now()
                ];
            } else {
                return [
                    'task_id' => $taskId,
                    'document_type' => 'OTHER', // Default document type
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => $type,
                    'file_size' => $fileSize,
                    'uploaded_by' => auth()->id(),
                    'created_at' => now()
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Error saving base64 file: ' . $e->getMessage());
            return null;
        }
    }

    // Helper untuk menentukan apakah file adalah media (gambar/video)
    private function isMediaFile($mimeType)
    {
        return strpos($mimeType, 'image/') === 0 || 
               strpos($mimeType, 'video/') === 0 || 
               in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm']);
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

    public function updateTaskStatus(Request $request)
    {
        try {
            $taskId = $request->input('taskId');
            $newStatus = $request->input('status');
            
            // Validasi input
            if (!$taskId || !$newStatus) {
                return response()->json(['error' => 'Task ID dan status baru harus diisi'], 400);
            }
            
            // Validasi status
            $validStatuses = MaintenanceTask::getValidStatuses();
            if (!in_array($newStatus, $validStatuses)) {
                return response()->json(['error' => 'Status tidak valid'], 400);
            }
            
            // Ambil data task sebelum update untuk memeriksa perubahan status
            $task = DB::table('maintenance_tasks')->where('id', $taskId)->first();
            $oldStatus = $task ? $task->status : null;

            // Validasi perpindahan dari PO ke IN_PROGRESS
            if ($oldStatus === 'PO' && $newStatus === 'IN_PROGRESS') {
                // Cek apakah user memiliki akses (division_id = 20 atau superadmin)
                $user = auth()->user();
                $hasAccess = $user->division_id == 20 || $user->id_role === '5af56935b011a';

                Log::info('Validasi akses user untuk memindahkan task ke IN_PROGRESS', [
                    'user_id' => $user->id,
                    'division_id' => $user->division_id,
                    'id_role' => $user->id_role,
                    'has_access' => $hasAccess
                ]);

                if (!$hasAccess) {
                    Log::warning('User tidak memiliki akses untuk memindahkan task ke IN_PROGRESS', [
                        'user_id' => $user->id,
                        'division_id' => $user->division_id,
                        'id_role' => $user->id_role
                    ]);
                    return response()->json([
                        'error' => 'Anda tidak memiliki akses untuk memindahkan task ini ke IN_PROGRESS. Hanya Maintenance Division (division_id = 20) atau Superadmin yang dapat melakukan ini.'
                    ], 403);
                }

                // Ambil statistik PO dengan status yang lebih detail
                $poStats = DB::table('maintenance_purchase_orders')
                    ->where('task_id', $taskId)
                    ->select(
                        DB::raw('COUNT(*) as total_po'),
                        DB::raw('SUM(CASE WHEN status = "RECEIVED" THEN 1 ELSE 0 END) as received_po'),
                        DB::raw('SUM(CASE WHEN status = "REJECTED" THEN 1 ELSE 0 END) as rejected_po'),
                        DB::raw('SUM(CASE WHEN status IN ("DRAFT", "PENDING") THEN 1 ELSE 0 END) as pending_po'),
                        DB::raw('SUM(CASE WHEN status = "APPROVED" THEN 1 ELSE 0 END) as approved_po')
                    )
                    ->first();

                // Hitung total PO yang sudah selesai (RECEIVED + REJECTED)
                $completedPo = ($poStats->received_po ?? 0) + ($poStats->rejected_po ?? 0);
                $totalPo = $poStats->total_po ?? 0;
                $pendingPo = $poStats->pending_po ?? 0;
                $approvedPo = $poStats->approved_po ?? 0;

                // Log untuk debugging
                Log::info('Statistik PO untuk task ' . $taskId, [
                    'total_po' => $totalPo,
                    'received_po' => $poStats->received_po,
                    'rejected_po' => $poStats->rejected_po,
                    'pending_po' => $pendingPo,
                    'approved_po' => $approvedPo,
                    'completed_po' => $completedPo
                ]);

                // Validasi apakah semua PO sudah selesai
                if ($totalPo === 0) {
                    Log::warning('Tidak ada PO yang dibuat untuk task', [
                        'task_id' => $taskId
                    ]);
                    return response()->json([
                        'error' => 'Task tidak dapat dipindahkan ke IN_PROGRESS karena belum ada PO yang dibuat. Silakan buat PO terlebih dahulu.'
                    ], 400);
                }

                if ($pendingPo > 0) {
                    Log::warning('Masih ada PO yang belum diapprove', [
                        'task_id' => $taskId,
                        'pending_po' => $pendingPo
                    ]);
                    return response()->json([
                        'error' => 'Task tidak dapat dipindahkan ke IN_PROGRESS karena masih ada ' . $pendingPo . ' PO yang belum diapprove (DRAFT/PENDING). Silakan tunggu hingga semua PO diapprove.'
                    ], 400);
                }

                if ($approvedPo > 0) {
                    Log::warning('Masih ada PO yang sudah diapprove tapi belum RECEIVED/REJECTED', [
                        'task_id' => $taskId,
                        'approved_po' => $approvedPo
                    ]);
                    return response()->json([
                        'error' => 'Task tidak dapat dipindahkan ke IN_PROGRESS karena masih ada ' . $approvedPo . ' PO yang sudah diapprove tapi belum RECEIVED/REJECTED. Silakan tunggu hingga semua PO selesai.'
                    ], 400);
                }

                if ($completedPo < $totalPo) {
                    Log::warning('Masih ada PO yang belum selesai', [
                        'task_id' => $taskId,
                        'completed_po' => $completedPo,
                        'total_po' => $totalPo
                    ]);
                    return response()->json([
                        'error' => 'Task tidak dapat dipindahkan ke IN_PROGRESS karena masih ada ' . ($totalPo - $completedPo) . ' PO yang belum selesai (RECEIVED/REJECTED). Silakan tunggu hingga semua PO selesai.'
                    ], 400);
                }

                Log::info('Validasi PO berhasil, task dapat dipindahkan ke IN_PROGRESS', [
                    'task_id' => $taskId,
                    'completed_po' => $completedPo,
                    'total_po' => $totalPo
                ]);
            }
            
            // Validasi perpindahan ke DONE
            if ($newStatus === 'DONE') {
                // Cek apakah user memiliki akses (division_id = 20 atau superadmin)
                $user = auth()->user();
                $hasAccess = $user->status === 'A' && ($user->division_id == 20 || $user->id_role === '5af56935b011a');

                if (!$hasAccess) {
                    Log::warning('User tidak memiliki akses untuk memindahkan task ke DONE', [
                        'user_id' => $user->id,
                        'division_id' => $user->division_id,
                        'id_role' => $user->id_role,
                        'status' => $user->status
                    ]);
                    return response()->json([
                        'error' => 'Anda tidak memiliki akses untuk memindahkan task ini ke DONE. Hanya Maintenance Division (division_id = 20) atau Superadmin yang dapat melakukan ini.'
                    ], 403);
                }

                // Cek apakah task memiliki evidence - PERBAIKI NAMA TABEL DISINI
                $evidence = DB::table('maintenance_evidence') // Ubah dari 'maintenance_evidences' ke 'maintenance_evidence'
                    ->where('task_id', $taskId)
                    ->count();

                if ($evidence == 0) {
                    Log::warning('Task tidak memiliki evidence untuk dipindahkan ke DONE', [
                        'task_id' => $taskId
                    ]);
                    return response()->json([
                        'error' => 'Task tidak dapat dipindahkan ke DONE karena belum memiliki evidence. Silakan tambahkan evidence terlebih dahulu.'
                    ], 400);
                }

                // Jika semuanya valid, tambahkan completed_at timestamp
                DB::table('maintenance_tasks')
                    ->where('id', $taskId)
                    ->update([
                        'status' => $newStatus,
                        'completed_at' => now(), // Pastikan ini ada
                        'updated_at' => now(),
                        'updated_by' => auth()->id()
                    ]);
            } else {
                // Update status normal
                DB::table('maintenance_tasks')
                    ->where('id', $taskId)
                    ->update([
                        'status' => $newStatus,
                        'updated_at' => now(),
                        'updated_by' => auth()->id()
                    ]);
            }
            
            // Tambahkan log aktivitas
            DB::table('maintenance_activity_logs')->insert([
                'task_id' => $taskId,
                'user_id' => auth()->id(),
                'activity_type' => 'STATUS_CHANGED',
                'description' => 'Status changed to ' . $newStatus,
                'created_at' => now()
            ]);
            
            // Setelah tambahkan log aktivitas
            // Ambil data task dan outlet untuk notifikasi
            $task = DB::table('maintenance_tasks')->where('id', $taskId)->first();
            $outlet = DB::table('tbl_data_outlet')->where('id_outlet', $task->id_outlet)->first();
            $outletName = $outlet ? $outlet->nama_outlet : 'Unknown Outlet';

            NotificationService::sendTaskNotification(
                $taskId,
                'STATUS_CHANGED',
                'Status task #' . $task->task_number . ' "' . $task->title . '" (Outlet: ' . $outletName . ') diubah menjadi ' . $newStatus
            );
            
            // Jika status berubah menjadi IN_REVIEW, kirim notifikasi ke Chief Engineering, QC Manager, dan QC Staff
            if ($newStatus === 'IN_REVIEW') {
                // Kirim notifikasi ke user dengan id_jabatan 165 (Chief Engineering), 262 (QC Manager), dan 209 (QC Staff)
                NotificationService::sendNotificationToSpecificJobs(
                    $taskId,
                    'TASK_NEEDS_REVIEW',
                    'Task #' . $task->task_number . ' "' . $task->title . '" (Outlet: ' . $outletName . ') telah selesai dan memerlukan pengecekan dan QC. Silakan segera lakukan pengecekan.',
                    [165, 262, 209]  // ID jabatan: 165 = Chief Engineering, 262 = QC Manager, 209 = QC Staff
                );
                
                \Log::info('Notifikasi pengecekan dan QC dikirim ke Chief Engineering, QC Manager, dan QC Staff', [
                    'task_id' => $taskId,
                    'task_number' => $task->task_number,
                    'task_title' => $task->title,
                    'outlet' => $outletName
                ]);
            }
            
            // Jika status berubah dari PR ke PO, kirim notifikasi ke Purchasing Manager dan Purchasing Admin
            if ($oldStatus == 'PR' && $newStatus == 'PO') {
                // Ambil data task untuk notifikasi
                $task = DB::table('maintenance_tasks')->where('id', $taskId)->first();
                $outlet = DB::table('tbl_data_outlet')->where('id_outlet', $task->id_outlet)->first();
                $outletName = $outlet ? $outlet->nama_outlet : 'Unknown Outlet';
                
                // Kirim notifikasi ke Purchasing Manager dan Purchasing Admin
                NotificationService::sendNotificationToSpecificJobs(
                    $taskId,
                    'PO_CREATION_NEEDED',
                    'Task ' . $task->task_number . ' - ' . $task->title . ' (Outlet: ' . $outletName . ') telah berubah status dari PR ke PO. Harap buat Purchase Order berdasarkan PR yang telah disetujui.',
                    [168, 244] // ID jabatan: 168 = Purchasing Manager, 244 = Purchasing Admin
                );
                
                Log::info('Notifikasi pembuatan PO dikirim ke Purchasing Manager dan Admin Purchasing', [
                    'task_id' => $taskId,
                    'task_number' => $task->task_number,
                    'task_title' => $task->title,
                    'outlet' => $outletName
                ]);
            }
            
            // Jika status berubah menjadi DONE, kirim notifikasi ke Chief Engineering, QC Manager, dan QC Staff
            if ($newStatus === 'DONE') {
                // Kirim notifikasi ke user dengan id_jabatan yang sama seperti untuk IN_REVIEW
                // yaitu 165 (Chief Engineering), 262 (QC Manager), dan 209 (QC Staff)
                NotificationService::sendNotificationToSpecificJobs(
                    $taskId,
                    'TASK_COMPLETED',
                    'Task #' . $task->task_number . ' "' . $task->title . '" (Outlet: ' . $outletName . ') telah selesai dan dipindahkan ke board DONE.',
                    [165, 262, 209]  // ID jabatan: 165 = Chief Engineering, 262 = QC Manager, 209 = QC Staff
                );
                
                \Log::info('Notifikasi task completed dikirim ke Chief Engineering, QC Manager, dan QC Staff', [
                    'task_id' => $taskId,
                    'task_number' => $task->task_number,
                    'task_title' => $task->title,
                    'outlet' => $outletName
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Status task berhasil diperbarui'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error updating task status: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get timeline data for a task
     *
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTaskTimeline($taskId)
    {
        try {
            $timeline = DB::table('maintenance_activity_logs as ml')
                ->join('users as u', 'ml.user_id', '=', 'u.id')
                ->where('ml.task_id', $taskId)
                ->select('ml.*', 'u.nama_lengkap as user_name')
                ->orderBy('ml.created_at', 'desc')
                ->get();
                
            foreach ($timeline as $item) {
                $item->user = [
                    'id' => $item->user_id,
                    'nama_lengkap' => $item->user_name
                ];
                unset($item->user_name);
            }
            
            return response()->json($timeline);
        } catch (\Exception $e) {
            \Log::error('Error fetching task timeline: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a task
     *
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($taskId)
    {
        return DB::transaction(function() use ($taskId) {
            try {
                // Verifikasi task ada
                $task = DB::table('maintenance_tasks')->where('id', $taskId)->first();
                
                if (!$task) {
                    return response()->json(['error' => 'Task tidak ditemukan'], 404);
                }

                // 1. Hapus file fisik di storage sebelum menghapus record di database
                // Ambil media untuk dihapus dari storage
                $mediaFiles = DB::table('maintenance_media')
                    ->where('task_id', $taskId)
                    ->get(['file_path']);

                foreach ($mediaFiles as $media) {
                    if ($media->file_path && Storage::exists($media->file_path)) {
                        Storage::delete($media->file_path);
                    }
                }

                // Ambil dokumen untuk dihapus dari storage
                $documentFiles = DB::table('maintenance_documents')
                    ->where('task_id', $taskId)
                    ->get(['file_path']);

                foreach ($documentFiles as $document) {
                    if ($document->file_path && Storage::exists($document->file_path)) {
                        Storage::delete($document->file_path);
                    }
                }

                // Ambil attachment komentar untuk dihapus dari storage
                $commentIds = DB::table('maintenance_comments')
                    ->where('task_id', $taskId)
                    ->pluck('id');

                if (count($commentIds) > 0) {
                    $commentAttachments = DB::table('maintenance_comment_attachments')
                        ->whereIn('comment_id', $commentIds)
                        ->get(['file_path']);

                    foreach ($commentAttachments as $attachment) {
                        if ($attachment->file_path && Storage::exists($attachment->file_path)) {
                            Storage::delete($attachment->file_path);
                        }
                    }

                    // 2. Hapus comment_attachments
                    DB::table('maintenance_comment_attachments')
                        ->whereIn('comment_id', $commentIds)
                        ->delete();
                }

                // 3. Hapus comments
                DB::table('maintenance_comments')->where('task_id', $taskId)->delete();
                
                // 4. Hapus maintenance_media
                DB::table('maintenance_media')->where('task_id', $taskId)->delete();
                
                // 5. Hapus maintenance_documents
                DB::table('maintenance_documents')->where('task_id', $taskId)->delete();
                
                // 6. Hapus maintenance_members
                DB::table('maintenance_members')->where('task_id', $taskId)->delete();
                
                // 7. Hapus maintenance_activity_logs
                DB::table('maintenance_activity_logs')->where('task_id', $taskId)->delete();

                // 8. Log aktivitas penghapusan di activity_logs
                $this->logActivity('maintenance', 'DELETE', 'Menghapus task ' . $task->task_number, (array)$task);
                
                // 9. Hapus maintenance_tasks
                DB::table('maintenance_tasks')->where('id', $taskId)->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Task berhasil dihapus'
                ]);
            } catch (\Exception $e) {
                \Log::error('Error deleting task: ' . $e->getMessage());
                \Log::error('Error trace: ' . $e->getTraceAsString());
                return response()->json(['error' => $e->getMessage()], 500);
            }
        });
    }

    /**
     * Log activity helper method
     */
    protected function logActivity($module, $activity_type, $description, $old_data = null)
    {
        try {
            // Log ke activity_logs
            DB::table('activity_logs')->insert([
                'user_id' => auth()->id(),
                'activity_type' => $activity_type,
                'module' => $module,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'old_data' => json_encode($old_data),
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error logging activity: ' . $e->getMessage());
        }
    }

    /**
     * Get task details
     *
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTask($taskId)
    {
        try {
            $task = DB::table('maintenance_tasks')
                ->where('id', $taskId)
                ->first();
            
            // Log untuk debug
            \Log::info('Task data fetched:', [
                'task_id' => $taskId,
                'has_completed_at' => isset($task->completed_at),
                'completed_at' => $task->completed_at ?? null
            ]);
            
            return response()->json([
                'success' => true,
                'task' => $task
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching task details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get members of a task
     *
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTaskMembers($taskId)
    {
        try {
            $members = DB::table('maintenance_members as mm')
                ->join('users as u', 'mm.user_id', '=', 'u.id')
                ->where('mm.task_id', $taskId)
                ->select('u.id', 'u.nama_lengkap as name')
                ->get();
                
            return response()->json($members);
        } catch (\Exception $e) {
            \Log::error('Error fetching task members: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update members of a task
     *
     * @param \Illuminate\Http\Request $request
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTaskMembers(Request $request, $taskId)
    {
        return DB::transaction(function() use ($request, $taskId) {
            try {
                \Log::info('=== START UPDATE TASK MEMBERS ===');
                \Log::info('Request data:', $request->all());
                
                // Verifikasi task ada
                $task = DB::table('maintenance_tasks')->where('id', $taskId)->first();
                
                if (!$task) {
                    return response()->json(['error' => 'Task tidak ditemukan'], 404);
                }
                
                // Verifikasi user memiliki akses
                $user = auth()->user();
                if ($user->id_role !== '5af56935b011a' && $user->division_id != 20) {
                    return response()->json(['error' => 'Anda tidak memiliki akses untuk mengedit task ini'], 403);
                }
                
                // Inisialisasi array untuk menyimpan data member
                $memberData = [];
                
                // Pastikan creator tetap sebagai member
                $memberData[] = [
                    'task_id' => $taskId,
                    'user_id' => $task->created_by,
                    'role' => 'ASSIGNEE',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                // Tangani khusus field member_ids yang dikirim dari form
                if ($request->has('member_ids')) {
                    $memberIds = $request->input('member_ids');
                    // Cek jika member_ids adalah string, parse menjadi array
                    if (is_string($memberIds)) {
                        $memberIds = explode(',', $memberIds);
                    }
                    
                    \Log::info('Processing member_ids from form:', ['member_ids' => $memberIds]);
                    
                    // Pastikan member_ids adalah array
                    if (is_array($memberIds)) {
                    foreach ($memberIds as $memberId) {
                            if (is_numeric($memberId) && intval($memberId) > 0) {
                            // Verifikasi bahwa user ID benar-benar ada dalam database
                            $userExists = DB::table('users')->where('id', intval($memberId))->exists();
                            
                                // Pastikan tidak duplikat dengan creator
                                if ($userExists && intval($memberId) != $task->created_by) {
                                $memberData[] = [
                                    'task_id' => $taskId,
                                    'user_id' => intval($memberId),
                                    'role' => 'ASSIGNEE',
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ];
                                
                                \Log::info('Added member:', ['user_id' => intval($memberId)]);
                            }
                        }
                    }
                    }
                }
                
                // Insert data member ke database
                if (!empty($memberData)) {
                    // Hapus semua data yang lama untuk menghindari duplikasi
                    DB::table('maintenance_members')->where('task_id', $taskId)->delete();
                    
                    // Insert data member baru
                    DB::table('maintenance_members')->insert($memberData);
                    \Log::info('Inserted members:', ['count' => count($memberData), 'data' => $memberData]);
                }
                
                // Insert ke activity logs
                DB::table('maintenance_activity_logs')->insert([
                    'task_id' => $taskId,
                    'user_id' => auth()->id(),
                    'activity_type' => 'MEMBERS_UPDATED',
                    'description' => 'Task members updated',
                    'created_at' => now()
                ]);
                
                // Dapatkan nama outlet
                $outlet = DB::table('tbl_data_outlet')
                    ->where('id_outlet', $task->id_outlet)
                    ->first();
                $outletName = $outlet ? $outlet->nama_outlet : 'Unknown Outlet';

                // Ambil member yang baru ditambahkan (kecuali creator dan current user)
                $newMembers = array_filter($memberData, function($member) use ($task) {
                    return $member['user_id'] != $task->created_by && $member['user_id'] != auth()->id();
                });

                // Kirim notifikasi ke member baru
                if (!empty($newMembers)) {
                    $notifications = [];
                    foreach ($newMembers as $member) {
                        $notifications[] = [
                            'user_id' => $member['user_id'],
                            'task_id' => $taskId,
                            'type' => 'MEMBER_ADDED',
                            'message' => 'Anda telah ditugaskan ke task #' . $task->task_number . ': ' . $task->title . ' (Outlet: ' . $outletName . ')',
                            'url' => '/maintenance/kanban/task/' . $taskId,
                            'is_read' => false,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                    
                    // Simpan semua notifikasi
                    if (!empty($notifications)) {
                        DB::table('notifications')->insert($notifications);
                        \Log::info('Sent notifications to newly assigned members:', ['count' => count($notifications)]);
                    }
                }
                
                \Log::info('=== END UPDATE TASK MEMBERS ===');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Members task berhasil diperbarui'
                ]);
                
            } catch (\Exception $e) {
                \Log::error('=== ERROR UPDATE TASK MEMBERS ===');
                \Log::error('Error Message: ' . $e->getMessage());
                \Log::error('Error Code: ' . $e->getCode());
                \Log::error('Error File: ' . $e->getFile() . ':' . $e->getLine());
                \Log::error('Error Trace: ' . $e->getTraceAsString());
                
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        });
    }

    /**
     * Get task preview data for notification
     *
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTaskPreview($taskId)
    {
        try {
            // Log untuk debugging
            \Log::info("Fetching task preview for task ID: {$taskId}");
            
            $task = DB::table('maintenance_tasks as t')
                ->leftJoin('users as u', 't.created_by', '=', 'u.id')
                ->leftJoin('maintenance_labels as l', 't.label_id', '=', 'l.id')
                ->leftJoin('maintenance_priorities as p', 't.priority_id', '=', 'p.id') // Periksa relasi ini
                ->where('t.id', $taskId)
                ->select(
                    't.*',
                    'u.nama_lengkap as creator_name',
                    'l.name as label_name',
                    'l.color as label_color',
                    'p.priority as priority_name', // Pastikan nama kolom ini benar
                    'p.description as priority_description'
                )
                ->first();
            
            // Log hasil query untuk debugging
            \Log::info("Task data:", [
                'task_id' => $taskId,
                'priority_id' => $task->priority_id ?? 'null',
                'priority_name' => $task->priority_name ?? 'null'
            ]);
            
            if (!$task) {
                return response()->json(['success' => false, 'message' => 'Task tidak ditemukan'], 404);
            }
            
            // Ambil jumlah komentar
            $commentCount = DB::table('maintenance_comments')
                ->where('task_id', $taskId)
                ->count();
            
            // Ambil jumlah media
            $mediaCount = DB::table('maintenance_media')
                ->where('task_id', $taskId)
                ->count();
            
            $task->comment_count = $commentCount;
            $task->media_count = $mediaCount;
            
            return response()->json([
                'success' => true,
                'task' => $task
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching task preview: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Approve a PR
     */
    public function approvePr(Request $request, $id)
    {
        $pr = MaintenancePurchaseRequisition::find($id);
        if (!$pr) {
            return response()->json(['success' => false, 'message' => 'Purchase Requisition tidak ditemukan'], 404);
        }
        
        // Log data request untuk debugging
        Log::info('PR Approval Request untuk ID: ' . $id, [
            'input' => $request->all(),
            'user_id' => Auth::id()
        ]);
        
        // Ambil notes dari request
        $notes = $request->input('notes');
        
        // Log nilai notes yang diterima dari form
        Log::info('Notes from request input: ' . ($notes ?: 'NULL'));
        
        // Coba ambil dari query string (jika ada)
        $queryNotes = $request->query('approvalNotes');
        if (!$notes && $queryNotes) {
            $notes = $queryNotes;
            Log::info('Using notes from query string');
        }
        
        // Coba dari field khusus jika masih null
        if (!$notes) {
            // Chief Engineering approval notes
            if ($request->filled('chief_engineering_approval_notes')) {
                $notes = $request->input('chief_engineering_approval_notes');
                Log::info('Using notes from chief_engineering_approval_notes field');
            }
            // Purchasing Manager approval notes
            elseif ($request->filled('purchasing_manager_approval_notes')) {
                $notes = $request->input('purchasing_manager_approval_notes');
                Log::info('Using notes from purchasing_manager_approval_notes field');
            }
            // COO approval notes
            elseif ($request->filled('coo_approval_notes')) {
                $notes = $request->input('coo_approval_notes');
                Log::info('Using notes from coo_approval_notes field');
            }
        }
        
        try {
            // Tentukan level approval berdasarkan status PR saat ini
        $level = $request->input('approval_level');
            
            if (!$level) {
                if (!$pr->chief_engineering_approval || $pr->chief_engineering_approval == 'PENDING') {
                    $level = 'Chief Engineering';
                } elseif ($pr->chief_engineering_approval == 'APPROVED' && 
                        (!$pr->purchasing_manager_approval || $pr->purchasing_manager_approval == 'PENDING')) {
                    $level = 'Purchasing Manager';
                } elseif ($pr->chief_engineering_approval == 'APPROVED' && 
                        $pr->purchasing_manager_approval == 'APPROVED' && 
                        (!$pr->coo_approval || $pr->coo_approval == 'PENDING')) {
                    $level = 'COO';
                }
                
                Log::info('Determined approval level: ' . $level);
            }
            
            // Ambil data task dan outlet
            $task = DB::table('maintenance_tasks')->where('id', $pr->task_id)->first();
            $outlet = DB::table('tbl_data_outlet')->where('id_outlet', $task->id_outlet)->first();
            $outletName = $outlet ? $outlet->nama_outlet : 'Unknown Outlet';
            $taskTitle = $task ? $task->title : 'Unknown Task';
            
            // Update PR berdasarkan level approval
        if ($level == 'Chief Engineering') {
                // Pastikan ada notes
                $finalNotes = $notes ?: 'Approved by Chief Engineering';
                
                $pr->chief_engineering_approval = 'APPROVED';
                $pr->chief_engineering_approval_by = Auth::id();
                $pr->chief_engineering_approval_date = now();
                $pr->chief_engineering_approval_notes = $finalNotes;
                
                // Log aktivitas
                DB::table('maintenance_activity_logs')->insert([
                    'task_id' => $pr->task_id,
                    'user_id' => Auth::id(),
                    'activity_type' => 'PR_APPROVED',
                    'description' => 'PR ' . $pr->pr_number . ' approved by Chief Engineering',
                    'created_at' => now()
                ]);
                
                // Kirim notifikasi ke semua user terkait task - satu kali saja
                NotificationService::sendTaskNotification(
                    $pr->task_id,
                    'PR_CHIEF_ENGINEERING_APPROVED',
                    'PR ' . $pr->pr_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') telah disetujui oleh Chief Engineering'
                );
                
                // Kirim notifikasi ke Purchasing Manager dan Purchasing Admin - satu kali saja
                $purchasingManagerIds = [168, 244]; // Purchasing Manager dan Purchasing Admin
                NotificationService::sendNotificationToSpecificJobs(
                    $pr->task_id,
                    'PR_NEEDS_APPROVAL',
                    'PR ' . $pr->pr_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') memerlukan persetujuan Anda sebagai Purchasing Manager',
                    $purchasingManagerIds
                );
            } 
            elseif ($level == 'Purchasing Manager') {
                // Pastikan Chief Engineering sudah approve
                if ($pr->chief_engineering_approval != 'APPROVED') {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Chief Engineering approval is required before Purchasing Manager can approve'
                    ], 400);
                }
                
                // Pastikan ada notes
                $finalNotes = $notes ?: 'Approved by Purchasing Manager';
                
                $pr->purchasing_manager_approval = 'APPROVED';
                $pr->purchasing_manager_approval_by = Auth::id();
                $pr->purchasing_manager_approval_date = now();
                $pr->purchasing_manager_approval_notes = $finalNotes;
                
                // Log aktivitas
                DB::table('maintenance_activity_logs')->insert([
                    'task_id' => $pr->task_id,
                    'user_id' => Auth::id(),
                    'activity_type' => 'PR_APPROVED',
                    'description' => 'PR ' . $pr->pr_number . ' approved by Purchasing Manager',
                    'created_at' => now()
                ]);
                
                // Kirim notifikasi ke semua user terkait task - satu kali saja
                NotificationService::sendTaskNotification(
                    $pr->task_id,
                    'PR_PURCHASING_MANAGER_APPROVED',
                    'PR ' . $pr->pr_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') telah disetujui oleh Purchasing Manager'
                );
                
                // Kirim notifikasi ke Sekretaris dan COO - satu kali saja
                $cooSecretaryIds = [151, 217]; // COO dan Sekretaris
                NotificationService::sendNotificationToSpecificJobs(
                    $pr->task_id,
                    'PR_NEEDS_APPROVAL',
                    'PR ' . $pr->pr_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') memerlukan persetujuan Anda sebagai COO',
                    $cooSecretaryIds
                );
            } 
            elseif ($level == 'COO') {
                // Pastikan Chief Engineering dan Purchasing Manager sudah approve
                if ($pr->chief_engineering_approval != 'APPROVED' || $pr->purchasing_manager_approval != 'APPROVED') {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Chief Engineering and Purchasing Manager approval are required before COO can approve'
                    ], 400);
                }
                
                // Simpan notes dari berbagai sumber sesuai level
                $finalNotes = $notes ?: 'Approved by COO';
                
                $pr->coo_approval = 'APPROVED';
                $pr->coo_approval_by = Auth::id();
                $pr->coo_approval_date = now();
                $pr->coo_approval_notes = $finalNotes;
                $pr->status = 'APPROVED'; // PR fully approved
                
                // Log aktivitas
                DB::table('maintenance_activity_logs')->insert([
                    'task_id' => $pr->task_id,
                    'user_id' => Auth::id(),
                    'activity_type' => 'PR_APPROVED',
                    'description' => 'PR ' . $pr->pr_number . ' fully approved by COO',
                    'created_at' => now()
                ]);
                
                // Kirim notifikasi ke semua user terkait task - satu kali saja
                NotificationService::sendTaskNotification(
                    $pr->task_id,
                    'PR_FULLY_APPROVED',
                    'PR ' . $pr->pr_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') telah disetujui sepenuhnya oleh COO'
                );
            } 
            else {
                return response()->json(['success' => false, 'message' => 'Level approval tidak valid'], 400);
            }
            
            // Simpan perubahan setelah semua modifikasi dilakukan
            $pr->save();
            
            Log::info('PR successfully approved', [
                'pr_id' => $pr->id,
                'pr_number' => $pr->pr_number,
            'level' => $level,
                'approved_by' => Auth::id()
        ]);
                
                return response()->json([
                    'success' => true,
                'message' => 'Purchase Requisition telah disetujui'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error approving PR', [
                'pr_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to approve Purchase Requisition: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectPr(Request $request, $id)
    {
        $pr = MaintenancePurchaseRequisition::find($id);
        if (!$pr) {
            return response()->json(['success' => false, 'message' => 'Purchase Requisition tidak ditemukan'], 404);
        }

        // Log nilai request untuk debugging
        Log::info('PR Rejection Request untuk ID: ' . $id, [
            'input' => $request->all(),
            'user_id' => Auth::id()
        ]);

        // Ambil catatan penolakan dari request
        $notes = $request->input('notes');
        
        // Log nilai notes yang diterima dari form
        Log::info('Rejection notes from request input: ' . ($notes ?: 'NULL'));
        
        // Coba ambil dari query string (jika ada)
        $queryNotes = $request->query('rejectionNotes');
        if (!$notes && $queryNotes) {
            $notes = $queryNotes;
            Log::info('Using rejection notes from query string');
        }
        
        // Coba dari field khusus jika masih null
        if (!$notes && $request->has('rejection_notes')) {
            $notes = $request->input('rejection_notes');
            Log::info('Using rejection notes from rejection_notes field');
        }
        
        // Pastikan ada catatan penolakan
        if (!$notes) {
                return response()->json([
                    'success' => false,
                'message' => 'Alasan penolakan (notes) wajib diisi'
            ], 400);
        }
        
        try {
            // Tentukan siapa yang menolak berdasarkan status PR
            $level = $request->input('rejection_level');
            
            if (!$level) {
                // Tentukan level penolakan berdasarkan status PR saat ini
            if (!$pr->chief_engineering_approval || $pr->chief_engineering_approval == 'PENDING') {
                    $level = 'Chief Engineering';
                } elseif ($pr->chief_engineering_approval == 'APPROVED' && 
                   (!$pr->purchasing_manager_approval || $pr->purchasing_manager_approval == 'PENDING')) {
                    $level = 'Purchasing Manager';
                } elseif ($pr->chief_engineering_approval == 'APPROVED' && 
                    $pr->purchasing_manager_approval == 'APPROVED' && 
                    (!$pr->coo_approval || $pr->coo_approval == 'PENDING')) {
                    $level = 'COO';
                }
                
                Log::info('Determined rejection level: ' . $level);
            }
            
            // Ambil data task dan outlet
            $task = DB::table('maintenance_tasks')->where('id', $pr->task_id)->first();
            $outlet = DB::table('tbl_data_outlet')->where('id_outlet', $task->id_outlet)->first();
            $outletName = $outlet ? $outlet->nama_outlet : 'Unknown Outlet';
            $taskTitle = $task ? $task->title : 'Unknown Task';
            
            // Update PR berdasarkan level penolakan
            if ($level == 'Chief Engineering') {
                $pr->chief_engineering_approval = 'REJECTED';
                $pr->chief_engineering_approval_by = Auth::id();
                $pr->chief_engineering_approval_date = now();
                $pr->chief_engineering_approval_notes = $notes;
                $pr->status = 'REJECTED';
                
                Log::info('Chief Engineering rejected PR', [
                    'rejected_by' => Auth::id(),
                    'notes' => $notes
                ]);
                
                // Log aktivitas penolakan ke activity logs
                DB::table('maintenance_activity_logs')->insert([
                    'task_id' => $pr->task_id,
                    'user_id' => Auth::id(),
                    'activity_type' => 'PR_REJECTED',
                    'description' => 'PR ' . $pr->pr_number . ' rejected by Chief Engineering. Reason: ' . $notes,
                    'created_at' => now()
                ]);
                
                // Kirim notifikasi ke semua user terkait task
                NotificationService::sendTaskNotification(
                    $pr->task_id,
                    'PR_REJECTED',
                    'PR ' . $pr->pr_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') telah ditolak oleh Chief Engineering. Alasan: ' . $notes
                );
                
                // Maintenance Admin menerima notifikasi dari sendTaskNotification, tidak perlu kirim lagi
                // NotificationService::sendNotificationToSpecificJobs(
                //     $pr->task_id,
                //     'PR_INFO',
                //     'PR ' . $pr->pr_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') telah ditolak oleh Chief Engineering',
                //     [263] // Maintenance Admin
                // );
            } 
            elseif ($level == 'Purchasing Manager') {
                $pr->purchasing_manager_approval = 'REJECTED';
                $pr->purchasing_manager_approval_by = Auth::id();
                $pr->purchasing_manager_approval_date = now();
                $pr->purchasing_manager_approval_notes = $notes;
                $pr->status = 'REJECTED';
                
                Log::info('Purchasing Manager rejected PR', [
                    'rejected_by' => Auth::id(),
                    'notes' => $notes
                ]);
                
                // Log aktivitas penolakan ke activity logs
                DB::table('maintenance_activity_logs')->insert([
                    'task_id' => $pr->task_id,
                    'user_id' => Auth::id(),
                    'activity_type' => 'PR_REJECTED',
                    'description' => 'PR ' . $pr->pr_number . ' rejected by Purchasing Manager. Reason: ' . $notes,
                    'created_at' => now()
                ]);
                
                // Kirim notifikasi ke semua user terkait task
                NotificationService::sendTaskNotification(
                    $pr->task_id,
                    'PR_REJECTED',
                    'PR ' . $pr->pr_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') telah ditolak oleh Purchasing Manager. Alasan: ' . $notes
                );
                
                // Maintenance Admin menerima notifikasi dari sendTaskNotification, tidak perlu kirim lagi
                // NotificationService::sendNotificationToSpecificJobs(
                //     $pr->task_id,
                //     'PR_INFO',
                //     'PR ' . $pr->pr_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') telah ditolak oleh Purchasing Manager',
                //     [263] // Maintenance Admin
                // );
            } 
            elseif ($level == 'COO') {
                $pr->coo_approval = 'REJECTED';
                $pr->coo_approval_by = Auth::id();
                $pr->coo_approval_date = now();
                $pr->coo_approval_notes = $notes;
                $pr->status = 'REJECTED';
                
                Log::info('COO rejected PR', [
                    'rejected_by' => Auth::id(),
                    'notes' => $notes
                ]);
                
                // Log aktivitas penolakan ke activity logs
                DB::table('maintenance_activity_logs')->insert([
                    'task_id' => $pr->task_id,
                    'user_id' => Auth::id(),
                    'activity_type' => 'PR_REJECTED',
                    'description' => 'PR ' . $pr->pr_number . ' rejected by COO. Reason: ' . $notes,
                    'created_at' => now()
                ]);
                
                // Kirim notifikasi ke semua user terkait task
                NotificationService::sendTaskNotification(
                    $pr->task_id,
                    'PR_REJECTED',
                    'PR ' . $pr->pr_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') telah ditolak oleh COO. Alasan: ' . $notes
                );
                
                // Maintenance Admin menerima notifikasi dari sendTaskNotification, tidak perlu kirim lagi
                // NotificationService::sendNotificationToSpecificJobs(
                //     $pr->task_id,
                //     'PR_INFO',
                //     'PR ' . $pr->pr_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') telah ditolak oleh COO',
                //     [263] // Maintenance Admin
                // );
            } 
            else {
                return response()->json(['success' => false, 'message' => 'Level penolakan tidak valid'], 400);
            }
            
            $pr->save();
            
            Log::info('PR successfully rejected', [
                'pr_id' => $pr->id,
                'pr_number' => $pr->pr_number,
                'level' => $level,
                'rejected_by' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Purchase Requisition telah ditolak'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error rejecting PR', [
                'pr_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak Purchase Requisition: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPrDetail($id)
    {
        try {
            // Cari PR berdasarkan ID
            $pr = DB::table('maintenance_purchase_requisitions as pr')
                ->leftJoin('users as u', 'pr.created_by', '=', 'u.id')
                ->leftJoin('maintenance_tasks as t', 'pr.task_id', '=', 't.id')
                ->where('pr.id', $id)
                ->select(
                    'pr.*',
                    'u.nama_lengkap as creator_name',
                    't.task_number'
                )
                ->first();
            
            if (!$pr) {
                return response()->json([
                    'success' => false,
                    'message' => 'PR not found'
                ], 404);
            }
            
            // Get PR items
            $items = DB::table('maintenance_purchase_requisition_items as pri')
                ->leftJoin('units as u', 'pri.unit_id', '=', 'u.id')
                ->where('pri.pr_id', $id)
                ->select('pri.*', 'u.name as unit_name')
                ->get();
            
            // Add items to PR
            $pr->items = $items;
            
            return response()->json([
                'success' => true,
                'data' => $pr
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting PR detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get PR detail'
            ], 500);
        }
    }

    public function previewBa($id)
    {
        try {
            // Load BA data with relationships
            $ba = \App\Models\MaintenancePurchaseRequisition::with([
                'items.unit',
                'creator.jabatan',
                'task',
                'chiefEngineeringApprover.jabatan',
                'purchasingManagerApprover.jabatan',
                'cooApprover.jabatan'
            ])->findOrFail($id);
            
            // Ambil foto-foto dari task
            $taskPhotos = DB::table('maintenance_media')
                ->where('task_id', $ba->task_id)
                ->where('file_type', 'like', 'image/%')
                ->select('id', 'file_name', 'file_path', 'file_type')
                ->get();
            
            // Ambil user dengan jabatan Chief Engineering, Purchasing Manager, dan COO
            // dengan filter status='A' (aktif)
            $chiefEngineering = \App\Models\User::with('jabatan')
                ->where('id_jabatan', 165)
                ->where('status', 'A')
                ->first();
                
            $purchasingManager = \App\Models\User::with('jabatan')
                ->where('id_jabatan', 168)
                ->where('status', 'A')
                ->first();
                
            $coo = \App\Models\User::with('jabatan')
                ->where('id_jabatan', 151)
                ->where('status', 'A')
                ->first();
            
            return view('maintenance.purchase-requisition.preview-ba', compact(
                'ba', 
                'taskPhotos',
                'chiefEngineering', 
                'purchasingManager', 
                'coo'
            ));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error generating BA preview: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Gagal membuat preview BA: ' . $e->getMessage());
        }
    }

    public function getTaskPos($taskId)
    {
        try {
            // Ambil data task
            $task = MaintenanceTask::findOrFail($taskId);
            
            // Ambil daftar PO untuk task ini dengan eager loading supplier
            $pos = MaintenancePurchaseOrder::with(['supplier']) // Tambahkan eager loading supplier
                ->where('task_id', $taskId)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $pos,
                'task' => $task
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting task POs: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load purchase orders'
            ], 500);
        }
    }

    /**
     * Get PR items for a task that can be used to create PO
     */
    public function getTaskPrItems($id)
    {
        try {
            // Ambil semua PR yang sudah diapprove dari task ini dan belum PO
            $prs = DB::table('maintenance_purchase_requisitions as pr')
                ->select(
                    'pr.id as pr_id',
                    'pr.pr_number',
                    'pr.task_id',
                    'pr.total_amount',
                    'pr.notes as pr_notes',
                    'pri.id as item_id',
                    'pri.item_name',
                    'pri.description',
                    'pri.specifications',
                    'pri.quantity',
                    'pri.unit_id',
                    'pri.price',
                    'pri.subtotal',
                    'pri.notes as item_notes',
                    'u.name as unit_name'
                )
                ->join('maintenance_purchase_requisition_items as pri', 'pr.id', '=', 'pri.pr_id')
                ->join('units as u', 'pri.unit_id', '=', 'u.id')
                ->where('pr.task_id', $id)
                ->where('pr.status', 'APPROVED')
                ->where('pr.chief_engineering_approval', 'APPROVED')
                ->where('pr.purchasing_manager_approval', 'APPROVED')
                ->where('pr.coo_approval', 'APPROVED')
                ->whereNotIn('pr.status', ['PO'])
                ->get();

            // Ambil supplier yang aktif dengan informasi lengkap
            $suppliers = DB::table('suppliers')
                ->select(
                    'id',
                    'code',
                    'name',
                    'contact_person',
                    'phone',
                    'email',
                    'address',
                    'city',
                    'province',
                    'postal_code',
                    'npwp',
                    'bank_name',
                    'bank_account_number',
                    'bank_account_name',
                    'payment_term',
                    'payment_days'
                )
                ->where('status', 'active')
                ->orderBy('name')
                ->get();

            // Format data untuk response
            $formattedPrs = [];
            foreach ($prs as $item) {
                if (!isset($formattedPrs[$item->pr_id])) {
                    $formattedPrs[$item->pr_id] = [
                        'pr_id' => $item->pr_id,
                        'pr_number' => $item->pr_number,
                        'total_amount' => $item->total_amount,
                        'notes' => $item->pr_notes,
                        'items' => []
                    ];
                }

                $formattedPrs[$item->pr_id]['items'][] = [
                    'id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'description' => $item->description,
                    'specifications' => $item->specifications,
                    'quantity' => $item->quantity,
                    'unit_id' => $item->unit_id,
                    'unit_name' => $item->unit_name,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal,
                    'notes' => $item->item_notes
                ];
            }

            return response()->json([
                'success' => true,
                'prs' => array_values($formattedPrs),
                'suppliers' => $suppliers
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting task PR items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat item PR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new PO
     */
    public function storePo(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validasi input
            $request->validate([
                'supplier_id' => 'required|exists:suppliers,id',
                'task_id' => 'required|exists:maintenance_tasks,id',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:maintenance_purchase_requisition_items,id',
                'items.*.pr_id' => 'required|exists:maintenance_purchase_requisitions,id',
                'items.*.supplier_price' => 'required|numeric|min:0',
                'items.*.supplier_subtotal' => 'required|numeric|min:0'
            ]);

            // Generate PO number
            $poNumber = 'PO-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

            // Hitung total amount
            $totalAmount = collect($request->items)->sum('supplier_subtotal');

            // Create single PO untuk supplier
            $po = DB::table('maintenance_purchase_orders')->insertGetId([
                'po_number' => $poNumber,
                'task_id' => $request->task_id,
                'supplier_id' => $request->supplier_id,
                'status' => 'DRAFT',
                'total_amount' => $totalAmount,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create PO items
            $poItems = collect($request->items)->map(function($item) use ($po) {
                $prItem = DB::table('maintenance_purchase_requisition_items')
                    ->find($item['item_id']);
                
                return [
                    'po_id' => $po,
                    'item_name' => $prItem->item_name,
                    'description' => $prItem->description,
                    'specifications' => $prItem->specifications,
                    'quantity' => $prItem->quantity,
                    'unit_id' => $prItem->unit_id,
                    'price' => $prItem->price,
                    'supplier_price' => $item['supplier_price'],
                    'subtotal' => $item['supplier_subtotal'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->all();

            // Insert semua items sekaligus
            DB::table('maintenance_purchase_order_items')->insert($poItems);

            // Update status PR menjadi PO
            $prIds = collect($request->items)->pluck('pr_id')->unique();
            DB::table('maintenance_purchase_requisitions')
                ->whereIn('id', $prIds)
                ->update(['status' => 'PO']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PO berhasil dibuat',
                'po_id' => $po
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating PO: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PO: ' . $e->getMessage()
            ], 500);
        }
    }

    public function canApproveOrRejectPo()
    {
        $user = auth()->user();
        
        // Superadmin dan Sekretaris bisa approve/reject semua level
        if ($user->id_role === '5af56935b011a' || 
            ($user->id_jabatan === 217 && $user->status === 'A')) {
            return true;
        }
        
        // Cek jabatan dan status user
        if ($user->status === 'A') {
            $allowedJabatan = [
                149, // Presiden Director
                150, // Managing Director
                152  // GM Finance
            ];
            
            return in_array($user->id_jabatan, $allowedJabatan);
        }
        
        return false;
    }

    public function getPoList($taskId)
    {
        try {
            $pos = DB::table('maintenance_purchase_orders as po')
                ->select(
                    'po.id',
                    'po.po_number',
                    'po.status',
                    'po.total_amount',
                    'po.created_at',
                    'po.supplier_id',
                    's.name as supplier_name',
                    'u1.nama_lengkap as creator_name',
                    'u2.nama_lengkap as approver_name'
                )
                ->join('suppliers as s', 'po.supplier_id', '=', 's.id')
                ->join('users as u1', 'po.created_by', '=', 'u1.id')
                ->leftJoin('users as u2', 'po.approved_by', '=', 'u2.id')
                ->where('po.task_id', $taskId)
                ->orderBy('po.created_at', 'desc')
                ->get();

            // Debug log
            \Log::info('PO List Data:', ['pos' => $pos]);

            $task = DB::table('maintenance_tasks')
                ->where('id', $taskId)
                ->first();

            return response()->json([
                'success' => true,
                'data' => $pos,
                'task' => $task,
                'can_approve' => $this->canApproveOrRejectPo()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getPoList: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load POs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPoDetail($id)
    {
        try {
            $po = MaintenancePurchaseOrder::findOrFail($id);
            
            // Format data yang diperlukan
            $data = [
                'id' => $po->id,
                'po_number' => $po->po_number,
                'created_at' => $po->created_at,
                'status' => $po->status,
                'creator_name' => $po->creator->nama_lengkap,
                'supplier_name' => $po->supplier->name,
                'supplier_phone' => $po->supplier->phone,
                'supplier_email' => $po->supplier->email,
                'supplier_address' => $po->supplier->address,
                'total_amount' => $po->total_amount,
                'items' => $po->items->map(function($item) {
                    return [
                        'item_name' => $item->item_name,
                        'description' => $item->description,
                        'specifications' => $item->specifications,
                        'quantity' => $item->quantity,
                        'unit_name' => $item->unit->name,
                        'supplier_price' => $item->supplier_price,
                        'subtotal' => $item->subtotal
                    ];
                }),
                // Tambahkan informasi invoice
                'invoice_number' => $po->invoice_number,
                'invoice_date' => $po->invoice_date,
                'invoice_file_path' => $po->invoice_file_path,
                // Approval information
                'gm_finance_approval' => $po->gm_finance_approval,
                'gm_finance_approval_date' => $po->gm_finance_approval_date,
                'gm_finance_approval_notes' => $po->gm_finance_approval_notes,
                'managing_director_approval' => $po->managing_director_approval,
                'managing_director_approval_date' => $po->managing_director_approval_date,
                'managing_director_approval_notes' => $po->managing_director_approval_notes,
                'president_director_approval' => $po->president_director_approval,
                'president_director_approval_date' => $po->president_director_approval_date,
                'president_director_approval_notes' => $po->president_director_approval_notes,
                // Tambahkan payment terms
                'supplier_payment_term' => $po->supplier->payment_term,
                'supplier_payment_days' => $po->supplier->payment_days,
                
                // Tambahkan data good receive
                'receive_date' => $po->receive_date,
                'receive_notes' => $po->receive_notes,
                'receive_photos' => $po->receive_photos,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load PO detail: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approvePo(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $user = auth()->user();
            $now = now();
            $notes = $request->input('notes');
            $level = $request->input('approval_level');
            
            // Cari PO berdasarkan ID
            $po = MaintenancePurchaseOrder::findOrFail($id);
            
            // Tentukan level approval berdasarkan status PO saat ini dan role user
            $isSuperAdmin = $user->id_role == '5af56935b011a';
            $isSecretary = $user->id_jabatan == 217;
            
            // Default update data
            $updateData = [];
            
            // Ambil data task dan outlet untuk notifikasi
            $task = DB::table('maintenance_tasks')->where('id', $po->task_id)->first();
            $outlet = DB::table('tbl_data_outlet')->where('id_outlet', $task->id_outlet)->first();
            $outletName = $outlet ? $outlet->nama_outlet : 'Unknown Outlet';
            $taskTitle = $task ? $task->title : 'Unknown Task';
            
            // Level 1: GM Finance approval
            if ($level == 'GM Finance' || 
                (!$po->gm_finance_approval || $po->gm_finance_approval == 'PENDING')) {
                // Hanya superadmin, sekretaris, atau GM Finance yang bisa approve
                if ($isSuperAdmin || $isSecretary || $user->id_jabatan == 152) {
                    $updateData = [
                        'gm_finance_approval' => 'APPROVED',
                        'gm_finance_approval_date' => $now,
                        'gm_finance_approval_by' => $user->id,
                        'gm_finance_approval_notes' => $notes
                    ];
                }
            }
            // Level 2: Managing Director approval
            elseif ($level == 'Managing Director' || 
                    ($po->gm_finance_approval == 'APPROVED' && 
                    (!$po->managing_director_approval || $po->managing_director_approval == 'PENDING'))) {
                if ($isSuperAdmin || $isSecretary || $user->id_jabatan == 150) {
                    $updateData = [
                        'managing_director_approval' => 'APPROVED',
                        'managing_director_approval_date' => $now,
                        'managing_director_approval_by' => $user->id,
                        'managing_director_approval_notes' => $notes
                    ];
                }
            }
            // Level 3: President Director approval
            elseif ($level == 'President Director' || 
                    ($po->gm_finance_approval == 'APPROVED' && 
                    $po->managing_director_approval == 'APPROVED' && 
                    (!$po->president_director_approval || $po->president_director_approval == 'PENDING'))) {
                if ($isSuperAdmin || $isSecretary || $user->id_jabatan == 149) {
                    $updateData = [
                        'president_director_approval' => 'APPROVED',
                        'president_director_approval_date' => $now,
                        'president_director_approval_by' => $user->id,
                        'president_director_approval_notes' => $notes
                    ];
                }
            }
            
            // Jika ada data yang perlu diupdate
            if (!empty($updateData)) {
                try {
                    // Update PO
                    DB::table('maintenance_purchase_orders')
                        ->where('id', $id)
                        ->update($updateData);
                    
                    // Setelah update, cek status approval untuk update status PO
                    $updatedPo = MaintenancePurchaseOrder::find($id);
                    
                    // Update status PO berdasarkan approval yang ada
                    $newStatus = 'PENDING';
                    
                    if ($updatedPo->gm_finance_approval == 'APPROVED' &&
                        $updatedPo->managing_director_approval == 'APPROVED' &&
                        $updatedPo->president_director_approval == 'APPROVED') {
                        $newStatus = 'APPROVED';
                    } elseif ($updatedPo->gm_finance_approval == 'REJECTED' ||
                             $updatedPo->managing_director_approval == 'REJECTED' ||
                             $updatedPo->president_director_approval == 'REJECTED') {
                        $newStatus = 'REJECTED';
                    }
                    
                    // Update status PO jika berbeda
                    if ($updatedPo->status != $newStatus) {
                        DB::table('maintenance_purchase_orders')
                            ->where('id', $id)
                            ->update(['status' => $newStatus]);
                    }
                    
                    // Log aktivitas
                    DB::table('maintenance_activity_logs')->insert([
                        'task_id' => $po->task_id,
                        'user_id' => $user->id,
                        'activity_type' => 'PO_APPROVED',
                        'description' => $user->nama_lengkap . ' approved PO ' . $po->po_number . ' as ' . $level,
                        'created_at' => now()
                    ]);
                    
                    // Siapkan notifikasi berdasarkan level approval
                    $notificationType = '';
                    $notificationMessage = '';
                    $additionalRecipients = [];
                    
                    if ($level == 'GM Finance' || (!$po->gm_finance_approval || $po->gm_finance_approval == 'PENDING')) {
                        $notificationType = 'PO_GM_FINANCE_APPROVED';
                        $notificationMessage = 'PO ' . $po->po_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') telah disetujui oleh GM Finance';
                        $additionalRecipients = [150, 217]; // Managing Director dan Sekretaris
                    } 
                    elseif ($level == 'Managing Director' || ($po->gm_finance_approval == 'APPROVED' && 
                        (!$po->managing_director_approval || $po->managing_director_approval == 'PENDING'))) {
                        $notificationType = 'PO_MANAGING_DIRECTOR_APPROVED';
                        $notificationMessage = 'PO ' . $po->po_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') telah disetujui oleh Managing Director';
                        $additionalRecipients = [149, 217]; // President Director dan Sekretaris
                    } 
                    elseif ($level == 'President Director' || ($po->gm_finance_approval == 'APPROVED' && 
                        $po->managing_director_approval == 'APPROVED' && 
                        (!$po->president_director_approval || $po->president_director_approval == 'PENDING'))) {
                        $notificationType = 'PO_FULLY_APPROVED';
                        $notificationMessage = 'PO ' . $po->po_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') telah disetujui sepenuhnya';
                        $additionalRecipients = [168, 244]; // Purchasing Manager dan Purchasing Admin
                    }
                    
                    // Kirim notifikasi ke semua user terkait task dan penerima tambahan dalam satu kali
                    if (!empty($notificationType) && !empty($notificationMessage)) {
                        // Cek duplikasi notifikasi sebelum mengirim
                        $existingNotification = DB::table('notifications')
                            ->where('task_id', $po->task_id)
                            ->where('type', $notificationType)
                            ->where('created_at', '>=', now()->subMinutes(5))
                            ->first();
                        
                        if (!$existingNotification) {
                            // Kirim notifikasi ke semua user terkait task
                            NotificationService::sendTaskNotification(
                                $po->task_id,
                                $notificationType,
                                $notificationMessage
                            );
                            
                            // Kirim notifikasi ke penerima tambahan
                            if (!empty($additionalRecipients)) {
                                NotificationService::sendNotificationToSpecificJobs(
                                    $po->task_id,
                                    $notificationType,
                                    $notificationMessage,
                                    $additionalRecipients
                                );
                            }
                            
                            // Kirim notifikasi khusus untuk level berikutnya (jika bukan approval terakhir)
                            if ($level == 'GM Finance') {
                                // Notifikasi khusus untuk Managing Director
                                NotificationService::sendNotificationToSpecificJobs(
                                    $po->task_id,
                                    'PO_NEEDS_APPROVAL',
                                    'PO ' . $po->po_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') memerlukan persetujuan Anda sebagai Managing Director',
                                    [150, 217] // Managing Director dan Sekretaris
                                );
                            } 
                            elseif ($level == 'Managing Director') {
                                // Notifikasi khusus untuk President Director
                                NotificationService::sendNotificationToSpecificJobs(
                                    $po->task_id,
                                    'PO_NEEDS_APPROVAL',
                                    'PO ' . $po->po_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') memerlukan persetujuan Anda sebagai President Director',
                                    [149, 217] // President Director dan Sekretaris
                                );
                            }
                        }
                    }
                    
                    DB::commit();
                    
                    Log::info('PO successfully approved', [
                        'po_id' => $po->id,
                        'po_number' => $po->po_number,
                        'level' => $level,
                        'approved_by' => $user->id
                    ]);
                    
                    // Refresh PO data
                    $finalPo = MaintenancePurchaseOrder::find($id);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'PO has been approved successfully',
                        'data' => $finalPo
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Error updating PO:', [
                        'error' => $e->getMessage(),
                        'po_id' => $id,
                        'update_data' => $updateData
                    ]);
                    throw $e;
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to approve this PO or PO is already approved/rejected'
            ], 403);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in approve PO:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve PO: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectPo(Request $request, $id)
    {
        try {
            $user = auth()->user();
            $now = now();
            $notes = $request->input('notes');
            
            // Validasi notes (alasan penolakan wajib diisi)
            if (!$notes) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alasan penolakan (notes) wajib diisi'
                ], 400);
            }
            
            // Cari PO berdasarkan ID
            $po = MaintenancePurchaseOrder::findOrFail($id);
            
            // Log nilai request untuk debugging
            Log::info('PO Rejection Request untuk ID: ' . $id, [
                'input' => $request->all(),
                'user_id' => $user->id
            ]);
            
            // Tentukan level approval berdasarkan status PO saat ini dan role user
            $isSuperAdmin = $user->id_role == '5af56935b011a';
            $isSecretary = $user->id_jabatan == 217;
            
            // Tentukan level penolakan berdasarkan status PO saat ini
            $level = $request->input('rejection_level');
            if (!$level) {
                if (!$po->gm_finance_approval || $po->gm_finance_approval == 'PENDING') {
                    $level = 'GM Finance';
                } elseif ($po->gm_finance_approval == 'APPROVED' && 
                        (!$po->managing_director_approval || $po->managing_director_approval == 'PENDING')) {
                    $level = 'Managing Director';
                } elseif ($po->gm_finance_approval == 'APPROVED' && 
                        $po->managing_director_approval == 'APPROVED' && 
                        (!$po->president_director_approval || $po->president_director_approval == 'PENDING')) {
                    $level = 'President Director';
                }
                
                Log::info('Determined rejection level: ' . $level);
            }
            
            // Ambil data task dan outlet untuk notifikasi
            $task = DB::table('maintenance_tasks')->where('id', $po->task_id)->first();
            $outlet = DB::table('tbl_data_outlet')->where('id_outlet', $task->id_outlet)->first();
            $outletName = $outlet ? $outlet->nama_outlet : 'Unknown Outlet';
            $taskTitle = $task ? $task->title : 'Unknown Task';
            
            // Default update data
            $updateData = [];
            $hasPermission = false;
            
            // Level 1: GM Finance rejection
            if ($level == 'GM Finance') {
                if ($isSuperAdmin || $isSecretary || $user->id_jabatan == 152) {
                    $updateData = [
                        'gm_finance_approval' => 'REJECTED',
                        'gm_finance_approval_date' => $now,
                        'gm_finance_approval_by' => $user->id,
                        'gm_finance_approval_notes' => $notes,
                        'status' => 'REJECTED'
                    ];
                    $hasPermission = true;
                }
            }
            // Level 2: Managing Director rejection
            elseif ($level == 'Managing Director') {
                if ($isSuperAdmin || $isSecretary || $user->id_jabatan == 150) {
                    $updateData = [
                        'managing_director_approval' => 'REJECTED',
                        'managing_director_approval_date' => $now,
                        'managing_director_approval_by' => $user->id,
                        'managing_director_approval_notes' => $notes,
                        'status' => 'REJECTED'
                    ];
                    $hasPermission = true;
                }
            }
            // Level 3: President Director rejection
            elseif ($level == 'President Director') {
                if ($isSuperAdmin || $isSecretary || $user->id_jabatan == 149) {
                    $updateData = [
                        'president_director_approval' => 'REJECTED',
                        'president_director_approval_date' => $now,
                        'president_director_approval_by' => $user->id,
                        'president_director_approval_notes' => $notes,
                        'status' => 'REJECTED'
                    ];
                    $hasPermission = true;
                }
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'Level penolakan tidak valid'
                ], 400);
            }
            
            // Jika ada data yang perlu diupdate dan user memiliki izin
            if (!empty($updateData) && $hasPermission) {
                try {
                    // Update PO
                    DB::table('maintenance_purchase_orders')
                        ->where('id', $id)
                        ->update($updateData);
                    
                    // Log aktivitas
                    DB::table('maintenance_activity_logs')->insert([
                        'task_id' => $po->task_id,
                        'user_id' => $user->id,
                        'activity_type' => 'PO_REJECTED',
                        'description' => $user->nama_lengkap . ' rejected PO ' . $po->po_number . ' as ' . $level . '. Reason: ' . $notes,
                        'created_at' => now()
                    ]);
                    
                    // Siapkan penerima notifikasi tambahan berdasarkan level penolakan
                    $additionalRecipients = [168, 244]; // Purchasing Manager dan Purchasing Admin (selalu diberi tahu)
                    
                    // Tambahkan penerima lain berdasarkan level penolakan
                    if ($level == 'Managing Director' || $level == 'President Director') {
                        $additionalRecipients[] = 152; // GM Finance
                        $additionalRecipients[] = 217; // Sekretaris
                    }
                    
                    if ($level == 'President Director') {
                        $additionalRecipients[] = 150; // Managing Director
                    }
                    
                    // Kirim notifikasi ke semua user terkait task dan pihak yang perlu diberi tahu
                    NotificationService::sendTaskAndJobNotification(
                        $po->task_id,
                        'PO_REJECTED',
                        'PO ' . $po->po_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') telah ditolak oleh ' . $level . '. Alasan: ' . $notes,
                        $additionalRecipients
                    );
                    
                    // Kirim notifikasi khusus untuk Purchasing Manager dan Admin
                    NotificationService::sendNotificationToSpecificJobs(
                        $po->task_id,
                        'PO_NEEDS_REVISION',
                        'PO ' . $po->po_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') telah ditolak oleh ' . $level . ' dan memerlukan revisi. Alasan: ' . $notes,
                        [168, 244] // Purchasing Manager dan Purchasing Admin
                    );
                    
                    Log::info('PO successfully rejected', [
                        'po_id' => $po->id,
                        'po_number' => $po->po_number,
                        'level' => $level,
                        'rejected_by' => $user->id
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Purchase Order telah ditolak'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error updating PO for rejection:', [
                        'error' => $e->getMessage(),
                        'po_id' => $id,
                        'update_data' => $updateData
                    ]);
                    throw $e;
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menolak PO ini atau PO sudah disetujui/ditolak'
            ], 403);
            
        } catch (\Exception $e) {
            Log::error('Error in reject PO:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak Purchase Order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function previewPo($id)
    {
        $po = MaintenancePurchaseOrder::with([
            'creator.jabatan',
            'supplier',
            'items.unit',
            'gmFinanceApprover.jabatan',
            'managingDirectorApprover.jabatan',
            'presidentDirectorApprover.jabatan'
        ])->findOrFail($id);

        // Get approver roles
        $gmFinance = User::where('id_jabatan', 152)
                         ->where('status', 'A')
                         ->first();
        
        $managingDirector = User::where('id_jabatan', 150)
                               ->where('status', 'A')
                               ->first();
        
        $presidentDirector = User::where('id_jabatan', 149)
                                ->where('status', 'A')
                                ->first();

        return view('maintenance.purchase-order.preview', compact(
            'po',
            'gmFinance',
            'managingDirector',
            'presidentDirector'
        ));
    }

    public function uploadInvoice(Request $request, $id)
    {
        try {
            $po = MaintenancePurchaseOrder::findOrFail($id);
            
            $request->validate([
                'invoice_number' => 'required|string|max:255',
                'invoice_date' => 'required|date',
                'invoice_file' => 'required|image|max:2048' // max 2MB
            ]);

            // Store the file
            $path = $request->file('invoice_file')->store('invoices', 'public');

            // Update PO with invoice info
            $po->update([
                'invoice_number' => $request->invoice_number,
                'invoice_date' => $request->invoice_date,
                'invoice_file_path' => $path
            ]);

            // Log activity
            DB::table('maintenance_activity_logs')->insert([
                'task_id' => $po->task_id,
                'user_id' => auth()->id(),
                'activity_type' => 'INVOICE_UPLOADED',
                'description' => "Invoice {$request->invoice_number} uploaded for PO {$po->po_number}",
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice uploaded successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createGoodReceive(Request $request, $id)
    {
        try {
            $request->validate([
                'receive_date' => 'required|date',
                'photos' => 'required|array|min:1',
                'photos.*' => 'required|image|max:2048',
                'notes' => 'nullable|string'
            ]);

            $po = MaintenancePurchaseOrder::findOrFail($id);

            // Store photos
            $photoPaths = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('good-receives', 'public');
                $photoPaths[] = $path;
            }

            // Update PO
            $po->update([
                'receive_date' => $request->receive_date,
                'receive_photos' => json_encode($photoPaths), // Store as JSON array
                'receive_notes' => $request->notes,
                'status' => 'RECEIVED'
            ]);

            // Log activity
            DB::table('maintenance_activity_logs')->insert([
                'task_id' => $po->task_id,
                'user_id' => auth()->id(),
                'activity_type' => 'GOOD_RECEIVED',
                'description' => "Goods received for PO {$po->po_number}",
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Good receive has been created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create good receive: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveGoodReceive(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $po = MaintenancePurchaseOrder::findOrFail($id);

            // Validate input
            $request->validate([
                'receive_date' => 'required|date',
                'receive_notes' => 'nullable|string', // Sesuaikan nama field
                'photos' => 'required|array',
                'photos.*' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $photoPaths = [];

            // Process each photo
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $filename = 'good_receive_' . time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                    $path = $photo->storeAs('public/good_receive', $filename);
                    $photoPaths[] = str_replace('public/', 'storage/', $path);
                }
            }

            // Update PO
            $po->update([
                'receive_date' => $request->receive_date,
                'receive_notes' => $request->receive_notes, // Sesuaikan nama field
                'receive_photos' => json_encode($photoPaths),
                'status' => 'RECEIVED'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Good receive recorded successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in saveGoodReceive: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save good receive data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get PO statistics for a task
     */
    public function getPoStats($taskId)
    {
        $stats = [
            'total' => 0,
            'approved' => 0,
            'rejected' => 0,
            'received' => 0,
            'draft' => 0
        ];
        
        try {
            // Hitung statistik PO
            $stats['total'] = DB::table('maintenance_purchase_orders')
                ->where('task_id', $taskId)
                ->count();
                
            $stats['approved'] = DB::table('maintenance_purchase_orders')
                ->where('task_id', $taskId)
                ->where('status', 'APPROVED')
                ->count();
                
            $stats['rejected'] = DB::table('maintenance_purchase_orders')
                ->where('task_id', $taskId)
                ->where('status', 'REJECTED')
                ->count();
                
            $stats['received'] = DB::table('maintenance_purchase_orders')
                ->where('task_id', $taskId)
                ->where('status', 'RECEIVED')
                ->count();
                
            $stats['draft'] = DB::table('maintenance_purchase_orders')
                ->where('task_id', $taskId)
                ->whereIn('status', ['DRAFT', 'PENDING'])
                ->count();
        } catch (\Exception $e) {
            // Log error
            Log::error('Error fetching PO stats: ' . $e->getMessage());
        }
        
        return response()->json($stats);
    }

    public function uploadEvidence(Request $request)
    {
        try {
            \Log::info('===== EVIDENCE UPLOAD START =====');
            \Log::info('Request data:', $request->all());
            \Log::info('Files data:', $request->allFiles());
            \Log::info('Notes value: "' . $request->input('notes') . '"');
            
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|exists:maintenance_tasks,id',
                'notes' => 'nullable|string',
                'photos.*' => 'nullable|file|max:10240', // Multiple photos - 10MB max
                'videos.*' => 'nullable|file|max:51200', // Multiple videos - 50MB max
            ]);
            
            if ($validator->fails()) {
                \Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . $validator->errors()->first()
                ], 422);
            }
            
            \Log::info('Validation passed, proceeding with upload');
            
            // Buat record evidence baru untuk menampung foto/video
            DB::beginTransaction();
            
            try {
                // Ambil notes dari request, pastikan ada nilainya meskipun empty string
                $notes = $request->has('notes') ? $request->input('notes') : '';
                \Log::info('Notes yang akan disimpan: "' . $notes . '"');
                
                // Buat evidence parent
                $evidence = new \App\Models\MaintenanceEvidence([
                    'task_id' => $request->task_id,
                    'created_by' => auth()->id(),
                    'notes' => $notes
                ]);
                
                $evidence->save();
                \Log::info('Created evidence record with ID: ' . $evidence->id);
                
                $mediaCount = 0;
                
                // Simpan semua foto
                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $index => $photoFile) {
                        \Log::info("Processing photo #{$index}");
                        
                        $path = $photoFile->store('maintenance/evidence/photos', 'public');
                        $evidencePhoto = new \App\Models\MaintenanceEvidencePhoto([
                            'evidence_id' => $evidence->id,
                            'path' => $path,
                            'file_name' => $photoFile->getClientOriginalName(),
                            'file_type' => $photoFile->getMimeType(),
                            'file_size' => $photoFile->getSize()
                        ]);
                        
                        $evidencePhoto->save();
                        \Log::info("Saved photo #{$index} with ID: " . $evidencePhoto->id);
                        $mediaCount++;
                    }
                }
                
                // Simpan semua video
                if ($request->hasFile('videos')) {
                    foreach ($request->file('videos') as $index => $videoFile) {
                        \Log::info("Processing video #{$index}, size: " . $videoFile->getSize());
                        
                        $path = $videoFile->store('maintenance/evidence/videos', 'public');
                        $evidenceVideo = new \App\Models\MaintenanceEvidenceVideo([
                            'evidence_id' => $evidence->id,
                            'path' => $path,
                            'file_name' => $videoFile->getClientOriginalName(),
                            'file_type' => $videoFile->getMimeType(),
                            'file_size' => $videoFile->getSize()
                        ]);
                        
                        $evidenceVideo->save();
                        \Log::info("Saved video #{$index} with ID: " . $evidenceVideo->id);
                        $mediaCount++;
                    }
                }
                
                // Jika tidak ada media yang disimpan, batalkan transaksi
                if ($mediaCount === 0) {
                    DB::rollBack();
                    \Log::warning('No media saved, transaction rolled back');
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada foto atau video yang diupload.'
                    ], 400);
                }
                
                DB::commit();
                \Log::info("Evidence upload successful - {$mediaCount} media saved");
                \Log::info("Notes saved: \"" . $evidence->notes . "\"");
                
                // Ambil data task
                $task = DB::table('maintenance_tasks')->where('id', $request->task_id)->first();
                
                if ($task) {
                    // Ambil data outlet
                    $outlet = DB::table('tbl_data_outlet')->where('id_outlet', $task->id_outlet)->first();
                    $outletName = $outlet ? $outlet->nama_outlet : 'Unknown Outlet';
                    
                    // Ambil nama pengguna yang mengupload evidence
                    $user = auth()->user();
                    $userName = $user ? $user->nama_lengkap : 'Unknown User';
                    
                    // Buat pesan notifikasi
                    $notificationMessage = "Evidence baru telah ditambahkan pada task #{$task->task_number}: {$task->title} (Outlet: {$outletName}) oleh {$userName}";
                    
                    // Kirim notifikasi ke semua member task dan admin maintenance
                    NotificationService::sendTaskNotification(
                        $request->task_id,
                        'EVIDENCE_ADDED',
                        $notificationMessage
                    );
                    
                    // Kirim notifikasi ke admin/supervisor maintenance dan jabatan lainnya
                    NotificationService::sendNotificationToSpecificJobs(
                        $request->task_id,
                        'EVIDENCE_ADDED',
                        $notificationMessage,
                        [209, 165, 262]  // ID jabatan: 209 = Maintenance Supervisor, 165 = Admin, 262 = Manager
                    );
                    
                    // Tambahkan entry ke maintenance_activity_logs
                    DB::table('maintenance_activity_logs')->insert([
                        'task_id' => $request->task_id,
                        'user_id' => auth()->id(),
                        'activity_type' => 'EVIDENCE_ADDED',
                        'description' => "Evidence ditambahkan oleh " . $userName . " ({$mediaCount} media)",
                        'created_at' => now()
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => "Evidence berhasil disimpan ({$mediaCount} media)",
                    'evidence_id' => $evidence->id
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error("Database transaction error: " . $e->getMessage());
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Upload Evidence Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan evidence: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan evidence untuk suatu task 
     */
    public function getTaskEvidence($taskId)
    {
        try {
            \Log::info('Fetching evidence for task ID: ' . $taskId);
            
            $evidence = \App\Models\MaintenanceEvidence::where('task_id', $taskId)
                ->with(['photos', 'videos', 'creator'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            foreach ($evidence as $item) {
                // Format path untuk foto
                foreach ($item->photos as $photo) {
                    $photo->full_url = asset('storage/' . $photo->path);
                }
                
                // Format path untuk video
                foreach ($item->videos as $video) {
                    $video->full_url = asset('storage/' . $video->path);
                }
                
                // Format waktu
                $item->created_at_formatted = $item->created_at->format('d M Y H:i');
                
                // Format nama creator
                if ($item->creator) {
                    $item->creator_name = $item->creator->name;
                }
            }
            
            \Log::info('Found ' . count($evidence) . ' evidence records for task ID: ' . $taskId);
            
            return response()->json([
                'success' => true,
                'data' => $evidence
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching evidence: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data evidence: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkDoneRequirements(Request $request)
    {
        $taskId = $request->input('taskId');
        
        try {
            // Tambahkan log untuk debugging
            \Log::info('Checking done requirements for task', [
                'task_id' => $taskId,
                'user_id' => auth()->id(),
                'user_role' => auth()->user()->id_role,
                'user_division' => auth()->user()->division_id,
                'user_status' => auth()->user()->status
            ]);
            
            // Ambil data task
            $task = DB::table('maintenance_tasks')
                ->where('id', $taskId)
                ->first();
                
            if (!$task) {
                return response()->json([
                    'canMove' => false,
                    'message' => 'Task tidak ditemukan dalam database.'
                ]);
            }
            
            // Ambil user data untuk logging dan validasi
            $user = auth()->user();
            
            // Cek apakah user adalah superadmin (id_role=5af56935b011a) atau divisi maintenance (division_id=20)
            $isSuperAdmin = $user->id_role === '5af56935b011a' && $user->status === 'A';
            $isMaintenanceDiv = $user->division_id == 20 && $user->status === 'A';
            
            // Jika user tidak memiliki akses
            if (!$isSuperAdmin && !$isMaintenanceDiv) {
                $message = '';
                
                if ($user->status !== 'A') {
                    $message = 'Status user Anda tidak aktif. Hanya user dengan status aktif yang dapat memindahkan task ke board Done.';
                } else {
                    $message = 'Anda tidak memiliki akses yang diperlukan. Hanya Superadmin dan anggota Divisi Maintenance yang dapat memindahkan task ke board Done.';
                }
                
                // Log untuk debugging
                \Log::warning('User mencoba memindahkan task ke Done tanpa akses yang sesuai', [
                    'task_id' => $taskId,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'division_id' => $user->division_id,
                    'role_id' => $user->id_role,
                    'status' => $user->status
                ]);
                
                return response()->json([
                    'canMove' => false,
                    'message' => $message
                ]);
            }
            
            // Cek apakah task memiliki evidence - PERBAIKI NAMA TABEL DISINI
            $evidenceCount = DB::table('maintenance_evidence') // Ubah dari 'maintenance_evidences' ke 'maintenance_evidence'
                ->where('task_id', $taskId)
                ->count();
                
            if ($evidenceCount == 0) {
                return response()->json([
                    'canMove' => false,
                    'message' => 'Task belum memiliki evidence. Untuk memindahkan task ke board Done, Anda harus menambahkan setidaknya satu evidence (foto/video).'
                ]);
            }
            
            // Semua persyaratan terpenuhi
            return response()->json([
                'canMove' => true,
                'message' => 'Semua persyaratan terpenuhi. Task dapat dipindahkan ke board Done.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error saat memeriksa persyaratan untuk board Done:', [
                'task_id' => $taskId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'canMove' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
} 
