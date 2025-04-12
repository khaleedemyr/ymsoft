@extends('layouts.master')

@section('title')
    {{ trans('translation.aging_report.title') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">{{ trans('translation.aging_report.title') }}</h5>
                        <div class="flex-shrink-0">
                            <form id="exportForm" action="{{ route('finance.aging-report.export') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="search" id="exportSearch">
                                <button type="submit" class="btn btn-success" id="btnExport">
                                    <i class="ri-file-excel-2-line align-bottom me-1"></i>
                                    {{ trans('translation.aging_report.export.title') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ trans('translation.search') }}</label>
                            <input type="text" class="form-control" id="search" placeholder="{{ trans('translation.search_supplier') }}">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-nowrap" id="aging-table">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ trans('translation.aging_report.table.supplier') }}</th>
                                    <th class="text-end">{{ trans('translation.aging_report.table.current') }}</th>
                                    <th class="text-end">{{ trans('translation.aging_report.table.1_30') }}</th>
                                    <th class="text-end">{{ trans('translation.aging_report.table.31_60') }}</th>
                                    <th class="text-end">{{ trans('translation.aging_report.table.61_90') }}</th>
                                    <th class="text-end">{{ trans('translation.aging_report.table.over_90') }}</th>
                                    <th class="text-end">{{ trans('translation.aging_report.table.total') }}</th>
                                </tr>
                            </thead>
                            <tbody id="reportData">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
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
            let dataTable;
            let searchTimer;

            // Fungsi debounce untuk menunda eksekusi
            function debounce(func, wait) {
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(searchTimer);
                        func(...args);
                    };
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(later, wait);
                };
            }

            function loadReport() {
                const search = $("#search").val();

                $.ajax({
                    url: '{{ route("finance.aging-report.data") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        search: search
                    },
                    beforeSend: function() {
                        // Tambahkan loading state
                        $("#reportData").html('<tr><td colspan="7" class="text-center">Loading...</td></tr>');
                    },
                    success: function(response) {
                        if (dataTable) {
                            dataTable.destroy();
                        }

                        let html = '';
                        let totalCurrent = 0;
                        let total1_30 = 0;
                        let total31_60 = 0;
                        let total61_90 = 0;
                        let totalOver90 = 0;
                        let grandTotal = 0;

                        response.data.forEach(function(row) {
                            html += `
                                <tr>
                                    <td>${row.supplier_name}</td>
                                    <td class="text-end">${formatNumber(row.current)}</td>
                                    <td class="text-end">${formatNumber(row.days_1_30)}</td>
                                    <td class="text-end">${formatNumber(row.days_31_60)}</td>
                                    <td class="text-end">${formatNumber(row.days_61_90)}</td>
                                    <td class="text-end">${formatNumber(row.days_over_90)}</td>
                                    <td class="text-end">${formatNumber(row.total)}</td>
                                </tr>`;

                            totalCurrent += parseFloat(row.current);
                            total1_30 += parseFloat(row.days_1_30);
                            total31_60 += parseFloat(row.days_31_60);
                            total61_90 += parseFloat(row.days_61_90);
                            totalOver90 += parseFloat(row.days_over_90);
                            grandTotal += parseFloat(row.total);
                        });

                        // Add totals row
                        html += `
                            <tr class="table-info">
                                <th>{{ trans('translation.total') }}</th>
                                <th class="text-end">${formatNumber(totalCurrent)}</th>
                                <th class="text-end">${formatNumber(total1_30)}</th>
                                <th class="text-end">${formatNumber(total31_60)}</th>
                                <th class="text-end">${formatNumber(total61_90)}</th>
                                <th class="text-end">${formatNumber(totalOver90)}</th>
                                <th class="text-end">${formatNumber(grandTotal)}</th>
                            </tr>`;
                        
                        $("#reportData").html(html);

                        // Initialize DataTable
                        dataTable = $('#aging-table').DataTable({
                            pageLength: 25,
                            ordering: true,
                            order: [[6, 'desc']], // Sort by total column
                            searching: false, // Matikan fitur search bawaan DataTables
                            language: {
                                url: "{{ URL::asset('build/libs/datatables/id.json') }}"
                            },
                            dom: 'Bfrtip',
                            buttons: [
                                {
                                    extend: 'excel',
                                    text: '{{ trans("translation.export_excel") }}',
                                    className: 'btn btn-success d-none',
                                    title: '{{ trans("translation.aging_report.title") }}',
                                    exportOptions: {
                                        columns: ':visible'
                                    }
                                }
                            ]
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to load data'
                        });
                    }
                });
            }

            // Event listener untuk input search dengan debounce
            $("#search").on('input', debounce(function() {
                loadReport();
            }, 500)); // Delay 500ms

            // Update fungsi export
            $("#btnExport").click(function(e) {
                e.preventDefault();
                
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    html: 'Please wait while we prepare your download.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Get current search value
                const search = $("#search").val();
                $("#exportSearch").val(search);

                // Submit form
                $("#exportForm").submit();

                // Hide loading after a short delay
                setTimeout(() => {
                    Swal.close();
                }, 1000);
            });

            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID').format(number || 0);
            }

            // Load initial data
            loadReport();
        });
    </script>
@endsection 