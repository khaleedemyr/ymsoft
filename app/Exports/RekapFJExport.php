<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class RekapFJExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, ShouldAutoSize
{
    protected $data;
    protected $subCategories;
    protected $startDate;
    protected $endDate;
    protected $user;

    public function __construct($data, $subCategories, $startDate, $endDate, $user)
    {
        $this->data = $data;
        $this->subCategories = $subCategories;
        $this->startDate = Carbon::parse($startDate)->format('d-m-Y');
        $this->endDate = Carbon::parse($endDate)->format('d-m-Y');
        $this->user = $user;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        // Menambahkan 4 baris di atas data utama
        return [
            // Baris 1: Judul Report
            [__('translation.reports.rekap_fj.title')],
            // Baris 2: Periode
            ['Periode: ' . $this->startDate . ' s/d ' . $this->endDate],
            // Baris 3: Info download
            ['Downloaded at: ' . now()->format('d-m-Y H:i:s') . ' by: ' . $this->user->nama_lengkap],
            // Baris 4: Baris kosong
            [''],
            // Baris 5: Header tabel
            [
                'Customer',
                'Main Kitchen',
                'Main Store',
                'Chemical',
                'Stationary',
                'Marketing',
                'Line Total'
            ],
        ];
    }

    public function map($row): array
    {
        return [
            $row->customer_name,
            $row->main_kitchen_total,
            $row->main_store_total,
            $row->chemical_total,
            $row->stationary_total,
            $row->marketing_total,
            $row->line_total
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // Style untuk judul report (baris 1)
        $sheet->mergeCells('A1:' . $lastColumn . '1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Style untuk periode dan info download (baris 2-3)
        foreach (['A2:' . $lastColumn . '2', 'A3:' . $lastColumn . '3'] as $range) {
            $sheet->mergeCells($range);
            $sheet->getStyle($range)->applyFromArray([
                'font' => [
                    'size' => 11,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ]);
        }

        // Style untuk header tabel (baris 5)
        $headerRange = 'A5:' . $lastColumn . '5';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style untuk data
        $dataRange = 'A6:' . $lastColumn . $lastRow;
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Style untuk kolom numeric (amount)
        $numericRange = 'B6:' . $lastColumn . $lastRow;
        $sheet->getStyle($numericRange)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
            'numberFormat' => [
                'formatCode' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            ],
        ]);

        // Style untuk kolom customer name
        $sheet->getStyle('A6:A' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // Auto height untuk semua baris
        for ($row = 1; $row <= $lastRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(-1);
        }

        // Freeze pane setelah header
        $sheet->freezePane('A6');

        return [];
    }

    public function title(): string
    {
        return __('translation.reports.rekap_fj.export.sheet_name');
    }
} 