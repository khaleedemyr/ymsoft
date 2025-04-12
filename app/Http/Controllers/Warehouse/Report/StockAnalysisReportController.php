<?php

namespace App\Http\Controllers\Warehouse\Report;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\StockCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\LogActivity;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockAnalysisExport;
use Carbon\Carbon;

class StockAnalysisReportController extends Controller
{
    use LogActivity;

    public function index()
    {
        $warehouses = DB::table('warehouses')->get();
        $items = DB::table('items')->get();

        return view('warehouse.reports.stock-analysis.index', compact('warehouses', 'items'));
    }

    public function getData(Request $request)
    {
        try {
            \Log::info('StockAnalysisReport getData called with params:', $request->all());

            $query = DB::table('items')
                ->select([
                    'items.id as item_id',
                    'items.name as item_name',
                    'items.sku as item_sku',
                    'warehouses.id as warehouse_id',
                    'warehouses.name as warehouse_name',
                    'units.name as unit_name',
                    'inventories.stock_on_hand as stock_balance',
                    'inventories.moving_average_cost',
                    DB::raw('(inventories.stock_on_hand * inventories.moving_average_cost) as total_value')
                ])
                ->join('inventories', 'items.id', '=', 'inventories.item_id')
                ->join('warehouses', 'inventories.warehouse_id', '=', 'warehouses.id')
                ->join('units', 'items.small_unit_id', '=', 'units.id');

            // Filter by warehouse if selected
            if ($request->filled('warehouse_id')) {
                $query->where('inventories.warehouse_id', $request->warehouse_id);
            }

            // Filter by item if selected
            if ($request->filled('item_id')) {
                $query->where('items.id', $request->item_id);
            }

            // Filter by date range if selected
            if ($request->filled(['start_date', 'end_date'])) {
                $query->whereBetween('inventories.created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            // Get total records for pagination
            $total = $query->count();

            // Add pagination
            $perPage = 10;
            $currentPage = $request->input('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            
            $data = $query->limit($perPage)
                        ->offset($offset)
                        ->get();

            // Calculate turnover rate for each item
            foreach ($data as $item) {
                $item->turnover_rate = $this->calculateTurnoverRate($item->item_id, $request->start_date, $request->end_date);
                $item->total_in = $this->calculateTotalIn($item->item_id, $request->start_date, $request->end_date);
                $item->total_out = $this->calculateTotalOut($item->item_id, $request->start_date, $request->end_date);
            }

            \Log::info('StockAnalysisReport data count:', ['count' => $data->count()]);

            $this->logActivity(
                'stock_analysis_report',
                'LOAD',
                'Memuat data analisis stok'
            );

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $currentPage,
                    'last_page' => ceil($total / $perPage),
                    'from' => $offset + 1,
                    'to' => min($offset + $perPage, $total)
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in StockAnalysisReportController@getData: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error in StockAnalysisReportController@getData: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $warehouseId = $request->input('warehouse_id');
            $itemId = $request->input('item_id');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Generate filename
            $filename = 'stock_analysis_' . Carbon::now()->format('YmdHis') . '.xlsx';

            $this->logActivity(
                'stock_analysis_report',
                'EXPORT',
                'Mengekspor data analisis stok'
            );

            return Excel::download(new StockAnalysisExport($warehouseId, $itemId, $startDate, $endDate), $filename);

        } catch (\Exception $e) {
            \Log::error('Error in StockAnalysisReportController@export: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }

    private function calculateTurnoverRate($itemId, $startDate, $endDate)
    {
        try {
            $totalOut = $this->calculateTotalOut($itemId, $startDate, $endDate);
            $averageStock = DB::table('stock_cards')
                ->where('item_id', $itemId)
                ->when($startDate, function($q) use ($startDate) {
                    $q->whereDate('date', '>=', $startDate);
                })
                ->when($endDate, function($q) use ($endDate) {
                    $q->whereDate('date', '<=', $endDate);
                })
                ->avg('stock_balance');

            if ($averageStock > 0) {
                return round($totalOut / $averageStock, 2);
            }
            return 0;
        } catch (\Exception $e) {
            \Log::error('Error calculating turnover rate: ' . $e->getMessage());
            return 0;
        }
    }

    private function calculateTotalIn($itemId, $startDate, $endDate)
    {
        try {
            return DB::table('stock_cards')
                ->where('item_id', $itemId)
                ->when($startDate, function($q) use ($startDate) {
                    $q->whereDate('date', '>=', $startDate);
                })
                ->when($endDate, function($q) use ($endDate) {
                    $q->whereDate('date', '<=', $endDate);
                })
                ->sum('qty_in') ?? 0;
        } catch (\Exception $e) {
            \Log::error('Error calculating total in: ' . $e->getMessage());
            return 0;
        }
    }

    private function calculateTotalOut($itemId, $startDate, $endDate)
    {
        try {
            return DB::table('stock_cards')
                ->where('item_id', $itemId)
                ->when($startDate, function($q) use ($startDate) {
                    $q->whereDate('date', '>=', $startDate);
                })
                ->when($endDate, function($q) use ($endDate) {
                    $q->whereDate('date', '<=', $endDate);
                })
                ->sum('qty_out') ?? 0;
        } catch (\Exception $e) {
            \Log::error('Error calculating total out: ' . $e->getMessage());
            return 0;
        }
    }
} 