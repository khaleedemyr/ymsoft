<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Menu;

class CheckMenuPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()->getName();
        
        $menu = Menu::where('route', $routeName)->first();
        
        if (!$menu) {
            abort(404);
        }

        $userRole = auth()->user()->roles->first();
        
        if (!$userRole) {
            abort(403, 'Unauthorized');
        }

        $permission = $userRole->permissions()
            ->where('menu_id', $menu->id)
            ->where('can_view', 1)
            ->first();

        if (!$permission) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
