<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Unit;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Traits\LogActivity;
use App\Exports\ItemsExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Exports\ItemTemplateExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    use LogActivity;

    public function index()
    {
        $items = Item::with([
            'category',
            'subCategory',
            'smallUnit',
            'mediumUnit',
            'largeUnit',
            'prices.region',
            'availabilities',
            'images' => function($query) {
                $query->select('id', 'item_id', 'path');
            }
        ])->get();
        
        $categories = Category::where('status', 'active')
            ->select('id', 'name', 'status')
            ->get();
        
        $regions = Region::where('status', 'active')
            ->select('id', 'name', 'status')
            ->get();
        
        $outlets = DB::table('tbl_data_outlet')
            ->select('id_outlet', 'nama_outlet', 'lokasi', 'region_id')
            ->where('status', 'A')
            ->orderBy('nama_outlet')
            ->get();
        
        $units = Unit::where('status', 'active')
            ->select('id', 'name', 'status')
            ->get();
        
        return view('items.index', compact('items', 'categories', 'regions', 'outlets', 'units'));
    }

    public function getSubCategories(Request $request)
    {
        try {
            if (!$request->category_id) {
                return response()->json([
                    'message' => 'Category ID is required'
                ], 400);
            }

            $subCategories = SubCategory::where('category_id', $request->category_id)
                ->where('status', 'active')
                ->get();
            
            return response()->json($subCategories);
            
        } catch (\Exception $e) {
            \Log::error('Error in getSubCategories: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to get sub categories'
            ], 500);
        }
    }

    protected function handleImageUpload($request, $item)
    {
        try {
            if ($request->hasFile('images')) {
                \Log::info('Processing images:', ['count' => count($request->file('images'))]);
                
                foreach ($request->file('images') as $image) {
                    try {
                        // Validasi file
                        $validator = Validator::make(['image' => $image], [
                            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
                        ]);

                        if ($validator->fails()) {
                            \Log::error('Image validation failed:', $validator->errors()->all());
                            continue;
                        }

                        if (!$image->isValid()) {
                            \Log::error('Invalid image file');
                            continue;
                        }

                        // Generate unique filename
                        $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                        
                        // Store file
                        $path = $image->storeAs('items', $fileName, 'public');
                        
                        // Save to database
                        $item->images()->create([
                            'path' => $path
                        ]);

                        \Log::info('Image saved successfully:', ['path' => $path]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to save image:', ['error' => $e->getMessage()]);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error in handleImageUpload:', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            
            \Log::info('Creating item with data:', $request->except('images'));
            \Log::info('Number of images:', ['count' => count($request->file('images') ?? [])]);

            // Validasi data
            $validatedData = $request->validate([
                'category_id' => 'required',
                'sub_category_id' => 'required',
                'sku' => 'required|unique:items,sku',
                'name' => 'required',
                'description' => 'nullable',
                'small_unit_id' => 'required',
                'medium_unit_id' => 'required',
                'large_unit_id' => 'required',
                'medium_conversion_qty' => 'required|numeric',
                'small_conversion_qty' => 'required|numeric',
                'prices' => 'required|array',
                'prices.*.region_id' => 'required',
                'prices.*.price' => 'required|numeric',
                'availability_type' => 'required|in:all,region,outlet',
                'specification' => 'nullable'
            ]);

            // Buat item baru
            $item = Item::create([
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'sku' => $request->sku,
                'name' => $request->name,
                'description' => $request->description,
                'small_unit_id' => $request->small_unit_id,
                'medium_unit_id' => $request->medium_unit_id,
                'large_unit_id' => $request->large_unit_id,
                'medium_conversion_qty' => $request->medium_conversion_qty,
                'small_conversion_qty' => $request->small_conversion_qty,
                'specification' => $request->specification,
                'status' => 'active'
            ]);

            // Simpan harga
            foreach ($request->prices as $priceData) {
                $item->prices()->create([
                    'region_id' => $priceData['region_id'],
                    'price' => $priceData['price']
                ]);
            }

            // Simpan availability
            if ($request->availability_type === 'all') {
                $item->availabilities()->create([
                    'availability_type' => 'all'
                ]);
            } 
            else if ($request->availability_type === 'region' && !empty($request->region_ids)) {
                $regionIds = is_array($request->region_ids) ? $request->region_ids : json_decode($request->region_ids);
                foreach ($regionIds as $regionId) {
                    $item->availabilities()->create([
                        'availability_type' => 'region',
                        'region_id' => $regionId
                    ]);
                }
            } 
            else if ($request->availability_type === 'outlet' && !empty($request->outlet_ids)) {
                $outletIds = is_array($request->outlet_ids) ? $request->outlet_ids : json_decode($request->outlet_ids);
                foreach ($outletIds as $outletId) {
                    $item->availabilities()->create([
                        'availability_type' => 'outlet',
                        'outlet_id' => $outletId
                    ]);
                }
            }

            // Handle image upload
            $this->handleImageUpload($request, $item);

            // Log activity untuk pembuatan item baru
            $this->logActivity(
                'CREATE',
                'items',
                'Membuat item baru: ' . $item->name,
                null,
                $item->load(['prices', 'availabilities', 'images'])->toArray()
            );

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Item berhasil ditambahkan']);

        } catch (\Exception $e) {
            DB::rollBack();
            // Hapus file yang mungkin sudah terupload jika terjadi error
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            \Log::error('Error creating item:', ['message' => $e->getMessage(), 'data' => $request->all()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $item = Item::findOrFail($id);
            $oldData = $item->load(['prices', 'availabilities', 'images'])->toArray();

            // Validasi data
            $validatedData = $request->validate([
                'category_id' => 'required',
                'sub_category_id' => 'required',
                'sku' => 'required|unique:items,sku,' . $id,
                'name' => 'required',
                'description' => 'nullable',
                'small_unit_id' => 'required',
                'medium_unit_id' => 'required',
                'large_unit_id' => 'required',
                'medium_conversion_qty' => 'required|numeric',
                'small_conversion_qty' => 'required|numeric',
                'prices' => 'required|array',
                'prices.*.region_id' => 'required',
                'prices.*.price' => 'required|numeric',
                'availability_type' => 'required|in:all,region,outlet',
                'specification' => 'nullable'
            ]);

            // Update item
            $item->update([
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'sku' => $request->sku,
                'name' => $request->name,
                'description' => $request->description,
                'small_unit_id' => $request->small_unit_id,
                'medium_unit_id' => $request->medium_unit_id,
                'large_unit_id' => $request->large_unit_id,
                'medium_conversion_qty' => $request->medium_conversion_qty,
                'small_conversion_qty' => $request->small_conversion_qty,
                'specification' => $request->specification
            ]);

            // Update harga
            $item->prices()->delete();
            foreach ($request->prices as $priceData) {
                $item->prices()->create([
                    'region_id' => $priceData['region_id'],
                    'price' => $priceData['price']
                ]);
            }

            // Update availability
            $item->availabilities()->delete();
            
            if ($request->availability_type === 'all') {
                $item->availabilities()->create([
                    'availability_type' => 'all'
                ]);
            } 
            else if ($request->availability_type === 'region' && !empty($request->region_ids)) {
                $regionIds = is_array($request->region_ids) ? $request->region_ids : json_decode($request->region_ids);
                foreach ($regionIds as $regionId) {
                    $item->availabilities()->create([
                        'availability_type' => 'region',
                        'region_id' => $regionId
                    ]);
                }
            } 
            else if ($request->availability_type === 'outlet' && !empty($request->outlet_ids)) {
                $outletIds = is_array($request->outlet_ids) ? $request->outlet_ids : json_decode($request->outlet_ids);
                foreach ($outletIds as $outletId) {
                    $item->availabilities()->create([
                        'availability_type' => 'outlet',
                        'outlet_id' => $outletId
                    ]);
                }
            }

            // Handle image upload
            $this->handleImageUpload($request, $item);

            // Log activity untuk update item
            $newData = $item->fresh()->load(['prices', 'availabilities', 'images'])->toArray();
            $this->logActivity(
                'UPDATE',
                'items',
                'Mengupdate item: ' . $item->name,
                $oldData,
                $newData
            );

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Item berhasil diperbarui']);

        } catch (\Exception $e) {
            DB::rollBack();
            // Hapus file yang mungkin sudah terupload jika terjadi error
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            \Log::error('Error updating item:', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleStatus(Request $request, Item $item)
    {
        try {
            $newStatus = $request->status;
            $oldStatus = $item->status;
            $oldData = $item->toArray();
            
            $item->status = $newStatus;
            $item->save();

            // Log activity
            $this->logActivity(
                'UPDATE',     // module
                'items',      // activity_type
                'Mengubah status item: ' . $item->name . ' menjadi ' . $newStatus,
                $oldData,
                $item->toArray()
            );

            return response()->json([
                'success' => true,
                'message' => trans('translation.item.success_status_change')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Item $item)
    {
        $oldData = $item->toArray();
        $item->prices()->delete();
        $item->delete();
        
        // Log activity
        $this->logActivity(
            'DELETE',     // module
            'items',      // activity_type
            'Menghapus item: ' . $item->name,
            $oldData,
            null
        );

        return response()->json(['success' => true, 'message' => trans('translation.item.success_delete')]);
    }

    public function exportExcel(Request $request)
    {
        $query = Item::with(['category', 'subCategory', 'images', 'availabilities.region', 'availabilities.outlet', 'prices.region', 'smallUnit', 'mediumUnit', 'largeUnit']);
        
        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $items = $query->get();

        // Log activity untuk export Excel
        $this->logActivity(
            'EXPORT',
            'items',
            'Men-download daftar item dalam format Excel',
            null,
            ['total_items' => $items->count(), 'filters' => $request->all()]
        );

        // Pass items collection ke ItemsExport
        return Excel::download(new ItemsExport($items), 'items-' . date('Y-m-d') . '.xlsx');
    }

    public function exportPdf()
    {
        try {
            // Load data dengan chunk untuk menghemat memory
            $items = collect();
            Item::with([
                'category', 
                'subCategory', 
                'smallUnit', 
                'mediumUnit', 
                'largeUnit', 
                'prices.region'
            ])
            ->chunk(100, function($chunk) use (&$items) {
                $items = $items->concat($chunk);
            });
            
            // Get user data
            $user = auth()->user();
            
            // Set custom paper size dan orientation
            $pdf = PDF::loadView('items.pdf', compact('items', 'user'))
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'dpi' => 100,
                    'defaultFont' => 'sans-serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isPhpEnabled' => true,
                    'isJavascriptEnabled' => false,
                    'isFontSubsettingEnabled' => true,
                    'defaultMediaType' => 'screen',
                    'defaultPaperSize' => 'a4',
                    'defaultPaperOrientation' => 'landscape',
                    'fontHeightRatio' => 0.9,
                    'tempDir' => storage_path('app/pdf'),
                ]);
            
            // Log aktivitas
            $this->logActivity(
                'EXPORT',     // module
                'items',      // activity_type
                'Men-download daftar item dalam format PDF',
                null,
                null
            );

            // Bersihkan memory
            gc_collect_cycles();
            
            return $pdf->download('items_YMSoft_' . date('Y-m-d_H-i-s') . '.pdf');
            
        } catch (\Exception $e) {
            \Log::error('Error exporting PDF: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengunduh PDF: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        // Log activity untuk download template
        $this->logActivity(
            'DOWNLOAD',
            'items',
            'Men-download template import barang',
            null,
            null
        );

        return Excel::download(new ItemTemplateExport, 'template_import_barang.xlsx');
    }

    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $path = $request->file('file')->store('temp');
        $data = Excel::toArray([], storage_path('app/' . $path))[0];
        
        // Remove header rows
        $data = array_slice($data, 8);
        
        $preview = [];
        foreach($data as $row) {
            if (!empty($row[0])) { // Skip empty rows
                $prices = [];
                $priceData = explode(',', $row[9]);
                foreach($priceData as $price) {
                    list($region_id, $price_amount) = explode('|', $price);
                    $prices[] = [
                        'region_id' => $region_id,
                        'price' => $price_amount
                    ];
                }

                $preview[] = [
                    'sku' => $row[0],
                    'name' => $row[1],
                    'category_id' => $row[2],
                    'sub_category_id' => $row[3],
                    'small_unit_id' => $row[4],
                    'medium_unit_id' => $row[5],
                    'large_unit_id' => $row[6],
                    'medium_conversion_qty' => $row[7],
                    'small_conversion_qty' => $row[8],
                    'prices' => $prices
                ];
            }
        }

        // Log activity untuk preview import
        $this->logActivity(
            'PREVIEW',
            'items',
            'Melakukan preview import data barang',
            null,
            ['total_items' => count($preview)]
        );

        // Store preview data in session
        session(['import_data' => $preview]);
        
        return response()->json([
            'success' => true,
            'data' => $preview,
            'total' => count($preview)
        ]);
    }

    public function import()
    {
        try {
            $preview = session('import_data');
            
            if (empty($preview)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data untuk diimport'
                ], 400);
            }

            \DB::beginTransaction();
            
            // Proses data dalam batch
            $batchSize = 100; // Jumlah data per batch
            $batches = array_chunk($preview, $batchSize);
            
            foreach($batches as $batch) {
                foreach($batch as $row) {
                    $prices = $row['prices'];
                    unset($row['prices']);
                    
                    // Set default status
                    $row['status'] = 'active';
                    
                    $item = Item::create($row);
                    
                    // Insert prices dalam satu query
                    $priceData = array_map(function($price) use ($item) {
                        return [
                            'item_id' => $item->id,
                            'region_id' => $price['region_id'],
                            'price' => $price['price'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }, $prices);
                    
                    \DB::table('item_prices')->insert($priceData);
                }
                
                // Clear memory after each batch
                \DB::commit();
                \DB::beginTransaction();
            }
            
            \DB::commit();
            
            // Log activity
            $this->logActivity(
                'CREATE',
                'items',
                'Import data items',
                null,
                ['total_items' => count($preview)]
            );
            
            // Clear session
            session()->forget('import_data');
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diimport'
            ]);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Error importing items: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal import data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAvailability(Item $item)
    {
        try {
            $availabilities = $item->availabilities()
                ->with(['region', 'outlet'])
                ->get()
                ->map(function($availability) {
                    return [
                        'availability_type' => $availability->availability_type,
                        'region_name' => $availability->region ? $availability->region->name : null,
                        'outlet_name' => $availability->outlet ? $availability->outlet->nama_outlet : null
                    ];
                });

            return response()->json([
                'success' => true,
                'item_name' => $item->name,
                'availabilities' => $availabilities
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data availability'
            ], 500);
        }
    }

    public function getImages(Item $item)
    {
        $images = $item->images->map(function($image) {
            return [
                'url' => url('storage/' . $image->path),
                'path' => $image->path
            ];
        });
        
        return response()->json($images);
    }

    public function getPrices(Item $item)
    {
        dd('Reached getPrices method!', $item); // Basic check if method is called

        try {
            // Debug item yang diterima
            \Log::info('Received item ID:', ['id' => $item->id]);

            // Query langsung ke database
            $rawData = DB::select("
                SELECT id, name, description, specification 
                FROM items 
                WHERE id = ?
            ", [$item->id]);
            
            \Log::info('Raw database query result:', ['data' => $rawData]);

            $prices = $item->prices()
                ->with('region')
                ->get()
                ->map(function($price) {
                    return [
                        'region_name' => $price->region->name,
                        'price' => number_format($price->price, 2)
                    ];
                });

            $availabilities = $item->availabilities()
                ->with(['region', 'outlet'])
                ->get()
                ->map(function($availability) {
                    return [
                        'availability_type' => $availability->availability_type,
                        'region_name' => $availability->region ? $availability->region->name : null,
                        'outlet_name' => $availability->outlet ? $availability->outlet->nama_outlet : null
                    ];
                });

            // Siapkan response dengan debug info
            $response = [
                'success' => true,
                'item_name' => $item->name,
                'debug_item' => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'has_description' => !empty($item->description),
                    'has_specification' => !empty($item->specification),
                    'raw_data' => $rawData
                ],
                'prices' => $prices,
                'availabilities' => $availabilities,
                'raw_description' => $item->description,
                'raw_specification' => $item->specification,
                'description' => $item->description ? nl2br(e($item->description)) : null,
                'specification' => $item->specification ? nl2br(e($item->specification)) : null
            ];

            \Log::info('Sending response:', $response);

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Error in getPrices:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage(),
                'debug_info' => [
                    'error_class' => get_class($e),
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function getDetail($id)
    {
        try {
            $item = Item::with(['category', 'subcategory'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $item->id,
                    'sku' => $item->sku,
                    'name' => $item->name,
                    'category_name' => $item->category->name ?? '-',
                    'subcategory_name' => $item->subcategory->name ?? '-',
                    // tambahkan data lain yang diperlukan
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function show($id)
    {
        $item = Item::with(['category', 'subcategory'])->findOrFail($id);
        return view('items.show', compact('item'));
    }

    public function search(Request $request)
    {
        try {
            $term = $request->get('term');
            $warehouseId = $request->get('warehouse_id');

            $items = Item::select('items.*')
                ->with(['mediumUnit'])
                ->leftJoin('inventories', function($join) use ($warehouseId) {
                    $join->on('items.id', '=', 'inventories.item_id')
                         ->where('inventories.warehouse_id', '=', $warehouseId);
                })
                ->where(function($query) use ($term) {
                    $query->where('items.name', 'LIKE', "%{$term}%")
                          ->orWhere('items.sku', 'LIKE', "%{$term}%");
                })
                ->addSelect([
                    'inventories.stock_on_hand',
                    'inventories.stock_available'
                ])
                ->get();

            $result = [];
            foreach($items as $item) {
                $result[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'stock_on_hand' => $item->stock_on_hand ?? 0,
                    'stock_available' => $item->stock_available ?? 0,
                    'unit' => [
                        'name' => $item->mediumUnit->name ?? '-'
                    ]
                ];
            }

            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Error in item search: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getUnits($itemId)
    {
        try {
            $item = Item::findOrFail($itemId);
            
            $units = [];
            
            // Tambahkan large unit jika ada
            if ($item->large_unit_id) {
                $largeUnit = Unit::find($item->large_unit_id);
                if ($largeUnit) {
                    $units[] = [
                        'id' => $largeUnit->id,
                        'name' => $largeUnit->name,
                        'is_largest' => true
                    ];
                }
            }
            
            // Tambahkan medium unit jika ada
            if ($item->medium_unit_id) {
                $mediumUnit = Unit::find($item->medium_unit_id);
                if ($mediumUnit) {
                    $units[] = [
                        'id' => $mediumUnit->id,
                        'name' => $mediumUnit->name,
                        'is_largest' => !$item->large_unit_id
                    ];
                }
            }
            
            // Tambahkan small unit jika ada
            if ($item->small_unit_id) {
                $smallUnit = Unit::find($item->small_unit_id);
                if ($smallUnit) {
                    $units[] = [
                        'id' => $smallUnit->id,
                        'name' => $smallUnit->name,
                        'is_largest' => !$item->large_unit_id && !$item->medium_unit_id
                    ];
                }
            }

            return response()->json($units);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getSpecsAndImages(Request $request)
    {
        try {
            $item = Item::with('images')->findOrFail($request->item_id);
            
            return response()->json([
                'success' => true,
                'specifications' => $item->specification,
                'images' => $item->images->map(function($image) {
                    return [
                        'id' => $image->id,
                        'path' => asset('storage/' . $image->path)
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data item'
            ], 500);
        }
    }

    public function getConversions($id)
    {
        try {
            $item = Item::findOrFail($id);
            
            // Debug log
            \Log::info('Item conversion data:', [
                'id' => $id,
                'medium_conversion_qty' => $item->medium_conversion_qty,
                'small_conversion_qty' => $item->small_conversion_qty
            ]);

            return response()->json([
                'medium' => floatval($item->medium_conversion_qty ?: 0),
                'large' => floatval($item->small_conversion_qty ?: 0)
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getConversions:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to get conversion data',
                'medium' => 0,
                'large' => 0
            ], 200); // Return 200 with default values instead of 500
        }
    }
} 