/**
 * Maintenance Purchase Order JS
 */
(function () {
    'use strict';

    // Global variables
    let currentTaskId = null;
    let currentPoId = null;
    let currentPo = null;
    
    // Global variables untuk camera
    let stream;
    let currentFacingMode = 'environment'; // 'environment' untuk kamera belakang
    let capturedPhotos = [];
    
    // Initialize PO functionality
    function initPo() {
        // Event handler untuk tombol PO di task card
        $(document).on('click', '.view-po-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const taskId = $(this).data('task-id');
            currentTaskId = taskId;
            
            // Load PO list untuk task ini
            loadPoList(taskId);
        });

        // Event untuk tombol Create PO
        $(document).on('click', '.add-new-po-btn', function() {
            $('#poListModal').modal('hide');
            showCreatePoModal();
        });

        // Event handler untuk tombol detail
        $(document).on('click', '.view-po-detail-btn', function() {
            const poId = $(this).data('po-id');
            loadPoDetail(poId);
        });

        // Event untuk tombol upload invoice
        $(document).on('click', '.upload-invoice-btn', function() {
            const poId = $(this).data('po-id');
            const poNumber = $(this).data('po-number');
            
            // Set PO info di modal
            $('#uploadInvoiceModal').data('po-id', poId);
            $('#uploadInvoiceModal .modal-title').text(`Upload Invoice - PO ${poNumber}`);
            
            // Reset form
            $('#invoiceForm')[0].reset();
            
            // Show modal
            $('#uploadInvoiceModal').modal('show');
        });

        // Event handler untuk tombol create good receive
        $(document).on('click', '#createGoodReceiveBtn', async function(e) {
            e.preventDefault();
            console.log('Opening good receive modal');
            
            // Reset form dan photos
            $('#goodReceiveForm')[0].reset();
            capturedPhotos = [];
            const previewContainer = document.getElementById('photoPreviewContainer');
            if (previewContainer) {
                previewContainer.innerHTML = ''; // Bersihkan container preview
            }
            updateNoPhotosMessage();
            
            // Set default date
            $('input[name="receive_date"]').val(new Date().toISOString().split('T')[0]);
            
            // Show modal first
            $('#goodReceiveModal').modal('show');
            
            // Start camera after modal is shown
            $('#goodReceiveModal').on('shown.bs.modal', async function() {
                console.log('Modal shown, starting camera');
                await startCamera();
            });
        });
    }
    
    // Fungsi untuk memuat daftar PO
    function loadPoList(taskId) {
        $.ajax({
            url: `/maintenance/task/${taskId}/po/list`,
            method: 'GET',
            beforeSend: function() {
                $('#poListTableBody').html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');
                $('#noPOMessage').addClass('d-none');
            },
            success: function(response) {
                if (response.success) {
                    const pos = response.data;
                    const task = response.task;
                    const canApprove = response.can_approve;
                    
                    // Debug log
                    console.log('PO List Response:', response);
                    
                    // Update task number display
                    $('.task-number-display').text(`Task: ${task.task_number}`);
                    
                    // Populate table
                    if (pos && pos.length > 0) {
                        let html = '';
                        
                        pos.forEach(function(po) {
                            // Debug log
                            console.log('Processing PO:', po);
                            
                            const createdAt = new Date(po.created_at).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });
                            
                            const totalAmount = formatCurrency(po.total_amount);
                            
                            // Gunakan fungsi getStatusClass untuk styling badge
                            const statusClass = getStatusClass(po.status);
                            
                            html += `
                                <tr>
                                    <td>${po.po_number || '-'}</td>
                                    <td>${po.supplier ? po.supplier.name : '-'}</td>
                                    <td>${createdAt}</td>
                                    <td><span class="badge ${statusClass}">${po.status}</span></td>
                                    <td class="text-end">${totalAmount}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-info view-po-detail-btn" 
                                                    data-po-id="${po.id}" 
                                                    title="View Detail">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary upload-invoice-btn"
                                                    data-po-id="${po.id}"
                                                    data-po-number="${po.po_number}"
                                                    title="Upload Invoice">
                                                <i class="ri-upload-2-line"></i>
                                            </button>
                                            <a href="/maintenance/po/${po.id}/preview" 
                                               class="btn btn-sm btn-info" 
                                               target="_blank" 
                                               title="Preview PO">
                                                <i class="ri-file-text-line"></i>
                                            </a>
                                            ${canApprove && po.status !== 'APPROVED' ? `
                                                <button class="btn btn-sm btn-success approve-po-btn" 
                                                        data-po-id="${po.id}"
                                                        title="Approve PO">
                                                    <i class="ri-check-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger reject-po-btn" 
                                                        data-po-id="${po.id}"
                                                        title="Reject PO">
                                                    <i class="ri-close-line"></i>
                                                </button>
                                            ` : ''}
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        $('#poListTableBody').html(html);
                        $('#noPOMessage').addClass('d-none');
                    } else {
                        $('#poListTableBody').html('');
                        $('#noPOMessage').removeClass('d-none');
                    }
                    
                    // Show modal
                    $('#poListModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to load POs'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error loading POs:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load POs. Please try again later.'
                });
            }
        });
    }

    // Fungsi untuk menampilkan modal create PO
    function showCreatePoModal() {
        // Load PR items untuk task yang sedang aktif
        loadPrItems(currentTaskId);
    }

    // Fungsi untuk memuat items PR
    function loadPrItems(taskId) {
        $.ajax({
            url: `/maintenance/task/${taskId}/pr-items`,
            method: 'GET',
            beforeSend: function() {
                $('#prAccordion').html('<div class="text-center">Loading...</div>');
                $('#groupBySupplierBtn').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    const prs = response.prs;
                    const suppliers = response.suppliers;
                    
                    let html = '';
                    prs.forEach(function(pr, index) {
                        html += `
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button ${index === 0 ? '' : 'collapsed'}" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#pr${pr.pr_id}">
                                        ${pr.pr_number} - Total: ${formatCurrency(pr.total_amount)}
                                    </button>
                                </h2>
                                <div id="pr${pr.pr_id}" 
                                     class="accordion-collapse collapse ${index === 0 ? 'show' : ''}"
                                     data-bs-parent="#prAccordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Item Name</th>
                                                        <th>Description</th>
                                                        <th>Specifications</th>
                                                        <th>Quantity</th>
                                                        <th>Unit</th>
                                                        <th>Price (PR)</th>
                                                        <th>Subtotal (PR)</th>
                                                        <th>Supplier</th>
                                                        <th>Price (Supplier)</th>
                                                        <th>Subtotal (Supplier)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${pr.items.map(item => `
                                                        <tr data-item-id="${item.id}" data-pr-id="${pr.pr_id}">
                                                            <td>${item.item_name}</td>
                                                            <td>${item.description || '-'}</td>
                                                            <td>${item.specifications || '-'}</td>
                                                            <td class="text-end">${formatNumber(item.quantity)}</td>
                                                            <td>${item.unit_name}</td>
                                                            <td class="text-end">${formatCurrency(item.price)}</td>
                                                            <td class="text-end">${formatCurrency(item.subtotal)}</td>
                                                            <td>
                                                                <select class="form-select supplier-select" 
                                                                        data-item-id="${item.id}">
                                                                    <option value="">Pilih Supplier</option>
                                                                    ${suppliers.map(s => `
                                                                        <option value="${s.id}" 
                                                                                data-payment-term="${s.payment_term}"
                                                                                data-payment-days="${s.payment_days}"
                                                                                data-contact="${s.contact_person}"
                                                                                data-phone="${s.phone}"
                                                                                data-email="${s.email}"
                                                                                data-address="${s.address}"
                                                                                data-city="${s.city}"
                                                                                data-province="${s.province}">
                                                                            ${s.code} - ${s.name}
                                                                        </option>
                                                                    `).join('')}
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number" 
                                                                       class="form-control supplier-price" 
                                                                       data-item-id="${item.id}"
                                                                       step="0.01" 
                                                                       min="0">
                                                            </td>
                                                            <td class="text-end supplier-subtotal">
                                                                ${formatCurrency(0)}
                                                            </td>
                                                        </tr>
                                                    `).join('')}
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="6" class="text-end fw-bold">Total PR:</td>
                                                        <td class="text-end fw-bold">${formatCurrency(pr.total_amount)}</td>
                                                        <td colspan="2" class="text-end fw-bold">Total Supplier:</td>
                                                        <td class="text-end fw-bold total-supplier-amount">${formatCurrency(0)}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    $('#prAccordion').html(html);
                    
                    // Tambahkan event listeners setelah HTML dirender
                    $('.supplier-select, .supplier-price').on('change keyup', function() {
                        updateSupplierSubtotal(this);
                    });
                    
                    if (prs.length === 0) {
                        $('#prAccordion').html(`
                            <div class="alert alert-info">
                                Tidak ada PR yang disetujui untuk task ini.
                            </div>
                        `);
                        $('#groupBySupplierBtn').prop('disabled', true);
                    } else {
                        $('#groupBySupplierBtn').prop('disabled', false);
                    }
                    
                    // Show modal
                    $('#createPoModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Gagal memuat item PR'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error loading PR items:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat item PR. Silakan coba lagi nanti.'
                });
            }
        });
    }

    // Helper function untuk format angka
    function formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    // Helper function untuk format currency
    function formatCurrency(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    }

    // Definisikan fungsi updateSupplierSubtotal di scope global
    window.updateSupplierSubtotal = function(element) {
        const row = $(element).closest('tr');
        // Ambil quantity dari kolom quantity (hilangkan pemisah ribuan)
        const quantity = parseFloat(row.find('td:eq(3)').text().replace(/[,.]/g, ''));
        const supplierPrice = parseFloat(row.find('.supplier-price').val()) || 0;
        const subtotal = quantity * supplierPrice;
        
        // Update subtotal cell dengan format yang sama dengan PR
        row.find('.supplier-subtotal').text(formatCurrency(subtotal));
        
        // Update total supplier amount untuk PR ini
        const table = row.closest('table');
        let totalSupplierAmount = 0;
        
        // Hitung total dari semua subtotal supplier
        table.find('.supplier-subtotal').each(function() {
            // Bersihkan string dari currency format (Rp, titik, koma)
            const value = $(this).text().replace(/[^0-9-]/g, '');
            totalSupplierAmount += parseFloat(value) || 0;
        });
        
        // Update total dengan format yang sama dengan PR
        table.find('.total-supplier-amount').text(formatCurrency(totalSupplierAmount));
    }

    // Event handler untuk Group by Supplier button
    $('#groupBySupplierBtn').on('click', function() {
        // Validasi input
        let isValid = true;
        let groupedItems = {}; // Object untuk mengelompokkan item berdasarkan supplier
        
        $('#prAccordion .table tbody tr').each(function() {
            const $row = $(this);
            const supplierId = $row.find('.supplier-select').val();
            const supplierPrice = $row.find('.supplier-price').val();
            const quantity = parseFloat($row.find('td:eq(3)').text().replace(/[,.]/g, ''));
            const subtotal = quantity * (parseFloat(supplierPrice) || 0);
            
            if (!supplierId) {
                $row.find('.supplier-select').addClass('is-invalid');
                isValid = false;
            } else {
                $row.find('.supplier-select').removeClass('is-invalid');
            }
            
            if (!supplierPrice) {
                $row.find('.supplier-price').addClass('is-invalid');
                isValid = false;
            } else {
                $row.find('.supplier-price').removeClass('is-invalid');
            }
            
            if (supplierId && supplierPrice) {
                // Inisialisasi array untuk supplier jika belum ada
                if (!groupedItems[supplierId]) {
                    const $selectedOption = $row.find('.supplier-select option:selected');
                    groupedItems[supplierId] = {
                        supplier_id: supplierId,
                        task_id: currentTaskId,
                        items: []
                    };
                }

                // Tambahkan item ke array supplier yang sesuai
                groupedItems[supplierId].items.push({
                    item_id: $row.data('item-id'),
                    pr_id: $row.data('pr-id'),
                    supplier_price: parseFloat(supplierPrice),
                    supplier_subtotal: subtotal,
                    quantity: quantity
                });
            }
        });
        
        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Error',
                text: 'Mohon pilih supplier dan masukkan harga untuk semua item'
            });
            return;
        }

        // Jika tidak ada items yang valid
        if (Object.keys(groupedItems).length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Tidak ada item yang valid untuk dibuat PO'
            });
            return;
        }

        // Tampilkan konfirmasi
        Swal.fire({
            icon: 'question',
            title: 'Konfirmasi',
            text: `Akan dibuat ${Object.keys(groupedItems).length} PO berdasarkan supplier. Lanjutkan?`,
            showCancelButton: true,
            confirmButtonText: 'Ya, Buat PO',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Buat PO untuk setiap supplier
                let successCount = 0;
                let totalPO = Object.keys(groupedItems).length;
                
                // Tampilkan loading
                Swal.fire({
                    title: 'Membuat PO...',
                    html: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Buat promises untuk semua createPO
                const promises = Object.values(groupedItems).map(supplierGroup => 
                    createPO(supplierGroup)
                );

                // Jalankan semua promises
                Promise.all(promises)
                    .then(results => {
                        successCount = results.filter(r => r.success).length;
                        
                        if (successCount === totalPO) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: `${successCount} PO berhasil dibuat`
                            }).then(() => {
                                // Tutup modal create PO
                                $('#createPoModal').modal('hide');
                                // Refresh daftar PO
                                loadPoList(currentTaskId);
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Perhatian',
                                text: `${successCount} dari ${totalPO} PO berhasil dibuat`
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error creating POs:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal membuat PO. Silakan coba lagi nanti.'
                        });
                    });
            }
        });
    });

    // Fungsi untuk membuat PO
    function createPO(data) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/maintenance/po/store',
                method: 'POST',
                data: {
                    ...data,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    resolve(response);
                },
                error: function(xhr) {
                    console.error('Error creating PO:', xhr);
                    resolve({ success: false, message: xhr.responseText });
                }
            });
        });
    }

    // Fungsi untuk memuat detail PO
    function loadPoDetail(poId) {
        currentPoId = poId;
        console.log('Loading PO Detail for ID:', poId);
        
        $.ajax({
            url: `/maintenance/po/${poId}/detail`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const po = response.data;
                    currentPo = po;
                    console.log('Loaded PO data:', po);
                    
                    // Update informasi PO
                    $('#poDetailModal .po-number').text(po.po_number);
                    $('#poDetailModal .created-date').text(formatDate(po.created_at));
                    $('#poDetailModal .po-status').html(`<span class="badge ${getStatusClass(po.status)}">${po.status}</span>`);
                    $('#poDetailModal .created-by').text(po.creator_name);
                    
                    // Update informasi supplier
                    $('#poDetailModal .supplier-name').text(po.supplier_name);
                    $('#poDetailModal .supplier-phone').text(po.supplier_phone || '-');
                    $('#poDetailModal .supplier-email').text(po.supplier_email || '-');
                    $('#poDetailModal .supplier-address').text(po.supplier_address || '-');
                    
                    // Update payment terms
                    let paymentTermsText = '';
                    if (po.supplier_payment_term && po.supplier_payment_days) {
                        paymentTermsText = `${po.supplier_payment_term} - ${po.supplier_payment_days} days`;
                    } else if (po.supplier_payment_term) {
                        paymentTermsText = po.supplier_payment_term;
                    } else if (po.supplier_payment_days) {
                        paymentTermsText = `${po.supplier_payment_days} days`;
                    } else {
                        paymentTermsText = '-';
                    }
                    $('#poDetailModal .supplier-payment-terms').text(paymentTermsText);
                    
                    // Update items
                    let itemsHtml = '';
                    po.items.forEach(item => {
                        itemsHtml += `
                            <tr>
                                <td>${item.item_name}</td>
                                <td>${item.description || '-'}</td>
                                <td>${item.specifications || '-'}</td>
                                <td class="text-end">${formatNumber(item.quantity)}</td>
                                <td>${item.unit_name}</td>
                                <td class="text-end">${formatCurrency(item.supplier_price)}</td>
                                <td class="text-end">${formatCurrency(item.subtotal)}</td>
                            </tr>
                        `;
                    });
                    $('#poItemsTableBody').html(itemsHtml);
                    $('#poDetailModal .total-amount').text(formatCurrency(po.total_amount));
                    
                    // Update approval info
                    $('#poDetailModal .approved-by').text(po.approver_name || '-');
                    $('#poDetailModal .approved-date').text(po.approved_at ? formatDate(po.approved_at) : '-');
                    $('#poDetailModal .approval-notes').text(po.approval_notes || '-');
                    
                    // Update approval status
                    updateApprovalStatus(po);
                    
                    // Setup data untuk modal approval baru
                    setupApprovalModalData(po);
                    
                    // Handling visibility tombol berdasarkan status
                    const staticApprovalButtons = $('#staticApprovalButtons');
                    const createGoodReceiveBtn = $('#createGoodReceiveBtn, .good-receive-section #createGoodReceiveBtn');
                    
                    // Default sembunyikan semua tombol action
                    staticApprovalButtons.addClass('d-none');
                    createGoodReceiveBtn.addClass('d-none');

                    // Tampilkan tombol berdasarkan status
                    if (po.status === 'RECEIVED') {
                        // Jika RECEIVED, semua tombol tetap hidden
                        console.log('Status RECEIVED: hiding all buttons');
                    } else if (po.status === 'APPROVED') {
                        // Jika APPROVED, tampilkan tombol good receive
                        createGoodReceiveBtn.removeClass('d-none');
                        console.log('Status APPROVED: showing good receive button only');
                    } else if (po.status === 'DRAFT' || po.status === 'PENDING') {
                        // Jika DRAFT atau PENDING, tampilkan tombol approval
                        staticApprovalButtons.removeClass('d-none');
                        console.log('Status DRAFT/PENDING: showing approval buttons');
                    }

                    // Update invoice section
                    if (po.invoice_file_path) {
                        // Update invoice info
                        $('#poDetailModal .invoice-number').text(po.invoice_number || '-');
                        $('#poDetailModal .invoice-date').text(po.invoice_date ? formatDate(po.invoice_date) : '-');
                        
                        // Update invoice image dengan URL yang benar
                        const invoiceImageUrl = `/storage/app/public/${po.invoice_file_path}`;
                        $('#poDetailModal .invoice-image').attr('src', invoiceImageUrl);
                        
                        // Show invoice section
                        $('.invoice-section').removeClass('d-none');
                    } else {
                        // Hide invoice section if no invoice
                        $('.invoice-section').addClass('d-none');
                    }

                    // Update Good Receive section
                    updateGoodReceiveSection(po);

                    // Show modal
                    $('#poDetailModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to load PO detail'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error loading PO detail:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load PO detail. Please try again later.'
                });
            }
        });
    }

    // Helper function untuk format tanggal
    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    // Helper function untuk mendapatkan class status
    function getStatusClass(status) {
        switch(status) {
            case 'DRAFT':
                return 'bg-warning text-dark';
            case 'PENDING':
                return 'bg-info';
            case 'APPROVED':
                return 'bg-success';
            case 'REJECTED':
                return 'bg-danger';
            case 'RECEIVED':
                return 'bg-primary';
            case 'PAYMENT':
                return 'bg-dark';
            default:
                return 'bg-secondary';
        }
    }

    // Update tampilan approval status berdasarkan data PO
    function updateApprovalStatus(po) {
        console.log('Updating approval status for PO:', po);
        
        // GM Finance Approval
        if (po.gm_finance_approval) {
            if (po.gm_finance_approval === 'APPROVED') {
                $('#gmFinanceApproval').html('<span class="badge bg-success">Approved</span>');
                
                if (po.gm_finance_approval_notes) {
                    $('#gmFinanceDate').html(`
                        <small class="text-muted">${new Date(po.gm_finance_approval_date).toLocaleDateString('id-ID', {
                            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                        })}</small>
                        <div class="approval-notes mt-2">
                            <small class="fw-bold">Notes:</small>
                            <p class="small mb-0">${po.gm_finance_approval_notes}</p>
                        </div>
                    `);
                } else {
                    $('#gmFinanceDate').text(new Date(po.gm_finance_approval_date).toLocaleDateString('id-ID', {
                        day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                    }));
                }
            } else if (po.gm_finance_approval === 'REJECTED') {
                $('#gmFinanceApproval').html('<span class="badge bg-danger">Rejected</span>');
            } else {
                $('#gmFinanceApproval').html('<span class="badge bg-secondary">Pending</span>');
            }
        }
        
        // Managing Director Approval
        if (po.managing_director_approval) {
            if (po.managing_director_approval === 'APPROVED') {
                $('#managingDirectorApproval').html('<span class="badge bg-success">Approved</span>');
                
                if (po.managing_director_approval_notes) {
                    $('#managingDirectorDate').html(`
                        <small class="text-muted">${new Date(po.managing_director_approval_date).toLocaleDateString('id-ID', {
                            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                        })}</small>
                        <div class="approval-notes mt-2">
                            <small class="fw-bold">Notes:</small>
                            <p class="small mb-0">${po.managing_director_approval_notes}</p>
                        </div>
                    `);
                } else {
                    $('#managingDirectorDate').text(new Date(po.managing_director_approval_date).toLocaleDateString('id-ID', {
                        day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                    }));
                }
            } else if (po.managing_director_approval === 'REJECTED') {
                $('#managingDirectorApproval').html('<span class="badge bg-danger">Rejected</span>');
            } else {
                $('#managingDirectorApproval').html('<span class="badge bg-secondary">Pending</span>');
            }
        }
        
        // President Director Approval
        if (po.president_director_approval) {
            if (po.president_director_approval === 'APPROVED') {
                $('#presidentDirectorApproval').html('<span class="badge bg-success">Approved</span>');
                
                if (po.president_director_approval_notes) {
                    $('#presidentDirectorDate').html(`
                        <small class="text-muted">${new Date(po.president_director_approval_date).toLocaleDateString('id-ID', {
                            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                        })}</small>
                        <div class="approval-notes mt-2">
                            <small class="fw-bold">Notes:</small>
                            <p class="small mb-0">${po.president_director_approval_notes}</p>
                        </div>
                    `);
                } else {
                    $('#presidentDirectorDate').text(new Date(po.president_director_approval_date).toLocaleDateString('id-ID', {
                        day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                    }));
                }
            } else if (po.president_director_approval === 'REJECTED') {
                $('#presidentDirectorApproval').html('<span class="badge bg-danger">Rejected</span>');
            } else {
                $('#presidentDirectorApproval').html('<span class="badge bg-secondary">Pending</span>');
            }
        }
    }

    // Tambahkan fungsi untuk update section good receive
    function updateGoodReceiveSection(po) {
        const goodReceiveData = $('#goodReceiveData');
        const noGoodReceiveData = $('#noGoodReceiveData');
        const createGoodReceiveBtn = $('#createGoodReceiveBtn, .good-receive-section #createGoodReceiveBtn');

        // Check if good receive data exists
        if (po.receive_date) {
            // Show good receive data
            goodReceiveData.removeClass('d-none');
            noGoodReceiveData.addClass('d-none');
            createGoodReceiveBtn.addClass('d-none');

            // Update receive date
            goodReceiveData.find('.receive-date').text(
                new Date(po.receive_date).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                })
            );

            // Update notes
            goodReceiveData.find('.receive-notes').text(
                po.receive_notes || '-'
            );

            // Update photos
            const photosContainer = goodReceiveData.find('.receive-photos');
            photosContainer.empty();

            if (po.receive_photos) {
                const photos = JSON.parse(po.receive_photos);
                photos.forEach(photoPath => {
                    const photoDiv = $(`
                        <div class="photo-item" style="width: 150px; height: 150px;">
                            <a href="/${photoPath}" target="_blank" class="d-block h-100">
                                <img src="/${photoPath}" 
                                     class="img-fluid h-100 w-100" 
                                     style="object-fit: cover; border-radius: 4px;"
                                     alt="Good Receive Photo">
                            </a>
                        </div>
                    `);
                    photosContainer.append(photoDiv);
                });
            }
        } else {
            // Hide good receive data and show no data message
            goodReceiveData.addClass('d-none');
            noGoodReceiveData.removeClass('d-none');
            createGoodReceiveBtn.removeClass('d-none');
        }

        // Hide create button if PO is not approved
        if (po.status !== 'APPROVED') {
            createGoodReceiveBtn.addClass('d-none');
        }
    }

    // Setup data for approval modal
    function setupApprovalModalData(po) {
        // Set approval modal data based on PO
        $('#po-approvalModalPoNumber').text(`PO Number: ${po.po_number}`);
        
        // Tentukan level approval
        let approvalLevel = "GM Finance";
        if (po.gm_finance_approval === 'APPROVED') {
            approvalLevel = "Managing Director";
            if (po.managing_director_approval === 'APPROVED') {
                approvalLevel = "President Director";
            }
        }
        
        $('#po-approvalModalLevel').text(`Approval Level: ${approvalLevel}`);
    }

    // Initialize event handlers for approval modal
    function initApprovalModal() {
        // When approve button is clicked, show approval form
        $(document).on('click', '#po-newApproveBtn', function() {
            $('#po-newApproveBtn, #po-newRejectBtn').hide();
            $('#po-newApprovalForm').show();
            $('#po-newRejectionForm').hide();
        });
        
        // When reject button is clicked, show rejection form
        $(document).on('click', '#po-newRejectBtn', function() {
            $('#po-newApproveBtn, #po-newRejectBtn').hide();
            $('#po-newRejectionForm').show();
            $('#po-newApprovalForm').hide();
        });
        
        // When cancel button is clicked on either form
        $(document).on('click', '.po-cancel-form-btn', function() {
            $('#po-newApprovalForm, #po-newRejectionForm').hide();
            $('#po-newApproveBtn, #po-newRejectBtn').show();
        });
        
        // Handle approval form submission
        $(document).on('submit', '#po-newApprovalNotesForm', function(e) {
            e.preventDefault();
            const notes = $('#po-newApprovalNotes').val().trim();
            
            // Tentukan level approval
            let approvalLevel = "GM Finance";
            if (currentPo.gm_finance_approval === 'APPROVED') {
                approvalLevel = "Managing Director";
                if (currentPo.managing_director_approval === 'APPROVED') {
                    approvalLevel = "President Director";
                }
            }
            
            approvePo(currentPoId, notes, approvalLevel);
        });
        
        // Handle rejection form submission
        $(document).on('submit', '#po-newRejectionNotesForm', function(e) {
            e.preventDefault();
            const notes = $('#po-newRejectionNotes').val().trim();
            
            if (!notes) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please provide a reason for rejection'
                });
                return;
            }
            
            rejectPo(currentPoId, notes);
        });
        
        // Reset forms when modal is closed
        $('#poApprovalModal').on('hidden.bs.modal', function() {
            $('#po-newApprovalForm, #po-newRejectionForm').hide();
            $('#po-newApproveBtn, #po-newRejectBtn').show();
            $('#po-newApprovalNotes, #po-newRejectionNotes').val('');
        });
    }

    // Fungsi untuk approve PO
    function approvePo(poId, notes, level) {
        console.log('Executing approvePo with:', { poId, notes, level });
        
        if (!poId) {
            console.error('No PO ID provided for approval');
            return;
        }

        // Tambahkan flag untuk mencegah multiple submission
        if (window.isApprovingPo) {
            console.log('PO approval already in progress, preventing multiple submissions');
            return;
        }
        
        // Set flag bahwa sedang dalam proses approval
        window.isApprovingPo = true;

        $.ajax({
            url: `/maintenance/po/${poId}/approve`, // Pastikan URL benar untuk PO
            method: 'POST',
            data: {
                notes: notes,
                approval_level: level
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                console.log('Sending PO approval request:', {
                    url: `/maintenance/po/${poId}/approve`,
                    data: { notes, approval_level: level }
                });
                
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                console.log('PO approval response:', response);
                
                // Reset flag
                window.isApprovingPo = false;
                
                if (response.success) {
                    // Tutup modal approval jika terbuka
                    $('#poApprovalModal').modal('hide');
                    $('#approvalNotesSection').addClass('d-none');
                    $('#approvalNotes').val('');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'PO has been approved successfully'
                    }).then(() => {
                        loadPoDetail(poId);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to approve PO'
                    });
                    $('#approvalNotesSection').removeClass('d-none');
                }
            },
            error: function(xhr) {
                console.error('Error in PO approval:', xhr);
                
                // Reset flag
                window.isApprovingPo = false;
                
                let errorMessage = 'Failed to approve PO. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
                $('#approvalNotesSection').removeClass('d-none');
            }
        });
    }

    // Fungsi untuk reject PO
    function rejectPo(poId, notes) {
        // Tambahkan flag untuk mencegah multiple submission
        if (window.isRejectingPo) {
            console.log('PO rejection already in progress, preventing multiple submissions');
            return;
        }
        
        // Set flag bahwa sedang dalam proses rejection
        window.isRejectingPo = true;

        $.ajax({
            url: `/maintenance/po/${poId}/reject`,
            method: 'POST',
            data: { notes: notes },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                // Reset flag
                window.isRejectingPo = false;

                if (response.success) {
                    // Tutup modal approval jika terbuka
                    $('#poApprovalModal').modal('hide');
                    
                    // Sembunyikan form rejection
                    $('#rejectionNotesSection').addClass('d-none');
                    $('#rejectionNotes').val('');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'PO has been rejected'
                    }).then(() => {
                        // Reload PO detail
                        loadPoDetail(poId);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to reject PO'
                    });
                    
                    // Tampilkan lagi form rejection
                    $('#rejectionNotesSection').removeClass('d-none');
                }
            },
            error: function(xhr) {
                console.error('Error rejecting PO:', xhr);
                
                // Reset flag
                window.isRejectingPo = false;
                
                let errorMessage = 'Failed to reject PO. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
                
                // Tampilkan lagi form rejection
                $('#rejectionNotesSection').removeClass('d-none');
            }
        });
    }

    // Handle form submission
    $('#saveInvoiceBtn').on('click', function() {
        const poId = $('#uploadInvoiceModal').data('po-id');
        const formData = new FormData();
        
        // Get file input
        const fileInput = $('input[name="invoice_file"]')[0];
        const file = fileInput.files[0];
        
        // Validate file
        if (!file) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select an invoice file'
            });
            return;
        }

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Only image files are allowed (JPG, PNG, GIF, WEBP)'
            });
            return;
        }

        // Add data to FormData
        formData.append('invoice_file', file);
        formData.append('invoice_number', $('input[name="invoice_number"]').val());
        formData.append('invoice_date', $('input[name="invoice_date"]').val());
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Send request
        $.ajax({
            url: `/maintenance/po/${poId}/upload-invoice`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                Swal.fire({
                    title: 'Uploading...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Invoice has been uploaded successfully'
                    }).then(() => {
                        $('#uploadInvoiceModal').modal('hide');
                        // Refresh PO list if needed
                        loadPoList(currentTaskId);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to upload invoice'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error uploading invoice:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to upload invoice. Please try again later.'
                });
            }
        });
    });

    // Update file input validation
    $('input[name="invoice_file"]').on('change', function() {
        const file = this.files[0];
        if (file) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Only image files are allowed (JPG, PNG, GIF, WEBP)'
                });
                this.value = ''; // Clear the file input
            }
        }
    });

    // Function untuk start camera
    async function startCamera() {
        try {
            console.log('Starting camera with facing mode:', currentFacingMode);
            
            // Stop existing stream if any
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }

            // Get available devices first
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(device => device.kind === 'videoinput');
            console.log('Available video devices:', videoDevices);

            const constraints = {
                video: {
                    facingMode: currentFacingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };

            console.log('Requesting media with constraints:', constraints);
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            
            const videoElement = document.getElementById('cameraStream');
            videoElement.srcObject = stream;
            
            // Log success
            console.log('Camera started successfully');
            
            return true;
        } catch (err) {
            console.error('Error starting camera:', err);
            Swal.fire({
                icon: 'error',
                title: 'Camera Error',
                text: 'Unable to access camera. Please make sure you have granted camera permissions.'
            });
            return false;
        }
    }

    // Function untuk switch camera
    async function switchCamera() {
        console.log('Switching camera from', currentFacingMode);
        currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
        console.log('to', currentFacingMode);
        
        try {
            await startCamera();
        } catch (err) {
            console.error('Error switching camera:', err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to switch camera. Please try again.'
            });
        }
    }

    // Fungsi capture yang diperbaiki
    function capturePhoto() {
        // Prevent multiple captures
        $('#captureBtn').prop('disabled', true);
        
        try {
            console.log('Capturing photo');
            const video = document.getElementById('cameraStream');
            const canvas = document.createElement('canvas');
            
            if (video.videoWidth === 0 || video.videoHeight === 0) {
                console.error('Video element has no dimensions');
                $('#captureBtn').prop('disabled', false);
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            canvas.toBlob((blob) => {
                const photoId = Date.now();
                
                capturedPhotos.push({
                    id: photoId,
                    blob: blob
                });
                
                addPhotoPreview(photoId, URL.createObjectURL(blob));
                $('#captureBtn').prop('disabled', false);
            }, 'image/jpeg', 0.8);
            
        } catch (err) {
            console.error('Error capturing photo:', err);
            $('#captureBtn').prop('disabled', false);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to capture photo. Please try again.'
            });
        }
    }

    // Function untuk add photo preview
    function addPhotoPreview(photoId, photoUrl) {
        console.log('Adding photo preview for ID:', photoId);
        
        const previewContainer = document.getElementById('photoPreviewContainer');
        
        // Buat element preview baru dengan class khusus untuk modal good receive
        const previewDiv = document.createElement('div');
        previewDiv.className = 'good-receive-photo-preview position-relative d-inline-block me-2 mb-2';
        previewDiv.setAttribute('data-photo-id', photoId);
        previewDiv.style.cssText = `
            width: 200px !important;
            height: 200px !important;
            border: 2px solid #dee2e6 !important;
            border-radius: 8px !important;
            background-color: #f8f9fa !important;
            overflow: hidden !important;
            margin: 8px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        `;
        
        // Buat element image wrapper dengan style khusus
        const imgWrapper = document.createElement('div');
        imgWrapper.className = 'good-receive-img-wrapper';
        imgWrapper.style.cssText = `
            width: 100% !important;
            height: 100% !important;
            padding: 8px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background-color: #fff !important;
        `;
        
        // Buat element image dengan style khusus
        const img = document.createElement('img');
        img.src = photoUrl;
        img.className = 'good-receive-preview-image';
        img.style.cssText = `
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important;
            border-radius: 4px !important;
        `;
        
        // Buat tombol delete dengan style khusus
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'btn btn-danger btn-sm position-absolute good-receive-delete-btn';
        deleteBtn.style.cssText = `
            top: 8px !important;
            right: 8px !important;
            z-index: 10 !important;
            opacity: 0.9 !important;
            padding: 6px 10px !important;
            border-radius: 50% !important;
            width: 30px !important;
            height: 30px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
        `;
        deleteBtn.innerHTML = '<i class="ri-delete-bin-line"></i>';
        
        // Event listener untuk tombol delete
        deleteBtn.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            removePhoto(photoId);
        };
        
        // Gabungkan elements
        imgWrapper.appendChild(img);
        previewDiv.appendChild(imgWrapper);
        previewDiv.appendChild(deleteBtn);
        
        // Tambahkan ke container
        previewContainer.appendChild(previewDiv);
        
        updateNoPhotosMessage();
    }

    // Function untuk remove photo
    function removePhoto(photoId) {
        console.log('Removing photo with ID:', photoId);
        
        // Hapus dari array capturedPhotos
        capturedPhotos = capturedPhotos.filter(photo => photo.id !== photoId);
        
        // Hapus preview dari DOM
        const previewElement = document.querySelector(`.good-receive-photo-preview[data-photo-id="${photoId}"]`);
        if (previewElement) {
            previewElement.remove();
        }
        
        updateNoPhotosMessage();
    }

    // Function untuk update no photos message
    function updateNoPhotosMessage() {
        const container = document.getElementById('photoPreviewContainer');
        const noPhotosMessage = document.getElementById('noPhotosMessage');
        
        if (capturedPhotos.length === 0) {
            if (!noPhotosMessage) {
                const message = document.createElement('div');
                message.id = 'noPhotosMessage';
                message.className = 'text-muted';
                message.textContent = 'Belum ada foto';
                container.appendChild(message);
            }
        } else {
            if (noPhotosMessage) {
                noPhotosMessage.remove();
            }
        }
    }

    // Initialize when document is ready
    $(document).ready(function() {
        initPo();
        
        // Initialize Approval Modal
        initApprovalModal();
        
        // Event handler untuk tombol Cancel Approval
        $(document).off('click', '#cancelApprovalBtn').on('click', '#cancelApprovalBtn', function() {
            $('#approvalNotesSection').addClass('d-none');
            $('#staticApprovalButtons').removeClass('d-none');
            $('#approvalNotes').val('');
        });
        
        // Event handler untuk tombol Cancel Rejection
        $(document).off('click', '#cancelRejectionBtn').on('click', '#cancelRejectionBtn', function() {
            $('#rejectionNotesSection').addClass('d-none');
            $('#staticApprovalButtons').removeClass('d-none');
            $('#rejectionNotes').val('');
        });

        // Event handler untuk static approve button
        $(document).off('click', '#staticApproveBtn').on('click', '#staticApproveBtn', function() {
            console.log('Opening PO approval modal');
            // Buka modal approval baru untuk PO
            $('#poApprovalModal').modal('show');
        });
        
        // Event handler untuk static reject button
        $(document).off('click', '#staticRejectBtn').on('click', '#staticRejectBtn', function() {
            $('#rejectionNotesSection').removeClass('d-none');
            $('#staticApprovalButtons').addClass('d-none');
        });

        // Spesifik untuk konfirmasi approval PO
        $(document).off('click', '#confirmApprovalBtn').on('click', '#confirmApprovalBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Pastikan ini hanya berjalan untuk PO approval
            if ($(this).data('type') === 'po') {
                console.log('PO Approval confirmation clicked');
                const notes = $('#approvalNotes').val();
                let level = "GM Finance";
                
                if (currentPo) {
                    if (currentPo.gm_finance_approval === 'APPROVED') {
                        level = "Managing Director";
                        if (currentPo.managing_director_approval === 'APPROVED') {
                            level = "President Director";
                        }
                    }
                }
                
                approvePo(currentPoId, notes, level);
            }
        });

        // Update event handler untuk tombol save
        $('#saveGoodReceiveBtn').off('click').on('click', async function(e) {
            e.preventDefault();
            console.log('Save button clicked');

            // Validasi foto
            if (capturedPhotos.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Mohon capture minimal 1 foto'
                });
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Menyimpan data...',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('receive_date', $('input[name="receive_date"]').val());
            formData.append('receive_notes', $('textarea[name="receive_notes"]').val());
            formData.append('po_id', currentPoId);

            // Log form data untuk debugging
            console.log('Form data values:', {
                receive_date: $('input[name="receive_date"]').val(),
                receive_notes: $('textarea[name="receive_notes"]').val(),
                photos_count: capturedPhotos.length
            });

            // Append setiap foto ke formData
            capturedPhotos.forEach((photo, index) => {
                formData.append(`photos[]`, photo.blob, `photo_${index}.jpg`);
            });

            try {
                const response = await $.ajax({
                    url: `/maintenance/po/${currentPoId}/good-receive`,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Close loading
                Swal.close();

                if (response.success) {
                    // Show success message
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data good receive berhasil disimpan',
                        timer: 1500
                    });

                    // Reset form dan tutup modal
                    $('#goodReceiveModal').modal('hide');
                    capturedPhotos = [];
                    $('#photoPreviewContainer').empty();
                    
                    // Reload PO detail
                    loadPoDetail(currentPoId);
                } else {
                    throw new Error(response.message || 'Unknown error occurred');
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                
                // Close loading dan tampilkan error
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal menyimpan data: ' + (error.responseJSON?.message || error.message || 'Unknown error')
                });
            }
        });

        // Event handler untuk tombol capture
        $('#captureBtn').off('click');
        
        // Pasang event handler baru
        $('#captureBtn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            capturePhoto();
        });

        // Event handler untuk switch camera
        $(document).on('click', '#switchCameraBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            switchCamera();
        });

        // Clean up when modal is hidden
        $('#goodReceiveModal').on('hidden.bs.modal', function() {
            console.log('Modal hidden, stopping camera');
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });
    });
})();
