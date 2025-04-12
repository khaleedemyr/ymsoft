@extends('layouts.master')

@section('title')
    {{ trans('translation.good_receive.detail') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .input-group-text {
            background-color: transparent;
        }
        table.table-items th, table.table-items td {
            padding: 0.5rem;
            vertical-align: middle;
        }
        .invalid-feedback {
            display: block;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }
            
            .form-group {
                margin-bottom: 1rem;
            }
            
            .form-group label {
                font-size: 0.9rem;
                margin-bottom: 0.3rem;
            }
            
            .form-control {
                font-size: 0.9rem;
            }
            
            .table-responsive {
                margin: 1rem -1rem;
                width: calc(100% + 2rem);
            }
            
            .table-items {
                font-size: 0.85rem;
            }
            
            .table-items th, 
            .table-items td {
                padding: 0.4rem;
            }
            
            .btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
            
            .btn i {
                font-size: 1rem;
            }
            
            .mt-3 {
                margin-top: 1rem !important;
            }
            
            .mt-4 {
                margin-top: 1.5rem !important;
            }

            .badge {
                font-size: 0.8rem;
                padding: 0.4rem 0.6rem;
            }
        }

        /* Mobile-first table styles */
        @media (max-width: 576px) {
            .table-items {
                display: block;
            }
            
            .table-items thead {
                display: none;
            }
            
            .table-items tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.25rem;
            }
            
            .table-items td {
                display: block;
                text-align: right;
                padding: 0.5rem;
            }
            
            .table-items td::before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 0.8rem;
                color: #6c757d;
            }
            
            .table-items tfoot {
                display: block;
                margin-top: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.25rem;
            }
            
            .table-items tfoot tr {
                display: block;
            }
            
            .table-items tfoot th {
                display: block;
                text-align: right;
                padding: 0.5rem;
            }
            
            .table-items tfoot th::before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 0.8rem;
                color: #6c757d;
            }

            /* Approval history table */
            .table-bordered {
                display: block;
            }
            
            .table-bordered thead {
                display: none;
            }
            
            .table-bordered tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.25rem;
            }
            
            .table-bordered td {
                display: block;
                text-align: right;
                padding: 0.5rem;
            }
            
            .table-bordered td::before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 0.8rem;
                color: #6c757d;
            }
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('translation.good_receive.detail') }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    @component('components.breadcrumb')
                        @slot('li_1')
                            {{ trans('translation.warehouse_management.title') }}
                        @endslot
                        @slot('title')
                            {{ trans('translation.good_receive.detail') }}
                        @endslot
                    @endcomponent

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">{{ trans('translation.good_receive.form.title') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('translation.good_receive.form.gr_number') }}</label>
                                        <input type="text" class="form-control" value="{{ $goodReceive->gr_number }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('translation.good_receive.form.po_number') }}</label>
                                        <input type="text" class="form-control" value="{{ $goodReceive->purchaseOrder->po_number }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('translation.good_receive.form.supplier') }}</label>
                                        <input type="text" class="form-control" value="{{ $goodReceive->purchaseOrder->supplier->name }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('translation.good_receive.form.receive_date') }}</label>
                                        <input type="text" class="form-control" value="{{ $goodReceive->receive_date->format('d/m/Y') }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('translation.good_receive.form.status') }}</label>
                                        <div>
                                            @if($goodReceive->status === 'draft')
                                                <span class="badge bg-warning">{{ trans('translation.good_receive.status.draft') }}</span>
                                            @elseif($goodReceive->status === 'approved')
                                                <span class="badge bg-success">{{ trans('translation.good_receive.status.approved') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ trans('translation.good_receive.status.rejected') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('translation.good_receive.form.total_amount') }}</label>
                                        <input type="text" class="form-control" value="{{ number_format($goodReceive->total_amount, 2) }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ trans('translation.good_receive.form.notes') }}</label>
                                        <textarea class="form-control" rows="3" readonly>{{ $goodReceive->notes }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive mt-4">
                                <table class="table table-bordered table-items" id="items-table">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('translation.good_receive.form.item_name') }}</th>
                                            <th>{{ trans('translation.good_receive.form.quantity') }}</th>
                                            <th>{{ trans('translation.good_receive.form.unit') }}</th>
                                            <th>{{ trans('translation.item.price') }}</th>
                                            <th>{{ trans('translation.total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($goodReceive->items as $item)
                                        <tr>
                                            <td data-label="{{ trans('translation.good_receive.form.item_name') }}">{{ $item->purchaseOrderItem->item->name }}</td>
                                            <td data-label="{{ trans('translation.good_receive.form.quantity') }}">{{ $item->quantity }}</td>
                                            <td data-label="{{ trans('translation.good_receive.form.unit') }}">{{ $item->purchaseOrderItem->unit->name }}</td>
                                            <td data-label="{{ trans('translation.item.price') }}">{{ number_format($item->price, 2) }}</td>
                                            <td data-label="{{ trans('translation.total') }}">{{ number_format($item->quantity * $item->price, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-end" data-label="{{ trans('translation.total') }}">{{ trans('translation.total') }}:</th>
                                            <th>{{ number_format($goodReceive->total_amount, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            @if($goodReceive->status === 'approved' || $goodReceive->status === 'rejected')
                            <div class="mt-4">
                                <h5>{{ trans('translation.good_receive.form.approval_history') }}</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ trans('translation.good_receive.form.action') }}</th>
                                                <th>{{ trans('translation.good_receive.form.action_by') }}</th>
                                                <th>{{ trans('translation.good_receive.form.action_date') }}</th>
                                                <th>{{ trans('translation.good_receive.form.notes') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td data-label="{{ trans('translation.good_receive.form.action') }}">
                                                    @if($goodReceive->status === 'approved')
                                                        <span class="badge bg-success">{{ trans('translation.good_receive.status.approved') }}</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ trans('translation.good_receive.status.rejected') }}</span>
                                                    @endif
                                                </td>
                                                <td data-label="{{ trans('translation.good_receive.form.action_by') }}">{{ $goodReceive->approver->name }}</td>
                                                <td data-label="{{ trans('translation.good_receive.form.action_date') }}">{{ $goodReceive->approved_at->format('d/m/Y H:i') }}</td>
                                                <td data-label="{{ trans('translation.good_receive.form.notes') }}">{{ $goodReceive->approval_notes }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            <div class="mt-3">
                                @if($goodReceive->status === 'draft')
                                    @can('edit good receive')
                                    <a href="{{ route('good-receives.edit', $goodReceive->id) }}" class="btn btn-primary">
                                        <i class="ri-edit-line"></i> {{ trans('translation.good_receive.form.edit') }}
                                    </a>
                                    @endcan
                                    @can('delete good receive')
                                    <button type="button" class="btn btn-danger" id="delete-btn">
                                        <i class="ri-delete-bin-line"></i> {{ trans('translation.good_receive.form.delete') }}
                                    </button>
                                    @endcan
                                @endif
                                @if($goodReceive->status === 'draft' && auth()->user()->can('approve good receive'))
                                    <button type="button" class="btn btn-success" id="approve-btn">
                                        <i class="ri-check-line"></i> {{ trans('translation.good_receive.form.approve') }}
                                    </button>
                                    <button type="button" class="btn btn-danger" id="reject-btn">
                                        <i class="ri-close-line"></i> {{ trans('translation.good_receive.form.reject') }}
                                    </button>
                                @endif
                                <a href="{{ route('good-receives.index') }}" class="btn btn-secondary">
                                    <i class="ri-arrow-left-line"></i> {{ trans('translation.good_receive.form.back') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
$(document).ready(function() {
    // Setup CSRF token untuk semua request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Handle delete button click
    $('#delete-btn').click(function() {
        Swal.fire({
            title: '{{ trans('translation.good_receive.message.confirm_delete') }}',
            text: '{{ trans('translation.good_receive.message.delete_warning') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ trans('translation.good_receive.form.delete') }}',
            cancelButtonText: '{{ trans('translation.good_receive.form.cancel') }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('good-receives.destroy', $goodReceive->id) }}',
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: '{{ trans('translation.good_receive.message.success_title') }}',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                window.location.href = '{{ route('good-receives.index') }}';
                            });
                        } else {
                            Swal.fire({
                                title: '{{ trans('translation.good_receive.message.error_title') }}',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: '{{ trans('translation.good_receive.message.error_title') }}',
                            text: '{{ trans('translation.good_receive.message.delete_error') }}',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    // Handle approve button click
    $('#approve-btn').click(function() {
        Swal.fire({
            title: '{{ trans('translation.good_receive.message.confirm_approve') }}',
            text: '{{ trans('translation.good_receive.message.approve_warning') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '{{ trans('translation.good_receive.form.approve') }}',
            cancelButtonText: '{{ trans('translation.good_receive.form.cancel') }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('good-receives.approve', $goodReceive->id) }}',
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: '{{ trans('translation.good_receive.message.success_title') }}',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: '{{ trans('translation.good_receive.message.error_title') }}',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: '{{ trans('translation.good_receive.message.error_title') }}',
                            text: '{{ trans('translation.good_receive.message.approve_error') }}',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    // Handle reject button click
    $('#reject-btn').click(function() {
        Swal.fire({
            title: '{{ trans('translation.good_receive.message.confirm_reject') }}',
            text: '{{ trans('translation.good_receive.message.reject_warning') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '{{ trans('translation.good_receive.form.reject') }}',
            cancelButtonText: '{{ trans('translation.good_receive.form.cancel') }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route('good-receives.reject', $goodReceive->id) }}',
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: '{{ trans('translation.good_receive.message.success_title') }}',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: '{{ trans('translation.good_receive.message.error_title') }}',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: '{{ trans('translation.good_receive.message.error_title') }}',
                            text: '{{ trans('translation.good_receive.message.reject_error') }}',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
});
</script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 