@extends('layouts.master')

@section('title')
    {{ __('translation.purchase_requisition.list') }}
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
                    <h1 class="m-0">{{ __('translation.purchase_requisition.title') }}</h1>
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
                                Purchasing
                            @endslot
                            @slot('title')
                                Purchase Requisition
                            @endslot
                        @endcomponent

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center">
                                        <h6 class="card-title flex-grow-1 mb-0">{{ __('translation.purchase_requisition.list') }}</h6>
                                        <div class="flex-shrink-0">
                                            <div class="d-flex flex-wrap gap-2">
                                                <div class="search-box">
                                                    <input type="text" class="form-control search" placeholder="{{ __('translation.purchase_requisition.table.search_placeholder') }}">
                                                </div>
                                                <div class="input-group" style="width: auto;">
                                                    <input type="date" class="form-control" name="from" id="from">
                                                    <input type="date" class="form-control" name="to" id="to">
                                                    <button type="button" id="filter" class="btn btn-primary">{{ __('translation.floor_order.index.filter') }}</button>
                                                    <button type="button" id="refresh" class="btn btn-default">{{ __('translation.floor_order.index.refresh') }}</button>
                                                </div>
                                                <a href="{{ route('purchasing.purchase-requisitions.create') }}" class="btn btn-primary">
                                                    <i class="ri-add-line align-bottom me-1"></i> {{ __('translation.purchase_requisition.add') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="table-responsive table-card">
                                            <table class="table table-bordered" id="prTable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('translation.purchase_requisition.table.pr_number') }}</th>
                                                        <th>{{ __('translation.purchase_requisition.table.date') }}</th>
                                                        <th>{{ __('translation.purchase_requisition.table.warehouse') }}</th>
                                                        <th>{{ __('translation.purchase_requisition.table.department') }}</th>
                                                        <th>{{ __('translation.purchase_requisition.table.requester') }}</th>
                                                        <th>{{ __('translation.purchase_requisition.table.status') }}</th>
                                                        <th>{{ __('translation.purchase_requisition.table.approved_ssd_by') }}</th>
                                                        <th>{{ __('translation.purchase_requisition.table.approved_ssd_at') }}</th>
                                                        <th>{{ __('translation.purchase_requisition.table.approved_cc_by') }}</th>
                                                        <th>{{ __('translation.purchase_requisition.table.approved_cc_at') }}</th>
                                                        <th>{{ __('translation.purchase_requisition.table.action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($purchaseRequisitions as $pr)
                                                        <tr>
                                                            <td>{{ $pr->pr_number }}</td>
                                                            <td>{{ $pr->date instanceof \DateTime ? $pr->date->format('d/m/Y') : date('d/m/Y', strtotime($pr->date)) }}</td>
                                                            <td>{{ optional($pr->warehouse)->name ?? '-' }}</td>
                                                            <td>{{ $pr->department }}</td>
                                                            <td>{{ optional($pr->requester)->name ?? '-' }}</td>
                                                            <td>
                                                                @switch($pr->status)
                                                                    @case('draft')
                                                                        <span class="badge bg-warning text-dark">{{ __('translation.purchase_requisition.status.draft') }}</span>
                                                                        @break
                                                                    @case('approved_ssd')
                                                                        <span class="badge bg-info">{{ __('translation.purchase_requisition.status.approved_ssd') }}</span>
                                                                        @break
                                                                    @case('approved_cc')
                                                                        <span class="badge bg-success">{{ __('translation.purchase_requisition.status.approved_cc') }}</span>
                                                                        @break
                                                                    @case('rejected')
                                                                        <span class="badge bg-danger">{{ __('translation.purchase_requisition.status.rejected') }}</span>
                                                                        @break
                                                                    @default
                                                                        <span class="badge bg-secondary">{{ ucfirst($pr->status) }}</span>
                                                                @endswitch
                                                            </td>
                                                            <td>{{ optional($pr->ssdApprover)->name ?? '-' }}</td>
                                                            <td>{{ $pr->approved_ssd_at ? date('d/m/Y H:i', strtotime($pr->approved_ssd_at)) : '-' }}</td>
                                                            <td>{{ optional($pr->ccApprover)->name ?? '-' }}</td>
                                                            <td>{{ $pr->approved_cc_at ? date('d/m/Y H:i', strtotime($pr->approved_cc_at)) : '-' }}</td>
                                                            <td>
                                                                <div class="d-flex gap-2">
                                                                    <a href="{{ route('purchasing.purchase-requisitions.show', $pr->id) }}" 
                                                                       class="btn btn-sm btn-info">
                                                                        <i class="ri-eye-line"></i>
                                                                    </a>
                                                                    @if($pr->status == 'draft')
                                                                    <a href="{{ route('purchasing.purchase-requisitions.edit', $pr->id) }}" 
                                                                       class="btn btn-sm btn-primary">
                                                                        <i class="ri-pencil-line"></i>
                                                                    </a>
                                                                    <button type="button" 
                                                                            onclick="deletePR('{{ $pr->id }}', '{{ $pr->pr_number }}')"
                                                                            class="btn btn-sm btn-danger">
                                                                        <i class="ri-delete-bin-line"></i>
                                                                    </button>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="11" class="text-center">{{ __('translation.purchase_requisition.no_data') }}</td>
                                                        </tr>
                                                    @endforelse
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
    // Custom date range filter
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var from = $('#from').val();
        var to = $('#to').val();
        var date = data[1]; // index 1 adalah kolom tanggal PR

        if (from === '' && to === '') return true;
        
        if (from !== '' && to !== '') {
            // Convert date format dd/mm/yyyy to yyyy-mm-dd for comparison
            var parts = date.split('/');
            var dateStr = parts[2] + '-' + parts[1] + '-' + parts[0];
            return (dateStr >= from && dateStr <= to);
        }
        
        return true;
    });

    var table = $('#prTable').DataTable({
        dom: 'rt<"bottom"ip>',
        pageLength: 10,
        ordering: true,
        responsive: true,
        order: [[1, 'desc']], // Order by tanggal PR descending
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

// Fungsi Delete PR
window.deletePR = function(id, prNumber) {
    Swal.fire({
        title: "{{ __('translation.purchase_requisition.confirm_delete') }}",
        text: prNumber,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "{{ __('translation.yes') }}",
        cancelButtonText: "{{ __('translation.no') }}",
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/purchasing/purchase-requisitions/${id}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: "{{ __('translation.purchase_requisition.success_delete') }}"
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseJSON);
                    Swal.fire('Error!', 'Failed to delete Purchase Requisition', 'error');
                }
            });
        }
    });
}
</script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
