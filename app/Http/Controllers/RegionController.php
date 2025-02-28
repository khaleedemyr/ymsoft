<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;
use App\Traits\LogActivity;

class RegionController extends Controller
{
    use LogActivity;

    public function index()
    {
        $regions = Region::all();
        return view('regions.index', compact('regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|max:10|unique:regions',
            'name' => 'required|max:50'
        ]);

        $region = Region::create($request->all());
        
        // Log activity
        $this->logActivity(
            'CREATE',     // module
            'regions',    // activity_type
            'Membuat region baru: ' . $region->name,
            null,
            $region->toArray()
        );

        return response()->json(['success' => true, 'message' => trans('translation.region.success_add')]);
    }

    public function update(Request $request, Region $region)
    {
        $request->validate([
            'code' => 'required|max:10|unique:regions,code,' . $region->id,
            'name' => 'required|max:50'
        ]);

        $oldData = $region->toArray();
        $region->update($request->all());
        
        // Log activity
        $this->logActivity(
            'UPDATE',     // module
            'regions',    // activity_type
            'Mengupdate region: ' . $region->name,
            $oldData,
            $region->toArray()
        );

        return response()->json(['success' => true, 'message' => trans('translation.region.success_edit')]);
    }

    public function toggleStatus(Request $request, Region $region)
    {
        try {
            $newStatus = $request->status;
            $oldStatus = $region->status;
            $oldData = $region->toArray();
            
            $region->status = $newStatus;
            $region->save();

            // Log activity
            $this->logActivity(
                'UPDATE',     // module
                'regions',    // activity_type
                'Mengubah status region: ' . $region->name . ' menjadi ' . $newStatus,
                $oldData,
                $region->toArray()
            );

            return response()->json([
                'success' => true,
                'message' => trans('translation.region.success_status_change')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Region $region)
    {
        $oldData = $region->toArray();
        $region->delete();
        
        // Log activity
        $this->logActivity(
            'DELETE',     // module
            'regions',    // activity_type
            'Menghapus region: ' . $region->name,
            $oldData,
            null
        );

        return response()->json(['success' => true, 'message' => trans('translation.region.success_delete')]);
    }
} 