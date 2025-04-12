@extends('layouts.master')

@section('title')
    @lang('translation.warehouse.reports.stock_analysis.title') | {{ config('app.name') }}
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

        /* Fixed Columns */
        .table-fixed-columns th:nth-child(1),
        .table-fixed-columns td:nth-child(1) {
            position: sticky;
            background-color: #fff;
            z-index: 1;
            left: 0;
        }

        /* Header styling */
        .table-fixed-columns thead th {
            background-color: #f3f6f9;
            z-index: 2;
        }

        /* Shadow effect for fixed columns */
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

        /* Ensure proper border rendering */
        .table-fixed-columns th,
        .table-fixed-columns td {
            border-right: 1px solid #e9ebec !important;
        }

        /* Container styling */
        .table-container {
            overflow-x: auto;
            max-width: 100%;
            border-radius: 0.25rem;
        }

        /* Minimum width for columns */
        .table-fixed-columns th:nth-child(1),
        .table-fixed-columns td:nth-child(1) {
            min-width: 200px; /* Item column */
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
        .price-info {
            color: #495057;
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
                        @lang('translation.warehouse.reports.stock_analysis.title')
                    @endslot
                @endcomponent

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card" id="tasksList">
                            <div class="card-header border-0">
                                <div class="d-flex align-items-center">
                                    <h5 class="card-title mb-0 flex-grow-1">@lang('translation.warehouse.reports.stock_analysis.title')</h5>
                                </div>
                            </div>
                            <div class="card-body border border-dashed border-end-0 border-start-0">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">@lang('translation.warehouse.reports.stock_analysis.filter.warehouse')</label>
                                        <select class="form-select" id="warehouse_id">
                                            <option value="">@lang('translation.warehouse.reports.stock_analysis.filter.all_warehouses')</option>
                                            @foreach($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">@lang('translation.warehouse.reports.stock_analysis.filter.item')</label>
                                        <select class="form-select" id="item_id">
                                            <option value="">@lang('translation.warehouse.reports.stock_analysis.filter.all_items')</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->sku }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">@lang('translation.warehouse.reports.stock_analysis.filter.start_date')</label>
                                        <input type="text" class="form-control flatpickr" id="start_date" placeholder="@lang('translation.warehouse.reports.stock_analysis.filter.start_date')">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">@lang('translation.warehouse.reports.stock_analysis.filter.end_date')</label>
                                        <input type="text" class="form-control flatpickr" id="end_date" placeholder="@lang('translation.warehouse.reports.stock_analysis.filter.end_date')">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-primary flex-grow-1" id="btnLoad">
                                                <i class="ri-search-line align-bottom me-1"></i> @lang('translation.warehouse.reports.stock_analysis.filter.load')
                                            </button>
                                            <button type="button" class="btn btn-success" id="btnExport" disabled>
                                                <i class="ri-file-excel-2-line align-bottom me-1"></i>
                                                @lang('translation.warehouse.reports.stock_analysis.export.title')
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
                                                <th>@lang('translation.warehouse.reports.stock_analysis.table.item')</th>
                                                <th>@lang('translation.warehouse.reports.stock_analysis.table.sku')</th>
                                                <th>@lang('translation.warehouse.reports.stock_analysis.table.warehouse')</th>
                                                <th>@lang('translation.warehouse.reports.stock_analysis.table.stock')</th>
                                                <th>@lang('translation.warehouse.reports.stock_analysis.table.total_in')</th>
                                                <th>@lang('translation.warehouse.reports.stock_analysis.table.total_out')</th>
                                                <th>@lang('translation.warehouse.reports.stock_analysis.table.turnover')</th>
                                                <th>@lang('translation.warehouse.reports.stock_analysis.table.avg_cost')</th>
                                                <th>@lang('translation.warehouse.reports.stock_analysis.table.total_value')</th>
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
    <form id="exportForm" method="POST" action="{{ route('warehouse.reports.stock-analysis.export') }}">
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

            function formatStockInfo(smallQty) {
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
                
                // Konversi Besar
                html += `<div class="stock-info">
                    <strong>Konversi Besar:</strong><br>
                    <div class="conversion-info">
                        ${mediumPacks} Pack ${mediumRemainder > 0 ? `+ ${mediumRemainder} Gram` : ''}<br>
                        <span class="unit-info">(1 Pack = 1 Pack = ${formatNumber(mediumConversion)} Gram)</span>
                    </div>
                </div>`;
                
                return html;
            }

            function formatPriceInfo(basePrice) {
                if (!basePrice) return '-';
                
                const mediumConversion = 250;
                const mediumPrice = basePrice * mediumConversion;
                
                let html = `<div class="price-info">
                    <strong>Harga Dasar:</strong> Rp ${formatNumber(basePrice)}/Gram<br>
                    <strong>Konversi Menengah:</strong><br>
                    <div class="conversion-info">
                        Rp ${formatNumber(mediumPrice)}/Pack<br>
                        <span class="unit-info">(1 Pack = ${formatNumber(mediumConversion)} Gram)</span>
                    </div>
                    <strong>Konversi Besar:</strong><br>
                    <div class="conversion-info">
                        Rp ${formatNumber(mediumPrice)}/Pack<br>
                        <span class="unit-info">(1 Pack = 1 Pack = ${formatNumber(mediumConversion)} Gram)</span>
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
                        text: '@lang("translation.warehouse.reports.stock_analysis.please_select_date")'
                    });
                    hideLoading();
                    return;
                }

                $.ajax({
                    url: '{{ route("warehouse.reports.stock-analysis.data") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        warehouse_id: warehouseId,
                        item_id: itemId,
                        start_date: startDate,
                        end_date: endDate,
                        page: currentPage
                    },
                    success: function(response) {
                        let html = '';
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(function(row) {
                                html += `
                                    <tr>
                                        <td>${row.item_name || '-'}</td>
                                        <td>${row.item_sku || '-'}</td>
                                        <td>${row.warehouse_name || '-'}</td>
                                        <td>${formatStockInfo(row.stock_balance)}</td>
                                        <td>${formatStockInfo(row.total_in)}</td>
                                        <td>${formatStockInfo(row.total_out)}</td>
                                        <td>${formatStockInfo(row.turnover_rate)}</td>
                                        <td>${formatPriceInfo(row.moving_average_cost)}</td>
                                        <td class="text-end">Rp ${formatNumber(row.total_value)}</td>
                                    </tr>`;
                            });
                        } else {
                            html = '<tr><td colspan="9" class="text-center">@lang("translation.warehouse.reports.stock_analysis.no_data")</td></tr>';
                        }
                        
                        $("#reportData").html(html);
                        $("#btnExport").prop('disabled', false);
                        
                        if (response.pagination) {
                            $("#fromItem").text(response.pagination.from || 0);
                            $("#toItem").text(response.pagination.to || 0);
                            $("#totalItems").text(response.pagination.total);
                            renderPagination(response.pagination);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '@lang("translation.warehouse.reports.stock_analysis.error_loading")'
                        });
                        $("#btnExport").prop('disabled', true);
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
                        text: '@lang("translation.warehouse.reports.stock_analysis.please_select_date")'
                    });
                    return;
                }

                if (!itemId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '@lang("translation.warehouse.reports.stock_analysis.please_select_item")'
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
                $("#reportData").html('<tr><td colspan="9" class="text-center">Silakan klik tombol Cari untuk memuat data</td></tr>');
                $("#pagination").html('');
                $("#fromItem").text('0');
                $("#toItem").text('0');
                $("#totalItems").text('0');
                $("#btnExport").prop('disabled', true);
            });

            // Tampilkan pesan awal
            $("#reportData").html('<tr><td colspan="9" class="text-center">Silakan pilih item dan tanggal, lalu klik tombol Cari</td></tr>');
        });
    </script>
@endsection 