<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceTask;
use Illuminate\Http\Request;
use DataTables;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MaintenanceTaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Cek apakah user bisa memilih outlet
        $canSelectOutlet = $this->userCanSelectOutlet($user);
        
        if ($canSelectOutlet) {
            // User bisa memilih outlet
            $outlets = Outlet::orderBy('nama_outlet')->get();
            $selectedOutletId = session('selected_outlet_id', $user->id_outlet);
        } else {
            // User tidak bisa memilih outlet, tampilkan outlet sesuai id_outlet user
            $outlets = Outlet::where('id_outlet', $user->id_outlet)->get();
            $selectedOutletId = $user->id_outlet;
        }

        $selectedOutlet = Outlet::find($selectedOutletId);

        // Ambil data users untuk assigned to
        $users = User::where('status', 'active')
                     ->orderBy('nama_lengkap')
                     ->get();

        return view('maintenance.tasks.index', compact(
            'canSelectOutlet',
            'outlets',
            'selectedOutlet',
            'selectedOutletId',
            'users'
        ));
    }

    private function userCanSelectOutlet($user)
    {
        return $user->id_role === '5af56935b011a' || // Super Admin
               $user->division_id === 20 || // Divisi tertentu
               $user->id_outlet === 1; // User dengan id_outlet = 1
    }

    public function getData(Request $request)
    {
        $tasks = MaintenanceTask::with(['outlet', 'ruko', 'assignedTo', 'creator'])
            ->select('maintenance_tasks.*');

        return DataTables::of($tasks)
            ->addColumn('outlet_name', function ($task) {
                return $task->outlet ? $task->outlet->nama : '-';
            })
            ->addColumn('ruko_name', function ($task) {
                return $task->ruko ? $task->ruko->nama : '-';
            })
            ->addColumn('assigned_to_name', function ($task) {
                return $task->assignedTo ? $task->assignedTo->name : '-';
            })
            ->addColumn('created_by_name', function ($task) {
                return $task->creator ? $task->creator->name : '-';
            })
            ->addColumn('action', function ($task) {
                return view('maintenance.tasks.action_buttons', compact('task'))->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        try {
            $task = MaintenanceTask::create([
                'task_number' => $this->generateTaskNumber(),
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'status' => 'OPEN',
                'outlet_id' => $request->outlet_id,
                'ruko_id' => $request->ruko_id,
                'assigned_to' => $request->assigned_to,
                'due_date' => $request->due_date,
                'created_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task berhasil dibuat',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateTaskNumber()
    {
        $prefix = 'MT' . date('ym');
        $lastNumber = MaintenanceTask::where('task_number', 'like', $prefix . '%')
            ->orderBy('task_number', 'desc')
            ->first();

        if ($lastNumber) {
            $number = intval(substr($lastNumber->task_number, -4)) + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function getRuko($outlet_id)
    {
        \Log::info('getRuko called with outlet_id: ' . $outlet_id);
        
        try {
            $rukos = DB::table('tbl_data_ruko')
                ->where('id_outlet', $outlet_id)
                ->get();

            \Log::info('Ruko data found:', ['count' => $rukos->count()]);
            
            return response()->json([
                'success' => true,
                'data' => $rukos,
                'debug' => [
                    'outlet_id' => $outlet_id,
                    'count' => $rukos->count(),
                    'timestamp' => now()->toDateTimeString()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getRuko: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'debug' => [
                    'outlet_id' => $outlet_id,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
}
