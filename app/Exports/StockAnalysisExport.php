<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class StockAnalysisExport implements FromCollection, WithHeadings, WithMapping
{
    protected $warehouseId;
    protected $itemId;
    protected $startDate;
    protected $endDate;

    public function __construct($warehouseId, $itemId, $startDate, $endDate)
    {
        $this->warehouseId = $warehouseId;
        $this->itemId = $itemId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
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

        if ($this->warehouseId) {
            $query->where('inventories.warehouse_id', $this->warehouseId);
        }

        if ($this->itemId) {
            $query->where('items.id', $this->itemId);
        }

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('inventories.created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Item',
            'SKU',
            'Gudang',
            'Unit',
            'Stok',
            'Total Masuk',
            'Total Keluar',
            'Tingkat Perputaran',
            'Harga Rata-rata',
            'Total Nilai'
        ];
    }

    public function map($row): array
    {
        // Hitung total in dan out
        $totalIn = DB::table('stock_cards')
            ->where('item_id', $row->item_id)
            ->when($this->startDate, function($q) {
                $q->whereDate('date', '>=', $this->startDate);
            })
            ->when($this->endDate, function($q) {
                $q->whereDate('date', '<=', $this->endDate);
            })
            ->sum('qty_in') ?? 0;

        $totalOut = DB::table('stock_cards')
            ->where('item_id', $row->item_id)
            ->when($this->startDate, function($q) {
                $q->whereDate('date', '>=', $this->startDate);
            })
            ->when($this->endDate, function($q) {
                $q->whereDate('date', '<=', $this->endDate);
            })
            ->sum('qty_out') ?? 0;

        // Hitung turnover rate
        $averageStock = DB::table('stock_cards')
            ->where('item_id', $row->item_id)
            ->when($this->startDate, function($q) {
                $q->whereDate('date', '>=', $this->startDate);
            })
            ->when($this->endDate, function($q) {
                $q->whereDate('date', '<=', $this->endDate);
            })
            ->avg('stock_balance');

        $turnoverRate = $averageStock > 0 ? round($totalOut / $averageStock, 2) : 0;

        return [
            $row->item_name,
            $row->item_sku,
            $row->warehouse_name,
            $row->unit_name,
            $row->stock_balance,
            $totalIn,
            $totalOut,
            $turnoverRate,
            $row->moving_average_cost,
            $row->total_value
        ];
    }
} 