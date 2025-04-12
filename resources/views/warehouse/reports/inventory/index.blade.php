@extends('layouts.master')

@section('title')
    Laporan Stok | {{ config('app.name') }}
@endsection

@section('css')
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

        .unit-info {
            font-size: 12px;
            color: #6c757d;
        }

        .stock-info {
            border-left: 3px solid #0ab39c;
            padding-left: 8px;
            margin: 4px 0;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                @component('components.breadcrumb')
                    @slot('li_1')
                        Laporan
                    @endslot
                    @slot('title')
                        Laporan Stok
                    @endslot
                @endcomponent

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card" id="tasksList">
                            <div class="card-header border-0">
                                <div class="d-flex align-items-center">
                                    <h5 class="card-title mb-0 flex-grow-1">Laporan Stok</h5>
                                </div>
                            </div>
                            <div class="card-body border border-dashed border-end-0 border-start-0">
                                <form id="searchForm" onsubmit="return false;">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Gudang</label>
                                            <select class="form-select" id="warehouse_id" name="warehouse_id">
                                                <option value="">Semua Gudang</option>
                                                @foreach($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Pencarian</label>
                                            <input type="text" class="form-control" id="search" name="search" placeholder="Cari berdasarkan nama atau kode item">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">&nbsp;</label>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary flex-grow-1" id="btnLoad">
                                                    <i class="ri-search-line align-bottom me-1"></i> Cari
                                                </button>
                                                <button type="button" class="btn btn-success" id="btnExport" disabled>
                                                    <i class="ri-file-excel-2-line align-bottom me-1"></i>
                                                    Export
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
                                                <th>Gudang</th>
                                                <th>Item</th>
                                                <th>Informasi Stok & Konversi</th>
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
    <form id="exportForm" action="{{ route('warehouse.reports.inventory.export') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="warehouse_id" id="export_warehouse_id">
        <input type="hidden" name="search" id="export_search">
    </form>
@endsection

@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        $(document).ready(function() {
            let currentPage = 1;

            $("#searchForm").on('submit', function(e) {
                e.preventDefault();
                currentPage = 1;
                loadReport();
            });

            $("#btnExport").click(function() {
                // Set nilai untuk form export
                $("#export_warehouse_id").val($("#warehouse_id").val());
                $("#export_search").val($("#search").val());
                
                // Submit form export
                $("#exportForm").submit();
            });

            let searchTimeout;
            $("#search").on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    currentPage = 1;
                    loadReport();
                }, 500);
            });

            $("#warehouse_id").change(function() {
                currentPage = 1;
                loadReport();
            });

            function loadReport() {
                const warehouseId = $("#warehouse_id").val();
                const search = $("#search").val();

                $(".loading-overlay").removeClass("d-none");
                $("#btnLoad").prop("disabled", true);
                $("#btnExport").prop("disabled", true);

                $.ajax({
                    url: '{{ route("warehouse.reports.inventory.data") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        warehouse_id: warehouseId,
                        search: search,
                        page: currentPage
                    },
                    success: function(response) {
                        console.log('Response:', response); // Tambahkan log untuk debugging
                        if (response.success) {
                            let html = '';
                            if (response.data.length > 0) {
                                response.data.forEach(function(row) {
                                    // Format unit conversion information
                                    let unitInfo = '';
                                    
                                    // Small unit info
                                    unitInfo += `<div class="stock-info">
                                        <strong>Stok Dasar:</strong> ${formatNumber(row.stock_on_hand)} ${row.small_unit_name || '-'}
                                    </div>`;
                                    
                                    // Medium unit conversion if exists
                                    if (row.medium_unit_name && row.small_conversion_qty) {
                                        const mediumQty = Math.floor(row.stock_on_hand / row.small_conversion_qty);
                                        const smallRemainder = row.stock_on_hand % row.small_conversion_qty;
                                        
                                        unitInfo += `<div class="stock-info">
                                            <strong>Konversi Menengah:</strong><br>
                                            ${mediumQty} ${row.medium_unit_name} ${smallRemainder > 0 ? `+ ${smallRemainder} ${row.small_unit_name}` : ''}<br>
                                            <span class="unit-info">(1 ${row.medium_unit_name} = ${formatNumber(row.small_conversion_qty)} ${row.small_unit_name})</span>
                                        </div>`;
                                    }
                                    
                                    // Large unit conversion if exists
                                    if (row.large_unit_name && row.small_conversion_qty) {
                                        const largeQty = Math.floor(row.stock_on_hand / row.small_conversion_qty);
                                        const smallQty = row.stock_on_hand % row.small_conversion_qty;
                                        
                                        unitInfo += `<div class="stock-info">
                                            <strong>Konversi Besar:</strong><br>
                                            ${largeQty} ${row.large_unit_name} ${smallQty > 0 ? `+ ${smallQty} ${row.small_unit_name}` : ''}<br>
                                            <span class="unit-info">(1 ${row.large_unit_name} = 1 ${row.medium_unit_name} = ${formatNumber(row.small_conversion_qty)} ${row.small_unit_name})</span>
                                        </div>`;
                                    }

                                    html += `
                                        <tr>
                                            <td>${row.warehouse_name}</td>
                                            <td>${row.item_name}</td>
                                            <td>${unitInfo}</td>
                                        </tr>`;
                                });
                            } else {
                                html = '<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>';
                            }
                            
                            $("#reportData").html(html);
                            $("#btnExport").prop('disabled', false);
                            
                            // Update pagination info
                            $("#fromItem").text(response.pagination.from || 0);
                            $("#toItem").text(response.pagination.to || 0);
                            $("#totalItems").text(response.pagination.total);

                            // Render pagination
                            renderPagination(response.pagination);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Gagal memuat data'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Ajax Error:', xhr); // Tambahkan log untuk debugging
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data'
                        });
                    },
                    complete: function() {
                        $(".loading-overlay").addClass("d-none");
                        $("#btnLoad").prop("disabled", false);
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

            // Load data when page is loaded
            loadReport();
        });
    </script>
@endsection