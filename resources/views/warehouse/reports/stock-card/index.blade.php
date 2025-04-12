@extends('layouts.master')

@section('title')
    Kartu Stok | {{ config('app.name') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        .table-fixed-columns {
            position: relative;
        }

        .table-fixed-columns thead th,
        .table-fixed-columns tbody td {
            white-space: nowrap;
        }

        .table-fixed-columns th:nth-child(1),
        .table-fixed-columns td:nth-child(1) {
            position: sticky;
            background-color: #fff;
            z-index: 1;
            left: 0;
        }

        .table-fixed-columns thead th {
            background-color: #f3f6f9;
            z-index: 2;
        }

        .table-fixed-columns td:nth-child(1)::after,
        .table-fixed-columns th:nth-child(1)::after {
            content: '';
            position: absolute;
            top: 0;
            right: -5px;
            bottom: 0;
            width: 5px;
            background: linear-gradient(to right, rgba(0,0,0,0.1), rgba(0,0,0,0));
            pointer-events: none;
        }

        .table-fixed-columns th,
        .table-fixed-columns td {
            border-right: 1px solid #e9ebec !important;
        }

        .table-container {
            overflow-x: auto;
            max-width: 100%;
            border-radius: 0.25rem;
        }

        .table-fixed-columns th:nth-child(1),
        .table-fixed-columns td:nth-child(1) {
            min-width: 200px;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .transaction-type {
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .transaction-type.in {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .transaction-type.out {
            background-color: #ffebee;
            color: #c62828;
        }

        .stock-info {
            margin-bottom: 8px;
        }
        .stock-info:last-child {
            margin-bottom: 0;
        }
        .conversion-info {
            padding-left: 12px;
        }
        .unit-info {
            color: #6c757d;
            font-size: 0.9em;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                @component('components.breadcrumb')
                    @slot('li_1')
                        @lang('translation.warehouse.reports.title')
                    @endslot
                    @slot('title')
                        @lang('translation.warehouse.reports.stock_card.title')
                    @endslot
                @endcomponent

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card" id="tasksList">
                            <div class="card-header border-0">
                                <div class="d-flex align-items-center">
                                    <h5 class="card-title mb-0 flex-grow-1">@lang('translation.warehouse.reports.stock_card.title')</h5>
                                </div>
                            </div>
                            <div class="card-body border border-dashed border-end-0 border-start-0">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">@lang('translation.warehouse.reports.stock_card.filter.warehouse')</label>
                                        <select class="form-select" id="warehouse_id">
                                            <option value="">@lang('translation.warehouse.reports.stock_card.filter.all_warehouses')</option>
                                            @foreach($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">@lang('translation.warehouse.reports.stock_card.filter.item')</label>
                                        <select class="form-select" id="item_id">
                                            <option value="">@lang('translation.warehouse.reports.stock_card.filter.all_items')</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Tanggal Mulai</label>
                                        <input type="text" class="form-control flatpickr" id="start_date" placeholder="Tanggal Mulai">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Tanggal Akhir</label>
                                        <input type="text" class="form-control flatpickr" id="end_date" placeholder="Tanggal Akhir">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-primary flex-grow-1" id="btnLoad">
                                                <i class="ri-search-line align-bottom me-1"></i> Cari
                                            </button>
                                            <button type="button" class="btn btn-success" id="btnExport" disabled>
                                                <i class="ri-file-excel-2-line align-bottom me-1"></i>
                                                Export
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body position-relative">
                                <div class="loading-overlay d-none">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                                <div class="table-container">
                                    <table class="table table-bordered table-nowrap align-middle mb-0 table-fixed-columns">
                                        <thead class="table-light">
                                            <tr>
                                                <th>@lang('translation.warehouse.reports.stock_card.table.date')</th>
                                                <th>@lang('translation.warehouse.reports.stock_card.table.reference_number')</th>
                                                <th>@lang('translation.warehouse.reports.stock_card.table.type')</th>
                                                <th>@lang('translation.warehouse.reports.stock_card.table.item')</th>
                                                <th>@lang('translation.warehouse.reports.stock_card.table.warehouse')</th>
                                                <th>@lang('translation.warehouse.reports.stock_card.table.qty_in')</th>
                                                <th>@lang('translation.warehouse.reports.stock_card.table.qty_out')</th>
                                                <th>@lang('translation.warehouse.reports.stock_card.table.balance')</th>
                                            </tr>
                                        </thead>
                                        <tbody id="reportData">
                                            <!-- Data will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="pagination-info">
                                        Menampilkan <span id="fromItem">0</span> - <span id="toItem">0</span> dari <span id="totalItems">0</span> item
                                    </div>
                                    <div class="pagination-container">
                                        <ul class="pagination" id="pagination">
                                            <!-- Pagination will be rendered here -->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form untuk export -->
    <form id="exportForm" action="{{ route('warehouse.reports.stock-card.export') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="warehouse_id" id="export_warehouse_id">
        <input type="hidden" name="item_id" id="export_item_id">
        <input type="hidden" name="start_date" id="export_start_date">
        <input type="hidden" name="end_date" id="export_end_date">
    </form>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi flatpickr
            $(".flatpickr").flatpickr({
                dateFormat: "Y-m-d"
            });

            let currentPage = 1;

            function showLoading() {
                $(".loading-overlay").removeClass("d-none");
                $("#btnLoad").prop("disabled", true);
                $("#btnExport").prop("disabled", true);
            }

            function hideLoading() {
                $(".loading-overlay").addClass("d-none");
                $("#btnLoad").prop("disabled", false);
            }

            function formatStockInfo(smallQty, conversionData) {
                if (!smallQty) return '-';
                
                let html = '';
                
                // Stok Dasar
                html += `<div class="stock-info">
                    <strong>Stok Dasar:</strong> ${formatNumber(smallQty)} Gram
                </div>`;
                
                // Konversi Menengah (250 gram = 1 Pack)
                const mediumConversion = 250;
                const mediumPacks = Math.floor(smallQty / mediumConversion);
                const mediumRemainder = smallQty % mediumConversion;
                
                html += `<div class="stock-info">
                    <strong>Konversi Menengah:</strong><br>
                    <div class="conversion-info">
                        ${mediumPacks} Pack ${mediumRemainder > 0 ? `+ ${mediumRemainder} Gram` : ''}<br>
                        <span class="unit-info">(1 Pack = ${formatNumber(mediumConversion)} Gram)</span>
                    </div>
                </div>`;
                
                // Konversi Besar (sama dengan konversi menengah karena 1 Pack Besar = 1 Pack)
                html += `<div class="stock-info">
                    <strong>Konversi Besar:</strong><br>
                    <div class="conversion-info">
                        ${mediumPacks} Pack ${mediumRemainder > 0 ? `+ ${mediumRemainder} Gram` : ''}<br>
                        <span class="unit-info">(1 Pack = 1.00 Pack = ${formatNumber(mediumConversion)} Gram)</span>
                    </div>
                </div>`;
                
                return html;
            }

            function loadReport() {
                showLoading();
                
                const warehouseId = $("#warehouse_id").val();
                const itemId = $("#item_id").val();
                const startDate = $("#start_date").val();
                const endDate = $("#end_date").val();

                if (!startDate || !endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '@lang("translation.warehouse.reports.stock_card.message.select_date")'
                    });
                    hideLoading();
                    return;
                }

                if (!itemId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '@lang("translation.warehouse.reports.stock_card.message.select_item")'
                    });
                    hideLoading();
                    return;
                }

                const formData = new FormData();
                formData.append('warehouse_id', warehouseId);
                formData.append('item_id', itemId);
                formData.append('start_date', startDate);
                formData.append('end_date', endDate);
                formData.append('page', currentPage);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: '{{ route("warehouse.reports.stock-card.data") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Response:', response);
                        
                        if (response.success) {
                            let html = '';
                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(row) {
                                    const date = new Date(row.date);
                                    const formattedDate = date.toLocaleDateString('id-ID', {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric'
                                    });
                                    
                                    const transactionType = row.qty_in > 0 ? 'Masuk' : 'Keluar';
                                    const transactionTypeClass = row.qty_in > 0 ? 'success' : 'danger';
                                    
                                    html += `
                                        <tr>
                                            <td>${formattedDate}</td>
                                            <td>${row.reference_number || '-'}</td>
                                            <td><span class="badge bg-${transactionTypeClass}">${transactionType}</span></td>
                                            <td>${row.item_name || '-'}</td>
                                            <td>${row.warehouse_name || '-'}</td>
                                            <td>${formatStockInfo(row.qty_in)}</td>
                                            <td>${formatStockInfo(row.qty_out)}</td>
                                            <td>${formatStockInfo(row.stock_balance)}</td>
                                        </tr>`;
                                });
                            } else {
                                html = '<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>';
                            }
                            
                            $("#reportData").html(html);
                            $("#btnExport").prop('disabled', false);
                            
                            if (response.pagination) {
                                $("#fromItem").text(response.pagination.from || 0);
                                $("#toItem").text(response.pagination.to || 0);
                                $("#totalItems").text(response.pagination.total);
                                renderPagination(response.pagination);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Gagal memuat data'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax error:', {xhr, status, error});
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data: ' + error
                        });
                    },
                    complete: function() {
                        hideLoading();
                    }
                });
            }

            function renderPagination(pagination) {
                let html = '';
                
                // Previous button
                html += `
                    <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                            <i class="ri-arrow-left-s-line"></i>
                        </a>
                    </li>`;

                // Page numbers
                for (let i = 1; i <= pagination.last_page; i++) {
                    if (
                        i === 1 || // First page
                        i === pagination.last_page || // Last page
                        (i >= pagination.current_page - 2 && i <= pagination.current_page + 2) // Pages around current
                    ) {
                        html += `
                            <li class="page-item ${pagination.current_page === i ? 'active' : ''}">
                                <a class="page-link" href="#" data-page="${i}">${i}</a>
                            </li>`;
                    } else if (
                        i === pagination.current_page - 3 ||
                        i === pagination.current_page + 3
                    ) {
                        html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                }

                // Next button
                html += `
                    <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                            <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </li>`;

                $("#pagination").html(html);

                // Bind click events
                $("#pagination").on("click", "a.page-link", function(e) {
                    e.preventDefault();
                    const page = $(this).data("page");
                    if (page && page !== currentPage) {
                        currentPage = page;
                        loadReport();
                    }
                });
            }

            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(number || 0);
            }

            // Event handler untuk tombol Cari
            $("#btnLoad").on('click', function(e) {
                e.preventDefault();
                currentPage = 1;
                loadReport();
            });

            // Event handler untuk tombol export
            $("#btnExport").on('click', function() {
                const startDate = $("#start_date").val();
                const endDate = $("#end_date").val();
                const itemId = $("#item_id").val();

                if (!startDate || !endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '@lang("translation.warehouse.reports.stock_card.message.select_date")'
                    });
                    return;
                }

                if (!itemId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '@lang("translation.warehouse.reports.stock_card.message.select_item")'
                    });
                    return;
                }

                $("#export_warehouse_id").val($("#warehouse_id").val());
                $("#export_item_id").val(itemId);
                $("#export_start_date").val(startDate);
                $("#export_end_date").val(endDate);
                $("#exportForm").submit();
            });

            // Event handler untuk filter
            $("#warehouse_id, #item_id, #start_date, #end_date").on('change', function() {
                currentPage = 1;
                $("#reportData").html('<tr><td colspan="8" class="text-center">@lang("translation.warehouse.reports.stock_card.message.please_search")</td></tr>');
                $("#pagination").html('');
                $("#fromItem").text('0');
                $("#toItem").text('0');
                $("#totalItems").text('0');
                $("#btnExport").prop('disabled', true);
            });

            // Tampilkan pesan awal
            $("#reportData").html('<tr><td colspan="8" class="text-center">@lang("translation.warehouse.reports.stock_card.message.please_search")</td></tr>');
        });
    </script>
@endsection 