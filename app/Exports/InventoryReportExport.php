<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;

class InventoryReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;
    protected $user;

    public function __construct($data, $user)
    {
        $this->data = $data;
        $this->user = $user;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Gudang',
            'Item',
            'Stok',
            'Satuan',
            'Harga Rata-rata',
            'Harga Beli Terakhir',
            'Total Nilai'
        ];
    }

    public function map($item): array
    {
        return [
            $item->warehouse_name,
            $item->item_name,
            $item->stock_on_hand,
            $item->small_unit_name ?? '-',
            $item->moving_average_cost,
            $item->last_purchase_price,
            $item->total_value
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'E2:G' . ($this->data->count() + 1) => ['numberFormat' => ['formatCode' => '#,##0.00']],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 30,
            'C' => 15,
            'D' => 15,
            'E' => 20,
            'F' => 20,
            'G' => 20,
        ];
    }

    public function title(): string
    {
        return 'Laporan Stok';
    }
} 