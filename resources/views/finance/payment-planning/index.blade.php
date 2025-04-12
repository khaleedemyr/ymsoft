@extends('layouts.master')

@section('title')
    @lang('translation.payment_planning.title') | {{ config('app.name') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                @component('components.breadcrumb')
                    @slot('li_1')
                        @lang('translation.finance.title')
                    @endslot
                    @slot('title')
                        @lang('translation.payment_planning.title')
                    @endslot
                @endcomponent

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card" id="tasksList">
                            <div class="card-header border-0">
                                <div class="d-flex align-items-center">
                                    <h5 class="card-title mb-0 flex-grow-1">@lang('translation.payment_planning.title')</h5>
                                </div>
                            </div>
                            <div class="card-body border border-dashed border-end-0 border-start-0">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">@lang('translation.payment_planning.filter.start_date')</label>
                                        <input type="text" class="form-control flatpickr" id="start_date" placeholder="@lang('translation.payment_planning.filter.start_date')">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">@lang('translation.payment_planning.filter.end_date')</label>
                                        <input type="text" class="form-control flatpickr" id="end_date" placeholder="@lang('translation.payment_planning.filter.end_date')">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">@lang('translation.payment_planning.filter.search')</label>
                                        <input type="text" class="form-control" id="search" placeholder="@lang('translation.payment_planning.filter.search')">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-primary flex-grow-1" id="btnLoad">
                                                @lang('translation.payment_planning.filter.load')
                                            </button>
                                            <button type="button" class="btn btn-success" id="btnExport" disabled>
                                                <i class="ri-file-excel-2-line align-bottom me-1"></i>
                                                @lang('translation.payment_planning.export.title')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Weekly Summary Section -->
                                <div class="mb-4">
                                    <h6>@lang('translation.payment_planning.weekly_summary')</h6>
                                    <div id="weeklySummary" class="table-responsive">
                                        <!-- Weekly summary data will be loaded here -->
                                    </div>
                                </div>

                                <!-- Detailed Table Section -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-nowrap align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>@lang('translation.payment_planning.table.contra_bon_number')</th>
                                                <th>@lang('translation.payment_planning.table.supplier')</th>
                                                <th>@lang('translation.payment_planning.table.issue_date')</th>
                                                <th>@lang('translation.payment_planning.table.due_date')</th>
                                                <th>@lang('translation.payment_planning.table.days_until_due')</th>
                                                <th>@lang('translation.payment_planning.table.total_amount')</th>
                                                <th>@lang('translation.payment_planning.table.paid_amount')</th>
                                                <th>@lang('translation.payment_planning.table.remaining_amount')</th>
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

            // Handle load button click
            $("#btnLoad").click(function() {
                loadReport();
            });

            // Handle export button click
            $("#btnExport").click(function() {
                const startDate = $("#start_date").val();
                const endDate = $("#end_date").val();
                const search = $("#search").val();

                if (!startDate || !endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '@lang("translation.payment_planning.message.select_date")'
                    });
                    return;
                }

                Swal.fire({
                    title: '@lang("translation.payment_planning.message.loading")',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Gunakan AJAX untuk mendapatkan URL download
                $.ajax({
                    url: '{{ route("finance.payment-planning.export") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        start_date: startDate,
                        end_date: endDate,
                        search: search
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response, status, xhr) {
                        // Tutup loading
                        Swal.close();

                        // Buat URL untuk file
                        const url = window.URL.createObjectURL(new Blob([response]));
                        
                        // Buat link download
                        const link = document.createElement('a');
                        link.href = url;
                        
                        // Dapatkan filename dari header response
                        const contentDisposition = xhr.getResponseHeader('Content-Disposition');
                        let filename = 'payment_planning.xlsx';
                        if (contentDisposition && contentDisposition.indexOf('attachment') !== -1) {
                            const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                            const matches = filenameRegex.exec(contentDisposition);
                            if (matches != null && matches[1]) {
                                filename = matches[1].replace(/['"]/g, '');
                            }
                        }
                        
                        link.setAttribute('download', filename);
                        document.body.appendChild(link);
                        link.click();
                        
                        // Cleanup
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(link);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '@lang("translation.payment_planning.message.error_export")'
                        });
                    }
                });
            });

            function loadReport() {
                const startDate = $("#start_date").val();
                const endDate = $("#end_date").val();
                const search = $("#search").val();

                if (!startDate || !endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '@lang("translation.payment_planning.message.select_date")'
                    });
                    return;
                }

                // Show loading state
                Swal.fire({
                    title: '@lang("translation.payment_planning.message.loading")',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route("finance.payment-planning.data") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        start_date: startDate,
                        end_date: endDate,
                        search: search
                    },
                    success: function(response) {
                        Swal.close();

                        if (response.data.length === 0) {
                            $("#weeklySummary").html('<div class="alert alert-info">@lang("translation.payment_planning.message.no_data")</div>');
                            $("#reportData").html('');
                            $("#btnExport").prop('disabled', true);
                            return;
                        }

                        // Render weekly summary
                        let weeklyHtml = '<table class="table table-bordered mb-0">';
                        weeklyHtml += '<thead><tr><th>@lang("translation.payment_planning.table.week")</th><th>@lang("translation.payment_planning.table.period")</th><th>@lang("translation.payment_planning.table.total")</th></tr></thead><tbody>';
                        
                        response.weekly_totals.forEach(function(week, index) {
                            weeklyHtml += `
                                <tr>
                                    <td>@lang("translation.payment_planning.table.week") ${index + 1}</td>
                                    <td>${week.start_date} - ${week.end_date}</td>
                                    <td class="text-end">${formatNumber(week.total_amount)}</td>
                                </tr>`;
                        });
                        weeklyHtml += '</tbody></table>';
                        $("#weeklySummary").html(weeklyHtml);

                        // Render detailed data
                        let detailHtml = '';
                        response.data.forEach(function(row) {
                            detailHtml += `
                                <tr>
                                    <td>${row.contra_bon_number}</td>
                                    <td>${row.supplier_name}</td>
                                    <td>${row.issue_date}</td>
                                    <td>${row.due_date}</td>
                                    <td class="text-center">${row.days_until_due}</td>
                                    <td class="text-end">${formatNumber(row.total_amount)}</td>
                                    <td class="text-end">${formatNumber(row.paid_amount)}</td>
                                    <td class="text-end">${formatNumber(row.remaining_amount)}</td>
                                </tr>`;
                        });
                        
                        $("#reportData").html(detailHtml);
                        $("#btnExport").prop('disabled', false);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '@lang("translation.payment_planning.message.error_loading")'
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