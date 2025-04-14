<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function testRequest(Request $request)
    {
        Log::info('Request data received', [
            'requestData' => $request->all()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diterima',
            'data' => $request->all()
        ]);
    }
}
