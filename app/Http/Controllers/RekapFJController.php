<?php

namespace App\Http\Controllers;

use App\Exports\RekapFJExport;
use App\Models\Category;
use App\Models\Customer;
use App\Models\SalesHeader;
use App\Models\SalesDetail;
use App\Models\SubCategory;
use App\Traits\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RekapFJController extends Controller
{
    use LogActivity;

    public function index()
    {
        $this->logActivity(
            'rekap_fj',
            'VIEW',
            'Melihat halaman rekap FJ'
        );

        return view('reports.rekap-fj.index');
    }

    public function data(Request $request)
    {
        try {
            $query = SalesHeader::query()
                ->join('customers', 'sales_headers.customer_id', '=', 'customers.id')
                ->join('sales_details', 'sales_headers.id', '=', 'sales_details.sales_header_id')
                ->join('items', 'sales_details.item_id', '=', 'items.id')
                ->join('sub_categories', 'items.sub_category_id', '=', 'sub_categories.id')
                ->join('categories', 'sub_categories.category_id', '=', 'categories.id')
                ->select(
                    'customers.name as customer_name',
                    'customers.id as customer_id',
                    DB::raw('SUM(CASE WHEN categories.name = "Main Kitchen" THEN sales_details.amount ELSE 0 END) as main_kitchen_total'),
                    DB::raw('SUM(CASE 
                        WHEN categories.name != "Main Kitchen" 
                        AND sub_categories.name NOT IN ("Chemical", "Stationary", "Marketing") 
                        THEN sales_details.amount ELSE 0 END) as main_store_total'),
                    DB::raw('SUM(CASE WHEN sub_categories.name = "Chemical" THEN sales_details.amount ELSE 0 END) as chemical_total'),
                    DB::raw('SUM(CASE WHEN sub_categories.name = "Stationary" THEN sales_details.amount ELSE 0 END) as stationary_total'),
                    DB::raw('SUM(CASE WHEN sub_categories.name = "Marketing" THEN sales_details.amount ELSE 0 END) as marketing_total'),
                    DB::raw('SUM(sales_details.amount) as line_total')
                )
                ->whereBetween('sales_headers.sales_date', [
                    $request->start_date,
                    $request->end_date
                ]);

            // Filter pencarian
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('customers.name', 'like', "%{$search}%");
            }

            $data = $query->groupBy('customers.id', 'customers.name')
                         ->orderBy('customers.name')
                         ->get();

            $this->logActivity(
                'rekap_fj',
                'LOAD',
                'Memuat data rekap FJ'
            );

            return response()->json([
                'status' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in RekapFJ data: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'status' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $query = SalesHeader::query()
                ->join('customers', 'sales_headers.customer_id', '=', 'customers.id')
                ->join('sales_details', 'sales_headers.id', '=', 'sales_details.sales_header_id')
                ->join('items', 'sales_details.item_id', '=', 'items.id')
                ->join('sub_categories', 'items.sub_category_id', '=', 'sub_categories.id')
                ->join('categories', 'sub_categories.category_id', '=', 'categories.id')
                ->select(
                    'customers.name as customer_name',
                    DB::raw('SUM(CASE WHEN categories.name = "Main Kitchen" THEN sales_details.amount ELSE 0 END) as main_kitchen_total'),
                    DB::raw('SUM(CASE 
                        WHEN categories.name != "Main Kitchen" 
                        AND sub_categories.name NOT IN ("Chemical", "Stationary", "Marketing") 
                        THEN sales_details.amount ELSE 0 END) as main_store_total'),
                    DB::raw('SUM(CASE WHEN sub_categories.name = "Chemical" THEN sales_details.amount ELSE 0 END) as chemical_total'),
                    DB::raw('SUM(CASE WHEN sub_categories.name = "Stationary" THEN sales_details.amount ELSE 0 END) as stationary_total'),
                    DB::raw('SUM(CASE WHEN sub_categories.name = "Marketing" THEN sales_details.amount ELSE 0 END) as marketing_total'),
                    DB::raw('SUM(sales_details.amount) as line_total')
                )
                ->whereBetween('sales_headers.sales_date', [
                    $request->start_date,
                    $request->end_date
                ]);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('customers.name', 'like', "%{$search}%");
            }

            $data = $query->groupBy('customers.id', 'customers.name')
                         ->orderBy('customers.name')
                         ->get();

            $subCategories = ['Main Kitchen', 'Main Store', 'Chemical', 'Stationary', 'Marketing'];
            $user = auth()->user();

            $this->logActivity(
                'rekap_fj',
                'EXPORT',
                'Mengexport data rekap FJ'
            );

            return Excel::download(
                new RekapFJExport(
                    $data, 
                    $subCategories, 
                    $request->start_date, 
                    $request->end_date,
                    $user
                ),
                'Rekap_FJ_' . date('d-m-Y') . '.xlsx'
            );

        } catch (\Exception $e) {
            \Log::error('Error in RekapFJ export: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect()->back()->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }
} 