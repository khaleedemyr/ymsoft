@extends('layouts.master')

@section('title', 'Maintenance Tasks')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
<style>
    /* Status Colors */
    .status-open { background: #e3f2fd; color: #1976d2; }
    .status-pr { background: #fff3e0; color: #f57c00; }
    .status-po { background: #e8f5e9; color: #388e3c; }
    .status-in-progress { background: #f3e5f5; color: #7b1fa2; }
    .status-in-review { background: #fffde7; color: #fbc02d; }
    .status-done { background: #e8f5e9; color: #388e3c; }

    /* Priority Colors */
    .priority-important-urgent { background: #ffebee; color: #c62828; }
    .priority-important-not-urgent { background: #fff3e0; color: #ef6c00; }
    .priority-not-important-urgent { background: #e3f2fd; color: #1976d2; }

    /* Label Colors */
    .label-heater { background: #ffcdd2; }
    .label-refrigeration { background: #bbdefb; }
    .label-civil { background: #c8e6c9; }
    .label-gas { background: #fff9c4; }
    .label-machinary { background: #d1c4e9; }
    .label-others { background: #f5f5f5; }

    .task-label {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    /* Styling untuk select boxes */
    .form-select {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }

    .form-select:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .form-select option {
        background-color: #fff;
        color: #495057;
        padding: 8px;
    }

    .form-select:disabled,
    .form-select[readonly] {
        background-color: #e9ecef;
        opacity: 1;
    }

    /* Label styling */
    .form-label {
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #495057;
    }

    /* Card styling */
    .card {
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .card-body {
        padding: 1.25rem;
    }

    .select2-container--bootstrap-5 .select2-selection {
        color: #495057 !important;
        background-color: #fff !important;
    }

    .select2-container--bootstrap-5 .select2-selection__rendered {
        color: #495057 !important;
    }

    .select2-container--bootstrap-5 .select2-results__option {
        color: #495057 !important;
        background-color: #fff !important;
    }

    .select2-container--bootstrap-5 .select2-results__option--highlighted {
        color: #fff !important;
        background-color: #0d6efd !important;
    }

    /* Styling untuk disabled state */
    .form-select:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    /* Loading indicator untuk select */
    .select-loading {
        position: relative;
    }

    .select-loading::after {
        content: '';
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: translateY(-50%) rotate(0deg); }
        100% { transform: translateY(-50%) rotate(360deg); }
    }

    #rukoContainer {
        transition: all 0.3s ease;
    }

    #rukoContainer.show {
        display: block !important;
    }

    /* Pastikan select2 tidak mengubah visibility */
    .select2-container {
        display: block !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Maintenance Task</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-task">
                            <i class="fas fa-plus"></i> Tambah Task
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div id="debugInfo" class="alert alert-info" style="display: none;">
                                <pre id="debugText"></pre>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Outlet</label>
                            <select class="form-select" id="outletId" name="outlet_id">
                                <option value="">Pilih Outlet</option>
                                @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->id_outlet }}">
                                        {{ $outlet->nama_outlet }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Ruko container --}}
                        <div class="col-lg-4" id="rukoContainer">
                            <label class="form-label">Ruko</label>
                            <select class="form-select" id="rukoId" name="ruko_id">
                                <option value="">Pilih Ruko</option>
                            </select>
                        </div>

                        {{-- Debug Buttons --}}
                        <div class="col-12">
                            <button type="button" class="btn btn-info" id="debugBtn">Debug Info</button>
                            <button type="button" class="btn btn-warning" id="testRouteBtn">Test Route</button>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <select class="form-control select2" id="filter_status">
                                <option value="">Semua Status</option>
                                @foreach(App\Models\MaintenanceTask::STATUSES as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control select2" id="filter_priority">
                                <option value="">Semua Priority</option>
                                @foreach(App\Models\MaintenanceTask::PRIORITIES as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <select class="form-control select2" id="filter_label">
                                <option value="">Semua Label</option>
                                @foreach(App\Models\MaintenanceTask::LABELS as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <table id="tasks-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Task Number</th>
                                <th>Title</th>
                                <th>Outlet</th>
                                <th>Ruko</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Due Date</th>
                                <th>Assigned To</th>
                                <th>Created By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('maintenance.tasks.modal_form')

@endsection

@section('scripts')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    $('.select2').select2();

    var table = $('#tasks-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('maintenance.tasks.data') }}",
            data: function(d) {
                d.outlet_id = $('#filter_outlet').val();
                d.ruko_id = $('#filter_ruko').val();
                d.status = $('#filter_status').val();
                d.priority = $('#filter_priority').val();
                d.label = $('#filter_label').val();
            }
        },
        columns: [
            {data: 'task_number', name: 'task_number'},
            {data: 'title', name: 'title'},
            {
                data: 'label', 
                name: 'label',
                render: function(data) {
                    return `<span class="task-label label-${data.toLowerCase()}">${data}</span>`;
                }
            },
            {
                data: 'priority', 
                name: 'priority',
                render: function(data) {
                    let className = data.toLowerCase().replace(/ /g, '-');
                    return `<span class="task-label priority-${className}">${data}</span>`;
                }
            },
            {
                data: 'status', 
                name: 'status',
                render: function(data) {
                    return `<span class="task-label status-${data.toLowerCase().replace(/ /g, '-')}">${data}</span>`;
                }
            },
            {data: 'outlet_name', name: 'outlet_name'},
            {data: 'ruko_name', name: 'ruko_name'},
            {data: 'due_date', name: 'due_date'},
            {data: 'assigned_to_name', name: 'assigned_to_name'},
            {data: 'created_by_name', name: 'created_by_name'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });

    // Filter event handlers
    $('.select2').change(function() {
        table.draw();
    });

    // Tunggu sampai document ready
    $(document).ready(function() {
        // Debug: Log ketika document ready
        console.log('Document ready');

        // Debug function
        function showDebug(message) {
            $('#debugText').html(message);
            $('#debugInfo').show();
        }

        // Test route button
        $('#testRouteBtn').click(function() {
            $.get('/maintenance/test-ruko', function(response) {
                showDebug('Test Route Response:\n' + JSON.stringify(response, null, 2));
            });
        });

        // Debug button
        $('#debugBtn').click(function() {
            const debugInfo = {
                'Outlet Value': $('#outletId').val(),
                'Ruko Container Display': $('#rukoContainer').css('display'),
                'jQuery Version': $().jquery,
                'Window Width': $(window).width()
            };
            showDebug(JSON.stringify(debugInfo, null, 2));
        });

        // Outlet change handler
        $('#outletId').change(function() {
            const outletId = $(this).val();
            showDebug('Selected Outlet: ' + outletId);

            if (outletId === '1') {
                // Show loading state
                $('#rukoId').html('<option value="">Loading...</option>');
                $('#rukoContainer').show();

                // Fetch ruko data
                $.ajax({
                    url: '/maintenance/get-ruko/' + outletId,
                    method: 'GET',
                    success: function(response) {
                        showDebug('Ruko Response:\n' + JSON.stringify(response, null, 2));
                        
                        if (response.success && response.data) {
                            let options = '<option value="">Pilih Ruko</option>';
                            response.data.forEach(function(ruko) {
                                options += `<option value="${ruko.id_ruko}">${ruko.nama_ruko}</option>`;
                            });
                            $('#rukoId').html(options);
                        }
                    },
                    error: function(xhr, status, error) {
                        showDebug('Error:\n' + error + '\nStatus: ' + status);
                        $('#rukoId').html('<option value="">Error loading ruko</option>');
                    }
                });
            } else {
                $('#rukoContainer').hide();
            }
        });

        // Tambahkan debugging untuk memastikan nilai outlet
        console.log('Initial outlet value:', '{{ $selectedOutletId }}');
        console.log('Can select outlet:', '{{ $canSelectOutlet }}');
    });
});
</script>
@endsection
