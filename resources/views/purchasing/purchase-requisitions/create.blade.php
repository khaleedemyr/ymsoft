@extends('layouts.master')

@section('title')
    {{ trans('translation.purchase_requisition.create') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .item-row.is-valid {
            background-color: rgba(0, 255, 0, 0.05);
        }
        
        .item-row.is-invalid {
            background-color: rgba(255, 0, 0, 0.05);
        }

        /* Custom validity styling */
        .form-control:valid,
        .form-select:valid {
            border-color: #198754;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .form-control:invalid,
        .form-select:invalid {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        /* Styling untuk icon tooltip */
        .ri-information-line {
            font-size: 14px;
            cursor: help;
        }

        /* Optional: Hover effect untuk icon */
        .ri-information-line:hover {
            opacity: 0.8;
        }

        .stock-large, .stock-medium, .stock-small,
        .available-large, .available-medium, .available-small {
            flex: 1;
            text-align: center;
            padding: 0 5px;
            min-width: 60px;
        }
        
        .d-flex.justify-content-around small {
            flex: 1;
            text-align: center;
            font-size: 0.8em;
            color: #666;
        }

        .item-stock, .item-available {
            min-width: 200px;
        }

        .item-uom {
            width: 100%;
        }

        .d-flex.justify-content-around > div {
            flex: 1;
            padding: 0 5px;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Purchasing
        @endslot
        @slot('title')
            Buat Purchase Requisition
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Form Purchase Requisition</h4>
                </div>
                <div class="card-body">
                    <form id="prForm" class="needs-validation" novalidate>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control flatpickr" name="date" required>
                                <div class="invalid-feedback">
                                    Mohon isi tanggal
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Departemen <span class="text-danger">*</span></label>
                                <select class="form-control" name="warehouse_id" id="warehouse_id" required>
                                    <option value="">Pilih Departemen</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Mohon pilih departemen
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Catatan</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title">Detail Item</h5>
                                    <button type="button" class="btn btn-success btn-sm" id="addItem">
                                        <i class="ri-add-line"></i> Tambah Item
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th width="25%">Item</th>
                                                <th width="25%" class="text-center">
                                                    Stock
                                                    <div class="d-flex justify-content-around">
                                                        <small>Besar</small>
                                                        <small>Sedang</small>
                                                        <small>Kecil</small>
                                                    </div>
                                                </th>
                                                <th width="25%" class="text-center">
                                                    Available
                                                    <div class="d-flex justify-content-around">
                                                        <small>Besar</small>
                                                        <small>Sedang</small>
                                                        <small>Kecil</small>
                                                    </div>
                                                </th>
                                                <th width="10%">Qty</th>
                                                <th width="15%">Satuan</th>
                                                <th>Notes</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="item-row">
                                                <td>
                                                    <input type="text" class="form-control item-search" required>
                                                    <input type="hidden" name="items[][item_id]">
                                                </td>
                                                <td class="text-end item-stock">
                                                    <div class="d-flex justify-content-around">
                                                        <span class="stock-large">0</span>
                                                        <span class="stock-medium">0</span>
                                                        <span class="stock-small">0</span>
                                                    </div>
                                                </td>
                                                <td class="text-end item-available">
                                                    <div class="d-flex justify-content-around">
                                                        <span class="available-large">0</span>
                                                        <span class="available-medium">0</span>
                                                        <span class="available-small">0</span>
                                                    </div>
                                                </td>
                                                <td><input type="number" class="form-control text-end item-qty" name="items[][quantity]" required></td>
                                                <td>
                                                    <select class="form-control item-uom" name="items[][uom_id]" required>
                                                        <option value="">Pilih Satuan</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control item-notes" name="items[][notes]"></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete-row">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('purchasing.purchase-requisitions.index') }}" class="btn btn-secondary">
                                        Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Datalist untuk autocomplete -->
    <datalist id="itemsList"></datalist>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize flatpickr
            $('.flatpickr').flatpickr({
                dateFormat: "Y-m-d",
                defaultDate: new Date()
            });

            // Handle add item button
            $('#addItem').click(function() {
                let rowCount = $('.item-row').length;
                let newRow = $('.item-row:first').clone();
                
                // Clear values
                newRow.find('input').val('');
                newRow.find('select').val('');
                
                // Update names
                newRow.find('[name]').each(function() {
                    let name = $(this).attr('name');
                    $(this).attr('name', name.replace('[0]', '[' + rowCount + ']'));
                });
                
                $('#itemsTable tbody').append(newRow);
            });

            // Handle remove item button
            $(document).on('click', '.remove-item', function() {
                if ($('.item-row').length > 1) {
                    $(this).closest('tr').remove();
                }
            });

            // Fungsi untuk mengaktifkan autocomplete
            function activateAutocomplete(input) {
                let row = input.closest('tr');
                let warehouseId = $('#warehouse_id').val() || $('select[name="warehouse_id"]').val();

                // Set attribute list untuk datalist
                input.attr('list', 'itemsList');
            }

            // Event handler untuk pencarian item
            $(document).on('input', '.item-search', function() {
                let input = $(this);
                let row = input.closest('tr');
                let warehouseId = $('#warehouse_id').val() || $('select[name="warehouse_id"]').val();
                let searchTerm = input.val();

                // Debug
                console.log('Search initiated:', { searchTerm, warehouseId });

                if (searchTerm && !warehouseId) {
                    Swal.fire('Error', 'Silahkan pilih departemen terlebih dahulu', 'error');
                    input.val('');
                    return;
                }

                if (searchTerm.length > 0 && warehouseId) {
                    $.ajax({
                        url: "/items/search",
                        type: 'GET',
                        data: {
                            term: searchTerm,
                            warehouse_id: warehouseId
                        },
                        success: function(items) {
                            // Debug
                            console.log('Search results:', items);
                            
                            let datalist = $('#itemsList');
                            datalist.empty();
                            
                            items.forEach(function(item) {
                                datalist.append(`
                                    <option value="${item.name}"
                                        data-id="${item.id}"
                                        data-stock="${item.stock_on_hand}"
                                        data-available="${item.stock_available}">
                                        ${item.name} (${item.sku})
                                    </option>
                                `);
                            });
                        }
                    });
                }
            });

            // Event handler ketika item dipilih
            $(document).on('change', '.item-search', function() {
                let input = $(this);
                let row = input.closest('tr');
                let selectedOption = $(`#itemsList option[value="${input.val()}"]`);
                
                if (selectedOption.length) {
                    let itemId = selectedOption.data('id');
                    let stock = parseInt(selectedOption.data('stock')) || 0;
                    let available = parseInt(selectedOption.data('available')) || 0;
                    
                    // Ambil data unit
                    $.ajax({
                        url: `/items/${itemId}/units`,
                        type: 'GET',
                        success: function(units) {
                            console.log('Server response (units):', units);

                            // Update select satuan
                            let uomSelect = row.find('.item-uom');
                            uomSelect.empty();
                            uomSelect.append('<option value="">Pilih Satuan</option>');

                            // Default values untuk stock
                            let stockData = {
                                small: stock,
                                medium: 0,
                                large: 0
                            };

                            let availableData = {
                                small: available,
                                medium: 0,
                                large: 0
                            };

                            // Tambahkan unit-unit yang tersedia
                            units.forEach((unit, index) => {
                                if (unit.id && unit.name) {
                                    uomSelect.append(`
                                        <option value="${unit.id}" 
                                            data-type="${unit.is_largest ? 'large' : index === 1 ? 'medium' : 'small'}">
                                            ${unit.name}
                                        </option>
                                    `);
                                }
                            });

                            // Coba ambil data konversi
                            $.ajax({
                                url: `/items/${itemId}/conversions`,
                                type: 'GET',
                                success: function(conversions) {
                                    console.log('Conversion data:', conversions);
                                    if (conversions && !conversions.error) {
                                        if (conversions.medium > 0) {
                                            stockData.medium = Math.floor(stock / conversions.medium);
                                            availableData.medium = Math.floor(available / conversions.medium);
                                        }
                                        if (conversions.large > 0) {
                                            stockData.large = Math.floor(stock / conversions.large);
                                            availableData.large = Math.floor(available / conversions.large);
                                        }
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.log('Using default conversion values due to error:', error);
                                },
                                complete: function() {
                                    updateDisplay();
                                }
                            });

                            function updateDisplay() {
                                // Update tampilan stock
                                let stockHtml = `
                                    <div class="d-flex justify-content-around">
                                        <div class="text-center">
                                            <small>Besar</small><br>
                                            <span>${formatNumber(stockData.large)}</span>
                                        </div>
                                        <div class="text-center">
                                            <small>Sedang</small><br>
                                            <span>${formatNumber(stockData.medium)}</span>
                                        </div>
                                        <div class="text-center">
                                            <small>Kecil</small><br>
                                            <span>${formatNumber(stockData.small)}</span>
                                        </div>
                                    </div>
                                `;

                                let availableHtml = `
                                    <div class="d-flex justify-content-around">
                                        <div class="text-center">
                                            <small>Besar</small><br>
                                            <span>${formatNumber(availableData.large)}</span>
                                        </div>
                                        <div class="text-center">
                                            <small>Sedang</small><br>
                                            <span>${formatNumber(availableData.medium)}</span>
                                        </div>
                                        <div class="text-center">
                                            <small>Kecil</small><br>
                                            <span>${formatNumber(availableData.small)}</span>
                                        </div>
                                    </div>
                                `;

                                row.find('.item-stock').html(stockHtml);
                                row.find('.item-available').html(availableHtml);

                                // Pilih unit sedang secara otomatis
                                let mediumOption = uomSelect.find('option[data-type="medium"]');
                                if (mediumOption.length) {
                                    mediumOption.prop('selected', true);
                                }
                            }

                            // Update display langsung dengan nilai default
                            updateDisplay();
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching units:', error);
                        }
                    });

                    row.find('input[name="items[][item_id]"]').val(itemId);
                }
            });

            // Event handler untuk perubahan satuan
            $(document).on('change', '.item-uom', function() {
                let row = $(this).closest('tr');
                validateRow(row);
            });

            // Event handler untuk tab key di notes
            $(document).on('keydown', '.item-notes', function(e) {
                if (e.key === 'Tab' && !e.shiftKey) {
                    e.preventDefault();
                    
                    let currentRow = $(this).closest('tr');
                    let newRow = currentRow.clone();
                    
                    // Clear values in the new row
                    newRow.find('input:not([type="hidden"])').val('');
                    newRow.find('.item-stock').text('0');
                    newRow.find('.item-available').text('0');
                    newRow.find('.item-uom').empty().append('<option value="">Pilih Satuan</option>');
                    
                    // Insert new row after current row
                    currentRow.after(newRow);
                    
                    // Focus on the item search in new row
                    let newItemSearch = newRow.find('.item-search');
                    newItemSearch.focus();
                    
                    // Activate autocomplete for new row
                    activateAutocomplete(newItemSearch);
                }
            });

            // Activate autocomplete for first row when page loads
            $(document).ready(function() {
                activateAutocomplete($('.item-search:first'));
                
                // Event handler untuk perubahan warehouse
                $('#warehouse_id, select[name="warehouse_id"]').change(function() {
                    // Clear existing items
                    $('.item-row:not(:first)').remove();
                    let firstRow = $('.item-row:first');
                    firstRow.find('input:not([type="hidden"])').val('');
                    firstRow.find('.item-stock').text('0');
                    firstRow.find('.item-available').text('0');
                    firstRow.find('.item-uom').empty().append('<option value="">Pilih Satuan</option>');
                    
                    // Reactivate autocomplete for first row
                    activateAutocomplete(firstRow.find('.item-search'));
                });
            });

            // Format number function
            function formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num || 0);
            }

            // Tambahkan validasi untuk quantity
            $(document).on('input', 'input[name$="[quantity]"]', function() {
                let input = $(this);
                let itemRow = input.closest('.item-row');
                
                if (input.val() > 0) {
                    input[0].setCustomValidity('');
                } else {
                    input[0].setCustomValidity('Quantity harus lebih dari 0');
                }
                validateRow(itemRow);
            });

            // Tambahkan validasi untuk unit
            $(document).on('change', '.item-uom', function() {
                let select = $(this);
                let itemRow = select.closest('.item-row');
                
                if (select.val()) {
                    select[0].setCustomValidity('');
                } else {
                    select[0].setCustomValidity('Pilih satuan');
                }
                validateRow(itemRow);
            });

            // Handle form submission
            $('#prForm').submit(function(e) {
                e.preventDefault();
                
                let form = $(this);
                form.addClass('was-validated');
                
                // Validasi semua row
                let isValid = true;
                $('.item-row').each(function() {
                    let row = $(this);
                    validateRow(row);
                });
                
                if (form[0].checkValidity() && isValid) {
                    // Kumpulkan data form
                    let formData = [];
                    
                    // Data header
                    formData.push({name: 'date', value: $('input[name="date"]').val()});
                    formData.push({name: 'warehouse_id', value: $('#warehouse_id').val()});
                    formData.push({name: 'notes', value: $('textarea[name="notes"]').val()});
                    
                    // Data items
                    $('.item-row').each(function(index) {
                        let row = $(this);
                        if (row.find('input[name$="[item_id]"]').val()) { // Hanya ambil row yang ada item-nya
                            formData.push({name: `items[${index}][item_id]`, value: row.find('input[name$="[item_id]"]').val()});
                            formData.push({name: `items[${index}][quantity]`, value: row.find('input[name$="[quantity]"]').val()});
                            formData.push({name: `items[${index}][uom_id]`, value: row.find('.item-uom').val()});
                            formData.push({name: `items[${index}][notes]`, value: row.find('.item-notes').val()});
                        }
                    });

                    // Tampilkan loading
                    Swal.fire({
                        title: 'Menyimpan Data',
                        text: 'Mohon tunggu...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ route('purchasing.purchase-requisitions.store') }}",
                        type: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                Swal.fire({
                                    title: 'Sukses!',
                                    text: response.message,
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = response.redirect;
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            let message = 'Terjadi kesalahan';
                            let errorDetails = '';
                            
                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.errors) {
                                    // Kumpulkan semua pesan error
                                    errorDetails = '<ul>';
                                    Object.values(xhr.responseJSON.errors).forEach(function(errors) {
                                        errors.forEach(function(error) {
                                            errorDetails += `<li>${error}</li>`;
                                        });
                                    });
                                    errorDetails += '</ul>';
                                } else if (xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                            }
                            
                            Swal.fire({
                                title: 'Error!',
                                html: message + (errorDetails ? '<br>' + errorDetails : ''),
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                } else {
                    // Tampilkan pesan error jika ada field yang belum diisi
                    Swal.fire({
                        title: 'Error!',
                        text: 'Mohon lengkapi semua field yang wajib diisi',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });

            // Update fungsi validateRow
            function validateRow(row) {
                let itemSearch = row.find('.item-search');
                let itemId = row.find('input[name$="[item_id]"]');
                let quantity = row.find('input[name$="[quantity]"]');
                let uom = row.find('.item-uom');
                
                // Cek apakah row ini diisi (jika ada item_id)
                if (itemId.val()) {
                    // Validasi field-field required
                    let isValid = itemSearch.val() && 
                                 quantity.val() > 0 && 
                                 uom.val();
                    
                    // Set visual feedback
                    [itemSearch, quantity, uom].forEach(field => {
                        if (field.val() && field[0].checkValidity()) {
                            field.addClass('is-valid').removeClass('is-invalid');
                        } else {
                            field.addClass('is-invalid').removeClass('is-valid');
                        }
                    });
                    
                    // Set class untuk row
                    if (isValid) {
                        row.addClass('is-valid').removeClass('is-invalid');
                    } else {
                        row.addClass('is-invalid').removeClass('is-valid');
                    }
                    
                    return isValid;
                }
                
                return true; // Row kosong dianggap valid
            }

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Tambahkan event handler untuk tombol delete row
            $(document).on('click', '.btn-delete-row', function(e) {
                e.preventDefault();
                
                let row = $(this).closest('tr');
                let totalRows = $('#itemsTable tbody tr').length;

                // Jika hanya tersisa 1 row, kosongkan nilai-nilainya saja
                if (totalRows === 1) {
                    row.find('input, select').val('');
                    row.find('.item-stock, .item-available').html('');
                } else {
                    // Hapus row jika lebih dari 1
                    row.remove();
                }

                // Perbarui nomor urut
                $('#itemsTable tbody tr').each(function(index) {
                    $(this).find('.row-number').text(index + 1);
                });
            });

            // Di dalam fungsi addItemToTable
            function addItemToTable(item, prNumber) {
                console.log('Adding item to table:', {
                    item_id: item.item_id,
                    item_name: item.item ? item.item.name : 'Unknown',
                    last_price: item.last_price,
                    lowest_price: item.lowest_price,
                    highest_price: item.highest_price,
                    moving_average_cost: item.moving_average_cost
                });

                const $template = $('#item-row-template').html();
                const $newRow = $($template);
                
                // ... existing code ...

                // Handle price history dengan logging
                if (item.last_price) {
                    console.log('Setting last price:', item.last_price);
                    $newRow.find('.last-price-col .price-value').text(formatNumber(item.last_price));
                    if (item.last_price_date) {
                        const lastPriceDate = new Date(item.last_price_date);
                        $newRow.find('.last-price-col .date-value').text(formatDate(lastPriceDate));
                    }
                    $newRow.data('last-price', item.last_price);
                    $newRow.find('.use-last-price').show();
                } else {
                    console.log('No last price available');
                }

                if (item.lowest_price) {
                    console.log('Setting lowest price:', item.lowest_price);
                    $newRow.find('.lowest-price-col .price-value').text(formatNumber(item.lowest_price));
                    $newRow.data('lowest-price', item.lowest_price);
                    $newRow.find('.use-lowest-price').show();
                } else {
                    console.log('No lowest price available');
                }

                if (item.highest_price) {
                    console.log('Setting highest price:', item.highest_price);
                    $newRow.find('.highest-price-col .price-value').text(formatNumber(item.highest_price));
                    $newRow.data('highest-price', item.highest_price);
                    $newRow.find('.use-highest-price').show();
                } else {
                    console.log('No highest price available');
                }

                // ... rest of the code ...
            }

            // Di dalam event handler PR selection
            $('#purchase_requisition_id').on('change', function() {
                const selectedPRIds = $(this).val();
                
                if (!selectedPRIds || selectedPRIds.length === 0) {
                    clearItems();
                    return;
                }

                Swal.fire({
                    title: 'Loading...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

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
                        clearItems();
                        
                        responses.forEach(response => {
                            console.log('PR Response:', response); // Debug full response
                            
                            if (response.success && response.items && response.items.length > 0) {
                                response.items.forEach(item => {
                                    console.log('Item data before adding:', {
                                        item_id: item.item_id,
                                        name: item.item?.name,
                                        last_price: item.last_price,
                                        lowest_price: item.lowest_price,
                                        highest_price: item.highest_price
                                    });
                                    
                                    addItemToTable(item, response.pr.pr_number);
                                });
                            }
                        });

                        updateRowNumbers();
                        calculateGrandTotal();
                        initializeSupplierSelects();

                        if ($('#creation_mode').val() === 'auto') {
                            updateAutoModePreview();
                        }

                        Swal.close();
                    })
                    .catch(error => {
                        console.error('Error fetching PR items:', error);
                        Swal.fire('Error', 'Gagal mengambil data item PR', 'error');
                    });
            });
        });
    </script>
@endsection 