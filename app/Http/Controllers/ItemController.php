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

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'sub_category_id' => 'required|exists:sub_categories,id',
                'sku' => 'required|unique:items,sku|max:50',
                'name' => 'required|max:100',
                'description' => 'nullable',
                'small_unit_id' => 'required|exists:units,id',
                'medium_unit_id' => 'required|exists:units,id',
                'large_unit_id' => 'required|exists:units,id',
                'medium_conversion_qty' => 'required|numeric|min:0',
                'small_conversion_qty' => 'required|numeric|min:0',
                'prices' => 'required|array',
                'prices.*.region_id' => 'required|exists:regions,id',
                'prices.*.price' => 'required|numeric|min:0',
                'availability_type' => 'required|in:all,region,outlet',
                'region_ids' => 'required_if:availability_type,region|array',
                'region_ids.*' => 'exists:regions,id',
                'outlet_ids' => 'required_if:availability_type,outlet|array',
                'outlet_ids.*' => 'exists:tbl_data_outlet,id_outlet',
                'specification' => 'nullable|string',
                'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $item = Item::create([
                'category_id' => $validatedData['category_id'],
                'sub_category_id' => $validatedData['sub_category_id'],
                'sku' => $validatedData['sku'],
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'small_unit_id' => $validatedData['small_unit_id'],
                'medium_unit_id' => $validatedData['medium_unit_id'],
                'large_unit_id' => $validatedData['large_unit_id'],
                'medium_conversion_qty' => $validatedData['medium_conversion_qty'],
                'small_conversion_qty' => $validatedData['small_conversion_qty'],
                'status' => 'active',
                'specification' => $validatedData['specification']
            ]);

            // Simpan harga
            foreach ($validatedData['prices'] as $price) {
                $item->prices()->create([
                    'region_id' => $price['region_id'],
                    'price' => $price['price']
                ]);
            }

            // Debug log
            \Log::info('Processing availability with type: ' . $validatedData['availability_type']);

            // Simpan availability
            if ($validatedData['availability_type'] === 'outlet' && !empty($validatedData['outlet_ids'])) {
                \Log::info('Creating outlet availabilities for outlets:', $validatedData['outlet_ids']);
                
                foreach ($validatedData['outlet_ids'] as $outletId) {
                    $availability = $item->availabilities()->create([
                        'availability_type' => 'outlet',
                        'outlet_id' => $outletId
                    ]);
                    \Log::info('Created availability:', $availability->toArray());
                }
            } elseif ($validatedData['availability_type'] === 'region' && !empty($validatedData['region_ids'])) {
                foreach ($validatedData['region_ids'] as $regionId) {
                    $item->availabilities()->create([
                        'availability_type' => 'region',
                        'region_id' => $regionId
                    ]);
                }
            } else {
                // Hanya buat 'all' jika memang tipe nya 'all'
                if ($validatedData['availability_type'] === 'all') {
                    $item->availabilities()->create([
                        'availability_type' => 'all'
                    ]);
                } else {
                    throw new \Exception('Invalid availability configuration');
                }
            }

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('items', 'public');
                    $item->images()->create([
                        'path' => $path
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Item $item)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'sub_category_id' => 'required|exists:sub_categories,id',
                'sku' => 'required|max:50|unique:items,sku,' . $item->id,
                'name' => 'required|max:100',
                'description' => 'nullable',
                'specification' => 'nullable|string',
                'small_unit_id' => 'required|exists:units,id',
                'medium_unit_id' => 'required|exists:units,id',
                'large_unit_id' => 'required|exists:units,id',
                'medium_conversion_qty' => 'required|numeric|min:0',
                'small_conversion_qty' => 'required|numeric|min:0',
                'prices' => 'required|json',
                'availability_type' => 'required|in:all,region,outlet',
                'region_ids' => 'required_if:availability_type,region|array',
                'region_ids.*' => 'exists:regions,id',
                'outlet_ids' => 'required_if:availability_type,outlet|array',
                'outlet_ids.*' => 'exists:tbl_data_outlet,id_outlet',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            // Update item basic info
            $item->update([
                'category_id' => $validatedData['category_id'],
                'sub_category_id' => $validatedData['sub_category_id'],
                'sku' => $validatedData['sku'],
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'specification' => $validatedData['specification'],
                'small_unit_id' => $validatedData['small_unit_id'],
                'medium_unit_id' => $validatedData['medium_unit_id'],
                'large_unit_id' => $validatedData['large_unit_id'],
                'medium_conversion_qty' => $validatedData['medium_conversion_qty'],
                'small_conversion_qty' => $validatedData['small_conversion_qty']
            ]);

            // Update prices
            $prices = json_decode($validatedData['prices'], true);
            $item->prices()->delete();
            foreach ($prices as $price) {
                $item->prices()->create([
                    'region_id' => $price['region_id'],
                    'price' => $price['price']
                ]);
            }

            // Update availabilities
            $item->availabilities()->delete();
            if ($validatedData['availability_type'] === 'region' && !empty($validatedData['region_ids'])) {
                foreach ($validatedData['region_ids'] as $regionId) {
                    $item->availabilities()->create([
                        'availability_type' => 'region',
                        'region_id' => $regionId
                    ]);
                }
            } elseif ($validatedData['availability_type'] === 'outlet' && !empty($validatedData['outlet_ids'])) {
                foreach ($validatedData['outlet_ids'] as $outletId) {
                    $item->availabilities()->create([
                        'availability_type' => 'outlet',
                        'outlet_id' => $outletId
                    ]);
                }
            } else {
                $item->availabilities()->create([
                    'availability_type' => 'all'
                ]);
            }

            // Di dalam method update, sebelum handle image uploads
            \Log::info('Files in request:', [
                'hasFile' => $request->hasFile('images'),
                'allFiles' => $request->allFiles(),
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    try {
                        // Generate nama file yang unik
                        $fileName = uniqid('item_') . '.' . $image->getClientOriginalExtension();
                        
                        // Simpan file
                        $path = $image->storeAs('items', $fileName, 'public');
                        
                        // Debug log
                        \Log::info('Image stored at:', ['path' => $path]);
                        
                        // Simpan path ke database dengan forward slash
                        $path = str_replace('\\', '/', $path);
                        $item->images()->create([
                            'path' => $path
                        ]);
                        
                    } catch (\Exception $e) {
                        \Log::error('Error storing image: ' . $e->getMessage());
                        throw $e;
                    }
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating item: ' . $e->getMessage());
            \Log::error('Request data: ' . json_encode($request->all()));
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui item: ' . $e->getMessage()
            ], 500);
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
} 