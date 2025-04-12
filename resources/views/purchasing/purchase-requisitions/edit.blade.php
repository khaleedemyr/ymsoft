@extends('layouts.master')

@section('title')
    Edit Purchase Requisition
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
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Purchasing
        @endslot
        @slot('title')
            Edit Purchase Requisition
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
                                <input type="text" class="form-control flatpickr" name="date" value="{{ $pr->date }}" required>
                                <div class="invalid-feedback">
                                    Mohon isi tanggal
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Departemen <span class="text-danger">*</span></label>
                                <select class="form-control" name="warehouse_id" id="warehouse_id" required>
                                    <option value="">Pilih Departemen</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ $pr->warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
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
                                <textarea class="form-control" name="notes" rows="3">{{ $pr->notes }}</textarea>
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
                                                <th width="30%">Item</th>
                                                <th width="15%" class="text-center">
                                                    Stock
                                                    <i class="ri-information-line text-primary" 
                                                       data-bs-toggle="tooltip" 
                                                       data-bs-placement="top" 
                                                       title="Total fisik barang yang ada di gudang/departemen, termasuk stock yang sudah di-reserve"></i>
                                                </th>
                                                <th width="15%" class="text-center">
                                                    Available
                                                    <i class="ri-information-line text-primary" 
                                                       data-bs-toggle="tooltip" 
                                                       data-bs-placement="top" 
                                                       title="Stock yang tersedia untuk digunakan (Stock - Reserved)"></i>
                                                </th>
                                                <th width="10%">Qty</th>
                                                <th width="15%">Satuan</th>
                                                <th>Catatan</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pr->items as $item)
                                                <tr class="item-row">
                                                    <td>
                                                        <input type="text" class="form-control item-search" 
                                                               value="{{ $item->item->name }}" required>
                                                        <input type="hidden" name="items[][item_id]" 
                                                               value="{{ $item->item_id }}">
                                                    </td>
                                                    <td class="text-end item-stock">{{ number_format($item->item->stock_on_hand, 0) }}</td>
                                                    <td class="text-end item-available">{{ number_format($item->item->stock_available, 0) }}</td>
                                                    <td>
                                                        <input type="number" class="form-control text-end item-qty" 
                                                               name="items[][quantity]" value="{{ $item->quantity }}" required>
                                                    </td>
                                                    <td>
                                                        <select class="form-control item-uom" name="items[][uom_id]" required>
                                                            <option value="">Pilih Satuan</option>
                                                            @foreach($units as $unit)
                                                                <option value="{{ $unit->id }}" 
                                                                    {{ $item->uom_id == $unit->id ? 'selected' : '' }}>
                                                                    {{ $unit->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control item-notes" 
                                                               name="items[][notes]" value="{{ $item->notes }}">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete-row">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('purchasing.purchase-requisitions.show', $pr->id) }}" class="btn btn-secondary">
                                        Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">Update</button>
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
        // Gunakan script yang sama seperti di create.blade.php
        // Dengan penyesuaian URL untuk update
        $(document).ready(function() {
            // Initialize flatpickr
            $('.flatpickr').flatpickr({
                dateFormat: "Y-m-d"
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Handle add item button
            $('#addItem').click(function() {
                let rowCount = $('.item-row').length;
                let newRow = $('.item-row:first').clone();
                
                // Clear values
                newRow.find('input').val('');
                newRow.find('select').val('');
                newRow.find('.item-stock').text('0');
                newRow.find('.item-available').text('0');
                
                // Update names
                newRow.find('[name]').each(function() {
                    let name = $(this).attr('name');
                    $(this).attr('name', name.replace(/\[\d*\]/, '[' + rowCount + ']'));
                });

                // Reset validasi
                newRow.removeClass('is-valid is-invalid');
                newRow.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
                
                // Append new row
                $('#itemsTable tbody').append(newRow);
                
                // Focus ke item search di row baru
                let newItemSearch = newRow.find('.item-search');
                setTimeout(function() {
                    newItemSearch.focus();
                    activateAutocomplete(newItemSearch);
                }, 100);
            });

            // Handle delete row button
            $(document).on('click', '.btn-delete-row', function() {
                if ($('.item-row').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Minimal harus ada 1 item',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });

            // Fungsi untuk mengaktifkan autocomplete
            function activateAutocomplete(input) {
                let row = input.closest('tr');
                let warehouseId = $('#warehouse_id').val();
                input.attr('list', 'itemsList');
            }

            // Event handler untuk pencarian item
            $(document).on('input', '.item-search', function() {
                let input = $(this);
                let row = input.closest('tr');
                let warehouseId = $('#warehouse_id').val();
                let searchTerm = input.val();

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
                            let datalist = $('#itemsList');
                            datalist.empty();
                            
                            items.forEach(function(item) {
                                datalist.append(`
                                    <option value="${item.name}"
                                        data-id="${item.id}"
                                        data-stock="${item.stock_on_hand}"
                                        data-available="${item.stock_available}"
                                        data-unit="${item.unit.name}">
                                        ${item.name} - Stock: ${item.stock_on_hand}
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
                    let stock = selectedOption.data('stock') || 0;
                    let available = selectedOption.data('available') || 0;
                    
                    row.find('.item-stock').text(formatNumber(stock));
                    row.find('.item-available').text(formatNumber(available));
                    row.find('input[name$="[item_id]"]').val(itemId);

                    // Ambil data unit untuk item yang dipilih
                    $.ajax({
                        url: `/items/${itemId}/units`,
                        type: 'GET',
                        success: function(response) {
                            let uomSelect = row.find('.item-uom');
                            uomSelect.empty();
                            uomSelect.append('<option value="">Pilih Satuan</option>');
                            
                            response.forEach(function(unit) {
                                uomSelect.append(`
                                    <option value="${unit.id}" ${unit.is_largest ? 'selected' : ''}>
                                        ${unit.name}
                                    </option>
                                `);
                            });
                        }
                    });
                }
            });

            // Update event handler untuk tab key di notes
            $(document).on('keydown', '.item-notes', function(e) {
                if (e.key === 'Tab' && !e.shiftKey) {
                    e.preventDefault();
                    
                    let currentRow = $(this).closest('tr');
                    let newRow = currentRow.clone();
                    
                    // Clear values
                    newRow.find('input').val('');
                    newRow.find('select').val('');
                    newRow.find('.item-stock').text('0');
                    newRow.find('.item-available').text('0');
                    
                    // Update names untuk index baru
                    let rowCount = $('.item-row').length;
                    newRow.find('[name]').each(function() {
                        let name = $(this).attr('name');
                        $(this).attr('name', name.replace(/\[\d*\]/, '[' + rowCount + ']'));
                    });

                    // Reset validasi
                    newRow.removeClass('is-valid is-invalid');
                    newRow.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
                    
                    // Append new row
                    $('#itemsTable tbody').append(newRow);
                    
                    // Focus ke item search di row baru
                    let newItemSearch = newRow.find('.item-search');
                    setTimeout(function() {
                        newItemSearch.focus();
                        activateAutocomplete(newItemSearch);
                    }, 100);
                }
            });

            // Format number function
            function formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }

            // Form submission
            $('#prForm').submit(function(e) {
                e.preventDefault();
                
                let form = $(this);
                form.addClass('was-validated');
                
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

                if (form[0].checkValidity()) {
                    Swal.fire({
                        title: 'Mengupdate Data',
                        text: 'Mohon tunggu...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ route('purchasing.purchase-requisitions.update', $pr->id) }}",
                        type: 'PUT',
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
                    Swal.fire({
                        title: 'Error!',
                        text: 'Mohon lengkapi semua field yang wajib diisi',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>
@endsection 