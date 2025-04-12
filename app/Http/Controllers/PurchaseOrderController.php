<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseRequisition;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\ItemPriceHistory;
use App\Traits\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;
use App\Models\ActivityLog;
use App\Models\PurchaseRequisitionItem;

class PurchaseOrderController extends Controller
{
    use LogActivity;
    
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with([
            'purchaseRequisition.warehouse:id,name',
            'creator:id,nama_lengkap',
            'supplier:id,name'
        ])->get();
        
        $this->logActivity(
            'purchase_orders',
            'READ',
            'Mengakses daftar purchase order'
        );
        
        return view('purchasing.purchase-orders.index', compact('purchaseOrders'));
    }
    
    public function create()
    {
        $purchaseRequisitions = PurchaseRequisition::where('status', 'approved_cc')
            ->whereHas('items', function($query) {
                $query->where('remaining_quantity', '>', 0);
            })
            ->with(['warehouse:id,name', 'items.item', 'items.unit'])
            ->get();
        
        $suppliers = Supplier::where('status', 'active')->get();
        
        $this->logActivity(
            'purchase_orders',
            'VIEW',
            'Melihat form buat purchase order'
        );
        
        return view('purchasing.purchase-orders.create', compact('purchaseRequisitions', 'suppliers'));
    }
    
    public function getPRItems($id)
    {
        try {
            $pr = PurchaseRequisition::with(['items.item', 'items.unit'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'items' => $pr->items,
                'pr' => $pr
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting PR items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data item PR: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'po_date' => 'required|date',
                'purchase_requisition_ids' => 'required|array|min:1',
                'purchase_requisition_ids.*' => 'exists:purchase_requisitions,id',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.supplier_id' => 'required|exists:suppliers,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.uom_id' => 'required|exists:units,id',
                'items.*.price' => 'required|numeric|min:0',
                'creation_mode' => 'required|in:auto,manual'
            ]);
    
            DB::beginTransaction();
    
            $createdPOs = [];
            $mode = $request->creation_mode;
    
            if ($mode === 'auto') {
                $itemGrouping = json_decode($request->item_grouping, true);
    
                if (!$itemGrouping) {
                    throw new \Exception('Invalid item grouping data');
                }
    
                foreach ($itemGrouping as $supplierId => $group) {
                    $purchase_requisition_id = $request->purchase_requisition_ids[0];
                    
                    // Buat PO baru
                    $po = PurchaseOrder::create([
                        'po_number' => $this->generatePONumber(),
                        'po_date' => $request->po_date,
                        'supplier_id' => $supplierId,
                        'purchase_requisition_id' => $purchase_requisition_id,
                        'notes' => $request->notes,
                        'status' => 'draft',
                        'created_by' => auth()->id(),
                        'total' => 0
                    ]);
                    
                    $createdPOs[] = $po;
                    $totalPO = 0;
    
                    if (isset($group['items']) && is_array($group['items'])) {
                        foreach ($group['items'] as $item) {
                            // Log data item untuk debugging
                            \Log::info('Processing item:', ['item' => $item]);
                            
                            // Cari item di database berdasarkan nama
                            $dbItem = \App\Models\Item::where('name', $item['name'])->first();
                            if (!$dbItem) {
                                \Log::warning('Item tidak ditemukan di database:', ['name' => $item['name']]);
                                continue;
                            }
                            
                            // Cari unit berdasarkan nama
                            $unit = \App\Models\Unit::where('name', $item['unit'])->first();
                            if (!$unit) {
                                \Log::warning('Unit tidak ditemukan:', ['unit' => $item['unit']]);
                                continue;
                            }
    
                            // Cari PR berdasarkan PR Ref
                            $prNumber = $item['prRef'];
                            $pr = PurchaseRequisition::where('pr_number', $prNumber)->first();
                            if (!$pr) {
                                \Log::warning('PR tidak ditemukan:', ['pr_number' => $prNumber]);
                                continue;
                            }
                            
                            $prItem = PurchaseRequisitionItem::where('purchase_requisition_id', $pr->id)
                                ->where('item_id', $dbItem->id)
                                ->first();
    
                            // Hitung subtotal
                            $subtotal = $item['quantity'] * $item['price'];
                            
                            // Simpan PO item
                            $poItem = $po->items()->create([
                                'item_id' => $dbItem->id,
                                'quantity' => $item['quantity'],
                                'uom_id' => $unit->id,
                                'price' => $item['price'],
                                'subtotal' => $subtotal,
                                'total' => $subtotal,
                                'supplier_id' => $supplierId,
                                'purchase_requisition_item_id' => $prItem ? $prItem->id : null
                            ]);
                            
                            // Update remaining quantity di PR item jika ada
                            if ($prItem) {
                                $prItem->remaining_quantity = max(0, $prItem->remaining_quantity - $item['quantity']);
                                $prItem->save();
                            }
                            
                            $totalPO += $subtotal;
                        }
                    }
                    
                    // Update total PO
                    $po->update(['total' => $totalPO]);
                }
            } else {
                // Mode Manual
                $itemsBySupplier = collect($request->items)->groupBy('supplier_id');
                
                foreach ($itemsBySupplier as $supplierId => $items) {
                    $purchase_requisition_id = $request->purchase_requisition_ids[0];
                    
                    // Buat PO baru
                    $po = PurchaseOrder::create([
                        'po_number' => $this->generatePONumber(),
                        'po_date' => $request->po_date,
                        'supplier_id' => $supplierId,
                        'purchase_requisition_id' => $purchase_requisition_id,
                        'notes' => $request->notes,
                        'status' => 'draft',
                        'created_by' => auth()->id(),
                        'total' => 0 // akan diupdate setelah items disimpan
                    ]);
                    
                    $createdPOs[] = $po;
                    $totalPO = 0;
                    
                    foreach ($items as $item) {
                        // Hitung subtotal
                        $subtotal = $item['quantity'] * $item['price'];
                        
                        // Simpan PO item
                        $poItem = $po->items()->create([
                            'item_id' => $item['item_id'],
                            'quantity' => $item['quantity'],
                            'uom_id' => $item['uom_id'],
                            'price' => $item['price'],
                            'subtotal' => $subtotal,
                            'total' => $subtotal,
                            'supplier_id' => $supplierId,
                            'purchase_requisition_item_id' => $item['pr_item_id'] ?? null
                        ]);
                        
                        // Update remaining quantity di PR item
                        if (isset($item['pr_item_id'])) {
                            $prItem = PurchaseRequisitionItem::find($item['pr_item_id']);
                            if ($prItem) {
                                $prItem->remaining_quantity = max(0, $prItem->remaining_quantity - $item['quantity']);
                                $prItem->save();
                            }
                        }
                        
                        $totalPO += $subtotal;
                    }
                    
                    // Update total PO
                    $po->update(['total' => $totalPO]);
                }
            }
    
            // Update status PR
            foreach ($request->purchase_requisition_ids as $prId) {
                $pr = PurchaseRequisition::find($prId);
                if ($pr) {
                    $remainingItems = $pr->items()
                        ->where('remaining_quantity', '>', 0)
                        ->count();
    
                    if ($remainingItems === 0) {
                        $pr->update(['status' => 'po']);
                    }
                }
            }
    
            DB::commit();
    
            // Log activity untuk pembuatan PO
            foreach ($createdPOs as $po) {
                $this->logActivity(
                    'purchase_orders',
                    'CREATE',
                    'Membuat purchase order baru: ' . $po->po_number,
                    null,
                    $po->load(['items.item', 'items.supplier', 'purchaseRequisition'])->toArray()
                );
            }
    
            return response()->json([
                'success' => true,
                'message' => count($createdPOs) . ' Purchase Order berhasil disimpan',
                'redirect' => route('purchasing.purchase-orders.index')
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
            \Log::error('Error creating PO: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with([
            'purchaseRequisition',
            'items.item',
            'items.supplier',
            'creator'
        ])->findOrFail($id);
        
        $this->logActivity(
            'purchase_orders',
            'VIEW',
            'Melihat detail purchase order: ' . $purchaseOrder->po_number
        );
        
        return view('purchasing.purchase-orders.show', compact('purchaseOrder'));
    }
    
    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::with([
            'purchaseRequisition',
            'items.item',
            'items.supplier',
            'creator'
        ])->findOrFail($id);
        
        // Hanya PO dengan status draft yang bisa diedit
        if ($purchaseOrder->status !== 'draft') {
            return redirect()
                ->route('purchasing.purchase-orders.show', $purchaseOrder->id)
                ->with('error', __('translation.purchase_order.cannot_edit'));
        }
        
        $suppliers = Supplier::where('status', 'active')->get();
        
        // Ambil unit dari item yang ada di PO
        $itemUnits = [];
        foreach ($purchaseOrder->items as $poItem) {
            $item = $poItem->item;
            if ($item) {
                // Ambil unit besar
                if ($item->large_unit_id) {
                    $largeUnit = \App\Models\Unit::find($item->large_unit_id);
                    if ($largeUnit) {
                        $itemUnits[$largeUnit->id] = $largeUnit;
                    }
                }
                
                // Ambil unit sedang
                if ($item->medium_unit_id) {
                    $mediumUnit = \App\Models\Unit::find($item->medium_unit_id);
                    if ($mediumUnit) {
                        $itemUnits[$mediumUnit->id] = $mediumUnit;
                    }
                }
                
                // Ambil unit kecil
                if ($item->small_unit_id) {
                    $smallUnit = \App\Models\Unit::find($item->small_unit_id);
                    if ($smallUnit) {
                        $itemUnits[$smallUnit->id] = $smallUnit;
                    }
                }
            }
        }
        
        // Convert ke array biasa
        $units = collect($itemUnits)->values();
        
        $this->logActivity(
            'purchase_orders',
            'VIEW',
            'Melihat form edit purchase order: ' . $purchaseOrder->po_number
        );
        
        return view('purchasing.purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'units'));
    }
    
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($id);
            
            // Hanya PO dengan status draft yang bisa diupdate
            if ($purchaseOrder->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => __('translation.purchase_order.cannot_edit')
                ], 400);
            }
            
            // Validasi request
            $validated = $request->validate([
                'po_date' => 'required|date',
                'supplier_id' => 'required|exists:suppliers,id',
                'items' => 'required|array',
                'items.*.purchase_order_item_id' => 'required|exists:purchase_order_items,id',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.unit_id' => 'required|exists:units,id',
                'items.*.price' => 'required|numeric|min:0'
            ]);
            
            // Backup data lama untuk activity log
            $oldData = $purchaseOrder->load(['items.item', 'items.supplier', 'purchaseRequisition'])->toArray();
            
            // Update PO
            $purchaseOrder->update([
                'po_date' => $request->po_date,
                'supplier_id' => $request->supplier_id
            ]);
            
            // Update items
            $itemIdsToKeep = [];
            $totalPO = 0;
            
            foreach ($request->items as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['price'];
                
                // Update existing item
                $poItem = PurchaseOrderItem::find($itemData['purchase_order_item_id']);
                if (!$poItem) {
                    throw new \Exception('Item tidak ditemukan');
                }
                
                $poItem->update([
                    'quantity' => $itemData['quantity'],
                    'unit_id' => $itemData['unit_id'],
                    'price' => $itemData['price'],
                    'subtotal' => $subtotal,
                    'total' => $subtotal
                ]);
                
                $itemIdsToKeep[] = $poItem->id;
                $totalPO += $subtotal;
                
                // Update price history
                ItemPriceHistory::updateOrCreate(
                    [
                        'item_id' => $itemData['item_id'],
                        'supplier_id' => $request->supplier_id,
                        'purchase_order_id' => $purchaseOrder->id
                    ],
                    [
                        'price' => $itemData['price'],
                        'price_date' => $request->po_date
                    ]
                );
            }
            
            // Delete items not in the request
            PurchaseOrderItem::where('purchase_order_id', $purchaseOrder->id)
                           ->whereNotIn('id', $itemIdsToKeep)
                           ->delete();
            
            // Update total PO
            $purchaseOrder->update([
                'total' => $totalPO
            ]);
            
            DB::commit();
            
            // Log activity untuk update PO
            $newData = $purchaseOrder->fresh()->load(['items.item', 'items.supplier', 'purchaseRequisition'])->toArray();
            $this->logActivity(
                'purchase_orders',
                'UPDATE',
                'Mengupdate purchase order: ' . $purchaseOrder->po_number,
                $oldData,
                $newData
            );
            
            return response()->json([
                'success' => true,
                'message' => __('translation.purchase_order.success_update'),
                'redirect' => route('purchasing.purchase-orders.show', $purchaseOrder->id)
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
            \Log::error('Error updating PO: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('translation.purchase_order.message.save_error')
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $purchaseOrder = PurchaseOrder::with(['items.item', 'items.supplier', 'purchaseRequisition'])->findOrFail($id);
            
            // Hanya PO dengan status draft yang bisa dihapus
            if ($purchaseOrder->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => __('translation.purchase_order.cannot_delete')
                ]);
            }
            
            // Backup data untuk activity log
            $oldData = $purchaseOrder->toArray();
            
            // Kembalikan status PR ke approved_cc
            $purchaseRequisition = $purchaseOrder->purchaseRequisition;
            $purchaseRequisition->status = 'approved_cc';
            $purchaseRequisition->save();
            
            // Hapus PO
            $purchaseOrder->delete();
            
            $this->logActivity(
                'purchase_orders',
                'DELETE',
                'Menghapus purchase order: ' . $purchaseOrder->po_number,
                $oldData,
                null
            );
            
            return response()->json([
                'success' => true,
                'message' => __('translation.purchase_order.success_delete')
            ]);
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Purchase Order: ' . $e->getMessage()
            ]);
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
                    'message' => 'Anda tidak memiliki akses untuk menyetujui PO'
                ], 403);
            }

            $po = PurchaseOrder::findOrFail($id);
            
            // Cek apakah PO masih draft
            if ($po->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'PO hanya bisa disetujui saat status draft'
                ], 400);
            }

            DB::beginTransaction();

            $oldData = $po->load(['items.item', 'items.supplier', 'purchaseRequisition'])->toArray();

            // Update status PO
            $po->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            // Log aktivitas
            $newData = $po->fresh()->load(['items.item', 'items.supplier', 'purchaseRequisition'])->toArray();
            $this->logActivity(
                'purchase_orders',
                'APPROVE',
                'Menyetujui purchase order: ' . $po->po_number,
                $oldData,
                $newData
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('translation.purchase_order.message.success_approve')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error approving PO: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyetujui PO: ' . $e->getMessage()
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
                    'message' => 'Anda tidak memiliki akses untuk menolak PO'
                ], 403);
            }

            $po = PurchaseOrder::findOrFail($id);
            
            // Cek apakah PO masih draft
            if ($po->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'PO hanya bisa ditolak saat status draft'
                ], 400);
            }

            DB::beginTransaction();

            $oldData = $po->load(['items.item', 'items.supplier', 'purchaseRequisition'])->toArray();

            // Update status PO
            $po->update([
                'status' => 'cancelled',
                'rejected_by' => auth()->id(),
                'rejected_at' => now()
            ]);

            // Kembalikan remaining quantity PR items
            foreach ($po->items as $item) {
                if ($item->purchase_requisition_item_id) {
                    $prItem = PurchaseRequisitionItem::find($item->purchase_requisition_item_id);
                    if ($prItem) {
                        $prItem->remaining_quantity += $item->quantity;
                        $prItem->save();
                    }
                }
            }

            // Log aktivitas
            $newData = $po->fresh()->load(['items.item', 'items.supplier', 'purchaseRequisition'])->toArray();
            $this->logActivity(
                'purchase_orders',
                'REJECT',
                'Menolak purchase order: ' . $po->po_number,
                $oldData,
                $newData
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('translation.purchase_order.message.success_reject')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error rejecting PO: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menolak PO: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generatePONumber()
    {
        $prefix = 'PO';
        $date = now()->format('Ymd');
        
        // Get last PO number for today
        $lastPO = PurchaseOrder::where('po_number', 'like', "{$prefix}/{$date}/%")
            ->orderBy('po_number', 'desc')
            ->first();

        if ($lastPO) {
            $lastNumber = intval(substr($lastPO->po_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}/{$date}/{$newNumber}";
    }

    /**
     * Get items from a Purchase Requisition
     */
    public function getPurchaseRequisitionItems($id)
    {
        try {
            $pr = \App\Models\PurchaseRequisition::with(['items.item', 'items.unit'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'items' => $pr->items,
                'pr' => $pr
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting PR items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data item PR: ' . $e->getMessage()
            ], 500);
        }
    }

    public function fetchPRItems(Request $request)
    {
        try {
            $id = $request->input('pr_id');
            $pr = \App\Models\PurchaseRequisition::with(['items.item', 'items.unit'])->findOrFail($id);
            
            // Tambahkan informasi harga untuk setiap item
            $itemsWithPrices = [];
            
            foreach ($pr->items as $item) {
                // Dapatkan history harga item dari purchase order sebelumnya
                $priceHistory = \App\Models\PurchaseOrderItem::where('item_id', $item->item_id)
                    ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                    ->select(
                        'purchase_order_items.price',
                        'purchase_orders.po_date as date',
                        'purchase_orders.po_number'
                    )
                    ->orderBy('purchase_orders.po_date', 'desc')
                    ->get();
                
                // Dapatkan harga terakhir
                $lastPrice = null;
                $lastPriceDate = null;
                $lastPricePoNumber = null;
                
                if ($priceHistory->count() > 0) {
                    $lastPrice = $priceHistory->first()->price;
                    $lastPriceDate = $priceHistory->first()->date;
                    $lastPricePoNumber = $priceHistory->first()->po_number;
                }
                
                // Dapatkan harga terendah
                $lowestPrice = null;
                $lowestPriceDate = null;
                $lowestPricePoNumber = null;
                
                if ($priceHistory->count() > 0) {
                    $lowestPriceItem = $priceHistory->sortBy('price')->first();
                    $lowestPrice = $lowestPriceItem->price;
                    $lowestPriceDate = $lowestPriceItem->date;
                    $lowestPricePoNumber = $lowestPriceItem->po_number;
                }
                
                // Dapatkan harga tertinggi
                $highestPrice = null;
                $highestPriceDate = null;
                $highestPricePoNumber = null;
                
                if ($priceHistory->count() > 0) {
                    $highestPriceItem = $priceHistory->sortByDesc('price')->first();
                    $highestPrice = $highestPriceItem->price;
                    $highestPriceDate = $highestPriceItem->date;
                    $highestPricePoNumber = $highestPriceItem->po_number;
                }
                
                // Tambahkan informasi harga ke item
                $itemData = $item->toArray();
                $itemData['last_price'] = $lastPrice;
                $itemData['last_price_date'] = $lastPriceDate;
                $itemData['last_price_po_number'] = $lastPricePoNumber;
                
                $itemData['lowest_price'] = $lowestPrice;
                $itemData['lowest_price_date'] = $lowestPriceDate;
                $itemData['lowest_price_po_number'] = $lowestPricePoNumber;
                
                $itemData['highest_price'] = $highestPrice;
                $itemData['highest_price_date'] = $highestPriceDate;
                $itemData['highest_price_po_number'] = $highestPricePoNumber;
                
                $itemsWithPrices[] = $itemData;
            }
            
            return response()->json([
                'success' => true,
                'items' => $itemsWithPrices,
                'pr' => $pr
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching PR items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data item PR: ' . $e->getMessage()
            ], 500);
        }
    }
} 