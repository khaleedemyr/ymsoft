<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use LogActivity;

    public function index()
    {
        \Log::info('UserController@index dipanggil');
        
        try {
            $users = User::with(['roles', 'jabatan', 'divisi', 'outlet'])
                ->where('status', 'A')
                ->get();
            
            \Log::info('Data users:', ['count' => $users->count()]);
            
            $roles = Role::where('status', 'active')
                ->get();
            
            \Log::info('Data roles:', ['count' => $roles->count()]);

            // Tambahkan debugging untuk memastikan view yang digunakan
            \Log::info('View path:', ['path' => 'users.index']);
            
            // Debug data yang dikirim ke view
            \Log::info('Data yang dikirim ke view:', [
                'users_exists' => isset($users),
                'roles_exists' => isset($roles),
                'users_count' => $users->count(),
                'roles_count' => $roles->count()
            ]);

            return view('users.index', compact('users', 'roles'));
            
        } catch (\Exception $e) {
            \Log::error('Error di UserController@index: ' . $e->getMessage());
            throw $e;
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'roles' => 'required|array',
                'roles.*' => 'exists:roles,id'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'active'
            ]);

            $user->roles()->attach($request->roles);

            $this->logActivity(
                'CREATE',
                'users',
                'Setting Role baru: ' . $user->name,
                null,
                $user->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8',
                'roles' => 'required|array',
                'roles.*' => 'exists:roles,id'
            ]);

            $oldData = $user->toArray();

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);
            $user->roles()->sync($request->roles);

            $this->logActivity(
                'UPDATE',
                'users',
                'Mengupdate user: ' . $user->name,
                $oldData,
                $user->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request, User $user)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'status' => 'required|in:active,inactive'
            ]);

            $oldData = $user->toArray();
            $oldStatus = $user->status;

            $user->status = $request->status;
            $user->save();

            $this->logActivity(
                'UPDATE',
                'users',
                'Mengubah status user ' . $user->name . ' dari ' . $oldStatus . ' menjadi ' . $request->status,
                $oldData,
                $user->toArray()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status user berhasil diubah'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            $oldData = $user->toArray();

            $user->roles()->detach();
            $user->delete();

            $this->logActivity(
                'DELETE',
                'users',
                'Menghapus user: ' . $user->name,
                $oldData,
                null
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function setRole(Request $request, $userId)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($userId);
            
            // Hapus role yang ada
            $user->roles()->detach();
            
            // Tambahkan role baru
            if ($request->role_id) {
                $user->roles()->attach($request->role_id);
            }

            // Log aktivitas
            $this->logActivity(
                'UPDATE',
                'users',
                'Mengubah role user: ' . $user->nama_lengkap,
                ['old_roles' => $user->roles()->pluck('name')],
                ['new_role' => Role::find($request->role_id)->name]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role user berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah role user: ' . $e->getMessage()
            ], 500);
        }
    }
}
