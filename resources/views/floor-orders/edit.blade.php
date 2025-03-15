@extends('layouts.master')

@section('title')
    {{ __('translation.floor_order.edit.title') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet">
    <style>
        .search-box {
            width: 100%;
            max-width: 300px;
            margin-bottom: 1rem;
            position: relative;
        }
        
        .search-box .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #74788d;
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

        .category-row td {
            padding: 12px 15px !important;
        }
        
        .subcategory-row td:first-child {
            padding-left: 25px !important;
        }
        
        .item-row td:first-child {
            padding-left: 50px !important;
        }

        .filter-section {
            background-color: #fff;
            padding: 15px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }

        .item-row:hover {
            background-color: #f8f9fa;
        }

        .table-responsive {
            max-height: calc(100vh - 400px);
        }

        .table thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }
            
            .table {
                font-size: 0.875rem;
            }
            
            .qty-input {
                width: 60px;
                min-width: 60px;
            }
            
            .category-row td {
                font-size: 1rem !important;
                padding: 10px !important;
            }
        }

        .search-box {
            position: relative;
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
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('translation.floor_order.edit.title') }}</h4>
                    </div>
                    <div class="card-body">
                        <form id="editForm" action="{{ route('floor-orders.update', $floorOrder->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Header Info -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('translation.floor_order.edit.fo_number') }}</label>
                                        <input type="text" class="form-control" value="{{ $floorOrder->fo_number }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('translation.floor_order.edit.warehouse') }}</label>
                                        <input type="text" class="form-control" value="{{ $floorOrder->warehouse->name }}" readonly>
                                        <input type="hidden" name="warehouse_id" value="{{ $floorOrder->warehouse_id }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('translation.floor_order.edit.arrival_date') }}</label>
                                        <input type="date" class="form-control" name="arrival_date" 
                                               value="{{ $floorOrder->arrival_date->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('translation.floor_order.edit.notes') }}</label>
                                        <textarea class="form-control" name="notes" rows="2">{{ $floorOrder->notes }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Debug info - akan dihapus nanti -->
                            <div class="alert alert-info">
                                Floor Order ID: {{ $floorOrder->id }}<br>
                                Warehouse: {{ $floorOrder->warehouse->name }}<br>
                                Items Count: {{ $floorOrder->items->count() }}
                            </div>

                            <!-- Search Box -->
                            <div class="filter-section">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ __('translation.floor_order.edit.search_item') }}</label>
                                        <div class="search-box">
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="searchItem" 
                                                   placeholder="{{ __('translation.floor_order.edit.search_placeholder') }}">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Items Table -->
                            <div class="table-responsive">
                                <table id="itemsTable" class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('translation.floor_order.edit.table.item_name') }}</th>
                                            <th>{{ __('translation.floor_order.edit.table.sku') }}</th>
                                            <th>{{ __('translation.floor_order.edit.table.qty') }}</th>
                                            <th>{{ __('translation.floor_order.edit.table.unit') }}</th>
                                            <th>{{ __('translation.floor_order.edit.table.price') }}</th>
                                            <th>{{ __('translation.floor_order.edit.table.total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupedItems as $category => $subcategories)
                                            <tr class="category-row" data-category="{{ $category }}">
                                                <td colspan="6">
                                                    <i class="ri-arrow-down-s-line collapse-icon"></i>
                                                    {{ $category }}
                                                </td>
                                            </tr>
                                            
                                            @foreach($subcategories as $subcategory => $items)
                                                <tr class="subcategory-row" data-category="{{ $category }}" data-subcategory="{{ $subcategory }}">
                                                    <td colspan="6">
                                                        <i class="ri-arrow-down-s-line collapse-icon"></i>
                                                        {{ $subcategory }}
                                                    </td>
                                                </tr>
                                                
                                                @foreach($items as $item)
                                                    <tr class="item-row" data-category="{{ $category }}" data-subcategory="{{ $subcategory }}">
                                                        <td>{{ $item['name'] }}</td>
                                                        <td>{{ $item['sku'] }}</td>
                                                        <td>
                                                            <input type="number" 
                                                                class="form-control form-control-sm qty-input" 
                                                                name="items[{{ $item['id'] }}][qty]" 
                                                                min="0"
                                                                value="{{ $item['qty'] }}"
                                                                data-price="{{ $item['price'] }}">
                                                        </td>
                                                        <td>{{ $item['medium_unit'] }}</td>
                                                        <td class="text-end">
                                                            Rp {{ number_format($item['price'], 0) }}
                                                            <small class="d-block text-muted">
                                                                ({{ $item['medium_conversion_qty'] }} {{ $item['medium_unit'] }})
                                                            </small>
                                                        </td>
                                                        <td class="text-end line-total">Rp {{ number_format($item['total'], 0) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold">Total:</td>
                                            <td class="text-end fw-bold" id="grandTotal">
                                                Rp {{ number_format($floorOrder->total_amount, 0) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">{{ __('translation.floor_order.edit.buttons.save') }}</button>
                                    <a href="{{ route('floor-orders.index') }}" class="btn btn-secondary">{{ __('translation.floor_order.edit.buttons.back') }}</a>
                                </div>
                            </div>
                        </form>
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
            let autoSaveTimeout;

            // Handle qty input change
            $(document).on('input', '.qty-input', function() {
                const itemId = $(this).attr('name').match(/\[(\d+)\]/)[1];
                const qty = parseFloat($(this).val()) || 0;
                const price = parseFloat($(this).data('price')) || 0;
                const total = qty * price;
                
                // Update line total
                const $row = $(this).closest('tr');
                $row.find('.line-total').text('Rp ' + numberFormat(total));
                
                // Update grand total
                updateGrandTotal();
            });

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

            // Search functionality
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
                        
                        $(`.category-row[data-category="${category}"]`)
                            .show()
                            .removeClass('collapsed');
                            
                        $(`.subcategory-row[data-category="${category}"][data-subcategory="${subcategory}"]`)
                            .show()
                            .removeClass('collapsed');
                    }
                });

                if (!hasResults) {
                    $('#no-results').remove();
                    $('#itemsTable tbody').append(`
                        <tr id="no-results">
                            <td colspan="6" class="text-center">
                                Tidak ada item yang cocok dengan pencarian
                            </td>
                        </tr>
                    `);
                } else {
                    $('#no-results').remove();
                }
            });

            function numberFormat(number) {
                return Math.ceil(number / 100) * 100
                    .toString()
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function updateGrandTotal() {
                let grandTotal = 0;
                $('.qty-input').each(function() {
                    const qty = parseFloat($(this).val()) || 0;
                    const price = parseFloat($(this).data('price')) || 0;
                    grandTotal += qty * price;
                });
                
                $('#grandTotal').text('Rp ' + numberFormat(grandTotal));
            }

            // Initialize
            updateGrandTotal();

            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                
                // Debug: log form data
                console.log('Form action:', $(this).attr('action'));
                console.log('Form method:', $(this).attr('method'));
                
                let formData = new FormData(this);
                formData.append('_method', 'PUT'); // Pastikan method PUT
                
                // Collect items data
                let items = {};
                $('.qty-input').each(function() {
                    const qty = parseFloat($(this).val()) || 0;
                    if (qty > 0) {
                        const itemId = $(this).attr('name').match(/\[(\d+)\]/)[1];
                        items[itemId] = {
                            qty: qty
                        };
                    }
                });
                
                // Debug: log items data
                console.log('Items data:', items);
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST', // Tetap POST karena _method: PUT sudah ditambahkan
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Success response:', response);
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Terjadi kesalahan'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                        });
                    }
                });
            });
        });
    </script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 