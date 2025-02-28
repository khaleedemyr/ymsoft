@extends('layouts.master')

@section('title')
    @lang('translation.reports.fj.title') | {{ config('app.name') }}
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
        .table-fixed-columns td:nth-child(1),
        .table-fixed-columns th:nth-child(2),
        .table-fixed-columns td:nth-child(2) {
            position: sticky;
            background-color: #fff;
            z-index: 1;
        }

        .table-fixed-columns th:nth-child(1),
        .table-fixed-columns td:nth-child(1) {
            left: 0;
        }

        .table-fixed-columns th:nth-child(2),
        .table-fixed-columns td:nth-child(2) {
            left: 200px; /* Sesuaikan dengan lebar kolom pertama */
        }

        /* Header styling */
        .table-fixed-columns thead th {
            background-color: #f3f6f9;
            z-index: 2;
        }

        /* Shadow effect for fixed columns */
        .table-fixed-columns td:nth-child(2)::after,
        .table-fixed-columns th:nth-child(2)::after {
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
            min-width: 200px; /* Customer column */
        }
        .table-fixed-columns th:nth-child(2),
        .table-fixed-columns td:nth-child(2) {
            min-width: 150px; /* Type column */
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                @component('components.breadcrumb')
                    @slot('li_1')
                        @lang('translation.reports.title')
                    @endslot
                    @slot('title')
                        @lang('translation.reports.fj.title')
                    @endslot
                @endcomponent

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card" id="tasksList">
                            <div class="card-header border-0">
                                <div class="d-flex align-items-center">
                                    <h5 class="card-title mb-0 flex-grow-1">@lang('translation.reports.fj.title')</h5>
                                </div>
                            </div>
                            <div class="card-body border border-dashed border-end-0 border-start 0">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">@lang('translation.reports.fj.filter.start_date')</label>
                                        <input type="text" class="form-control flatpickr" id="start_date" placeholder="@lang('translation.reports.fj.filter.start_date')">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">@lang('translation.reports.fj.filter.end_date')</label>
                                        <input type="text" class="form-control flatpickr" id="end_date" placeholder="@lang('translation.reports.fj.filter.end_date')">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">@lang('translation.reports.fj.filter.search')</label>
                                        <input type="text" class="form-control" id="search" placeholder="@lang('translation.reports.fj.filter.search')">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-primary flex-grow-1" id="btnLoad">
                                                @lang('translation.reports.fj.filter.load')
                                            </button>
                                            <button type="button" class="btn btn-success" id="btnExport" disabled>
                                                <i class="ri-file-excel-2-line align-bottom me-1"></i>
                                                @lang('translation.reports.fj.export.title')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-container">
                                    <table class="table table-bordered table-nowrap align-middle mb-0 table-fixed-columns">
                                        <thead class="table-light">
                                            <tr>
                                                <th>@lang('translation.reports.fj.table.customer')</th>
                                                <th>@lang('translation.reports.fj.table.type')</th>
                                                <th>@lang('translation.reports.fj.table.line_total')</th>
                                                @foreach($subCategories as $subCategory)
                                                    <th>{{ $subCategory->name }}</th>
                                                @endforeach
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
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize flatpickr
            $(".flatpickr").flatpickr({
                dateFormat: "Y-m-d"
            });

            // Load report button click
            $("#btnLoad").click(function() {
                loadReport();
            });

            // Export to Excel
            $("#btnExport").click(function() {
                const startDate = $("#start_date").val();
                const endDate = $("#end_date").val();
                const search = $("#search").val();

                if (!startDate || !endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '@lang("translation.reports.fj.please_select_date")'
                    });
                    return;
                }

                // Buat form temporary untuk submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("reports.fj.export") }}';
                
                // Tambahkan CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Tambahkan parameter
                const startDateInput = document.createElement('input');
                startDateInput.type = 'hidden';
                startDateInput.name = 'start_date';
                startDateInput.value = startDate;
                form.appendChild(startDateInput);

                const endDateInput = document.createElement('input');
                endDateInput.type = 'hidden';
                endDateInput.name = 'end_date';
                endDateInput.value = endDate;
                form.appendChild(endDateInput);

                const searchInput = document.createElement('input');
                searchInput.type = 'hidden';
                searchInput.name = 'search';
                searchInput.value = search;
                form.appendChild(searchInput);

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            });

            function loadReport() {
                const startDate = $("#start_date").val();
                const endDate = $("#end_date").val();
                const search = $("#search").val();

                if (!startDate || !endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Pilih tanggal awal dan akhir terlebih dahulu!'
                    });
                    return;
                }

                $.ajax({
                    url: '{{ route("reports.fj.data") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        start_date: startDate,
                        end_date: endDate,
                        search: search
                    },
                    success: function(response) {
                        let html = '';
                        response.data.forEach(function(row) {
                            html += `
                                <tr>
                                    <td>${row.customer_name}</td>
                                    <td>${row.customer_type}</td>
                                    <td class="text-end">${formatNumber(row.line_total)}</td>`;
                            
                            response.subCategories.forEach(function(subCategory) {
                                html += `<td class="text-end">${formatNumber(row['category_' + subCategory.id])}</td>`;
                            });
                            
                            html += '</tr>';
                        });
                        
                        $("#reportData").html(html);
                        $("#btnExport").prop('disabled', false);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data report!'
                        });
                        $("#btnExport").prop('disabled', true);
                    }
                });
            }

            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID').format(number || 0);
            }
        });
    </script>
@endsection 