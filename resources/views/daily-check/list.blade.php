@extends('layouts.master')

@section('title')
    {{ trans('translation.daily_check.list') }}
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
                    <h1 class="m-0">{{ trans('translation.daily_check.title') }}</h1>
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
                                {{ trans('translation.daily_check.title') }}
                            @endslot
                            @slot('title')
                                {{ trans('translation.daily_check.list') }}
                            @endslot
                        @endcomponent

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center">
                                        <h6 class="card-title flex-grow-1 mb-0">{{ trans('translation.daily_check.list') }}</h6>
                                        <div class="flex-shrink-0">
                                            <div class="d-flex flex-wrap gap-2">
                                                <div class="search-box">
                                                    <input type="text" class="form-control search" placeholder="{{ trans('translation.daily_check.table.search_placeholder') }}">
                                                </div>
                                                <div class="input-group" style="width: auto;">
                                                    <input type="date" class="form-control" name="from" id="from">
                                                    <input type="date" class="form-control" name="to" id="to">
                                                    <button type="button" id="filter" class="btn btn-primary">{{ trans('translation.filter') }}</button>
                                                    <button type="button" id="refresh" class="btn btn-default">{{ trans('translation.refresh') }}</button>
                                                </div>
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#outletModal">
                                                    <i class="ri-add-line align-bottom me-1"></i> {{ trans('translation.daily_check.add') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="table-responsive table-card">
                                            <table class="table table-bordered" id="dailyCheckTable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ trans('translation.daily_check.table.date') }}</th>
                                                        <th>{{ trans('translation.daily_check.table.outlet') }}</th>
                                                        <th>{{ trans('translation.daily_check.table.created_by') }}</th>
                                                        <th>{{ trans('translation.daily_check.table.created_at') }}</th>
                                                        <th>Status</th>
                                                        <th>{{ trans('translation.daily_check.table.action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($dailyChecks as $dailyCheck)
                                                    <tr>
                                                        <td>{{ $dailyCheck->date instanceof \DateTime ? $dailyCheck->date->format('d/m/Y') : date('d/m/Y', strtotime($dailyCheck->date)) }}</td>
                                                        <td>{{ optional($dailyCheck->outlet)->nama_outlet ?? '-' }}</td>
                                                        <td>{{ optional($dailyCheck->creator)->nama_lengkap ?? '-' }}</td>
                                                        <td>{{ $dailyCheck->created_at ? date('d/m/Y H:i', strtotime($dailyCheck->created_at)) : '-' }}</td>
                                                        <td>
                                                            @if($dailyCheck->status == 'draft')
                                                                <span class="badge bg-warning">Draft</span>
                                                            @else
                                                                <span class="badge bg-success">Saved</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-1">
                                                                <a href="{{ route('daily-check.show', $dailyCheck->id) }}" 
                                                                   class="btn btn-sm btn-info" title="{{ trans('translation.view') }}">
                                                                    <i class="ri-eye-line"></i>
                                                                </a>
                                                                @if($dailyCheck->status == 'draft')
                                                                <a href="{{ route('daily-check.create', ['outlet_id' => $dailyCheck->id_outlet]) }}" 
                                                                   class="btn btn-sm btn-warning" title="{{ trans('translation.continue') }}">
                                                                    <i class="ri-edit-line"></i>
                                                                </a>
                                                                @else
                                                                <a href="{{ route('daily-check.edit', $dailyCheck->id) }}" 
                                                                   class="btn btn-sm btn-primary" title="{{ trans('translation.edit') }}">
                                                                    <i class="ri-pencil-line"></i>
                                                                </a>
                                                                @endif
                                                                <button type="button" 
                                                                        onclick="deleteDailyCheck('{{ $dailyCheck->id }}', '{{ $dailyCheck->date instanceof \DateTime ? $dailyCheck->date->format('d/m/Y') : date('d/m/Y', strtotime($dailyCheck->date)) }}')"
                                                                        class="btn btn-sm btn-danger" title="{{ trans('translation.delete') }}">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            
                                            @if(count($dailyChecks) == 0)
                                                <div class="alert alert-info mt-3 text-center">
                                                    {{ trans('translation.daily_check.message.no_data') }}
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

<!-- Modal Pilih Outlet -->
<div class="modal fade" id="outletModal" tabindex="-1" aria-labelledby="outletModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="outletModalLabel">Pilih Outlet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('daily-check.create') }}" method="GET" id="outletForm">
                    <div class="mb-3">
                        <label for="outlet_id" class="form-label">Outlet</label>
                        <select class="form-select" id="outlet_id" name="outlet_id" required>
                            <option value="">Pilih Outlet</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Lanjutkan</button>
                    </div>
                </form>
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
        var date = data[0]; // index 0 adalah kolom tanggal

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

    // Inisialisasi DataTable hanya sekali
    if (!$.fn.DataTable.isDataTable('#dailyCheckTable')) {
        var table = $('#dailyCheckTable').DataTable({
            dom: 'rt<"bottom"ip>',
            pageLength: 10,
            ordering: true,
            responsive: true,
            order: [[0, 'desc']], // Order by tanggal descending
            columnDefs: [
                {
                    // Kolom status (indeks 4)
                    targets: 4,
                    render: function(data, type, row) {
                        if (type === 'sort' || type === 'type') {
                            return data;
                        }
                        if (data.includes('Draft')) {
                            return '<span class="badge bg-warning">Draft</span>';
                        } else if (data.includes('Saved')) {
                            return '<span class="badge bg-success">Saved</span>';
                        }
                        return data;
                    }
                }
            ],
            language: {
                emptyTable: "{{ trans('translation.daily_check.message.no_data') }}",
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
                .order([[0, 'desc']])
                .draw();
        });

        // Set default date range (hari ini)
        var today = new Date().toISOString().split('T')[0];
        $('#from').val(today);
        $('#to').val(today);
        
        // Trigger filter on load
        table.draw();
    }
});

// Setup CSRF token untuk semua request AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Fungsi Delete Daily Check
window.deleteDailyCheck = function(id, date) {
    Swal.fire({
        title: "{{ trans('translation.daily_check.message.confirm_delete') }}",
        text: "Data tanggal: " + date,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "{{ trans('translation.yes') }}",
        cancelButtonText: "{{ trans('translation.no') }}",
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/daily-check/${id}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: "{{ trans('translation.success') }}",
                            text: "{{ trans('translation.daily_check.message.success_delete') }}"
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire("{{ trans('translation.error') }}", response.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseJSON);
                    Swal.fire("{{ trans('translation.error') }}", "{{ trans('translation.daily_check.message.error_delete') }}", 'error');
                }
            });
        }
    });
}
</script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 