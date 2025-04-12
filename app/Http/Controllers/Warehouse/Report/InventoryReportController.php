<?php

namespace App\Http\Controllers\Warehouse\Report;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\LogActivity;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryReportExport;

class InventoryReportController extends Controller
{
    use LogActivity;

    public function index()
    {
        $this->logActivity(
            'inventory_report',
            'VIEW',
            'Melihat halaman laporan stok'
        );

        $warehouses = Warehouse::orderBy('name')->get();
        return view('warehouse.reports.inventory.index', compact('warehouses'));
    }

    public function getData(Request $request)
    {
        try {
            // Debug query untuk melihat perhitungan moving_average_cost
            DB::enableQueryLog();

            $query = Inventory::query()
                ->join('items', 'inventories.item_id', '=', 'items.id')
                ->join('warehouses', 'inventories.warehouse_id', '=', 'warehouses.id')
                ->leftJoin('units as small_units', 'items.small_unit_id', '=', 'small_units.id')
                ->leftJoin('units as medium_units', 'items.medium_unit_id', '=', 'medium_units.id')
                ->leftJoin('units as large_units', 'items.large_unit_id', '=', 'large_units.id')
                ->select(
                    'warehouses.name as warehouse_name',
                    'items.name as item_name',
                    'items.sku as item_sku',
                    'inventories.stock_on_hand',
                    'inventories.moving_average_cost', // Harga rata-rata dari tabel inventories
                    'inventories.last_purchase_price',
                    'inventories.total_value',
                    'items.small_unit_id',
                    'items.medium_unit_id',
                    'items.large_unit_id',
                    'items.small_conversion_qty',
                    'items.medium_conversion_qty',
                    'small_units.name as small_unit_name',
                    'medium_units.name as medium_unit_name',
                    'large_units.name as large_unit_name'
                );

            if ($request->filled('warehouse_id')) {
                $query->where('inventories.warehouse_id', $request->warehouse_id);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('items.name', 'like', "%{$search}%")
                      ->orWhere('items.sku', 'like', "%{$search}%");
                });
            }

            $data = $query->orderBy('items.name')->paginate(10);

            // Log query yang dijalankan
            \Log::info('Query Log:', DB::getQueryLog());
            DB::disableQueryLog();

            $this->logActivity(
                'inventory_report',
                'LOAD',
                'Memuat data laporan stok'
            );

            return response()->json([
                'success' => true,
                'data' => $data->items(),
                'pagination' => [
                    'total' => $data->total(),
                    'per_page' => $data->perPage(),
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in Inventory Report data: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $query = Inventory::query()
                ->join('items', 'inventories.item_id', '=', 'items.id')
                ->join('warehouses', 'inventories.warehouse_id', '=', 'warehouses.id')
                ->leftJoin('units as small_units', 'items.small_unit_id', '=', 'small_units.id')
                ->leftJoin('units as medium_units', 'items.medium_unit_id', '=', 'medium_units.id')
                ->leftJoin('units as large_units', 'items.large_unit_id', '=', 'large_units.id')
                ->select(
                    'warehouses.name as warehouse_name',
                    'items.name as item_name',
                    'items.sku as sku',
                    'inventories.stock_on_hand',
                    'inventories.moving_average_cost',
                    'inventories.last_purchase_price',
                    'inventories.total_value',
                    'items.small_unit_id',
                    'items.medium_unit_id',
                    'items.large_unit_id',
                    'items.small_conversion_qty',
                    'items.medium_conversion_qty',
                    'small_units.name as small_unit_name',
                    'medium_units.name as medium_unit_name',
                    'large_units.name as large_unit_name'
                );

            if ($request->filled('warehouse_id')) {
                $query->where('inventories.warehouse_id', $request->warehouse_id);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('items.name', 'like', "%{$search}%")
                      ->orWhere('items.sku', 'like', "%{$search}%");
                });
            }

            $data = $query->get();
            $user = auth()->user();

            $this->logActivity(
                'inventory_report',
                'EXPORT',
                'Mengexport data laporan stok'
            );

            return Excel::download(
                new InventoryReportExport($data, $user),
                'Laporan_Stok_' . date('d-m-Y') . '.xlsx'
            );

        } catch (\Exception $e) {
            \Log::error('Error in Inventory Report export: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect()->back()->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }
}