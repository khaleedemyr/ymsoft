@extends('layouts.master')

@section('title')
    {{ __('translation.purchase_invoice.title') }} - {{ $purchaseInvoice->invoice_number }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">{{ __('translation.purchase_invoice.title') }} - {{ $purchaseInvoice->invoice_number }}</h4>
                    <div class="page-title-right">
                        <a href="{{ route('finance.purchase-invoices.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line align-middle me-1"></i> 
                            {{ __('translation.purchase_invoice.button.back') }}
                        </a>
                        @if($purchaseInvoice->status === 'draft')
                            <a href="{{ route('finance.purchase-invoices.edit', $purchaseInvoice->id) }}" class="btn btn-primary">
                                <i class="ri-edit-line align-middle me-1"></i> 
                                {{ __('translation.purchase_invoice.button.edit') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%">{{ trans('translation.purchase_invoice.table.invoice_number') }}</td>
                                        <td width="2%">:</td>
                                        <td>{{ $purchaseInvoice->invoice_number }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('translation.purchase_invoice.table.invoice_date') }}</td>
                                        <td>:</td>
                                        <td>{{ date('d/m/Y', strtotime($purchaseInvoice->invoice_date)) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('translation.purchase_invoice.table.supplier') }}</td>
                                        <td>:</td>
                                        <td>{{ optional($purchaseInvoice->supplier)->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('translation.purchase_invoice.table.warehouse') }}</td>
                                        <td>:</td>
                                        <td>{{ optional($purchaseInvoice->warehouse)->name }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%">{{ trans('translation.purchase_invoice.table.po_number') }}</td>
                                        <td width="2%">:</td>
                                        <td>{{ optional($purchaseInvoice->goodReceive->purchaseOrder)->po_number }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('translation.purchase_invoice.table.gr_number') }}</td>
                                        <td>:</td>
                                        <td>{{ optional($purchaseInvoice->goodReceive)->gr_number }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('translation.purchase_invoice.table.status') }}</td>
                                        <td>:</td>
                                        <td>
                                            @switch($purchaseInvoice->status)
                                                @case('draft')
                                                    <span class="badge bg-warning text-dark">{{ trans('translation.purchase_invoice.status.draft') }}</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge bg-success">{{ trans('translation.purchase_invoice.status.approved') }}</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge bg-danger">{{ trans('translation.purchase_invoice.status.rejected') }}</span>
                                                    @break
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('translation.purchase_invoice.table.payment_status') }}</td>
                                        <td>:</td>
                                        <td>
                                            @switch($purchaseInvoice->payment_status)
                                                @case('unpaid')
                                                    <span class="badge bg-danger">{{ trans('translation.purchase_invoice.payment_status.unpaid') }}</span>
                                                    @break
                                                @case('partial')
                                                    <span class="badge bg-warning text-dark">{{ trans('translation.purchase_invoice.payment_status.partial') }}</span>
                                                    @break
                                                @case('paid')
                                                    <span class="badge bg-success">{{ trans('translation.purchase_invoice.payment_status.paid') }}</span>
                                                    @break
                                            @endswitch
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="table-responsive mt-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ trans('translation.purchase_invoice.form.items.item') }}</th>
                                        <th>{{ trans('translation.purchase_invoice.form.items.quantity') }}</th>
                                        <th>{{ trans('translation.purchase_invoice.form.items.unit') }}</th>
                                        <th>{{ trans('translation.purchase_invoice.form.items.invoice_price') }}</th>
                                        <th>{{ trans('translation.purchase_invoice.form.items.discount') }}</th>
                                        <th>{{ trans('translation.purchase_invoice.form.items.subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseInvoice->items as $item)
                                        <tr>
                                            <td>{{ optional($item->item)->name }}</td>
                                            <td class="text-end">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                                            <td>{{ optional($item->unit)->name }}</td>
                                            <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                @if($item->discount_type == 'percentage')
                                                    {{ number_format($item->discount_value, 2) }}% ({{ number_format($item->discount_amount, 0, ',', '.') }})
                                                @else
                                                    {{ number_format($item->discount_amount, 0, ',', '.') }}
                                                @endif
                                            </td>
                                            <td class="text-end">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end">{{ trans('translation.purchase_invoice.form.subtotal') }}</td>
                                        <td class="text-end">{{ number_format($purchaseInvoice->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                    @if($purchaseInvoice->discount_amount > 0)
                                    <tr>
                                        <td colspan="5" class="text-end">
                                            {{ trans('translation.purchase_invoice.form.discount.total') }}
                                            @if($purchaseInvoice->discount_type == 'percentage')
                                                ({{ number_format($purchaseInvoice->discount_value, 2) }}%)
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($purchaseInvoice->discount_amount, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    @if($purchaseInvoice->vat_amount > 0)
                                    <tr>
                                        <td colspan="5" class="text-end">{{ trans('translation.purchase_invoice.form.vat.total') }} ({{ $purchaseInvoice->vat_percentage }}%)</td>
                                        <td class="text-end">{{ number_format($purchaseInvoice->vat_amount, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>{{ trans('translation.purchase_invoice.form.grand_total') }}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($purchaseInvoice->grand_total, 0, ',', '.') }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($purchaseInvoice->notes)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>{{ trans('translation.purchase_invoice.form.notes') }}</h5>
                                    <p>{{ $purchaseInvoice->notes }}</p>
                                </div>
                            </div>
                        @endif

                        @if($purchaseInvoice->status === 'draft' && \App\Helpers\UserHelper::canApprovePurchaseInvoice())
                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="button" class="btn btn-success approve-invoice" data-id="{{ $purchaseInvoice->id }}">
                                        <i class="ri-check-line align-middle me-1"></i> 
                                        {{ __('translation.purchase_invoice.button.approve') }}
                                    </button>
                                    <button type="button" class="btn btn-danger reject-invoice" data-id="{{ $purchaseInvoice->id }}">
                                        <i class="ri-close-line align-middle me-1"></i> 
                                        {{ __('translation.purchase_invoice.button.reject') }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Handle approve button click
    $('.approve-invoice').click(function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: '{{ trans("translation.purchase_invoice.message.confirm_approve") }}',
            text: '{{ trans("translation.purchase_invoice.message.confirm_approve_message") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ trans("translation.yes") }}',
            cancelButtonText: '{{ trans("translation.no") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('finance.purchase-invoices.approve', ':id') }}".replace(':id', id),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: '{{ trans("translation.success") }}',
                                text: '{{ trans("translation.purchase_invoice.message.success_approve") }}',
                                icon: 'success',
                                confirmButtonText: '{{ trans("translation.ok") }}'
                            }).then((result) => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: '{{ trans("translation.error") }}',
                            text: xhr.responseJSON?.message || '{{ trans("translation.purchase_invoice.message.error_approve") }}',
                            icon: 'error',
                            confirmButtonText: '{{ trans("translation.ok") }}'
                        });
                    }
                });
            }
        });
    });

    // Handle reject button click
    $('.reject-invoice').click(function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: '{{ trans("translation.purchase_invoice.message.confirm_reject") }}',
            text: '{{ trans("translation.purchase_invoice.message.confirm_reject_message") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ trans("translation.yes") }}',
            cancelButtonText: '{{ trans("translation.no") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('finance.purchase-invoices.reject', ':id') }}".replace(':id', id),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: '{{ trans("translation.success") }}',
                                text: '{{ trans("translation.purchase_invoice.message.success_reject") }}',
                                icon: 'success',
                                confirmButtonText: '{{ trans("translation.ok") }}'
                            }).then((result) => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: '{{ trans("translation.error") }}',
                            text: xhr.responseJSON?.message || '{{ trans("translation.purchase_invoice.message.error_reject") }}',
                            icon: 'error',
                            confirmButtonText: '{{ trans("translation.ok") }}'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection 