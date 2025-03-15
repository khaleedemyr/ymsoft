<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    use LogActivity;

    public function index()
    {
        try {
            $roles = Role::with('permissions')->get();
            $menus = Menu::where('status', 'active')->get();
            
            return view('auth.roles.index', compact('roles', 'menus'));
        } catch (\Exception $e) {
            Log::error('Error in RoleController@index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }

    public function create()
    {
        $menus = Menu::where('status', 'active')->get();
        return view('roles.create', compact('menus'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validasi input dengan pesan error kustom
            $validator = validator($request->all(), [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('roles', 'name')
                ],
                'description' => 'nullable|string',
                'permissions' => 'array'
            ], [
                'name.required' => 'Nama role harus diisi',
                'name.unique' => 'Nama role sudah digunakan',
                'name.max' => 'Nama role maksimal 255 karakter'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Buat role baru
            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
                'status' => 'active'
            ]);

            // Simpan permissions
            if ($request->has('permissions')) {
                foreach ($request->permissions as $menuId => $permissions) {
                    Permission::create([
                        'role_id' => $role->id,
                        'menu_id' => $menuId,
                        'can_view' => isset($permissions['can_view']),
                        'can_create' => isset($permissions['can_create']),
                        'can_edit' => isset($permissions['can_edit']),
                        'can_delete' => isset($permissions['can_delete'])
                    ]);
                }
            }

            // Log aktivitas
            $this->logActivity(
                'CREATE',
                'roles',
                'Membuat role baru: ' . $role->name,
                null,
                $role->load('permissions')->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in RoleController@store: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Role $role)
    {
        $role->load('permissions.menu');
        $menus = Menu::where('status', 'active')->get();
        return view('roles.edit', compact('role', 'menus'));
    }

    public function update(Request $request, Role $role)
    {
        try {
            DB::beginTransaction();

            $oldData = $role->load('permissions')->toArray();

            // Validasi input dengan pesan error kustom
            $validator = validator($request->all(), [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('roles', 'name')->ignore($role->id)
                ],
                'description' => 'nullable|string',
                'permissions' => 'array'
            ], [
                'name.required' => 'Nama role harus diisi',
                'name.unique' => 'Nama role sudah digunakan',
                'name.max' => 'Nama role maksimal 255 karakter'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Update role
            $role->update([
                'name' => $request->name,
                'description' => $request->description
            ]);

            // Hapus permissions lama
            $role->permissions()->delete();

            // Simpan permissions baru
            if ($request->has('permissions')) {
                foreach ($request->permissions as $menuId => $permissions) {
                    Permission::create([
                        'role_id' => $role->id,
                        'menu_id' => $menuId,
                        'can_view' => isset($permissions['can_view']),
                        'can_create' => isset($permissions['can_create']),
                        'can_edit' => isset($permissions['can_edit']),
                        'can_delete' => isset($permissions['can_delete'])
                    ]);
                }
            }

            // Log aktivitas
            $this->logActivity(
                'UPDATE',
                'roles',
                'Mengupdate role: ' . $role->name,
                $oldData,
                $role->fresh()->load('permissions')->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in RoleController@update: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request, Role $role)
    {
        try {
            DB::beginTransaction();

            $oldData = $role->toArray();

            // Validasi status
            $request->validate([
                'status' => 'required|in:active,inactive'
            ]);

            // Update status
            $role->update(['status' => $request->status]);

            // Log aktivitas
            $this->logActivity(
                'UPDATE',
                'roles',
                'Mengubah status role ' . $role->name . ' menjadi ' . $request->status,
                $oldData,
                $role->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status role berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in RoleController@toggleStatus: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status role'
            ], 500);
        }
    }

    public function destroy(Role $role)
    {
        try {
            DB::beginTransaction();

            $oldData = $role->load('permissions')->toArray();

            // Cek apakah role masih digunakan
            if ($role->users()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role tidak dapat dihapus karena masih digunakan'
                ], 422);
            }

            // Hapus permissions
            $role->permissions()->delete();
            
            // Hapus role
            $role->delete();

            // Log aktivitas
            $this->logActivity(
                'DELETE',
                'roles',
                'Menghapus role: ' . $role->name,
                $oldData,
                null
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in RoleController@destroy: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus role'
            ], 500);
        }
    }
} 