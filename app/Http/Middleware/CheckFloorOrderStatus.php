<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\FloorOrder;

class CheckFloorOrderStatus
{
    public function handle(Request $request, Closure $next)
    {
        $floorOrder = $request->route('floorOrder');

        if ($floorOrder) {
            // Khusus untuk save-draft endpoint
            if ($request->is('*/save-draft')) {
                if ($floorOrder->status !== 'draft') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hanya Floor Order dengan status draft yang dapat disimpan'
                    ], 403);
                }
            }
            // Untuk endpoint edit/update lainnya
            elseif (!$request->isMethod('delete')) {
                if (in_array($floorOrder->status, ['saved', 'completed', 'cancelled'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Floor Order yang sudah disimpan, selesai atau dibatalkan tidak dapat diedit'
                    ], 403);
                }
            }
        }

        return $next($request);
    }
} 