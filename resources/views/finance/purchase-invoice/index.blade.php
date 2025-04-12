@extends('layouts.master')

@section('title')
    {{ trans('translation.purchase_invoice.list') }}
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    
    <style>
        .search-box {
            min-width: 200px;
        }
        
        .dataTables_filter {
            display: none;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        @media (max-width: 576px) {
            .card-tools {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .search-box {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('translation.purchase_invoice.title') }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div>
                        @component('components.breadcrumb')
                            @slot('li_1')
                                {{ trans('translation.finance.title') }}
                            @endslot
                            @slot('title')
                                {{ trans('translation.purchase_invoice.title') }}
                            @endslot
                        @endcomponent

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <h6 class="card-title mb-0 flex-grow-1">{{ trans('translation.purchase_invoice.list') }}</h6>
                                            <div class="flex-shrink-0">
                                                <div class="d-flex flex-wrap gap-2">
                                                    <div class="search-box">
                                                        <input type="text" class="form-control search" placeholder="{{ trans('translation.purchase_invoice.table.search_placeholder') }}">
                                                    </div>
                                                    <div class="input-group" style="width: auto;">
                                                        <input type="date" class="form-control" name="from" id="from">
                                                        <input type="date" class="form-control" name="to" id="to">
                                                        <button type="button" id="filter" class="btn btn-primary">{{ trans('translation.filter') }}</button>
                                                        <button type="button" id="refresh" class="btn btn-default">{{ trans('translation.refresh') }}</button>
                                                    </div>
                                                    <a href="{{ route('finance.purchase-invoices.create') }}" class="btn btn-primary">
                                                        <i class="ri-add-line align-bottom me-1"></i> {{ trans('translation.purchase_invoice.add') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="table-responsive table-card">
                                            <table class="table table-bordered" id="invoiceTable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ trans('translation.purchase_invoice.table.invoice_number') }}</th>
                                                        <th>{{ trans('translation.purchase_invoice.table.invoice_date') }}</th>
                                                        <th>{{ trans('translation.purchase_invoice.table.po_number') }}</th>
                                                        <th>{{ trans('translation.purchase_invoice.table.gr_number') }}</th>
                                                        <th>{{ trans('translation.purchase_invoice.table.supplier') }}</th>
                                                        <th>{{ trans('translation.purchase_invoice.table.warehouse') }}</th>
                                                        <th>{{ trans('translation.purchase_invoice.table.total_amount') }}</th>
                                                        <th>{{ trans('translation.purchase_invoice.table.payment_status') }}</th>
                                                        <th>{{ trans('translation.purchase_invoice.table.contra_bon_number') }}</th>
                                                        <th>{{ trans('translation.purchase_invoice.table.status') }}</th>
                                                        <th>{{ trans('translation.purchase_invoice.table.action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($purchaseInvoices as $invoice)
                                                    <tr>
                                                        <td>{{ $invoice->invoice_number }}</td>
                                                        <td>{{ $invoice->invoice_date instanceof \DateTime ? $invoice->invoice_date->format('d/m/Y') : date('d/m/Y', strtotime($invoice->invoice_date)) }}</td>
                                                        <td>{{ optional($invoice->goodReceive->purchaseOrder)->po_number ?? '-' }}</td>
                                                        <td>{{ optional($invoice->goodReceive)->gr_number ?? '-' }}</td>
                                                        <td>{{ optional($invoice->supplier)->name ?? '-' }}</td>
                                                        <td>{{ optional($invoice->warehouse)->name ?? '-' }}</td>
                                                        <td class="text-end">{{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
                                                        <td>
                                                            @if(isset($invoice->payment_status))
                                                                @switch($invoice->payment_status)
                                                                    @case('unpaid')
                                                                        <span class="badge bg-danger">{{ trans('translation.purchase_invoice.payment_status.unpaid') }}</span>
                                                                        @break
                                                                    @case('partial')
                                                                        <span class="badge bg-warning text-dark">{{ trans('translation.purchase_invoice.payment_status.partial') }}</span>
                                                                        @break
                                                                    @case('paid')
                                                                        <span class="badge bg-success">{{ trans('translation.purchase_invoice.payment_status.paid') }}</span>
                                                                        @break
                                                                    @default
                                                                        <span class="badge bg-secondary">{{ trans('translation.purchase_invoice.payment_status.unpaid') }}</span>
                                                                @endswitch
                                                            @else
                                                                <span class="badge bg-secondary">{{ trans('translation.purchase_invoice.payment_status.unpaid') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $invoice->contra_bon_number ?? '-' }}</td>
                                                        <td>
                                                            @switch($invoice->status)
                                                                @case('draft')
                                                                    <span class="badge bg-warning text-dark">{{ trans('translation.purchase_invoice.status.draft') }}</span>
                                                                    @break
                                                                @case('approved')
                                                                    <span class="badge bg-success">{{ trans('translation.purchase_invoice.status.approved') }}</span>
                                                                    @break
                                                                @case('rejected')
                                                                    <span class="badge bg-danger">{{ trans('translation.purchase_invoice.status.rejected') }}</span>
                                                                    @break
                                                                @case('cancelled')
                                                                    <span class="badge bg-secondary">{{ trans('translation.purchase_invoice.status.cancelled') }}</span>
                                                                    @break
                                                                @default
                                                                    <span class="badge bg-secondary">{{ ucfirst($invoice->status) }}</span>
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <a href="{{ route('finance.purchase-invoices.show', $invoice->id) }}" class="btn btn-sm btn-info">
                                                                    <i class="ri-eye-line"></i>
                                                                </a>
                                                                @if($invoice->status == 'draft')
                                                                <a href="{{ route('finance.purchase-invoices.edit', $invoice->id) }}" class="btn btn-sm btn-primary">
                                                                    <i class="ri-pencil-line"></i>
                                                                </a>
                                                                <button type="button" onclick="deleteInvoice('{{ $invoice->id }}', '{{ $invoice->invoice_number }}')" class="btn btn-sm btn-danger">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            
                                            @if(count($purchaseInvoices) == 0)
                                                <div class="alert alert-info mt-3 text-center">
                                                    {{ trans('translation.purchase_invoice.message.no_data') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
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
<script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
<script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#invoiceTable').DataTable({
        dom: 'rt<"bottom"ip>',
        pageLength: 10,
        ordering: true,
        responsive: true,
        order: [[1, 'desc']],
        language: {
            emptyTable: "{{ trans('translation.purchase_invoice.message.no_data') }}",
            info: "{{ trans('translation.showing') }} _START_ {{ trans('translation.to') }} _END_ {{ trans('translation.of') }} _TOTAL_ {{ trans('translation.entries') }}",
            infoEmpty: "{{ trans('translation.showing') }} 0 {{ trans('translation.to') }} 0 {{ trans('translation.of') }} 0 {{ trans('translation.entries') }}",
            infoFiltered: "({{ trans('translation.filtered_from') }} _MAX_ {{ trans('translation.total_entries') }})",
            lengthMenu: "{{ trans('translation.show') }} _MENU_ {{ trans('translation.entries') }}",
            search: "{{ trans('translation.search') }}:",
            zeroRecords: "{{ trans('translation.no_matching_records') }}",
            paginate: {
                first: "{{ trans('translation.first') }}",
                last: "{{ trans('translation.last') }}",
                next: "{{ trans('translation.next') }}",
                previous: "{{ trans('translation.previous') }}"
            }
        }
    });

    // Live search
    $('.search').keyup(function() {
        table.search($(this).val()).draw();
    });

    // Date filter
    $('#filter').click(function() {
        table.draw();
    });

    // Refresh button
    $('#refresh').click(function() {
        $('#from').val('');
        $('#to').val('');
        $('.search').val('');
        table.search('').draw();
    });

    // Set default date range (hari ini)
    var today = new Date().toISOString().split('T')[0];
    $('#from').val(today);
    $('#to').val(today);
    
    // Trigger filter on load
    table.draw();
});

// Setup CSRF token untuk semua request AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Fungsi Delete Invoice
window.deleteInvoice = function(id, invoiceNumber) {
    Swal.fire({
        title: "{{ trans('translation.purchase_invoice.message.confirm_delete') }}",
        text: invoiceNumber,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "{{ trans('translation.yes') }}",
        cancelButtonText: "{{ trans('translation.no') }}",
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/finance/purchase-invoices/${id}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: "{{ trans('translation.success') }}",
                            text: "{{ trans('translation.purchase_invoice.message.success_delete') }}"
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire("{{ trans('translation.error') }}", response.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseJSON);
                    Swal.fire("{{ trans('translation.error') }}", "{{ trans('translation.purchase_invoice.message.error_delete') }}", 'error');
                }
            });
        }
    });
}
</script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 