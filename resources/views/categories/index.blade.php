@extends('layouts.master')

@section('title')
    @lang('translation.category.title')
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
        
        /* Sembunyikan search default DataTables */
        .dataTables_filter {
            display: none;
        }
        
        /* Styling untuk card header */
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Responsive styling */
        @media (max-width: 576px) {
            .card-tools {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .search-box {
                width: 100%;
            }
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
                    Kategori
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-lg-12">
                    <div class="card" id="categoryList">
                        <div class="card-header d-flex align-items-center">
                            <h6 class="card-title flex-grow-1 mb-0">@lang('translation.category.list')</h6>
                            <div class="flex-shrink-0">
                                <div class="d-flex flex-wrap gap-2">
                                    <!-- Toggle switch -->
                                    <div class="d-flex align-items-center">
                                        <label class="toggle-switch">
                                            <input type="checkbox" id="statusToggle" checked>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <span class="toggle-label" id="statusLabel">@lang('translation.category.show_active')</span>
                                    </div>
                                    <!-- Search box -->
                                    <div class="search-box">
                                        <input type="text" class="form-control search" placeholder="Cari kategori...">
                                    </div>
                                    <button type="button" class="btn btn-primary add-btn" data-bs-toggle="modal" data-bs-target="#modal-category">
                                        <i class="ri-add-line align-bottom me-1"></i> @lang('translation.category.add')
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
                                                <th>@lang('translation.category.code')</th>
                                                <th>@lang('translation.category.name')</th>
                                                <th>@lang('translation.category.description')</th>
                                                <th>@lang('translation.category.status')</th>
                                                <th>@lang('translation.category.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($categories as $key => $category)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $category->code }}</td>
                                                <td>{{ $category->name }}</td>
                                                <td>{{ $category->description }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $category->status == 'active' ? 'success' : 'danger' }}">
                                                        {{ $category->status == 'active' ? __('translation.category.active') : __('translation.category.inactive') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-sm btn-link text-primary edit-btn" 
                                                                data-id="{{ $category->id }}"
                                                                data-code="{{ $category->code }}"
                                                                data-name="{{ $category->name }}"
                                                                data-description="{{ $category->description }}"
                                                                data-status="{{ $category->status }}">
                                                            <i class="ti ti-edit fs-5"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-link text-{{ $category->status == 'active' ? 'danger' : 'success' }} toggle-status-btn"
                                                                data-id="{{ $category->id }}"
                                                                data-status="{{ $category->status }}">
                                                            <i class="ti ti-{{ $category->status == 'active' ? 'ban' : 'check' }} fs-5"></i>
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
<div class="modal fade" id="modal-category" tabindex="-1" aria-labelledby="modal-title" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">@lang('translation.category.add')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="category_id">
                    <div class="mb-3">
                        <label class="form-label">@lang('translation.category.code')</label>
                        <input type="text" class="form-control" id="code" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('translation.category.name')</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('translation.category.description')</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('translation.category.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('translation.category.save')</button>
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

    <!-- Your custom scripts -->
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable dengan konfigurasi search dan filter
            var table = $('#datatable').DataTable({
                dom: 'rt<"bottom"ip>',
                pageLength: 10,
                ordering: true,
                responsive: true,
                order: [[4, 'asc']],
            });

            // Fungsi untuk memfilter data
            function filterData() {
                table.rows().every(function() {
                    var row = this.node();
                    var showActive = $('#statusToggle').is(':checked');
                    var status = $(row).find('td:eq(4) .badge').text().trim();
                    
                    if (showActive && status === '@lang("translation.category.active")' ||
                        !showActive && status === '@lang("translation.category.inactive")') {
                        $(row).show();
                    } else {
                        $(row).hide();
                    }
                });
            }

            // Set toggle ke active dan trigger filter saat pertama kali load
            $('#statusToggle').prop('checked', true);
            $('#statusLabel').text('@lang("translation.category.show_active")');
            filterData();

            // Handle toggle switch untuk filter
            $('#statusToggle').change(function() {
                var isChecked = $(this).is(':checked');
                $('#statusLabel').text(isChecked ? 
                    '@lang("translation.category.show_active")' : 
                    '@lang("translation.category.show_inactive")'
                );
                filterData();
            });

            // Handle toggle status button
            $(document).on('click', '.toggle-status-btn', function() {
                let button = $(this);
                let id = button.data('id');
                let currentStatus = button.data('status');
                let newStatus = currentStatus === 'active' ? 'inactive' : 'active';
                let confirmMessage = currentStatus === 'active' ? 
                    '@lang("translation.category.confirm_deactivate")' : 
                    '@lang("translation.category.confirm_activate")';

                console.log('Toggle clicked:', { id, currentStatus, newStatus });
                
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
                            url: `/categories/${id}/toggle-status`,
                            method: 'PUT',
                            data: {
                                status: newStatus,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                console.log('Success response:', response);
                                if(response.success) {
                                    let row = button.closest('tr');
                                    
                                    // Update badge
                                    row.find('td:eq(4)').html(`
                                        <span class="badge bg-${newStatus === 'active' ? 'success' : 'danger'}">
                                            ${newStatus === 'active' ? '@lang("translation.category.active")' : '@lang("translation.category.inactive")'}
                                        </span>
                                    `);

                                    // Update button
                                    button
                                        .data('status', newStatus)
                                        .attr('data-status', newStatus)
                                        .removeClass('text-danger text-success')
                                        .addClass(`text-${newStatus === 'active' ? 'danger' : 'success'}`)
                                        .find('i')
                                        .removeClass('ti-ban ti-check')
                                        .addClass(`ti-${newStatus === 'active' ? 'ban' : 'check'}`);

                                    // Update edit button
                                    row.find('.edit-btn')
                                        .data('status', newStatus)
                                        .attr('data-status', newStatus);

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
                            error: function(xhr, status, error) {
                                console.log('Error:', xhr, status, error);
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

            // Implementasi live search
            $('.search').keyup(function() {
                table.search($(this).val()).draw();
            });

            // Tambahkan console.log untuk debugging
            console.log('Document ready');

            // Setup AJAX CSRF
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle form submission
            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();
                console.log('Form submitted'); // Debug log

                let id = $('#category_id').val();
                let url = id ? `/categories/${id}` : '/categories';
                let method = id ? 'PUT' : 'POST';
                let formData = $(this).serialize();
                
                console.log('URL:', url); // Debug log
                console.log('Method:', method); // Debug log
                console.log('Data:', formData); // Debug log

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    success: function(response) {
                        console.log('Success response:', response); // Debug log
                        if(response.success) {
                            $('#modal-category').modal('hide');
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
                    error: function(xhr, status, error) {
                        console.log('Error:', xhr, status, error); // Debug log
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
                $('#modal-title').text('@lang("translation.category.edit")');
                $('#category_id').val($(this).data('id'));
                $('#code').val($(this).data('code'));
                $('#name').val($(this).data('name'));
                $('#description').val($(this).data('description'));
                $('#modal-category').modal('show');
            });
        });

        // Reset form when modal is closed
        $('#modal-category').on('hidden.bs.modal', function () {
            $('#categoryForm')[0].reset();
            $('#category_id').val('');
            $('#modal-title').text('@lang("translation.category.add")');
        });
    </script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 