<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\MaintenanceTask;
use App\Models\MaintenanceEvidence;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class MaintenanceEvidenceController extends Controller
{
    /**
     * Cek akses user untuk fitur evidence
     */
    public function checkAccess(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Log untuk debugging
            Log::info('Checking evidence access for user', [
                'user_id' => $user->id,
                'name' => $user->name,
                'division_id' => $user->division_id,
                'id_role' => $user->id_role,
                'status' => $user->status
            ]);
            
            // Cek apakah user adalah superadmin (id_role = 5af56935b011a dan status = A)
            $isSuperadmin = ($user->id_role === '5af56935b011a' && $user->status === 'A');
            
            // Cek apakah user adalah dari Maintenance Division (division_id = 20)
            $isMaintenanceDivision = ($user->division_id == 20);
            
            if ($isSuperadmin || $isMaintenanceDivision) {
                return response()->json([
                    'success' => true,
                    'message' => 'User memiliki akses untuk menambahkan evidence'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menambahkan evidence. Hanya Maintenance Division (division_id = 20) atau Superadmin yang dapat melakukan ini.'
                ], 403);
            }
        } catch (\Exception $e) {
            Log::error('Error checking evidence access: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memeriksa akses: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Simpan evidence untuk task
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Cek akses user
            $isSuperadmin = ($user->id_role === '5af56935b011a' && $user->status === 'A');
            $isMaintenanceDivision = ($user->division_id == 20);
            
            if (!$isSuperadmin && !$isMaintenanceDivision) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menambahkan evidence. Hanya Maintenance Division (division_id = 20) atau Superadmin yang dapat melakukan ini.'
                ], 403);
            }
            
            // Validasi input
            $request->validate([
                'task_id' => 'required|exists:maintenance_tasks,id',
                'notes' => 'nullable|string|max:1000',
                'photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // max 5MB
                'videos.*' => 'nullable|mimes:mp4,webm|max:51200', // max 50MB
            ]);
            
            $taskId = $request->task_id;
            $notes = $request->notes;
            
            // Cek apakah task ada dan statusnya IN_REVIEW
            $task = MaintenanceTask::findOrFail($taskId);
            if ($task->status !== 'IN_REVIEW') {
                return response()->json([
                    'success' => false,
                    'message' => 'Task harus dalam status IN_REVIEW untuk menambahkan evidence.'
                ], 400);
            }
            
            // Simpan evidence
            $evidence = new MaintenanceEvidence();
            $evidence->task_id = $taskId;
            $evidence->created_by = $user->id;
            $evidence->notes = $notes;
            $evidence->save();
            
            // Simpan foto
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $photo) {
                    $path = $photo->store('maintenance/evidence/photos', 'public');
                    
                    $evidence->photos()->create([
                        'path' => $path,
                        'file_name' => $photo->getClientOriginalName(),
                        'file_type' => 'photo',
                        'file_size' => $photo->getSize(),
                    ]);
                }
            }
            
            // Simpan video
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $index => $video) {
                    $path = $video->store('maintenance/evidence/videos', 'public');
                    
                    $evidence->videos()->create([
                        'path' => $path,
                        'file_name' => $video->getClientOriginalName(),
                        'file_type' => 'video',
                        'file_size' => $video->getSize(),
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Evidence berhasil disimpan',
                'data' => $evidence
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving evidence: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan evidence: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Tampilkan evidence untuk task
     */
    public function show($taskId)
    {
        try {
            $evidence = MaintenanceEvidence::where('task_id', $taskId)
                ->with(['photos', 'videos', 'creator'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => $evidence
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching evidence: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data evidence: ' . $e->getMessage()
            ], 500);
        }
    }
} 