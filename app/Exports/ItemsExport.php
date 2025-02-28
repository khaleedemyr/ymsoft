<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithProperties, WithCustomStartCell
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function properties(): array
    {
        return [
            'title'          => 'Daftar Item YMSoft',
            'created'        => now(),
            'lastModifiedBy' => auth()->user()->nama_lengkap,
        ];
    }

    public function startCell(): string
    {
        return 'A4'; // Mulai data dari baris 4
    }

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        return [
            'No',
            'SKU',
            'Nama',
            'Kategori',
            'Sub Kategori',
            'Deskripsi',
            'Spesifikasi',
            'Availability',
            'Satuan Kecil',
            'Satuan Sedang',
            'Satuan Besar',
            'Status',
            'Harga'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Get the last row number
        $lastRow = $sheet->getHighestRow();
        
        // Tambah informasi download di atas tabel
        $sheet->setCellValue('A1', 'DAFTAR ITEM YMSOFT');
        $sheet->setCellValue('A2', 'Tanggal Download: ' . now()->format('d/m/Y H:i:s'));
        $sheet->setCellValue('A3', 'Download oleh: ' . auth()->user()->nama_lengkap);
        
        // Merge cells untuk judul
        $sheet->mergeCells('A1:M1');
        $sheet->mergeCells('A2:M2');
        $sheet->mergeCells('A3:M3');
        
        // Style untuk judul dan info
        $sheet->getStyle('A1:A3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
            ]
        ]);
        
        // Style untuk header tabel
        $sheet->getStyle('A4:M4')->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E4E4E4',
                ],
            ],
        ]);

        // Set wrap text untuk kolom deskripsi (F), spesifikasi (G), dan availability (H)
        $sheet->getStyle('F5:F'.$lastRow)->getAlignment()->setWrapText(true);
        $sheet->getStyle('G5:G'.$lastRow)->getAlignment()->setWrapText(true);
        $sheet->getStyle('H5:H'.$lastRow)->getAlignment()->setWrapText(true);

        // Set fixed width yang lebih kecil
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);

        // Set row height
        for($row = 5; $row <= $lastRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(-1);
        }

        // Set vertical alignment
        $sheet->getStyle('F5:H'.$lastRow)
            ->getAlignment()
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        // Freeze pane pada nama item
        $sheet->freezePane('D5');

        return [
            4 => ['font' => ['bold' => true]], // Header sekarang di baris 4
        ];
    }

    public function map($item): array
    {
        static $counter = 0;
        $counter++;

        // Format availability
        $availability = $item->availabilities->map(function($avail) {
            if($avail->availability_type == 'region') {
                return 'Region: ' . ($avail->region->name ?? '-');
            } elseif($avail->availability_type == 'outlet') {
                return 'Outlet: ' . ($avail->outlet->nama_outlet ?? '-');
            } elseif($avail->availability_type == 'all') {
                return 'All Regions';
            }
            return '-';
        })->join(", ");

        // Format prices
        $prices = $item->prices->map(function($price) {
            return $price->region->name . ": Rp " . number_format($price->price, 0, ',', '.');
        })->join(", ");

        return [
            $counter,
            $item->sku,
            $item->name,
            $item->category->name ?? '-',
            $item->subCategory->name ?? '-',
            $item->description ?? '-',
            $item->specification ?? '-',
            $availability,
            $item->smallUnit->name ?? '-',
            $item->mediumUnit->name ?? '-',
            $item->largeUnit->name ?? '-',
            $item->status == 'active' ? 'Aktif' : 'Tidak Aktif',
            $prices
        ];
    }
}