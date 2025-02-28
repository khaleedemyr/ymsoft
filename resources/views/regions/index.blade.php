@extends('layouts.master')

@section('title')
    @lang('translation.region.title')
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
                        Region
                    @endslot
                @endcomponent

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card" id="regionList">
                            <div class="card-header d-flex align-items-center">
                                <h6 class="card-title flex-grow-1 mb-0">@lang('translation.region.list')</h6>
                                <div class="flex-shrink-0">
                                    <div class="d-flex flex-wrap gap-2">
                                        <!-- Toggle switch -->
                                        <div class="d-flex align-items-center">
                                            <label class="toggle-switch">
                                                <input type="checkbox" id="statusToggle" checked>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span class="toggle-label" id="statusLabel">@lang('translation.region.show_active')</span>
                                        </div>
                                        <!-- Search box -->
                                        <div class="search-box">
                                            <input type="text" class="form-control search" placeholder="Cari region...">
                                        </div>
                                        <button type="button" class="btn btn-primary add-btn" data-bs-toggle="modal" data-bs-target="#modal-region">
                                            <i class="ri-add-line align-bottom me-1"></i> @lang('translation.region.add')
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
                                                    <th>@lang('translation.region.code')</th>
                                                    <th>@lang('translation.region.name')</th>
                                                    <th>@lang('translation.region.status')</th>
                                                    <th>@lang('translation.region.action')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($regions as $key => $region)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $region->code }}</td>
                                                    <td>{{ $region->name }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $region->status == 'active' ? 'success' : 'danger' }}">
                                                            {{ $region->status == 'active' ? __('translation.region.active') : __('translation.region.inactive') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button class="btn btn-sm btn-info edit-btn"
                                                                    data-id="{{ $region->id }}"
                                                                    data-code="{{ $region->code }}"
                                                                    data-name="{{ $region->name }}">
                                                                <i class="ri-pencil-fill align-bottom"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-{{ $region->status == 'active' ? 'danger' : 'success' }} toggle-status-btn"
                                                                    data-id="{{ $region->id }}"
                                                                    data-status="{{ $region->status }}">
                                                                <i class="ri-{{ $region->status == 'active' ? 'close' : 'check' }}-fill align-bottom"></i>
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
    <div class="modal fade" id="modal-region" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">@lang('translation.region.add')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="regionForm">
                    @csrf
                    <input type="hidden" id="region_id" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="code" class="form-label">@lang('translation.region.code')</label>
                            <input type="text" class="form-control" id="code" name="code" required maxlength="10">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">@lang('translation.region.name')</label>
                            <input type="text" class="form-control" id="name" name="name" required maxlength="50">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('translation.region.close')</button>
                        <button type="submit" class="btn btn-primary">@lang('translation.region.save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            let table = $('#datatable').DataTable({
                dom: 't<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                ordering: true,
                pageLength: 10,
                language: {
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "<i class='ri-arrow-left-s-line'></i>",
                        next: "<i class='ri-arrow-right-s-line'></i>",
                    }
                }
            });

            // Search functionality
            $('.search').keyup(function() {
                table.search($(this).val()).draw();
            });

            // Toggle status switch
            $('#statusToggle').change(function() {
                let showActive = $(this).prop('checked');
                $('#statusLabel').text(showActive ? '@lang("translation.region.show_active")' : '@lang("translation.region.show_inactive")');
                
                table.column(3).search(showActive ? 'active' : 'inactive').draw();
            });

            // Handle form submission
            $('#regionForm').on('submit', function(e) {
                e.preventDefault();

                let id = $('#region_id').val();
                let url = id ? `/regions/${id}` : '/regions';
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        if(response.success) {
                            $('#modal-region').modal('hide');
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
                $('#modal-title').text('@lang("translation.region.edit")');
                $('#region_id').val($(this).data('id'));
                $('#code').val($(this).data('code'));
                $('#name').val($(this).data('name'));
                $('#modal-region').modal('show');
            });

            // Handle toggle status button
            $(document).on('click', '.toggle-status-btn', function() {
                let id = $(this).data('id');
                let currentStatus = $(this).data('status');
                let newStatus = currentStatus === 'active' ? 'inactive' : 'active';
                let confirmMessage = currentStatus === 'active' 
                    ? '@lang("translation.region.confirm_deactivate")'
                    : '@lang("translation.region.confirm_activate")';

                Swal.fire({
                    title: 'Konfirmasi',
                    text: confirmMessage,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/regions/${id}/toggle-status`,
                            method: 'PUT',
                            data: {
                                status: newStatus,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if(response.success) {
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
        });

        // Reset form when modal is closed
        $('#modal-region').on('hidden.bs.modal', function () {
            $('#regionForm')[0].reset();
            $('#region_id').val('');
            $('#modal-title').text('@lang("translation.region.add")');
        });
    </script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 