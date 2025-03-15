<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $menuSlug, $permission = 'view')
    {
        $user = auth()->user();
        
        // Super admin bypass all permissions
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        // Check if user has the required permission through any of their roles
        $hasPermission = $user->roles()->get()->contains(function ($role) use ($menuSlug, $permission) {
            return $role->hasPermission($menuSlug, $permission);
        });

        if (!$hasPermission) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan aksi ini'
                ], 403);
            }
            
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melakukan aksi ini');
        }

        return $next($request);
    }
} 