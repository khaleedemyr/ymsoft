@extends('layouts.master')

@section('title')
    {{ trans('translation.purchase_invoice.create') }}
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

        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('translation.purchase_invoice.create') }}</h1>
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
                            {{ trans('translation.purchase_invoice.create') }}
                        @endslot
                    @endcomponent

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">{{ trans('translation.purchase_invoice.form.title') }}</h4>
                        </div>
                        <div class="card-body">
                            <form id="createForm" action="{{ route('finance.purchase-invoices.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label required">Good Receive</label>
                                            <input type="hidden" name="supplier_id" id="supplier_id">
                                            <input type="hidden" name="warehouse_id" id="warehouse_id">
                                            <select class="form-select @error('good_receive_id') is-invalid @enderror" 
                                                    name="good_receive_id" 
                                                    id="good_receive_id"
                                                    required>
                                                <option value="">-- Pilih Good Receipt --</option>
                                                @foreach($goodReceives as $gr)
                                                    <option value="{{ $gr['id'] }}" 
                                                            data-payment-days="{{ $gr['payment_days'] }}"
                                                            data-supplier="{{ $gr['supplier_name'] }}"
                                                            data-supplier-id="{{ $gr['supplier_id'] }}"
                                                            data-warehouse="{{ $gr['warehouse_name'] }}"
                                                            data-warehouse-id="{{ $gr['warehouse_id'] ?? '' }}">
                                                        {{ $gr['display_name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('good_receive_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label required">Nomor Invoice Supplier</label>
                                            <input type="text" 
                                                   class="form-control @error('invoice_number') is-invalid @enderror" 
                                                   name="invoice_number" 
                                                   value="{{ old('invoice_number') }}"
                                                   placeholder="Masukkan nomor invoice dari supplier"
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
                                            <label class="form-label required">Tanggal Invoice</label>
                                            <input type="date" 
                                                   class="form-control @error('invoice_date') is-invalid @enderror" 
                                                   name="invoice_date" 
                                                   id="invoice_date"
                                                   value="{{ old('invoice_date', date('Y-m-d')) }}"
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
                                                   readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label required">Tanggal Jatuh Tempo</label>
                                            <input type="date" 
                                                   class="form-control @error('due_date') is-invalid @enderror" 
                                                   name="due_date" 
                                                   id="due_date"
                                                   value="{{ old('due_date') }}"
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
                                            <input type="text" class="form-control" id="supplier" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ trans('translation.purchase_invoice.form.warehouse') }}</label>
                                            <input type="text" class="form-control" id="warehouse" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ trans('translation.purchase_invoice.form.notes') }}</label>
                                            <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="items-table" class="table table-bordered table-striped">
                                                <thead class="bg-light">
                                                    <tr class="text-center">
                                                        <th width="25%">Item</th>
                                                        <th width="10%">Qty</th>
                                                        <th width="10%">Unit</th>
                                                        <th width="15%">Harga GR</th>
                                                        <th width="15%">Harga Invoice</th>
                                                        <th width="15%">Diskon</th>
                                                        <th width="10%">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <!-- Kolom kiri untuk subtotal dan diskon -->
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-4 col-form-label">Subtotal:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control text-end" id="subtotal_display" readonly>
                                                <input type="hidden" name="subtotal" id="subtotal" value="0">
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <label class="col-sm-4 col-form-label">Tipe Diskon:</label>
                                            <div class="col-sm-8">
                                                <select class="form-select" name="discount_type" id="discount_type">
                                                    <option value="">Tanpa Diskon</option>
                                                    <option value="percentage">Persentase (%)</option>
                                                    <option value="fixed">Nominal</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <label class="col-sm-4 col-form-label">Nilai Diskon:</label>
                                            <div class="col-sm-8">
                                                <input type="number" 
                                                       class="form-control" 
                                                       name="discount_value" 
                                                       id="discount_value" 
                                                       value="0" 
                                                       step="0.01">
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <label class="col-sm-4 col-form-label">Total Diskon:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control text-end" id="discount_amount_display" readonly>
                                                <input type="hidden" name="discount_amount" id="discount_amount" value="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <!-- Kolom kanan untuk PPN dan grand total -->
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-4 col-form-label">Tipe PPN:</label>
                                            <div class="col-sm-8">
                                                <select class="form-select" name="vat_type" id="vat_type">
                                                    <option value="">Tanpa PPN</option>
                                                    <option value="include">Include</option>
                                                    <option value="exclude">Exclude</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <label class="col-sm-4 col-form-label">PPN (%):</label>
                                            <div class="col-sm-8">
                                                <input type="number" 
                                                       class="form-control" 
                                                       name="vat_percentage" 
                                                       id="vat_percentage" 
                                                       value="0" 
                                                       step="0.01">
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <label class="col-sm-4 col-form-label">Total PPN:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control text-end" id="vat_amount_display" readonly>
                                                <input type="hidden" name="vat_amount" id="vat_amount" value="0">
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <label class="col-sm-4 col-form-label">Grand Total:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control text-end" id="grand_total_display" readonly>
                                                <input type="hidden" name="grand_total" id="grand_total" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-save-line align-bottom me-1"></i>
                                            {{ trans('translation.purchase_invoice.form.buttons.save') }}
                                        </button>
                                        <a href="{{ route('finance.purchase-invoices.index') }}" class="btn btn-secondary">
                                            <i class="ri-arrow-left-line align-bottom me-1"></i>
                                            {{ trans('translation.purchase_invoice.form.buttons.cancel') }}
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

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-2">{{ trans('translation.loading') }}</div>
        </div>
    </div>

    <!-- Debug: Tampilkan data payment days -->
    <div class="mt-3">
        <pre id="debug-info"></pre>
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

    $('#good_receive_id').on('change', function() {
        const selected = $(this).find('option:selected');
        
        // Show loading
        $('#loading-overlay').show();
        
        // Clear form jika tidak ada yang dipilih
        if (!$(this).val()) {
            clearForm();
            $('#loading-overlay').hide();
            return;
        }

        // Set supplier dan warehouse
        $('#supplier').val(selected.data('supplier') || '');
        $('#warehouse').val(selected.data('warehouse') || '');
        
        // Set supplier_id dan warehouse_id
        $('#supplier_id').val(selected.data('supplier-id'));
        $('#warehouse_id').val(selected.data('warehouse-id'));
        
        // Set payment days dan hitung due date
        const paymentDays = selected.data('payment-days') || 0;
        $('#payment_days').val(paymentDays);
        calculateDueDate();

        // Load items
        if ($(this).val()) {
            loadItems($(this).val());
        }
    });

    function loadItems(goodReceiveId) {
        $.ajax({
            url: `/finance/purchase-invoice/items/${goodReceiveId}`,
            type: 'GET',
            beforeSend: function() {
                $('#items-table tbody').html('<tr><td colspan="7" class="text-center">Loading...</td></tr>');
            },
            success: function(response) {
                if (response.success) {
                    let tbody = $('#items-table tbody');
                    tbody.empty();
                    
                    response.data.forEach(function(item, index) {
                        tbody.append(`
                            <tr>
                                <td>${item.item_name}
                                    <input type="hidden" name="items[${index}][id]" value="${item.id}">
                                </td>
                                <td class="text-end">
                                    <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                                    ${formatNumber(item.quantity)}
                                </td>
                                <td class="text-center">${item.unit}</td>
                                <td class="text-end">
                                    <input type="hidden" class="original-price" value="${item.price}">
                                    ${formatNumber(item.price)}
                                </td>
                                <td>
                                    <input type="number" 
                                           class="form-control form-control-sm text-end invoice-price" 
                                           name="items[${index}][invoice_price]" 
                                           value="${item.price}"
                                           step="0.01"
                                           onchange="calculateRowTotal(this)">
                                </td>
                                <td>
                                    <div class="row g-1">
                                        <div class="col-5">
                                            <select class="form-select form-select-sm item-discount-type" 
                                                    name="items[${index}][discount_type]"
                                                    onchange="calculateRowTotal(this)">
                                                <option value="percentage">%</option>
                                                <option value="fixed">Rp</option>
                                            </select>
                                        </div>
                                        <div class="col-7">
                                            <input type="number" 
                                                   class="form-control form-control-sm text-end item-discount" 
                                                   name="items[${index}][discount_value]" 
                                                   value="0"
                                                   min="0"
                                                   step="0.01"
                                                   onchange="calculateRowTotal(this)">
                                        </div>
                                    </div>
                                    <input type="hidden" 
                                           name="items[${index}][discount_amount]" 
                                           class="item-discount-amount" 
                                           value="0">
                                </td>
                                <td class="text-end">
                                    <span class="row-total">${formatNumber(item.subtotal)}</span>
                                    <input type="hidden" 
                                           name="items[${index}][subtotal]" 
                                           class="row-subtotal" 
                                           value="${item.subtotal}">
                                </td>
                            </tr>
                        `);
                    });

                    calculateAllTotals();
                }
            },
            error: function(xhr) {
                showError('Failed to load items: ' + (xhr.responseJSON?.message || 'Unknown error'));
            },
            complete: function() {
                $('#loading-overlay').hide();
            }
        });
    }

    function calculateDueDate() {
        const invoiceDate = $('#invoice_date').val();
        const paymentDays = parseInt($('#payment_days').val()) || 0;
        
        if (invoiceDate && paymentDays >= 0) {
            const dueDate = new Date(invoiceDate);
            dueDate.setDate(dueDate.getDate() + paymentDays);
            $('#due_date').val(dueDate.toISOString().split('T')[0]);
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
        
        // Pastikan semua hidden input terisi
        $('#subtotal').val(subtotal || 0);
        $('#discount_amount').val(discountAmount || 0);
        $('#vat_amount').val(vatAmount || 0);
        $('#grand_total').val(grandTotal || 0);
    }

    function clearForm() {
        $('#supplier').val('');
        $('#warehouse').val('');
        $('#supplier_id').val('');
        $('#warehouse_id').val('');
        $('#payment_days').val('');
        $('#due_date').val('');
        $('#items-table tbody').empty();
        $('#subtotal').val(0);
        $('#discount_amount').val(0);
        $('#vat_amount').val(0);
        $('#grand_total').val(0);
        $('#subtotal_display').val('0');
        $('#discount_amount_display').val('0');
        $('#vat_amount_display').val('0');
        $('#grand_total_display').val('0');
    }

    function formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    function showError(message) {
        // Tampilkan error menggunakan SweetAlert atau alert biasa
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
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

    // Handle form submission
    $('#createForm').on('submit', function(e) {
        e.preventDefault();
        
        // Show loading
        $('#loading-overlay').show();
        
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
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: message
                });
            },
            complete: function() {
                $('#loading-overlay').hide();
            }
        });
    });

    // Hitung due date saat halaman pertama kali dimuat
    calculateDueDate();
});
</script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 