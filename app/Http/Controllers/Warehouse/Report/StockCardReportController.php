<?php

namespace App\Http\Controllers\Warehouse\Report;

use App\Http\Controllers\Controller;
use App\Models\StockCard;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\LogActivity;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockCardReportExport;

class StockCardReportController extends Controller
{
    use LogActivity;

    public function index()
    {
        $this->logActivity(
            'stock_card_report',
            'VIEW',
            'Melihat halaman kartu stok'
        );

        $warehouses = Warehouse::orderBy('name')->get();
        $items = Item::orderBy('name')->get();

        return view('warehouse.reports.stock-card.index', compact('warehouses', 'items'));
    }

    public function getData(Request $request)
    {
        try {
            \Log::info('StockCardReport getData called with params:', $request->all());

            $query = StockCard::query()
                ->join('items', 'stock_cards.item_id', '=', 'items.id')
                ->join('warehouses', 'stock_cards.warehouse_id', '=', 'warehouses.id')
                ->select([
                    'stock_cards.*',
                    'items.name as item_name',
                    'items.sku as item_sku',
                    'items.small_unit_id',
                    'items.medium_unit_id',
                    'items.large_unit_id',
                    'items.medium_conversion_qty',
                    'items.small_conversion_qty',
                    'warehouses.name as warehouse_name'
                ])
                ->with(['smallUnit', 'mediumUnit', 'largeUnit']);

            if ($request->filled('warehouse_id')) {
                $query->where('stock_cards.warehouse_id', $request->warehouse_id);
            }

            if ($request->filled('item_id')) {
                $query->where('stock_cards.item_id', $request->item_id);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('stock_cards.date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('stock_cards.date', '<=', $request->end_date);
            }

            $query->orderBy('stock_cards.date', 'desc');

            $data = $query->paginate(10);

            \Log::info('StockCardReport data count:', ['count' => $data->count()]);
            \Log::info('StockCardReport first item:', ['item' => $data->first()]);

            $this->logActivity(
                'stock_card_report',
                'LOAD',
                'Memuat data kartu stok'
            );

            $response = [
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
            ];

            \Log::info('StockCardReport response:', $response);

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Error in StockCardReportController@getData: ' . $e->getMessage());
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
            $query = StockCard::query()
                ->join('items', 'stock_cards.item_id', '=', 'items.id')
                ->join('warehouses', 'stock_cards.warehouse_id', '=', 'warehouses.id')
                ->select([
                    'stock_cards.*',
                    'items.name as item_name',
                    'items.sku as item_sku',
                    'items.small_unit_id',
                    'items.medium_unit_id',
                    'items.large_unit_id',
                    'items.medium_conversion_qty',
                    'items.small_conversion_qty',
                    'warehouses.name as warehouse_name'
                ])
                ->with(['smallUnit', 'mediumUnit', 'largeUnit']);

            if ($request->filled('warehouse_id')) {
                $query->where('stock_cards.warehouse_id', $request->warehouse_id);
            }

            if ($request->filled('item_id')) {
                $query->where('stock_cards.item_id', $request->item_id);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('stock_cards.date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('stock_cards.date', '<=', $request->end_date);
            }

            $data = $query->orderBy('stock_cards.date', 'desc')->get();

            $this->logActivity(
                'stock_card_report',
                'EXPORT',
                'Mengexport data kartu stok'
            );

            return Excel::download(new StockCardReportExport($data), 'kartu_stok_' . date('d-m-Y') . '.xlsx');

        } catch (\Exception $e) {
            \Log::error('Error in StockCardReportController@export: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect()->back()->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }
} 