<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PackingList;

class PackingListController extends Controller
{
    public function index()
    {
        // Sementara kirim array kosong untuk testing
        $packingLists = [];
        return view('packing-lists.index', compact('packingLists'));
    }

    public function getDetails($id)
    {
        try {
            $packingList = PackingList::with(['warehouse', 'creator', 'items.item'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'html' => view('packing-lists.detail-modal', compact('packingList'))->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }
} 