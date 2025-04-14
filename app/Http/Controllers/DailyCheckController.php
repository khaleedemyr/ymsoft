<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\DailyCheckHeader;
use App\Models\DailyCheckDetail;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\DailyCheckPhoto;
use App\Models\DailyCheck;
use App\Models\DailyCheckItem;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;

class DailyCheckController extends Controller
{
    public function index()
    {
        $dailyChecks = DailyCheckHeader::with(['outlet'])
            ->orderBy('date', 'desc')
            ->get();
            
        $outlets = Outlet::orderBy('nama_outlet')->get();
            
        return view('daily-check.list', compact('dailyChecks', 'outlets'));
    }

    public function create(Request $request)
    {
        if (!$request->outlet_id) {
            return redirect()->route('daily-check.list')->with('error', 'Silakan pilih outlet terlebih dahulu');
        }

        try {
            DB::beginTransaction();
            
            $outlet = Outlet::findOrFail($request->outlet_id);
            $areas = Area::all();
            $outlets = Outlet::orderBy('nama_outlet')->get();
            
            // Buat header daily check jika belum ada
            $dailyCheck = DailyCheckHeader::firstOrCreate(
                [
                    'id_outlet' => $request->outlet_id,
                    'date' => date('Y-m-d')
                ],
                [
                    'no_daily_check' => $this->generateDailyCheckNumber($request->outlet_id),
                    'user_id' => auth()->id(),
                    'status' => 'draft'
                ]
            );

            // Buat detail untuk semua item dengan nilai default
            foreach ($areas as $area) {
                foreach ($area->items as $item) {
                    DailyCheckDetail::firstOrCreate(
                        [
                            'daily_check_id' => $dailyCheck->id,
                            'item_id' => $item->id
                        ],
                        [
                            'condition' => 'NA',
                            'time' => '00:00:00',
                            'other_issue' => null,
                            'remark' => null
                        ]
                    );
                }
            }

            DB::commit();
            
            return view('daily-check.index', compact('areas', 'outlet', 'outlets', 'dailyCheck'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('daily-check.list')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function generateDailyCheckNumber($idOutlet)
    {
        $today = Carbon::now();
        $outlet = Outlet::findOrFail($idOutlet);
        $outletCode = substr(strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $outlet->nama_outlet)), 0, 3);
        
        // Format: DC-OUT-YYYYMMDD-XXXX
        $prefix = 'DC-' . $outletCode . '-' . $today->format('Ymd');
        
        $lastNumber = DailyCheckHeader::where('no_daily_check', 'like', $prefix . '%')
            ->orderBy('no_daily_check', 'desc')
            ->first();

        if ($lastNumber) {
            $sequence = intval(substr($lastNumber->no_daily_check, -4)) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function autosave(Request $request)
    {
        try {
            \Log::info('Autosave request received', [
                'request_data' => $request->all()
            ]);

            // Validasi input dasar
            $request->validate([
                'id_outlet' => 'required|exists:tbl_data_outlet,id_outlet',
                'date' => 'required|date',
                'status' => 'required|in:draft,saved',
            ]);

            $idOutlet = $request->input('id_outlet');
            $date = $request->input('date');
            $status = $request->input('status', 'draft');

            DB::beginTransaction();

            // Ambil atau buat daily check header
            $dailyCheck = DailyCheckHeader::firstOrCreate(
                [
                    'id_outlet' => $idOutlet,
                    'date' => $date
                ],
                [
                    'no_daily_check' => $this->generateDailyCheckNumber($idOutlet),
                    'user_id' => auth()->id(),
                    'status' => $status
                ]
            );

            // Update status jika perlu
            if ($dailyCheck->status !== $status) {
                $dailyCheck->status = $status;
                $dailyCheck->save();
            }

            \Log::info('Processing daily check', [
                'daily_check_id' => $dailyCheck->id,
                'id_outlet' => $idOutlet,
                'date' => $date,
                'status' => $status
            ]);

            // Proses setiap item yang dikirim
            if ($request->has('checks')) {
                foreach ($request->checks as $itemId => $data) {
                    \Log::info('Processing item', [
                        'item_id' => $itemId,
                        'data' => $data
                    ]);

                    // Skip jika tidak ada condition
                    if (isset($data['condition'])) {
                        // Logging data mentah yang diterima
                        \Log::info('Raw data received for item', [
                            'item_id' => $itemId,
                            'raw_data' => $data
                        ]);

                        // Validasi data item
                        $itemValidator = \Validator::make($data, [
                            'condition' => 'required|in:C,WM,D,NA',
                            'time' => 'nullable|date_format:H:i:s,H:i',
                            'other_issue' => 'nullable|string|max:255',
                            'remark' => 'nullable|string|max:1000',
                        ]);

                        if ($itemValidator->fails()) {
                            \Log::warning('Item validation failed', [
                                'item_id' => $itemId,
                                'errors' => $itemValidator->errors()
                            ]);
                            continue; // Skip item ini dan lanjut ke item berikutnya
                        }

                        // Siapkan data untuk update/create
                        $updateData = [
                            'condition' => $data['condition'],
                        ];
                        
                        // Format waktu dengan benar, menambahkan detik jika perlu
                        if (isset($data['time']) && $data['time']) {
                            // Periksa apakah format waktu sudah memiliki detik
                            if (substr_count($data['time'], ':') === 1) {
                                // Tambahkan detik jika belum ada
                                $updateData['time'] = $data['time'] . ':00';
                            } else {
                                $updateData['time'] = $data['time'];
                            }
                        } else {
                            $updateData['time'] = date('H:i:s');
                        }

                        // Tambahkan other_issue (bahkan jika empty string)
                        $updateData['other_issue'] = isset($data['other_issue']) ? $data['other_issue'] : '';

                        // Tambahkan remark (bahkan jika empty string)
                        $updateData['remark'] = isset($data['remark']) ? $data['remark'] : '';

                        // Log data mentah dan yang akan disimpan
                        \Log::info('Raw and prepared data for update', [
                            'item_id' => $itemId,
                            'raw_data' => $data,
                            'updateData' => $updateData,
                            'isset_other_issue' => isset($data['other_issue']),
                            'isset_remark' => isset($data['remark'])
                        ]);

                        // Update atau buat data detail
                        $detail = DailyCheckDetail::updateOrCreate(
                            [
                                'daily_check_id' => $dailyCheck->id,
                                'item_id' => $itemId
                            ],
                            $updateData
                        );

                        \Log::info('Detail updated/created', [
                            'item_id' => $itemId,
                            'detail_id' => $detail->id,
                            'data' => $data,
                            'saved_detail' => $detail->toArray(),
                            'updateData' => $updateData
                        ]);

                        // Handle photos
                        if (isset($data['photos']) && is_array($data['photos'])) {
                            // Kumpulkan semua foto yang akan diproses dalam satu batch
                            $processedPhotos = [];
                            
                            foreach ($data['photos'] as $photo) {
                                if ($photo instanceof UploadedFile) {
                                    // Generate hash berdasarkan ukuran dan nama asli foto untuk identifikasi
                                    $photoId = md5($photo->getClientOriginalName() . $photo->getSize());
                                    
                                    // Skip jika foto ini sudah diproses dalam batch yang sama
                                    if (in_array($photoId, $processedPhotos)) {
                                        \Log::info('Duplicate photo skipped in same batch', [
                                            'item_id' => $itemId,
                                            'photo_id' => $photoId
                                        ]);
                                        continue;
                                    }
                                    
                                    // Cek apakah foto dengan metadata serupa sudah disimpan dalam 60 detik terakhir
                                    $recentPhoto = DailyCheckPhoto::where('daily_check_id', $dailyCheck->id)
                                        ->where('item_id', $itemId)
                                        ->where('photo_size', $photo->getSize())
                                        ->where('created_at', '>', now()->subSeconds(60))
                                        ->first();

                                    if (!$recentPhoto) {
                                        $path = $photo->store('daily-checks/photos', 'public');
                                        DailyCheckPhoto::create([
                                            'daily_check_id' => $dailyCheck->id,
                                            'item_id' => $itemId,
                                            'photo_path' => $path,
                                            'photo_size' => $photo->getSize(),
                                            'original_name' => $photo->getClientOriginalName()
                                        ]);
                                        
                                        // Tandai foto ini sudah diproses
                                        $processedPhotos[] = $photoId;
                                        
                                        \Log::info('New photo saved', [
                                            'item_id' => $itemId,
                                            'path' => $path,
                                            'photo_id' => $photoId
                                        ]);
                                    } else {
                                        \Log::info('Similar photo already exists, skipped', [
                                            'item_id' => $itemId,
                                            'existing_photo_id' => $recentPhoto->id
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();
            \Log::info('Autosave completed successfully');

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'daily_check_id' => $dailyCheck->id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Validation error in autosave', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Autosave failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_outlet' => 'required',
                'date' => 'required|date',
                'status' => 'required',
                'checks' => 'array'
            ]);
            
            // Log incoming data for debugging
            \Log::info('Daily Check Store Raw Data', [
                'raw_data' => $request->all()
            ]);
            
            // Create or find daily check
            $dailyCheck = DailyCheck::updateOrCreate(
                [
                    'id_outlet' => $validated['id_outlet'],
                    'date' => $validated['date']
                ],
                [
                    'status' => $validated['status']
                ]
            );
            
            if (isset($request->checks) && is_array($request->checks)) {
                foreach ($request->checks as $itemId => $data) {
                    // Initialize update data with defaults
                    $updateData = [
                        'other_issue' => '',
                        'remark' => ''
                    ];
                    
                    // Set condition if provided
                    if (isset($data['condition'])) {
                        $updateData['condition'] = $data['condition'];
                    }
                    
                    // Override with provided values
                    if (isset($data['other_issue'])) {
                        $updateData['other_issue'] = $data['other_issue'];
                    }
                    
                    if (isset($data['remark'])) {
                        $updateData['remark'] = $data['remark'];
                    }
                    
                    if (isset($data['time'])) {
                        $updateData['time'] = $data['time'];
                    } else if (!isset($updateData['time'])) {
                        $updateData['time'] = now()->format('H:i');
                    }
                    
                    \Log::info('Storing daily check item', [
                        'item_id' => $itemId,
                        'raw_data' => $data,
                        'updateData' => $updateData,
                        'other_issue_set' => isset($data['other_issue']),
                        'remark_set' => isset($data['remark'])
                    ]);
                    
                    // Save the record
                    DailyCheckDetail::updateOrCreate(
                        [
                            'daily_check_id' => $dailyCheck->id,
                            'item_id' => $itemId
                        ],
                        $updateData
                    );
                    
                    // Handle photos
                    if (isset($data['photos']) && is_array($data['photos'])) {
                        // Kumpulkan semua foto yang akan diproses dalam satu batch
                        $processedPhotos = [];
                        
                        foreach ($data['photos'] as $photo) {
                            if ($photo instanceof UploadedFile) {
                                // Generate hash berdasarkan ukuran dan nama asli foto untuk identifikasi
                                $photoId = md5($photo->getClientOriginalName() . $photo->getSize());
                                
                                // Skip jika foto ini sudah diproses dalam batch yang sama
                                if (in_array($photoId, $processedPhotos)) {
                                    \Log::info('Duplicate photo skipped in same batch', [
                                        'item_id' => $itemId,
                                        'photo_id' => $photoId
                                    ]);
                                    continue;
                                }
                                
                                // Cek apakah foto dengan metadata serupa sudah disimpan dalam 60 detik terakhir
                                $recentPhoto = DailyCheckPhoto::where('daily_check_id', $dailyCheck->id)
                                    ->where('item_id', $itemId)
                                    ->where('photo_size', $photo->getSize())
                                    ->where('created_at', '>', now()->subSeconds(60))
                                    ->first();

                                if (!$recentPhoto) {
                                    $path = $photo->store('daily-checks/photos', 'public');
                                    DailyCheckPhoto::create([
                                        'daily_check_id' => $dailyCheck->id,
                                        'item_id' => $itemId,
                                        'photo_path' => $path,
                                        'photo_size' => $photo->getSize(),
                                        'original_name' => $photo->getClientOriginalName()
                                    ]);
                                    
                                    // Tandai foto ini sudah diproses
                                    $processedPhotos[] = $photoId;
                                    
                                    \Log::info('New photo saved', [
                                        'item_id' => $itemId,
                                        'path' => $path,
                                        'photo_id' => $photoId
                                    ]);
                                } else {
                                    \Log::info('Similar photo already exists, skipped', [
                                        'item_id' => $itemId,
                                        'existing_photo_id' => $recentPhoto->id
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Data daily check berhasil disimpan',
                'redirect' => route('daily-check.index')
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error saving daily check: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function list()
    {
        $dailyChecks = DailyCheckHeader::with(['outlet'])
            ->orderBy('date', 'desc')
            ->get();
            
        $outlets = Outlet::orderBy('nama_outlet')->get();
            
        return view('daily-check.list', compact('dailyChecks', 'outlets'));
    }

    public function show($id)
    {
        $dailyCheck = DailyCheckHeader::with(['outlet', 'details.item.area', 'photos'])->findOrFail($id);
        
        // Tambahkan logging untuk debugging
        \Log::info('Show Daily Check Details', [
            'id' => $id,
            'has_details' => $dailyCheck->details->count(),
            'details' => $dailyCheck->details->take(3)->toArray()
        ]);
        
        return view('daily-check.show', compact('dailyCheck'));
    }
    
    public function edit($id)
    {
        $dailyCheck = DailyCheckHeader::with(['outlet', 'details.item.area', 'photos'])->findOrFail($id);
        $areas = Area::with('items')->get();
        $outlets = Outlet::orderBy('nama_outlet')->get();
        
        // Tambahkan logging untuk debugging
        \Log::info('Edit Daily Check Details', [
            'id' => $id,
            'has_details' => $dailyCheck->details->count(),
            'details' => $dailyCheck->details->take(3)->toArray()
        ]);
        
        return view('daily-check.edit', compact('dailyCheck', 'areas', 'outlets'));
    }
    
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $dailyCheck = DailyCheckHeader::findOrFail($id);
            
            // Hapus semua detail
            $dailyCheck->details()->delete();
            
            // Hapus semua foto
            $dailyCheck->photos()->delete();
            
            // Hapus header
            $dailyCheck->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Daily check berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus daily check: ' . $e->getMessage()
            ], 500);
        }
    }

    public function debug($id = null)
    {
        if ($id) {
            $dailyCheck = DailyCheckHeader::with(['details', 'photos'])->findOrFail($id);
            dd([
                'header' => $dailyCheck->toArray(),
                'details' => $dailyCheck->details->toArray(),
                'photos' => $dailyCheck->photos->toArray()
            ]);
        } else {
            $latestDailyCheck = DailyCheckHeader::with(['details', 'photos'])
                ->latest()
                ->first();
                
            if (!$latestDailyCheck) {
                return 'No daily checks found.';
            }
            
            dd([
                'header' => $latestDailyCheck->toArray(),
                'details' => $latestDailyCheck->details->toArray(),
                'photos' => $latestDailyCheck->photos->toArray()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $dailyCheck = DailyCheck::findOrFail($id);
            $rawData = $request->all();
            
            // Log incoming data for debugging
            \Log::info('Daily Check Update Raw Data', [
                'id' => $id,
                'raw_data' => $rawData
            ]);
            
            if (isset($rawData['checks']) && is_array($rawData['checks'])) {
                foreach ($rawData['checks'] as $itemId => $data) {
                    // Initialize update data with defaults
                    $updateData = [
                        'other_issue' => '',
                        'remark' => '',
                    ];
                    
                    // Set condition if provided
                    if (isset($data['condition'])) {
                        $updateData['condition'] = $data['condition'];
                    }
                    
                    // Override with provided values
                    if (isset($data['other_issue'])) {
                        $updateData['other_issue'] = $data['other_issue'];
                    }
                    
                    if (isset($data['remark'])) {
                        $updateData['remark'] = $data['remark'];
                    }
                    
                    if (isset($data['time'])) {
                        $updateData['time'] = $data['time'];
                    } else if (!isset($updateData['time'])) {
                        $updateData['time'] = now()->format('H:i');
                    }
                    
                    \Log::info('Updating daily check item', [
                        'item_id' => $itemId,
                        'raw_data' => $data,
                        'updateData' => $updateData,
                        'other_issue_set' => isset($data['other_issue']),
                        'remark_set' => isset($data['remark'])
                    ]);
                    
                    // Update the existing detail or create if not exists
                    DailyCheckDetail::updateOrCreate(
                        [
                            'daily_check_id' => $dailyCheck->id,
                            'item_id' => $itemId
                        ],
                        $updateData
                    );
                    
                    // Handle photos
                    if (isset($data['photos']) && is_array($data['photos'])) {
                        // Kumpulkan semua foto yang akan diproses dalam satu batch
                        $processedPhotos = [];
                        
                        foreach ($data['photos'] as $photo) {
                            if ($photo instanceof UploadedFile) {
                                // Generate hash berdasarkan ukuran dan nama asli foto untuk identifikasi
                                $photoId = md5($photo->getClientOriginalName() . $photo->getSize());
                                
                                // Skip jika foto ini sudah diproses dalam batch yang sama
                                if (in_array($photoId, $processedPhotos)) {
                                    \Log::info('Duplicate photo skipped in same batch', [
                                        'item_id' => $itemId,
                                        'photo_id' => $photoId
                                    ]);
                                    continue;
                                }
                                
                                // Cek apakah foto dengan metadata serupa sudah disimpan dalam 60 detik terakhir
                                $recentPhoto = DailyCheckPhoto::where('daily_check_id', $dailyCheck->id)
                                    ->where('item_id', $itemId)
                                    ->where('photo_size', $photo->getSize())
                                    ->where('created_at', '>', now()->subSeconds(60))
                                    ->first();

                                if (!$recentPhoto) {
                                    $path = $photo->store('daily-checks/photos', 'public');
                                    DailyCheckPhoto::create([
                                        'daily_check_id' => $dailyCheck->id,
                                        'item_id' => $itemId,
                                        'photo_path' => $path,
                                        'photo_size' => $photo->getSize(),
                                        'original_name' => $photo->getClientOriginalName()
                                    ]);
                                    
                                    // Tandai foto ini sudah diproses
                                    $processedPhotos[] = $photoId;
                                    
                                    \Log::info('New photo saved', [
                                        'item_id' => $itemId,
                                        'path' => $path,
                                        'photo_id' => $photoId
                                    ]);
                                } else {
                                    \Log::info('Similar photo already exists, skipped', [
                                        'item_id' => $itemId,
                                        'existing_photo_id' => $recentPhoto->id
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating daily check: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a photo.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePhoto(Request $request)
    {
        try {
            // Jika dipanggil dengan parameter id dari URL
            if ($request->isMethod('delete') && $request->route('id')) {
                $photoId = $request->route('id');
            } 
            // Jika dipanggil dengan POST request
            else if ($request->has('photo_id')) {
                $photoId = $request->input('photo_id');
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'ID foto tidak ditemukan'
                ], 400);
            }
            
            $photo = DailyCheckPhoto::findOrFail($photoId);
            
            // Hapus file foto
            if (\Storage::disk('public')->exists($photo->photo_path)) {
                \Storage::disk('public')->delete($photo->photo_path);
            }
            
            // Hapus record
            $photo->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting photo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Upload foto untuk daily check item
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPhoto(Request $request)
    {
        try {
            $request->validate([
                'daily_check_id' => 'required|exists:daily_check_headers,id',
                'item_id' => 'required|exists:daily_check_items,id',
                'photos' => 'required|array',
                'photos.*' => 'required|image|max:5120', // Max 5MB per image
            ]);
            
            $dailyCheckId = $request->input('daily_check_id');
            $itemId = $request->input('item_id');
            $uploadedPhotos = [];
            
            // Simpan setiap foto yang diunggah
            foreach ($request->file('photos') as $photo) {
                // Generate hash berdasarkan ukuran dan nama asli foto untuk identifikasi
                $photoId = md5($photo->getClientOriginalName() . $photo->getSize());
                
                // Cek apakah foto dengan metadata serupa sudah disimpan dalam 60 detik terakhir
                $recentPhoto = DailyCheckPhoto::where('daily_check_id', $dailyCheckId)
                    ->where('item_id', $itemId)
                    ->where('photo_size', $photo->getSize())
                    ->where('created_at', '>', now()->subSeconds(60))
                    ->first();
                
                if (!$recentPhoto) {
                    $path = $photo->store('daily-checks/photos', 'public');
                    $newPhoto = DailyCheckPhoto::create([
                        'daily_check_id' => $dailyCheckId,
                        'item_id' => $itemId,
                        'photo_path' => $path,
                        'photo_size' => $photo->getSize(),
                        'original_name' => $photo->getClientOriginalName()
                    ]);
                    
                    $uploadedPhotos[] = [
                        'id' => $newPhoto->id,
                        'path' => $path
                    ];
                    
                    \Log::info('New photo uploaded', [
                        'item_id' => $itemId,
                        'path' => $path,
                        'photo_id' => $photoId
                    ]);
                } else {
                    \Log::info('Similar photo already exists, skipped', [
                        'item_id' => $itemId,
                        'existing_photo_id' => $recentPhoto->id
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diunggah',
                'photos' => $uploadedPhotos
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in photo upload', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Error uploading photos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah foto: ' . $e->getMessage()
            ], 500);
        }
    }
} 