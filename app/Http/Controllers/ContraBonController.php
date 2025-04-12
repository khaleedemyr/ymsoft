<?php

namespace App\Http\Controllers;

use App\Models\ContraBon;
use App\Models\ContraBonInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Traits\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Helpers\UserHelper;

class ContraBonController extends Controller
{
    use LogActivity;

    public function index()
    {
        $contraBons = ContraBon::with([
            'supplier', 
            'createdBy:id,nama_lengkap'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        $this->logActivity(
            'contra_bons',
            'READ',
            'Mengakses daftar kontra bon'
        );

        return view('finance.contra-bons.index', compact('contraBons'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->get();

        $this->logActivity(
            'contra_bons',
            'VIEW',
            'Melihat form tambah kontra bon'
        );

        return view('finance.contra-bons.create', compact('suppliers'));
    }

    public function getSupplierInvoices($supplierId)
    {
        try {
            $invoices = PurchaseInvoice::where('purchase_invoices.supplier_id', $supplierId)
                ->where('purchase_invoices.status', 'approved')
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('contra_bon_invoices')
                        ->whereRaw('contra_bon_invoices.purchase_invoice_id = purchase_invoices.id');
                })
                ->with(['goodReceive.purchaseOrder'])
                ->get();

            // Log untuk debugging
            \Log::info('Fetching invoices for supplier: ' . $supplierId, [
                'count' => $invoices->count(),
                'invoices' => $invoices->toArray()
            ]);

            return response()->json([
                'success' => true,
                'data' => $invoices
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting supplier invoices: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validasi request
            $validated = $request->validate([
                'supplier_id' => 'required|exists:suppliers,id',
                'issue_date' => 'required|date',
                'due_date' => 'required|date',
                'invoice_ids' => 'required|array',
                'invoice_ids.*' => 'exists:purchase_invoices,id',
                'notes' => 'nullable|string'
            ]);

            // Generate nomor kontra bon
            $contraBonNumber = $this->generateContraBonNumber();

            // Hitung total amount dari invoice yang dipilih
            $totalAmount = PurchaseInvoice::whereIn('id', $request->invoice_ids)
                ->sum('grand_total');

            // Buat contra bon baru
            $contraBon = ContraBon::create([
                'contra_bon_number' => $contraBonNumber,
                'supplier_id' => $validated['supplier_id'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'total_amount' => $totalAmount,
                'status' => 'draft',
                'notes' => $validated['notes'],
                'created_by' => auth()->id()
            ]);

            // Simpan relasi invoice di tabel pivot
            foreach ($request->invoice_ids as $invoiceId) {
                $invoice = PurchaseInvoice::find($invoiceId);
                $contraBon->invoices()->attach($invoiceId, [
                    'amount' => $invoice->grand_total,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('translation.contra_bon.message.success_create'),
                'redirect' => route('finance.contra-bons.index')
            ]);

        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kontra bon: ' . $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error creating contra bon: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kontra bon: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(ContraBon $contraBon)
    {
        // Load relasi dengan lebih spesifik
        $contraBon->load([
            'supplier',
            'invoices' => function($query) {
                $query->with(['goodReceive' => function($q) {
                    $q->with('purchaseOrder');
                }]);
            },
            'createdBy:id,nama_lengkap',
            'approvedBy:id,nama_lengkap',
            'paidBy:id,nama_lengkap'
        ]);

        $this->logActivity(
            'contra_bons',
            'VIEW',
            'Melihat detail kontra bon: ' . $contraBon->contra_bon_number
        );

        return view('finance.contra-bons.show', compact('contraBon'));
    }

    public function approve($id)
    {
        try {
            if (!UserHelper::canApproveContraBon()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('translation.contra_bon.message.unauthorized_approval')
                ], 403);
            }

            $contraBon = ContraBon::findOrFail($id);
            
            if ($contraBon->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => trans('translation.contra_bon.message.cannot_approve')
                ], 400);
            }

            DB::beginTransaction();

            $oldData = $contraBon->load(['supplier', 'invoices'])->toArray();

            $contraBon->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            $this->logActivity(
                'contra_bons',
                'APPROVE',
                'Menyetujui kontra bon: ' . $contraBon->contra_bon_number,
                $oldData,
                $contraBon->fresh()->load(['supplier', 'invoices'])->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('translation.contra_bon.message.success_approve')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error approving contra bon: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui kontra bon: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAsPaid($id)
    {
        try {
            $contraBon = ContraBon::findOrFail($id);

            // Validasi status
            if ($contraBon->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => trans('translation.contra_bon.message.cannot_mark_as_paid')
                ], 400);
            }

            DB::beginTransaction();

            $oldData = $contraBon->load(['supplier', 'invoices'])->toArray();

            $contraBon->update([
                'status' => 'paid',
                'paid_by' => auth()->id(),
                'paid_at' => now()
            ]);

            // Update status pembayaran invoice
            foreach ($contraBon->invoices as $invoice) {
                $invoice->purchaseInvoice->update([
                    'payment_status' => 'paid'
                ]);
            }

            $this->logActivity(
                'contra_bons',
                'PAID',
                'Menandai kontra bon sebagai lunas: ' . $contraBon->contra_bon_number,
                $oldData,
                $contraBon->fresh()->load(['supplier', 'invoices'])->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('translation.contra_bon.message.success_mark_as_paid')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai kontra bon sebagai lunas: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateContraBonNumber()
    {
        $prefix = 'CB';
        $date = now()->format('Ymd');
        
        // Get last contra bon number for today
        $lastCB = ContraBon::where('contra_bon_number', 'like', "{$prefix}/{$date}/%")
            ->orderBy('contra_bon_number', 'desc')
            ->first();

        if ($lastCB) {
            $lastNumber = intval(substr($lastCB->contra_bon_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}/{$date}/{$newNumber}";
    }
} 