@extends('layouts.master')

@section('title')
    @lang('translation.payment_history.summary.title') | {{ config('app.name') }}
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
                        @lang('translation.payment_history.summary.title')
                    @endslot
                @endcomponent

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card" id="tasksList">
                            <div class="card-header border-0">
                                <div class="d-flex align-items-center">
                                    <h5 class="card-title mb-0 flex-grow-1">
                                        @lang('translation.payment_history.summary.title')
                                    </h5>
                                </div>
                            </div>
                            <div class="card-body border border-dashed border-end-0 border-start-0">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">@lang('translation.payment_history.filter.start_date')</label>
                                        <input type="text" class="form-control flatpickr" id="start_date" placeholder="@lang('translation.payment_history.filter.start_date')">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">@lang('translation.payment_history.filter.end_date')</label>
                                        <input type="text" class="form-control flatpickr" id="end_date" placeholder="@lang('translation.payment_history.filter.end_date')">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex">
                                            <button class="btn btn-primary w-100" type="button" id="btnLoad">
                                                @lang('translation.payment_history.filter.load')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Statistics Cards -->
                                <div class="row mb-4" id="statisticsCards">
                                    <!-- Will be populated by JavaScript -->
                                </div>

                                <!-- Summary Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>@lang('translation.payment_history.summary.table.period')</th>
                                                <th>@lang('translation.payment_history.summary.table.total_suppliers')</th>
                                                <th>@lang('translation.payment_history.summary.table.total_payments')</th>
                                                <th>@lang('translation.payment_history.summary.table.total_amount')</th>
                                                <th>@lang('translation.payment_history.summary.table.payment_methods')</th>
                                            </tr>
                                        </thead>
                                        <tbody id="summaryData"></tbody>
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
    <script>
        $(document).ready(function() {
            $(".flatpickr").flatpickr({
                dateFormat: "Y-m-d"
            });

            $("#btnLoad").click(function() {
                loadData();
            });

            function loadData() {
                const startDate = $("#start_date").val();
                const endDate = $("#end_date").val();

                if (!startDate || !endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '@lang("translation.payment_history.message.select_date")'
                    });
                    return;
                }

                Swal.fire({
                    title: '@lang("translation.payment_history.message.loading")',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route("finance.payment-history.summary.data") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        Swal.close();

                        // Update statistics cards
                        const stats = response.statistics;
                        $("#statisticsCards").html(`
                            <div class="col-md-3">
                                <div class="card mini-stats-wid">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted fw-medium">@lang('translation.payment_history.summary.stats.total_amount')</p>
                                                <h4 class="mb-0">${formatNumber(stats.total_amount)}</h4>
                                            </div>
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                                <span class="avatar-title">
                                                    <i class="ri-money-dollar-circle-line font-size-14"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card mini-stats-wid">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted fw-medium">@lang('translation.payment_history.summary.stats.total_payments')</p>
                                                <h4 class="mb-0">${stats.total_payments}</h4>
                                            </div>
                                            <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                                <span class="avatar-title">
                                                    <i class="ri-file-list-3-line font-size-14"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card mini-stats-wid">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted fw-medium">@lang('translation.payment_history.summary.stats.avg_per_payment')</p>
                                                <h4 class="mb-0">${formatNumber(stats.avg_amount_per_payment)}</h4>
                                            </div>
                                            <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                                <span class="avatar-title">
                                                    <i class="ri-bar-chart-line font-size-14"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card mini-stats-wid">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted fw-medium">@lang('translation.payment_history.summary.stats.avg_suppliers')</p>
                                                <h4 class="mb-0">${formatNumber(stats.avg_suppliers_per_month, 0)}</h4>
                                            </div>
                                            <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                                <span class="avatar-title">
                                                    <i class="ri-group-line font-size-14"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);

                        // Update summary table
                        if (response.data.length === 0) {
                            $("#summaryData").html(`
                                <tr>
                                    <td colspan="5" class="text-center">@lang('translation.payment_history.message.no_data')</td>
                                </tr>
                            `);
                            return;
                        }

                        let html = '';
                        response.data.forEach(function(row) {
                            html += `
                                <tr>
                                    <td>${formatPeriod(row.period)}</td>
                                    <td class="text-center">${row.total_suppliers}</td>
                                    <td class="text-center">${row.total_payments}</td>
                                    <td class="text-end">${formatNumber(row.total_amount)}</td>
                                    <td>${formatPaymentMethods(row.payment_methods)}</td>
                                </tr>
                            `;
                        });
                        
                        $("#summaryData").html(html);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '@lang("translation.payment_history.message.error_loading")'
                        });
                    }
                });
            }

            function formatNumber(number, decimals = 2) {
                return new Intl.NumberFormat('id-ID').format(Number(number || 0).toFixed(decimals));
            }

            function formatPeriod(period) {
                const [year, month] = period.split('-');
                const date = new Date(year, month - 1);
                return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long' });
            }

            function formatPaymentMethods(methods) {
                return methods.split(',').join(', ');
            }
        });
    </script>
@endsection 