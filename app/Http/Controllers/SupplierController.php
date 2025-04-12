<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    use LogActivity;

    public function index()
    {
        $suppliers = Supplier::all();
        
        // Log activity
        $this->logActivity(
            'READ',
            'suppliers',
            'Mengakses daftar supplier',
            null,
            null
        );
        
        return view('master-data.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('master-data.suppliers.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateRequest($request);
            
            if ($validated['payment_term'] === 'cash') {
                $validated['payment_days'] = 0;
            }
            
            $supplier = Supplier::create($validated);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Supplier berhasil ditambahkan',
                    'supplier' => $supplier
                ]);
            } else {
                return redirect()->route('suppliers.index')
                    ->with('success', 'Supplier berhasil ditambahkan');
            }
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            } else {
                return redirect()->back()
                    ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                    ->withInput();
            }
        }
    }

    public function show($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            
            // Default payment_term ke 'cash' jika null
            if (is_null($supplier->payment_term)) {
                $supplier->payment_term = 'cash';
            }
            
            // Default payment_days ke 0 jika null
            if (is_null($supplier->payment_days)) {
                $supplier->payment_days = 0;
            }
            
            return response()->json([
                'success' => true,
                'supplier' => $supplier
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier tidak ditemukan'
            ], 404);
        }
    }

    public function edit($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'supplier' => $supplier
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $oldData = $supplier->toArray();
            
            $validated = $this->validateRequest($request);
            
            // Jika payment_term adalah cash, set payment_days ke 0
            if ($validated['payment_term'] === 'cash') {
                $validated['payment_days'] = 0;
            }
            
            $supplier->update($validated);
            
            // Log activity jika menggunakan trait LogActivity
            if (method_exists($this, 'logActivity')) {
                $this->logActivity(
                    'suppliers',
                    'UPDATE',
                    'Mengubah supplier: ' . $supplier->name,
                    $oldData,
                    $supplier->toArray()
                );
            }
            
            return response()->json([
                'success' => true,
                'message' => __('translation.supplier.success_edit'),
                'supplier' => $supplier
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $oldData = $supplier->toArray();
            
            $supplier->status = $request->status;
            $supplier->save();
            
            // Log activity jika method tersedia
            if (method_exists($this, 'logActivity')) {
                $this->logActivity(
                    'suppliers',
                    'UPDATE',
                    'Mengubah status supplier: ' . $supplier->name . ' menjadi ' . $request->status,
                    $oldData,
                    $supplier->toArray()
                );
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Status supplier berhasil diubah',
                'supplier' => $supplier
            ]);
        } catch (\Exception $e) {
            \Log::error('Error toggling supplier status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $oldData = $supplier->toArray();
            
            $supplier->delete();
            
            // Log activity jika method tersedia
            if (method_exists($this, 'logActivity')) {
                $this->logActivity(
                    'suppliers',
                    'DELETE',
                    'Menghapus supplier: ' . $supplier->name,
                    $oldData,
                    null
                );
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting supplier: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'npwp' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'payment_term' => 'required|string|in:cash,credit',
            'payment_days' => 'nullable|integer|min:0',
            'status' => 'required|string|in:active,inactive',
        ]);
    }
} 