<?php

namespace App\Http\Controllers;

use App\Models\GoodReceive;
use App\Models\PurchaseOrder;
use App\Models\GoodReceiveItem;
use App\Models\Inventory;
use App\Models\StockCard;
use App\Traits\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GoodReceiveController extends Controller
{
    use LogActivity;
    
    public function index()
    {
        $goodReceives = GoodReceive::with(['purchaseOrder', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $this->logActivity(
            'good_receives',
            'READ',
            'Mengakses daftar good receive'
        );

        return view('warehouse.good-receives.index', compact('goodReceives'));
    }

    public function create()
    {
        $this->logActivity(
            'good_receives',
            'VIEW',
            'Melihat form tambah good receive'
        );

        return view('warehouse.good-receives.create');
    }

    public function scanQR(Request $request)
    {
        try {
            $poNumber = $request->input('po_number');
            $purchaseOrder = PurchaseOrder::where('po_number', $poNumber)
                ->with(['items', 'supplier'])
                ->first();

            if (!$purchaseOrder) {
                return response()->json([
                    'success' => false,
                    'message' => trans('translation.good_receive.po_not_found')
                ]);
            }

            // Check if PO status is already received
            if ($purchaseOrder->status === 'received') {
                return response()->json([
                    'success' => false,
                    'message' => trans('translation.good_receive.po_already_received')
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $purchaseOrder
            ]);
        } catch (\Exception $e) {
            Log::error('Error scanning QR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => trans('translation.good_receive.message.save_error')
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Log request data
            Log::info('Store Request Data:', [
                'items' => $request->items
            ]);

            // Decode items JSON string to array
            $items = json_decode($request->items, true);
            
            // Log decoded items
            Log::info('Decoded Items:', $items);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid items data format');
            }

            // Validate request
            $validator = \Validator::make($request->all(), [
                'po_id' => 'required|exists:purchase_orders,id',
                'receive_date' => 'required|date',
                'notes' => 'nullable|string',
                'items' => 'required|string',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.quantity_received' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                throw new \Illuminate\Validation\ValidationException($validator);
            }

            $po = PurchaseOrder::with([
                'items.item', 
                'items.purchaseRequisitionItem',
                'purchaseRequisition'
            ])->findOrFail($request->po_id);
            
            $warehouseId = $po->purchaseRequisition->warehouse_id;

            // Validate quantities
            foreach ($items as $item) {
                $poItem = $po->items->where('item_id', $item['item_id'])->first();
                if (!$poItem) continue;

                $maxAllowedQty = $poItem->quantity * 1.1; // 110% dari quantity PO
                if ($item['quantity_received'] > $maxAllowedQty) {
                    throw new \Exception("Jumlah terima untuk item {$poItem->item->name} tidak boleh lebih dari " . number_format($maxAllowedQty, 2) . " (110% dari quantity PO)");
                }
            }

            // Create Good Receive
            $goodReceive = GoodReceive::create([
                'po_id' => $request->po_id,
                'gr_number' => 'GR-' . date('YmdHis'),
                'receive_date' => $request->receive_date,
                'notes' => $request->notes,
                'status' => 'approved',
                'created_by' => auth()->id(),
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            $totalAmount = 0;

            foreach ($items as $item) {
                $poItem = $po->items->where('item_id', $item['item_id'])->first();
                if (!$poItem) continue;

                // Log item data before creating good_receive_item
                Log::info('Creating GoodReceiveItem:', [
                    'item_id' => $item['item_id'],
                    'unit_id' => $item['unit_id'],
                    'po_item_uom_id' => $poItem->purchaseRequisitionItem->uom_id ?? null
                ]);

                $itemTotalAmount = $item['quantity_received'] * $poItem->price;
                $goodReceiveItem = GoodReceiveItem::create([
                    'good_receive_id' => $goodReceive->id,
                    'purchase_order_item_id' => $poItem->id,
                    'unit_id' => $poItem->purchaseRequisitionItem->uom_id,
                    'quantity' => $item['quantity_received'],
                    'price' => $poItem->price,
                    'subtotal' => $itemTotalAmount
                ]);

                $totalAmount += $itemTotalAmount;

                // Konversi quantity dan price untuk inventory dan stock card
                $conversionMultiplier = $poItem->item->getConversionMultiplier($poItem->purchaseRequisitionItem->uom_id);
                $quantityInSmallestUnit = $item['quantity_received'] * $conversionMultiplier;
                $pricePerSmallestUnit = $poItem->price / $conversionMultiplier;

                // Ambil harga terendah dan tertinggi dari good_receive_items dalam 3 bulan terakhir
                $threeMonthsAgo = now()->subMonths(3);
                $priceHistory = DB::table('good_receive_items as gri')
                    ->join('good_receives as gr', 'gr.id', '=', 'gri.good_receive_id')
                    ->join('purchase_order_items as poi', 'poi.id', '=', 'gri.purchase_order_item_id')
                    ->where('poi.item_id', $poItem->item_id)
                    ->where('gr.created_at', '>=', $threeMonthsAgo)
                    ->whereNull('gr.deleted_at')
                    ->whereNull('gri.deleted_at')
                    ->select(
                        'gri.price',
                        'gri.unit_id',
                        'poi.item_id'
                    )
                    ->get();

                // Log data harga yang ditemukan
                Log::info('Price History Data:', [
                    'item_id' => $poItem->item_id,
                    'prices' => $priceHistory->toArray()
                ]);

                // Konversi semua harga ke satuan terkecil
                $convertedPrices = $priceHistory->map(function($record) use ($poItem) {
                    // Ambil faktor konversi berdasarkan unit
                    $conversionFactor = 1;
                    if ($record->unit_id == $poItem->item->medium_unit_id) {
                        $conversionFactor = $poItem->item->small_conversion_qty;
                    } elseif ($record->unit_id == $poItem->item->large_unit_id) {
                        $conversionFactor = $poItem->item->small_conversion_qty;
                    }

                    // Konversi harga ke satuan terkecil
                    return $record->price / $conversionFactor;
                });

                // Tambahkan harga saat ini ke array harga
                $convertedPrices->push($pricePerSmallestUnit);

                // Update or create inventory
                $inventory = Inventory::firstOrNew([
                    'warehouse_id' => $warehouseId,
                    'item_id' => $poItem->item_id
                ]);

                $oldStock = $inventory->stock_on_hand ?? 0;
                $oldValue = $inventory->total_value ?? 0;
                $newValue = $quantityInSmallestUnit * $pricePerSmallestUnit;
                $totalStock = $oldStock + $quantityInSmallestUnit;
                $averageCost = $totalStock > 0 ? ($oldValue + $newValue) / $totalStock : 0;

                // Log untuk debugging
                Log::info('Converted Prices:', [
                    'item_id' => $poItem->item_id,
                    'all_prices' => $convertedPrices->toArray(),
                    'lowest' => $convertedPrices->min(),
                    'highest' => $convertedPrices->max()
                ]);

                // Update inventory dengan harga yang sudah dikonversi
                $inventory->stock_on_hand = $totalStock;
                $inventory->stock_available = $totalStock;
                $inventory->total_value = $oldValue + $newValue;
                $inventory->moving_average_cost = $averageCost;
                $inventory->last_purchase_price = $pricePerSmallestUnit;
                $inventory->lowest_purchase_price = $convertedPrices->min();
                $inventory->highest_purchase_price = $convertedPrices->max();

                $inventory->save();

                // Create stock card entry
                StockCard::create([
                    'warehouse_id' => $warehouseId,
                    'item_id' => $poItem->item_id,
                    'date' => $request->receive_date,
                    'reference_type' => 'GR',
                    'reference_id' => $goodReceive->id,
                    'reference_number' => $goodReceive->gr_number,
                    'qty_in' => $quantityInSmallestUnit,
                    'qty_out' => 0,
                    'stock_balance' => $totalStock,
                    'notes' => 'Good Receive from PO: ' . $po->po_number,
                    'created_by' => auth()->id(),
                    'unit_price' => $pricePerSmallestUnit,
                    'total_value' => $newValue,
                    'moving_average_cost' => $averageCost,
                    'old_stock_value' => $oldValue,
                    'new_stock_value' => $oldValue + $newValue
                ]);
            }

            // Update total amount in Good Receive
            $goodReceive->update(['total_amount' => $totalAmount]);

            // Update PO status to received
            $po->update(['status' => 'received']);

            $this->logActivity(
                'good_receives',
                'CREATE',
                'Membuat good receive baru: ' . $goodReceive->gr_number,
                null,
                $goodReceive->load(['items.item', 'purchaseOrder'])->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('translation.good_receive.success_create'),
                'redirect' => route('warehouse.good-receives.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in Good Receive store process: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function convertToSmallestUnit($quantity, $mediumConversion, $smallConversion)
    {
        // Convert to smallest unit based on conversion rates
        return $quantity * $mediumConversion * $smallConversion;
    }

    private function checkLowStockAlert($inventory)
    {
        // Implement low stock alert logic here
        // For example, if stock is below certain threshold
        if ($inventory->stock_on_hand < 10) {
            // Send notification or log alert
            \Log::warning("Low stock alert for item {$inventory->item_id} in warehouse {$inventory->warehouse_id}");
        }
    }

    public function show(GoodReceive $goodReceive)
    {
        $this->logActivity(
            'good_receives',
            'VIEW',
            'Melihat detail good receive: ' . $goodReceive->gr_number
        );

        $goodReceive->load(['purchaseOrder', 'items.purchaseOrderItem', 'creator', 'approver', 'rejector']);
        return view('warehouse.good-receives.show', compact('goodReceive'));
    }

    public function edit(GoodReceive $goodReceive)
    {
        if (!$goodReceive->canEdit()) {
            return redirect()->route('warehouse.good-receives.show', $goodReceive->id)
                ->with('error', trans('translation.good_receive.message.cannot_edit'));
        }

        $this->logActivity(
            'good_receives',
            'VIEW',
            'Melihat form edit good receive: ' . $goodReceive->gr_number
        );

        $goodReceive->load(['purchaseOrder', 'items.purchaseOrderItem']);
        return view('warehouse.good-receives.edit', compact('goodReceive'));
    }

    public function update(Request $request, GoodReceive $goodReceive)
    {
        if (!$goodReceive->canEdit()) {
            return response()->json([
                'success' => false,
                'message' => trans('translation.good_receive.message.cannot_edit')
            ]);
        }

        try {
            DB::beginTransaction();

            $oldData = $goodReceive->load(['items.item', 'purchaseOrder'])->toArray();

            $request->validate([
                'receive_date' => 'required|date',
                'notes' => 'nullable|string',
                'items' => 'required|array',
                'items.*.id' => 'required|exists:good_receive_items,id',
                'items.*.quantity' => 'required|numeric|min:0',
                'items.*.unit_id' => 'required|exists:units,id',
                'items.*.price' => 'required|numeric|min:0'
            ]);

            $goodReceive->update([
                'receive_date' => $request->receive_date,
                'notes' => $request->notes
            ]);

            $totalAmount = 0;

            foreach ($request->items as $item) {
                $goodReceiveItem = GoodReceiveItem::findOrFail($item['id']);
                $goodReceiveItem->update([
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                    'price' => $item['price']
                ]);

                $totalAmount += $goodReceiveItem->subtotal;
            }

            $goodReceive->update(['total_amount' => $totalAmount]);

            $newData = $goodReceive->fresh()->load(['items.item', 'purchaseOrder'])->toArray();
            
            $this->logActivity(
                'good_receives',
                'UPDATE',
                'Mengupdate good receive: ' . $goodReceive->gr_number,
                $oldData,
                $newData
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('translation.good_receive.message.success_update'),
                'redirect' => route('warehouse.good-receives.show', $goodReceive->id)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating good receive: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => trans('translation.good_receive.message.save_error')
            ]);
        }
    }

    public function destroy(GoodReceive $goodReceive)
    {
        if (!$goodReceive->canDelete()) {
            return response()->json([
                'success' => false,
                'message' => trans('translation.good_receive.message.cannot_delete')
            ]);
        }

        try {
            DB::beginTransaction();

            $oldData = $goodReceive->load(['items.item', 'purchaseOrder'])->toArray();

            $goodReceive->delete();

            $this->logActivity(
                'good_receives',
                'DELETE',
                'Menghapus good receive: ' . $goodReceive->gr_number,
                $oldData,
                null
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('translation.good_receive.message.success_delete')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting good receive: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => trans('translation.good_receive.message.error_delete')
            ]);
        }
    }

    public function approve(GoodReceive $goodReceive)
    {
        if (!$goodReceive->canApprove()) {
            return response()->json([
                'success' => false,
                'message' => trans('translation.good_receive.message.cannot_approve')
            ]);
        }

        try {
            DB::beginTransaction();

            $oldData = $goodReceive->load(['items.item', 'purchaseOrder'])->toArray();

            $goodReceive->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            $newData = $goodReceive->fresh()->load(['items.item', 'purchaseOrder'])->toArray();

            $this->logActivity(
                'good_receives',
                'APPROVE',
                'Menyetujui good receive: ' . $goodReceive->gr_number,
                $oldData,
                $newData
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('translation.good_receive.message.success_approve')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving good receive: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => trans('translation.good_receive.message.save_error')
            ]);
        }
    }

    public function reject(GoodReceive $goodReceive)
    {
        if (!$goodReceive->canReject()) {
            return response()->json([
                'success' => false,
                'message' => trans('translation.good_receive.message.cannot_reject')
            ]);
        }

        try {
            DB::beginTransaction();

            $oldData = $goodReceive->load(['items.item', 'purchaseOrder'])->toArray();

            $goodReceive->update([
                'status' => 'rejected',
                'rejected_by' => Auth::id(),
                'rejected_at' => now()
            ]);

            $newData = $goodReceive->fresh()->load(['items.item', 'purchaseOrder'])->toArray();

            $this->logActivity(
                'good_receives',
                'REJECT',
                'Menolak good receive: ' . $goodReceive->gr_number,
                $oldData,
                $newData
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('translation.good_receive.message.success_reject')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting good receive: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => trans('translation.good_receive.message.save_error')
            ]);
        }
    }

    public function searchPO(Request $request)
    {
        try {
            $request->validate([
                'po_number' => 'required|string'
            ]);

            $po = PurchaseOrder::with([
                'items.item',
                'items.purchaseRequisitionItem.unit',
                'supplier'
            ])
            ->where('po_number', $request->po_number)
            ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $po->id,
                    'po_number' => $po->po_number,
                    'supplier_name' => $po->supplier->name,
                    'items' => $po->items->map(function ($item) {
                        $uomId = $item->purchaseRequisitionItem ? $item->purchaseRequisitionItem->uom_id : null;
                        $unitName = $item->purchaseRequisitionItem && $item->purchaseRequisitionItem->unit 
                            ? $item->purchaseRequisitionItem->unit->name 
                            : 'N/A';
                        
                        Log::info('Item unit:', [
                            'item_id' => $item->item_id,
                            'uom_id' => $uomId,
                            'unit_name' => $unitName
                        ]);

                        return [
                            'id' => $item->item_id,
                            'name' => $item->item ? $item->item->name : 'Unknown Item',
                            'quantity' => $item->quantity,
                            'unit_id' => $uomId,
                            'unit' => $unitName
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in searchPO: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}