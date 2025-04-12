<?php

namespace App\Exports\Finance;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentPlanningExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting
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
            trans('translation.payment_planning.table.contra_bon_number'),
            trans('translation.payment_planning.table.supplier'),
            trans('translation.payment_planning.table.issue_date'),
            trans('translation.payment_planning.table.due_date'),
            trans('translation.payment_planning.table.days_until_due'),
            trans('translation.payment_planning.table.total_amount'),
            trans('translation.payment_planning.table.paid_amount'),
            trans('translation.payment_planning.table.remaining_amount'),
        ];
    }

    public function map($row): array
    {
        return [
            $row->contra_bon_number,
            $row->supplier_name,
            $row->issue_date,
            $row->due_date,
            $row->days_until_due,
            $row->total_amount,
            $row->paid_amount,
            $row->remaining_amount,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'E:H' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
} 