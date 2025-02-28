@extends('layouts.master')

@section('title')
    @lang('translation.unit.title')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    
    <style>
        /* Style untuk search box */
        .search-box {
            min-width: 200px;
        }
        
        .dataTables_filter {
            display: none;
        }
        
        /* Style untuk toggle switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #2196F3;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(30px);
        }

        .toggle-label {
            margin-left: 10px;
            line-height: 30px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div>
                @component('components.breadcrumb')
                    @slot('li_1')
                        Master Data
                    @endslot
                    @slot('title')
                        Unit
                    @endslot
                @endcomponent

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card" id="unitList">
                            <div class="card-header d-flex align-items-center">
                                <h6 class="card-title flex-grow-1 mb-0">@lang('translation.unit.list')</h6>
                                <div class="flex-shrink-0">
                                    <div class="d-flex flex-wrap gap-2">
                                        <!-- Toggle switch -->
                                        <div class="d-flex align-items-center">
                                            <label class="toggle-switch">
                                                <input type="checkbox" id="statusToggle" checked>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span class="toggle-label" id="statusLabel">@lang('translation.unit.show_active')</span>
                                        </div>
                                        <!-- Search box -->
                                        <div class="search-box">
                                            <input type="text" class="form-control search" placeholder="Cari unit...">
                                        </div>
                                        <button type="button" class="btn btn-primary add-btn" data-bs-toggle="modal" data-bs-target="#modal-unit">
                                            <i class="ri-add-line align-bottom me-1"></i> @lang('translation.unit.add')
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div>
                                    <div class="table-responsive table-card mb-1">
                                        <table id="datatable" class="table align-middle table-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>@lang('translation.unit.code')</th>
                                                    <th>@lang('translation.unit.name')</th>
                                                    <th>@lang('translation.unit.status')</th>
                                                    <th>@lang('translation.unit.action')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($units as $key => $unit)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $unit->code }}</td>
                                                    <td>{{ $unit->name }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $unit->status == 'active' ? 'success' : 'danger' }}">
                                                            {{ $unit->status == 'active' ? __('translation.unit.active') : __('translation.unit.inactive') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button class="btn btn-sm btn-info edit-btn" 
                                                                    data-id="{{ $unit->id }}"
                                                                    data-code="{{ $unit->code }}"
                                                                    data-name="{{ $unit->name }}">
                                                                <i class="ri-pencil-fill align-bottom"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-link text-{{ $unit->status == 'active' ? 'danger' : 'success' }} toggle-status-btn"
                                                                    data-id="{{ $unit->id }}"
                                                                    data-status="{{ $unit->status }}">
                                                                <i class="ti ti-{{ $unit->status == 'active' ? 'ban' : 'check' }} fs-5"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
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

    <!-- Modal -->
    <div class="modal fade" id="modal-unit" tabindex="-1" aria-labelledby="modal-title" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">@lang('translation.unit.add')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="unitForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="unit_id" name="unit_id">
                        <div class="mb-3">
                            <label class="form-label">@lang('translation.unit.code')</label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('translation.unit.name')</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('translation.unit.close')</button>
                        <button type="submit" class="btn btn-primary">@lang('translation.unit.save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                dom: 'rt<"bottom"ip>',
                pageLength: 10,
                ordering: true,
                responsive: true,
                order: [[3, 'asc']],
            });

            function filterData() {
                table.rows().every(function() {
                    var row = this.node();
                    var showActive = $('#statusToggle').is(':checked');
                    var status = $(row).find('td:eq(3) .badge').text().trim();
                    
                    if (showActive && status === '@lang("translation.unit.active")' ||
                        !showActive && status === '@lang("translation.unit.inactive")') {
                        $(row).show();
                    } else {
                        $(row).hide();
                    }
                });
            }

            $('#statusToggle').prop('checked', true);
            $('#statusLabel').text('@lang("translation.unit.show_active")');
            filterData();

            $('#statusToggle').change(function() {
                var isChecked = $(this).is(':checked');
                $('#statusLabel').text(isChecked ? 
                    '@lang("translation.unit.show_active")' : 
                    '@lang("translation.unit.show_inactive")'
                );
                filterData();
            });

            $('.search').keyup(function() {
                table.search($(this).val()).draw();
            });

            $(document).on('click', '.toggle-status-btn', function() {
                let button = $(this);
                let id = button.data('id');
                let currentStatus = button.data('status');
                let newStatus = currentStatus === 'active' ? 'inactive' : 'active';
                let confirmMessage = currentStatus === 'active' ? 
                    '@lang("translation.unit.confirm_deactivate")' : 
                    '@lang("translation.unit.confirm_activate")';

                Swal.fire({
                    title: confirmMessage,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/units/${id}/toggle-status`,
                            method: 'PUT',
                            data: {
                                status: newStatus,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if(response.success) {
                                    let row = button.closest('tr');
                                    
                                    row.find('td:eq(3)').html(`
                                        <span class="badge bg-${newStatus === 'active' ? 'success' : 'danger'}">
                                            ${newStatus === 'active' ? '@lang("translation.unit.active")' : '@lang("translation.unit.inactive")'}
                                        </span>
                                    `);

                                    button
                                        .data('status', newStatus)
                                        .attr('data-status', newStatus)
                                        .removeClass('text-danger text-success')
                                        .addClass(`text-${newStatus === 'active' ? 'danger' : 'success'}`)
                                        .find('i')
                                        .removeClass('ti-ban ti-check')
                                        .addClass(`ti-${newStatus === 'active' ? 'ban' : 'check'}`);

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        filterData();
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                                });
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.edit-btn', function() {
                $('#modal-title').text('@lang("translation.unit.edit")');
                $('#unit_id').val($(this).data('id'));
                $('#code').val($(this).data('code'));
                $('#name').val($(this).data('name'));
                $('#modal-unit').modal('show');
            });

            $('#unitForm').on('submit', function(e) {
                e.preventDefault();

                let id = $('#unit_id').val();
                let url = id ? `/units/${id}` : '/units';
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        if(response.success) {
                            $('#modal-unit').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON?.errors;
                        let errorMessage = '';
                        
                        if (errors) {
                            $.each(errors, function(key, value) {
                                errorMessage += value[0] + '\n';
                            });
                        } else {
                            errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    }
                });
            });
        });

        $('#modal-unit').on('hidden.bs.modal', function () {
            $('#unitForm')[0].reset();
            $('#unit_id').val('');
            $('#modal-title').text('@lang("translation.unit.add")');
        });
    </script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 