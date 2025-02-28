<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Traits\LogActivity;

class CustomerController extends Controller
{
    use LogActivity;

    public function index()
    {
        $customers = Customer::all();
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:customers|max:50',
            'name' => 'required|max:100',
            'type' => 'required|in:branch,customer',
        ]);

        $customer = Customer::create($request->all());

        // Log activity
        $this->logActivity(
            'CREATE',
            'customers',
            'Membuat customer baru: ' . $customer->name,
            null,
            $customer->toArray()
        );

        return response()->json(['success' => true, 'message' => trans('translation.customer.success_add')]);
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'code' => 'required|max:50|unique:customers,code,' . $customer->id,
            'name' => 'required|max:100',
            'type' => 'required|in:branch,customer',
        ]);

        $oldData = $customer->toArray();
        $customer->update($request->all());

        // Log activity
        $this->logActivity(
            'UPDATE',
            'customers',
            'Mengupdate customer: ' . $customer->name,
            $oldData,
            $customer->toArray()
        );

        return response()->json(['success' => true, 'message' => trans('translation.customer.success_edit')]);
    }

    public function toggleStatus(Request $request, Customer $customer)
    {
        $newStatus = $request->status;
        $oldData = $customer->toArray();

        $customer->status = $newStatus;
        $customer->save();

        // Log activity
        $this->logActivity(
            'UPDATE',
            'customers',
            'Mengubah status customer: ' . $customer->name . ' menjadi ' . $newStatus,
            $oldData,
            $customer->toArray()
        );

        return response()->json(['success' => true, 'message' => trans('translation.customer.success_status_change')]);
    }
}
