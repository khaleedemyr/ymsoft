<?php

namespace App\Http\Controllers;

use App\Models\FloorOrder;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Traits\LogActivity;
use App\Models\FloorOrderItem;
use Yajra\DataTables\DataTables;
use App\Models\Warehouse;

class FloorOrderController extends Controller
{
    use LogActivity;

    public function __construct()
    {
        $this->middleware('check.floor.order.status')->only(['edit', 'update', 'destroy']);
    }

    public function index()
    {
        $floorOrders = FloorOrder::with([
            'warehouse:id,name',
            'user:id,nama_lengkap'
        ])->get();

        return view('floor-orders.index', compact('floorOrders'));
    }

    public function create()
    {
        $warehouses = DB::table('warehouses')
            ->select('id', 'name', 'code')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        // Ambil ID kategori Main Kitchen
        $mainKitchenCategoryId = Category::where('name', 'Main Kitchen')
            ->value('id');

        // Ambil semua items dengan kategorinya
        $items = Item::with(['category'])
            ->where('status', 'active')
            ->select('id', 'name', 'sku', 'category_id')
            ->orderBy('name')
            ->get();

        $draftId = null; // Ini akan diisi oleh auto-save
        return view('floor-orders.create', compact('warehouses', 'items', 'mainKitchenCategoryId', 'draftId'));
    }

    private function roundUpToHundred($number) 
    {
        return ceil($number / 100) * 100;
    }

    public function getItemsByWarehouse($warehouseCode)
    {
        try {
            \Log::info("Getting items for warehouse: " . $warehouseCode);

            $mainKitchenCategoryId = 14;
        $user = auth()->user();
            
            $userOutletId = $user->id_outlet;
            $userRegionId = DB::table('tbl_data_outlet')
                ->where('id_outlet', $userOutletId)
                ->value('region_id');

            $query = Item::with([
                'category', 
                'subCategory', 
                'mediumUnit',
                'prices' => function($query) use ($userRegionId) {
                    $query->where('region_id', $userRegionId);
                },
                'availabilities'
            ])
            ->where('status', 'active')
            ->where(function($query) use ($userRegionId, $userOutletId) {
                $query->whereHas('availabilities', function($q) {
                    $q->where('availability_type', 'all');
                })
                ->orWhereHas('availabilities', function($q) use ($userRegionId) {
                    $q->where('availability_type', 'region')
                      ->where('region_id', $userRegionId);
                })
                ->orWhereHas('availabilities', function($q) use ($userOutletId) {
                    $q->where('availability_type', 'outlet')
                      ->where('outlet_id', $userOutletId);
                });
            });

            if ($warehouseCode === 'MK') {
                $query->where('category_id', $mainKitchenCategoryId);
            } else {
                $query->where('category_id', '!=', $mainKitchenCategoryId);
            }

            $items = $query->get();

            // Group items
            $groupedItems = [];
            foreach ($items as $item) {
                $categoryName = $item->category->name ?? 'Uncategorized';
                $subCategoryName = $item->subCategory->name ?? 'Uncategorized';
                
                if (!isset($groupedItems[$categoryName])) {
                    $groupedItems[$categoryName] = [];
                }
                if (!isset($groupedItems[$categoryName][$subCategoryName])) {
                    $groupedItems[$categoryName][$subCategoryName] = [];
                }
                
                $price = $item->prices->first();
                $mediumPrice = $price ? $this->roundUpToHundred($price->price / $item->medium_conversion_qty) : 0;
                
                $groupedItems[$categoryName][$subCategoryName][] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'medium_unit' => $item->mediumUnit->name ?? '-',
                    'price' => $mediumPrice,
                    'original_price' => $price ? $this->roundUpToHundred($price->price) : 0,
                    'medium_conversion_qty' => $item->medium_conversion_qty
                ];
            }

            \Log::info("Items retrieved successfully", ['count' => count($groupedItems)]);
            
            return response()->json([
                'success' => true,
                'data' => $groupedItems,
                'debug' => [
                    'user_outlet_id' => $userOutletId,
                    'user_region_id' => $userRegionId
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error("Error getting items: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $user = auth()->user();
            
            $floorOrder = FloorOrder::create([
                'warehouse_id' => $request->warehouse_id,
                'arrival_date' => $request->arrival_date,
                'notes' => $request->notes,
                'status' => 'submitted', // atau status yang sesuai
                'created_by' => $user->id,
                'id_outlet' => $user->id_outlet,
                'fo_number' => $this->generateFoNumber(),
                'order_date' => now()
            ]);

            $items = json_decode($request->items, true);
            foreach ($items as $itemId => $itemData) {
                $floorOrder->items()->create([
                    'item_id' => $itemId,
                    'qty' => $itemData['qty'],
                    'price' => $itemData['price'],
                    'total' => $itemData['total']
                ]);
            }

            // Update total_amount
            $totalAmount = $floorOrder->items()->sum('total');
            $floorOrder->update(['total_amount' => $totalAmount]);

            // Log activity untuk pembuatan floor order baru
            $this->logActivity(
                'CREATE',
                'floor-orders',
                'Membuat floor order baru: ' . $floorOrder->fo_number,
                null,
                $floorOrder->load(['floorOrderItems'])->toArray()
            );

            DB::commit();

        return response()->json([
            'success' => true,
                'message' => 'Floor Order berhasil disimpan',
                'redirect' => route('floor-orders.index')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error saving floor order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan Floor Order'
            ], 500);
        }
    }

    public function storeDraft(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $user = auth()->user();
            
            // Cek apakah sudah ada draft untuk warehouse dan tanggal yang sama
            $existingDraft = FloorOrder::where('warehouse_id', $request->warehouse_id)
                ->where('arrival_date', $request->arrival_date)
            ->where('status', 'draft')
                ->where('created_by', $user->id)
            ->first();

            if ($existingDraft) {
                // Jika sudah ada, gunakan yang ada
                $draftId = $existingDraft->id;
                
                // Update items jika ada
                if (!empty($request->items)) {
                    foreach ($request->items as $itemId => $itemData) {
                        $existingDraft->items()->updateOrCreate(
                            ['item_id' => $itemId],
                            [
                                'qty' => $itemData['qty'],
                                'price' => $itemData['price'],
                                'total' => $itemData['total']
                            ]
                        );
                    }
                }

                // Update total_amount
                $totalAmount = $existingDraft->items()->sum('total');
                $existingDraft->update(['total_amount' => $totalAmount]);

            } else {
                // Buat draft baru
                $floorOrder = FloorOrder::create([
                    'warehouse_id' => $request->warehouse_id,
                    'arrival_date' => $request->arrival_date,
                    'notes' => $request->notes,
                    'status' => 'draft',
                    'created_by' => $user->id,
                    'id_outlet' => $user->id_outlet,
                    'fo_number' => $this->generateFoNumber(),
                    'order_date' => now(),
                    'total_amount' => 0
                ]);

                // Simpan items jika ada
                if (!empty($request->items)) {
                    foreach ($request->items as $itemId => $itemData) {
                        $floorOrder->items()->create([
                            'item_id' => $itemId,
                            'qty' => $itemData['qty'],
                            'price' => $itemData['price'],
                            'total' => $itemData['total']
                        ]);
                    }

                    // Update total_amount
                    $totalAmount = $floorOrder->items()->sum('total');
                    $floorOrder->update(['total_amount' => $totalAmount]);
                }

                $draftId = $floorOrder->id;
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'draft_id' => $draftId,
                'message' => 'Draft berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error saving draft: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan draft'
            ], 500);
        }
    }

    public function updateDraft(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $floorOrder = FloorOrder::findOrFail($id);
            
            // Update header jika ada perubahan
            $floorOrder->update([
                'warehouse_id' => $request->warehouse_id,
                'arrival_date' => $request->arrival_date,
                'notes' => $request->notes
            ]);

            // Update items
            if (!empty($request->items)) {
                foreach ($request->items as $itemId => $itemData) {
                    $floorOrder->items()->updateOrCreate(
                        ['item_id' => $itemId],
                        [
                            'qty' => $itemData['qty'],
                            'price' => $itemData['price'],
                            'total' => $itemData['total']
                        ]
                    );
                }

                // Update total_amount
                $totalAmount = $floorOrder->items()->sum('total');
                $floorOrder->update(['total_amount' => $totalAmount]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Draft berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating draft: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui draft'
            ], 500);
        }
    }

    public function finalize(FloorOrder $floorOrder)
    {
        try {
            DB::beginTransaction();

            // Validasi status saat ini
            if ($floorOrder->status !== 'draft') {
            return response()->json([
                'success' => false,
                    'message' => 'Hanya floor order dengan status draft yang dapat difinalisasi'
            ], 400);
        }

            // Update status
            $floorOrder->status = 'saved';
            $floorOrder->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Floor Order berhasil disimpan',
                'redirect' => route('floor-orders.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error finalizing floor order: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan Floor Order'
            ], 500);
        }
    }

    public function finalizeDraft($id)
    {
        try {
            DB::beginTransaction();
            
            $floorOrder = FloorOrder::findOrFail($id);
            
            // Update status dari draft ke saved
            $floorOrder->update([
                'status' => 'saved'
            ]);

            DB::commit();

        return response()->json([
            'success' => true,
                'message' => 'Floor Order berhasil disimpan',
                'redirect' => route('floor-orders.index')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error finalizing floor order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan Floor Order'
            ], 500);
        }
    }

    public function data()
    {
        $query = FloorOrder::query()
            ->select([
                'id',
                'fo_number',
                'order_date',
                'arrival_date',
                'notes',
                'status'
            ]);

        return DataTables::of($query)
            ->addColumn('action', function ($floorOrder) {
                $actions = '';
                
                // View button
                $actions .= '<a href="' . route('floor-orders.show', $floorOrder->id) . '" 
                               class="btn btn-sm btn-info me-1">
                               <i class="bi bi-eye"></i>
                            </a>';

                // Edit button (only for draft status)
                if ($floorOrder->status === 'draft') {
                    $actions .= '<a href="' . route('floor-orders.edit', $floorOrder->id) . '" 
                                   class="btn btn-sm btn-primary me-1">
                                   <i class="bi bi-pencil"></i>
                                </a>';
                }

                // Delete button (only for draft status)
                if ($floorOrder->status === 'draft') {
                    $actions .= '<button type="button" 
                                   class="btn btn-sm btn-danger delete-btn"
                                   data-id="' . $floorOrder->id . '"
                                   data-number="' . $floorOrder->fo_number . '">
                                   <i class="bi bi-trash"></i>
                                </button>';
                }

                return $actions;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function show($id)
    {
        $floorOrder = FloorOrder::with([
            'items.item.category', 
            'items.item.subcategory',
            'items.item.medium_unit'
        ])->findOrFail($id);

        $groupedItems = [];
        foreach ($floorOrder->items as $orderItem) {
            $item = $orderItem->item;
            $categoryName = $item->category->name;
            $subcategoryName = $item->subcategory->name;
            
            $groupedItems[$categoryName][$subcategoryName][] = [
                'name' => $item->name,
                'sku' => $item->sku,
                'qty' => $orderItem->qty,
                'medium_unit' => $item->medium_unit->name,
                'price' => $orderItem->price,
                'total' => $orderItem->total
            ];
        }

        return view('floor-orders.show', compact('floorOrder', 'groupedItems'));
    }

    public function edit($id)
    {
        try {
            // 1. Ambil floor order dengan warehouse
            $floorOrder = FloorOrder::with('warehouse')->findOrFail($id);
            
            // 2. Ambil semua items dari warehouse yang dipilih
            $mainKitchenCategoryId = 14; // sesuai dengan yang ada di create
            $user = auth()->user();
            $userOutletId = $user->id_outlet;
            $userRegionId = DB::table('tbl_data_outlet')
                ->where('id_outlet', $userOutletId)
                ->value('region_id');

            $query = Item::with([
                'category', 
                'subcategory', 
                'mediumUnit',
                'prices' => function($query) use ($userRegionId) {
                    $query->where('region_id', $userRegionId);
                }
            ])
            ->where('status', 'active');

            // Filter berdasarkan warehouse (MK atau non-MK)
            if ($floorOrder->warehouse->code === 'MK') {
                $query->where('category_id', $mainKitchenCategoryId);
            } else {
                $query->where('category_id', '!=', $mainKitchenCategoryId);
            }

            $items = $query->get();

            // 3. Ambil existing items dari floor order ini
            $existingItems = $floorOrder->items->keyBy('item_id');

            // 4. Kelompokkan items
            $groupedItems = [];
            foreach ($items as $item) {
                $categoryName = $item->category->name;
                $subcategoryName = $item->subcategory->name;
                
                if (!isset($groupedItems[$categoryName])) {
                    $groupedItems[$categoryName] = [];
                }
                if (!isset($groupedItems[$categoryName][$subcategoryName])) {
                    $groupedItems[$categoryName][$subcategoryName] = [];
                }

                // Cek apakah item ini sudah ada di floor order
                $existingItem = $existingItems->get($item->id);
                
                // Ambil harga dari prices
                $price = $item->prices->first() ? 
                         $this->roundUpToHundred($item->prices->first()->price / $item->medium_conversion_qty) : 
                         0;

                // Jika ada existing item, gunakan harga dari existing item
                if ($existingItem) {
                    $price = $existingItem->price;
                }

                $groupedItems[$categoryName][$subcategoryName][] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'qty' => $existingItem ? $existingItem->qty : 0,
                    'medium_unit' => $item->mediumUnit->name,
                    'price' => $price,
                    'total' => $existingItem ? $existingItem->total : 0,
                    'medium_conversion_qty' => $item->medium_conversion_qty
                ];
            }

            return view('floor-orders.edit', compact('floorOrder', 'groupedItems'));

        } catch (\Exception $e) {
            \Log::error("Error in edit floor order: " . $e->getMessage());
            return redirect()->route('floor-orders.index')
                ->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }

    // Helper function untuk mendapatkan harga item berdasarkan region
    private function getItemPrice($itemId)
    {
        $user = auth()->user();
        $userRegionId = DB::table('tbl_data_outlet')
            ->where('id_outlet', $user->id_outlet)
            ->value('region_id');

        $price = DB::table('item_prices')
            ->where('item_id', $itemId)
            ->where('region_id', $userRegionId)
            ->value('price');

        return $price ? $this->roundUpToHundred($price) : 0;
    }

    public function update(Request $request, FloorOrder $floorOrder)
    {
        try {
            DB::beginTransaction();
            
            // Simpan data lama untuk log
            $oldData = $floorOrder->load(['floorOrderItems'])->toArray();

            // Update floor order
            $floorOrder->arrival_date = $request->arrival_date;
            $floorOrder->notes = $request->notes;
            
            // Update items
            $totalAmount = 0;
            
            // Hapus semua item yang ada terlebih dahulu
            DB::table('floor_order_items')
                ->where('floor_order_id', $floorOrder->id)
                ->delete();
            
            // Insert items baru
            foreach ($request->items as $itemId => $itemData) {
                $qty = floatval($itemData['qty'] ?? 0);
                if ($qty > 0) {
                    // Ambil harga item
                    $item = Item::find($itemId);
                    if ($item) {
                        $price = $item->prices()
                            ->where('region_id', auth()->user()->outlet->region_id)
            ->first();

                        if ($price) {
                            $itemPrice = $price->price / $item->medium_conversion_qty;
                            $lineTotal = $qty * $itemPrice;
                            $totalAmount += $lineTotal;

                            // Insert new floor order item
                            DB::table('floor_order_items')->insert([
                                'floor_order_id' => $floorOrder->id,
                                'item_id' => $itemId,
                                'qty' => $qty,
                                'price' => $itemPrice,
                                'total' => $lineTotal,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
            }

            // Update total amount
            $floorOrder->total_amount = $this->roundUpToHundred($totalAmount);
            $floorOrder->save();

            // Log activity untuk update floor order
            $newData = $floorOrder->fresh()->load(['floorOrderItems'])->toArray();
            $this->logActivity(
                'UPDATE',
                'floor-orders',
                'Mengupdate floor order: ' . $floorOrder->fo_number,
                $oldData,
                $newData
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Floor Order berhasil diperbarui',
                'redirect' => route('floor-orders.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error updating floor order: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate Floor Order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:completed,cancelled'
            ]);

            DB::beginTransaction();

            $floorOrder = FloorOrder::findOrFail($id);
            
            // Hanya bisa update status jika status saat ini adalah 'saved'
            if ($floorOrder->status !== 'saved') {
            return response()->json([
                'success' => false,
                    'message' => 'Hanya Floor Order dengan status saved yang dapat diubah statusnya'
            ], 400);
        }

            $oldData = $floorOrder->toArray();
            
            $floorOrder->update([
                'status' => $request->status
            ]);

            // Log activity
            $this->logActivity(
                'UPDATE',
                'floor-orders',
                'Mengubah status Floor Order: ' . $floorOrder->fo_number . ' menjadi ' . $request->status,
                $oldData,
                $floorOrder->toArray()
            );

            DB::commit();

        return response()->json([
            'success' => true,
                'message' => 'Status Floor Order berhasil diubah menjadi ' . $request->status
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateFoNumber()
    {
        $date = now();
        $prefix = 'FO';
        $yearMonth = $date->format('Ymd');
        
        // Ambil nomor urut terakhir untuk hari ini
        $lastOrder = FloorOrder::where('fo_number', 'like', "{$prefix}/{$yearMonth}/%")
            ->orderBy('fo_number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->fo_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}/{$yearMonth}/{$newNumber}";
    }

    // Fungsi baru khusus untuk get items di edit mode
    public function getItemsByWarehouseEdit($warehouseCode)
    {
        try {
            \Log::info("Getting items for warehouse (Edit Mode): " . $warehouseCode);
            $mainKitchenCategoryId = 14;
            $user = auth()->user();
            
            $userOutletId = $user->id_outlet;
            $userRegionId = DB::table('tbl_data_outlet')
                ->where('id_outlet', $userOutletId)
                ->value('region_id');

            $query = Item::with([
                'category', 
                'subCategory', 
                'mediumUnit',
                'prices' => function($query) use ($userRegionId) {
                    $query->where('region_id', $userRegionId);
                },
                'availabilities'
            ])
            ->where('status', 'active')
            ->where(function($query) use ($userRegionId, $userOutletId) {
                $query->whereHas('availabilities', function($q) {
                    $q->where('availability_type', 'all');
                })
                ->orWhereHas('availabilities', function($q) use ($userRegionId) {
                    $q->where('availability_type', 'region')
                      ->where('region_id', $userRegionId);
                })
                ->orWhereHas('availabilities', function($q) use ($userOutletId) {
                    $q->where('availability_type', 'outlet')
                      ->where('outlet_id', $userOutletId);
                });
            });

            if ($warehouseCode === 'MK') {
                $query->where('category_id', $mainKitchenCategoryId);
            } else {
                $query->where('category_id', '!=', $mainKitchenCategoryId);
            }

            $items = $query->get();

            // Group items
            $groupedItems = [];
            foreach ($items as $item) {
                $categoryName = $item->category->name ?? 'Uncategorized';
                $subCategoryName = $item->subCategory->name ?? 'Uncategorized';
                
                if (!isset($groupedItems[$categoryName])) {
                    $groupedItems[$categoryName] = [];
                }
                if (!isset($groupedItems[$categoryName][$subCategoryName])) {
                    $groupedItems[$categoryName][$subCategoryName] = [];
                }
                
                $price = $item->prices->first();
                $mediumPrice = $price ? $this->roundUpToHundred($price->price / $item->medium_conversion_qty) : 0;
                
                $groupedItems[$categoryName][$subCategoryName][] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'medium_unit' => $item->mediumUnit->name ?? '-',
                    'price' => $mediumPrice,
                    'original_price' => $price ? $this->roundUpToHundred($price->price) : 0,
                    'medium_conversion_qty' => $item->medium_conversion_qty
                ];
            }

        return response()->json([
            'success' => true,
                'data' => $groupedItems
            ]);

        } catch (\Exception $e) {
            \Log::error("Error getting items (Edit Mode): " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data'
            ], 500);
        }
    }

    public function saveDraft(FloorOrder $floorOrder)
    {
        try {
            DB::beginTransaction();
            
            $oldData = $floorOrder->toArray();
            $oldStatus = $floorOrder->status;

            $floorOrder->status = 'saved';
            $floorOrder->save();

            // Log activity untuk perubahan status
            $this->logActivity(
                'UPDATE',
                'floor-orders',
                'Mengubah status floor order: ' . $floorOrder->fo_number . ' dari ' . $oldStatus . ' menjadi saved',
                $oldData,
                $floorOrder->toArray()
            );

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Status berhasil diubah']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error saving draft: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status'
            ], 500);
        }
    }

    public function destroy(FloorOrder $floorOrder)
    {
        try {
            DB::beginTransaction();
            
            $oldData = $floorOrder->load(['floorOrderItems'])->toArray();

            // Hapus items terlebih dahulu
            $floorOrder->floorOrderItems()->delete();
            
            // Hapus floor order
            $floorOrder->delete();

            // Log activity untuk penghapusan floor order
            $this->logActivity(
                'DELETE',
                'floor-orders',
                'Menghapus floor order: ' . $floorOrder->fo_number,
                $oldData,
                null
            );

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Floor Order berhasil dihapus']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error deleting floor order: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Floor Order'
            ], 500);
        }
    }
}