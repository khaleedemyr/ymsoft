@extends('layouts.master')

@section('title')
    {{ trans('translation.good_receive.edit') }}
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
        
        /* Loading overlay styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .loading-content {
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
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
            
            .table-items input {
                font-size: 0.85rem;
                padding: 0.3rem;
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
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('translation.good_receive.edit') }}</h1>
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
                            {{ trans('translation.good_receive.edit') }}
                        @endslot
                    @endcomponent

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">{{ trans('translation.good_receive.form.title') }}</h4>
                        </div>
                        <div class="card-body">
                            <form id="gr-form" method="post" action="{{ route('good-receives.update', $goodReceive->id) }}">
                                @csrf
                                @method('PUT')
                                
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
                                            <input type="date" class="form-control" name="receive_date" value="{{ $goodReceive->receive_date->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{ trans('translation.good_receive.form.notes') }}</label>
                                            <textarea class="form-control" name="notes" rows="3">{{ $goodReceive->notes }}</textarea>
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
                                                <td data-label="{{ trans('translation.good_receive.form.quantity') }}">
                                                    <input type="number" class="form-control quantity-input" 
                                                           name="items[{{ $item->purchaseOrderItem->id }}][quantity]" 
                                                           value="{{ $item->quantity }}" 
                                                           min="0" 
                                                           max="{{ $item->purchaseOrderItem->quantity }}"
                                                           required>
                                                </td>
                                                <td data-label="{{ trans('translation.good_receive.form.unit') }}">{{ $item->purchaseOrderItem->unit->name }}</td>
                                                <td data-label="{{ trans('translation.item.price') }}">
                                                    <input type="number" class="form-control price-input" 
                                                           name="items[{{ $item->purchaseOrderItem->id }}][price]" 
                                                           value="{{ $item->price }}" 
                                                           min="0" 
                                                           required>
                                                </td>
                                                <td data-label="{{ trans('translation.total') }}" class="subtotal">{{ number_format($item->quantity * $item->price, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-end" data-label="{{ trans('translation.total') }}">{{ trans('translation.total') }}:</th>
                                                <th id="total-amount">{{ number_format($goodReceive->total_amount, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line"></i> {{ trans('translation.good_receive.form.save') }}
                                    </button>
                                    <a href="{{ route('good-receives.show', $goodReceive->id) }}" class="btn btn-secondary">
                                        <i class="ri-arrow-left-line"></i> {{ trans('translation.good_receive.form.cancel') }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-2">Menyimpan data...</div>
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

    // Calculate subtotal when quantity or price changes
    $(document).on('input', '.quantity-input, .price-input', function() {
        const row = $(this).closest('tr');
        const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
        const price = parseFloat(row.find('.price-input').val()) || 0;
        const subtotal = quantity * price;
        row.find('.subtotal').text(subtotal.toFixed(2));

        // Update total
        let total = 0;
        $('.subtotal').each(function() {
            total += parseFloat($(this).text()) || 0;
        });
        $('#total-amount').text(total.toFixed(2));
    });

    // Handle form submission
    $('#gr-form').submit(function(e) {
        e.preventDefault();
        
        // Show loading overlay
        $('#loadingOverlay').css('display', 'flex');
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function(response) {
                // Hide loading overlay
                $('#loadingOverlay').hide();
                
                if (response.success) {
                    Swal.fire({
                        title: '{{ trans('translation.good_receive.message.success_title') }}',
                        text: response.message,
                        icon: 'success'
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                } else {
                    Swal.fire({
                        title: '{{ trans('translation.good_receive.message.error_title') }}',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                // Hide loading overlay
                $('#loadingOverlay').hide();
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        if (key.includes('items.')) {
                            const parts = key.split('.');
                            const index = parts[1];
                            const field = parts[2];
                            
                            const $row = $(`input[name="items[${index}][${field}]"]`).closest('tr');
                            $row.find(`.${field}-input`).addClass('is-invalid');
                            
                            Swal.fire('Validation Error', value[0], 'error');
                        } else {
                            $(`#${key}_error`).text(value[0]);
                        }
                    });
                } else {
                    Swal.fire({
                        title: '{{ trans('translation.good_receive.message.error_title') }}',
                        text: '{{ trans('translation.good_receive.message.save_error') }}',
                        icon: 'error'
                    });
                }
            }
        });
    });
});
</script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 