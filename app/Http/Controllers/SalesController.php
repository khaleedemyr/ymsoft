<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\SubCategory;
use App\Models\SalesHeader;
use App\Models\SalesDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SalesImport;
use App\Traits\LogActivity;

class SalesController extends Controller
{
    use LogActivity;

    public function __construct()
    {
        ini_set('max_input_vars', '3000');  // atau nilai yang lebih tinggi sesuai kebutuhan
    }

    public function index()
    {
        $salesData = [];
        $sales = SalesHeader::with(['customer', 'details'])->get();
        return view('sales.index', compact('sales', 'salesData'));
    }

    public function uploadForm()
    {
        $salesData = [];
        return view('sales.upload', compact('salesData'));
    }

    public function preview(Request $request)
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json([
                    'error' => 'Tidak ada file yang diupload'
                ]);
            }

            $file = $request->file('file');
            $import = new SalesImport();
            $data = Excel::toCollection($import, $file);
            
            $salesData = [];
            
            if ($data->count() > 0 && $data[0]->count() > 0) {
                $rows = $data[0]->skip(1); // Skip header row
                
                foreach ($rows as $index => $row) {
                    // Skip baris yang kosong
                    if (empty($row['customer']) && empty($row['invoice_document']) && 
                        empty($row['sales_date']) && empty($row['items'])) {
                        continue;
                    }

                    // Validasi data wajib
                    if (empty($row['customer'])) {
                        throw new \Exception("Customer tidak boleh kosong pada baris " . ($index + 2));
                    }
                    if (empty($row['invoice_document'])) {
                        throw new \Exception("Invoice Document tidak boleh kosong pada baris " . ($index + 2));
                    }
                    if (empty($row['sales_date'])) {
                        throw new \Exception("Sales Date tidak boleh kosong pada baris " . ($index + 2));
                    }
                    if (empty($row['items'])) {
                        throw new \Exception("Items tidak boleh kosong pada baris " . ($index + 2));
                    }
                    if (!isset($row['uom']) || trim((string)$row['uom']) === '') {
                        throw new \Exception("UOM tidak boleh kosong pada baris " . ($index + 2));
                    }

                    $customerName = trim($row['customer']);
                    $customer = Customer::select('customers.*', 'regions.id as region_id')
                        ->join('regions', 'regions.code', '=', 'customers.region')
                        ->where('customers.name', $customerName)
                        ->first();

                    if (!$customer) {
                        throw new \Exception("Customer tidak ditemukan: {$customerName}");
                    }

                    $subCategory = SubCategory::where('name', trim($row['sub_category']))->first();
                    if (!$subCategory) {
                        throw new \Exception("Sub kategori tidak ditemukan: {$row['sub_category']}");
                    }

                    $item = Item::where('name', trim($row['items']))->first();
                    if (!$item) {
                        throw new \Exception("Item tidak ditemukan: {$row['items']}");
                    }

                    $price = $item->prices()
                        ->where('region_id', $customer->region_id)
                        ->first();

                    if (!$price) {
                        throw new \Exception("Harga tidak ditemukan untuk item {$row['items']} di region {$customer->region}");
                    }

                    // Konversi harga dari unit besar ke unit sedang
                    $mediumConversionQty = $item->medium_conversion_qty;
                    if (!$mediumConversionQty || $mediumConversionQty <= 0) {
                        throw new \Exception("Konversi unit sedang tidak valid untuk item {$row['items']}");
                    }

                    // Harga unit sedang = Harga unit besar / medium_conversion_qty
                    $mediumUnitPrice = $price->price / $mediumConversionQty;

                    // Gunakan harga unit sedang untuk perhitungan
                    $amount = ceil($mediumUnitPrice * $row['inv_qty'] / 100) * 100;

                    $invoiceDoc = trim($row['invoice_document']);

                    if (!isset($salesData[$customerName][$invoiceDoc])) {
                        $salesData[$customerName][$invoiceDoc] = [
                            'customer_id' => $customer->id,
                            'sales_date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['sales_date'])->format('Y-m-d'),
                            'delivery_number' => $row['delivery_number'],
                            'items' => []
                        ];
                    }

                    $salesData[$customerName][$invoiceDoc]['items'][] = [
                        'sub_category' => $row['sub_category'],
                        'sub_category_id' => $subCategory->id,
                        'item' => $row['items'],
                        'item_id' => $item->id,
                        'quantity' => $row['inv_qty'],
                        'uom' => trim((string)$row['uom']),
                        'price' => $price->price,
                        'amount' => $amount
                    ];

                    \Log::info("Processed row " . ($index + 2) . ":", [
                        'customer' => $customerName,
                        'invoice' => $invoiceDoc,
                        'uom' => trim((string)$row['uom'])
                    ]);
                }
            }

            if (empty($salesData)) {
                throw new \Exception("Tidak ada data valid yang dapat diproses");
            }

            session(['preview_data' => $salesData]);
            
            $this->logActivity(
                'SALES',
                'VIEW',
                'Melihat preview data penjualan yang diupload',
                null,
                null
            );
            
            return response()->json([
                'success' => true,
                'redirect' => route('sales.show_preview')
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Error in preview: " . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
            ]);
        }
    }

    public function showPreview()
    {
        $salesData = session('preview_data');
        
        if (empty($salesData)) {
            return redirect()->route('sales.upload')
                            ->with('error', 'Tidak ada data preview yang tersedia');
        }
        
        return view('sales.preview', compact('salesData'));
    }

    private function convertExcelDate($excelDate)
    {
        if (is_numeric($excelDate)) {
            // Excel date dimulai dari 1900-01-01
            $unixDate = ($excelDate - 25569) * 86400;
            return date('Y-m-d', $unixDate);
        } else {
            // Jika bukan numeric, coba parse sebagai string date
            try {
                return date('Y-m-d', strtotime($excelDate));
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    public function getProgress()
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            if (!file_exists($logPath)) {
                return response()->json(['progress' => 0]);
            }

            $logContent = file_get_contents($logPath);
            if (empty($logContent)) {
                return response()->json(['progress' => 0]);
            }

            // Cari progress terakhir
            preg_match_all('/Processed row (\d+) of (\d+) \(([\d.]+)%\)/', $logContent, $matches, PREG_SET_ORDER);
            
            if (empty($matches)) {
                return response()->json(['progress' => 0]);
            }

            // Ambil entri terakhir
            $lastMatch = end($matches);
            $currentRow = $lastMatch[1];
            $totalRows = $lastMatch[2];
            $progress = $lastMatch[3];

            $response = [
                'currentRow' => (int)$currentRow,
                'totalRows' => (int)$totalRows,
                'progress' => (float)$progress
            ];

            // Tambahkan URL redirect jika progress sudah 100%
            if ((float)$progress >= 100) {
                $response['redirect'] = route('sales.preview');
            }

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Error getting progress: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal mendapatkan status progress'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        try {
            // Terima data sebagai JSON
            $data = $request->json()->all();
            
            if (!isset($data['sales'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data penjualan tidak ditemukan'
                ], 400);
            }

            $salesData = $data['sales'];
            
            // Validasi format data
            if (!is_array($salesData)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Format data tidak valid'
                ], 400);
            }

            $processedHeaders = 0;
            $processedDetails = 0;
            $totalInvoices = 0;
            $totalItems = 0;
            $chunkSize = 50;
            
            // Hitung total data
            foreach ($salesData as $invoices) {
                $totalInvoices += count($invoices);
                foreach ($invoices as $sale) {
                    if (isset($sale['items'])) {
                        $totalItems += count($sale['items']);
                    }
                }
            }
            
            \Log::info("Starting process for {$totalInvoices} invoices with approximately {$totalItems} items");
            
            // Loop through each customer
            foreach ($salesData as $customerName => $invoices) {
                // Loop through each invoice
                foreach ($invoices as $invoiceNumber => $sale) {
                    DB::beginTransaction();
                    try {
                        if (!isset($sale['items']) || !is_array($sale['items'])) {
                            \Log::warning("Skipping invoice {$invoiceNumber} - no valid items array");
                            continue;
                        }

                        // Filter valid items first
                        $validItems = array_filter($sale['items'], function($item) {
                            return !empty($item['item_id']) && 
                                   !empty($item['sub_category_id']) && 
                                   isset($item['quantity']) && 
                                   !empty($item['uom']) && 
                                   isset($item['price']) && 
                                   isset($item['amount']);
                        });

                        if (empty($validItems)) {
                            \Log::warning("Skipping invoice {$invoiceNumber} - no valid items after filtering");
                            continue;
                        }

                        // Save header
                        $salesHeader = new SalesHeader();
                        $salesHeader->customer_id = $sale['customer_id'];
                        $salesHeader->sales_date = $sale['sales_date'];
                        $salesHeader->invoice_document = $invoiceNumber;
                        $salesHeader->delivery_number = $sale['delivery_number'];
                        $salesHeader->total_amount = array_sum(array_column($validItems, 'amount'));
                        $salesHeader->status = 'draft';
                        $salesHeader->save();

                        $processedHeaders++;

                        // Save details
                        foreach ($validItems as $item) {
                            $salesDetail = new SalesDetail();
                            $salesDetail->sales_header_id = $salesHeader->id;
                            $salesDetail->item_id = $item['item_id'];
                            $salesDetail->sub_category_id = $item['sub_category_id'];
                            $salesDetail->quantity = $item['quantity'];
                            $salesDetail->uom = $item['uom'];
                            $salesDetail->price = $item['price'];
                            $salesDetail->amount = $item['amount'];
                            $salesDetail->save();

                            $processedDetails++;
                        }

                        DB::commit();
                        \Log::info("Successfully processed invoice {$invoiceNumber} with " . count($validItems) . " items");

                    } catch (\Exception $e) {
                        DB::rollback();
                        \Log::error("Error processing invoice {$invoiceNumber}: " . $e->getMessage());
                        \Log::error("Stack trace: " . $e->getTraceAsString());
                        // Continue to next invoice instead of throwing
                        continue;
                    }
                }
            }

            $successMessage = "Berhasil memproses {$processedHeaders} dari {$totalInvoices} invoice";
            \Log::info($successMessage);

            $this->logActivity(
                'SALES',
                'CREATE',
                $successMessage,
                null,
                null
            );

            return response()->json([
                'status' => true,
                'message' => __('translation.sales.success_save'),
                'data' => [
                    'total_invoices' => $totalInvoices,
                    'processed_headers' => $processedHeaders,
                    'total_items' => $totalItems,
                    'processed_details' => $processedDetails
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error("Fatal error: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 400);
        }
    }

    public function downloadTemplate()
    {
        // Create new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'Customer',
            'Sub Category',
            'Invoice Document',
            'Sales Date',
            'Items',
            'Delivery Number',
            'INV (Qty)',
            'UOM'
        ];

        // Style for headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4B5563'],
            ],
        ];

        // Add headers
        foreach ($headers as $key => $header) {
            $column = chr(65 + $key); // Convert number to letter (0 = A, 1 = B, etc)
            $sheet->setCellValue($column . '1', $header);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Style the header row
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Add example data
        $exampleData = [
            ['Customer A', 'Category 1', 'INV-001', '2024-03-20', 'Item 1', 'DO-001', '10', 'PCS'],
            ['Customer A', 'Category 2', 'INV-001', '2024-03-20', 'Item 2', 'DO-001', '5', 'BOX'],
            ['Customer B', 'Category 1', 'INV-002', '2024-03-21', 'Item 1', 'DO-002', '15', 'PCS'],
        ];

        // Add example data starting from row 2
        $row = 2;
        foreach ($exampleData as $data) {
            foreach ($data as $key => $value) {
                $column = chr(65 + $key);
                $sheet->setCellValue($column . $row, $value);
            }
            $row++;
        }

        // Create writer
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $fileName = 'sales_template.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function getDetails($id)
    {
        try {
            $sale = SalesHeader::with(['customer', 'details.item', 'details.subCategory'])
                ->findOrFail($id);
            
            $details = $sale->details->map(function($detail) {
                return [
                    'sub_category' => $detail->subCategory->name,
                    'item' => $detail->item->name,
                    'quantity' => $detail->quantity,
                    'uom' => $detail->uom,
                    'price' => $detail->price,
                    'amount' => $detail->amount
                ];
            });

            return response()->json([
                'customer' => $sale->customer->name,
                'invoice_document' => $sale->invoice_document,
                'sales_date' => \Carbon\Carbon::parse($sale->sales_date)->format('d/m/Y'),
                'delivery_number' => $sale->delivery_number,
                'details' => $details
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting sale details: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memuat detail penjualan'
            ], 500);
        }
    }

    public function upload(Request $request)
    {
        try {
            // ... kode upload yang sudah ada ...

            $this->logActivity(
                'SALES',
                'UPLOAD',
                'Mengupload data penjualan dari Excel',
                null,
                null
            );
            
            return response()->json([
                'success' => true,
                'message' => __('translation.sales.success_upload')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
