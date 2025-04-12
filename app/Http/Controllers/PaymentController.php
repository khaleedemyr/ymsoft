<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\ContraBon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\UserHelper;
use App\Traits\LogActivity;

class PaymentController extends Controller
{
    use LogActivity;

    public function index()
    {
        $payments = Payment::with(['contraBon.supplier', 'createdBy'])->orderBy('created_at', 'desc')->get();
        return view('finance.payments.index', compact('payments'));
    }

    public function create()
    {
        $contraBons = ContraBon::where('status', 'approved')
            ->whereDoesntHave('payment')
            ->with('supplier')
            ->get();
        return view('finance.payments.create', compact('contraBons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contra_bon_id' => 'required|exists:contra_bons,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric',
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $paymentProof = $request->file('payment_proof')->store('payment-proofs');
            
            $payment = Payment::create([
                'payment_number' => 'PAY-' . date('YmdHis'),
                'contra_bon_id' => $request->contra_bon_id,
                'payment_method' => $request->payment_method,
                'amount' => $request->amount,
                'payment_proof' => $paymentProof,
                'notes' => $request->notes,
                'created_by' => auth()->id()
            ]);

            DB::commit();
            return redirect()->route('finance.payments.index')
                ->with('success', trans('translation.payment.message.success_create'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', trans('translation.payment.message.error_create'));
        }
    }

    public function show(Payment $payment)
    {
        $payment->load(['contraBon.supplier']);
        return view('finance.payments.show', compact('payment'));
    }

    public function approve($id)
    {
        try {
            if (!UserHelper::canApprovePurchaseInvoice()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('translation.payment.message.unauthorized_approval')
                ], 403);
            }

            DB::beginTransaction();

            $payment = Payment::with(['contraBon'])->findOrFail($id);
            
            if ($payment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => trans('translation.payment.message.cannot_approve')
                ], 400);
            }

            // Backup data lama untuk log aktivitas
            $oldData = $payment->toArray();

            // Update payment status
            $payment->status = 'completed';
            $payment->approved_by = auth()->id();
            $payment->approved_at = now();
            $payment->save();

            // Update contra bon status
            $contraBon = $payment->contraBon;
            $contraBon->status = 'paid';
            $contraBon->paid_by = auth()->id();
            $contraBon->paid_at = now();
            $contraBon->save();

            // Update purchase invoices status
            // Ambil semua purchase invoice yang terkait dengan contra bon ini
            $purchaseInvoices = DB::table('contra_bon_purchase_invoice as cbpi')
                ->join('purchase_invoices as pi', 'pi.id', '=', 'cbpi.purchase_invoice_id')
                ->where('cbpi.contra_bon_id', $contraBon->id)
                ->select('pi.id')
                ->get();

            // Debug: Log jumlah invoice yang akan diupdate
            \Log::info('Updating payment status for ' . $purchaseInvoices->count() . ' invoices');

            foreach ($purchaseInvoices as $invoice) {
                // Debug: Log ID invoice yang diupdate
                \Log::info('Updating invoice ID: ' . $invoice->id);

                $updated = DB::table('purchase_invoices')
                    ->where('id', $invoice->id)
                    ->update([
                        'payment_status' => 'paid',
                        'updated_by' => auth()->id(),
                        'updated_at' => now()
                    ]);

                // Debug: Log hasil update
                \Log::info('Update result for invoice ' . $invoice->id . ': ' . ($updated ? 'success' : 'failed'));
            }

            // Double check: Verifikasi update berhasil
            $verifyUpdate = DB::table('purchase_invoices')
                ->whereIn('id', $purchaseInvoices->pluck('id'))
                ->where('payment_status', '!=', 'paid')
                ->count();

            if ($verifyUpdate > 0) {
                throw new \Exception('Gagal mengupdate status pembayaran pada ' . $verifyUpdate . ' invoice');
            }

            // Log aktivitas
            $this->logActivity(
                'payments',
                'APPROVE',
                'Menyetujui pembayaran: ' . $payment->payment_number,
                $oldData,
                $payment->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('translation.payment.message.success_approve')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error approving payment: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function reject($id)
    {
        try {
            if (!UserHelper::canApprovePurchaseInvoice()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('translation.payment.message.unauthorized_approval')
                ], 403);
            }

            DB::beginTransaction();

            $payment = Payment::with(['contraBon'])->findOrFail($id);
            
            if ($payment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => trans('translation.payment.message.cannot_reject')
                ], 400);
            }

            // Backup data lama untuk log aktivitas
            $oldData = $payment->toArray();

            // Update payment status
            $payment->status = 'rejected';
            $payment->rejected_by = auth()->id();
            $payment->rejected_at = now();
            $payment->save();

            // Update contra bon status
            $contraBon = $payment->contraBon;
            $contraBon->status = 'approved';
            $contraBon->paid_by = null;
            $contraBon->paid_at = null;
            $contraBon->save();

            // Update purchase invoices status
            $purchaseInvoices = DB::table('contra_bon_purchase_invoice')
                ->where('contra_bon_id', $contraBon->id)
                ->join('purchase_invoices', 'purchase_invoices.id', '=', 'contra_bon_purchase_invoice.purchase_invoice_id')
                ->select('purchase_invoices.id')
                ->get();

            foreach ($purchaseInvoices as $invoice) {
                DB::table('purchase_invoices')
                    ->where('id', $invoice->id)
                    ->update([
                        'payment_status' => 'unpaid',
                        'updated_by' => auth()->id(),
                        'updated_at' => now()
                    ]);
            }

            // Log aktivitas
            $this->logActivity(
                'payments',
                'REJECT',
                'Menolak pembayaran: ' . $payment->payment_number,
                $oldData,
                $payment->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('translation.payment.message.success_reject')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error rejecting payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 