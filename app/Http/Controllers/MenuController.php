<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    use LogActivity;

    public function index()
    {
        // Ambil semua menu dengan parent
        $menus = Menu::with('parent')->orderBy('order')->get();
        
        // Ambil menu yang bisa jadi parent (yang tidak memiliki parent)
        $parentMenus = Menu::whereNull('parent_id')
            ->where('status', 'active')
            ->get();

        return view('auth.menus.index', compact('menus', 'parentMenus'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validasi input
            $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:menus,slug',
                'icon' => 'nullable|string|max:255',
                'route' => 'nullable|string|max:255',
                'parent_id' => 'nullable|exists:menus,id',
                'order' => 'required|integer|min:0'
            ]);

            // Buat menu baru
            $menu = Menu::create([
                'name' => $request->name,
                'slug' => $request->slug ?: Str::slug($request->name),
                'icon' => $request->icon,
                'route' => $request->route,
                'parent_id' => $request->parent_id,
                'order' => $request->order,
                'status' => 'active'
            ]);

            // Log activity
            $this->logActivity(
                'CREATE',
                'menus',
                'Membuat menu baru: ' . $menu->name,
                null,
                $menu->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Menu $menu)
    {
        try {
            DB::beginTransaction();

            // Validasi input
            $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:menus,slug,' . $menu->id,
                'icon' => 'nullable|string|max:255',
                'route' => 'nullable|string|max:255',
                'parent_id' => 'nullable|exists:menus,id',
                'order' => 'required|integer|min:0'
            ]);

            // Simpan data lama untuk logging
            $oldData = $menu->toArray();

            // Update menu
            $menu->update([
                'name' => $request->name,
                'slug' => $request->slug ?: Str::slug($request->name),
                'icon' => $request->icon,
                'route' => $request->route,
                'parent_id' => $request->parent_id,
                'order' => $request->order
            ]);

            // Log activity
            $this->logActivity(
                'UPDATE',
                'menus',
                'Mengupdate menu: ' . $menu->name,
                $oldData,
                $menu->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request, Menu $menu)
    {
        try {
            DB::beginTransaction();

            // Validasi status
            $request->validate([
                'status' => 'required|in:active,inactive'
            ]);

            // Simpan data lama untuk logging
            $oldData = $menu->toArray();
            $oldStatus = $menu->status;

            // Update status
            $menu->status = $request->status;
            $menu->save();

            // Log activity
            $this->logActivity(
                'UPDATE',
                'menus',
                'Mengubah status menu ' . $menu->name . ' dari ' . $oldStatus . ' menjadi ' . $request->status,
                $oldData,
                $menu->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status menu berhasil diubah'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Menu $menu)
    {
        try {
            DB::beginTransaction();

            // Cek apakah menu memiliki child
            if ($menu->children()->exists()) {
                throw new \Exception('Menu ini memiliki sub-menu. Hapus sub-menu terlebih dahulu.');
            }

            // Simpan data lama untuk logging
            $oldData = $menu->toArray();

            // Hapus menu
            $menu->delete();

            // Log activity
            $this->logActivity(
                'DELETE',
                'menus',
                'Menghapus menu: ' . $menu->name,
                $oldData,
                null
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getParentMenus()
    {
        try {
            $parentMenus = Menu::whereNull('parent_id')
                ->where('status', 'active')
                ->get(['id', 'name']);

            return response()->json([
                'success' => true,
                'data' => $parentMenus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
