<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseInvoice;
use App\Models\ContraBon;
use App\Models\Supplier;
use Carbon\Carbon;
use DB;
use Log;

class OutstandingInvoiceController extends Controller
{
    public function invoices()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('finance.outstanding-invoice.invoices', compact('suppliers'));
    }

    public function contraBon()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('finance.outstanding-invoice.contra-bon', compact('suppliers'));
    }

    public function invoicesData(Request $request)
    {
        try {
            $query = PurchaseInvoice::select(
                'purchase_invoices.id',
                'purchase_invoices.invoice_number',
                DB::raw('DATE_FORMAT(purchase_invoices.invoice_date, "%Y-%m-%d") as invoice_date'),
                DB::raw('DATE_FORMAT(purchase_invoices.due_date, "%Y-%m-%d") as due_date'),
                'purchase_invoices.grand_total',
                'suppliers.name as supplier_name',
                DB::raw('DATEDIFF(purchase_invoices.due_date, CURDATE()) as remaining_days')
            )
            ->join('suppliers', 'purchase_invoices.supplier_id', '=', 'suppliers.id')
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('contra_bon_invoices')
                    ->whereRaw('contra_bon_invoices.purchase_invoice_id = purchase_invoices.id');
            })
            ->where('purchase_invoices.status', 'approved');

            if ($request->supplier_id) {
                $query->where('purchase_invoices.supplier_id', $request->supplier_id);
            }

            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('purchase_invoices.invoice_number', 'like', '%' . $request->search . '%')
                      ->orWhere('suppliers.name', 'like', '%' . $request->search . '%');
                });
            }

            $data = $query->orderBy('remaining_days', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error in Outstanding Invoice data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load data'
            ], 500);
        }
    }

    public function contraBonData(Request $request)
    {
        try {
            $query = ContraBon::select(
                'contra_bons.id',
                'contra_bons.contra_bon_number',
                DB::raw('DATE_FORMAT(contra_bons.issue_date, "%Y-%m-%d") as issue_date'),
                DB::raw('DATE_FORMAT(contra_bons.due_date, "%Y-%m-%d") as due_date'),
                'contra_bons.total_amount',
                'suppliers.name as supplier_name',
                DB::raw('COALESCE(SUM(payments.amount), 0) as paid_amount'),
                DB::raw('contra_bons.total_amount - COALESCE(SUM(payments.amount), 0) as remaining_amount'),
                DB::raw('DATEDIFF(contra_bons.due_date, CURDATE()) as remaining_days')
            )
            ->join('suppliers', 'contra_bons.supplier_id', '=', 'suppliers.id')
            ->leftJoin('payments', 'contra_bons.id', '=', 'payments.contra_bon_id')
            ->where('contra_bons.status', 'approved')
            ->havingRaw('remaining_amount > 0')
            ->groupBy(
                'contra_bons.id',
                'contra_bons.contra_bon_number',
                'contra_bons.issue_date',
                'contra_bons.due_date',
                'contra_bons.total_amount',
                'suppliers.name'
            );

            if ($request->supplier_id) {
                $query->where('contra_bons.supplier_id', $request->supplier_id);
            }

            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('contra_bons.contra_bon_number', 'like', '%' . $request->search . '%')
                      ->orWhere('suppliers.name', 'like', '%' . $request->search . '%');
                });
            }

            $data = $query->orderBy('remaining_days', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error in Outstanding Contra Bon data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load data'
            ], 500);
        }
    }
} 