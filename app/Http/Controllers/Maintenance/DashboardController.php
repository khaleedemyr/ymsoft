<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceTask;
use App\Models\MaintenancePurchaseOrder;
use App\Models\MaintenancePurchaseRequisition;
use App\Models\MaintenanceEvidence;
use App\Models\MaintenanceEvidencePhoto;
use App\Models\MaintenanceEvidenceVideo;
use App\Models\MaintenanceActivityLog;
use App\Models\MaintenanceDocument;
use App\Models\MaintenanceLabel;
use App\Models\MaintenancePriority;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TasksByMemberExport;
use App\Exports\TasksByPriorityExport;
use App\Exports\MaintenanceActivitiesExport;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard maintenance
     */
    public function index(Request $request)
    {
        // Default periode: 30 hari
        $period = $request->input('period', '30d');
        
        // Tentukan tanggal awal berdasarkan periode
        $endDate = Carbon::now();
        $startDate = null;
        
        switch ($period) {
            case '1d':
                $startDate = Carbon::now()->startOfDay();
                break;
            case '7d':
                $startDate = Carbon::now()->subDays(7)->startOfDay();
                break;
            case '30d':
                $startDate = Carbon::now()->subDays(30)->startOfDay();
                break;
            case '90d':
                $startDate = Carbon::now()->subDays(90)->startOfDay();
                break;
            case '1y':
                $startDate = Carbon::now()->subYear()->startOfDay();
                break;
            default:
                $period = '30d'; // Default jika parameter tidak valid
                $startDate = Carbon::now()->subDays(30)->startOfDay();
        }
        
        // Statistik utama dengan filter periode
        $totalTasks = MaintenanceTask::whereBetween('created_at', [$startDate, $endDate])->count();
        $inProgressTasks = MaintenanceTask::whereIn('status', ['IN_PROGRESS', 'PR', 'PO'])
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->orWhereBetween('updated_at', [$startDate, $endDate]);
            })
            ->count();
        $completedTasks = MaintenanceTask::where('status', 'DONE')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->count();
        $totalPurchaseOrders = MaintenancePurchaseOrder::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // Interval waktu yang akan digunakan
        $lastMonth = Carbon::now()->subMonth();
        $lastWeek = Carbon::now()->subWeek();
        
        // Total tasks perbandingan bulan lalu (tidak berubah)
        $lastMonthTotalTasks = MaintenanceTask::where('created_at', '<', $lastMonth)->count();
        $taskIncreasePercent = $lastMonthTotalTasks > 0 
            ? round((($totalTasks - $lastMonthTotalTasks) / $lastMonthTotalTasks) * 100, 2) 
            : 0;
        
        // Gunakan maintenance_activity_logs untuk memeriksa perubahan status
        // Cari tasks yang berubah status menjadi IN_PROGRESS, PR, atau PO dalam seminggu terakhir
        $lastWeekStatusChanges = DB::table('maintenance_activity_logs')
            ->where('created_at', '>=', $lastWeek)
            ->where('activity_type', 'STATUS_CHANGED')
            ->whereIn('new_value', ['IN_PROGRESS', 'PR', 'PO'])
            ->distinct('task_id')
            ->count('task_id');
        
        // Hitung tasks in progress seminggu lalu dengan mengurangi perubahan status terbaru
        $estimatedLastWeekInProgress = max(0, $inProgressTasks - $lastWeekStatusChanges);
        
        // Alternatif jika pendekatan di atas tidak mencerminkan data historis dengan baik:
        // Kita bisa menghitung jumlah tugas yang dibuat sebelum minggu lalu dan saat ini masih dalam progress
        $createdBeforeLastWeekInProgress = MaintenanceTask::whereIn('status', ['IN_PROGRESS', 'PR', 'PO'])
            ->where('created_at', '<', $lastWeek)
            ->count();
        
        // Gunakan nilai yang lebih dapat diandalkan (pilih salah satu)
        $lastWeekInProgress = $estimatedLastWeekInProgress; 
        // atau $lastWeekInProgress = $createdBeforeLastWeekInProgress;
        
        // Hitung persentase perubahan
        if ($lastWeekInProgress > 0) {
            // Ada data historis, hitung persentase perubahan
            $inProgressIncreasePercent = round((($inProgressTasks - $lastWeekInProgress) / $lastWeekInProgress) * 100, 2);
        } else if ($inProgressTasks > 0) {
            // Tidak ada data historis, tapi ada task in progress sekarang
            // Opsi 1: Tampilkan sebagai nilai baru
            $inProgressIncreasePercent = "New";
            // Opsi 2: Atau tetap tampilkan sebagai 100% tapi dengan ikon berbeda
            // $inProgressIncreasePercent = 100;
        } else {
            // Tidak ada data historis dan tidak ada task in progress sekarang
            $inProgressIncreasePercent = 0;
        }
        
        // Tentukan class & icon
        if ($inProgressIncreasePercent === "New") {
            $inProgressIncreaseClass = 'info';
            $inProgressIncreaseIcon = 'plus';
        } else {
            $inProgressIncreaseClass = $inProgressIncreasePercent > 0 ? 'success' : ($inProgressIncreasePercent < 0 ? 'danger' : 'secondary');
            $inProgressIncreaseIcon = $inProgressIncreasePercent > 0 ? 'up' : ($inProgressIncreasePercent < 0 ? 'down' : 'dash');
        }
        
        // Total tasks perbandingan bulan lalu
        $lastMonthCompleted = MaintenanceTask::where('status', 'DONE')
            ->where('completed_at', '<', $lastMonth)
            ->count();
        $completedIncreasePercent = $lastMonthCompleted > 0 
            ? round((($completedTasks - $lastMonthCompleted) / $lastMonthCompleted) * 100, 2) 
            : 0;
        
        // Total purchase orders perbandingan bulan lalu
        $lastMonthPO = MaintenancePurchaseOrder::where('created_at', '<', $lastMonth)->count();
        $poIncreasePercent = $lastMonthPO > 0 
            ? round((($totalPurchaseOrders - $lastMonthPO) / $lastMonthPO) * 100, 2) 
            : 0;
        
        // Tentukan class untuk warna indikator kenaikan/penurunan
        $poIncreaseClass = $poIncreasePercent >= 0 ? 'success' : 'danger';
        $poIncreaseIcon = $poIncreasePercent >= 0 ? 'up' : 'down';
        
        // Data untuk task status chart dengan filter periode
        $taskStatusData = [
            'todo' => MaintenanceTask::where('status', 'TASK')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'inProgress' => MaintenanceTask::where('status', 'IN_PROGRESS')
                ->where(function($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                          ->orWhereBetween('updated_at', [$startDate, $endDate]);
                })
                ->count(),
            'pr' => MaintenanceTask::where('status', 'PR')
                ->where(function($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                          ->orWhereBetween('updated_at', [$startDate, $endDate]);
                })
                ->count(),
            'po' => MaintenanceTask::where('status', 'PO')
                ->where(function($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                          ->orWhereBetween('updated_at', [$startDate, $endDate]);
                })
                ->count(),
            'inReview' => MaintenanceTask::where('status', 'IN_REVIEW')
                ->where(function($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                          ->orWhereBetween('updated_at', [$startDate, $endDate]);
                })
                ->count(),
            'done' => MaintenanceTask::where('status', 'DONE')
                ->whereBetween('completed_at', [$startDate, $endDate])
                ->count(),
        ];
        
        // Query untuk kategori maintenance dengan filter periode
        $categoryData = DB::table('maintenance_tasks')
            ->leftJoin('maintenance_labels', 'maintenance_tasks.label_id', '=', 'maintenance_labels.id')
            ->whereBetween('maintenance_tasks.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('COALESCE(maintenance_labels.name, "Tanpa Label") as name'),
                DB::raw('COALESCE(maintenance_labels.color, "#3498db") as color'),
                DB::raw('count(*) as total')
            )
            ->groupBy('maintenance_labels.id', 'maintenance_labels.name', 'maintenance_labels.color')
            ->orderBy('total', 'desc')
            ->get();
        
        // Data untuk aktivitas maintenance chart dengan periode yang dinamis
        $activityData = [];
        
        // Sesuaikan interval berdasarkan periode
        $interval = 'day'; // default
        $format = 'd M Y';
        
        if ($period == '1y') {
            $interval = 'month';
            $format = 'M Y';
        } elseif ($period == '90d') {
            $interval = 'week';
            $format = 'W M Y';
        }
        
        // Buat periode berdasarkan interval
        $periods = [];
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            $periodKey = $current->format($format);
            $periods[$periodKey] = [
                'start' => $current->copy(),
                'end' => $interval == 'day' 
                    ? $current->copy()->endOfDay() 
                    : ($interval == 'week' 
                        ? $current->copy()->addDays(6)->endOfDay()
                        : $current->copy()->endOfMonth()),
                'created' => 0,
                'completed' => 0
            ];
            
            if ($interval == 'day') {
                $current->addDay();
            } elseif ($interval == 'week') {
                $current->addDays(7);
            } else {
                $current->addMonth();
            }
        }
        
        // Hitung data untuk tiap periode
        foreach ($periods as $key => $periodData) {
            $created = MaintenanceTask::whereBetween('created_at', [$periodData['start'], $periodData['end']])->count();
            $completed = MaintenanceTask::where('status', 'DONE')
                ->whereBetween('completed_at', [$periodData['start'], $periodData['end']])
                ->count();
                
            $activityData[] = [
                'month' => $key,
                'created' => $created,
                'completed' => $completed
            ];
        }
        
        // Recent media evidence dengan filter periode
        $recentEvidenceMedia = $this->getRecentEvidenceMedia($startDate, $endDate);
        
        // Recent tasks dengan filter periode
        $recentTasks = MaintenanceTask::with(['creator', 'documents'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(10)
            ->get();
            
        // Recent activities (untuk feed aktivitas)
        $recentActivities = DB::table('maintenance_activity_logs')
            ->join('users', 'maintenance_activity_logs.user_id', '=', 'users.id')
            ->leftJoin('maintenance_tasks', 'maintenance_activity_logs.task_id', '=', 'maintenance_tasks.id')
            ->select(
                'maintenance_activity_logs.*',
                'users.nama_lengkap as user_name',
                'maintenance_tasks.task_number',
                'maintenance_tasks.title as task_title'
            )
            ->orderBy('maintenance_activity_logs.created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Statistik label (untuk progress bars)
        $labelStats = $this->getLabelStats();
        
        // Recent purchase orders (untuk tabel)
        $recentPurchaseOrders = MaintenancePurchaseOrder::with('task')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(6)
            ->get();
        
        // Menghitung task yang due date hari ini
        $dueTodayTasks = MaintenanceTask::whereDate('due_date', Carbon::today())
            ->whereNotIn('status', ['DONE', 'CANCELLED'])
            ->count();

        // Menghitung task yang overdue (lewat due date tapi belum selesai)
        $overdueTasks = MaintenanceTask::where('due_date', '<', Carbon::today())
            ->whereNotIn('status', ['DONE', 'CANCELLED'])
            ->count();

        // Data list overdue tasks untuk detail
        $overdueTasksList = MaintenanceTask::with('creator')
            ->where('due_date', '<', Carbon::today())
            ->whereNotIn('status', ['DONE', 'CANCELLED'])
            ->orderBy('due_date')
            ->limit(5)
            ->get();
        
        // Data untuk chart tasks berdasarkan member (semua user di divisi 20 dengan status A)
        $tasksByMember = DB::table('users')
            ->leftJoin('maintenance_members', 'users.id', '=', 'maintenance_members.user_id')
            ->leftJoin('maintenance_tasks', 'maintenance_members.task_id', '=', 'maintenance_tasks.id')
            ->where('users.division_id', 20)
            ->where('users.status', 'A')
            ->select(
                'users.id as user_id',
                'users.nama_lengkap as member_name',
                DB::raw('count(DISTINCT maintenance_tasks.id) as total_tasks'),
                DB::raw('SUM(CASE WHEN maintenance_tasks.status = "DONE" THEN 1 ELSE 0 END) as completed_tasks')
            )
            ->groupBy('users.id', 'users.nama_lengkap')
            ->orderBy('member_name')
            ->get();
        
        // Tambahkan ini setelah $labelStats = $this->getLabelStats();
        $priorityStats = $this->getPriorityStats($startDate, $endDate);
        
        // Tambahkan ini setelah $tasksByMember = DB::table('users')...
        $allMedia = $this->getAllMedia();
        
        return view('maintenance.dashboard', compact(
            'totalTasks',
            'inProgressTasks',
            'completedTasks',
            'totalPurchaseOrders',
            'taskIncreasePercent',
            'inProgressIncreasePercent',
            'inProgressIncreaseClass',
            'inProgressIncreaseIcon',
            'completedIncreasePercent',
            'poIncreasePercent',
            'poIncreaseClass',
            'poIncreaseIcon',
            'taskStatusData',
            'categoryData',
            'activityData',
            'recentEvidenceMedia',
            'recentTasks',
            'recentActivities',
            'labelStats',
            'priorityStats',
            'recentPurchaseOrders',
            'startDate',
            'endDate',
            'period',
            'dueTodayTasks',
            'overdueTasks',
            'overdueTasksList',
            'tasksByMember',
            'allMedia'
        ));
    }
    
    /**
     * Mengambil media evidence terbaru (gambar dan video)
     */
    private function getRecentEvidenceMedia($startDate, $endDate)
    {
        $photos = MaintenanceEvidencePhoto::join('maintenance_evidence', 'maintenance_evidence_photos.evidence_id', '=', 'maintenance_evidence.id')
            ->join('maintenance_tasks', 'maintenance_evidence.task_id', '=', 'maintenance_tasks.id')
            ->whereBetween('maintenance_evidence_photos.created_at', [$startDate, $endDate])
            ->select(
                'maintenance_evidence_photos.id',
                'maintenance_evidence_photos.path',
                'maintenance_evidence_photos.file_name',
                'maintenance_evidence_photos.file_type',
                'maintenance_evidence_photos.created_at',
                'maintenance_tasks.id as task_id',
                'maintenance_tasks.task_number',
                'maintenance_tasks.title as task_title',
                DB::raw("'image' as type")
            )
            ->latest('maintenance_evidence_photos.created_at')
            ->limit(5);
            
        $videos = MaintenanceEvidenceVideo::join('maintenance_evidence', 'maintenance_evidence_videos.evidence_id', '=', 'maintenance_evidence.id')
            ->join('maintenance_tasks', 'maintenance_evidence.task_id', '=', 'maintenance_tasks.id')
            ->whereBetween('maintenance_evidence_videos.created_at', [$startDate, $endDate])
            ->select(
                'maintenance_evidence_videos.id',
                'maintenance_evidence_videos.path',
                'maintenance_evidence_videos.file_name',
                'maintenance_evidence_videos.file_type',
                'maintenance_evidence_videos.created_at',
                'maintenance_tasks.id as task_id',
                'maintenance_tasks.task_number',
                'maintenance_tasks.title as task_title',
                DB::raw("'video' as type")
            )
            ->latest('maintenance_evidence_videos.created_at')
            ->limit(5);
            
        $combinedMedia = $photos->union($videos)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();
            
        return $combinedMedia;
    }
    
    /**
     * Menyiapkan data statistik label untuk progress bars
     * Menggunakan label_id dan LABELS dari model MaintenanceTask
     */
    private function getLabelStats()
    {
        $labelStats = DB::table('maintenance_tasks')
            ->select('label_id', DB::raw('count(*) as total'))
            ->groupBy('label_id')
            ->orderBy('total', 'desc')
            ->limit(7)
            ->get();
            
        $totalTasks = MaintenanceTask::count();
        $colors = [
            'bg-success bg-opacity-50',
            'bg-warning bg-opacity-50',
            'bg-danger bg-opacity-50',
            'bg-info bg-opacity-50',
            'bg-primary bg-opacity-50',
            'bg-secondary bg-opacity-50',
            'bg-dark bg-opacity-50',
        ];
        
        $formattedStats = [];
        foreach ($labelStats as $index => $label) {
            $percentage = $totalTasks > 0 ? round(($label->total / $totalTasks) * 100) : 0;
            $labelName = MaintenanceTask::LABELS[$label->label_id] ?? 'Unknown';
            
            $formattedStats[] = [
                'id' => $label->label_id,
                'name' => $labelName,
                'total' => $label->total,
                'percentage' => $percentage,
                'color' => $colors[$index % count($colors)]
            ];
        }
        
        return $formattedStats;
    }

    /**
     * Menyiapkan data statistik berdasarkan priority
     */
    private function getPriorityStats($startDate, $endDate)
    {
        $priorityStats = DB::table('maintenance_tasks')
            ->join('maintenance_priorities', 'maintenance_tasks.priority_id', '=', 'maintenance_priorities.id')
            ->whereBetween('maintenance_tasks.created_at', [$startDate, $endDate])
            ->select(
                'maintenance_priorities.id',
                'maintenance_priorities.priority as name',
                DB::raw('count(*) as total')
            )
            ->groupBy('maintenance_priorities.id', 'maintenance_priorities.priority')
            ->orderBy('total', 'desc')
            ->get();
            
        $totalTasks = MaintenanceTask::whereBetween('created_at', [$startDate, $endDate])->count();
        
        $colors = [
            'bg-danger bg-opacity-50',   // IMPORTANT VS URGENT
            'bg-warning bg-opacity-50',  // IMPORTANT VS NOT URGENT
            'bg-info bg-opacity-50',     // NOT IMPORTANT VS URGENT
            'bg-success bg-opacity-50',  // NOT IMPORTANT VS NOT URGENT (jika ada)
        ];
        
        $formattedStats = [];
        foreach ($priorityStats as $index => $priority) {
            $percentage = $totalTasks > 0 ? round(($priority->total / $totalTasks) * 100) : 0;
            
            $formattedStats[] = [
                'id' => $priority->id,
                'name' => $priority->name,
                'total' => $priority->total,
                'percentage' => $percentage,
                'color' => $colors[$index % count($colors)]
            ];
        }
        
        return $formattedStats;
    }

    public function exportTasksByMember(Request $request)
    {
        // Ambil parameter filter yang sama dengan dashboard
        $period = $request->input('period', '30d');
        $member_id = $request->input('member_id');
        
        // Tentukan tanggal awal berdasarkan periode
        $endDate = Carbon::now();
        $startDate = null;
        
        switch ($period) {
            case '1d':
                $startDate = Carbon::now()->startOfDay();
                break;
            case '7d':
                $startDate = Carbon::now()->subDays(7)->startOfDay();
                break;
            case '30d':
                $startDate = Carbon::now()->subDays(30)->startOfDay();
                break;
            case '90d':
                $startDate = Carbon::now()->subDays(90)->startOfDay();
                break;
            case '1y':
                $startDate = Carbon::now()->subYear()->startOfDay();
                break;
            default:
                $period = '30d'; // Default jika parameter tidak valid
                $startDate = Carbon::now()->subDays(30)->startOfDay();
        }
        
        // Query untuk mendapatkan detail tasks untuk member tertentu
        $query = MaintenanceTask::select(
            'maintenance_tasks.id',
            'maintenance_tasks.task_number',
            'maintenance_tasks.title',
            'maintenance_tasks.description',
            'maintenance_tasks.due_date',
            'maintenance_tasks.created_at',
            'maintenance_tasks.completed_at',
            'maintenance_tasks.status',
            'users.nama_lengkap as member_name'
        )
        ->join('maintenance_members', 'maintenance_tasks.id', '=', 'maintenance_members.task_id')
        ->join('users', 'maintenance_members.user_id', '=', 'users.id')
        ->whereBetween('maintenance_tasks.created_at', [$startDate, $endDate]);
        
        // Filter by member jika diberikan
        if ($member_id) {
            $query->where('users.id', $member_id);
        }
        
        $tasks = $query->orderBy('maintenance_tasks.created_at', 'desc')->get();
        
        // Nama file untuk export
        $fileName = 'tasks_by_member_' . ($member_id ? 'id_' . $member_id : 'all') . '_' . date('Y-m-d') . '.xlsx';
        
        // Export ke excel
        return Excel::download(new TasksByMemberExport($tasks), $fileName);
    }

    public function exportTasksByPriority(Request $request)
    {
        // Ambil parameter filter yang sama dengan dashboard
        $period = $request->input('period', '30d');
        $priority_id = $request->input('priority_id');
        
        // Tentukan tanggal awal berdasarkan periode
        $endDate = Carbon::now();
        $startDate = null;
        
        switch ($period) {
            case '1d':
                $startDate = Carbon::now()->startOfDay();
                break;
            case '7d':
                $startDate = Carbon::now()->subDays(7)->startOfDay();
                break;
            case '30d':
                $startDate = Carbon::now()->subDays(30)->startOfDay();
                break;
            case '90d':
                $startDate = Carbon::now()->subDays(90)->startOfDay();
                break;
            case '1y':
                $startDate = Carbon::now()->subYear()->startOfDay();
                break;
            default:
                $period = '30d'; // Default jika parameter tidak valid
                $startDate = Carbon::now()->subDays(30)->startOfDay();
        }
        
        // Query untuk mendapatkan detail tasks berdasarkan priority
        $query = MaintenanceTask::select(
            'maintenance_tasks.id',
            'maintenance_tasks.task_number',
            'maintenance_tasks.title',
            'maintenance_tasks.description',
            'maintenance_tasks.due_date',
            'maintenance_tasks.created_at',
            'maintenance_tasks.completed_at',
            'maintenance_tasks.status',
            'maintenance_priorities.priority as priority_name'
        )
        ->join('maintenance_priorities', 'maintenance_tasks.priority_id', '=', 'maintenance_priorities.id')
        ->whereBetween('maintenance_tasks.created_at', [$startDate, $endDate]);
        
        // Filter by priority jika diberikan
        if ($priority_id) {
            $query->where('maintenance_priorities.id', $priority_id);
        }
        
        $tasks = $query->orderBy('maintenance_tasks.created_at', 'desc')->get();
        
        // Nama file untuk export
        $fileName = 'tasks_by_priority_' . ($priority_id ? 'id_' . $priority_id : 'all') . '_' . date('Y-m-d') . '.xlsx';
        
        // Export ke excel - pastikan import di awal file
        // use App\Exports\TasksByPriorityExport;
        return Excel::download(new TasksByPriorityExport($tasks), $fileName);
    }

    /**
     * Mendapatkan semua aktivitas maintenance untuk ditampilkan di modal
     */
    public function getAllActivities(Request $request)
    {
        $activities = DB::table('maintenance_activity_logs')
            ->join('users', 'maintenance_activity_logs.user_id', '=', 'users.id')
            ->leftJoin('maintenance_tasks', 'maintenance_activity_logs.task_id', '=', 'maintenance_tasks.id')
            ->select(
                'maintenance_activity_logs.*',
                'users.nama_lengkap as user_name',
                'maintenance_tasks.task_number'
            )
            ->orderBy('maintenance_activity_logs.created_at', 'desc')
            ->paginate(15);
        
        return view('maintenance.activities', compact('activities'));
    }

    /**
     * Export aktivitas maintenance ke Excel
     */
    public function exportActivities(Request $request)
    {
        $search = $request->input('search', '');
        $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from'))->startOfDay() : null;
        $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to'))->endOfDay() : null;
        
        $query = MaintenanceActivityLog::with(['user', 'task'])
            ->select('maintenance_activity_logs.*');
        
        // Filter berdasarkan pencarian
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($user) use ($search) {
                      $user->where('nama_lengkap', 'like', "%{$search}%");
                  })
                  ->orWhereHas('task', function($task) use ($search) {
                      $task->where('task_number', 'like', "%{$search}%")
                           ->orWhere('title', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter berdasarkan tanggal
        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        } else if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        } else if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }
        
        $activities = $query->orderBy('created_at', 'desc')->get();
        
        // Nama file untuk export
        $fileName = 'maintenance_activities_' . date('Y-m-d') . '.xlsx';
        
        // Export ke excel - pastikan import di awal file
        // use Maatwebsite\Excel\Facades\Excel;
        // use App\Exports\MaintenanceActivitiesExport;
        return Excel::download(new MaintenanceActivitiesExport($activities), $fileName);
    }

    /**
     * Mendapatkan seluruh media (gambar dan video) dari tabel maintenance_media
     */
    private function getAllMedia($limit = 15)
    {
        $media = DB::table('maintenance_media')
            ->join('maintenance_tasks', 'maintenance_media.task_id', '=', 'maintenance_tasks.id')
            ->leftJoin('tbl_data_outlet', 'maintenance_tasks.id_outlet', '=', 'tbl_data_outlet.id_outlet')
            ->leftJoin('tbl_data_ruko', function($join) {
                $join->on('maintenance_tasks.id_ruko', '=', 'tbl_data_ruko.id_ruko')
                     ->where('maintenance_tasks.id_outlet', '=', 1);
            })
            ->select(
                'maintenance_media.id',
                'maintenance_media.file_name',
                'maintenance_media.file_path',
                'maintenance_media.file_type',
                'maintenance_media.created_at',
                'maintenance_tasks.id as task_id',
                'maintenance_tasks.task_number',
                'maintenance_tasks.title as task_title',
                'maintenance_tasks.id_outlet',
                'tbl_data_outlet.nama_outlet',
                'tbl_data_ruko.nama_ruko'
            )
            ->where(function($query) {
                $query->where('maintenance_media.file_type', 'like', 'image/%')
                      ->orWhere('maintenance_media.file_type', 'like', 'video/%');
            })
            ->orderBy('maintenance_media.created_at', 'desc')
            ->limit($limit)
            ->get();
            
        // Tambahkan informasi tipe file (image/video) berdasarkan file_type
        // Dan format lokasi: outlet_name - nama_ruko (jika id_outlet=1)
        foreach ($media as $item) {
            $item->type = strpos($item->file_type, 'image/') === 0 ? 'image' : 'video';
            
            // Format tampilan nama lokasi
            if ($item->id_outlet == 1 && !empty($item->nama_ruko)) {
                $item->location = $item->nama_outlet . ' - ' . $item->nama_ruko;
            } else {
                $item->location = $item->nama_outlet;
            }
        }
        
        return $media;
    }

    /**
     * Get evidence outlets with search filter
     */
    public function getEvidenceOutlets(Request $request)
    {
        $search = $request->input('search');
        
        $query = DB::table('maintenance_tasks')
            ->join('maintenance_evidence', 'maintenance_tasks.id', '=', 'maintenance_evidence.task_id')
            ->join('tbl_data_outlet', 'maintenance_tasks.id_outlet', '=', 'tbl_data_outlet.id_outlet')
            ->leftJoin('maintenance_evidence_photos', 'maintenance_evidence.id', '=', 'maintenance_evidence_photos.evidence_id')
            ->leftJoin('maintenance_evidence_videos', 'maintenance_evidence.id', '=', 'maintenance_evidence_videos.evidence_id')
            ->select(
                'tbl_data_outlet.id_outlet',
                'tbl_data_outlet.nama_outlet',
                DB::raw('COUNT(DISTINCT maintenance_evidence.id) as count')
            )
            ->whereNotNull(DB::raw('COALESCE(maintenance_evidence_photos.id, maintenance_evidence_videos.id)'));
        
        // Tambahkan filter pencarian jika ada
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('tbl_data_outlet.nama_outlet', 'like', "%{$search}%")
                  ->orWhere('maintenance_tasks.task_number', 'like', "%{$search}%")
                  ->orWhere('maintenance_tasks.title', 'like', "%{$search}%");
            });
        }
        
        $outlets = $query->groupBy('tbl_data_outlet.id_outlet', 'tbl_data_outlet.nama_outlet')
            ->orderBy('tbl_data_outlet.nama_outlet')
            ->get();
        
        return response()->json(['success' => true, 'data' => $outlets]);
    }

    /**
     * Get ruko folders for evidence gallery (untuk outlet pusat)
     */
    public function getEvidenceRukos(Request $request)
    {
        $outletId = $request->input('outlet_id');
        
        // Query untuk mendapatkan daftar ruko dengan jumlah evidence
        $rukos = DB::table('maintenance_tasks')
            ->join('maintenance_evidence', 'maintenance_tasks.id', '=', 'maintenance_evidence.task_id')
            ->join('tbl_data_ruko', 'maintenance_tasks.id_ruko', '=', 'tbl_data_ruko.id_ruko')
            ->leftJoin('maintenance_evidence_photos', 'maintenance_evidence.id', '=', 'maintenance_evidence_photos.evidence_id')
            ->leftJoin('maintenance_evidence_videos', 'maintenance_evidence.id', '=', 'maintenance_evidence_videos.evidence_id')
            ->select(
                'tbl_data_ruko.id_ruko',
                'tbl_data_ruko.nama_ruko',
                DB::raw('COUNT(DISTINCT maintenance_evidence.id) as count')
            )
            ->where('maintenance_tasks.id_outlet', $outletId)
            ->whereNotNull(DB::raw('COALESCE(maintenance_evidence_photos.id, maintenance_evidence_videos.id)'))
            ->groupBy('tbl_data_ruko.id_ruko', 'tbl_data_ruko.nama_ruko')
            ->orderBy('tbl_data_ruko.nama_ruko')
            ->get();
        
        return response()->json(['success' => true, 'data' => $rukos]);
    }

    /**
     * Get date folders for evidence gallery
     */
    public function getEvidenceDates(Request $request)
    {
        $outletId = $request->input('outlet_id');
        $rukoId = $request->input('ruko_id');
        
        // Base query
        $query = DB::table('maintenance_tasks')
            ->join('maintenance_evidence', 'maintenance_tasks.id', '=', 'maintenance_evidence.task_id')
            ->leftJoin('maintenance_evidence_photos', 'maintenance_evidence.id', '=', 'maintenance_evidence_photos.evidence_id')
            ->leftJoin('maintenance_evidence_videos', 'maintenance_evidence.id', '=', 'maintenance_evidence_videos.evidence_id')
            ->select(
                DB::raw('DATE(maintenance_evidence.created_at) as date'),
                DB::raw('DATE_FORMAT(maintenance_evidence.created_at, "%d %b %Y") as display_date'),
                DB::raw('COUNT(DISTINCT maintenance_evidence.id) as count')
            )
            ->where('maintenance_tasks.id_outlet', $outletId)
            ->whereNotNull(DB::raw('COALESCE(maintenance_evidence_photos.id, maintenance_evidence_videos.id)'));
        
        // Jika ada ruko_id dan outlet adalah pusat (id=1)
        if ($rukoId && $outletId == 1) {
            $query->where('maintenance_tasks.id_ruko', $rukoId);
        }
        
        $dates = $query->groupBy('date', 'display_date')
            ->orderBy('date', 'desc')
            ->get();
        
        return response()->json(['success' => true, 'data' => $dates]);
    }

    /**
     * Get task folders for evidence gallery
     */
    public function getEvidenceTasks(Request $request)
    {
        $outletId = $request->input('outlet_id');
        $date = $request->input('date');
        $rukoId = $request->input('ruko_id');
        
        // Base query
        $query = DB::table('maintenance_tasks')
            ->join('maintenance_evidence', 'maintenance_tasks.id', '=', 'maintenance_evidence.task_id')
            ->leftJoin('maintenance_evidence_photos', 'maintenance_evidence.id', '=', 'maintenance_evidence_photos.evidence_id')
            ->leftJoin('maintenance_evidence_videos', 'maintenance_evidence.id', '=', 'maintenance_evidence_videos.evidence_id')
            ->select(
                'maintenance_tasks.id',
                'maintenance_tasks.task_number',
                'maintenance_tasks.title',
                DB::raw('COUNT(DISTINCT maintenance_evidence.id) as count')
            )
            ->where('maintenance_tasks.id_outlet', $outletId)
            ->whereDate('maintenance_evidence.created_at', $date)
            ->whereNotNull(DB::raw('COALESCE(maintenance_evidence_photos.id, maintenance_evidence_videos.id)'));
        
        // Jika ada ruko_id dan outlet adalah pusat (id=1)
        if ($rukoId && $outletId == 1) {
            $query->where('maintenance_tasks.id_ruko', $rukoId);
        }
        
        $tasks = $query->groupBy('maintenance_tasks.id', 'maintenance_tasks.task_number', 'maintenance_tasks.title')
            ->orderBy('maintenance_tasks.task_number')
            ->get();
        
        return response()->json(['success' => true, 'data' => $tasks]);
    }

    /**
     * Get evidence files for a specific task
     */
    public function getEvidenceFiles(Request $request)
    {
        $taskId = $request->input('task_id');
        $type = $request->input('type');
        
        // Get the outlet info for this task
        $taskInfo = DB::table('maintenance_tasks')
            ->join('tbl_data_outlet', 'maintenance_tasks.id_outlet', '=', 'tbl_data_outlet.id_outlet')
            ->leftJoin('tbl_data_ruko', function($join) {
                $join->on('maintenance_tasks.id_ruko', '=', 'tbl_data_ruko.id_ruko')
                    ->where('maintenance_tasks.id_outlet', '=', 1);
            })
            ->select(
                'maintenance_tasks.task_number',
                'maintenance_tasks.title as task_title',
                'tbl_data_outlet.nama_outlet',
                'tbl_data_ruko.nama_ruko'
            )
            ->where('maintenance_tasks.id', $taskId)
            ->first();
        
        $outletName = $taskInfo->nama_outlet;
        if ($taskInfo->nama_ruko) {
            $outletName .= ' - ' . $taskInfo->nama_ruko;
        }
        
        $result = [];
        
        // Get photos if requested type is 'all' or 'image'
        if (!$type || $type === 'all' || $type === 'image') {
            $photos = DB::table('maintenance_evidence')
                ->join('maintenance_evidence_photos', 'maintenance_evidence.id', '=', 'maintenance_evidence_photos.evidence_id')
                ->select(
                    'maintenance_evidence.id',
                    'maintenance_evidence.notes',
                    'maintenance_evidence.created_at',
                    'maintenance_evidence_photos.path as file_path',
                    'maintenance_evidence_photos.file_name',
                    DB::raw("'image' as type")
                )
                ->where('maintenance_evidence.task_id', $taskId)
                ->get();
            
            // Format photos
            $photos->map(function($photo) use ($taskInfo, $outletName) {
                $photo->file_path = asset('storage/' . $photo->file_path);
                $photo->created_at = Carbon::parse($photo->created_at)->format('d M Y H:i');
                $photo->task_number = $taskInfo->task_number;
                $photo->task_title = $taskInfo->task_title;
                $photo->outlet_name = $outletName;
                return $photo;
            });
            
            $result = $photos;
        }
        
        // Get videos if requested type is 'all' or 'video'
        if (!$type || $type === 'all' || $type === 'video') {
            $videos = DB::table('maintenance_evidence')
                ->join('maintenance_evidence_videos', 'maintenance_evidence.id', '=', 'maintenance_evidence_videos.evidence_id')
                ->select(
                    'maintenance_evidence.id',
                    'maintenance_evidence.notes',
                    'maintenance_evidence.created_at',
                    'maintenance_evidence_videos.path as file_path',
                    'maintenance_evidence_videos.file_name',
                    DB::raw("'video' as type")
                )
                ->where('maintenance_evidence.task_id', $taskId)
                ->get();
            
            // Format videos
            $videos->map(function($video) use ($taskInfo, $outletName) {
                $video->file_path = asset('storage/' . $video->file_path);
                $video->created_at = Carbon::parse($video->created_at)->format('d M Y H:i');
                $video->task_number = $taskInfo->task_number;
                $video->task_title = $taskInfo->task_title;
                $video->outlet_name = $outletName;
                return $video;
            });
            
            // Merge photos and videos if both requested
            if (!$type || $type === 'all') {
                $result = $result->merge($videos);
            } else {
                $result = $videos;
            }
        }
        
        // Sort by created_at
        $allFiles = collect($result)->sortByDesc('created_at')->values();
        
        return response()->json(['success' => true, 'data' => $allFiles]);
    }

    /**
     * Get all evidence files (for flat view)
     */
    public function getAllEvidence(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');
        
        // Base query for photos
        $photosQuery = DB::table('maintenance_evidence')
            ->join('maintenance_evidence_photos', 'maintenance_evidence.id', '=', 'maintenance_evidence_photos.evidence_id')
            ->join('maintenance_tasks', 'maintenance_evidence.task_id', '=', 'maintenance_tasks.id')
            ->join('tbl_data_outlet', 'maintenance_tasks.id_outlet', '=', 'tbl_data_outlet.id_outlet')
            ->leftJoin('tbl_data_ruko', function($join) {
                $join->on('maintenance_tasks.id_ruko', '=', 'tbl_data_ruko.id_ruko')
                    ->where('maintenance_tasks.id_outlet', '=', 1);
            })
            ->select(
                'maintenance_evidence.id',
                'maintenance_evidence.notes',
                'maintenance_evidence.created_at',
                'maintenance_evidence_photos.path as file_path',
                'maintenance_evidence_photos.file_name',
                'maintenance_tasks.task_number',
                'maintenance_tasks.title as task_title',
                'tbl_data_outlet.nama_outlet',
                'tbl_data_ruko.nama_ruko',
                DB::raw("'image' as type")
            );
        
        // Base query for videos
        $videosQuery = DB::table('maintenance_evidence')
            ->join('maintenance_evidence_videos', 'maintenance_evidence.id', '=', 'maintenance_evidence_videos.evidence_id')
            ->join('maintenance_tasks', 'maintenance_evidence.task_id', '=', 'maintenance_tasks.id')
            ->join('tbl_data_outlet', 'maintenance_tasks.id_outlet', '=', 'tbl_data_outlet.id_outlet')
            ->leftJoin('tbl_data_ruko', function($join) {
                $join->on('maintenance_tasks.id_ruko', '=', 'tbl_data_ruko.id_ruko')
                    ->where('maintenance_tasks.id_outlet', '=', 1);
            })
            ->select(
                'maintenance_evidence.id',
                'maintenance_evidence.notes',
                'maintenance_evidence.created_at',
                'maintenance_evidence_videos.path as file_path',
                'maintenance_evidence_videos.file_name',
                'maintenance_tasks.task_number',
                'maintenance_tasks.title as task_title',
                'tbl_data_outlet.nama_outlet',
                'tbl_data_ruko.nama_ruko',
                DB::raw("'video' as type")
            );
        
        // Apply search filter if provided
        if ($search) {
            $photosQuery->where(function($query) use ($search) {
                $query->where('maintenance_tasks.task_number', 'like', "%{$search}%")
                    ->orWhere('maintenance_tasks.title', 'like', "%{$search}%")
                    ->orWhere('tbl_data_outlet.nama_outlet', 'like', "%{$search}%")
                    ->orWhere('tbl_data_ruko.nama_ruko', 'like', "%{$search}%");
            });
            
            $videosQuery->where(function($query) use ($search) {
                $query->where('maintenance_tasks.task_number', 'like', "%{$search}%")
                    ->orWhere('maintenance_tasks.title', 'like', "%{$search}%")
                    ->orWhere('tbl_data_outlet.nama_outlet', 'like', "%{$search}%")
                    ->orWhere('tbl_data_ruko.nama_ruko', 'like', "%{$search}%");
            });
        }
        
        // Apply type filter if provided
        if ($type && $type !== 'all') {
            if ($type === 'image') {
                $videosQuery->whereRaw('1=0'); // No videos
            } else if ($type === 'video') {
                $photosQuery->whereRaw('1=0'); // No photos
            }
        }
        
        // Get photos and videos
        $photos = $photosQuery->get();
        $videos = $videosQuery->get();
        
        // Format photos
        $photos->map(function($photo) {
            $photo->file_path = asset('storage/' . $photo->file_path);
            $photo->created_at = Carbon::parse($photo->created_at)->format('d M Y H:i');
            $photo->outlet_name = $photo->nama_outlet;
            if ($photo->nama_ruko) {
                $photo->outlet_name .= ' - ' . $photo->nama_ruko;
            }
            return $photo;
        });
        
        // Format videos
        $videos->map(function($video) {
            $video->file_path = asset('storage/' . $video->file_path);
            $video->created_at = Carbon::parse($video->created_at)->format('d M Y H:i');
            $video->outlet_name = $video->nama_outlet;
            if ($video->nama_ruko) {
                $video->outlet_name .= ' - ' . $video->nama_ruko;
            }
            return $video;
        });
        
        // Merge photos and videos and sort by created_at
        $allFiles = $photos->merge($videos)->sortByDesc('created_at')->values();
        
        return response()->json(['success' => true, 'data' => $allFiles]);
    }
}
