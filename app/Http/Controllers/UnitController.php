<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Traits\LogActivity;

class UnitController extends Controller
{
    use LogActivity;

    public function index()
    {
        $units = Unit::all();
        return view('units.index', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|max:10|unique:units',
            'name' => 'required|max:50'
        ]);

        $unit = Unit::create($request->all());
        
        // Log activity
        $this->logActivity(
            'CREATE',     // module
            'units',      // activity_type
            'Membuat unit baru: ' . $unit->name,
            null,
            $unit->toArray()
        );

        return response()->json(['success' => true, 'message' => trans('translation.unit.success_add')]);
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'code' => 'required|max:10|unique:units,code,' . $unit->id,
            'name' => 'required|max:50'
        ]);

        $oldData = $unit->toArray();
        $unit->update($request->all());
        
        // Log activity
        $this->logActivity(
            'UPDATE',     // module
            'units',      // activity_type
            'Mengupdate unit: ' . $unit->name,
            $oldData,
            $unit->toArray()
        );

        return response()->json(['success' => true, 'message' => trans('translation.unit.success_edit')]);
    }

    public function toggleStatus(Request $request, Unit $unit)
    {
        try {
            $newStatus = $request->status;
            $oldStatus = $unit->status;
            $oldData = $unit->toArray();
            
            $unit->status = $newStatus;
            $unit->save();

            // Log activity
            $this->logActivity(
                'UPDATE',     // module
                'units',      // activity_type
                'Mengubah status unit: ' . $unit->name . ' menjadi ' . $newStatus,
                $oldData,
                $unit->toArray()
            );

            return response()->json([
                'success' => true,
                'message' => trans('translation.unit.success_status_change')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Unit $unit)
    {
        $oldData = $unit->toArray();
        $unit->delete();
        
        // Log activity
        $this->logActivity(
            'DELETE',     // module
            'units',      // activity_type
            'Menghapus unit: ' . $unit->name,
            $oldData,
            null
        );

        return response()->json(['success' => true, 'message' => trans('translation.unit.success_delete')]);
    }
} 