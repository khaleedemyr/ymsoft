@extends('layouts.master')

@section('title')
    {{ trans('translation.purchase_order.list') }}
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
                    <h1 class="m-0">{{ trans('translation.purchase_order.title') }}</h1>
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
                                {{ trans('translation.purchasing.title') }}
                            @endslot
                            @slot('title')
                                {{ trans('translation.purchase_order.title') }}
                            @endslot
                        @endcomponent

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center">
                                        <h6 class="card-title flex-grow-1 mb-0">{{ trans('translation.purchase_order.list') }}</h6>
                                        <div class="flex-shrink-0">
                                            <div class="d-flex flex-wrap gap-2">
                                                <div class="search-box">
                                                    <input type="text" class="form-control search" placeholder="{{ trans('translation.purchase_order.table.search_placeholder') }}">
                                                </div>
                                                <div class="input-group" style="width: auto;">
                                                    <input type="date" class="form-control" name="from" id="from">
                                                    <input type="date" class="form-control" name="to" id="to">
                                                    <button type="button" id="filter" class="btn btn-primary">{{ trans('translation.filter') }}</button>
                                                    <button type="button" id="refresh" class="btn btn-default">{{ trans('translation.refresh') }}</button>
                                                </div>
                                                <a href="{{ route('purchasing.purchase-orders.create') }}" class="btn btn-primary">
                                                    <i class="ri-add-line align-bottom me-1"></i> {{ trans('translation.purchase_order.add') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="table-responsive table-card">
                                            <table class="table table-bordered" id="poTable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ trans('translation.purchase_order.table.po_number') }}</th>
                                                        <th>{{ trans('translation.purchase_order.table.po_date') }}</th>
                                                        <th>{{ trans('translation.purchase_order.table.pr_number') }}</th>
                                                        <th>{{ trans('translation.purchase_order.table.supplier') }}</th>
                                                        <th>{{ trans('translation.purchase_order.table.warehouse') }}</th>
                                                        <th>{{ trans('translation.purchase_order.table.department') }}</th>
                                                        <th>{{ trans('translation.purchase_order.table.status') }}</th>
                                                        <th>{{ trans('translation.purchase_order.table.created_by') }}</th>
                                                        <th>{{ trans('translation.purchase_order.table.created_at') }}</th>
                                                        <th>{{ trans('translation.purchase_order.table.total_amount') }}</th>
                                                        <th>{{ trans('translation.purchase_order.table.action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($purchaseOrders as $po)
                                                    <tr>
                                                        <td>{{ $po->po_number }}</td>
                                                        <td>{{ $po->po_date instanceof \DateTime ? $po->po_date->format('d/m/Y') : date('d/m/Y', strtotime($po->po_date)) }}</td>
                                                        <td>{{ optional($po->purchaseRequisition)->pr_number ?? '-' }}</td>
                                                        <td>{{ optional($po->supplier)->name ?? '-' }}</td>
                                                        <td>{{ optional($po->purchaseRequisition)->warehouse->name ?? '-' }}</td>
                                                        <td>{{ optional($po->purchaseRequisition)->department ?? '-' }}</td>
                                                        <td>
                                                            @switch($po->status)
                                                                @case('draft')
                                                                    <span class="badge bg-warning text-dark">{{ trans('translation.purchase_order.status.draft') }}</span>
                                                                    @break
                                                                @case('approved')
                                                                    <span class="badge bg-success">{{ trans('translation.purchase_order.status.approved') }}</span>
                                                                    @break
                                                                @case('cancelled')
                                                                    <span class="badge bg-danger">{{ trans('translation.purchase_order.status.cancelled') }}</span>
                                                                    @break
                                                                @default
                                                                    <span class="badge bg-secondary">{{ ucfirst($po->status) }}</span>
                                                            @endswitch
                                                        </td>
                                                        <td>{{ optional($po->creator)->nama_lengkap ?? '-' }}</td>
                                                        <td>{{ $po->created_at ? date('d/m/Y H:i', strtotime($po->created_at)) : '-' }}</td>
                                                        <td class="text-end">{{ number_format($po->total, 0, ',', '.') }}</td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <a href="{{ route('purchasing.purchase-orders.show', $po->id) }}" 
                                                                   class="btn btn-sm btn-info">
                                                                    <i class="ri-eye-line"></i>
                                                                </a>
                                                                @if($po->status == 'draft')
                                                                <a href="{{ route('purchasing.purchase-orders.edit', $po->id) }}" 
                                                                   class="btn btn-sm btn-primary">
                                                                    <i class="ri-pencil-line"></i>
                                                                </a>
                                                                <button type="button" 
                                                                        onclick="deletePO('{{ $po->id }}', '{{ $po->po_number }}')"
                                                                        class="btn btn-sm btn-danger">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            
                                            @if(count($purchaseOrders) == 0)
                                                <div class="alert alert-info mt-3 text-center">
                                                    {{ trans('translation.purchase_order.message.no_data') }}
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
    // Debugging
    console.log('Document ready!');
    console.log('Jumlah kolom thead:', $('#poTable thead th').length);
    console.log('Jumlah kolom tbody (row pertama):', $('#poTable tbody tr:first td').length);
    
    // Custom date range filter
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var from = $('#from').val();
        var to = $('#to').val();
        var date = data[1]; // index 1 adalah kolom tanggal PO

        if (from === '' && to === '') return true;
        
        if (from !== '' && to !== '') {
            // Convert date format dd/mm/yyyy to yyyy-mm-dd for comparison
            var parts = date.split('/');
            if (parts.length < 3) return true; // Skip jika format tidak valid
            var dateStr = parts[2] + '-' + parts[1] + '-' + parts[0];
            return (dateStr >= from && dateStr <= to);
        }
        
        return true;
    });

    var table = $('#poTable').DataTable({
        dom: 'rt<"bottom"ip>',
        pageLength: 10,
        ordering: true,
        responsive: true,
        order: [[1, 'desc']], // Order by tanggal PO descending
        language: {
            emptyTable: "{{ trans('translation.purchase_order.message.no_data') }}",
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
        // Clear inputs
        $('#from').val('');
        $('#to').val('');
        $('.search').val('');
        
        // Reset table
        table
            .search('')
            .order([[1, 'desc']])
            .draw();
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

// Fungsi Delete PO
window.deletePO = function(id, poNumber) {
    Swal.fire({
        title: "{{ trans('translation.purchase_order.message.confirm_delete') }}",
        text: poNumber,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "{{ trans('translation.yes') }}",
        cancelButtonText: "{{ trans('translation.no') }}",
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/purchasing/purchase-orders/${id}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: "{{ trans('translation.success') }}",
                            text: "{{ trans('translation.purchase_order.message.success_delete') }}"
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire("{{ trans('translation.error') }}", response.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseJSON);
                    Swal.fire("{{ trans('translation.error') }}", "{{ trans('translation.purchase_order.message.error_delete') }}", 'error');
                }
            });
        }
    });
}
</script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 