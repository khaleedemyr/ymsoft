<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\GoodReceive;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Supplier;
use App\Models\Item;
use App\Traits\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class PurchaseInvoiceController extends Controller
{
    use LogActivity;
    
    public function index()
    {
        $purchaseInvoices = PurchaseInvoice::with(['goodReceive', 'supplier', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $this->logActivity(
            'purchase_invoices',
            'READ',
            'Mengakses daftar faktur pembelian'
        );
        
        return view('finance.purchase-invoice.index', compact('purchaseInvoices'));
    }
    
    public function create()
    {
        $goodReceives = GoodReceive::whereDoesntHave('purchaseInvoice')
            ->where('status', 'approved')
            ->with([
                'purchaseOrder.supplier',
                'purchaseOrder.purchaseRequisition.warehouse'
            ])
            ->get()
            ->map(function ($gr) {
                $supplier = $gr->purchaseOrder->supplier ?? null;
                $warehouse = $gr->purchaseOrder->purchaseRequisition->warehouse ?? null;
                
                return [
                    'id' => $gr->id,
                    'gr_number' => $gr->gr_number,
                    'supplier_name' => $supplier ? $supplier->name : '-',
                    'supplier_id' => $supplier ? $supplier->id : null,
                    'payment_days' => $supplier ? $supplier->payment_days : 0,
                    'warehouse_name' => $warehouse ? $warehouse->name : '-',
                    'po_number' => $gr->purchaseOrder->po_number ?? '-',
                    'display_name' => sprintf(
                        '%s - %s - %s',
                        $supplier ? $supplier->name : '-',
                        $gr->gr_number ?? '-',
                        $gr->purchaseOrder->po_number ?? '-'
                    )
                ];
            });

        $this->logActivity(
            'purchase_invoices',
            'VIEW',
            'Melihat form buat faktur pembelian'
        );
        
        return view('finance.purchase-invoice.create', compact('goodReceives'));
    }
    
    public function getItems(GoodReceive $goodReceive)
    {
        try {
            \DB::enableQueryLog();

            $items = $goodReceive->items()
                ->with(['purchaseOrderItem.item', 'unit'])
                ->get();

            // Log query yang dijalankan
            \Log::info('Queries:', \DB::getQueryLog());

            // Debug log
            \Log::info('Good Receive Items:', [
                'gr_number' => $goodReceive->gr_number,
                'items' => $items->toArray()
            ]);

            $mappedItems = $items->map(function ($item) {
                // Debug log untuk setiap item
                \Log::info('Processing Item:', [
                    'purchase_order_item' => $item->purchaseOrderItem,
                    'item_data' => $item->purchaseOrderItem->item ?? 'null'
                ]);

                return [
                    'id' => $item->id,
                    'item_id' => $item->purchaseOrderItem->item_id ?? null,
                    'item_name' => $item->purchaseOrderItem->item->name ?? '-',
                    'quantity' => $item->quantity,
                    'unit_id' => $item->unit_id,
                    'unit_name' => $item->unit->name ?? '-',
                    'price' => $item->price ?? 0,
                ];
            });

            return response()->json([
                'success' => true,
                'items' => $mappedItems
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getItems: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data items: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'good_receive_id' => 'required',
                'invoice_number' => 'required',
                'invoice_date' => 'required',
                'due_date' => 'required',
                'items' => 'required|array',
                'subtotal' => 'required',
                'grand_total' => 'required'
            ]);

            DB::beginTransaction();

            $goodReceive = GoodReceive::with([
                'purchaseOrder.supplier', 
                'purchaseOrder.purchaseRequisition.warehouse',
                'goodReceiveItems.purchaseOrderItem.item',
                'goodReceiveItems.unit'
            ])->findOrFail($request->good_receive_id);

            // Buat Purchase Invoice
            $purchaseInvoice = PurchaseInvoice::create([
                'invoice_number' => $request->invoice_number,
                'invoice_date' => $request->invoice_date,
                'good_receive_id' => $goodReceive->id,
                'supplier_id' => $goodReceive->purchaseOrder->supplier->id,
                'warehouse_id' => $goodReceive->purchaseOrder->purchaseRequisition->warehouse->id,
                'notes' => $request->notes,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Simpan items
            foreach ($request->items as $item) {
                $grItem = $goodReceive->goodReceiveItems->find($item['id']);
                
                $purchaseInvoice->items()->create([
                    'item_id' => $grItem->purchaseOrderItem->item_id,
                    'quantity' => $item['quantity'],
                    'unit_id' => $grItem->unit_id,
                    'original_price' => $grItem->price,
                    'invoice_price' => $item['invoice_price'],
                    'discount_type' => $item['discount_type'],
                    'discount_value' => $item['discount_value'],
                    'discount_amount' => $item['discount_amount'],
                    'price' => $item['invoice_price'],
                    'subtotal' => $item['subtotal']
                ]);
            }

            // Update total invoice
            $purchaseInvoice->update([
                'subtotal' => $request->subtotal,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'discount_amount' => $request->discount_amount,
                'vat_type' => $request->vat_type,
                'vat_percentage' => $request->vat_percentage,
                'vat_amount' => $request->vat_amount,
                'grand_total' => $request->grand_total
            ]);

            // Update status GR
            $goodReceive->update([
                'invoice_status' => 'invoiced', // atau status lain yang sesuai
                'updated_by' => auth()->id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Faktur pembelian berhasil dibuat',
                'data' => [
                    'id' => $purchaseInvoice->id,
                    'invoice_number' => $purchaseInvoice->invoice_number
                ],
                'redirect_url' => route('finance.purchase-invoices.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in store method: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function show(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load(['goodReceive', 'supplier', 'warehouse', 'creator', 'items.item', 'items.unit']);
        
        $this->logActivity(
            'purchase_invoices',
            'VIEW',
            'Melihat detail faktur pembelian: ' . $purchaseInvoice->invoice_number
        );
        
        return view('finance.purchase-invoice.show', compact('purchaseInvoice'));
    }
    
    public function edit(PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status !== 'draft') {
            return redirect()->route('finance.purchase-invoices.index')
                ->with('error', trans('translation.purchase_invoice.message.cannot_edit'));
        }
        
        $suppliers = Supplier::where('status', 'active')->get();
        
        $this->logActivity(
            'purchase_invoices',
            'VIEW',
            'Melihat form edit faktur pembelian: ' . $purchaseInvoice->invoice_number
        );
        
        return view('finance.purchase-invoice.edit', compact('purchaseInvoice', 'suppliers'));
    }
    
    public function update(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => trans('translation.purchase_invoice.message.cannot_edit')
            ], 403);
        }

        $request->validate([
            'invoice_number' => 'required|unique:purchase_invoices,invoice_number,' . $purchaseInvoice->id,
            'invoice_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $purchaseInvoice->update([
                'invoice_number' => $request->invoice_number,
                'invoice_date' => $request->invoice_date,
                'notes' => $request->notes,
            ]);

            // Hapus items lama
            $purchaseInvoice->items()->delete();

            // Simpan items baru
            $total = 0;
            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['price'];
                $total += $subtotal;

                $purchaseInvoice->items()->create([
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);
            }

            // Update total
            $purchaseInvoice->update([
                'subtotal' => $total,
                'tax' => 0, // Bisa disesuaikan dengan kebutuhan
                'total' => $total,
            ]);

            DB::commit();

            $this->logActivity(
                'purchase_invoices',
                'UPDATE',
                'Mengupdate faktur pembelian: ' . $purchaseInvoice->invoice_number,
                $purchaseInvoice->toArray(),
                $purchaseInvoice->fresh()->toArray()
            );
            
            return response()->json([
                'success' => true,
                'message' => trans('translation.purchase_invoice.message.success_update'),
                'redirect_url' => route('finance.purchase-invoices.show', $purchaseInvoice->id),
            ]);
                
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating purchase invoice: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy(PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => trans('translation.purchase_invoice.message.cannot_delete')
            ], 403);
        }

        try {
            DB::beginTransaction();

            $purchaseInvoice->items()->delete();
            $purchaseInvoice->delete();

            DB::commit();

            $this->logActivity(
                'purchase_invoices',
                'DELETE',
                'Menghapus faktur pembelian: ' . $purchaseInvoice->invoice_number,
                $purchaseInvoice->toArray(),
                null
            );
            
            return response()->json([
                'success' => true,
                'message' => trans('translation.purchase_invoice.message.success_delete')
            ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function approve($id)
    {
        try {
            // Cek apakah user memiliki akses
            $user = auth()->user();
            $hasRole = $user->user_roles()->where('role_id', 1)->exists();
            $hasPosition = $user->id_jabatan == 168 && $user->status == 'A';

            if (!$hasRole && !$hasPosition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui faktur pembelian'
                ], 403);
            }

            $invoice = PurchaseInvoice::findOrFail($id);
            
            // Cek apakah faktur masih draft
            if ($invoice->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Faktur pembelian hanya bisa disetujui saat status draft'
                ], 400);
            }

            DB::beginTransaction();

            $oldData = $invoice->load(['items.item', 'items.supplier', 'goodReceive'])->toArray();

            // Update status faktur
            $invoice->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            // Log aktivitas
            $newData = $invoice->fresh()->load(['items.item', 'items.supplier', 'goodReceive'])->toArray();
            $this->logActivity(
                'purchase_invoices',
                'APPROVE',
                'Menyetujui faktur pembelian: ' . $invoice->invoice_number,
                $oldData,
                $newData
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Faktur pembelian berhasil disetujui'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error approving purchase invoice: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyetujui faktur pembelian: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function reject($id)
    {
        try {
            // Cek apakah user memiliki akses
            $user = auth()->user();
            $hasRole = $user->user_roles()->where('role_id', 1)->exists();
            $hasPosition = $user->id_jabatan == 168 && $user->status == 'A';

            if (!$hasRole && !$hasPosition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menolak faktur pembelian'
                ], 403);
            }

            $invoice = PurchaseInvoice::findOrFail($id);
            
            // Cek apakah faktur masih draft
            if ($invoice->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Faktur pembelian hanya bisa ditolak saat status draft'
                ], 400);
            }

            DB::beginTransaction();

            $oldData = $invoice->load(['items.item', 'items.supplier', 'goodReceive'])->toArray();

            // Update status faktur
            $invoice->update([
                'status' => 'cancelled',
                'rejected_by' => auth()->id(),
                'rejected_at' => now()
            ]);

            // Log aktivitas
            $newData = $invoice->fresh()->load(['items.item', 'items.supplier', 'goodReceive'])->toArray();
            $this->logActivity(
                'purchase_invoices',
                'REJECT',
                'Menolak faktur pembelian: ' . $invoice->invoice_number,
                $oldData,
                $newData
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Faktur pembelian berhasil ditolak'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error rejecting purchase invoice: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menolak faktur pembelian: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateInvoiceNumber()
    {
        $prefix = 'PI';
        $date = now()->format('Ymd');
        
        // Get last invoice number for today
        $lastInvoice = PurchaseInvoice::where('internal_number', 'like', "{$prefix}/{$date}/%")
            ->orderBy('internal_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->internal_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}/{$date}/{$newNumber}";
    }
} 