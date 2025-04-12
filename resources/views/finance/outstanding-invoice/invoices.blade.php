@extends('layouts.master')

@section('title')
    @lang('translation.outstanding_invoice.invoices.title') | {{ config('app.name') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
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
                        @lang('translation.outstanding_invoice.invoices.title')
                    @endslot
                @endcomponent

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">@lang('translation.outstanding_invoice.invoices.title')</h4>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">@lang('translation.outstanding_invoice.invoices.filter.supplier')</label>
                                        <select class="form-select" id="supplier_id">
                                            <option value="">@lang('translation.outstanding_invoice.invoices.filter.all_suppliers')</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">@lang('translation.outstanding_invoice.invoices.filter.search')</label>
                                        <input type="text" class="form-control" id="search" placeholder="@lang('translation.outstanding_invoice.invoices.filter.search_placeholder')">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-primary w-100" id="btnLoad">
                                            @lang('translation.outstanding_invoice.invoices.filter.load')
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>@lang('translation.outstanding_invoice.invoices.table.invoice_number')</th>
                                                <th>@lang('translation.outstanding_invoice.invoices.table.invoice_date')</th>
                                                <th>@lang('translation.outstanding_invoice.invoices.table.supplier')</th>
                                                <th>@lang('translation.outstanding_invoice.invoices.table.due_date')</th>
                                                <th>@lang('translation.outstanding_invoice.invoices.table.amount')</th>
                                                <th>@lang('translation.outstanding_invoice.invoices.table.remaining_days')</th>
                                            </tr>
                                        </thead>
                                        <tbody id="invoiceData"></tbody>
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
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            $("#btnLoad").click(function() {
                loadData();
            });

            function loadData() {
                const supplierId = $("#supplier_id").val();
                const search = $("#search").val();

                Swal.fire({
                    title: '@lang("translation.outstanding_invoice.message.loading")',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route("finance.outstanding-invoice.invoices.data") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        supplier_id: supplierId,
                        search: search
                    },
                    success: function(response) {
                        Swal.close();

                        if (response.data.length === 0) {
                            $("#invoiceData").html(`
                                <tr>
                                    <td colspan="6" class="text-center">@lang('translation.outstanding_invoice.message.no_data')</td>
                                </tr>
                            `);
                            return;
                        }

                        let html = '';
                        response.data.forEach(function(row) {
                            const remainingDaysClass = row.remaining_days < 0 ? 'text-danger' : 
                                                     row.remaining_days <= 7 ? 'text-warning' : 'text-success';
                            
                            html += `
                                <tr>
                                    <td>${row.invoice_number}</td>
                                    <td>${formatDate(row.invoice_date)}</td>
                                    <td>${row.supplier_name}</td>
                                    <td>${formatDate(row.due_date)}</td>
                                    <td class="text-end">${formatNumber(row.grand_total)}</td>
                                    <td class="text-center ${remainingDaysClass}">${row.remaining_days}</td>
                                </tr>
                            `;
                        });
                        
                        $("#invoiceData").html(html);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '@lang("translation.outstanding_invoice.message.error_loading")'
                        });
                    }
                });
            }

            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID').format(number || 0);
            }

            function formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                }).split('/').join('-');
            }

            // Load data when page is loaded
            loadData();
        });
    </script>
@endsection 