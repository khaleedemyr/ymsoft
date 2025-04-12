@extends('layouts.master')

@section('title')
    {{ trans('translation.good_receive.add') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .input-group-text {
            background-color: transparent;
        }
        table.table-items th, table.table-items td {
            padding: 0.5rem;
            vertical-align: middle;
        }
        .invalid-feedback {
            display: block;
        }
        
        /* Loading overlay styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .loading-content {
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        /* Camera button styles */
        .camera-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #556ee6;
            color: white;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .camera-button:hover {
            background-color: #4757b6;
            transform: scale(1.1);
        }

        .camera-button i {
            font-size: 28px;
        }

        @media (max-width: 768px) {
            .camera-button {
                width: 50px;
                height: 50px;
                bottom: 15px;
                right: 15px;
            }

            .camera-button i {
                font-size: 24px;
            }
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }
            
            .form-group {
                margin-bottom: 1rem;
            }
            
            .form-group label {
                font-size: 0.9rem;
                margin-bottom: 0.3rem;
            }
            
            .form-control {
                font-size: 0.9rem;
            }
            
            .table-responsive {
                margin: 1rem -1rem;
                width: calc(100% + 2rem);
            }
            
            .table-items {
                font-size: 0.85rem;
            }
            
            .table-items th, 
            .table-items td {
                padding: 0.4rem;
            }
            
            .table-items input {
                font-size: 0.85rem;
                padding: 0.3rem;
            }
            
            .btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
            
            .btn i {
                font-size: 1rem;
            }
            
            .mt-3 {
                margin-top: 1rem !important;
            }
            
            .mt-4 {
                margin-top: 1.5rem !important;
            }
        }

        /* Mobile-first table styles */
        @media (max-width: 576px) {
            .table-items {
                display: block;
            }
            
            .table-items thead {
                display: none;
            }
            
            .table-items tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.25rem;
            }
            
            .table-items td {
                display: block;
                text-align: right;
                padding: 0.5rem;
            }
            
            .table-items td::before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 0.8rem;
                color: #6c757d;
            }
            
            .table-items tfoot {
                display: block;
                margin-top: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.25rem;
            }
            
            .table-items tfoot tr {
                display: block;
            }
            
            .table-items tfoot th {
                display: block;
                text-align: right;
                padding: 0.5rem;
            }
            
            .table-items tfoot th::before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 0.8rem;
                color: #6c757d;
            }
        }

        /* Tooltip styles */
        .info-tooltip {
            display: inline-block;
            margin-left: 5px;
            color: #556ee6;
            cursor: pointer;
        }

        .quantity-info {
            position: relative;
            display: inline-block;
        }

        .quantity-warning {
            color: #f46a6a;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        /* Highlight input when exceeding limit */
        .quantity-exceeded {
            border-color: #f46a6a;
            background-color: #fff8f8;
        }

        /* Slider styles */
        .specs-modal .swiper {
            width: 100%;
            height: 300px;
        }

        .specs-modal .swiper-slide {
            text-align: center;
            background: #f8f8f8;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .specs-modal .swiper-slide img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .specs-modal .swiper-button-next,
        .specs-modal .swiper-button-prev {
            color: #556ee6;
        }

        .specs-modal .swiper-pagination-bullet-active {
            background: #556ee6;
        }

        .specs-modal .modal-body {
            padding: 1rem;
        }

        .specs-modal .specifications {
            margin-top: 1rem;
            padding: 1rem;
            background: #f8f8f8;
            border-radius: 0.25rem;
            white-space: pre-wrap;
        }

        .no-images {
            padding: 2rem;
            text-align: center;
            color: #6c757d;
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('translation.good_receive.add') }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    @component('components.breadcrumb')
                        @slot('li_1')
                            {{ trans('translation.warehouse_management.title') }}
                        @endslot
                        @slot('title')
                            {{ trans('translation.good_receive.add') }}
                        @endslot
                    @endcomponent

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">{{ trans('translation.good_receive.form.title') }}</h4>
                        </div>
                        <div class="card-body">
                            <form id="scan-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{ trans('translation.good_receive.form.scan_qr') }}</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="qr-code" placeholder="{{ trans('translation.good_receive.form.scan_placeholder') }}">
                                                <button type="button" class="btn btn-primary" id="open-camera-btn">
                                                    <i class="ri-camera-line"></i> {{ trans('translation.good_receive.form.open_camera') }}
                                                </button>
                                                <button type="button" class="btn btn-primary" id="search-po-btn">
                                                    <i class="ri-search-line"></i> {{ trans('translation.good_receive.form.search_po') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <form id="good-receive-form" method="POST" action="{{ route('warehouse.good-receives.store') }}">
                                @csrf
                                <input type="hidden" name="po_id" id="po_id">
                                <input type="hidden" name="items" id="items-data">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ trans('translation.good_receive.form.po_number') }}</label>
                                            <input type="text" class="form-control" id="po-number" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ trans('translation.good_receive.form.supplier') }}</label>
                                            <input type="text" class="form-control" id="supplier-name" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ trans('translation.good_receive.form.receive_date') }}</label>
                                            <input type="date" class="form-control" name="receive_date" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{ trans('translation.good_receive.form.notes') }}</label>
                                            <textarea class="form-control" name="notes" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered table-items" id="items-table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    {{ trans('translation.good_receive.form.item_name') }}
                                                </th>
                                                <th>
                                                    {{ trans('translation.good_receive.form.quantity') }}
                                                    <i class="ri-information-line info-tooltip" 
                                                       data-bs-toggle="tooltip" 
                                                       data-bs-placement="top" 
                                                       title="Jumlah pada PO"></i>
                                                </th>
                                                <th>
                                                    {{ trans('translation.good_receive.form.quantity_received') }}
                                                    <i class="ri-information-line info-tooltip" 
                                                       data-bs-toggle="tooltip" 
                                                       data-bs-placement="top" 
                                                       title="Jumlah yang diterima. Maksimal 110% dari jumlah PO"></i>
                                                </th>
                                                <th>{{ trans('translation.good_receive.form.unit') }}</th>
                                                <th>Spesifikasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Modal Spesifikasi dan Foto -->
                                <div class="modal fade specs-modal" id="specsModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Spesifikasi Item</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="swiper-container">
                                                    <div class="swiper-wrapper">
                                                        <!-- Slides will be populated dynamically -->
                                                    </div>
                                                    <div class="swiper-pagination"></div>
                                                    <div class="swiper-button-next"></div>
                                                    <div class="swiper-button-prev"></div>
                                                </div>
                                                <div class="specifications mt-3">
                                                    <!-- Specifications will be populated dynamically -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line"></i> {{ trans('translation.good_receive.form.save') }}
                                    </button>
                                    <a href="{{ route('warehouse.good-receives.index') }}" class="btn btn-secondary">
                                        <i class="ri-arrow-left-line"></i> {{ trans('translation.good_receive.form.cancel') }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-2">Memproses data...</div>
        </div>
    </div>

    <!-- Camera Button -->
    <button type="button" class="camera-button" id="camera-btn">
        <i class="ri-camera-line"></i>
    </button>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>

<script>
$(document).ready(function() {
    // Setup CSRF token untuk semua request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Handle scan button click
    $('#scan-btn').click(function() {
        const qrCode = $('#qr-code').val();
        if (!qrCode) {
            Swal.fire({
                title: '{{ trans('translation.good_receive.message.error_title') }}',
                text: '{{ trans('translation.good_receive.message.qr_required') }}',
                icon: 'error'
            });
            return;
        }

        // Show loading overlay
        $('#loadingOverlay').css('display', 'flex');

        $.ajax({
            url: '{{ route('warehouse.good-receives.scan-qr') }}',
            type: 'POST',
            data: { qr_code: qrCode },
            success: function(response) {
                // Hide loading overlay
                $('#loadingOverlay').hide();

                if (response.success) {
                    // Populate form with data
                    $('#po-number').val(response.data.po_number);
                    $('#supplier-name').val(response.data.supplier_name);
                    $('#total-amount').val(response.data.total_amount);

                    // Populate items table
                    const tbody = $('#items-table tbody');
                    tbody.empty();

                    response.data.items.forEach(function(item) {
                        const maxQty = item.quantity * 1.1; // Maksimal 110% dari quantity PO
                        const row = `
                            <tr>
                                <td data-label="{{ trans('translation.good_receive.form.item_name') }}">${item.name}</td>
                                <td data-label="{{ trans('translation.good_receive.form.quantity') }}">${item.quantity}</td>
                                <td data-label="{{ trans('translation.good_receive.form.quantity_received') }}">
                                    <div class="quantity-info">
                                        <input type="number" class="form-control quantity-received-input" 
                                               name="items[${item.id}][quantity_received]" 
                                               value="0" 
                                               min="0" 
                                               max="${maxQty}"
                                               data-po-qty="${item.quantity}"
                                               data-item-name="${item.name}"
                                               required>
                                        <div class="quantity-warning"></div>
                                    </div>
                                </td>
                                <td data-label="{{ trans('translation.good_receive.form.unit') }}">${item.uom_name}</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm view-specs"
                                            data-item-id="${item.id}"
                                            data-item-name="${item.name}">
                                        <i class="ri-file-list-3-line"></i> SPS
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });

                    // Real-time validation for quantity inputs
                    $(document).on('input', '.quantity-received-input', function() {
                        const input = $(this);
                        const poQty = parseFloat(input.data('po-qty'));
                        const maxQty = poQty * 1.1;
                        const value = parseFloat(input.val());
                        const itemName = input.data('item-name');
                        const warningDiv = input.siblings('.quantity-warning');

                        // Reset styles
                        input.removeClass('quantity-exceeded');
                        warningDiv.hide();

                        if (value > maxQty) {
                            input.addClass('quantity-exceeded');
                            warningDiv.html(`Maksimal ${maxQty} (110% dari PO)`).show();
                            input[0].setCustomValidity(`Jumlah terima untuk ${itemName} tidak boleh lebih dari ${maxQty}`);
                        } else if (value < 0) {
                            input.addClass('quantity-exceeded');
                            warningDiv.html('Jumlah tidak boleh negatif').show();
                            input[0].setCustomValidity('Jumlah tidak boleh negatif');
                        } else {
                            input[0].setCustomValidity('');
                        }
                    });

                    // Show GR form and hide scan form
                    $('#scan-form').hide();
                    $('#good-receive-form').show();
                } else {
                    Swal.fire({
                        title: '{{ trans('translation.good_receive.message.error_title') }}',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function() {
                // Hide loading overlay
                $('#loadingOverlay').hide();

                Swal.fire({
                    title: '{{ trans('translation.good_receive.message.error_title') }}',
                    text: '{{ trans('translation.good_receive.message.scan_error') }}',
                    icon: 'error'
                });
            }
        });
    });

    // Calculate subtotal when quantity or price changes
    $(document).on('input', '.quantity-input, .price-input', function() {
        const row = $(this).closest('tr');
        const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
        const price = parseFloat(row.find('.price-input').val()) || 0;
        const subtotal = quantity * price;
        row.find('.subtotal').text(subtotal.toFixed(2));

        // Update total
        let total = 0;
        $('.subtotal').each(function() {
            total += parseFloat($(this).text()) || 0;
        });
        $('#total-amount').val(total.toFixed(2));
    });

    // Handle form submission
    $('#good-receive-form').submit(function(e) {
        e.preventDefault();
        
        // Validate form
        if (!$('#po_id').val()) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ trans('translation.good_receive.po_not_found') }}'
            });
            return;
        }

        // Validate quantities
        let isValid = true;
        $('.quantity-received').each(function() {
            const input = $(this);
            const poQty = parseFloat(input.data('po-qty'));
            const maxQty = poQty * 1.1;
            const value = parseFloat(input.val()) || 0;
            const itemName = input.data('item-name');

            if (value > maxQty) {
                isValid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `Jumlah terima untuk ${itemName} tidak boleh lebih dari ${maxQty.toFixed(2)} (110% dari PO)`
                });
                return false;
            }
        });

        if (!isValid) return;

        // Update items data before submit
        updateItemsData();

        // Show loading overlay
        $('#loadingOverlay').css('display', 'flex');
        
        // Submit form using AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Hide loading overlay
                $('#loadingOverlay').hide();
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ trans('translation.success') }}',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = response.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ trans('translation.error') }}',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                // Hide loading overlay
                $('#loadingOverlay').hide();
                
                // Show error message
                let errorMessage = '{{ trans('translation.good_receive.message.save_error') }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: '{{ trans('translation.good_receive.message.error_title') }}',
                    text: errorMessage
                });
            }
        });
    });

    // Handle open camera button click
    $('#open-camera-btn').click(function() {
        // Check if browser supports getUserMedia
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            Swal.fire({
                title: '{{ trans('translation.good_receive.message.error_title') }}',
                text: '{{ trans('translation.good_receive.message.camera_not_supported') }}',
                icon: 'error'
            });
            return;
        }

        // Request camera access
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(function(stream) {
                // Create video element
                const video = document.createElement('video');
                video.setAttribute('playsinline', '');
                video.setAttribute('autoplay', '');
                video.srcObject = stream;

                // Create canvas for QR code scanning
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');

                // Create modal for camera preview
                const modal = `
                    <div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ trans('translation.good_receive.form.scan_qr') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="camera-preview" class="position-relative">
                                        <div class="camera-controls position-absolute top-0 end-0 p-2">
                                            <button type="button" class="btn btn-light btn-sm" id="switch-camera-btn">
                                                <i class="ri-camera-switch-line"></i> {{ trans('translation.good_receive.form.switch_camera') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Add modal to body
                $('body').append(modal);
                $('#camera-preview').append(video);

                // Show modal
                const cameraModal = new bootstrap.Modal(document.getElementById('cameraModal'));
                cameraModal.show();

                let currentStream = stream;
                let facingMode = 'environment';

                // Handle camera switch
                $('#switch-camera-btn').click(function() {
                    // Stop current stream
                    currentStream.getTracks().forEach(track => track.stop());

                    // Switch facing mode
                    facingMode = facingMode === 'environment' ? 'user' : 'environment';

                    // Request new stream
                    navigator.mediaDevices.getUserMedia({ video: { facingMode: facingMode } })
                        .then(function(newStream) {
                            currentStream = newStream;
                            video.srcObject = newStream;
                        })
                        .catch(function(err) {
                            Swal.fire({
                                title: '{{ trans('translation.good_receive.message.error_title') }}',
                                text: '{{ trans('translation.good_receive.message.camera_switch_error') }}',
                                icon: 'error'
                            });
                        });
                });

                // Handle modal close
                $('#cameraModal').on('hidden.bs.modal', function () {
                    currentStream.getTracks().forEach(track => track.stop());
                    $(this).remove();
                });

                // Start scanning
                function scanQR() {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);

                    // Here you would implement QR code scanning logic
                    // For example, using a library like jsQR
                    // For now, we'll just simulate it
                    setTimeout(scanQR, 100);
                }

                scanQR();
            })
            .catch(function(err) {
                Swal.fire({
                    title: '{{ trans('translation.good_receive.message.error_title') }}',
                    text: '{{ trans('translation.good_receive.message.camera_access_denied') }}',
                    icon: 'error'
                });
            });
    });

    // Handle search PO button click
    $('#search-po-btn').click(function() {
        const poNumber = $('#qr-code').val();
        if (!poNumber) {
            Swal.fire({
                title: '{{ trans('translation.good_receive.message.error_title') }}',
                text: '{{ trans('translation.good_receive.message.po_number_required') }}',
                icon: 'error'
            });
            return;
        }

        // Show loading overlay
        $('#loadingOverlay').css('display', 'flex');

        $.ajax({
            url: '{{ route('warehouse.good-receives.search-po') }}',
            type: 'POST',
            data: { po_number: poNumber },
            success: function(response) {
                console.log('PO Items:', response.data.items); // Tambahkan log ini untuk melihat struktur data
                // Hide loading overlay
                $('#loadingOverlay').hide();

                if (response.success) {
                    // Populate form with data
                    $('#po_id').val(response.data.id);
                    $('#po-number').val(response.data.po_number);
                    $('#supplier-name').val(response.data.supplier_name);

                    // Populate items table
                    let items = [];
                    let tbody = $('#items-table tbody');
                    tbody.empty();

                    response.data.items.forEach(function(item) {
                        items.push({
                            item_id: item.id,
                            quantity_received: 0
                        });
                        
                        const maxQty = item.quantity * 1.1; // Maksimal 110% dari quantity PO
                        tbody.append(`
                            <tr>
                                <td>${item.name}</td>
                                <td>${item.quantity}</td>
                                <td>
                                    <div class="quantity-info">
                                        <input type="number" class="form-control quantity-received" 
                                               data-item-id="${item.id}"
                                               data-unit-id="${item.unit_id}"
                                               data-po-qty="${item.quantity}"
                                               data-item-name="${item.name}"
                                               min="0" 
                                               max="${maxQty}"
                                               step="1"
                                               value="0"
                                               required>
                                        <div class="quantity-warning"></div>
                                    </div>
                                </td>
                                <td>${item.unit}</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm view-specs"
                                            data-item-id="${item.id}"
                                            data-item-name="${item.name}">
                                        <i class="ri-file-list-3-line"></i> SPS
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                    
                    $('#items-data').val(JSON.stringify(items));

                    // Real-time validation untuk quantity
                    $(document).on('input', '.quantity-received', function() {
                        const input = $(this);
                        const poQty = parseFloat(input.data('po-qty'));
                        const maxQty = poQty * 1.1;
                        const value = parseFloat(input.val()) || 0;
                        const itemName = input.data('item-name');
                        const warningDiv = input.closest('.quantity-info').find('.quantity-warning');

                        // Reset styles
                        input.removeClass('quantity-exceeded');
                        warningDiv.hide();

                        if (value > maxQty) {
                            input.addClass('quantity-exceeded');
                            warningDiv.html(`Maksimal ${maxQty.toFixed(2)} (110% dari PO)`).show();
                            input[0].setCustomValidity(`Jumlah terima untuk ${itemName} tidak boleh lebih dari ${maxQty.toFixed(2)}`);
                            
                            // Set nilai ke maksimal yang diizinkan
                            input.val(maxQty.toFixed(2));
                        } else if (value < 0) {
                            input.addClass('quantity-exceeded');
                            warningDiv.html('Jumlah tidak boleh negatif').show();
                            input[0].setCustomValidity('Jumlah tidak boleh negatif');
                            
                            // Set nilai ke 0
                            input.val(0);
                        } else {
                            input[0].setCustomValidity('');
                        }

                        // Update items data
                        updateItemsData();
                    });

                    // Show GR form and hide scan form
                    $('#scan-form').hide();
                    $('#good-receive-form').show();
                } else {
                    Swal.fire({
                        title: '{{ trans('translation.good_receive.message.error_title') }}',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Search PO error:', {xhr, status, error});
                $('#loadingOverlay').hide();
                Swal.fire({
                    title: '{{ trans('translation.good_receive.message.error_title') }}',
                    text: '{{ trans('translation.good_receive.message.search_error') }}',
                    icon: 'error'
                });
            }
        });
    });

    function populateForm(data) {
        $('#po_id').val(data.id);
        $('#po-number').val(data.po_number);
        $('#supplier-name').val(data.supplier.name);
        
        // Populate items table
        let items = [];
        let tbody = $('#items-table tbody');
        tbody.empty();
        
        data.items.forEach(function(item) {
            items.push({
                item_id: item.id,
                quantity_received: 0
            });
            
            tbody.append(`
                <tr>
                    <td>${item.name}</td>
                    <td>${item.quantity}</td>
                    <td>
                        <input type="number" class="form-control quantity-received" 
                               data-item-id="${item.id}" 
                               min="0" 
                               max="${item.quantity}" 
                               value="0">
                    </td>
                    <td>${item.uom_name}</td>
                    <td>
                        <button type="button" class="btn btn-info btn-sm view-specs"
                                data-item-id="${item.id}"
                                data-item-name="${item.name}">
                            <i class="ri-file-list-3-line"></i> SPS
                        </button>
                    </td>
                </tr>
            `);
        });
        
        $('#items-data').val(JSON.stringify(items));
        $('#scan-modal').modal('hide');
    }

    // Fungsi untuk update items data
    function updateItemsData() {
        let items = [];
        $('.quantity-received').each(function() {
            items.push({
                item_id: $(this).data('item-id'),
                quantity_received: parseFloat($(this).val()) || 0,
                unit_id: $(this).data('unit-id')
            });
        });
        $('#items-data').val(JSON.stringify(items));
    }

    // Fungsi untuk memuat spesifikasi dan foto
    function loadSpecsAndImages(itemId, itemName) {
        $('#specsModal .modal-title').text('Spesifikasi: ' + itemName);
        
        // Show loading dalam modal
        $('#specsModal .modal-body').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>');
        $('#specsModal').modal('show');

        // Ambil data spesifikasi dan foto
        $.ajax({
            url: '{{ route('warehouse.items.specs-and-images') }}',
            type: 'GET',
            data: { item_id: itemId },
            success: function(response) {
                if (response.success) {
                    const swiperWrapper = $('<div class="swiper-container"><div class="swiper-wrapper"></div><div class="swiper-pagination"></div><div class="swiper-button-next"></div><div class="swiper-button-prev"></div></div>');
                    
                    // Tambahkan slides jika ada gambar
                    if (response.images && response.images.length > 0) {
                        response.images.forEach(function(image) {
                            swiperWrapper.find('.swiper-wrapper').append(`
                                <div class="swiper-slide">
                                    <img src="${image.path}" alt="Item Image">
                                </div>
                            `);
                        });
                    } else {
                        swiperWrapper.find('.swiper-wrapper').append(`
                            <div class="no-images">
                                <i class="ri-image-line" style="font-size: 3rem;"></i>
                                <p class="mt-2">Tidak ada foto tersedia</p>
                            </div>
                        `);
                    }

                    // Tambahkan spesifikasi
                    const specifications = $('<div class="specifications mt-3"></div>').html(
                        response.specifications || 'Tidak ada spesifikasi tersedia'
                    );

                    // Update modal content
                    $('#specsModal .modal-body').empty()
                        .append(swiperWrapper)
                        .append(specifications);

                    // Initialize Swiper
                    if (response.images && response.images.length > 0) {
                        new Swiper('.swiper-container', {
                            pagination: {
                                el: '.swiper-pagination',
                                clickable: true
                            },
                            navigation: {
                                nextEl: '.swiper-button-next',
                                prevEl: '.swiper-button-prev',
                            },
                        });
                    }
                } else {
                    $('#specsModal .modal-body').html('<div class="alert alert-danger">Gagal memuat data</div>');
                }
            },
            error: function() {
                $('#specsModal .modal-body').html('<div class="alert alert-danger">Gagal memuat data</div>');
            }
        });
    }

    // Handle view specs button click
    $(document).on('click', '.view-specs', function() {
        const itemId = $(this).data('item-id');
        const itemName = $(this).data('item-name');
        loadSpecsAndImages(itemId, itemName);
    });
});
</script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 