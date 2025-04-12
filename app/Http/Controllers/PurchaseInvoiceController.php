<?php

namespace App\Http\Controllers;

use App\Models\GoodReceive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\PurchaseInvoice;
use App\Helpers\UserHelper;
use Carbon\Carbon;
use App\Traits\LogActivity;

class PurchaseInvoiceController extends Controller
{
    use LogActivity;

    public function create()
    {
        $goodReceives = GoodReceive::whereDoesntHave('purchaseInvoice')
            ->where('status', 'approved')
            ->with([
                'purchaseOrder.supplier',
                'purchaseOrder.purchaseRequisition.warehouse',
                'goodReceiveItems.purchaseOrderItem.item',
                'goodReceiveItems.unit'
            ])
            ->get()
            ->map(function ($gr) {
                // Debug data relasi
                \Log::debug('GR Relations:', [
                    'po' => $gr->purchaseOrder ? 'exists' : 'null',
                    'pr' => $gr->purchaseOrder?->purchaseRequisition ? 'exists' : 'null',
                    'warehouse' => $gr->purchaseOrder?->purchaseRequisition?->warehouse ? 'exists' : 'null'
                ]);

                $supplier = $gr->purchaseOrder?->supplier;
                $warehouse = $gr->purchaseOrder?->purchaseRequisition?->warehouse;
                
                $data = [
                    'id' => $gr->id,
                    'supplier_name' => $supplier?->name ?? '-',
                    'supplier_id' => $supplier?->id,
                    'warehouse_name' => $warehouse?->name ?? '-',
                    'warehouse_id' => $warehouse?->id,
                    'po_number' => $gr->purchaseOrder?->po_number ?? '-',
                    'payment_days' => $supplier?->payment_days ?? 0,
                    'display_name' => sprintf(
                        '%s - %s - %s',
                        $supplier?->name ?? '-',
                        $gr->gr_number,
                        $gr->purchaseOrder?->po_number ?? '-'
                    )
                ];

                // Debug data yang akan dikirim ke view
                \Log::debug('Mapped Data:', $data);

                return $data;
            });

        // Debug final collection
        \Log::debug('Final Collection:', $goodReceives->toArray());

        return view('finance.purchase-invoice.create', compact('goodReceives'));
    }

    public function getItems($id)
    {
        try {
            $goodReceive = GoodReceive::with([
                'goodReceiveItems.purchaseOrderItem.item',
                'goodReceiveItems.unit'
            ])->findOrFail($id);

            $items = $goodReceive->goodReceiveItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_name' => $item->purchaseOrderItem->item->name ?? '-',
                    'quantity' => $item->quantity,
                    'unit' => $item->unit->name ?? '-',
                    'price' => $item->purchaseOrderItem->price ?? 0,
                    'subtotal' => ($item->quantity * ($item->purchaseOrderItem->price ?? 0))
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validasi dasar
            $validated = $request->validate([
                'good_receive_id' => 'required|exists:good_receives,id',
                'invoice_number' => 'required|string|max:50|unique:purchase_invoices,invoice_number',
                'invoice_date' => 'required|date',
                'due_date' => 'required|date',
                'supplier_id' => 'required|exists:suppliers,id',
                'subtotal' => 'required|numeric|min:0',
                'grand_total' => 'required|numeric|min:0',
                'items' => 'required|array|min:1'
            ]);

            // Ambil warehouse_id dari good_receive
            $goodReceive = GoodReceive::with([
                'purchaseOrder.purchaseRequisition.warehouse',
                'goodReceiveItems.purchaseOrderItem.item',
                'goodReceiveItems.unit'
            ])->findOrFail($request->good_receive_id);

            // Buat array data untuk insert header
            $data = [
                'good_receive_id' => $validated['good_receive_id'],
                'invoice_number' => $validated['invoice_number'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $goodReceive->purchaseOrder->purchaseRequisition->warehouse->id,
                'subtotal' => $validated['subtotal'],
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'vat_type' => $request->vat_type,
                'vat_percentage' => $request->vat_percentage ?? 0,
                'vat_amount' => $request->vat_amount ?? 0,
                'grand_total' => $validated['grand_total'],
                'status' => 'draft',
                'notes' => $request->notes,
                'created_by' => auth()->id()
            ];

            // Simpan header
            $purchaseInvoice = PurchaseInvoice::create($data);

            // Simpan detail items
            foreach ($request->items as $item) {
                $grItem = $goodReceive->goodReceiveItems->find($item['id']);
                if (!$grItem) continue;

                $purchaseInvoice->items()->create([
                    'item_id' => $grItem->purchaseOrderItem->item_id,
                    'quantity' => $item['quantity'],
                    'unit_id' => $grItem->unit_id,
                    'original_price' => $grItem->purchaseOrderItem->price,
                    'invoice_price' => $item['invoice_price'],
                    'discount_type' => $item['discount_type'] ?? null,
                    'discount_value' => $item['discount_value'] ?? 0,
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'price' => $item['invoice_price'], // harga setelah diskon
                    'subtotal' => $item['subtotal']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('translation.purchase_invoice.message.success_create'),
                'redirect_url' => route('finance.purchase-invoices.index')
            ]);

        } catch (ValidationException $e) {
            DB::rollback();
            \Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error creating purchase invoice: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString()); // Tambahkan log stack trace
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $purchaseInvoices = PurchaseInvoice::with([
            'goodReceive.purchaseOrder',
            'supplier',
            'warehouse'
        ])->orderBy('created_at', 'desc')->get();

        return view('finance.purchase-invoice.index', compact('purchaseInvoices'));
    }

    public function show(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load([
            'supplier',
            'warehouse',
            'goodReceive.purchaseOrder',
            'items.item',
            'items.unit'
        ]);

        return view('finance.purchase-invoice.show', compact('purchaseInvoice'));
    }

    public function approve($id)
    {
        try {
            if (!UserHelper::canApprovePurchaseInvoice()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('translation.purchase_invoice.message.unauthorized_approval')
                ], 403);
            }

            $purchaseInvoice = PurchaseInvoice::findOrFail($id);
            
            if ($purchaseInvoice->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => trans('translation.purchase_invoice.message.cannot_approve')
                ], 400);
            }

            $purchaseInvoice->status = 'approved';
            $purchaseInvoice->approved_by = auth()->id();
            $purchaseInvoice->approved_at = now();
            $purchaseInvoice->save();

            return response()->json([
                'success' => true,
                'message' => trans('translation.purchase_invoice.message.success_approve')
            ]);
        } catch (\Exception $e) {
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
                    'message' => trans('translation.purchase_invoice.message.unauthorized_approval')
                ], 403);
            }

            $purchaseInvoice = PurchaseInvoice::findOrFail($id);
            
            if ($purchaseInvoice->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => trans('translation.purchase_invoice.message.cannot_reject')
                ], 400);
            }

            $purchaseInvoice->status = 'rejected';
            $purchaseInvoice->rejected_by = auth()->id();
            $purchaseInvoice->rejected_at = now();
            $purchaseInvoice->save();

            return response()->json([
                'success' => true,
                'message' => trans('translation.purchase_invoice.message.success_reject')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load([
            'supplier',
            'warehouse',
            'goodReceive',
            'goodReceive.purchaseOrder',
            'goodReceive.purchaseOrder.supplier',
            'items',
            'items.item',
            'items.unit'
        ]);

        return view('finance.purchase-invoice.edit', compact('purchaseInvoice'));
    }

    public function update(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        DB::beginTransaction();
        try {
            // Validasi dasar
            $validated = $request->validate([
                'good_receive_id' => 'required|exists:good_receives,id',
                'invoice_number' => 'required|string|max:50',
                'invoice_date' => 'required|date',
                'due_date' => 'required|date',
                'supplier_id' => 'required|exists:suppliers,id',
                'subtotal' => 'required|numeric|min:0',
                'grand_total' => 'required|numeric|min:0'
            ]);

            // Backup data lama untuk activity log
            $oldData = $purchaseInvoice->load(['supplier', 'warehouse', 'items.item', 'items.unit'])->toArray();

            // Buat array data untuk update
            $data = $validated;
            $data['warehouse_id'] = $purchaseInvoice->warehouse_id;
            $data['notes'] = $request->notes;
            $data['updated_by'] = auth()->id();

            // Tambahkan data diskon dan PPN
            $data['discount_type'] = $request->discount_type;
            $data['discount_value'] = $request->discount_value;
            $data['discount_amount'] = $request->discount_amount;
            $data['vat_type'] = $request->vat_type;
            $data['vat_percentage'] = $request->vat_percentage;
            $data['vat_amount'] = $request->vat_amount;

            // Update purchase invoice
            $purchaseInvoice->update($data);

            // Update items
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    $purchaseInvoice->items()->where('id', $item['id'])->update([
                        'price' => $item['invoice_price'],
                        'discount_type' => $item['discount_type'] ?? null,
                        'discount_value' => $item['discount_value'] ?? 0,
                        'discount_amount' => $item['discount_amount'] ?? 0,
                        'subtotal' => $item['subtotal']
                    ]);
                }
            }

            // Log activity untuk update Purchase Invoice
            $newData = $purchaseInvoice->fresh()->load(['supplier', 'warehouse', 'items.item', 'items.unit'])->toArray();
            $this->logActivity(
                'purchase_invoices',
                'UPDATE',
                'Mengupdate purchase invoice: ' . $purchaseInvoice->invoice_number,
                $oldData,
                $newData
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('translation.purchase_invoice.message.success_update'),
                'redirect_url' => route('finance.purchase-invoices.show', $purchaseInvoice->id)
            ]);

        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating purchase invoice: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 