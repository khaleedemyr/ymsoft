<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaintenanceUnitController extends Controller
{
    /**
     * Get units for dropdown
     */
    public function getUnits()
    {
        try {
            // Get active units
            $units = DB::table('units')
                ->where('status', 'active')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $units
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting units: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get units'
            ], 500);
        }
    }
}
