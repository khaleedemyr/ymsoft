<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TasksByPriorityExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $tasks;

    public function __construct($tasks)
    {
        $this->tasks = $tasks;
    }

    public function collection()
    {
        return $this->tasks;
    }

    public function headings(): array
    {
        return [
            'Task ID',
            'Task Number',
            'Title',
            'Description',
            'Priority',
            'Due Date',
            'Created At',
            'Completed At',
            'Status'
        ];
    }

    public function map($task): array
    {
        // Format data untuk export
        return [
            $task->id,
            $task->task_number,
            $task->title,
            $task->description,
            $task->priority_name,
            $task->due_date ? date('d M Y', strtotime($task->due_date)) : '-',
            date('d M Y H:i', strtotime($task->created_at)),
            $task->completed_at ? date('d M Y H:i', strtotime($task->completed_at)) : '-',
            $this->getStatusLabel($task->status)
        ];
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'TASK' => 'Tugas Baru',
            'IN_PROGRESS' => 'Dalam Proses',
            'PR' => 'Purchase Requisition',
            'PO' => 'Purchase Order',
            'IN_REVIEW' => 'Dalam Review',
            'DONE' => 'Selesai',
            'CANCELLED' => 'Dibatalkan'
        ];

        return $labels[$status] ?? $status;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
