namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckOutletAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $outletId = $request->route('outlet_id');

        // Jika user tidak memiliki akses dan mencoba mengakses outlet lain
        if (!$this->userCanSelectOutlet($user) && $user->id_outlet != $outletId) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Tidak memiliki akses'], 403);
            }
            return redirect()->back()->with('error', 'Tidak memiliki akses');
        }

        return $next($request);
    }

    private function userCanSelectOutlet($user)
    {
        return $user->id_role === '5af56935b011a' || 
               $user->division_id === 20 || 
               $user->id_outlet === 1;
    }
}
