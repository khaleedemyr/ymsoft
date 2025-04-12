<?php

namespace App\Http\Controllers;

use App\Models\ContraBon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentPlanningExport;

class PaymentPlanningController extends Controller
{
    public function index()
    {
        return view('finance.payment-planning.index');
    }

    public function data(Request $request)
    {
        try {
            $query = ContraBon::select(
                'contra_bons.id',
                'contra_bons.contra_bon_number',
                'contra_bons.issue_date',
                'contra_bons.due_date',
                'suppliers.name as supplier_name',
                DB::raw('COALESCE(SUM(purchase_invoices.grand_total), 0) as total_amount'),
                DB::raw('COALESCE(SUM(payments.amount), 0) as paid_amount'),
                DB::raw('COALESCE(SUM(purchase_invoices.grand_total), 0) - COALESCE(SUM(payments.amount), 0) as remaining_amount'),
                DB::raw('DATEDIFF(contra_bons.due_date, CURDATE()) as days_until_due')
            )
            ->join('suppliers', 'contra_bons.supplier_id', '=', 'suppliers.id')
            ->join('contra_bon_invoices', 'contra_bons.id', '=', 'contra_bon_invoices.contra_bon_id')
            ->join('purchase_invoices', 'contra_bon_invoices.purchase_invoice_id', '=', 'purchase_invoices.id')
            ->leftJoin('payments', 'contra_bons.id', '=', 'payments.contra_bon_id')
            ->where('contra_bons.status', 'approved')
            ->where('contra_bons.due_date', '>=', now())
            ->groupBy(
                'contra_bons.id',
                'contra_bons.contra_bon_number',
                'contra_bons.issue_date',
                'contra_bons.due_date',
                'suppliers.name'
            );

            // Filter berdasarkan periode
            if ($request->filled(['start_date', 'end_date'])) {
                $query->whereBetween('contra_bons.due_date', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            // Filter pencarian
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('suppliers.name', 'like', "%{$search}%")
                      ->orWhere('contra_bons.contra_bon_number', 'like', "%{$search}%");
                });
            }

            $data = $query->orderBy('contra_bons.due_date')->get();

            // Hitung total per periode (minggu)
            $weeklyTotals = $data->groupBy(function($item) {
                return \Carbon\Carbon::parse($item->due_date)->startOfWeek()->format('Y-m-d');
            })->map(function($items) {
                return [
                    'start_date' => \Carbon\Carbon::parse($items->first()->due_date)->startOfWeek()->format('Y-m-d'),
                    'end_date' => \Carbon\Carbon::parse($items->first()->due_date)->endOfWeek()->format('Y-m-d'),
                    'total_amount' => $items->sum('remaining_amount')
                ];
            })->values();

            return response()->json([
                'status' => true,
                'data' => $data,
                'weekly_totals' => $weeklyTotals
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in Payment Planning data: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => trans('translation.payment_planning.message.error_loading')
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $filename = trans('translation.payment_planning.export.filename') . '_' . date('d-m-Y');
            return Excel::download(
                new PaymentPlanningExport($request->all()),
                $filename . '.xlsx'
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', trans('translation.payment_planning.message.error_export'));
        }
    }
} 