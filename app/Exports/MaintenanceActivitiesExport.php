<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaintenanceActivitiesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $activities;

    public function __construct($activities)
    {
        $this->activities = $activities;
    }

    public function collection()
    {
        return $this->activities;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal',
            'User',
            'Aktivitas',
            'Deskripsi',
            'Task Number',
            'Task Title'
        ];
    }

    public function map($activity): array
    {
        return [
            $activity->id,
            $activity->created_at->format('d M Y H:i:s'),
            $activity->user->nama_lengkap ?? 'System',
            $activity->action,
            $activity->description,
            $activity->task->task_number ?? '-',
            $activity->task->title ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
