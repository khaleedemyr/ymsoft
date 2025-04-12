@extends('layouts.master')

@section('title')
    {{ trans('translation.purchase_order.add') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }
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
        .supplier-select {
            width: 100%;
        }
        
        /* Style untuk kolom harga historis */
        .last-price-col,
        .lowest-price-col,
        .highest-price-col {
            text-align: right;
            font-size: 0.9rem;
        }
        
        .last-price-col .price-value,
        .lowest-price-col .price-value,
        .highest-price-col .price-value {
            font-weight: 500;
        }
        
        .last-price-col .date-value,
        .lowest-price-col .date-value,
        .highest-price-col .date-value {
            font-size: 0.75rem;
        }
        
        .btn-xs {
            padding: 0.125rem 0.25rem;
            font-size: 0.675rem;
            line-height: 1.3;
            margin-top: 2px;
        }
        
        /* Tambahkan sedikit warna untuk membedakan masing-masing kolom harga */
        .last-price-col .price-value {
            color: #0d6efd; /* Blue */
        }
        
        .lowest-price-col .price-value {
            color: #198754; /* Green */
        }
        
        .highest-price-col .price-value {
            color: #dc3545; /* Red */
        }
        
        /* Responsif untuk layar kecil */
        @media (max-width: 992px) {
            .table-responsive {
                overflow-x: auto;
            }
        }
        
        /* Style untuk mode selection */
        .mode-selection {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .mode-selection .btn-mode {
            width: 120px;
        }
        
        .mode-selection .btn-mode.active {
            background-color: #0d6efd;
            color: white;
        }
        
        /* Style untuk PR selection */
        .pr-selection {
            margin-bottom: 20px;
        }
        
        .selected-pr-list {
            margin-top: 10px;
        }
        
        .selected-pr-item {
            background-color: #e9ecef;
            padding: 8px 12px;
            margin: 5px 0;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        
        .stock-info {
            border-left: 3px solid #0ab39c;
            padding-left: 8px;
            margin: 4px 0;
        }
        
        .unit-info {
            font-size: 12px;
            color: #6c757d;
        }
    </style>
@endsection

@section('content')
<meta name="get-pr-items-url" content="{{ route('get.pr.items', ['id' => '__PR_ID__']) }}">

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('translation.purchase_order.add') }}</h1>
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
                            {{ trans('translation.purchasing.title') }}
                        @endslot
                        @slot('title')
                            {{ trans('translation.purchase_order.add') }}
                        @endslot
                    @endcomponent

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">{{ trans('translation.purchase_order.form.title') }}</h4>
                        </div>
                        <div class="card-body">
                            <!-- Mode Selection -->
                            <div class="mode-selection">
                                <label class="form-label d-block">Mode Pembuatan PO:</label>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-mode active" data-mode="auto">
                                        <i class="ri-magic-line me-1"></i> Auto
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-mode" data-mode="manual">
                                        <i class="ri-edit-line me-1"></i> Manual
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="ri-information-line"></i>
                                    Mode Auto akan otomatis mengelompokkan item berdasarkan supplier. 
                                    Mode Manual memungkinkan Anda memilih item mana yang akan digabung.
                                </small>
                            </div>

                            <form id="po-form" method="post" action="{{ route('purchasing.purchase-orders.store') }}">
                                @csrf
                                <input type="hidden" name="creation_mode" id="creation_mode" value="auto">
                                
                                <!-- Header Form -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="po_date" class="form-label">@lang('translation.purchase_order.form.po_date') <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="po_date" name="po_date" value="{{ date('Y-m-d') }}" required>
                                            <div class="invalid-feedback" id="po_date_error"></div>
                                        </div>
                                        
                                        <div class="mb-3 pr-selection">
                                            <label for="purchase_requisition_id" class="form-label">@lang('translation.purchase_requisition.title')</label>
                                            <select class="form-control select2" id="purchase_requisition_id" name="purchase_requisition_ids[]" multiple>
                                                @foreach($purchaseRequisitions as $pr)
                                                    <option value="{{ $pr->id }}">{{ $pr->pr_number }} ({{ $pr->warehouse->name }})</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="purchase_requisition_id_error"></div>
                                            
                                            <!-- Selected PR List -->
                                            <div class="selected-pr-list" id="selected-pr-list"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">@lang('translation.notes')</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="4"></textarea>
                                            <div class="invalid-feedback" id="notes_error"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview Section for Auto Mode -->
                                <div id="auto-mode-preview" class="mb-4" style="display: none;">
                                    <h5>Preview Pengelompokan PO</h5>
                                    <div id="po-grouping-preview" class="table-responsive">
                                        <!-- Will be filled dynamically -->
                                    </div>
                                </div>
                                
                                <!-- Items Section -->
                                <h5 class="mb-3">@lang('translation.item.title')</h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-items" id="po-items">
                                        <thead>
                                            <tr>
                                                <th width="3%">#</th>
                                                <th width="15%">@lang('translation.purchase_order.form.item_row.item_name')</th>
                                                <th width="8%">PR Ref</th>
                                                <th width="8%">@lang('translation.purchase_order.form.item_row.quantity')</th>
                                                <th width="6%">@lang('translation.unit.title')</th>
                                                <th width="12%">@lang('translation.item.table.last_price')</th>
                                                <th width="12%">@lang('translation.item.table.lowest_price')</th>
                                                <th width="12%">@lang('translation.item.table.highest_price')</th>
                                                <th width="15%">@lang('translation.supplier.title')</th>
                                                <th width="9%">@lang('translation.item.price')</th>
                                                <th width="9%">@lang('translation.total')</th>
                                                <th width="5%">@lang('translation.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="no-items-row">
                                                <td colspan="12" class="text-center">@lang('translation.no_items')</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="10" class="text-end">@lang('translation.total')</th>
                                                <th colspan="2" id="grand-total">0</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                <div class="mt-4 text-end">
                                    <a href="{{ route('purchasing.purchase-orders.index') }}" class="btn btn-light me-2">@lang('translation.purchase_order.form.buttons.cancel')</a>
                                    <button type="submit" class="btn btn-primary">@lang('translation.purchase_order.form.buttons.save')</button>
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

<!-- Template for item row -->
<template id="item-row-template">
    <tr class="item-row">
        <td class="row-number"></td>
        <td>
            <input type="hidden" name="items[index][item_id]" value="item_id">
            <input type="hidden" name="items[index][pr_item_id]" value="pr_item_id">
            <div class="item-name">Item Name</div>
            <small class="text-muted">SKU123</small>
        </td>
        <td class="pr-reference">
            <small class="text-muted">PR-REF</small>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm qty-input" name="items[index][quantity]" value="1" min="0.01" step="0.01" required>
        </td>
        <td>
            <input type="hidden" name="items[index][uom_id]" value="unit_id">
            <span class="unit-name">Unit</span>
        </td>
        <td class="last-price-col">
            <span class="price-value">-</span>
            <small class="d-block date-value text-muted">-</small>
            <button type="button" class="btn btn-xs btn-outline-primary use-last-price" style="display:none">
                <i class="ri-check-line"></i> @lang('translation.item.table.use_price')
            </button>
        </td>
        <td class="lowest-price-col">
            <span class="price-value">-</span>
            <small class="d-block date-value text-muted">-</small>
            <button type="button" class="btn btn-xs btn-outline-primary use-lowest-price" style="display:none">
                <i class="ri-check-line"></i> @lang('translation.item.table.use_price')
            </button>
        </td>
        <td class="highest-price-col">
            <span class="price-value">-</span>
            <small class="d-block date-value text-muted">-</small>
            <button type="button" class="btn btn-xs btn-outline-primary use-highest-price" style="display:none">
                <i class="ri-check-line"></i> @lang('translation.item.table.use_price')
            </button>
        </td>
        <td>
            <select class="form-control form-control-sm supplier-select" name="items[index][supplier_id]" required>
                <option value="">@lang('translation.select')</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm price-input" name="items[index][price]" value="0" min="0" required>
        </td>
        <td class="item-total">0</td>
        <td>
            <button type="button" class="btn btn-sm btn-danger remove-item">
                <i class="ri-delete-bin-line"></i>
            </button>
        </td>
    </tr>
</template>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2();
    
    // Global variables
    let itemCounter = 0;
    let selectedPRs = new Set();
    let itemsBySupplier = {};
    
    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Mode Selection Handler
    $('.btn-mode').on('click', function() {
        $('.btn-mode').removeClass('active');
        $(this).addClass('active');
        
        const mode = $(this).data('mode');
        $('#creation_mode').val(mode);
        
        if (mode === 'auto') {
            updateAutoModePreview();
        } else {
            $('#auto-mode-preview').hide();
        }
    });
    
    // Load PR items when PR is selected
    $('#purchase_requisition_id').on('change', function() {
        const selectedPRIds = $(this).val();
        
        // Clear items if no PR selected
        if (!selectedPRIds || selectedPRIds.length === 0) {
            clearItems();
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Load items for each selected PR
        const promises = selectedPRIds.map(prId => {
            let url = $('meta[name="get-pr-items-url"]').attr('content');
            url = url.replace('__PR_ID__', prId);
            
            return $.ajax({
                url: url,
                type: 'GET'
            });
        });
        
        Promise.all(promises)
            .then(responses => {
                // Clear existing items
                clearItems();
                
                // Process each PR's items
                responses.forEach(response => {
                    if (response.success && response.items && response.items.length > 0) {
                        response.items.forEach(item => {
                            addItemToTable(item, response.pr.pr_number);
                        });
                    }
                });
                
                updateRowNumbers();
                calculateGrandTotal();
                
                // Initialize Select2 for all supplier selects
                initializeSupplierSelects();
                
                // Update auto mode preview if in auto mode
                if ($('#creation_mode').val() === 'auto') {
                    updateAutoModePreview();
                }
                
                // Close loading
                Swal.close();
            })
            .catch(error => {
                console.error('Error fetching PR items:', error);
                Swal.fire('Error', 'Gagal mengambil data item PR', 'error');
            });
    });
    
    // Clear items function
    function clearItems() {
        $('#po-items tbody tr.item-row').remove();
        $('#no-items-row').show();
        itemCounter = 0;
        itemsBySupplier = {};
    }
    
    // Initialize supplier selects
    function initializeSupplierSelects() {
        setTimeout(() => {
            $('.supplier-select').select2({
                width: '100%',
                dropdownParent: $('#po-items')
            });
        }, 100);
    }
    
    // Update auto mode preview
    function updateAutoModePreview() {
        if ($('#creation_mode').val() !== 'auto') return;
        
        // Group items by supplier
        itemsBySupplier = {};
        $('.item-row').each(function() {
            const $row = $(this);
            const supplierId = $row.find('.supplier-select').val();
            const supplierName = $row.find('.supplier-select option:selected').text();
            
            if (supplierId) {
                if (!itemsBySupplier[supplierId]) {
                    itemsBySupplier[supplierId] = {
                        name: supplierName,
                        items: [],
                        total: 0
                    };
                }
                
                itemsBySupplier[supplierId].items.push({
                    name: $row.find('.item-name').text(),
                    sku: $row.find('small').text(),
                    prRef: $row.find('.pr-reference small').text(),
                    quantity: parseFloat($row.find('.qty-input').val()) || 0,
                    unit: $row.find('.unit-name').text(),
                    price: parseFloat($row.find('.price-input').val()) || 0,
                    total: parseFloat($row.data('total')) || 0
                });
                
                itemsBySupplier[supplierId].total += parseFloat($row.data('total')) || 0;
            }
        });
        
        // Generate preview HTML
        let previewHtml = '';
        for (const supplierId in itemsBySupplier) {
            const supplier = itemsBySupplier[supplierId];
            previewHtml += `
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">PO untuk ${supplier.name}</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>PR Ref</th>
                                        <th>Qty</th>
                                        <th>Unit</th>
                                        <th>Harga</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
            `;
            
            supplier.items.forEach(item => {
                previewHtml += `
                    <tr>
                        <td>${item.name}<br><small class="text-muted">${item.sku}</small></td>
                        <td>${item.prRef}</td>
                        <td>${item.quantity}</td>
                        <td>${item.unit}</td>
                        <td class="text-end">${formatNumber(item.price)}</td>
                        <td class="text-end">${formatNumber(item.total)}</td>
                    </tr>
                `;
            });
            
            previewHtml += `
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-end">Total:</th>
                                        <th class="text-end">${formatNumber(supplier.total)}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        }
        
        $('#po-grouping-preview').html(previewHtml);
        $('#auto-mode-preview').show();
    }
    
    // Add item to table
    function addItemToTable(item, prNumber) {
        console.log("Adding item to table:", item); // Debug log
        
        const $template = $('#item-row-template').html();
        const $newRow = $($template);
        
        // Update row with item data
        $newRow.find('input[name="items[index][item_id]"]').attr('name', `items[${itemCounter}][item_id]`).val(item.item_id);
        $newRow.find('input[name="items[index][pr_item_id]"]').attr('name', `items[${itemCounter}][pr_item_id]`).val(item.id);
        $newRow.find('.item-name').text(item.item ? item.item.name : 'Unknown Item');
        $newRow.find('small').text(item.item ? item.item.code : '');
        $newRow.find('.pr-reference small').text(prNumber);
        $newRow.find('.qty-input').attr('name', `items[${itemCounter}][quantity]`).val(item.quantity);
        $newRow.find('input[name="items[index][uom_id]"]').attr('name', `items[${itemCounter}][uom_id]`).val(item.uom_id);
        $newRow.find('.unit-name').text(item.unit ? item.unit.name : 'Unknown Unit');
        $newRow.find('.supplier-select').attr('name', `items[${itemCounter}][supplier_id]`);
        $newRow.find('.price-input').attr('name', `items[${itemCounter}][price]`);
        
        // Handle price history
        if (item.last_price) {
            $newRow.find('.last-price-col .price-value').text(formatNumber(item.last_price));
            if (item.last_price_date) {
                const lastPriceDate = new Date(item.last_price_date);
                $newRow.find('.last-price-col .date-value').text(formatDate(lastPriceDate));
                if (item.last_price_po_number) {
                    $newRow.find('.last-price-col .date-value').attr('title', `PO: ${item.last_price_po_number}`);
                }
            }
            $newRow.data('last-price', item.last_price);
            $newRow.find('.use-last-price').show();
        }
        
        if (item.lowest_price) {
            $newRow.find('.lowest-price-col .price-value').text(formatNumber(item.lowest_price));
            if (item.lowest_price_date) {
                const lowestPriceDate = new Date(item.lowest_price_date);
                $newRow.find('.lowest-price-col .date-value').text(formatDate(lowestPriceDate));
                if (item.lowest_price_po_number) {
                    $newRow.find('.lowest-price-col .date-value').attr('title', `PO: ${item.lowest_price_po_number}`);
                }
            }
            $newRow.data('lowest-price', item.lowest_price);
            $newRow.find('.use-lowest-price').show();
        }
        
        if (item.highest_price) {
            $newRow.find('.highest-price-col .price-value').text(formatNumber(item.highest_price));
            if (item.highest_price_date) {
                const highestPriceDate = new Date(item.highest_price_date);
                $newRow.find('.highest-price-col .date-value').text(formatDate(highestPriceDate));
                if (item.highest_price_po_number) {
                    $newRow.find('.highest-price-col .date-value').attr('title', `PO: ${item.highest_price_po_number}`);
                }
            }
            $newRow.data('highest-price', item.highest_price);
            $newRow.find('.use-highest-price').show();
        }
        
        // Calculate item total
        calculateItemTotal($newRow);
        
        // Hide no items row
        $('#no-items-row').hide();
        
        // Append new row to table
        $('#po-items tbody').append($newRow);
        
        // Increment counter
        itemCounter++;
    }
    
    // Remove item from table
    $(document).on('click', '.remove-item', function() {
        const $row = $(this).closest('tr');
        $row.remove();
        
        if ($('#po-items tbody tr.item-row').length === 0) {
            $('#no-items-row').show();
        }
        
        updateRowNumbers();
        calculateGrandTotal();
        
        // Update auto mode preview if in auto mode
        if ($('#creation_mode').val() === 'auto') {
            updateAutoModePreview();
        }
    });
    
    // Calculate item total when qty or price changes
    $(document).on('input', '.qty-input, .price-input', function() {
        const $row = $(this).closest('tr');
        calculateItemTotal($row);
        calculateGrandTotal();
        
        // Update auto mode preview if in auto mode
        if ($('#creation_mode').val() === 'auto') {
            updateAutoModePreview();
        }
    });
    
    // Update preview when supplier changes
    $(document).on('change', '.supplier-select', function() {
        if ($('#creation_mode').val() === 'auto') {
            updateAutoModePreview();
        }
    });
    
    // Calculate item total
    function calculateItemTotal($row) {
        const qty = parseFloat($row.find('.qty-input').val()) || 0;
        const price = parseFloat($row.find('.price-input').val()) || 0;
        const total = qty * price;
        $row.find('.item-total').text(formatNumber(total));
        $row.data('total', total);
    }
    
    // Calculate grand total
    function calculateGrandTotal() {
        let grandTotal = 0;
        $('.item-row').each(function() {
            grandTotal += parseFloat($(this).data('total') || 0);
        });
        $('#grand-total').text(formatNumber(grandTotal));
    }
    
    // Update row numbers
    function updateRowNumbers() {
        $('.item-row').each(function(index) {
            $(this).find('.row-number').text(index + 1);
        });
    }
    
    // Format number as currency
    function formatNumber(number) {
        return number.toLocaleString('id-ID');
    }
    
    // Format date
    function formatDate(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }
    
    // Use price buttons
    $(document).on('click', '.use-last-price', function() {
        const $row = $(this).closest('tr');
        const lastPrice = $row.data('last-price');
        if (lastPrice) {
            $row.find('.price-input').val(lastPrice).trigger('input');
            showToast("Harga terakhir digunakan");
        }
    });

    $(document).on('click', '.use-lowest-price', function() {
        const $row = $(this).closest('tr');
        const lowestPrice = $row.data('lowest-price');
        if (lowestPrice) {
            $row.find('.price-input').val(lowestPrice).trigger('input');
            showToast("Harga terendah digunakan");
        }
    });

    $(document).on('click', '.use-highest-price', function() {
        const $row = $(this).closest('tr');
        const highestPrice = $row.data('highest-price');
        if (highestPrice) {
            $row.find('.price-input').val(highestPrice).trigger('input');
            showToast("Harga tertinggi digunakan");
        }
    });

    // Show toast notification
    function showToast(message) {
        Swal.fire({
            icon: 'success',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }
    
    // Handle tab navigation for price inputs
    $(document).on('keydown', '.price-input', function(e) {
        if (e.key === 'Tab' && !e.shiftKey) {
            e.preventDefault();
            // Find next row's price input
            let nextRow = $(this).closest('tr').next('.item-row');
            if (nextRow.length) {
                nextRow.find('.price-input').focus();
            }
        }
    });
    
    // Form submission with loading indicator
    $('#po-form').on('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if ($('#po-items tbody tr.item-row').length === 0) {
            Swal.fire('Error', 'Mohon tambahkan minimal satu item', 'error');
            return;
        }
        
        // Check if all suppliers are selected
        let supplierMissing = false;
        $('.supplier-select').each(function() {
            if (!$(this).val()) {
                supplierMissing = true;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (supplierMissing) {
            Swal.fire('Error', 'Mohon pilih supplier untuk semua item', 'error');
            return;
        }
        
        // Show loading overlay
        $('#loadingOverlay').css('display', 'flex');
        
        // Clear error messages
        $('.invalid-feedback').empty();
        
        // Get creation mode
        const creationMode = $('#creation_mode').val();
        
        // Prepare form data
        let formData = $(this).serializeArray();
        
        // If auto mode, add grouping information
        if (creationMode === 'auto') {
            formData.push({
                name: 'item_grouping',
                value: JSON.stringify(itemsBySupplier)
            });
        }
        
        // Submit form
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function(response) {
                // Hide loading overlay
                $('#loadingOverlay').hide();
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        showCancelButton: false,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = response.redirect || '{{ route("purchasing.purchase-orders.index") }}';
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
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
                    Swal.fire('Error', 'Gagal menyimpan purchase order', 'error');
                }
            }
        });
    });

    // Ketika Good Receipt dipilih
    $('#good_receive_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        
        // Debug: cek nilai yang diambil
        console.log('Selected payment days:', selectedOption.data('payment-days'));
        
        const paymentDays = selectedOption.data('payment-days') || 0;
        $('#payment_days').val(paymentDays);
        
        // Update supplier dan warehouse yang sudah ada
        $('#supplier').val(selectedOption.data('supplier') || '');
        $('#warehouse').val(selectedOption.data('warehouse') || '');
        
        // Hitung due date
        calculateDueDate();
    });

    // Ketika tanggal invoice berubah
    $('#invoice_date').on('change', function() {
        calculateDueDate();
    });

    function calculateDueDate() {
        const invoiceDate = $('#invoice_date').val();
        const paymentDays = parseInt($('#payment_days').val()) || 0;
        
        console.log('Calculating due date:', {
            invoiceDate,
            paymentDays
        });
        
        if (invoiceDate && paymentDays >= 0) {
            const dueDate = new Date(invoiceDate);
            dueDate.setDate(dueDate.getDate() + paymentDays);
            const formattedDate = dueDate.toISOString().split('T')[0];
            console.log('Calculated due date:', formattedDate);
            $('#due_date').val(formattedDate);
        }
    }
});
</script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 