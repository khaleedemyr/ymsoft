@extends('layouts.master')

@section('title')
    {{ __('translation.floor_order.create.title') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .search-box {
            width: 100%;
            max-width: 300px;
            margin-bottom: 1rem;
        }
        
        .qty-input {
            width: 80px;
            min-width: 80px;
            text-align: right;
            padding-right: 5px;
        }
        
        .qty-input:focus {
            background-color: #fff;
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        
        .category-row, .subcategory-row {
            cursor: pointer;
        }
        
        .category-row {
            background-color: #f8f9fa !important;
        }
        
        .category-row td {
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            color: #1f1f1f;
        }
        
        .subcategory-row {
            background-color: #fff !important;
        }
        
        .subcategory-row td {
            font-weight: 600 !important;
            color: #495057;
        }
        
        .item-row td {
            font-weight: normal;
            color: #495057;
        }
        
        .category-row td {
            padding: 12px 15px !important;
        }
        
        .subcategory-row td:first-child {
            padding-left: 25px !important;
        }
        
        .item-row td:first-child {
            padding-left: 50px !important;
        }
        
        .collapse-icon {
            display: inline-block;
            width: 20px;
            text-align: center;
            transition: transform 0.2s;
            margin-right: 5px;
        }
        
        .collapsed .collapse-icon {
            transform: rotate(-90deg);
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }
            
            .table {
                font-size: 0.875rem;
            }
            
            .table td, .table th {
                padding: 0.5rem;
            }
            
            .item-row td:first-child {
                padding-left: 25px;
            }
            
            .qty-input {
                width: 60px;
                min-width: 60px;
            }
            
            /* Stack the user info on mobile */
            .user-info-table {
                margin-bottom: 1rem;
            }
            
            /* Make search box full width on mobile */
            .search-box {
                max-width: 100%;
            }
            
            /* Adjust button sizes */
            .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
            
            .category-row td {
                font-size: 1rem !important;
                padding: 10px !important;
            }
            
            .subcategory-row td:first-child {
                padding-left: 20px !important;
            }
            
            .item-row td:first-child {
                padding-left: 40px !important;
            }
        }
        
        .filter-section {
            background-color: #fff;
            padding: 15px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }

        .search-box {
            position: relative;
            margin-bottom: 1rem;
        }

        .search-box .form-control {
            padding-right: 35px;
        }

        .search-box .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #74788d;
        }

        @media (max-width: 768px) {
            .filter-section {
                padding: 10px;
            }
            
            .search-box {
                margin-bottom: 0.5rem;
            }
        }

        /* Add hover effect to rows */
        .item-row:hover {
            background-color: #f8f9fa;
        }

        /* Make sure the table header stays visible */
        .table-responsive {
            max-height: calc(100vh - 400px);
        }

        .table thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('translation.floor_order.create.title') }}</h4>
                </div>
                <div class="card-body">
                    <form id="createForm" class="needs-validation" novalidate>
                        @csrf
                        <!-- User Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-borderless mb-0 user-info-table">
                                    <tr>
                                        <th width="35%">{{ __('translation.floor_order.create.user_info.name') }}</th>
                                        <td>: {{ auth()->user()->nama_lengkap }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('translation.floor_order.create.user_info.position') }}</th>
                                        <td>: {{ auth()->user()->jabatan->nama_jabatan ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless mb-0 user-info-table">
                                    <tr>
                                        <th width="35%">{{ __('translation.floor_order.create.user_info.division') }}</th>
                                        <td>: {{ auth()->user()->divisi->nama_divisi ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('translation.floor_order.create.user_info.outlet') }}</th>
                                        <td>: {{ auth()->user()->outlet->nama_outlet ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('translation.floor_order.create.user_info.date') }}</th>
                                        <td>: {{ now()->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Tambahkan setelah informasi user dan sebelum tabel items -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="arrival_date">{{ __('translation.floor_order.create.arrival_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="arrival_date" 
                                           name="arrival_date" 
                                           required 
                                           min="{{ date('Y-m-d') }}"
                                           value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes">{{ __('translation.floor_order.create.notes') }}</label>
                                    <textarea class="form-control" 
                                              id="notes" 
                                              name="notes" 
                                              rows="2" 
                                              placeholder="{{ __('translation.floor_order.create.notes_placeholder') }}"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Warehouse Selection & Search -->
                        <div class="filter-section">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="warehouse_id">{{ __('translation.floor_order.create.warehouse') }} <span class="text-danger">*</span></label>
                                    <select class="form-select" id="warehouse_id" name="warehouse_id" required>
                                        <option value="">{{ __('translation.floor_order.create.select_warehouse') }}</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" data-code="{{ $warehouse->code }}">
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('translation.floor_order.create.search_item') }}</label>
                                    <div class="search-box">
                                        <input type="text" 
                                               class="form-control" 
                                               id="searchItem" 
                                               placeholder="{{ __('translation.floor_order.create.search_placeholder') }}">
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('translation.floor_order.create.table.item_name') }}</th>
                                        <th>{{ __('translation.floor_order.create.table.sku') }}</th>
                                        <th>{{ __('translation.floor_order.create.table.qty') }}</th>
                                        <th>{{ __('translation.floor_order.create.table.unit') }}</th>
                                        <th>{{ __('translation.floor_order.create.table.price') }}</th>
                                        <th>{{ __('translation.floor_order.create.table.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4" class="text-center">Silakan pilih gudang terlebih dahulu</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Tambahkan di bawah table -->
                        <div id="autoSaveStatus" style="position: fixed; bottom: 20px; right: 20px; z-index: 1050; display: none;"></div>

                        <!-- Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('floor-orders.index') }}" class="btn btn-light">{{ __('translation.floor_order.create.buttons.cancel') }}</a>
                                    <button type="submit" class="btn btn-primary" id="btn-save">{{ __('translation.floor_order.create.buttons.save') }}</button>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="id_outlet" id="id_outlet" value="{{ auth()->user()->id_outlet }}">
                        <input type="hidden" id="current_draft_id" name="current_draft_id" value="{{ $draftId ?? '' }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            let floorOrderId = null;
            let isDraft = false;
            let isSubmitting = false;
            let draftId = null;

            // Set default arrival date ke hari ini
            const today = new Date().toISOString().split('T')[0];
            $('#arrival_date').val(today);

            // Tambahkan ini di bagian awal script
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle qty input change
            $(document).on('input', '.qty-input', function() {
                const itemId = $(this).attr('name').match(/\[(\d+)\]/)[1];
                const qty = parseFloat($(this).val()) || 0;
                
                if (!isDraft) {
                    createDraft();
                } else {
                    updateDraft();
                }
            });

            function getFormData() {
                let items = {};
                $('.qty-input').each(function() {
                    const itemId = $(this).attr('name').match(/\[(\d+)\]/)[1];
                    const qty = parseFloat($(this).val()) || 0;
                    const price = parseFloat($(this).data('price')) || 0;
                    const total = qty * price;

                    if (qty > 0) {
                        items[itemId] = {
                            qty: qty,
                            price: price,
                            total: total
                        };
                    }
                });

                return {
                    warehouse_id: $('#warehouse_id').val(),
                    arrival_date: $('#arrival_date').val(),
                    notes: $('#notes').val(),
                    items: items
                };
            }

            function createDraft() {
                let formData = getFormData();

                $.ajax({
                    url: "{{ route('floor-orders.store-draft') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            floorOrderId = response.data.id;
                            isDraft = true;
                            showAutoSaveIndicator('Draft tersimpan');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Gagal menyimpan draft';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage += ': ' + xhr.responseJSON.message;
                        }
                        showAutoSaveIndicator(errorMessage, 'error');
                        console.error('Error creating draft:', xhr);
                    }
                });
            }

            function updateDraft() {
                if (!floorOrderId) return;

                let formData = getFormData();

                $.ajax({
                    url: `/floor-orders/${floorOrderId}/update-draft`,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            showAutoSaveIndicator('Draft diperbarui');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Gagal memperbarui draft';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage += ': ' + xhr.responseJSON.message;
                        }
                        showAutoSaveIndicator(errorMessage, 'error');
                        console.error('Error updating draft:', xhr);
                    }
                });
            }

            // Fungsi untuk menampilkan status auto-save
            function showAutoSaveIndicator(message, type = 'success') {
                const statusDiv = $('#autoSaveStatus');
                statusDiv.html(`
                    <div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show py-2" role="alert">
                        <small>${message}</small>
                    </div>
                `).fadeIn();

                setTimeout(() => {
                    statusDiv.fadeOut();
                }, 2000);
            }

            // Handle form submit final
            $('#createForm').submit(function(e) {
                e.preventDefault();
                
                let totalItems = 0;
                $('.qty-input').each(function() {
                    const qty = parseFloat($(this).val()) || 0;
                    if (qty > 0) totalItems++;
                });

                if (totalItems === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Silakan masukkan quantity minimal 1 item'
                    });
                    return;
                }

                finalizeDraft();
            });

            // Fungsi untuk finalisasi draft
            function finalizeDraft() {
                if (isSubmitting) return;

                const items = getItemsData();
                const itemCount = Object.keys(items).length;
                
                if (itemCount === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Minimal harus ada 1 item yang diisi'
                    });
                    return;
                }

                // Pastikan draftId ada
                if (!draftId) {
                    // Coba ambil dari form data terakhir
                    const formData = getFormData();
                    formData._token = $('meta[name="csrf-token"]').attr('content');

                    // Tampilkan loading
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Simpan draft dulu
                    $.ajax({
                        url: '/floor-orders/draft',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                draftId = response.draft_id;
                                doFinalize(draftId);
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal menyimpan draft'
                            });
                        }
                    });
                } else {
                    doFinalize(draftId);
                }
            }

            function doFinalize(currentDraftId) {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Anda akan menyimpan ${Object.keys(getItemsData()).length} item. Lanjutkan?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed && !isSubmitting) {
                        isSubmitting = true;
                        
                        // Tampilkan loading setelah konfirmasi
                        Swal.fire({
                            title: 'Menyimpan...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: `/floor-orders/draft/${currentDraftId}/finalize`,
                            type: 'PUT',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: 'Floor Order berhasil disimpan',
                                        allowOutsideClick: false
                                    }).then(() => {
                                        window.location.href = response.redirect || '/floor-orders';
                                    });
                                }
                            },
                            error: function(xhr) {
                                isSubmitting = false;
                                console.error('Error response:', xhr.responseText);
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal menyimpan Floor Order'
                                });
                            }
                        });
                    }
                });
            }

            function getItemsData() {
                let items = {};
                $('.qty-input').each(function() {
                    const itemId = $(this).attr('name').match(/\[(\d+)\]/)[1];
                    const qty = parseFloat($(this).val()) || 0;
                    const price = parseFloat($(this).data('price')) || 0;
                    const total = qty * price;

                    if (qty > 0) {
                        items[itemId] = {
                            qty: qty,
                            price: price,
                            total: total
                        };
                    }
                });
                return items;
            }

            $('#warehouse_id').change(function() {
                var warehouseId = $(this).val();
                var warehouseCode = $(this).find('option:selected').data('code');
                
                if (!warehouseId) {
                    $('#itemsTable tbody').html('<tr><td colspan="4" class="text-center">Silakan pilih gudang terlebih dahulu</td></tr>');
                    return;
                }

                $('#itemsTable tbody').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');

                $.ajax({
                    url: `/floor-orders/items/${warehouseCode}`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            let html = '';
                            
                            Object.keys(response.data).forEach(function(category) {
                                // Category row
                                html += `
                                    <tr class="category-row" data-category="${category}">
                                        <td colspan="4">
                                            <i class="ri-arrow-down-s-line collapse-icon"></i>
                                            ${category}
                                        </td>
                                    </tr>
                                `;
                                
                                Object.keys(response.data[category]).forEach(function(subcategory) {
                                    // Subcategory row
                                    html += `
                                        <tr class="subcategory-row" data-category="${category}" data-subcategory="${subcategory}">
                                            <td colspan="4">
                                                <i class="ri-arrow-down-s-line collapse-icon"></i>
                                                ${subcategory}
                                            </td>
                                        </tr>
                                    `;
                                    
                                    // Items
                                    response.data[category][subcategory].forEach(function(item) {
                                        html += generateItemRow(item, category, subcategory);
                                    });
                                });
                            });
                            
                            if (html === '') {
                                html = '<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>';
                            }
                            
                            $('#itemsTable tbody').html(html);
                        }
                    },
                    error: function(xhr) {
                        $('#itemsTable tbody').html('<tr><td colspan="4" class="text-center text-danger">Gagal memuat data</td></tr>');
                    }
                });
            });

            function generateItemRow(item, category, subcategory) {
                return `
                    <tr class="item-row" data-category="${category}" data-subcategory="${subcategory}">
                        <td>${item.name}</td>
                        <td>${item.sku}</td>
                        <td>
                            <input type="number" 
                                class="form-control form-control-sm qty-input" 
                                name="items[${item.id}][qty]" 
                                data-price="${item.price}"
                                min="0" 
                                value="0">
                        </td>
                        <td>${item.medium_unit}</td>
                        <td class="text-end">
                            Rp ${numberFormat(item.price)}
                            <small class="d-block text-muted">
                                (${item.medium_conversion_qty} ${item.medium_unit})
                            </small>
                        </td>
                        <td class="text-end line-total">Rp 0</td>
                    </tr>
                `;
            }

            function handleQtyChange(input) {
                const qty = parseFloat(input.value) || 0;
                const price = parseFloat(input.dataset.price) || 0;
                const total = qty * price;
                
                // Update line total
                const row = input.closest('tr');
                row.querySelector('.line-total').textContent = 'Rp ' + numberFormat(total);
                
                // Update grand total
                updateGrandTotal();
                
                // Panggil fungsi autosave yang sudah ada (jika ada)
                if (typeof autoSaveDraft === 'function') {
                    autoSaveDraft();
                }
            }

            function updateGrandTotal() {
                let grandTotal = 0;
                document.querySelectorAll('.qty-input').forEach(input => {
                    const qty = parseFloat(input.value) || 0;
                    const price = parseFloat(input.dataset.price) || 0;
                    grandTotal += qty * price;
                });
                
                // Tambahkan row total jika belum ada
                let totalRow = document.querySelector('.grand-total-row');
                if (!totalRow) {
                    const tbody = document.querySelector('#itemsTable tbody');
                    totalRow = document.createElement('tr');
                    totalRow.className = 'grand-total-row table-light fw-bold';
                    totalRow.innerHTML = `
                        <td colspan="5" class="text-end">Total:</td>
                        <td class="text-end grand-total">Rp 0</td>
                    `;
                    tbody.appendChild(totalRow);
                }
                
                // Update nilai total
                totalRow.querySelector('.grand-total').textContent = 'Rp ' + numberFormat(grandTotal);
            }

            function numberFormat(number) {
                return Math.ceil(number / 100) * 100
                    .toString()
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Handle category collapse/expand
            $(document).on('click', '.category-row', function() {
                const category = $(this).data('category');
                const isCollapsed = $(this).hasClass('collapsed');
                
                // Toggle collapse icon
                $(this).toggleClass('collapsed');
                
                // Toggle visibility of subcategories and items
                $(`tr[data-category="${category}"]`).not('.category-row').toggle(!isCollapsed);
            });

            // Handle subcategory collapse/expand
            $(document).on('click', '.subcategory-row', function(e) {
                e.stopPropagation();
                const category = $(this).data('category');
                const subcategory = $(this).data('subcategory');
                const isCollapsed = $(this).hasClass('collapsed');
                
                // Toggle collapse icon
                $(this).toggleClass('collapsed');
                
                // Toggle visibility of items
                $(`tr.item-row[data-category="${category}"][data-subcategory="${subcategory}"]`).toggle(!isCollapsed);
            });

            // Improved search functionality
            $('#searchItem').on('keyup', function() {
                const searchValue = $(this).val().toLowerCase();
                
                if (searchValue === '') {
                    $('.category-row, .subcategory-row, .item-row').show();
                    $('.category-row, .subcategory-row').removeClass('collapsed');
                    $('#no-results').remove();
                    return;
                }

                $('.category-row, .subcategory-row, .item-row').hide();
                
                let hasResults = false;
                $('.item-row').each(function() {
                    const $item = $(this);
                    const itemName = $item.find('td:first').text().toLowerCase();
                    const itemSku = $item.find('td:eq(1)').text().toLowerCase();
                    
                    if (itemName.includes(searchValue) || itemSku.includes(searchValue)) {
                        hasResults = true;
                        $item.show();
                        
                        const category = $item.data('category');
                        const subcategory = $item.data('subcategory');
                        
                        $(`.category-row[data-category="${category}"]`).show().removeClass('collapsed');
                        $(`.subcategory-row[data-category="${category}"][data-subcategory="${subcategory}"]`)
                            .show().removeClass('collapsed');
                    }
                });

                if (!hasResults) {
                    $('#no-results').remove();
                    $('#itemsTable tbody').append(`
                        <tr id="no-results">
                            <td colspan="4" class="text-center">
                                Tidak ada item yang cocok dengan pencarian
                            </td>
                        </tr>
                    `);
                } else {
                    $('#no-results').remove();
                }
            });

            // Clear search when warehouse changes
            $('#warehouse_id').change(function() {
                $('#searchItem').val('');
            });

            // Tambahkan di bagian script yang sudah ada
            function hitungLineTotal(input) {
                const qty = parseFloat(input.value) || 0;
                const price = parseFloat(input.getAttribute('data-price')) || 0;
                const total = qty * price;
                
                // Cari cell line total dalam baris yang sama
                const row = input.closest('tr');
                const lineTotalCell = row.querySelector('td:last-child');
                if (lineTotalCell) {
                    lineTotalCell.textContent = 'Rp ' + numberFormat(total);
                }
            }

            // Tambahkan event listener pada input qty yang sudah ada
            $(document).on('change keyup', '.qty-input', function() {
                hitungLineTotal(this);
                autoSaveDraft();
            });

            // Perbaikan fungsi autoSaveDraft
            function autoSaveDraft() {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(function() {
                    const formData = getFormData();
                    formData._token = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        url: draftId ? `/floor-orders/draft/${draftId}` : '/floor-orders/draft',
                        type: draftId ? 'PUT' : 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                if (!draftId && response.draft_id) {
                                    draftId = response.draft_id;
                                    console.log('Draft ID saved:', draftId); // Untuk debugging
                                }
                                showAutoSaveStatus('Tersimpan');
                            }
                        },
                        error: function() {
                            showAutoSaveStatus('Gagal menyimpan', true);
                        }
                    });
                }, 1000);
            }

            // Tambahkan fungsi untuk clear draftId saat form selesai atau dibatalkan
            function clearDraftId() {
                localStorage.removeItem('currentDraftId');
                draftId = null;
            }

            // Panggil clearDraftId saat form di-submit atau dibatalkan
            $('#createForm').on('submit', function() {
                clearDraftId();
            });

            // Jika ada tombol batal, tambahkan juga
            $('#btn-cancel').on('click', function() {
                clearDraftId();
            });

            // Di bagian document ready, pastikan draftId diset jika ada
            draftId = localStorage.getItem('currentDraftId');
            console.log('Initial draft ID:', draftId);

            // Tambahkan logging untuk debugging
            console.log('Initial draftId:', draftId);
            
            $('#btn-save').on('click', function(e) {
                e.preventDefault();
                console.log('Save clicked, current draftId:', draftId);
                finalizeDraft();
            });
        });
    </script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
