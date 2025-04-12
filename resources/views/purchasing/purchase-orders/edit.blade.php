@extends('layouts.master')

@section('title')
    {{ trans('translation.purchase_order.edit') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }
        .loading-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
        }
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('translation.purchase_order.edit') }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    @component('components.breadcrumb')
                        @slot('li_1')
                            {{ trans('translation.purchasing.title') }}
                        @endslot
                        @slot('title')
                            {{ trans('translation.purchase_order.edit') }}
                        @endslot
                    @endcomponent

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <form id="editPurchaseOrderForm" action="{{ route('purchasing.purchase-orders.update', $purchaseOrder->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ trans('translation.purchase_order.table.po_number') }}</label>
                                                    <input type="text" class="form-control" value="{{ $purchaseOrder->po_number }}" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">{{ trans('translation.purchase_order.table.po_date') }}</label>
                                                    <input type="date" class="form-control" name="po_date" value="{{ $purchaseOrder->po_date }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">{{ trans('translation.purchase_order.table.supplier') }}</label>
                                                    <select class="form-select" name="supplier_id" required>
                                                        <option value="">{{ trans('translation.select') }}</option>
                                                        @foreach($suppliers as $supplier)
                                                            <option value="{{ $supplier->id }}" {{ $purchaseOrder->supplier_id == $supplier->id ? 'selected' : '' }}>
                                                                {{ $supplier->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ trans('translation.purchase_order.table.pr_number') }}</label>
                                                    <input type="text" class="form-control" value="{{ optional($purchaseOrder->purchaseRequisition)->pr_number }}" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">{{ trans('translation.purchase_order.table.warehouse') }}</label>
                                                    <input type="text" class="form-control" value="{{ optional($purchaseOrder->purchaseRequisition)->warehouse->name }}" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">{{ trans('translation.purchase_order.table.department') }}</label>
                                                    <input type="text" class="form-control" value="{{ optional($purchaseOrder->purchaseRequisition)->department }}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>{{ trans('translation.purchase_order.table.item_sku') }}</th>
                                                        <th>{{ trans('translation.purchase_order.table.item_name') }}</th>
                                                        <th class="text-end">{{ trans('translation.purchase_order.table.quantity') }}</th>
                                                        <th>{{ trans('translation.purchase_order.table.uom') }}</th>
                                                        <th class="text-end">{{ trans('translation.purchase_order.table.price') }}</th>
                                                        <th class="text-end">{{ trans('translation.purchase_order.table.subtotal') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($purchaseOrder->items as $item)
                                                    <tr>
                                                        <td>
                                                            {{ optional($item->item)->sku }}
                                                            <input type="hidden" name="items[{{ $loop->index }}][purchase_order_item_id]" value="{{ $item->id }}">
                                                            <input type="hidden" name="items[{{ $loop->index }}][item_id]" value="{{ $item->item_id }}">
                                                        </td>
                                                        <td>{{ optional($item->item)->name }}</td>
                                                        <td class="text-end">
                                                            <input type="number" class="form-control text-end quantity" 
                                                                name="items[{{ $loop->index }}][quantity]" 
                                                                value="{{ $item->quantity }}" 
                                                                min="1" required>
                                                        </td>
                                                        <td>
                                                            <select class="form-select" name="items[{{ $loop->index }}][unit_id]" required>
                                                                @foreach($units as $unit)
                                                                    <option value="{{ $unit->id }}" {{ $item->unit_id == $unit->id ? 'selected' : '' }}>
                                                                        {{ $unit->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control text-end price" 
                                                                name="items[{{ $loop->index }}][price]" 
                                                                value="{{ $item->price }}" 
                                                                min="0" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control text-end subtotal" 
                                                                value="{{ number_format($item->total, 0, ',', '.') }}" 
                                                                readonly>
                                                            <input type="hidden" name="items[{{ $loop->index }}][total]" 
                                                                value="{{ $item->total }}">
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="5" class="text-end">
                                                            <strong>{{ trans('translation.purchase_order.table.total_amount') }}</strong>
                                                        </td>
                                                        <td class="text-end">
                                                            <input type="text" class="form-control text-end" id="totalAmount" 
                                                                value="{{ number_format($purchaseOrder->total, 0, ',', '.') }}" 
                                                                readonly>
                                                            <input type="hidden" name="total" id="totalAmountHidden" 
                                                                value="{{ $purchaseOrder->total }}">
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        <div class="text-end mt-3">
                                            <a href="{{ route('purchasing.purchase-orders.show', $purchaseOrder->id) }}" class="btn btn-light me-2">
                                                <i class="ri-arrow-left-line align-bottom me-1"></i>
                                                {{ trans('translation.purchase_order.button.cancel') }}
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-save-line align-bottom me-1"></i>
                                                {{ trans('translation.purchase_order.button.save') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="loading-content">
        <div class="spinner-border text-light mb-3" role="status"></div>
        <div>{{ trans('translation.purchase_order.message.saving') }}</div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editPurchaseOrderForm');
    const loadingOverlay = document.querySelector('.loading-overlay');
    
    // Calculate subtotal and total
    function calculateAmounts() {
        let total = 0;
        document.querySelectorAll('tbody tr').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const price = parseFloat(row.querySelector('.price').value) || 0;
            const subtotal = quantity * price;
            
            // Update subtotal display and hidden input
            row.querySelector('.subtotal').value = numberFormat(subtotal);
            row.querySelector('input[name$="[total]"]').value = subtotal;
            
            total += subtotal;
        });
        
        // Update total amount
        document.getElementById('totalAmount').value = numberFormat(total);
        document.getElementById('totalAmountHidden').value = total;
    }
    
    // Format number to Indonesian format
    function numberFormat(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }
    
    // Add event listeners for quantity and price changes
    document.querySelectorAll('.quantity, .price').forEach(input => {
        input.addEventListener('change', calculateAmounts);
        input.addEventListener('keyup', calculateAmounts);
    });
    
    // Handle tab key on price inputs
    document.querySelectorAll('.price').forEach((input, index, inputs) => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Tab' && !e.shiftKey && index < inputs.length - 1) {
                e.preventDefault();
                inputs[index + 1].focus();
            }
        });
    });
    
    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading overlay
        loadingOverlay.style.display = 'block';
        
        // Submit form
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Hide loading overlay
            loadingOverlay.style.display = 'none';
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '{{ trans('translation.purchase_order.message.success_title') }}',
                    text: data.message,
                    showConfirmButton: true
                }).then(() => {
                    window.location.href = data.redirect;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '{{ trans('translation.purchase_order.message.error_title') }}',
                    text: data.message
                });
            }
        })
        .catch(error => {
            // Hide loading overlay
            loadingOverlay.style.display = 'none';
            
            Swal.fire({
                icon: 'error',
                title: '{{ trans('translation.purchase_order.message.error_title') }}',
                text: '{{ trans('translation.purchase_order.message.save_error') }}'
            });
        });
    });
});
</script>
@endsection 