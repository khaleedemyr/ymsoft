<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Supplier;
use App\Traits\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PaymentHistoryController extends Controller
{
    use LogActivity;

    public function supplierIndex()
    {
        $suppliers = Supplier::where('status', 'active')->get();

        $this->logActivity(
            'payment_history',
            'VIEW',
            'Melihat halaman history pembayaran supplier'
        );

        return view('finance.payment-history.supplier', compact('suppliers'));
    }

    public function supplierData(Request $request)
    {
        try {
            $query = Payment::select(
                'payments.id',
                'payments.payment_number',
                'payments.created_at as payment_date',
                'payments.amount',
                'payments.payment_method',
                'payments.notes',
                'suppliers.name as supplier_name',
                'contra_bons.contra_bon_number',
                'contra_bons.issue_date as contra_bon_date',
                'users.nama_lengkap as created_by'
            )
            ->join('contra_bons', 'payments.contra_bon_id', '=', 'contra_bons.id')
            ->join('suppliers', 'contra_bons.supplier_id', '=', 'suppliers.id')
            ->join('users', 'payments.created_by', '=', 'users.id')
            ->orderBy('payments.created_at', 'desc');

            // Filter berdasarkan periode
            if ($request->filled(['start_date', 'end_date'])) {
                $query->whereBetween('payments.created_at', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            // Filter berdasarkan supplier
            if ($request->filled('supplier_id')) {
                $query->where('suppliers.id', $request->supplier_id);
            }

            // Filter pencarian
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('payments.payment_number', 'like', "%{$search}%")
                      ->orWhere('contra_bons.contra_bon_number', 'like', "%{$search}%")
                      ->orWhere('suppliers.name', 'like', "%{$search}%");
                });
            }

            $data = $query->get();

            $this->logActivity(
                'payment_history',
                'LOAD',
                'Memuat data history pembayaran supplier'
            );

            return response()->json([
                'status' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in Payment History data: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function summaryIndex()
    {
        $this->logActivity(
            'payment_history',
            'VIEW',
            'Melihat halaman ringkasan pembayaran'
        );

        return view('finance.payment-history.summary');
    }

    public function summaryData(Request $request)
    {
        try {
            $query = Payment::select(
                DB::raw('DATE_FORMAT(payments.created_at, "%Y-%m") as period'),
                DB::raw('COUNT(DISTINCT suppliers.id) as total_suppliers'),
                DB::raw('COUNT(payments.id) as total_payments'),
                DB::raw('SUM(payments.amount) as total_amount'),
                DB::raw('GROUP_CONCAT(DISTINCT payment_method) as payment_methods')
            )
            ->join('contra_bons', 'payments.contra_bon_id', '=', 'contra_bons.id')
            ->join('suppliers', 'contra_bons.supplier_id', '=', 'suppliers.id')
            ->groupBy(DB::raw('DATE_FORMAT(payments.created_at, "%Y-%m")'));

            // Filter berdasarkan periode
            if ($request->filled(['start_date', 'end_date'])) {
                $query->whereBetween('payments.created_at', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            $data = $query->orderBy('period', 'desc')->get();

            // Hitung statistik tambahan
            $statistics = [
                'total_amount' => $data->sum('total_amount'),
                'total_payments' => $data->sum('total_payments'),
                'avg_amount_per_payment' => $data->sum('total_amount') / $data->sum('total_payments'),
                'avg_suppliers_per_month' => $data->avg('total_suppliers')
            ];

            $this->logActivity(
                'payment_history',
                'LOAD',
                'Memuat data ringkasan pembayaran'
            );

            return response()->json([
                'status' => true,
                'data' => $data,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in Payment Summary data: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }
} 