@extends('layouts.master')

@section('title')
    {{ trans('translation.role_management.title') }}
@endsection

@section('css')
    <!-- DataTables -->
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

        .permission-group {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .permission-group-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .permission-item {
            margin-bottom: 8px;
        }

        .permission-check {
            margin-right: 10px;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div>
            @component('components.breadcrumb')
                @slot('li_1')
                    {{ trans('translation.user_management.title') }}
                @endslot
                @slot('title')
                    {{ trans('translation.role_management.title') }}
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-lg-12">
                    <div class="card" id="roleList">
                        <div class="card-header d-flex align-items-center">
                            <h6 class="card-title flex-grow-1 mb-0">{{ trans('translation.role_management.list') }}</h6>
                            <div class="flex-shrink-0">
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <label class="toggle-switch">
                                            <input type="checkbox" id="statusToggle" checked>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <span class="toggle-label" id="statusLabel">Tampilkan Role Aktif</span>
                                    </div>
                                    <div class="search-box">
                                        <input type="text" class="form-control search" placeholder="{{ trans('translation.role_management.search_placeholder') }}">
                                    </div>
                                    <button type="button" class="btn btn-primary add-btn" data-bs-toggle="modal" data-bs-target="#modal-role">
                                        <i class="ri-add-line align-bottom me-1"></i> Tambah Role
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
                                                <th>{{ trans('translation.role_management.name') }}</th>
                                                <th>{{ trans('translation.role_management.description') }}</th>
                                                <th>{{ trans('translation.role_management.permissions') }}</th>
                                                <th>{{ trans('translation.role_management.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($roles as $key => $role)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $role->name }}</td>
                                                <td>{{ $role->description }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $role->status == 'active' ? 'success' : 'danger' }}">
                                                        {{ $role->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-sm btn-link text-primary edit-btn" 
                                                                data-id="{{ $role->id }}"
                                                                data-name="{{ $role->name }}"
                                                                data-description="{{ $role->description }}"
                                                                data-permissions="{{ json_encode($role->permissions) }}"
                                                                data-status="{{ $role->status }}">
                                                            <i class="ti ti-edit fs-5"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-link text-{{ $role->status == 'active' ? 'danger' : 'success' }} toggle-status-btn"
                                                                data-id="{{ $role->id }}"
                                                                data-status="{{ $role->status }}">
                                                            <i class="ti ti-{{ $role->status == 'active' ? 'ban' : 'check' }} fs-5"></i>
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
<div class="modal fade" id="modal-role" tabindex="-1" aria-labelledby="modal-title" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">{{ trans('translation.role_management.set_role') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="roleForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="role_id">
                    <div class="mb-3">
                        <label class="form-label">{{ trans('translation.role_management.name') }}</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('translation.role_management.description') }}</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('translation.role_management.select_role') }}</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="">{{ trans('translation.role_management.select_role') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('translation.role_management.permissions') }}</label>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAllPermissions">
                                <label class="form-check-label fw-bold" for="selectAllPermissions">
                                    Pilih Semua Permissions
                                </label>
                            </div>
                        </div>
                        <div id="permissions-container">
                            @foreach($menus as $menu)
                                <div class="permission-group mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="permission-group-title">{{ $menu->name }}</div>
                                        @if($menu->parent_id === null)
                                            <div class="form-check">
                                                <input class="form-check-input permission-check" type="checkbox" 
                                                       name="permissions[{{ $menu->id }}][can_view]" 
                                                       id="view_{{ $menu->id }}"
                                                       data-group="{{ $menu->id }}"
                                                       value="1">
                                                <label class="form-check-label" for="view_{{ $menu->id }}">View</label>
                                            </div>
                                        @else
                                            <div class="form-check">
                                                <input class="form-check-input select-group" type="checkbox" 
                                                       id="selectGroup_{{ $menu->id }}"
                                                       data-group="{{ $menu->id }}">
                                                <label class="form-check-label" for="selectGroup_{{ $menu->id }}">
                                                    Pilih Semua
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                    @if($menu->parent_id !== null)
                                        <div class="permission-item">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-check" type="checkbox" 
                                                       name="permissions[{{ $menu->id }}][can_view]" 
                                                       id="view_{{ $menu->id }}"
                                                       data-group="{{ $menu->id }}"
                                                       value="1">
                                                <label class="form-check-label" for="view_{{ $menu->id }}">View</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-check" type="checkbox" 
                                                       name="permissions[{{ $menu->id }}][can_create]" 
                                                       id="create_{{ $menu->id }}"
                                                       data-group="{{ $menu->id }}"
                                                       value="1">
                                                <label class="form-check-label" for="create_{{ $menu->id }}">Create</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-check" type="checkbox" 
                                                       name="permissions[{{ $menu->id }}][can_edit]" 
                                                       id="edit_{{ $menu->id }}"
                                                       data-group="{{ $menu->id }}"
                                                       value="1">
                                                <label class="form-check-label" for="edit_{{ $menu->id }}">Edit</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input permission-check" type="checkbox" 
                                                       name="permissions[{{ $menu->id }}][can_delete]" 
                                                       id="delete_{{ $menu->id }}"
                                                       data-group="{{ $menu->id }}"
                                                       value="1">
                                                <label class="form-check-label" for="delete_{{ $menu->id }}">Delete</label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('translation.role_management.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('translation.role_management.save') }}</button>
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
        // Inisialisasi DataTable
        var table = $('#datatable').DataTable({
            dom: 'rt<"bottom"ip>',
            pageLength: 10,
            ordering: true,
            responsive: true,
            order: [[3, 'asc']],
        });

        // Fungsi untuk memfilter data
        function filterData() {
            table.rows().every(function() {
                var row = this.node();
                var showActive = $('#statusToggle').is(':checked');
                var status = $(row).find('td:eq(3) .badge').text().trim();
                
                if (showActive && status === 'Aktif' ||
                    !showActive && status === 'Tidak Aktif') {
                    $(row).show();
                } else {
                    $(row).hide();
                }
            });
        }

        // Set toggle ke active dan trigger filter saat pertama kali load
        $('#statusToggle').prop('checked', true);
        $('#statusLabel').text('Tampilkan Role Aktif');
        filterData();

        // Handle toggle switch untuk filter
        $('#statusToggle').change(function() {
            var isChecked = $(this).is(':checked');
            $('#statusLabel').text(isChecked ? 'Tampilkan Role Aktif' : 'Tampilkan Role Tidak Aktif');
            filterData();
        });

        // Implementasi live search
        $('.search').keyup(function() {
            table.search($(this).val()).draw();
        });

        // Handle form submission
        $('#roleForm').on('submit', function(e) {
            e.preventDefault();

            let id = $('#role_id').val();
            let url = id ? `/roles/${id}` : '/roles';
            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                success: function(response) {
                    if(response.success) {
                        $('#modal-role').modal('hide');
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

        // Handle edit button
        $(document).on('click', '.edit-btn', function() {
            $('#modal-title').text('Edit Role');
            $('#role_id').val($(this).data('id'));
            $('#name').val($(this).data('name'));
            $('#description').val($(this).data('description'));

            // Reset semua checkbox
            $('.permission-check').prop('checked', false);

            // Set permissions
            let permissions = $(this).data('permissions');
            permissions.forEach(function(permission) {
                if(permission.can_view) {
                    $(`#view_${permission.menu_id}`).prop('checked', true);
                }
                if(permission.can_create) {
                    $(`#create_${permission.menu_id}`).prop('checked', true);
                }
                if(permission.can_edit) {
                    $(`#edit_${permission.menu_id}`).prop('checked', true);
                }
                if(permission.can_delete) {
                    $(`#delete_${permission.menu_id}`).prop('checked', true);
                }
            });

            $('#modal-role').modal('show');
        });

        // Handle toggle status button
        $(document).on('click', '.toggle-status-btn', function() {
            let button = $(this);
            let id = button.data('id');
            let currentStatus = button.data('status');
            let newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            let confirmMessage = currentStatus === 'active' ? 
                'Apakah Anda yakin ingin menonaktifkan role ini?' : 
                'Apakah Anda yakin ingin mengaktifkan role ini?';
            
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
                        url: `/roles/${id}/toggle-status`,
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
                                        ${newStatus === 'active' ? 'Aktif' : 'Tidak Aktif'}
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
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan saat mengubah status'
                            });
                        }
                    });
                }
            });
        });

        // Handle Select All Permissions
        $('#selectAllPermissions').change(function() {
            var isChecked = $(this).is(':checked');
            $('.permission-check, .select-group').prop('checked', isChecked);
        });

        // Handle Select All per Group (hanya untuk menu child)
        $('.select-group').change(function() {
            var groupId = $(this).data('group');
            var isChecked = $(this).is(':checked');
            $('.permission-check[data-group="' + groupId + '"]').prop('checked', isChecked);
            updateSelectAllStatus();
        });

        // Handle individual permission checks
        $('.permission-check').change(function() {
            var groupId = $(this).data('group');
            // Hanya update group status jika bukan menu parent
            if ($('#selectGroup_' + groupId).length) {
                updateGroupStatus(groupId);
            }
            updateSelectAllStatus();
        });

        // Update group checkbox status (hanya untuk menu child)
        function updateGroupStatus(groupId) {
            var groupCheckboxes = $('.permission-check[data-group="' + groupId + '"]');
            var checkedGroupCheckboxes = groupCheckboxes.filter(':checked');
            var selectGroup = $('#selectGroup_' + groupId);
            
            if (selectGroup.length) {
                selectGroup.prop('checked', 
                    groupCheckboxes.length === checkedGroupCheckboxes.length
                );
            }
        }

        // Update select all checkbox status
        function updateSelectAllStatus() {
            var allCheckboxes = $('.permission-check');
            var checkedCheckboxes = allCheckboxes.filter(':checked');
            
            $('#selectAllPermissions').prop('checked',
                allCheckboxes.length === checkedCheckboxes.length
            );
        }

        // Reset checkboxes when modal is closed
        $('#modal-role').on('hidden.bs.modal', function() {
            $('#selectAllPermissions').prop('checked', false);
            $('.select-group').prop('checked', false);
        });
    });

    // Reset form when modal is closed
    $('#modal-role').on('hidden.bs.modal', function () {
        $('#roleForm')[0].reset();
        $('#role_id').val('');
        $('#modal-title').text('Tambah Role');
        $('.permission-check').prop('checked', false);
    });
</script>

<!-- App js -->
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
