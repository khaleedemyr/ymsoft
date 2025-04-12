<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceTask;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TaskStatusExport;
use DB;

class ReportController extends Controller
{
    /**
     * Generate report for task status
     */
    public function taskStatusReport(Request $request)
    {
        try {
            // Set default period to 30 days if not specified
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
            
            // Data untuk task status dengan filter periode
            $taskStatusData = [
                'Todo' => MaintenanceTask::where('status', 'TASK')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'In Progress' => MaintenanceTask::where('status', 'IN_PROGRESS')
                    ->where(function($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate])
                            ->orWhereBetween('updated_at', [$startDate, $endDate]);
                    })
                    ->count(),
                'PR' => MaintenanceTask::where('status', 'PR')
                    ->where(function($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate])
                            ->orWhereBetween('updated_at', [$startDate, $endDate]);
                    })
                    ->count(),
                'PO' => MaintenanceTask::where('status', 'PO')
                    ->where(function($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate])
                            ->orWhereBetween('updated_at', [$startDate, $endDate]);
                    })
                    ->count(),
                'In Review' => MaintenanceTask::where('status', 'IN_REVIEW')
                    ->where(function($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate])
                            ->orWhereBetween('updated_at', [$startDate, $endDate]);
                    })
                    ->count(),
                'Done' => MaintenanceTask::where('status', 'DONE')
                    ->whereBetween('completed_at', [$startDate, $endDate])
                    ->count(),
            ];
            
            // Get task details for detailed report dengan relasi yang diperlukan
            $tasks = MaintenanceTask::with(['creator'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();
            
            // Nama file
            $filename = 'task_status_report_' . date('Ymd_His') . '.xlsx';
            
            // Gunakan Laravel Excel untuk mengekspor
            return Excel::download(
                new TaskStatusExport($startDate, $endDate, $taskStatusData, $tasks),
                $filename
            );
            
        } catch (\Exception $e) {
            \Log::error('Error generating task status report: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            // Return as JSON if AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghasilkan laporan: ' . $e->getMessage()
                ], 500);
            }
            
            // Otherwise redirect back with error
            return redirect()->back()->with('error', 'Gagal menghasilkan laporan: ' . $e->getMessage());
        }
    }
}
