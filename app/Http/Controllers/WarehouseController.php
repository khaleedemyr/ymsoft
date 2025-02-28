<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Traits\LogActivity;

class WarehouseController extends Controller
{
    use LogActivity;

    public function index()
    {
        $warehouses = Warehouse::all();
        return view('warehouses.index', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:warehouses|max:50',
            'name' => 'required|max:100',
            'location' => 'required|max:255',
        ]);

        $warehouse = Warehouse::create($request->all());

        // Log activity
        $this->logActivity('CREATE', 'warehouses', 'Membuat gudang baru: ' . $warehouse->name, null, $warehouse->toArray());

        return response()->json(['success' => true, 'message' => 'Gudang berhasil ditambahkan']);
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'code' => 'required|max:50|unique:warehouses,code,' . $warehouse->id,
            'name' => 'required|max:100',
            'location' => 'required|max:255',
        ]);

        $oldData = $warehouse->toArray();
        $warehouse->update($request->all());

        // Log activity
        $this->logActivity('UPDATE', 'warehouses', 'Mengupdate gudang: ' . $warehouse->name, $oldData, $warehouse->toArray());

        return response()->json(['success' => true, 'message' => 'Gudang berhasil diperbarui']);
    }

    public function toggleStatus(Request $request, Warehouse $warehouse)
    {
        $newStatus = $request->status;
        $oldData = $warehouse->toArray();

        $warehouse->status = $newStatus;
        $warehouse->save();

        // Log activity
        $this->logActivity('UPDATE', 'warehouses', 'Mengubah status gudang: ' . $warehouse->name . ' menjadi ' . $newStatus, $oldData, $warehouse->toArray());

        return response()->json(['success' => true, 'message' => 'Status gudang berhasil diperbarui']);
    }

    public function destroy(Warehouse $warehouse)
    {
        $oldData = $warehouse->toArray();
        $warehouse->delete();

        // Log activity
        $this->logActivity('DELETE', 'warehouses', 'Menghapus gudang: ' . $warehouse->name, $oldData, null);

        return response()->json(['success' => true, 'message' => 'Gudang berhasil dihapus']);
    }
}
