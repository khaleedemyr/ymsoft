<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\LogActivity;

class SubCategoryController extends Controller
{
    use LogActivity;

    public function index()
    {
        $subCategories = SubCategory::with('category')->get();
        $categories = Category::all();
        return view('sub-categories.index', compact('subCategories', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:100',
            'description' => 'nullable'
        ]);

        $subCategory = SubCategory::create($request->all());
        
        // Log activity
        $this->logActivity(
             'CREATE',     // module
           'sub_categories',             // activity_type
            'Membuat sub kategori baru: ' . $subCategory->name,
            null,
            $subCategory->toArray()
        );

        return response()->json(['success' => true, 'message' => trans('translation.subcategory.success_add')]);
    }

    public function update(Request $request, SubCategory $subCategory)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:100',
            'description' => 'nullable'
        ]);

        $oldData = $subCategory->toArray();
        $subCategory->update($request->all());
        
        // Log activity
        $this->logActivity(
            'UPDATE',     // module
            'sub_categories',             // activity_type
            'Mengupdate sub kategori: ' . $subCategory->name,
            $oldData,
            $subCategory->toArray()
        );

        return response()->json(['success' => true, 'message' => trans('translation.subcategory.success_edit')]);
    }

    public function toggleStatus(Request $request, SubCategory $subCategory)
    {
        try {
            $newStatus = $request->status;
            $oldStatus = $subCategory->status;
            $oldData = $subCategory->toArray();
            
            $subCategory->status = $newStatus;
            $subCategory->save();

            // Log activity
            $this->logActivity(
                'UPDATE',     // module
                'sub_categories',             // activity_type
                'Mengubah status sub kategori: ' . $subCategory->name . ' menjadi ' . $newStatus,
                $oldData,
                $subCategory->toArray()
            );

            return response()->json([
                'success' => true,
                'message' => trans('translation.subcategory.success_status_change')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(SubCategory $subCategory)
    {
        $oldData = $subCategory->toArray();
        $subCategory->delete();
        
        // Log activity
        $this->logActivity(
           'DELETE' ,     // module
            'sub_categories',             // activity_type
            'Menghapus sub kategori: ' . $subCategory->name,
            $oldData,
            null
        );

        return response()->json(['success' => true, 'message' => trans('translation.subcategory.success_delete')]);
    }
} 