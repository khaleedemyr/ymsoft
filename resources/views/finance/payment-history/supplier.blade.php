@extends('layouts.master')

@section('title')
    @lang('translation.payment_history.supplier.title') | {{ config('app.name') }}
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
                        @lang('translation.payment_history.supplier.title')
                    @endslot
                @endcomponent

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card" id="tasksList">
                            <div class="card-header border-0">
                                <div class="d-flex align-items-center">
                                    <h5 class="card-title mb-0 flex-grow-1">
                                        @lang('translation.payment_history.supplier.title')
                                    </h5>
                                </div>
                            </div>
                            <div class="card-body border border-dashed border-end-0 border-start-0">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">@lang('translation.payment_history.filter.start_date')</label>
                                        <input type="text" class="form-control flatpickr" id="start_date" placeholder="@lang('translation.payment_history.filter.start_date')">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">@lang('translation.payment_history.filter.end_date')</label>
                                        <input type="text" class="form-control flatpickr" id="end_date" placeholder="@lang('translation.payment_history.filter.end_date')">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">@lang('translation.payment_history.filter.supplier')</label>
                                        <select class="form-select" id="supplier_id">
                                            <option value="">@lang('translation.payment_history.filter.all_suppliers')</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">@lang('translation.payment_history.filter.search')</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="search" placeholder="@lang('translation.payment_history.filter.search_placeholder')">
                                            <button class="btn btn-primary" type="button" id="btnLoad">
                                                @lang('translation.payment_history.filter.load')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>@lang('translation.payment_history.supplier.table.payment_number')</th>
                                                <th>@lang('translation.payment_history.supplier.table.payment_date')</th>
                                                <th>@lang('translation.payment_history.supplier.table.supplier')</th>
                                                <th>@lang('translation.payment_history.supplier.table.contra_bon')</th>
                                                <th>@lang('translation.payment_history.supplier.table.amount')</th>
                                                <th>@lang('translation.payment_history.supplier.table.payment_method')</th>
                                                <th>@lang('translation.payment_history.supplier.table.created_by')</th>
                                            </tr>
                                        </thead>
                                        <tbody id="paymentData"></tbody>
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
                const supplierId = $("#supplier_id").val();
                const search = $("#search").val();

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
                    url: '{{ route("finance.payment-history.supplier.data") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        start_date: startDate,
                        end_date: endDate,
                        supplier_id: supplierId,
                        search: search
                    },
                    success: function(response) {
                        Swal.close();

                        if (response.data.length === 0) {
                            $("#paymentData").html(`
                                <tr>
                                    <td colspan="7" class="text-center">@lang('translation.payment_history.message.no_data')</td>
                                </tr>
                            `);
                            return;
                        }

                        let html = '';
                        response.data.forEach(function(row) {
                            html += `
                                <tr>
                                    <td>${row.payment_number}</td>
                                    <td>${row.payment_date}</td>
                                    <td>${row.supplier_name}</td>
                                    <td>${row.contra_bon_number}</td>
                                    <td class="text-end">${formatNumber(row.amount)}</td>
                                    <td>${row.payment_method}</td>
                                    <td>${row.created_by}</td>
                                </tr>
                            `;
                        });
                        
                        $("#paymentData").html(html);
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

            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID').format(number || 0);
            }
        });
    </script>
@endsection 