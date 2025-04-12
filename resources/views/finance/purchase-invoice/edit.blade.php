@extends('layouts.master')

@section('title')
    {{ trans('translation.purchase_invoice.edit') }}
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
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
    <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('translation.purchase_invoice.edit') }}</h1>
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
                            {{ trans('translation.finance.title') }}
                        @endslot
                        @slot('title')
                            {{ trans('translation.purchase_invoice.edit') }}
                        @endslot
                    @endcomponent

                <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">{{ trans('translation.purchase_invoice.form.title') }}</h4>
                        </div>
                    <div class="card-body">
                            <form id="editForm" action="{{ route('finance.purchase-invoices.update', $purchaseInvoice->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Hidden inputs untuk data yang tidak berubah -->
                            <input type="hidden" name="good_receive_id" value="{{ $purchaseInvoice->good_receive_id }}">
                            <input type="hidden" name="supplier_id" value="{{ $purchaseInvoice->supplier_id }}">
                            <input type="hidden" name="warehouse_id" value="{{ $purchaseInvoice->warehouse_id }}">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">{{ trans('translation.purchase_invoice.form.good_receive') }}</label>
                                        <input type="text" 
                                               class="form-control" 
                                               value="{{ $purchaseInvoice->goodReceive->gr_number }} - {{ optional($purchaseInvoice->goodReceive->purchaseOrder)->po_number }}"
                                               readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label required">{{ trans('translation.purchase_invoice.form.supplier_invoice_number') }}</label>
                                        <input type="text" 
                                               class="form-control @error('invoice_number') is-invalid @enderror" 
                                               name="invoice_number" 
                                               value="{{ old('invoice_number', $purchaseInvoice->invoice_number) }}"
                                               required>
                                        @error('invoice_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label required">{{ trans('translation.purchase_invoice.form.invoice_date') }}</label>
                                        <input type="date" 
                                               class="form-control @error('invoice_date') is-invalid @enderror" 
                                               name="invoice_date" 
                                               id="invoice_date"
                                               value="{{ old('invoice_date', $purchaseInvoice->invoice_date->format('Y-m-d')) }}"
                                               required>
                                        @error('invoice_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">{{ trans('translation.purchase_invoice.form.payment_days') }}</label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="payment_days"
                                               value="{{ optional($purchaseInvoice->goodReceive->purchaseOrder->supplier)->payment_terms }}"
                                               readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label required">{{ trans('translation.purchase_invoice.form.due_date') }}</label>
                                        <input type="date" 
                                               class="form-control @error('due_date') is-invalid @enderror" 
                                               name="due_date" 
                                               id="due_date"
                                               value="{{ old('due_date', optional($purchaseInvoice->due_date)->format('Y-m-d')) }}"
                                               required>
                                        @error('due_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">{{ trans('translation.purchase_invoice.form.supplier') }}</label>
                                        <input type="text" 
                                               class="form-control" 
                                               value="{{ optional($purchaseInvoice->supplier)->name }}"
                                               readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">{{ trans('translation.purchase_invoice.form.warehouse') }}</label>
                                        <input type="text" 
                                               class="form-control" 
                                               value="{{ optional($purchaseInvoice->warehouse)->name }}"
                                               readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">{{ trans('translation.purchase_invoice.form.notes') }}</label>
                                        <textarea class="form-control" 
                                                  name="notes" 
                                                  rows="3">{{ old('notes', $purchaseInvoice->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Items Table -->
                            <div class="table-responsive mt-4">
                                <table id="items-table" class="table table-bordered table-striped">
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
                                        @foreach($purchaseInvoice->items as $index => $item)
                                                    <tr>
                                                        <td>
                                                            {{ $item->item->name }}
                                                <input type="hidden" name="items[{{$index}}][id]" value="{{ $item->id }}">
                                            </td>
                                            <td class="text-end">
                                                <input type="hidden" name="items[{{$index}}][quantity]" value="{{ $item->quantity }}">
                                                {{ number_format($item->quantity, 0) }}
                                                        </td>
                                            <td>{{ $item->unit->name }}</td>
                                            <td>
                                                <input type="number" 
                                                       class="form-control form-control-sm text-end invoice-price" 
                                                       name="items[{{$index}}][invoice_price]" 
                                                       value="{{ $item->price }}"
                                                                required>
                                                        </td>
                                                        <td>
                                                <div class="row g-1">
                                                    <div class="col-5">
                                                        <select class="form-select form-select-sm item-discount-type" 
                                                                name="items[{{$index}}][discount_type]">
                                                            <option value="percentage" {{ $item->discount_type == 'percentage' ? 'selected' : '' }}>{{ trans('translation.purchase_invoice.form.discount.percentage') }}</option>
                                                            <option value="fixed" {{ $item->discount_type == 'fixed' ? 'selected' : '' }}>{{ trans('translation.purchase_invoice.form.discount.fixed') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-7">
                                                        <input type="number" 
                                                               class="form-control form-control-sm text-end item-discount" 
                                                               name="items[{{$index}}][discount_value]" 
                                                               value="{{ $item->discount_value }}"
                                                               min="0">
                                                    </div>
                                                </div>
                                                <input type="hidden" 
                                                       name="items[{{$index}}][discount_amount]" 
                                                       class="item-discount-amount" 
                                                       value="{{ $item->discount_amount }}">
                                                        </td>
                                            <td class="text-end">
                                                <span class="row-total">{{ number_format($item->subtotal, 0) }}</span>
                                                <input type="hidden" 
                                                       name="items[{{$index}}][subtotal]" 
                                                       class="row-subtotal" 
                                                       value="{{ $item->subtotal }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                            </div>

                            <!-- Setelah table items -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <!-- Kolom kiri untuk subtotal dan diskon -->
                                    <div class="form-group row mb-3">
                                        <label class="col-sm-4 col-form-label">{{ trans('translation.purchase_invoice.form.subtotal') }}:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control text-end" id="subtotal_display" value="{{ number_format($purchaseInvoice->subtotal, 0) }}" readonly>
                                            <input type="hidden" name="subtotal" id="subtotal" value="{{ $purchaseInvoice->subtotal }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-4 col-form-label">{{ trans('translation.purchase_invoice.form.discount.type') }}:</label>
                                        <div class="col-sm-8">
                                            <select class="form-select" name="discount_type" id="discount_type">
                                                <option value="">{{ trans('translation.purchase_invoice.form.discount.no_discount') }}</option>
                                                <option value="percentage" {{ $purchaseInvoice->discount_type == 'percentage' ? 'selected' : '' }}>{{ trans('translation.purchase_invoice.form.discount.percentage') }}</option>
                                                <option value="fixed" {{ $purchaseInvoice->discount_type == 'fixed' ? 'selected' : '' }}>{{ trans('translation.purchase_invoice.form.discount.fixed') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-4 col-form-label">{{ trans('translation.purchase_invoice.form.discount.value') }}:</label>
                                        <div class="col-sm-8">
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="discount_value" 
                                                   id="discount_value" 
                                                   value="{{ $purchaseInvoice->discount_value }}" 
                                                   step="0.01">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-4 col-form-label">{{ trans('translation.purchase_invoice.form.discount.total') }}:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control text-end" id="discount_amount_display" value="{{ number_format($purchaseInvoice->discount_amount, 0) }}" readonly>
                                            <input type="hidden" name="discount_amount" id="discount_amount" value="{{ $purchaseInvoice->discount_amount }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Kolom kanan untuk PPN dan grand total -->
                                    <div class="form-group row mb-3">
                                        <label class="col-sm-4 col-form-label">{{ trans('translation.purchase_invoice.form.vat.type') }}:</label>
                                        <div class="col-sm-8">
                                            <select class="form-select" name="vat_type" id="vat_type">
                                                <option value="">{{ trans('translation.purchase_invoice.form.vat.no_vat') }}</option>
                                                <option value="include" {{ $purchaseInvoice->vat_type == 'include' ? 'selected' : '' }}>{{ trans('translation.purchase_invoice.form.vat.include') }}</option>
                                                <option value="exclude" {{ $purchaseInvoice->vat_type == 'exclude' ? 'selected' : '' }}>{{ trans('translation.purchase_invoice.form.vat.exclude') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-4 col-form-label">{{ trans('translation.purchase_invoice.form.vat.percentage') }}:</label>
                                        <div class="col-sm-8">
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="vat_percentage" 
                                                   id="vat_percentage" 
                                                   value="{{ $purchaseInvoice->vat_percentage }}" 
                                                   step="0.01">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-4 col-form-label">{{ trans('translation.purchase_invoice.form.vat.total') }}:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control text-end" id="vat_amount_display" value="{{ number_format($purchaseInvoice->vat_amount, 0) }}" readonly>
                                            <input type="hidden" name="vat_amount" id="vat_amount" value="{{ $purchaseInvoice->vat_amount }}">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-sm-4 col-form-label">{{ trans('translation.purchase_invoice.form.grand_total') }}:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control text-end" id="grand_total_display" value="{{ number_format($purchaseInvoice->grand_total, 0) }}" readonly>
                                            <input type="hidden" name="grand_total" id="grand_total" value="{{ $purchaseInvoice->grand_total }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit button -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line align-bottom me-1"></i>
                                        {{ trans('translation.save') }}
                                    </button>
                                    <a href="{{ route('finance.purchase-invoices.show', $purchaseInvoice->id) }}" class="btn btn-secondary">
                                        <i class="ri-arrow-left-line align-bottom me-1"></i>
                                        {{ trans('translation.back') }}
                                    </a>
                                </div>
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

<!-- Loading overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="mt-2">{{ trans('translation.loading') }}</div>
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

    // Inisialisasi loading overlay
    const loadingOverlay = $('<div id="loading-overlay" style="display:none;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    $('body').append(loadingOverlay);

    function calculateDueDate() {
        const invoiceDate = $('#invoice_date').val();
        const paymentDays = parseInt($('#payment_days').val()) || 0;
        
        if (invoiceDate) {
            // Menggunakan moment.js untuk perhitungan tanggal yang lebih akurat
            const dueDate = moment(invoiceDate).add(paymentDays, 'days').format('YYYY-MM-DD');
            $('#due_date').val(dueDate);
        }
    }

    function calculateRowTotal(input) {
        const row = $(input).closest('tr');
        const quantity = parseFloat(row.find('input[name$="[quantity]"]').val()) || 0;
        const price = parseFloat(row.find('.invoice-price').val()) || 0;
        const discountType = row.find('.item-discount-type').val();
        const discountValue = parseFloat(row.find('.item-discount').val()) || 0;
        
        console.log('Calculate Row:', {
            quantity,
            price,
            discountType,
            discountValue
        });
        
        const subtotalBeforeDiscount = quantity * price;
        let discountAmount = 0;
        
        // Hitung diskon berdasarkan tipe
        if (discountType === 'percentage') {
            discountAmount = subtotalBeforeDiscount * (discountValue / 100);
            console.log('Discount Percentage:', {
                subtotalBeforeDiscount,
                discountValue,
                discountAmount
            });
        } else if (discountType === 'fixed') {
            discountAmount = discountValue; // Langsung gunakan nilai diskon sebagai nominal
            console.log('Discount Fixed:', {
                subtotalBeforeDiscount,
                discountValue,
                discountAmount
            });
        }
        
        const subtotal = subtotalBeforeDiscount - discountAmount;
        
        console.log('Final Values:', {
            subtotalBeforeDiscount,
            discountAmount,
            subtotal
        });
        
        // Update hidden fields dan tampilan
        row.find('.item-discount-amount').val(discountAmount || 0);
        row.find('.row-subtotal').val(subtotal || 0);
        row.find('.row-total').text(formatNumber(subtotal));
        
        calculateAllTotals();
    }

    function calculateAllTotals() {
        // Hitung subtotal dari semua items
        let subtotal = 0;
        $('.row-subtotal').each(function() {
            subtotal += parseFloat($(this).val()) || 0;
        });
        
        // Update subtotal
        $('#subtotal').val(subtotal || 0);
        $('#subtotal_display').val(formatNumber(subtotal));
        
        // Hitung diskon invoice
        const discountType = $('#discount_type').val();
        const discountValue = parseFloat($('#discount_value').val()) || 0;
        let discountAmount = 0;
        
        if (discountType === 'percentage') {
            discountAmount = subtotal * (discountValue / 100);
        } else if (discountType === 'fixed') {
            discountAmount = discountValue;
        }
        
        // Update diskon
        $('#discount_amount').val(discountAmount || 0);
        $('#discount_amount_display').val(formatNumber(discountAmount));
        
        // Hitung setelah diskon
        const afterDiscount = subtotal - discountAmount;
        
        // Hitung PPN
        const vatType = $('#vat_type').val();
        const vatPercentage = parseFloat($('#vat_percentage').val()) || 0;
        let vatAmount = 0;
        
        if (vatType === 'include') {
            vatAmount = afterDiscount * (vatPercentage / (100 + vatPercentage));
        } else if (vatType === 'exclude') {
            vatAmount = afterDiscount * (vatPercentage / 100);
        }
        
        // Update PPN
        $('#vat_amount').val(vatAmount || 0);
        $('#vat_amount_display').val(formatNumber(vatAmount));
        
        // Hitung grand total
        const grandTotal = afterDiscount + (vatType === 'exclude' ? vatAmount : 0);
        $('#grand_total').val(grandTotal || 0);
        $('#grand_total_display').val(formatNumber(grandTotal));
        
        console.log('All Totals:', {
            subtotal,
            discountAmount,
            afterDiscount,
            vatAmount,
            grandTotal
        });
    }

    function formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    function showError(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: '{{ trans("translation.error") }}',
                text: message
            });
        } else {
            alert(message);
        }
    }

    // Tambahkan event listeners
    $(document).on('change', '.invoice-price, .item-discount, .item-discount-type', function() {
        calculateRowTotal(this);
    });

    $('#discount_type, #discount_value').on('change input', function() {
        calculateAllTotals();
    });

    $('#vat_type, #vat_percentage').on('change input', function() {
        calculateAllTotals();
    });

    // Event untuk invoice date
    $('#invoice_date').on('change', function() {
        calculateDueDate();
    });

    // Event untuk payment days
    $('#payment_days').on('change input', function() {
        calculateDueDate();
    });

    // Event untuk supplier select
    $('#supplier_id').on('change', function() {
        const supplierId = $(this).val();
        if (supplierId) {
            // Ambil data supplier via AJAX
            $.get(`/api/suppliers/${supplierId}`, function(response) {
                $('#payment_days').val(response.payment_terms);
                calculateDueDate();
            });
        }
    });

    // Pastikan fungsi dijalankan setelah semua nilai dimuat
    setTimeout(function() {
        calculateDueDate();
        calculateAllTotals();
    }, 100);

            // Handle form submission
    $('#editForm').on('submit', function(e) {
                e.preventDefault();
        
        // Show loading overlay
        $('#loadingOverlay').show();
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                                text: response.message,
                        showCancelButton: false,
                        confirmButtonText: 'OK'
                            }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = response.redirect_url;
                        }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan';
                
                if (xhr.status === 422) {
                    let errorMessages = [];
                    const errors = xhr.responseJSON.errors;
                    
                    for (let field in errors) {
                        errorMessages.push(errors[field][0]);
                    }
                    
                    message = errorMessages.join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: message
                });
            },
            complete: function() {
                // Hide loading overlay
                $('#loadingOverlay').hide();
                    }
                });
            });
        });
    </script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 