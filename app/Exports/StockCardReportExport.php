<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StockCardReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'No. Referensi',
            'Tipe Transaksi',
            'Item',
            'Gudang',
            'Unit',
            'Qty Masuk',
            'Qty Keluar',
            'Saldo',
            'Harga Rata-rata',
            'Total Nilai'
        ];
    }

    public function map($row): array
    {
        return [
            date('d/m/Y', strtotime($row->transaction_date)),
            $row->reference_number,
            $row->transaction_type,
            $row->item_name,
            $row->warehouse_name,
            $row->unit_name ?? '-',
            $row->qty_in,
            $row->qty_out,
            $row->balance,
            $row->moving_average_cost,
            $row->total_value
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'G' => ['alignment' => ['horizontal' => 'right']],
            'H' => ['alignment' => ['horizontal' => 'right']],
            'I' => ['alignment' => ['horizontal' => 'right']],
            'J' => ['alignment' => ['horizontal' => 'right']],
            'K' => ['alignment' => ['horizontal' => 'right']]
        ];
    }
} 