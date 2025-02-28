<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SubCategory;
use App\Models\SalesHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Traits\LogActivity;

class ReportFJController extends Controller
{
    use LogActivity;

    public function index()
    {
        $subCategories = SubCategory::orderBy('name')->get();
        return view('reports.fj.index', compact('subCategories'));
    }

    public function getData(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $search = $request->input('search');

            $query = Customer::select([
                'customers.id',
                'customers.name as customer_name',
                'customers.type as customer_type',
                DB::raw('SUM(sales_details.amount) as line_total')
            ])
            ->leftJoin('sales_headers', 'customers.id', '=', 'sales_headers.customer_id')
            ->leftJoin('sales_details', 'sales_headers.id', '=', 'sales_details.sales_header_id')
            ->whereBetween('sales_headers.sales_date', [$startDate, $endDate])
            ->when($search, function($q) use ($search) {
                return $q->where('customers.name', 'like', "%{$search}%");
            })
            ->groupBy('customers.id', 'customers.name', 'customers.type');

            // Get all sub categories
            $subCategories = SubCategory::orderBy('name')->get();

            // Add sub category amounts to select
            foreach ($subCategories as $subCategory) {
                $query->addSelect([
                    DB::raw("SUM(CASE WHEN sales_details.sub_category_id = {$subCategory->id} 
                             THEN sales_details.amount ELSE 0 END) as category_{$subCategory->id}")
                ]);
            }

            $data = $query->get();

            $this->logActivity(
                'REPORT',
                'view',
                'Melihat Report FJ dari tanggal ' . $request->start_date . ' sampai ' . $request->end_date,
                null,
                null
            );
            
            return response()->json([
                'data' => $data,
                'subCategories' => $subCategories
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $search = $request->input('search');

            // Ambil data
            $query = Customer::select([
                'customers.id',
                'customers.name as customer_name',
                'customers.type as customer_type',
                DB::raw('SUM(sales_details.amount) as line_total')
            ])
            ->leftJoin('sales_headers', 'customers.id', '=', 'sales_headers.customer_id')
            ->leftJoin('sales_details', 'sales_headers.id', '=', 'sales_details.sales_header_id')
            ->whereBetween('sales_headers.sales_date', [$startDate, $endDate])
            ->when($search, function($q) use ($search) {
                return $q->where('customers.name', 'like', "%{$search}%");
            })
            ->groupBy('customers.id', 'customers.name', 'customers.type');

            // Get sub categories
            $subCategories = SubCategory::orderBy('name')->get();

            // Add sub category amounts
            foreach ($subCategories as $subCategory) {
                $query->addSelect([
                    DB::raw("SUM(CASE WHEN sales_details.sub_category_id = {$subCategory->id} 
                             THEN sales_details.amount ELSE 0 END) as category_{$subCategory->id}")
                ]);
            }

            $data = $query->get();

            // Buat spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set judul report
            $sheet->setCellValue('A1', 'REPORT FJ');
            $sheet->mergeCells('A1:F1');
            
            // Set informasi periode
            $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));
            $sheet->mergeCells('A2:F2');
            
            // Get logged in user's full name
            $user = User::select('nama_lengkap')
                       ->where('id', Auth::id())
                       ->first();
            
            $userName = $user ? $user->nama_lengkap : Auth::user()->name;
            
            // Set informasi download
            $sheet->setCellValue('A3', 'Downloaded by: ' . $userName);
            $sheet->setCellValue('A4', 'Download date: ' . now()->format('d/m/Y H:i:s'));
            $sheet->mergeCells('A3:F3');
            $sheet->mergeCells('A4:F4');

            // Mulai header tabel dari baris 6
            $headerRow = 6;
            $sheet->setCellValue('A'.$headerRow, __('translation.reports.fj.table.customer'));
            $sheet->setCellValue('B'.$headerRow, __('translation.reports.fj.table.type'));
            $sheet->setCellValue('C'.$headerRow, __('translation.reports.fj.table.line_total'));

            // Set sub category headers
            $col = 'D';
            foreach ($subCategories as $subCategory) {
                $sheet->setCellValue($col.$headerRow, $subCategory->name);
                $col++;
            }

            // Style untuk judul
            $sheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 14
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ]
            ]);

            // Style untuk informasi periode dan download
            $sheet->getStyle('A2:A4')->applyFromArray([
                'font' => [
                    'size' => 11
                ]
            ]);

            // Set data tanpa format number
            $row = $headerRow + 1;
            $lastColumn = $col; // Simpan kolom terakhir sebelum decrement
            $lastColumn--; // Kurangi satu karena $col sudah increment satu kali lebih banyak
            
            foreach ($data as $item) {
                $sheet->setCellValue('A'.$row, $item->customer_name);
                $sheet->setCellValue('B'.$row, $item->customer_type);
                $sheet->setCellValue('C'.$row, $item->line_total);

                $col = 'D';
                foreach ($subCategories as $subCategory) {
                    $categoryAmount = $item->{'category_'.$subCategory->id};
                    $sheet->setCellValue($col.$row, $categoryAmount);
                    $col++;
                }
                $row++;
            }

            // Perbaikan auto width columns
            foreach (range('A', $lastColumn) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(false);
                
                // Hitung max length untuk setiap kolom
                $maxLength = 0;
                foreach ($sheet->getColumnIterator($columnID) as $cell) {
                    foreach ($cell->getWorksheet()->getRowIterator() as $rowIndex) {
                        $cellValue = $sheet->getCell($columnID . $rowIndex->getRowIndex())->getValue();
                        $textLength = strlen($cellValue);
                        $maxLength = max($maxLength, $textLength);
                    }
                }
                
                $sheet->getColumnDimension($columnID)->setWidth($maxLength * 1.2);
            }

            // Style untuk header tabel
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4B88FF']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ];
            $sheet->getStyle('A'.$headerRow.':'.$lastColumn.$headerRow)->applyFromArray($headerStyle);

            // Style untuk data
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ];
            $sheet->getStyle('A'.($headerRow + 1).':'.$lastColumn.($row-1))->applyFromArray($dataStyle);

            // Style khusus untuk kolom angka
            $numberStyle = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT
                ]
            ];
            $sheet->getStyle('C'.($headerRow + 1).':'.$lastColumn.($row-1))->applyFromArray($numberStyle);

            // Pastikan header tidak terpotong
            $sheet->getStyle('A'.$headerRow.':'.$lastColumn.$headerRow)->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($headerRow)->setRowHeight(-1);

            // Freeze pane
            $sheet->freezePane('D'.($headerRow + 1));

            // Set print area dengan nilai yang benar
            $printArea = 'A1:' . $lastColumn . ($row-1);
            $sheet->getPageSetup()->setPrintArea($printArea);
            
            // Set print area
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);
            $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

            // Create Excel file
            $writer = new Xlsx($spreadsheet);
            $filename = 'Report_FJ_'.date('Y-m-d').'.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');

            $this->logActivity(
                'REPORT',
                'EXPORT',
                'Men-download Report FJ dari tanggal ' . $request->start_date . ' sampai ' . $request->end_date,
                null,
                null
            );
            
        } catch (\Exception $e) {
            \Log::error('Export error: ' . $e->getMessage());
            return redirect()->back()->with('error', __('translation.reports.fj.export.error'));
        }
    }
} 