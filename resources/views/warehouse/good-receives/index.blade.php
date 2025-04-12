@extends('layouts.master')

@section('title')
    {{ trans('translation.good_receive.title') }}
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
                    <h1 class="m-0">{{ trans('translation.good_receive.title') }}</h1>
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
                                {{ trans('translation.warehouse_management.title') }}
                            @endslot
                            @slot('title')
                                {{ trans('translation.good_receive.title') }}
                            @endslot
                        @endcomponent

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center">
                                        <h6 class="card-title flex-grow-1 mb-0">{{ trans('translation.good_receive.title') }}</h6>
                                        <div class="flex-shrink-0">
                                            <div class="d-flex flex-wrap gap-2">
                                                <div class="search-box">
                                                    <input type="text" class="form-control search" placeholder="{{ trans('translation.search') }}">
                                                </div>
                                                <div class="input-group" style="width: auto;">
                                                    <input type="date" class="form-control" name="from" id="from">
                                                    <input type="date" class="form-control" name="to" id="to">
                                                    <button type="button" id="filter" class="btn btn-primary">{{ trans('translation.filter') }}</button>
                                                    <button type="button" id="refresh" class="btn btn-default">{{ trans('translation.refresh') }}</button>
                                                </div>
                                                <a href="{{ route('warehouse.good-receives.create') }}" class="btn btn-primary">
                                                    <i class="ri-add-line align-bottom me-1"></i> {{ trans('translation.good_receive.scan_qr') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="table-responsive table-card">
                                            <table class="table table-bordered" id="grTable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ trans('translation.good_receive.table.gr_number') }}</th>
                                                        <th>{{ trans('translation.good_receive.table.po_number') }}</th>
                                                        <th>{{ trans('translation.good_receive.table.supplier') }}</th>
                                                        <th>{{ trans('translation.good_receive.table.receive_date') }}</th>
                                                        <th>{{ trans('translation.good_receive.table.status') }}</th>
                                                        <th>{{ trans('translation.good_receive.table.total') }}</th>
                                                        <th>{{ trans('translation.good_receive.table.action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($goodReceives as $goodReceive)
                                                    <tr>
                                                        <td>{{ $goodReceive->gr_number }}</td>
                                                        <td>{{ $goodReceive->purchaseOrder->po_number }}</td>
                                                        <td>{{ $goodReceive->purchaseOrder->supplier->name }}</td>
                                                        <td>{{ $goodReceive->receive_date->format('d/m/Y') }}</td>
                                                        <td>
                                                            @switch($goodReceive->status)
                                                                @case('draft')
                                                                    <span class="badge bg-warning text-dark">{{ trans('translation.good_receive.status.draft') }}</span>
                                                                    @break
                                                                @case('approved')
                                                                    <span class="badge bg-success">{{ trans('translation.good_receive.status.approved') }}</span>
                                                                    @break
                                                                @case('rejected')
                                                                    <span class="badge bg-danger">{{ trans('translation.good_receive.status.rejected') }}</span>
                                                                    @break
                                                                @default
                                                                    <span class="badge bg-secondary">{{ ucfirst($goodReceive->status) }}</span>
                                                            @endswitch
                                                        </td>
                                                        <td class="text-end">{{ number_format($goodReceive->total_amount, 2) }}</td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <a href="{{ route('warehouse.good-receives.show', $goodReceive->id) }}" 
                                                                   class="btn btn-sm btn-info">
                                                                    <i class="ri-eye-line"></i>
                                                                </a>
                                                                @if($goodReceive->canEdit())
                                                                <a href="{{ route('warehouse.good-receives.edit', $goodReceive->id) }}" 
                                                                   class="btn btn-sm btn-warning">
                                                                    <i class="ri-edit-line"></i>
                                                                </a>
                                                                @endif
                                                                @if($goodReceive->canDelete())
                                                                <button type="button" 
                                                                    onclick="deleteGR('{{ $goodReceive->id }}', '{{ $goodReceive->gr_number }}')"
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
                                            
                                            @if(count($goodReceives) == 0)
                                                <div class="alert alert-info mt-3 text-center">
                                                    {{ trans('translation.good_receive.message.no_data') }}
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
    // Custom date range filter
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var from = $('#from').val();
        var to = $('#to').val();
        var date = data[3]; // index 3 adalah kolom tanggal GR

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

    var table = $('#grTable').DataTable({
        dom: 'rt<"bottom"ip>',
        pageLength: 10,
        ordering: true,
        responsive: true,
        order: [[3, 'desc']], // Order by tanggal GR descending
        language: {
            emptyTable: "{{ trans('translation.good_receive.message.no_data') }}",
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
            .order([[3, 'desc']])
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

// Fungsi Delete GR
window.deleteGR = function(id, grNumber) {
    Swal.fire({
        title: "{{ trans('translation.good_receive.message.confirm_delete') }}",
        text: grNumber,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "{{ trans('translation.yes') }}",
        cancelButtonText: "{{ trans('translation.no') }}",
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/warehouse/good-receives/${id}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: "{{ trans('translation.success') }}",
                            text: "{{ trans('translation.good_receive.message.success_delete') }}"
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire("{{ trans('translation.error') }}", response.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseJSON);
                    Swal.fire("{{ trans('translation.error') }}", "{{ trans('translation.good_receive.message.error_delete') }}", 'error');
                }
            });
        }
    });
}
</script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 