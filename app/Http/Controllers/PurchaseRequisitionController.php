<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use App\Models\Item;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;
use App\Traits\LogActivity;
use App\Models\ActivityLog;

class PurchaseRequisitionController extends Controller
{
    use LogActivity;

    public function index()
    {
        $purchaseRequisitions = PurchaseRequisition::with([
            'warehouse:id,name',
            'ssdApprover:id,nama_lengkap',
            'ccApprover:id,nama_lengkap'
        ])->get();

        $this->logActivity(
            'purchase_requisitions',
            'READ',
            'Mengakses daftar purchase requisition'
        );

        return view('purchasing.purchase-requisitions.index', compact('purchaseRequisitions'));
    }

    public function create()
    {
        $items = Item::with(['smallUnit', 'mediumUnit', 'largeUnit'])
            ->where('status', 'active')
            ->get();
        
        $units = Unit::where('status', 'active')->get();
        $warehouses = Warehouse::where('status', 'active')->get();
        
        return view('purchasing.purchase-requisitions.create', compact('items', 'units', 'warehouses'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'warehouse_id' => 'required|exists:warehouses,id',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.uom_id' => 'required|exists:units,id',
                'items.*.notes' => 'nullable|string'
            ], [
                'date.required' => 'Tanggal harus diisi',
                'warehouse_id.required' => 'Departemen harus dipilih',
                'items.required' => 'Minimal harus ada 1 item',
                'items.*.item_id.required' => 'Item harus dipilih',
                'items.*.quantity.required' => 'Quantity harus diisi',
                'items.*.quantity.min' => 'Quantity harus lebih dari 0',
                'items.*.uom_id.required' => 'Satuan harus dipilih'
            ]);

            DB::beginTransaction();

            // Ambil nama departemen dari warehouse
            $warehouse = \App\Models\Warehouse::findOrFail($request->warehouse_id);

            // Buat PR header
            $pr = PurchaseRequisition::create([
                'pr_number' => $this->generatePRNumber(),
                'date' => $request->date,
                'warehouse_id' => $request->warehouse_id,
                'department' => $warehouse->name,
                'notes' => $request->notes,
                'status' => 'draft',
                'created_by' => auth()->id(),
                'requested_by' => auth()->id()
            ]);

            // Simpan PR items
            foreach ($request->items as $item) {
                $pr->items()->create([
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'remaining_quantity' => $item['quantity'],
                    'uom_id' => $item['uom_id'],
                    'notes' => $item['notes'] ?? null
                ]);
            }

            // Log activity untuk pembuatan PR baru
            $this->logActivity(
                'purchase_requisitions',
                'CREATE',
                'Membuat purchase requisition baru: ' . $pr->pr_number,
                null,
                $pr->load(['items.item', 'items.unit', 'warehouse'])->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Requisition berhasil disimpan',
                'redirect' => route('purchasing.purchase-requisitions.index')
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error creating PR: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }
    }

    public function show($id)
    {
        $warehouseId = session('warehouse_id');
        
        // Log warehouse_id yang digunakan
        \Log::info('Show PR - Warehouse ID:', [
            'warehouse_id' => $warehouseId,
            'pr_id' => $id
        ]);

        $pr = PurchaseRequisition::with([
            'items.item.smallUnit',
            'items.item.mediumUnit',
            'items.item.largeUnit',
            'items.unit',
            'creator',
            'requester',
            'items.item.inventories' => function($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            }
        ])->findOrFail($id);

        // Log query yang dijalankan
        \DB::enableQueryLog();
        $pr->load('items.item.inventories');
        \Log::info('PR Queries:', [
            'queries' => \DB::getQueryLog()
        ]);

        // Log data items dan inventories
        foreach ($pr->items as $item) {
            \Log::info('Item Data:', [
                'item_id' => $item->item_id,
                'item_name' => $item->item->name,
                'inventories' => $item->item->inventories->toArray(),
                'warehouse_id' => $warehouseId,
                'medium_conversion' => $item->item->medium_conversion_qty,
                'small_conversion' => $item->item->small_conversion_qty
            ]);
        }

        $this->logActivity(
            'purchase_requisitions',
            'VIEW',
            'Melihat detail purchase requisition: ' . $pr->pr_number
        );

        // Debug user data
        $user = auth()->user();
        \Log::info('User Show Access:', [
            'user_id' => $user->id,
            'jabatan_id' => $user->id_jabatan,
            'roles' => $user->user_roles->pluck('role_id')->toArray()
        ]);

        return view('purchasing.purchase-requisitions.show', compact('pr'));
    }

    public function edit($id)
    {
        try {
            $pr = PurchaseRequisition::with([
                'items.item',     // Load item
                'items.unit',     // Load unit yang dipilih
                'warehouse'
            ])->findOrFail($id);

            $warehouses = Warehouse::all();
            $units = Unit::where('status', 'active')->get(); // Ambil semua unit yang aktif
            
            return view('purchasing.purchase-requisitions.edit', compact('pr', 'warehouses', 'units'));
        } catch (\Exception $e) {
            \Log::error('Error in edit method:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->route('purchasing.purchase-requisitions.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'warehouse_id' => 'required|exists:warehouses,id',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.uom_id' => 'required|exists:units,id',
                'items.*.notes' => 'nullable|string'
            ], [
                'date.required' => 'Tanggal harus diisi',
                'date.date' => 'Format tanggal tidak valid',
                'warehouse_id.required' => 'Departemen harus dipilih',
                'warehouse_id.exists' => 'Departemen tidak valid',
                'items.required' => 'Minimal harus ada 1 item',
                'items.min' => 'Minimal harus ada 1 item',
                'items.*.item_id.required' => 'Item harus dipilih',
                'items.*.item_id.exists' => 'Item tidak valid',
                'items.*.quantity.required' => 'Quantity harus diisi',
                'items.*.quantity.numeric' => 'Quantity harus berupa angka',
                'items.*.quantity.min' => 'Quantity harus lebih dari 0',
                'items.*.uom_id.required' => 'Satuan harus dipilih',
                'items.*.uom_id.exists' => 'Satuan tidak valid'
            ]);

            DB::beginTransaction();

            $pr = PurchaseRequisition::findOrFail($id);
            $oldData = $pr->load(['items.item', 'items.unit', 'warehouse'])->toArray();
            
            // Update PR header
            $warehouse = Warehouse::findOrFail($request->warehouse_id);
            $pr->update([
                'date' => $request->date,
                'warehouse_id' => $request->warehouse_id,
                'department' => $warehouse->name,
                'notes' => $request->notes,
                'updated_by' => auth()->id()
            ]);

            // Delete existing items
            $pr->items()->delete();

            // Create new items
            foreach ($request->items as $item) {
                $pr->items()->create([
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'remaining_quantity' => $item['quantity'],
                    'uom_id' => $item['uom_id'],
                    'notes' => $item['notes'] ?? null
                ]);
            }

            // Log activity untuk update PR
            $newData = $pr->fresh()->load(['items.item', 'items.unit', 'warehouse'])->toArray();
            $this->logActivity(
                'purchase_requisitions',
                'UPDATE',
                'Mengupdate Purchase Requisition: ' . $pr->pr_number,
                $oldData,
                $newData
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Requisition berhasil diupdate',
                'redirect' => route('purchasing.purchase-requisitions.show', $pr->id)
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating PR: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $pr = PurchaseRequisition::with(['items.item', 'items.unit', 'warehouse'])->findOrFail($id);

            if ($pr->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya PR dengan status draft yang dapat dihapus'
                ], 403);
            }

            $oldData = $pr->toArray();

            DB::beginTransaction();
            
            $pr->items()->delete();
            $pr->delete();

            $this->logActivity(
                'purchase_requisitions',
                'DELETE',
                'Menghapus purchase requisition: ' . $pr->pr_number,
                $oldData,
                null
            );

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Purchase Requisition berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submit($id)
    {
        $pr = PurchaseRequisition::findOrFail($id);

        if ($pr->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya PR dengan status draft yang dapat disubmit'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $pr->update([
                'status' => 'waiting_approval_1',
                'updated_by' => Auth::id()
            ]);

            // Disini bisa ditambahkan notifikasi ke approver level 1

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Purchase Requisition berhasil disubmit untuk approval'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generatePRNumber()
    {
        $prefix = 'PR';
        $date = now()->format('Ymd');
        
        // Get last PR number for today
        $lastPR = PurchaseRequisition::where('pr_number', 'like', "{$prefix}/{$date}/%")
            ->orderBy('pr_number', 'desc')
            ->first();

        if ($lastPR) {
            $lastNumber = intval(substr($lastPR->pr_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}/{$date}/{$newNumber}";
    }

    public function getItems(Request $request)
    {
        $term = $request->get('term');
        $warehouseId = $request->get('warehouse_id');

        $items = Item::select('items.*')
            ->with(['unit'])
            ->leftJoin('inventories', function($join) use ($warehouseId) {
                $join->on('items.id', '=', 'inventories.item_id')
                     ->where('inventories.warehouse_id', '=', $warehouseId);
            })
            ->where(function($query) use ($term) {
                $query->where('items.name', 'LIKE', "%{$term}%")
                      ->orWhere('items.code', 'LIKE', "%{$term}%");
            })
            ->addSelect([
                'inventories.stock_on_hand',
                'inventories.stock_available'
            ])
            ->get();

        return response()->json($items);
    }

    public function approve(Request $request, $id)
    {
        try {
            // Validasi tipe approval
            $request->validate([
                'type' => 'required|in:ssd,cc'
            ]);

            // Ambil data PR
            $pr = PurchaseRequisition::findOrFail($id);
            $oldData = $pr->load(['items.item', 'items.unit', 'warehouse'])->toArray();
            
            // Ambil user yang sedang login
            $user = auth()->user();

            // Cek apakah user memiliki hak untuk approve
            $canApproveSSD = $user->id_jabatan == 172 || $user->id_jabatan == 161 || $user->hasRole(1);
            $canApproveCC = $user->id_jabatan == 167 || $user->hasRole(1);

            if ($request->type === 'ssd') {
                // Cek apakah user bisa approve SSD
                if (!$canApproveSSD) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki hak untuk melakukan approval SSD'
                    ], 403);
                }

                // Cek status PR
                if ($pr->status !== 'draft' && $pr->status !== null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PR ini tidak dalam status draft'
                    ], 400);
                }

                // Update status PR
                $pr->update([
                    'status' => 'approved_ssd',
                    'approved_ssd_by' => $user->id,
                    'approved_ssd_at' => now()
                ]);

                // Log activity
                $newData = $pr->fresh()->load(['items.item', 'items.unit', 'warehouse'])->toArray();
                $this->logActivity(
                    'purchase_requisitions',
                    'APPROVE',
                    'Approve SSD untuk PR: ' . $pr->pr_number,
                    $oldData,
                    $newData
                );

                return response()->json([
                    'success' => true,
                    'message' => 'PR berhasil diapprove oleh SSD'
                ]);
            }

            if ($request->type === 'cc') {
                // Cek apakah user bisa approve CC
                if (!$canApproveCC) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki hak untuk melakukan approval Cost Control'
                    ], 403);
                }

                // Cek status PR
                if ($pr->status !== 'approved_ssd') {
                    return response()->json([
                        'success' => false,
                        'message' => 'PR ini belum diapprove oleh SSD'
                    ], 400);
                }

                // Update status PR
                $pr->update([
                    'status' => 'approved_cc',
                    'approved_cc_by' => $user->id,
                    'approved_cc_at' => now()
                ]);

                // Log activity
                $newData = $pr->fresh()->load(['items.item', 'items.unit', 'warehouse'])->toArray();
                $this->logActivity(
                    'purchase_requisitions',
                    'APPROVE',
                    'Approve Cost Control untuk PR: ' . $pr->pr_number,
                    $oldData,
                    $newData
                );

                return response()->json([
                    'success' => true,
                    'message' => 'PR berhasil diapprove oleh Cost Control'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tipe approval tidak valid'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Error in approve PR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get items from a specific Purchase Requisition
     */
    public function getPRItems($id)
    {
        try {
            $pr = PurchaseRequisition::with(['items.item', 'items.unit'])->findOrFail($id);
            
            $items = $pr->items->map(function($item) use ($pr) {
                $inventory = \DB::table('inventories')
                    ->where('warehouse_id', $pr->warehouse_id)
                    ->where('item_id', $item->item_id)
                    ->select('last_purchase_price', 'lowest_purchase_price', 'highest_purchase_price')
                    ->first();

                // Hitung faktor konversi
                $conversionFactor = 1;
                if ($item->uom_id == $item->item->medium_unit_id) {
                    // Jika unit yang dipilih adalah medium unit (Pack)
                    // 1 Pack = 250 gram, maka harga per Pack = harga per gram * 250
                    $conversionFactor = $item->item->small_conversion_qty;
                } elseif ($item->uom_id == $item->item->large_unit_id) {
                    // Jika unit yang dipilih adalah large unit
                    // Large ke medium = 1, medium ke small = 250
                    // Jadi total konversi = 1 * 250 = 250
                    $conversionFactor = $item->item->small_conversion_qty;
                }

                // Konversi harga
                $lastPrice = $inventory ? ($inventory->last_purchase_price * $conversionFactor) : 0;
                $lowestPrice = $inventory ? ($inventory->lowest_purchase_price * $conversionFactor) : 0;
                $highestPrice = $inventory ? ($inventory->highest_purchase_price * $conversionFactor) : 0;

                \Log::info('Konversi Harga:', [
                    'item' => $item->item->name,
                    'unit_dipilih' => $item->unit->name,
                    'conversion_data' => [
                        'medium_conversion_qty' => $item->item->medium_conversion_qty,
                        'small_conversion_qty' => $item->item->small_conversion_qty
                    ],
                    'conversion_factor' => $conversionFactor,
                    'harga_dasar' => [
                        'last' => $inventory ? $inventory->last_purchase_price : 0,
                        'lowest' => $inventory ? $inventory->lowest_purchase_price : 0,
                        'highest' => $inventory ? $inventory->highest_purchase_price : 0
                    ],
                    'harga_konversi' => [
                        'last' => $lastPrice,
                        'lowest' => $lowestPrice,
                        'highest' => $highestPrice
                    ]
                ]);

                return [
                    'id' => $item->id,
                    'item_id' => $item->item_id,
                    'item' => $item->item,
                    'quantity' => $item->quantity,
                    'uom_id' => $item->uom_id,
                    'unit' => $item->unit,
                    'last_price' => $lastPrice,
                    'lowest_price' => $lowestPrice,
                    'highest_price' => $highestPrice
                ];
            });

            return response()->json([
                'success' => true,
                'pr' => $pr,
                'items' => $items
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getPRItems:', [
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getConversions($id)
    {
        $item = Item::findOrFail($id);
        
        return response()->json([
            'medium' => floatval($item->medium_conversion_qty),
            'large' => floatval($item->small_conversion_qty)
        ]);
    }

    public function getUnits($id)
    {
        $item = Item::with(['smallUnit', 'mediumUnit', 'largeUnit'])->findOrFail($id);
        
        // Debug
        \Log::info('Item data:', [
            'id' => $item->id,
            'medium_conversion_qty' => $item->medium_conversion_qty,
            'small_conversion_qty' => $item->small_conversion_qty,
            'units' => [
                'small' => $item->smallUnit,
                'medium' => $item->mediumUnit,
                'large' => $item->largeUnit
            ]
        ]);

        return [
            'units' => [
                [
                    'id' => $item->large_unit_id,
                    'name' => optional($item->largeUnit)->name,
                    'is_largest' => true,
                    'conversion' => floatval($item->small_conversion_qty)
                ],
                [
                    'id' => $item->medium_unit_id,
                    'name' => optional($item->mediumUnit)->name,
                    'is_largest' => false,
                    'conversion' => floatval($item->medium_conversion_qty)
                ],
                [
                    'id' => $item->small_unit_id,
                    'name' => optional($item->smallUnit)->name,
                    'is_largest' => false,
                    'conversion' => 1
                ]
            ],
            'conversions' => [
                'medium' => floatval($item->medium_conversion_qty),
                'large' => floatval($item->small_conversion_qty)
            ]
        ];
    }

    public function getItemDetail($id)
    {
        $item = Item::findOrFail($id);
        
        return response()->json([
            'id' => $item->id,
            'medium_conversion_qty' => floatval($item->medium_conversion_qty),
            'small_conversion_qty' => floatval($item->small_conversion_qty)
        ]);
    }
} 