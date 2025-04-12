<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\NotificationService;
use App\Models\User;
use App\Models\Notification;
use PDF;
use Illuminate\Support\Facades\File;

class MaintenancePurchaseRequisitionController extends Controller
{
    /**
     * Get Purchase Requisitions for a task
     */
    public function getTaskPrs($taskId)
    {
        try {
            // Get the task
            $task = DB::table('maintenance_tasks')
                ->where('id', $taskId)
                ->first();

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found'
                ], 404);
            }

            // Get PRs for the task
            $prs = DB::table('maintenance_purchase_requisitions as pr')
                ->leftJoin('users as u', 'pr.created_by', '=', 'u.id')
                ->where('pr.task_id', $taskId)
                ->select('pr.*', 'u.nama_lengkap as creator_name')
                ->orderBy('pr.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $prs,
                'task' => $task
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting task PRs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get PRs'
            ], 500);
        }
    }

    /**
     * Get Purchase Requisition detail
     */
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

    /**
     * Store a new Purchase Requisition
     */
    public function store(Request $request)
    {
        try {
            // Log the request for debugging
            Log::info('PR Store Request:', $request->all());

            // Validate input
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|exists:maintenance_tasks,id',
                'pr_number' => 'required|unique:maintenance_purchase_requisitions,pr_number',
                'notes' => 'nullable|string',
                'total_amount' => 'required|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.item_name' => 'required|string',
                'items.*.description' => 'nullable|string',
                'items.*.specifications' => 'nullable|string',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.unit_id' => 'required|exists:units,id',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.subtotal' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Begin transaction
            return DB::transaction(function () use ($request) {
                // Create PR
                $prId = DB::table('maintenance_purchase_requisitions')->insertGetId([
                    'pr_number' => $request->pr_number,
                    'task_id' => $request->task_id,
                    'created_by' => auth()->id(),
                    'status' => 'DRAFT',
                    'notes' => $request->notes,
                    'total_amount' => $request->total_amount,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Create PR items
                $items = [];
                foreach ($request->items as $item) {
                    $items[] = [
                        'pr_id' => $prId,
                        'item_name' => $item['item_name'],
                        'description' => $item['description'] ?? null,
                        'specifications' => $item['specifications'] ?? null,
                        'quantity' => $item['quantity'],
                        'unit_id' => $item['unit_id'],
                        'price' => $item['price'],
                        'subtotal' => $item['subtotal'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                DB::table('maintenance_purchase_requisition_items')->insert($items);

                // Log activity
                DB::table('maintenance_activity_logs')->insert([
                    'task_id' => $request->task_id,
                    'user_id' => auth()->id(),
                    'activity_type' => 'PR_CREATED',
                    'description' => 'Created PR: ' . $request->pr_number,
                    'created_at' => now()
                ]);
                
                // Dapatkan informasi task dan outlet untuk notifikasi
                $task = DB::table('maintenance_tasks')->where('id', $request->task_id)->first();
                $outlet = DB::table('tbl_data_outlet')->where('id_outlet', $task->id_outlet)->first();
                $outletName = $outlet ? $outlet->nama_outlet : 'Unknown Outlet';
                $taskTitle = $task ? $task->title : 'Unknown Task';
                
                // Kirim notifikasi ke semua user terkait task
                NotificationService::sendTaskNotification(
                    $request->task_id,
                    'PR_CREATED',
                    'PR baru ' . $request->pr_number . ' telah dibuat untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ')'
                );
                
                // Kirim notifikasi ke Chief Engineering, Maintenance Admin dan Maintenance SPV
                $approverRoles = [165, 263, 209]; // Chief Engineering, Maintenance Admin, Maintenance SPV
                NotificationService::sendNotificationToSpecificJobs(
                    $request->task_id,
                    'PR_NEEDS_APPROVAL',
                    'PR baru ' . $request->pr_number . ' untuk task ' . $taskTitle . ' (Outlet: ' . $outletName . ') memerlukan persetujuan Anda',
                    $approverRoles
                );

                return response()->json([
                    'success' => true,
                    'message' => 'PR created successfully',
                    'pr_id' => $prId
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Error creating PR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create PR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user info for approval purposes
     */
    public function getUserInfo()
    {
        try {
            $user = auth()->user();
            
            // Log semua properties user untuk troubleshooting
            \Log::info('All user properties:', [
                'user' => $user->toArray()
            ]);
            
            // Pastikan variable name sesuai dengan yang ada di database
            // Default variable yang mungkin ada
            $idJabatan = $user->id_jabatan ?? null;
            $idRole = $user->id_role ?? null;
            
            // Log semua kemungkinan nama field
            \Log::info('Possible field names:', [
                'id_jabatan' => $user->id_jabatan ?? 'not found',
                'jabatan_id' => $user->jabatan_id ?? 'not found',
                'job_id' => $user->job_id ?? 'not found',
                'id_role' => $user->id_role ?? 'not found',
                'role_id' => $user->role_id ?? 'not found',
                'role' => $user->role ?? 'not found',
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id_jabatan' => $idJabatan,
                    'id_role' => $idRole,
                    // Tambahkan semua kemungkinan field sebagai fallback
                    'jabatan_id' => $user->jabatan_id ?? null,
                    'job_id' => $user->job_id ?? null,
                    'role_id' => $user->role_id ?? null,
                    'role' => $user->role ?? null,
                    'user_id' => $user->id
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getUserInfo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting user info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a PR
     */
    public function approve($id)
    {
        try {
            $pr = MaintenancePurchaseRequisition::findOrFail($id);
            $user = auth()->user();
            $now = now();
            
            // Tentukan level approval berdasarkan status PR saat ini dan role user
            $isSuperAdmin = $user->id_role == '5af56935b011a';
            $isSecretary = $user->id_jabatan == 217;
            
            // Default update data
            $updateData = [];
            
            // Chief Engineering approval (first level)
            if (!$pr->chief_engineering_approval || $pr->chief_engineering_approval == 'PENDING') {
                if ($isSuperAdmin || $isSecretary || $user->id_jabatan == 165) {
                    $updateData['chief_engineering_approval'] = 'APPROVED';
                    $updateData['chief_engineering_approval_date'] = $now;
                    $updateData['chief_engineering_approval_by'] = $user->id;
                }
            }
            // Purchasing Manager approval (second level)
            elseif ($pr->chief_engineering_approval == 'APPROVED' && 
                   (!$pr->purchasing_manager_approval || $pr->purchasing_manager_approval == 'PENDING')) {
                if ($isSuperAdmin || $isSecretary || $user->id_jabatan == 168) {
                    $updateData['purchasing_manager_approval'] = 'APPROVED';
                    $updateData['purchasing_manager_approval_date'] = $now;
                    $updateData['purchasing_manager_approval_by'] = $user->id;
                }
            }
            // COO approval (third level)
            elseif ($pr->chief_engineering_approval == 'APPROVED' && 
                    $pr->purchasing_manager_approval == 'APPROVED' && 
                    (!$pr->coo_approval || $pr->coo_approval == 'PENDING')) {
                if ($isSuperAdmin || $isSecretary || $user->id_jabatan == 151) {
                    $updateData['coo_approval'] = 'APPROVED';
                    $updateData['coo_approval_date'] = $now;
                    $updateData['coo_approval_by'] = $user->id;
                    
                    // Jika semua level sudah approved, update status PR menjadi APPROVED
                    $updateData['status'] = 'APPROVED';
                }
            }
            
            // Jika ada data yang perlu diupdate
            if (!empty($updateData)) {
                $pr->update($updateData);
                
                // Kirim notifikasi ke user terkait
                $this->sendApprovalNotification($pr, $user, 'approved');
                
                return response()->json([
                    'success' => true,
                    'message' => 'PR has been approved successfully',
                    'data' => $pr
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to approve this PR'
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve PR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a PR
     */
    public function reject(Request $request, $id)
    {
        try {
            $pr = MaintenancePurchaseRequisition::findOrFail($id);
            $user = auth()->user();
            $now = now();
            
            // Validasi notes
            $request->validate([
                'notes' => 'required|string|max:1000'
            ]);
            
            // Tentukan level approval berdasarkan status PR saat ini dan role user
            $isSuperAdmin = $user->id_role == '5af56935b011a';
            $isSecretary = $user->id_jabatan == 217;
            
            // Default update data
            $updateData = [
                'rejection_notes' => $request->notes,
                'status' => 'REJECTED'
            ];
            
            // Chief Engineering rejection (first level)
            if (!$pr->chief_engineering_approval || $pr->chief_engineering_approval == 'PENDING') {
                if ($isSuperAdmin || $isSecretary || $user->id_jabatan == 165) {
                    $updateData['chief_engineering_approval'] = 'REJECTED';
                    $updateData['chief_engineering_approval_date'] = $now;
                    $updateData['chief_engineering_approval_by'] = $user->id;
                }
            }
            // Purchasing Manager rejection (second level)
            elseif ($pr->chief_engineering_approval == 'APPROVED' && 
                   (!$pr->purchasing_manager_approval || $pr->purchasing_manager_approval == 'PENDING')) {
                if ($isSuperAdmin || $isSecretary || $user->id_jabatan == 168) {
                    $updateData['purchasing_manager_approval'] = 'REJECTED';
                    $updateData['purchasing_manager_approval_date'] = $now;
                    $updateData['purchasing_manager_approval_by'] = $user->id;
                }
            }
            // COO rejection (third level)
            elseif ($pr->chief_engineering_approval == 'APPROVED' && 
                    $pr->purchasing_manager_approval == 'APPROVED' && 
                    (!$pr->coo_approval || $pr->coo_approval == 'PENDING')) {
                if ($isSuperAdmin || $isSecretary || $user->id_jabatan == 151) {
                    $updateData['coo_approval'] = 'REJECTED';
                    $updateData['coo_approval_date'] = $now;
                    $updateData['coo_approval_by'] = $user->id;
                }
            }
            
            // Jika ada data yang perlu diupdate
            if (count($updateData) > 2) { // lebih dari rejection_notes dan status
                $pr->update($updateData);
                
                // Kirim notifikasi ke user terkait
                $this->sendApprovalNotification($pr, $user, 'rejected');
                
                return response()->json([
                    'success' => true,
                    'message' => 'PR has been rejected',
                    'data' => $pr
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to reject this PR'
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject PR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification for PR approval/rejection
     */
    private function sendApprovalNotification($pr, $user, $action)
    {
        try {
            // Tentukan recipient (creator PR)
            $recipient = User::find($pr->created_by);
            
            if (!$recipient) {
                return;
            }
            
            // Buat pesan notifikasi
            $actionText = $action == 'approved' ? 'approved' : 'rejected';
            $message = "PR #{$pr->pr_number} has been {$actionText} by {$user->nama_lengkap}";
            
            if ($action == 'rejected' && !empty($pr->rejection_notes)) {
                $message .= ". Reason: {$pr->rejection_notes}";
            }
            
            // Kirim notifikasi
            Notification::create([
                'user_id' => $recipient->id,
                'message' => $message,
                'type' => $action == 'approved' ? 'success' : 'danger',
                'link' => route('maintenance.pr.detail', $pr->id),
                'is_read' => 0
            ]);
            
            // Jika PR diapprove oleh level tertentu, kirim notifikasi ke level berikutnya
            if ($action == 'approved') {
                // Jika Chief Engineering approve, kirim notifikasi ke Purchasing Manager
                if ($pr->chief_engineering_approval == 'APPROVED' && 
                    (!$pr->purchasing_manager_approval || $pr->purchasing_manager_approval == 'PENDING')) {
                    // Cari user dengan jabatan Purchasing Manager
                    $purchasingManagers = User::where('id_jabatan', 168)->get();
                    
                    foreach ($purchasingManagers as $manager) {
                        Notification::create([
                            'user_id' => $manager->id,
                            'message' => "PR #{$pr->pr_number} needs your approval",
                            'type' => 'info',
                            'link' => route('maintenance.pr.detail', $pr->id),
                            'is_read' => 0
                        ]);
                    }
                }
                
                // Jika Purchasing Manager approve, kirim notifikasi ke COO
                if ($pr->purchasing_manager_approval == 'APPROVED' && 
                    (!$pr->coo_approval || $pr->coo_approval == 'PENDING')) {
                    // Cari user dengan jabatan COO
                    $coos = User::where('id_jabatan', 151)->get();
                    
                    foreach ($coos as $coo) {
                        Notification::create([
                            'user_id' => $coo->id,
                            'message' => "PR #{$pr->pr_number} needs your approval",
                            'type' => 'info',
                            'link' => route('maintenance.pr.detail', $pr->id),
                            'is_read' => 0
                        ]);
                    }
                }
                
                // Jika semua level sudah approved
                if ($pr->chief_engineering_approval == 'APPROVED' && 
                    $pr->purchasing_manager_approval == 'APPROVED' && 
                    $pr->coo_approval == 'APPROVED') {
                    // Notifikasi ke creator bahwa PR fully approved
                    Notification::create([
                        'user_id' => $recipient->id,
                        'message' => "PR #{$pr->pr_number} has been fully approved",
                        'type' => 'success',
                        'link' => route('maintenance.pr.detail', $pr->id),
                        'is_read' => 0
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send PR approval notification: ' . $e->getMessage());
        }
    }

    /**
     * Get PR detail with approval information
     */
    public function getDetail($id)
    {
        try {
            $pr = MaintenancePurchaseRequisition::with(['items.unit', 'creator', 'task'])->findOrFail($id);
            
            // Format data
            $formattedPr = [
                'id' => $pr->id,
                'pr_number' => $pr->pr_number,
                'task_number' => $pr->task->task_number,
                'task_id' => $pr->task_id,
                'total_amount' => $pr->total_amount,
                'status' => $pr->status,
                'notes' => $pr->notes,
                'created_at' => $pr->created_at,
                'creator_name' => $pr->creator->name,
                'items' => [],
                
                // Approval information
                'chief_engineering_approval' => $pr->chief_engineering_approval,
                'chief_engineering_approval_date' => $pr->chief_engineering_approval_date,
                'chief_engineering_approval_by' => $pr->chief_engineering_approval_by,
                'purchasing_manager_approval' => $pr->purchasing_manager_approval,
                'purchasing_manager_approval_date' => $pr->purchasing_manager_approval_date,
                'purchasing_manager_approval_by' => $pr->purchasing_manager_approval_by,
                'coo_approval' => $pr->coo_approval,
                'coo_approval_date' => $pr->coo_approval_date,
                'coo_approval_by' => $pr->coo_approval_by,
                'rejection_notes' => $pr->rejection_notes
            ];
            
            // Format items
            foreach ($pr->items as $item) {
                $formattedPr['items'][] = [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'description' => $item->description,
                    'specifications' => $item->specifications,
                    'quantity' => $item->quantity,
                    'unit_id' => $item->unit_id,
                    'unit_name' => $item->unit->name,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $formattedPr
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load PR detail: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get PR statistics for a task
     */
    public function getTaskPrStats($taskId)
    {
        try {
            // Hitung statistik PR berdasarkan status
            $stats = [
                'total' => DB::table('maintenance_purchase_requisitions')
                    ->where('task_id', $taskId)
                    ->count(),
                'approved' => DB::table('maintenance_purchase_requisitions')
                    ->where('task_id', $taskId)
                    ->where('status', 'APPROVED')
                    ->count(),
                'rejected' => DB::table('maintenance_purchase_requisitions')
                    ->where('task_id', $taskId)
                    ->where('status', 'REJECTED')
                    ->count(),
                'draft' => DB::table('maintenance_purchase_requisitions')
                    ->where('task_id', $taskId)
                    ->whereIn('status', ['DRAFT', 'SUBMITTED', 'PENDING'])
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting PR stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get PR stats'
            ], 500);
        }
    }

    /**
     * Generate PDF for a Purchase Requisition
     */
    public function downloadPdf($id)
    {
        try {
            // Load PR data with relationships
            $pr = \App\Models\MaintenancePurchaseRequisition::with([
                'items.unit',
                'creator',
                'task',
                'chiefEngineeringApprover',
                'purchasingManagerApprover',
                'cooApprover'
            ])->findOrFail($id);
            
            // Check if logo exists
            $logoPath = public_path('build/images/logo/LOGO_JUSTUS_GROUP_1024X500.jpg');
            if (!File::exists($logoPath)) {
                // Create directory if it doesn't exist
                File::ensureDirectoryExists(public_path('build/images/logo'));
                
                // If logo doesn't exist, use a default logo
                if (File::exists(public_path('build/images/logojustusgroup.png'))) {
                    // Copy existing logo as fallback
                    File::copy(
                        public_path('build/images/logojustusgroup.png'),
                        public_path('build/images/logo/LOGO_JUSTUS_GROUP_1024X500.jpg')
                    );
                }
            }
            
            // Set paper size dan orientation dengan opsi yang lebih sederhana
            $pdf = PDF::loadView('maintenance.purchase-requisition.pdf', compact('pr'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'dpi' => 100,
                    'defaultFont' => 'sans-serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);
            
            // Log activity
            \Illuminate\Support\Facades\Log::info('User downloaded PR PDF', [
                'user_id' => auth()->id(),
                'pr_id' => $id,
                'pr_number' => $pr->pr_number
            ]);
            
            // Coba gunakan streaming daripada download
            return $pdf->stream('PR_' . $pr->pr_number . '_' . date('Ymd_His') . '.pdf');
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error generating PR PDF: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview Purchase Requisition sebagai HTML
     */
    public function previewPr($id)
    {
        try {
            // Load PR data with relationships
            $pr = \App\Models\MaintenancePurchaseRequisition::with([
                'items.unit',
                'creator.jabatan',
                'task',
                'chiefEngineeringApprover.jabatan',
                'purchasingManagerApprover.jabatan',
                'cooApprover.jabatan'
            ])->findOrFail($id);
            
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
            
            return view('maintenance.purchase-requisition.preview', compact(
                'pr', 
                'chiefEngineering', 
                'purchasingManager', 
                'coo'
            ));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error generating PR preview: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Gagal membuat preview PR: ' . $e->getMessage());
        }
    }
}
