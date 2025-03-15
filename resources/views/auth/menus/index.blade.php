@extends('layouts.master')

@section('title')
    @lang('translation.menu_management.title')
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

        .icon-item {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            margin: 5px;
            cursor: pointer;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .icon-item:hover {
            background-color: #e9ecef;
        }

        .icon-item.selected {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }

        .icon-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
            gap: 5px;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div>
            @component('components.breadcrumb')
                @slot('li_1')
                    User Management
                @endslot
                @slot('title')
                    @lang('translation.menu_management.title')
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-lg-12">
                    <div class="card" id="menuList">
                        <div class="card-header d-flex align-items-center">
                            <h6 class="card-title flex-grow-1 mb-0">@lang('translation.menu_management.list')</h6>
                            <div class="flex-shrink-0">
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <label class="toggle-switch">
                                            <input type="checkbox" id="statusToggle" checked>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <span class="toggle-label" id="statusLabel">@lang('translation.menu_management.show_active')</span>
                                    </div>
                                    <div class="search-box">
                                        <input type="text" class="form-control search" placeholder="@lang('translation.menu_management.search_placeholder')">
                                    </div>
                                    <button type="button" class="btn btn-primary add-btn" data-bs-toggle="modal" data-bs-target="#modal-menu">
                                        <i class="ri-add-line align-bottom me-1"></i> @lang('translation.menu_management.add')
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
                                                <th>@lang('translation.menu_management.name')</th>
                                                <th>@lang('translation.menu_management.slug')</th>
                                                <th>@lang('translation.menu_management.icon')</th>
                                                <th>@lang('translation.menu_management.route')</th>
                                                <th>@lang('translation.menu_management.parent_menu')</th>
                                                <th>@lang('translation.menu_management.order')</th>
                                                <th>Status</th>
                                                <th>@lang('translation.menu_management.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($menus as $key => $menu)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $menu->name }}</td>
                                                <td>{{ $menu->slug }}</td>
                                                <td><i class="{{ $menu->icon }}"></i> {{ $menu->icon }}</td>
                                                <td>{{ $menu->route }}</td>
                                                <td>{{ $menu->parent ? $menu->parent->name : '-' }}</td>
                                                <td>{{ $menu->order }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $menu->status == 'active' ? 'success' : 'danger' }}">
                                                        {{ $menu->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-sm btn-link text-primary edit-btn" 
                                                                data-id="{{ $menu->id }}"
                                                                data-name="{{ $menu->name }}"
                                                                data-slug="{{ $menu->slug }}"
                                                                data-icon="{{ $menu->icon }}"
                                                                data-route="{{ $menu->route }}"
                                                                data-parent="{{ $menu->parent_id }}"
                                                                data-order="{{ $menu->order }}"
                                                                data-status="{{ $menu->status }}">
                                                            <i class="ti ti-edit fs-5"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-link text-{{ $menu->status == 'active' ? 'danger' : 'success' }} toggle-status-btn"
                                                                data-id="{{ $menu->id }}"
                                                                data-status="{{ $menu->status }}">
                                                            <i class="ti ti-{{ $menu->status == 'active' ? 'ban' : 'check' }} fs-5"></i>
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
<div class="modal fade" id="modal-menu" tabindex="-1" aria-labelledby="modal-title" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">@lang('translation.menu_management.add')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="menuForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="menu_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.menu_management.name')</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.menu_management.slug')</label>
                                <input type="text" class="form-control" id="slug" name="slug" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.menu_management.route')</label>
                                <input type="text" class="form-control" id="route" name="route">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.menu_management.parent_menu')</label>
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="">@lang('translation.menu_management.select_parent')</option>
                                    @foreach($parentMenus as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.menu_management.order')</label>
                                <input type="number" class="form-control" id="order" name="order" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.menu_management.icon')</label>
                                <input type="text" class="form-control" id="icon" name="icon" readonly>
                                <div class="mt-2">
                                    <label class="form-label">@lang('translation.menu_management.selected_icon'): <i id="selected-icon"></i></label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.menu_management.search_icon')</label>
                                <input type="text" class="form-control" id="search-icon" placeholder="Cari icon...">
                            </div>
                            <div class="icon-list" style="height: 300px; overflow-y: auto; border: 1px solid #dee2e6; padding: 10px;">
                                <!-- Icons will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('translation.menu_management.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('translation.menu_management.save')</button>
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
            order: [[7, 'asc']],
        });

        // Fungsi untuk memfilter data
        function filterData() {
            table.rows().every(function() {
                var row = this.node();
                var showActive = $('#statusToggle').is(':checked');
                var status = $(row).find('td:eq(7) .badge').text().trim();
                
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
        $('#statusLabel').text('@lang("translation.menu_management.show_active")');
        filterData();

        // Handle toggle switch untuk filter
        $('#statusToggle').change(function() {
            var isChecked = $(this).is(':checked');
            $('#statusLabel').text(isChecked ? '@lang("translation.menu_management.show_active")' : '@lang("translation.menu_management.show_inactive")');
            filterData();
        });

        // Handle toggle status button
        $(document).on('click', '.toggle-status-btn', function() {
            let button = $(this);
            let id = button.data('id');
            let currentStatus = button.data('status');
            let newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            let confirmMessage = currentStatus === 'active' ? 
                '@lang("translation.menu_management.confirm_deactivate")' : 
                '@lang("translation.menu_management.confirm_activate")';
            
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
                        url: `/menus/${id}/toggle-status`,
                        method: 'PUT',
                        data: {
                            status: newStatus,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if(response.success) {
                                let row = button.closest('tr');
                                
                                row.find('td:eq(7)').html(`
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

        // Implementasi live search
        $('.search').keyup(function() {
            table.search($(this).val()).draw();
        });

        // Setup AJAX CSRF
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Handle form submission
        $('#menuForm').on('submit', function(e) {
            e.preventDefault();

            let id = $('#menu_id').val();
            let url = id ? `/menus/${id}` : '/menus';
            let method = id ? 'PUT' : 'POST';
            let formData = $(this).serialize();

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    if(response.success) {
                        $('#modal-menu').modal('hide');
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
            $('#modal-title').text('@lang("translation.menu_management.edit")');
            $('#menu_id').val($(this).data('id'));
            $('#name').val($(this).data('name'));
            $('#slug').val($(this).data('slug'));
            $('#icon').val($(this).data('icon'));
            $('#route').val($(this).data('route'));
            $('#parent_id').val($(this).data('parent'));
            $('#order').val($(this).data('order'));
            $('#modal-menu').modal('show');
        });

        // Daftar icon Remix Icon yang sering digunakan
        const remixIcons = [
            'ri-dashboard-line', 'ri-home-line', 'ri-user-line', 'ri-settings-line',
            'ri-file-list-line', 'ri-folder-line', 'ri-menu-line', 'ri-search-line',
            'ri-add-line', 'ri-pencil-line', 'ri-delete-bin-line', 'ri-save-line',
            'ri-eye-line', 'ri-eye-off-line', 'ri-lock-line', 'ri-lock-unlock-line',
            'ri-shield-line', 'ri-shield-user-line', 'ri-user-settings-line',
            'ri-team-line', 'ri-group-line', 'ri-user-add-line', 'ri-user-follow-line',
            'ri-database-2-line', 'ri-server-line', 'ri-cloud-line', 'ri-download-line',
            'ri-upload-line', 'ri-refresh-line', 'ri-arrow-left-line', 'ri-arrow-right-line',
            'ri-check-line', 'ri-close-line', 'ri-alert-line', 'ri-information-line',
            'ri-question-line', 'ri-mail-line', 'ri-calendar-line', 'ri-time-line',
            'ri-map-pin-line', 'ri-phone-line', 'ri-customer-service-line',
            'ri-shopping-cart-line', 'ri-bank-card-line', 'ri-coupon-line',
            'ri-price-tag-3-line', 'ri-bar-chart-line', 'ri-pie-chart-line',
            'ri-line-chart-line', 'ri-printer-line', 'ri-file-excel-line',
            'ri-file-pdf-line', 'ri-attachment-line', 'ri-link', 'ri-unlink',
            'ri-share-line', 'ri-chat-1-line', 'ri-message-2-line', 'ri-notification-line'
        ];

        // Populate icon list
        const iconList = $('.icon-list');
        remixIcons.forEach(icon => {
            iconList.append(`
                <div class="icon-item" data-icon="${icon}">
                    <i class="${icon}"></i>
                </div>
            `);
        });

        // Handle icon selection
        $('.icon-item').click(function() {
            const icon = $(this).data('icon');
            $('#icon').val(icon);
            $('#selected-icon').attr('class', icon);
            $('.icon-item').removeClass('selected');
            $(this).addClass('selected');
        });

        // Handle icon search
        $('#search-icon').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.icon-item').each(function() {
                const icon = $(this).data('icon');
                if (icon.toLowerCase().includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Set selected icon when editing
        $('#modal-menu').on('show.bs.modal', function() {
            const icon = $('#icon').val();
            if (icon) {
                $(`.icon-item[data-icon="${icon}"]`).addClass('selected');
                $('#selected-icon').attr('class', icon);
            }
        });

        // Reset icon selection when modal is closed
        $('#modal-menu').on('hidden.bs.modal', function() {
            $('.icon-item').removeClass('selected');
            $('#selected-icon').attr('class', '');
            $('#search-icon').val('');
            $('.icon-item').show();
        });
    });

    // Reset form when modal is closed
    $('#modal-menu').on('hidden.bs.modal', function () {
        $('#menuForm')[0].reset();
        $('#menu_id').val('');
        $('#modal-title').text('@lang("translation.menu_management.add")');
    });
</script>

<!-- App js -->
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
