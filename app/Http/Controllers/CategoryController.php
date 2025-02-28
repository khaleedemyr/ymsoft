<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\LogActivity;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use LogActivity;

    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|max:20|unique:categories',
                'name' => 'required|max:100',
                'description' => 'nullable'
            ]);

            $category = Category::create($validated);
            
            // Log aktivitas
            $this->logActivity(
                'CREATE',
                'categories',
                'Membuat kategori baru: ' . $category->code . ' - ' . $category->name,
                null,
                $category->toArray()
            );

            return response()->json([
                'success' => true,
                'message' => __('translation.category.success_add')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Category $category)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|max:20|unique:categories,code,' . $category->id,
                'name' => 'required|max:100',
                'description' => 'nullable'
            ]);

            $oldData = $category->toArray();
            $category->update($validated);

            // Log aktivitas
            $this->logActivity(
                'UPDATE',
                'categories',
                'Mengupdate kategori: ' . $category->code . ' - ' . $category->name,
                $oldData,
                $category->toArray()
            );

            return response()->json([
                'success' => true,
                'message' => __('translation.category.success_edit')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Category $category)
    {
        try {
            $oldData = $category->toArray();
            $categoryName = $category->name;
            
            $category->delete();

            // Log aktivitas
            $this->logActivity(
                'DELETE',
                'categories',
                'Menghapus kategori: ' . $categoryName,
                $oldData,
                null
            );

            return response()->json([
                'success' => true,
                'message' => __('translation.category.success_delete')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Category $category)
    {
        try {
            $oldData = $category->toArray();
            $newStatus = $category->status === 'active' ? 'inactive' : 'active';
            
            $category->update(['status' => $newStatus]);

            // Log aktivitas
            $this->logActivity(
                'UPDATE',
                'categories',
                'Mengubah status kategori: ' . $category->code . ' - ' . $category->name . ' menjadi ' . $newStatus,
                $oldData,
                $category->toArray()
            );

            return response()->json([
                'success' => true,
                'message' => __('translation.category.success_status_change')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getSubCategories(Category $category)
    {
        return response()->json($category->subCategories);
    }
} 