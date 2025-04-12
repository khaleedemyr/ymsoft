<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\AgingReportExport;
use Maatwebsite\Excel\Facades\Excel;

class AgingReportController extends Controller
{
    public function index()
    {
        return view('finance.aging-report.index');
    }

    public function data(Request $request)
    {
        try {
            $query = "
                SELECT 
                    supplier_id,
                    supplier_name,
                    SUM(CASE WHEN days_overdue <= 0 THEN remaining_amount ELSE 0 END) as current,
                    SUM(CASE WHEN days_overdue BETWEEN 1 AND 30 THEN remaining_amount ELSE 0 END) as days_1_30,
                    SUM(CASE WHEN days_overdue BETWEEN 31 AND 60 THEN remaining_amount ELSE 0 END) as days_31_60,
                    SUM(CASE WHEN days_overdue BETWEEN 61 AND 90 THEN remaining_amount ELSE 0 END) as days_61_90,
                    SUM(CASE WHEN days_overdue > 90 THEN remaining_amount ELSE 0 END) as days_over_90,
                    SUM(remaining_amount) as total
                FROM (
                    SELECT 
                        pi.supplier_id,
                        s.name as supplier_name,
                        pi.invoice_number,
                        pi.invoice_date,
                        pi.due_date,
                        pi.grand_total as invoice_amount,
                        COALESCE(SUM(p.amount), 0) as paid_amount,
                        (pi.grand_total - COALESCE(SUM(p.amount), 0)) as remaining_amount,
                        DATEDIFF(CURDATE(), pi.due_date) as days_overdue
                    FROM purchase_invoices pi
                    JOIN suppliers s ON pi.supplier_id = s.id
                    LEFT JOIN contra_bon_invoices cbi ON pi.id = cbi.purchase_invoice_id
                    LEFT JOIN contra_bons cb ON cbi.contra_bon_id = cb.id
                    LEFT JOIN payments p ON cb.id = p.contra_bon_id
                    WHERE pi.status = 'approved'
            ";

            // Tambahkan filter pencarian jika ada
            if ($request->filled('search')) {
                $search = $request->search;
                $query .= " AND s.name LIKE '%{$search}%'";
            }

            $query .= "
                    GROUP BY 
                        pi.id, 
                        pi.supplier_id, 
                        s.name, 
                        pi.invoice_number, 
                        pi.invoice_date, 
                        pi.due_date, 
                        pi.grand_total
                    HAVING remaining_amount > 0
                ) as unpaid_invoices
                GROUP BY supplier_id, supplier_name
                HAVING total > 0
                ORDER BY total DESC
            ";

            $data = DB::select($query);

            return response()->json([
                'status' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in Aging Report data: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'status' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $query = DB::select("
                SELECT 
                    s.id as supplier_id,
                    s.name as supplier_name,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), pi.due_date) <= 0 THEN (pi.grand_total - COALESCE(p.paid_amount, 0)) ELSE 0 END) as current,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), pi.due_date) BETWEEN 1 AND 30 THEN (pi.grand_total - COALESCE(p.paid_amount, 0)) ELSE 0 END) as days_1_30,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), pi.due_date) BETWEEN 31 AND 60 THEN (pi.grand_total - COALESCE(p.paid_amount, 0)) ELSE 0 END) as days_31_60,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), pi.due_date) BETWEEN 61 AND 90 THEN (pi.grand_total - COALESCE(p.paid_amount, 0)) ELSE 0 END) as days_61_90,
                    SUM(CASE WHEN DATEDIFF(CURDATE(), pi.due_date) > 90 THEN (pi.grand_total - COALESCE(p.paid_amount, 0)) ELSE 0 END) as days_over_90,
                    SUM(pi.grand_total - COALESCE(p.paid_amount, 0)) as total
                FROM purchase_invoices pi
                JOIN suppliers s ON pi.supplier_id = s.id
                LEFT JOIN (
                    SELECT 
                        cbi.purchase_invoice_id,
                        SUM(COALESCE(p.amount, 0)) as paid_amount
                    FROM contra_bon_invoices cbi
                    LEFT JOIN payments p ON p.contra_bon_id = cbi.contra_bon_id
                    WHERE p.status = 'completed'
                    GROUP BY cbi.purchase_invoice_id
                ) p ON p.purchase_invoice_id = pi.id
                WHERE pi.status = 'approved'
                " . ($request->filled('search') ? " AND s.name LIKE '%" . $request->search . "%'" : "") . "
                GROUP BY s.id, s.name
                HAVING total > 0
                ORDER BY total DESC
            ");

            $filename = trans('translation.aging_report.export.filename') . '_' . date('d-m-Y');
            
            return Excel::download(new AgingReportExport($query), $filename . '.xlsx');

        } catch (\Exception $e) {
            \Log::error('Error in Aging Report export: ' . $e->getMessage());
            return redirect()->back()->with('error', trans('translation.aging_report.message.error_export'));
        }
    }
} 