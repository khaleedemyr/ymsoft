@extends('layouts.master')

@section('title')
    @lang('translation.user.title')
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
                    {{ trans('translation.user.title') }}
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-lg-12">
                    <div class="card" id="userList">
                        <div class="card-header d-flex align-items-center">
                            <h6 class="card-title flex-grow-1 mb-0">{{ trans('translation.user.list') }}</h6>
                            <div class="flex-shrink-0">
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="search-box">
                                        <input type="text" class="form-control search" placeholder="{{ trans('translation.user.search_placeholder') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div>
                                <div class="table-responsive table-card mb-1">
                                    <table id="userDataTable" class="table align-middle table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>{{ trans('translation.user.name') }}</th>
                                                <th>{{ trans('translation.user.email') }}</th>
                                                <th>{{ trans('translation.user.position') }}</th>
                                                <th>{{ trans('translation.user.division') }}</th>
                                                <th>{{ trans('translation.user.outlet') }}</th>
                                                <th>{{ trans('translation.user.role') }}</th>
                                                <th>{{ trans('translation.user.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($users as $key => $user)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $user->nama_lengkap }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->jabatan->nama_jabatan ?? '-' }}</td>
                                                <td>{{ $user->divisi->nama_divisi ?? '-' }}</td>
                                                <td>{{ $user->outlet->nama_outlet ?? '-' }}</td>
                                                <td>
                                                    @foreach($user->roles as $role)
                                                        <span class="badge bg-primary">{{ $role->name }}</span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary set-role-btn" 
                                                            data-id="{{ $user->id }}"
                                                            data-nama="{{ $user->nama_lengkap }}"
                                                            data-roles="{{ $user->roles->pluck('id') }}"
                                                            title="Set Role">
                                                        <i class="ti ti-user-check"></i>
                                                    </button>
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
<div class="modal fade" id="modal-user" tabindex="-1" aria-labelledby="modal-title" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">{{ trans('translation.user.set_role') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userRoleForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="user_id" name="user_id">
                    <div class="mb-3">
                        <label class="form-label">{{ trans('translation.user.name') }}</label>
                        <input type="text" class="form-control" id="nama_lengkap" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('translation.user.role') }}</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="">{{ trans('translation.user.select_role') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('translation.user.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('translation.user.save') }}</button>
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

<!-- App js -->
<script src="{{ URL::asset('build/js/app.js') }}"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Periksa apakah DataTable sudah ada, jika ya, hancurkan terlebih dahulu
        if ($.fn.DataTable.isDataTable('#userDataTable')) {
            $('#userDataTable').DataTable().destroy();
        }
        
        // Inisialisasi DataTable
        var table = $('#userDataTable').DataTable({
            dom: 'rt<"bottom"ip>',
            pageLength: 10,
            ordering: true,
            responsive: true
        });

        // Implementasi live search
        $('.search').on('keyup', function() {
            table.search($(this).val()).draw();
        });

        // Handle set role button
        $(document).on('click', '.set-role-btn', function() {
            $('#user_id').val($(this).data('id'));
            $('#nama_lengkap').val($(this).data('nama'));
            $('#role_id').val($(this).data('roles')[0]); // Set role yang sudah ada
            $('#modal-user').modal('show');
        });

        // Handle form submission
        $('#userRoleForm').on('submit', function(e) {
            e.preventDefault();

            let userId = $('#user_id').val();
            
            $.ajax({
                url: `/users/${userId}/set-role`,
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if(response.success) {
                        $('#modal-user').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: '{{ trans('translation.user.success') }}',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ trans('translation.user.error') }}',
                        text: xhr.responseJSON?.message || '{{ trans('translation.user.error_message') }}'
                    });
                }
            });
        });

        // Reset form when modal is closed
        $('#modal-user').on('hidden.bs.modal', function () {
            $('#userRoleForm')[0].reset();
            $('#user_id').val('');
        });
    });
</script>
@endsection
