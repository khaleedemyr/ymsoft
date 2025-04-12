@extends('layouts.master')

@section('title')
    @lang('translation.supplier.title')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Sweet Alert-->
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    
    <style>
        .search-box {
            position: relative;
        }
        .search-box .form-control {
            padding-left: 40px;
        }
        .search-box .search-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #74788d;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
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
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .toggle-slider {
            background-color: #556ee6;
        }
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        .payment-days-div {
            transition: all 0.3s ease;
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
                    Supplier
        @endslot
    @endcomponent

    <div class="row">
                <div class="col-lg-12">
                    <div class="card" id="supplierList">
                        <div class="card-header d-flex align-items-center">
                            <h6 class="card-title flex-grow-1 mb-0">@lang('translation.supplier.list')</h6>
                            <div class="flex-shrink-0">
                                <div class="d-flex flex-wrap gap-2">
                                    <!-- Toggle switch -->
                                    <div class="d-flex align-items-center">
                                        <label class="toggle-switch">
                                            <input type="checkbox" id="statusToggle" checked>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <span class="toggle-label" id="statusLabel">@lang('translation.supplier.show_active')</span>
                                </div>
                                    <!-- Search box -->
                                    <div class="search-box">
                                        <input type="text" class="form-control search" placeholder="@lang('translation.supplier.search_placeholder')">
                            </div>
                                    <button type="button" class="btn btn-primary add-btn" data-bs-toggle="modal" data-bs-target="#modal-supplier">
                                        <i class="ri-add-line align-bottom me-1"></i> @lang('translation.supplier.add')
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
                                                <th>@lang('translation.supplier.code')</th>
                                                <th>@lang('translation.supplier.name')</th>
                                                <th>@lang('translation.supplier.contact_person')</th>
                                                <th>@lang('translation.supplier.phone')</th>
                                                <th>@lang('translation.supplier.city')</th>
                                                <th>@lang('translation.supplier.status')</th>
                                                <th>@lang('translation.supplier.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                            @foreach($suppliers as $key => $supplier)
                                <tr>
                                                <td>{{ $key + 1 }}</td>
                                    <td>{{ $supplier->code }}</td>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->contact_person ?? '-' }}</td>
                                    <td>{{ $supplier->phone ?? '-' }}</td>
                                    <td>{{ $supplier->city ?? '-' }}</td>
                                    <td>
                                                    <span class="badge bg-{{ $supplier->status == 'active' ? 'success' : 'danger' }}">
                                                        {{ $supplier->status == 'active' ? __('translation.supplier.active') : __('translation.supplier.inactive') }}
                                                </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                                        <button class="btn btn-sm btn-link text-primary view-btn" 
                                                                data-id="{{ $supplier->id }}">
                                                            <i class="ti ti-eye fs-5"></i>
                                            </button>
                                                        <button class="btn btn-sm btn-link text-primary edit-btn" 
                                                                data-id="{{ $supplier->id }}">
                                                            <i class="ti ti-edit fs-5"></i>
                                            </button>
                                                        <button class="btn btn-sm btn-link text-{{ $supplier->status == 'active' ? 'danger' : 'success' }} toggle-status-btn"
                                                                data-id="{{ $supplier->id }}"
                                                                data-status="{{ $supplier->status }}">
                                                            <i class="ti ti-{{ $supplier->status == 'active' ? 'ban' : 'check' }} fs-5"></i>
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

<!-- Modal Supplier -->
<div class="modal fade" id="modal-supplier" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">@lang('translation.supplier.add')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="supplierForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="supplier_id" name="id">
                    <input type="hidden" id="status" name="status" value="active">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.code') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.name') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.contact_person')</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.phone')</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.email')</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.npwp')</label>
                                <input type="text" class="form-control" id="npwp" name="npwp">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.address')</label>
                                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.city')</label>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.province')</label>
                                <input type="text" class="form-control" id="province" name="province">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.postal_code')</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code">
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <h5 class="mt-2">@lang('translation.supplier.bank_info')</h5>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.bank_name')</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.bank_account_number')</label>
                                <input type="text" class="form-control" id="bank_account_number" name="bank_account_number">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.bank_account_name')</label>
                                <input type="text" class="form-control" id="bank_account_name" name="bank_account_name">
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <h5 class="mt-2">@lang('translation.supplier.payment_terms')</h5>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.payment_term')</label>
                                <select class="form-select" id="payment_term" name="payment_term">
                                    <option value="cash">@lang('translation.supplier.payment_cash')</option>
                                    <option value="credit">@lang('translation.supplier.payment_credit')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 payment-days-div">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.payment_days')</label>
                                <input type="number" class="form-control" id="payment_days" name="payment_days" min="0" value="0">
                            </div>
                        </div>
                        
                        <!-- Status field (hanya ditampilkan saat edit) -->
                        <div class="col-md-6 status-field" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.supplier.status')</label>
                                <select class="form-select" id="edit_status" name="status">
                                    <option value="active">@lang('translation.supplier.active')</option>
                                    <option value="inactive">@lang('translation.supplier.inactive')</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('translation.supplier.close')</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">@lang('translation.supplier.save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- View Supplier Modal -->
    <div class="modal fade" id="viewSupplierModal" tabindex="-1" aria-labelledby="viewSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewSupplierModalLabel">@lang('translation.supplier.view')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">@lang('translation.supplier.code')</th>
                                    <td id="view_code"></td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.supplier.name')</th>
                                    <td id="view_name"></td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.supplier.contact_person')</th>
                                    <td id="view_contact_person"></td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.supplier.phone')</th>
                                    <td id="view_phone"></td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.supplier.email')</th>
                                    <td id="view_email"></td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.supplier.npwp')</th>
                                    <td id="view_npwp"></td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.supplier.payment_term')</th>
                                    <td id="view_payment_info"></td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.supplier.status')</th>
                                    <td id="view_status"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">@lang('translation.supplier.address')</th>
                                    <td id="view_address"></td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.supplier.city')</th>
                                    <td id="view_city"></td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.supplier.province')</th>
                                    <td id="view_province"></td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.supplier.postal_code')</th>
                                    <td id="view_postal_code"></td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.supplier.bank_name')</th>
                                    <td id="view_bank_name"></td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.supplier.bank_account_number')</th>
                                    <td id="view_bank_account_number"></td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.supplier.bank_account_name')</th>
                                    <td id="view_bank_account_name"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('translation.supplier.close')</button>
                </div>
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
            // Cek CSRF token
            console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
            
            // Setup AJAX CSRF
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Inisialisasi DataTable dengan konfigurasi search dan filter
            var table = $('#datatable').DataTable({
                dom: 'rt<"bottom"ip>',
                pageLength: 10,
                ordering: true,
                responsive: true,
                order: [[6, 'asc']],
            });

            // Fungsi untuk memfilter data
            function filterData() {
                table.rows().every(function() {
                    var row = this.node();
                    var showActive = $('#statusToggle').is(':checked');
                    var status = $(row).find('td:eq(6) .badge').text().trim();
                    
                    if (showActive && status === '@lang("translation.supplier.active")' ||
                        !showActive && status === '@lang("translation.supplier.inactive")') {
                        $(row).show();
                    } else {
                        $(row).hide();
                    }
                });
            }

            // Set toggle ke active dan trigger filter saat pertama kali load
            $('#statusToggle').prop('checked', true);
            $('#statusLabel').text('@lang("translation.supplier.show_active")');
            filterData();

            // Handle toggle switch untuk filter
            $('#statusToggle').change(function() {
                var isChecked = $(this).is(':checked');
                $('#statusLabel').text(isChecked ? 
                    '@lang("translation.supplier.show_active")' : 
                    '@lang("translation.supplier.show_inactive")'
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
                    '@lang("translation.supplier.confirm_deactivate")' : 
                    '@lang("translation.supplier.confirm_activate")';

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
                            url: `/master-data/suppliers/${id}/toggle-status`,
                            method: 'POST',
                            data: {
                                status: newStatus,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if(response.success) {
                                    let row = button.closest('tr');
                                    
                                    // Update badge
                                    row.find('td:eq(6)').html(`
                                        <span class="badge bg-${newStatus === 'active' ? 'success' : 'danger'}">
                                            ${newStatus === 'active' ? '@lang("translation.supplier.active")' : '@lang("translation.supplier.inactive")'}
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

            // Form submission
            $('#supplierForm').on('submit', function(e) {
                e.preventDefault();
                
                let id = $('#supplier_id').val();
                let url = id ? `/master-data/suppliers/${id}` : '/master-data/suppliers';
                let method = id ? 'PUT' : 'POST';
                
                // Disable button & tampilkan loading
                let saveBtn = $('#saveBtn');
                saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                
                // Serialize form data
                let formData = $(this).serialize();
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData + (method === 'PUT' ? '&_method=PUT' : ''),
                    success: function(response) {
                        if (response.success) {
                            // Tutup modal
                            $('#modal-supplier').modal('hide');
                            
                            // Tampilkan pesan sukses
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message || 'Data berhasil disimpan',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                // Refresh halaman
                                location.reload();
                            });
                        } else {
                            // Tampilkan pesan error
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan saat menyimpan data'
                            });
                            
                            // Reset tombol
                            saveBtn.prop('disabled', false).html('@lang("translation.supplier.save")');
                        }
                    },
                    error: function(xhr) {
                        // Tampilkan pesan error
                        let errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data';
                        
                        // Check if there are validation errors
                        if (xhr.responseJSON?.errors) {
                            errorMessage = '';
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errorMessage += value[0] + '<br>';
                            });
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            html: errorMessage
                        });
                        
                        // Reset tombol
                        saveBtn.prop('disabled', false).html('@lang("translation.supplier.save")');
                    }
                });
            });

            // Handle edit button
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                
                $('#modal-title').text('@lang("translation.supplier.edit")');
                $('#supplier_id').val(id);
                
                // Tampilkan field status saat edit
                $('.status-field').show();
                
                // Ambil data supplier dengan AJAX
                $.ajax({
                    url: `/master-data/suppliers/${id}/edit`,
                    method: 'GET',
                    success: function(response) {
                        console.log('Response from edit:', response); // Debug log
                        
                        if (response.success) {
                            let supplier = response.supplier;
                            
                            // Isi semua field dengan data supplier
                            $('#code').val(supplier.code);
                            $('#name').val(supplier.name);
                            $('#contact_person').val(supplier.contact_person);
                            $('#phone').val(supplier.phone);
                            $('#email').val(supplier.email);
                            $('#npwp').val(supplier.npwp);
                            $('#address').val(supplier.address);
                            $('#city').val(supplier.city);
                            $('#province').val(supplier.province);
                            $('#postal_code').val(supplier.postal_code);
                            $('#bank_name').val(supplier.bank_name);
                            $('#bank_account_number').val(supplier.bank_account_number);
                            $('#bank_account_name').val(supplier.bank_account_name);
                            
                            // Isi data payment term dan payment days
                            $('#payment_term').val(supplier.payment_term || 'cash');
                            $('#payment_days').val(supplier.payment_days || 0);
                            
                            // Update status
                            $('#edit_status').val(supplier.status);
                            
                            // Tampilkan/sembunyikan payment days berdasarkan payment term
                            if (supplier.payment_term === 'credit') {
                                $('.payment-days-div').show();
                            } else {
                                $('.payment-days-div').hide();
                            }
                            
                            // Tampilkan modal
                            $('#modal-supplier').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Gagal memuat data supplier'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Gagal memuat data supplier'
                        });
                    }
                });
            });

            // Handle view button
            $(document).on('click', '.view-btn', function() {
                let id = $(this).data('id');
                
                $.ajax({
                    url: '/master-data/suppliers/' + id,
                    method: 'GET',
                    success: function(response) {
                        let supplier = response.supplier;
                        
                        // Fill modal fields
                        $('#view_code').text(supplier.code);
                        $('#view_name').text(supplier.name);
                        $('#view_contact_person').text(supplier.contact_person || '-');
                        $('#view_phone').text(supplier.phone || '-');
                        $('#view_email').text(supplier.email || '-');
                        $('#view_npwp').text(supplier.npwp || '-');
                        $('#view_address').text(supplier.address || '-');
                        $('#view_city').text(supplier.city || '-');
                        $('#view_province').text(supplier.province || '-');
                        $('#view_postal_code').text(supplier.postal_code || '-');
                        $('#view_bank_name').text(supplier.bank_name || '-');
                        $('#view_bank_account_number').text(supplier.bank_account_number || '-');
                        $('#view_bank_account_name').text(supplier.bank_account_name || '-');
                        
                        // Set payment term info
                        let paymentInfo;
                        if (supplier.payment_term === 'credit') {
                            paymentInfo = '@lang("translation.supplier.payment_credit") - ' + 
                                          (supplier.payment_days || '0') + ' @lang("translation.supplier.days")';
                        } else {
                            paymentInfo = '@lang("translation.supplier.payment_cash")';
                        }
                        $('#view_payment_info').text(paymentInfo);
                        
                        // Set status with badge
                        let statusBadge = supplier.status === 'active' 
                            ? '<span class="badge bg-success">@lang("translation.supplier.active")</span>' 
                            : '<span class="badge bg-danger">@lang("translation.supplier.inactive")</span>';
                        $('#view_status').html(statusBadge);
                        
                        // Show modal
                        $('#viewSupplierModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load supplier data'
                        });
                    }
                });
            });

            // Toggle payment days berdasarkan payment term
            function togglePaymentDays() {
                if ($('#payment_term').val() === 'credit') {
                    $('.payment-days-div').show();
                } else {
                    $('.payment-days-div').hide();
                    $('#payment_days').val(0);
                }
            }

            // Event listener untuk perubahan payment term
            $('#payment_term').on('change', function() {
                togglePaymentDays();
            });

            // Panggil togglePaymentDays saat halaman dimuat
            togglePaymentDays();

            // Untuk add button
            $('.add-btn').on('click', function() {
                $('#supplier_id').val('');
                $('#method').val('POST');
                $('#supplierForm').attr('action', '/master-data/suppliers');
                // Kode lainnya...
            });

            // Tampilkan SweetAlert jika ada session flash message
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 1500
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: "{{ session('error') }}"
                });
            @endif
        });
    </script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection