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
        $recentActivities = collect([
            (object)[
                'id' => 1,
                'user' => (object)['name' => 'Admin'],
                'action' => 'CREATED',
                'description' => 'Membuat task baru',
                'created_at' => now()->subDays(1)
            ],
            (object)[
                'id' => 2,
                'user' => (object)['name' => 'Teknisi'],
                'action' => 'UPDATED',
                'description' => 'Mengubah status task',
                'created_at' => now()->subDays(2)
            ],
            (object)[
                'id' => 3,
                'user' => (object)['name' => 'Manager'],
                'action' => 'COMMENT',
                'description' => 'Menambahkan komentar',
                'created_at' => now()->subDays(3)
            ]
        ]);
        
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
            'recentPurchaseOrders',
            'startDate',
            'endDate',
            'period',
            'dueTodayTasks',
            'overdueTasks',
            'overdueTasksList'
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
}
