<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\MaintenanceTask;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use DB;

class TaskStatusExport implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;
    protected $taskStatusData;
    protected $tasks;

    public function __construct($startDate, $endDate, $taskStatusData, $tasks)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->taskStatusData = $taskStatusData;
        $this->tasks = $tasks;
    }

    public function sheets(): array
    {
        return [
            new TaskStatusSummarySheet($this->startDate, $this->endDate, $this->taskStatusData),
            new TaskDetailsSheet($this->startDate, $this->endDate, $this->tasks),
        ];
    }
}

class TaskStatusSummarySheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $taskStatusData;

    public function __construct($startDate, $endDate, $taskStatusData)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->taskStatusData = $taskStatusData;
    }

    public function collection()
    {
        $data = collect();
        
        $totalTasks = array_sum($this->taskStatusData);
        
        foreach ($this->taskStatusData as $status => $count) {
            $percentage = $totalTasks > 0 ? round(($count / $totalTasks) * 100, 2) . '%' : '0%';
            
            $data->push([
                'Status' => $status,
                'Count' => $count,
                'Percentage' => $percentage
            ]);
        }
        
        $data->push([
            'Status' => 'Total',
            'Count' => $totalTasks,
            'Percentage' => '100%'
        ]);
        
        return $data;
    }

    public function headings(): array
    {
        return [
            ['TASK STATUS REPORT'],
            ["Period: " . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y')],
            [''], // Empty row
            ['Status', 'Count', 'Percentage']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge title cells
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A2:C2');
        
        // Style for title
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Style for header row
        $sheet->getStyle('A4:C4')->getFont()->setBold(true);
        $sheet->getStyle('A4:C4')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDEBF7');
        
        // Style for the last row (total row)
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A{$lastRow}:C{$lastRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$lastRow}:C{$lastRow}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDEBF7');
        
        // Add borders to all data cells
        $sheet->getStyle('A4:C' . $lastRow)->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Summary';
    }
}

class TaskDetailsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $tasks;

    public function __construct($startDate, $endDate, $tasks)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->tasks = $tasks;
    }

    public function collection()
    {
        $data = collect();
        
        $counter = 1;
        foreach ($this->tasks as $task) {
            // Ambil data priority dari tabel maintenance_priorities
            $priority = '';
            if (isset($task->priority_id)) {
                $priorityData = DB::table('maintenance_priorities')
                    ->where('id', $task->priority_id)
                    ->first();
                $priority = $priorityData ? $priorityData->priority : ($task->priority ?? 'N/A');
            } else {
                $priority = $task->priority ?? 'N/A';
            }
            
            // Ambil data members dari relasi
            $members = DB::table('maintenance_members')
                ->join('users', 'maintenance_members.user_id', '=', 'users.id')
                ->where('maintenance_members.task_id', $task->id)
                ->select('users.nama_lengkap', 'maintenance_members.role')
                ->get();
            
            $membersList = '';
            foreach ($members as $member) {
                $membersList .= ($member->nama_lengkap ?? 'Unknown') . ' (' . ($member->role ?? 'Unknown') . '), ';
            }
            $membersList = rtrim($membersList, ', ');
            
            // Ambil data PO jika ada
            $po = DB::table('maintenance_purchase_orders')
                ->where('task_id', $task->id)
                ->select(
                    DB::raw('SUM(total_amount) as total_po'), 
                    DB::raw('GROUP_CONCAT(po_number SEPARATOR ", ") as po_numbers')
                )
                ->first();
            
            $poNumbers = $po && $po->po_numbers ? $po->po_numbers : 'N/A';
            $poTotal = $po && $po->total_po ? 'Rp ' . number_format($po->total_po, 0, ',', '.') : 'N/A';
            
            // Ambil data lokasi (outlet dan ruko)
            $location = 'N/A';
            if ($task->id_outlet) {
                $outlet = DB::table('tbl_data_outlet')
                    ->where('id_outlet', $task->id_outlet)
                    ->first();
                
                if ($outlet) {
                    if ($task->id_outlet == 1 && $task->id_ruko) {
                        // Jika id_outlet = 1 (Head Office), ambil data ruko
                        $ruko = DB::table('tbl_data_ruko')
                            ->where('id_ruko', $task->id_ruko)
                            ->first();
                        
                        $location = 'Head Office - ' . ($ruko ? $ruko->nama_ruko : 'Ruko N/A');
                    } else {
                        // Jika bukan Head Office, tampilkan hanya nama outlet
                        $location = $outlet->nama_outlet;
                    }
                }
            }
            
            // Hitung overdue (due date - completed date)
            $overdue = 'N/A';
            if ($task->due_date) {
                $dueDate = $task->due_date instanceof \Carbon\Carbon 
                    ? $task->due_date 
                    : \Carbon\Carbon::parse($task->due_date);
                
                if ($task->status === 'DONE' && $task->completed_at) {
                    // Jika task sudah selesai, hitung selisih dari due date sampai completed date
                    $completedDate = $task->completed_at instanceof \Carbon\Carbon 
                        ? $task->completed_at 
                        : \Carbon\Carbon::parse($task->completed_at);
                    
                    // Jika completed date lebih dari due date, berarti overdue
                    if ($completedDate->gt($dueDate)) {
                        $overdueDays = $dueDate->diffInDays($completedDate);
                        $overdue = $overdueDays . ' hari';
                    } else {
                        $overdue = '0 hari (tepat waktu)';
                    }
                } else if ($task->status !== 'DONE') {
                    // Jika task belum selesai, hitung selisih dari due date sampai sekarang
                    $today = \Carbon\Carbon::now();
                    
                    // Jika today lebih dari due date, berarti sedang overdue
                    if ($today->gt($dueDate)) {
                        $overdueDays = $dueDate->diffInDays($today);
                        $overdue = $overdueDays . ' hari (masih berlangsung)';
                    } else {
                        $overdue = 'Belum jatuh tempo';
                    }
                }
            }
            
            $data->push([
                'No' => $counter,
                'Task ID' => $task->task_number,
                'Title' => $task->title,
                'Status' => $task->status,
                'Priority' => $priority,
                'Members' => $membersList ?: 'N/A',
                'Created By' => $task->creator ? $task->creator->name : 'Unknown',
                'Created Date' => $task->created_at instanceof \Carbon\Carbon 
                    ? $task->created_at->format('Y-m-d H:i:s') 
                    : \Carbon\Carbon::parse($task->created_at)->format('Y-m-d H:i:s'),
                'Due Date' => $task->due_date 
                    ? (is_string($task->due_date) ? $task->due_date : \Carbon\Carbon::parse($task->due_date)->format('Y-m-d')) 
                    : 'N/A',
                'Completed Date' => $task->completed_at 
                    ? (is_string($task->completed_at) ? $task->completed_at : \Carbon\Carbon::parse($task->completed_at)->format('Y-m-d H:i:s')) 
                    : 'N/A',
                'Overdue' => $overdue,
                'PO Numbers' => $poNumbers,
                'PO Total' => $poTotal,
                'Location' => $location,
            ]);
            
            $counter++;
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            ['DETAILED TASK LIST'],
            ["Period: " . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y')],
            [''], // Empty row
            [
                'No', 
                'Task ID', 
                'Title', 
                'Status', 
                'Priority', 
                'Members', 
                'Created By', 
                'Created Date', 
                'Due Date', 
                'Completed Date',
                'Overdue',
                'PO Numbers',
                'PO Total',
                'Location'
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Calculate last column letter
        $lastColumn = 'N'; // Kolom terakhir (Location)
        
        // Merge title cells
        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->mergeCells("A2:{$lastColumn}2");
        
        // Style for title
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Style for header row
        $sheet->getStyle("A4:{$lastColumn}4")->getFont()->setBold(true);
        $sheet->getStyle("A4:{$lastColumn}4")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDEBF7');
        
        // Add borders to all data cells
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A4:{$lastColumn}{$lastRow}")->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Set column width for specific columns
        $sheet->getColumnDimension('C')->setWidth(40); // Title
        $sheet->getColumnDimension('F')->setWidth(30); // Members
        $sheet->getColumnDimension('K')->setWidth(25); // Overdue
        
        // Kondisional formatting baru untuk overdue cells
        
        // 1. Untuk tepat waktu (0 hari atau tepat waktu) - warna hijau
        $conditionalOnTime = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditionalOnTime->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
        $conditionalOnTime->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
        $conditionalOnTime->setText('tepat waktu');
        $conditionalOnTime->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $conditionalOnTime->getStyle()->getFill()->getEndColor()->setARGB('FFD4EDDA'); // Light green
        $conditionalOnTime->getStyle()->getFont()->getColor()->setARGB('FF155724'); // Dark green text
        
        // 2. Untuk belum jatuh tempo - warna hijau yang sama
        $conditionalInTime = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditionalInTime->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
        $conditionalInTime->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
        $conditionalInTime->setText('Belum jatuh tempo');
        $conditionalInTime->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $conditionalInTime->getStyle()->getFill()->getEndColor()->setARGB('FFD4EDDA'); // Light green
        $conditionalInTime->getStyle()->getFont()->getColor()->setARGB('FF155724'); // Dark green text
        
        // 3. Untuk overdue (hari) - warna merah
        $conditionalOverdue = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditionalOverdue->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
        $conditionalOverdue->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
        $conditionalOverdue->setText('hari (masih berlangsung)');
        $conditionalOverdue->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $conditionalOverdue->getStyle()->getFill()->getEndColor()->setARGB('FFF8D7DA'); // Light red
        $conditionalOverdue->getStyle()->getFont()->getColor()->setARGB('FF721C24'); // Dark red text
        
        // 4. Untuk task selesai tapi overdue - warna merah
        $conditionalLate = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
        $conditionalLate->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_EXPRESSION);
        $conditionalLate->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_GREATERTHAN);
        $conditionalLate->setText('0'); // Nilai overdue lebih dari 0
        $conditionalLate->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $conditionalLate->getStyle()->getFill()->getEndColor()->setARGB('FFF8D7DA'); // Light red
        $conditionalLate->getStyle()->getFont()->getColor()->setARGB('FF721C24'); // Dark red text
        
        // Gabungkan semua kondisi (urutan penting karena kondisi yang paling spesifik harus lebih dulu)
        $conditionalStyleArray = [
            $conditionalOnTime,
            $conditionalInTime,
            $conditionalOverdue,
            $conditionalLate
        ];
        
        // Terapkan conditional formatting ke kolom Overdue
        $sheet->getStyle("K5:K{$lastRow}")->setConditionalStyles($conditionalStyleArray);
        
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Task Details';
    }
}
