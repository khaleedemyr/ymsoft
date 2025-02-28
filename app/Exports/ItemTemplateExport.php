<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Maatwebsite\Excel\Events\AfterSheet;

class ItemTemplateExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    public function array(): array
    {
        return [
            [
                'ABC123', // SKU
                'Contoh Nama Barang', // Nama
                '1', // Category ID
                '1', // Sub Category ID
                '1', // Small Unit ID
                '1', // Medium Unit ID
                '1', // Large Unit ID
                '12', // Medium Conversion
                '24', // Small Conversion
                '1|100000,2|150000', // Prices (Format: region_id|price,region_id|price)
            ]
        ];
    }

    public function headings(): array
    {
        return [
            ['TEMPLATE IMPORT BARANG'],
            ['Catatan:'],
            ['- Category ID bisa dilihat di menu Category'],
            ['- Sub Category ID bisa dilihat di menu Sub Category'],
            ['- Unit ID bisa dilihat di menu Unit'],
            ['- Format harga: region_id|price,region_id|price (Contoh: 1|100000,2|150000)'],
            ['- Baris contoh dibawah ini bisa dihapus'],
            [
                'SKU',
                'Nama',
                'Category ID',
                'Sub Category ID',
                'Small Unit ID',
                'Medium Unit ID',
                'Large Unit ID',
                'Konversi ke Satuan Sedang',
                'Konversi ke Satuan Kecil',
                'Harga (region_id|price)'
            ]
        ];
    }

    public function styles($sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
            8 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '92D050']
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->mergeCells('A1:J1');
                $event->sheet->mergeCells('A2:J2');
                $event->sheet->mergeCells('A3:J3');
                $event->sheet->mergeCells('A4:J4');
                $event->sheet->mergeCells('A5:J5');
                $event->sheet->mergeCells('A6:J6');
                $event->sheet->mergeCells('A7:J7');
                
                // Set columns width
                foreach(range('A','J') as $col) {
                    $event->sheet->getColumnDimension($col)->setWidth(20);
                }
            }
        ];
    }
} 