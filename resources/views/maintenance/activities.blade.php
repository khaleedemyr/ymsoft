@extends('layouts.master')

@section('title')
    Aktivitas Maintenance
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
        
        .activity-item {
            padding: 1rem;
            border-bottom: 1px dashed var(--tb-border-color);
            margin-bottom: 0.5rem;
        }
        
        .avatar-sm {
            height: 3rem;
            width: 3rem;
        }
        
        .avatar-title {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
            font-weight: 500;
            border-radius: 50%;
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
                    <h1 class="m-0">Aktivitas Maintenance</h1>
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
                                Maintenance
                            @endslot
                            @slot('title')
                                Aktivitas Maintenance
                            @endslot
                        @endcomponent

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center">
                                        <h6 class="card-title flex-grow-1 mb-0">Daftar Aktivitas</h6>
                                        <div class="flex-shrink-0">
                                            <div class="d-flex flex-wrap gap-2">
                                                <div class="search-box">
                                                    <input type="text" class="form-control search" placeholder="Cari aktivitas...">
                                                </div>
                                                <div class="input-group" style="width: auto;">
                                                    <input type="date" class="form-control" name="from" id="from">
                                                    <input type="date" class="form-control" name="to" id="to">
                                                    <button type="button" id="filter" class="btn btn-primary">Filter</button>
                                                    <button type="button" id="refresh" class="btn btn-default">Refresh</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="table-responsive table-card">
                                            <table class="table table-bordered" id="activityTable">
                                                <thead>
                                                    <tr>
                                                        <th>Tanggal</th>
                                                        <th>User</th>
                                                        <th>Aktivitas</th>
                                                        <th>Deskripsi</th>
                                                        <th>Task</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($activities as $activity)
                                                    <tr>
                                                        <td>{{ date('d/m/Y H:i', strtotime($activity->created_at)) }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="flex-shrink-0">
                                                                    <div class="avatar-xxs">
                                                                        <div class="avatar-title bg-light text-primary rounded-circle">
                                                                            @php
                                                                                $name = $activity->user_name ?? 'User';
                                                                                $nameParts = explode(' ', $name);
                                                                                $initials = '';
                                                                                foreach ($nameParts as $part) {
                                                                                    if(strlen($part) > 0) {
                                                                                        $initials .= substr($part, 0, 1);
                                                                                    }
                                                                                }
                                                                                $initials = substr($initials, 0, 2);
                                                                            @endphp
                                                                            {{ $initials }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                {{ $activity->user_name ?? 'System' }}
                                                            </div>
                                                        </td>
                                                        <td>{{ $activity->activity_type }}</td>
                                                        <td>{{ $activity->description }}</td>
                                                        <td>
                                                            @if($activity->task_number)
                                                                <span class="badge bg-primary-subtle text-primary">
                                                                    {{ $activity->task_number }}
                                                                </span>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            
                                            @if(count($activities) == 0)
                                                <div class="alert alert-info mt-3 text-center">
                                                    Tidak ada aktivitas
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
        var date = data[0]; // index 0 adalah kolom tanggal

        if (from === '' && to === '') return true;
        
        if (from !== '' && to !== '') {
            // Convert date format dd/mm/yyyy to yyyy-mm-dd for comparison
            var parts = date.split('/');
            if (parts.length < 3) return true; // Skip jika format tidak valid
            var dateStr = parts[2].split(' ')[0] + '-' + parts[1] + '-' + parts[0];
            return (dateStr >= from && dateStr <= to);
        }
        
        return true;
    });

    // Solusi 1: Destroy tabel yang sudah ada sebelum menginisialisasi
    if ($.fn.DataTable.isDataTable('#activityTable')) {
        $('#activityTable').DataTable().destroy();
    }

    var table = $('#activityTable').DataTable({
        dom: 'rt<"bottom"ip>',
        pageLength: 15,
        ordering: true,
        responsive: true,
        order: [[0, 'desc']], // Order by tanggal descending
        language: {
            emptyTable: "Tidak ada aktivitas yang tersedia",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ aktivitas",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 aktivitas",
            infoFiltered: "(difilter dari _MAX_ total aktivitas)",
            lengthMenu: "Tampilkan _MENU_ aktivitas",
            search: "Cari:",
            zeroRecords: "Tidak ditemukan aktivitas yang sesuai",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
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

    // Trigger filter on load
    table.draw();
});
</script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
