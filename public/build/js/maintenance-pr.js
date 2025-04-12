/**
 * Maintenance Purchase Requisition JS
 */
(function () {
    'use strict';

    // Global variables
    let currentTaskId = null;
    let rowCounter = 0;
    let currentPrId = null;
    let currentPr = null;
    let currentUserJabatan = null;
    let currentUserRole = null;
    let isEventHandlerRegistered = false;
    let currentApprovalNotes = '';
    let currentApprovalLevel = '';
    
    // Initialize PR functionality
    function initPr() {
        // Event delegation for PR button
        $(document).on('click', '.create-pr-btn', function() {
            const taskId = $(this).data('task-id');
            currentTaskId = taskId;
            
            // Load PR list for this task
            loadPrList(taskId);
        });
        
        // Event for adding new PR
        $(document).on('click', '.add-new-pr-btn', function() {
            showCreatePrModal();
        });
        
        // Event for form submission
        $('#prForm').on('submit', function(e) {
            e.preventDefault();
            
            // Mencegah multiple submission
            if ($(this).data('submitting')) {
                return false;
            }
            
            // Set flag submitting di form
            $(this).data('submitting', true);
            
            savePr();
        });
        
        // Event for adding new row
        $('.add-row-btn').off();
        $('.add-row-btn').on('click', function(e) {
            e.preventDefault();
            addNewRow();
        });
        
        // Event for deleting row
        $(document).on('click', '.delete-row-btn', function() {
            const row = $(this).closest('.pr-item-row');
            
            // Don't delete if it's the last row
            if ($('.pr-item-row').length > 1) {
                row.remove();
            } else {
                // Reset values if it's the last row
                row.find('.item-name').val('');
                row.find('.item-qty').val(1);
                row.find('.item-unit').val('');
                row.find('.item-price').val(0);
                row.find('.item-subtotal').val(0);
            }
            
            calculateTotal();
        });
        
        // Event for calculating subtotal
        $(document).on('input', '.item-qty, .item-price', function() {
            calculateRowSubtotal($(this).closest('.pr-item-row'));
            calculateTotal();
        });
        
        // Event for viewing PR detail
        $(document).on('click', '.view-pr-btn', function() {
            const prId = $(this).data('pr-id');
            currentPrId = prId;
            
            // Pastikan user info sudah diload sebelum menampilkan PR detail
            loadUserInfo().then(() => {
                loadPrDetail(prId);
            }).catch(error => {
                console.error('Error loading user info before PR detail:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load user information. Please try again.'
                });
            });
        });
        
        // Event untuk approve PR
        $(document).on('click', '#prStaticApproveBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('PR Approve button clicked');
            
            // Gunakan selector yang lebih spesifik untuk modal PR
            $('#prApprovalModal #approvalNotesSection').removeClass('d-none').show();
            $('#prApprovalModal #staticApprovalButtons').addClass('d-none').hide();
        });
        
        // Event untuk reject PR
        $(document).on('click', '#prStaticRejectBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('PR Reject button clicked');
            
            // Gunakan selector yang lebih spesifik untuk modal PR
            $('#prApprovalModal #rejectionNotesSection').removeClass('d-none').show();
            $('#prApprovalModal #staticApprovalButtons').addClass('d-none').hide();
        });
        
        // Event untuk cancel approval
        $(document).on('click', '#cancelApprovalBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Cancel approval clicked');
            
            // Gunakan selector yang lebih spesifik untuk modal PR
            $('#prApprovalModal #approvalNotesSection').addClass('d-none').hide();
            $('#prApprovalModal #staticApprovalButtons').removeClass('d-none').show();
        });
        
        // Event untuk cancel rejection
        $(document).on('click', '#cancelRejectionBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Cancel rejection clicked');
            
            // Gunakan selector yang lebih spesifik untuk modal PR
            $('#prApprovalModal #rejectionNotesSection').addClass('d-none').hide();
            $('#prApprovalModal #staticApprovalButtons').removeClass('d-none').show();
        });
        
        // Event untuk confirm approval
        $(document).on('click', '#confirmApprovalBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Mencegah multiple klik
            if ($(this).data('processing')) {
                console.log('Button sudah di-klik, mencegah multiple submission');
                return false;
            }
            
            // Set flag processing
            $(this).data('processing', true);
            
            // Ambil notes dari textarea di modal PR
            const notes = $('#prApprovalModal #approvalNotes').val().trim();
            console.log('Notes value from PR modal:', notes);
            
            // Tentukan level berdasarkan status PR saat ini
            let level = "Chief Engineering"; // Default
            if (currentPr && currentPr.chief_engineering_approval === 'APPROVED') {
                level = "Purchasing Manager";
                if (currentPr.purchasing_manager_approval === 'APPROVED') {
                    level = "COO";
                }
            }
            
            console.log('Approval level determined:', level);
            
            // Panggil fungsi approvePr dengan notes yang sudah diambil
            approvePr(currentPrId, notes, level);
            
            // Reset flag processing setelah 3 detik untuk mencegah double klik
            setTimeout(() => {
                $(this).data('processing', false);
            }, 3000);
        });
        
        // Event untuk confirm rejection
        $(document).on('click', '#confirmRejectionBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const notes = $('#prApprovalModal #rejectionNotes').val().trim();
            
            if (!notes) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please provide a reason for rejection'
                });
                return;
            }
            
            console.log('Confirm rejection clicked, notes:', notes);
            
            // Kirim request dengan data yang benar
            rejectPr(currentPrId, notes);
        });
        
        // Reset modal saat ditutup
        $('#prApprovalModal').on('hidden.bs.modal', function() {
            console.log('PR Approval Modal hidden, resetting elements');
            
            // Reset semua section ke status awal
            $('#prApprovalModal #approvalNotesSection').addClass('d-none').hide();
            $('#prApprovalModal #rejectionNotesSection').addClass('d-none').hide();
            $('#prApprovalModal #staticApprovalButtons').removeClass('d-none').show();
            
            // Reset form values
            $('#prApprovalModal #approvalNotes').val('');
            $('#prApprovalModal #rejectionNotes').val('');
        });
        
        // Load units for dropdown
        loadUnits();
    }
    
    // Load PR list for a task
    function loadPrList(taskId) {
        $.ajax({
            url: `/maintenance/task/${taskId}/pr/list`,
            method: 'GET',
            beforeSend: function() {
                $('#prListTableBody').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
                $('#noPrMessage').addClass('d-none');
                $('#prListGrandTotal').text('-');
            },
            success: function(response) {
                if (response.success) {
                    const prs = response.data;
                    const task = response.task;
                    
                    // Update task number display
                    $('.task-number-display').text(`Task: ${task.task_number}`);
                    
                    // Populate table
                    if (prs.length > 0) {
                        let html = '';
                        let grandTotal = 0;
                        
                        prs.forEach(function(pr) {
                            // Akumulasi grand total
                            grandTotal += parseFloat(pr.total_amount);
                            
                            // Format date
                            const createdAt = new Date(pr.created_at).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });
                            
                            // Format currency
                            const totalAmount = parseFloat(pr.total_amount).toLocaleString('id-ID', {
                                style: 'currency',
                                currency: 'IDR'
                            });
                            
                            // Determine status badge color
                            let statusClass = 'bg-secondary';
                            switch(pr.status) {
                                case 'DRAFT':
                                    statusClass = 'bg-warning text-dark';
                                    break;
                                case 'SUBMITTED':
                                    statusClass = 'bg-info';
                                    break;
                                case 'APPROVED':
                                    statusClass = 'bg-success';
                                    break;
                                case 'REJECTED':
                                    statusClass = 'bg-danger';
                                    break;
                            }
                            
                            html += `
                                <tr>
                                    <td>${pr.pr_number}</td>
                                    <td>${createdAt}</td>
                                    <td><span class="badge ${statusClass}">${pr.status}</span></td>
                                    <td>${totalAmount}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-info view-pr-btn" data-pr-id="${pr.id}" title="Lihat Detail">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <a href="/maintenance/pr/${pr.id}/preview" class="btn btn-sm btn-info preview-pr-btn" target="_blank" title="Lihat & Download PR">
                                                <i class="ri-file-text-line"></i>
                                            </a>
                                            <a href="/maintenance/ba/${pr.id}/preview" class="btn btn-sm btn-info preview-ba-btn" target="_blank" title="Lihat & Download BA">
                                                <i class="ri-file-list-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        // Display grand total
                        const formattedGrandTotal = grandTotal.toLocaleString('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        });
                        $('#prListGrandTotal').text(formattedGrandTotal);
                        
                        $('#prListTableBody').html(html);
                        $('#noPrMessage').addClass('d-none');
                    } else {
                        $('#prListTableBody').html('');
                        $('#prListGrandTotal').text('Rp0');
                        $('#noPrMessage').removeClass('d-none');
                    }
                    
                    // Show modal
                    $('#prListModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to load PRs'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error loading PRs:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load PRs. Please try again later.'
                });
            }
        });
    }
    
    // Show create PR modal
    function showCreatePrModal() {
        // Hide PR list modal
        $('#prListModal').modal('hide');
        
        // Reset form
        $('#prForm')[0].reset();
        $('#prItemsTableBody').html('');
        
        // Load task details
        $.ajax({
            url: `/maintenance/tasks/${currentTaskId}`,
            method: 'GET',
            success: function(response) {
                // Log respons mentah untuk debugging
                console.log('Raw task response:', response);
                
                // Cek format respons dan ekstrak data task
                let task;
                if (typeof response === 'object' && response !== null) {
                    // Jika respons adalah objek, periksa apakah ada properti task atau data
                    if (response.task) {
                        task = response.task;
                    } else if (response.data) {
                        task = response.data;
                    } else {
                        // Jika tidak ada properti task atau data, gunakan respons langsung
                        task = response;
                    }
                } else {
                    // Jika respons bukan objek, gunakan sebagai fallback
                    task = { title: 'Error', task_number: 'Unknown', id: currentTaskId };
                    console.error('Unexpected response format:', response);
                }
                
                console.log('Processed task data:', task);
                
                // Set task info dengan fallback untuk mencegah undefined
                $('#prTaskNumber').val(task.task_number || 'N/A');
                $('#prTaskId').val(task.id || currentTaskId);
                
                // Generate PR number
                const date = new Date();
                const year = date.getFullYear().toString().substr(-2);
                const month = ('0' + (date.getMonth() + 1)).slice(-2);
                const day = ('0' + date.getDate()).slice(-2);
                const randomNum = Math.floor(1000 + Math.random() * 9000);
                const prNumber = `PR${year}${month}${day}${randomNum}`;
                
                $('#prNumber').val(prNumber);
                
                // Show create PR modal
                setTimeout(() => {
                    $('#createPrModal').modal('show');
                }, 500);
            },
            error: function(xhr, status, error) {
                console.error('Error loading task:', error);
                console.log('Error response:', xhr.responseText);
                
                // Show PR list modal again
                $('#prListModal').modal('show');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat detail task. Silakan coba lagi.'
                });
            }
        });
    }
    
    // Add new row to PR items table
    function addNewRow() {
        // Cek apakah sedang ada proses penambahan row atau tidak
        if (window.isAddingRow) {
            return; // Jika sedang dalam proses penambahan, hentikan eksekusi
        }
        
        // Set flag bahwa sedang ada proses penambahan row
        window.isAddingRow = true;
        
        rowCounter++;
        
        const newRow = `
            <tr class="pr-item-row">
                <td>
                    <input type="text" class="form-control item-name" name="items[${rowCounter}][item_name]" required>
                </td>
                <td>
                    <textarea class="form-control item-description" name="items[${rowCounter}][description]" rows="2"></textarea>
                </td>
                <td>
                    <textarea class="form-control item-specifications" name="items[${rowCounter}][specifications]" rows="2"></textarea>
                </td>
                <td>
                    <input type="number" class="form-control item-qty" name="items[${rowCounter}][quantity]" step="0.01" min="0.01" value="1" required>
                </td>
                <td>
                    <select class="form-select item-unit" name="items[${rowCounter}][unit_id]" required>
                        <option value="">Select Unit</option>
                        <!-- Units will be copied from the first row -->
                        ${$('.item-unit').first().html()}
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control item-price" name="items[${rowCounter}][price]" step="0.01" min="0" value="0" required>
                </td>
                <td>
                    <input type="number" class="form-control item-subtotal" name="items[${rowCounter}][subtotal]" step="0.01" min="0" value="0" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger delete-row-btn">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#prItemsTableBody').append(newRow);
        
        // Reset flag setelah penambahan selesai
        setTimeout(function() {
            window.isAddingRow = false;
        }, 100);
    }
    
    // Calculate subtotal for a row
    function calculateRowSubtotal(row) {
        const qty = parseFloat(row.find('.item-qty').val()) || 0;
        const price = parseFloat(row.find('.item-price').val()) || 0;
        const subtotal = qty * price;
        
        row.find('.item-subtotal').val(subtotal.toFixed(2));
    }
    
    // Calculate total amount
    function calculateTotal() {
        let total = 0;
        
        $('.item-subtotal').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        
        $('#prTotalAmount').val(total.toFixed(2));
    }
    
    // Save PR
    function savePr() {
        // Nonaktifkan tombol submit untuk mencegah double-click
        $('#prForm button[type="submit"]').prop('disabled', true);
        
        // Get form data
        const formData = $('#prForm').serialize();
        
        // Validate at least one item
        if ($('.pr-item-row').length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please add at least one item'
            });
            $('#prForm').data('submitting', false);
            $('#prForm button[type="submit"]').prop('disabled', false);
            return;
        }
        
        // Validate all required fields
        if (!$('#prForm')[0].checkValidity()) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please fill all required fields'
            });
            $('#prForm').data('submitting', false);
            $('#prForm button[type="submit"]').prop('disabled', false);
            return;
        }
        
        // Validate total amount
        const totalAmount = parseFloat($('#prTotalAmount').val());
        if (totalAmount <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Total amount must be greater than zero'
            });
            $('#prForm').data('submitting', false);
            $('#prForm button[type="submit"]').prop('disabled', false);
            return;
        }
        
        // Save PR
        $.ajax({
            url: '/maintenance/pr/store',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                Swal.fire({
                    title: 'Saving...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.success) {
                    // Tutup modal dulu sebelum menampilkan SweetAlert
                    $('#createPrModal').modal('hide');
                    
                    // Kemudian tampilkan SweetAlert
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'PR has been created successfully'
                        }).then(() => {
                            // Reload PR list
                            loadPrList(currentTaskId);
                        });
                    }, 500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to create PR'
                    });
                }
                
                // Reset flag submitting
                $('#prForm').data('submitting', false);
                $('#prForm button[type="submit"]').prop('disabled', false);
            },
            error: function(xhr) {
                console.error('Error saving PR:', xhr);
                
                let errorMessage = 'Failed to create PR. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
                
                // Reset flag submitting
                $('#prForm').data('submitting', false);
                $('#prForm button[type="submit"]').prop('disabled', false);
            }
        });
    }
    
    // Load units for dropdown
    function loadUnits() {
        $.ajax({
            url: '/maintenance/units',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const units = response.data;
                    
                    // Build options
                    let options = '<option value="">Select Unit</option>';
                    
                    units.forEach(function(unit) {
                        options += `<option value="${unit.id}">${unit.name}</option>`;
                    });
                    
                    // Add to select
                    $('.item-unit').html(options);
                }
            },
            error: function(xhr) {
                console.error('Error loading units:', xhr);
            }
        });
    }
    
    // Load user info untuk mengetahui jabatan dan role user
    function loadUserInfo() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/maintenance/user-info',
                method: 'GET',
                success: function(response) {
                    console.log('User info API response:', response);
                    if (response.success) {
                        currentUserJabatan = response.data.id_jabatan;
                        currentUserRole = response.data.id_role;
                        console.log('User info loaded - Jabatan:', currentUserJabatan, 'Role:', currentUserRole);
                        console.log('Super Admin?', currentUserRole == '5af56935b011a');
                        console.log('Secretary?', currentUserJabatan == '217');
                        console.log('Chief Engineering?', currentUserJabatan == '165');
                        console.log('Purchasing Manager?', currentUserJabatan == '168');
                        console.log('COO?', currentUserJabatan == '151');
                        resolve(response.data);
                    } else {
                        console.error('Failed to load user info:', response);
                        reject('Failed to load user info');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading user info (XHR):', xhr);
                    reject(xhr);
                }
            });
        });
    }
    
    // Load PR detail
    function loadPrDetail(prId) {
        $.ajax({
            url: `/maintenance/pr/${prId}/detail`,
            method: 'GET',
            beforeSend: function() {
                $('#prDetailItemsTableBody').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');
                // Reset approval sections
                $('#chiefEngineeringApproval, #purchasingManagerApproval, #cooApproval').html('<span class="badge bg-secondary">Pending</span>');
                $('#chiefEngineeringDate, #purchasingManagerDate, #cooDate').text('');
                $('#openApprovalModalBtn').hide(); // Hide approval action button by default
            },
            success: function(response) {
                if (response.success) {
                    const pr = response.data;
                    
                    // Format date
                    const createdAt = new Date(pr.created_at).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    // Format currency
                    const totalAmount = parseFloat(pr.total_amount).toLocaleString('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    });
                    
                    // Determine status badge color
                    let statusClass = 'bg-secondary';
                    switch(pr.status) {
                        case 'DRAFT':
                            statusClass = 'bg-warning text-dark';
                            break;
                        case 'SUBMITTED':
                            statusClass = 'bg-info';
                            break;
                        case 'APPROVED':
                            statusClass = 'bg-success';
                            break;
                        case 'REJECTED':
                            statusClass = 'bg-danger';
                            break;
                    }
                    
                    // Update header info
                    $('#detailPrNumber').text(pr.pr_number);
                    $('#detailTaskNumber').text(pr.task_number);
                    $('#detailCreatedBy').text(pr.creator_name);
                    $('#detailStatus').html(`<span class="badge ${statusClass}">${pr.status}</span>`);
                    $('#detailCreatedAt').text(createdAt);
                    $('#detailTotalAmount').text(totalAmount);
                    $('#detailNotes').text(pr.notes || '-');
                    
                    // Tampilkan approval status
                    updateApprovalStatus(pr);
                    
                    // Check if the current user can approve this PR
                    checkUserApprovalRights(pr);
                    
                    // Setup approval data for when the action button is clicked
                    setupApprovalModalData(pr);
                    
                    // Populate items table
                    if (pr.items && pr.items.length > 0) {
                        let html = '';
                        let totalSubtotal = 0;
                        
                        pr.items.forEach(function(item, index) {
                            // Format currency
                            const price = parseFloat(item.price).toLocaleString('id-ID', {
                                style: 'currency',
                                currency: 'IDR'
                            });
                            
                            const subtotal = parseFloat(item.subtotal).toLocaleString('id-ID', {
                                style: 'currency',
                                currency: 'IDR'
                            });
                            
                            totalSubtotal += parseFloat(item.subtotal);
                            
                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.item_name}</td>
                                    <td>${item.description || '-'}</td>
                                    <td>${item.specifications || '-'}</td>
                                    <td>${item.quantity}</td>
                                    <td>${item.unit_name}</td>
                                    <td>${price}</td>
                                    <td>${subtotal}</td>
                                </tr>
                            `;
                        });
                        
                        $('#prDetailItemsTableBody').html(html);
                        
                        // Update footer total
                        const formattedTotal = totalSubtotal.toLocaleString('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        });
                        $('#detailFooterTotal').text(formattedTotal);
                    } else {
                        $('#prDetailItemsTableBody').html('<tr><td colspan="8" class="text-center">No items found</td></tr>');
                        $('#detailFooterTotal').text('Rp 0');
                    }
                    
                    // Hide PR list modal and show detail modal
                    $('#prListModal').modal('hide');
                    setTimeout(() => {
                        $('#prDetailModal').modal('show');
                    }, 500);
                    
                    // Add event for closing detail modal
                    $('#prDetailModal').on('hidden.bs.modal', function() {
                        // Show PR list modal again
                        $('#prListModal').modal('show');
                    });
                    
                    // Simpan data PR saat ini
                    currentPr = pr;
                    
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to load PR detail'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error loading PR detail:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load PR detail. Please try again later.'
                });
            }
        });
    }
    
    // Check if current user has the right to approve this PR
    function checkUserApprovalRights(pr) {
        // Tampilkan/sembunyikan tombol approval berdasarkan role dan status PR
                    const isSuperAdmin = currentUserRole == '5af56935b011a';
                    const isSecretary = currentUserJabatan == '217';
        
        console.log('DEBUG - Checking approval visibility conditions:');
        console.log('- PR Status:', pr.status);
        console.log('- Current user role:', currentUserRole);
        console.log('- Current user jabatan:', currentUserJabatan);
        console.log('- Is Super Admin?', isSuperAdmin);
        console.log('- Is Secretary?', isSecretary);
        console.log('- Chief Engineering approval:', pr.chief_engineering_approval);
        console.log('- Purchasing Manager approval:', pr.purchasing_manager_approval);
        console.log('- COO approval:', pr.coo_approval);
        
        // Default hide approval button
        $('#openApprovalModalBtn').hide();
                    
                    // Sembunyikan tombol jika PR sudah rejected atau approved
                    if (pr.status === 'REJECTED' || pr.status === 'APPROVED') {
                        console.log('PR status is REJECTED or APPROVED, hiding approval buttons');
            return;
        }
        
                        // Cek role dan status approval
                        let canApprove = false;
        let approvalLevel = '';
                        
                        // Super Admin dan Sekretaris selalu bisa approve
                        if (isSuperAdmin || isSecretary) {
                            canApprove = true;
            
            // Determine level for Super Admin/Secretary based on current PR status
            if (!pr.chief_engineering_approval || pr.chief_engineering_approval === 'PENDING') {
                approvalLevel = 'Chief Engineering';
            } else if (pr.chief_engineering_approval === 'APPROVED' && 
                     (!pr.purchasing_manager_approval || pr.purchasing_manager_approval === 'PENDING')) {
                approvalLevel = 'Purchasing Manager';
            } else if (pr.chief_engineering_approval === 'APPROVED' && 
                      pr.purchasing_manager_approval === 'APPROVED' && 
                     (!pr.coo_approval || pr.coo_approval === 'PENDING')) {
                approvalLevel = 'COO';
            }
            
            console.log('User is SuperAdmin or Secretary, showing approval buttons for level:', approvalLevel);
                        } 
                        // Chief Engineering bisa approve jika belum di-approve oleh Chief Engineering
                        else if (currentUserJabatan == '165' && 
                                (!pr.chief_engineering_approval || pr.chief_engineering_approval === 'PENDING')) {
                            canApprove = true;
            approvalLevel = 'Chief Engineering';
                            console.log('User is Chief Engineering and PR is pending approval, showing approval buttons');
                        } 
                        // Purchasing Manager bisa approve jika sudah di-approve oleh Chief Engineering
                        else if (currentUserJabatan == '168' && 
                                 pr.chief_engineering_approval === 'APPROVED' && 
                                 (!pr.purchasing_manager_approval || pr.purchasing_manager_approval === 'PENDING')) {
                            canApprove = true;
            approvalLevel = 'Purchasing Manager';
                            console.log('User is Purchasing Manager and PR is ready for PM approval, showing approval buttons');
                        } 
                        // COO bisa approve jika sudah di-approve oleh Purchasing Manager
                        else if (currentUserJabatan == '151' && 
                                 pr.chief_engineering_approval === 'APPROVED' && 
                                 pr.purchasing_manager_approval === 'APPROVED' && 
                                 (!pr.coo_approval || pr.coo_approval === 'PENDING')) {
                            canApprove = true;
            approvalLevel = 'COO';
                            console.log('User is COO and PR is ready for COO approval, showing approval buttons');
                        } else {
                            console.log('User does not have approval rights for this PR, hiding approval buttons');
                            canApprove = false;
                        }
        
        // Save current approval level for later use
        currentApprovalLevel = approvalLevel;
                        
                        // Tunjukkan atau sembunyikan tombol berdasarkan hasil pengecekan
        if (canApprove && approvalLevel) {
            $('#openApprovalModalBtn').show();
                    } else {
            $('#openApprovalModalBtn').hide();
        }
    }
    
    // Setup data for approval modal
    function setupApprovalModalData(pr) {
        // Set approval modal data based on PR
        $('#pr-approvalModalPrNumber').text(`PR Number: ${pr.pr_number}`);
        $('#pr-approvalModalLevel').text(`Approval Level: ${currentApprovalLevel}`);
    }
    
    // Fungsi untuk approve PR
    function approvePr(prId, notes, level) {
        console.log('Executing approvePr with:', { prId, notes, level });
        
        if (!prId) {
            console.error('No PR ID provided for approval');
            return;
        }
        
        // Tambahkan flag untuk mencegah multiple submission
        if (window.isApprovingPr) {
            console.log('Approval sudah dalam proses, mencegah multiple submission');
            return;
        }
        
        // Set flag bahwa sedang dalam proses approval
        window.isApprovingPr = true;
        
        // Siapkan data yang akan dikirim
        const requestData = {
            notes: notes,
            approval_level: level,
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        console.log('Sending approval request with data:', requestData);
        
        $.ajax({
            url: `/maintenance/pr/${prId}/approve`,
            method: 'POST',
            data: requestData,
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
                console.log('PR approval response:', response);
                
                // Reset flag proses approval
                window.isApprovingPr = false;
                
                if (response.success) {
                    // Tutup modal approval
                    $('#prApprovalModal').modal('hide');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'PR has been approved successfully'
                    }).then(() => {
                        // Tampilkan kembali modal detail dan reload datanya
                        loadPrDetail(prId);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to approve PR'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error in PR approval:', xhr);
                
                // Reset flag proses approval
                window.isApprovingPr = false;
                
                let errorMessage = 'Failed to approve PR. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    }

    // Reject PR
    function rejectPr(prId, notes) {
        console.log('rejectPr function called with:', {
            prId: prId,
            notes: notes
        });
        
        $.ajax({
            url: `/maintenance/pr/${prId}/reject`,
            method: 'POST',
            data: {
                notes: notes,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                console.log('Starting AJAX request to reject PR');
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
                console.log('Rejection success response:', response);
                
                if (response.success) {
                    // Tutup modal approval
                    $('#prApprovalModal').modal('hide');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'PR has been rejected successfully'
                    }).then(() => {
                        // Tampilkan kembali modal detail dan reload datanya
                        loadPrDetail(prId);
                    });
                } else {
                    console.error('Error in success response:', response);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to reject PR'
                    });
                }
            },
            error: function(xhr) {
                console.error('AJAX error response:', xhr);
                console.error('Error rejecting PR:', xhr.responseText);
                
                let errorMessage = 'Failed to reject PR. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    }

    // Update tampilan approval status berdasarkan data PR
    function updateApprovalStatus(pr) {
        console.log('Updating approval status for PR:', pr);
        
        // Chief Engineering Approval
        if (pr.chief_engineering_approval) {
            if (pr.chief_engineering_approval === 'APPROVED') {
                $('#chiefEngineeringApproval').html('<span class="badge bg-success">Approved</span>');
                
                // Tampilkan notes jika ada
                if (pr.chief_engineering_approval_notes) {
                    $('#chiefEngineeringDate').html(`
                        <small class="text-muted">${new Date(pr.chief_engineering_approval_date).toLocaleDateString('id-ID', {
                            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                        })}</small>
                        <div class="approval-notes mt-2">
                            <small class="fw-bold">Notes:</small>
                            <p class="small mb-0">${pr.chief_engineering_approval_notes}</p>
                        </div>
                    `);
                } else {
                    $('#chiefEngineeringDate').text(new Date(pr.chief_engineering_approval_date).toLocaleDateString('id-ID', {
                        day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                    }));
                }
            } else if (pr.chief_engineering_approval === 'REJECTED') {
                $('#chiefEngineeringApproval').html('<span class="badge bg-danger">Rejected</span>');
            } else {
                $('#chiefEngineeringApproval').html('<span class="badge bg-secondary">Pending</span>');
            }
        }
        
        // Purchasing Manager Approval
        if (pr.purchasing_manager_approval) {
            if (pr.purchasing_manager_approval === 'APPROVED') {
                $('#purchasingManagerApproval').html('<span class="badge bg-success">Approved</span>');
                
                // Tampilkan notes jika ada
                if (pr.purchasing_manager_approval_notes) {
                    $('#purchasingManagerDate').html(`
                        <small class="text-muted">${new Date(pr.purchasing_manager_approval_date).toLocaleDateString('id-ID', {
                            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                        })}</small>
                        <div class="approval-notes mt-2">
                            <small class="fw-bold">Notes:</small>
                            <p class="small mb-0">${pr.purchasing_manager_approval_notes}</p>
                        </div>
                    `);
                } else {
                    $('#purchasingManagerDate').text(new Date(pr.purchasing_manager_approval_date).toLocaleDateString('id-ID', {
                        day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                    }));
                }
            } else if (pr.purchasing_manager_approval === 'REJECTED') {
                $('#purchasingManagerApproval').html('<span class="badge bg-danger">Rejected</span>');
            } else {
                $('#purchasingManagerApproval').html('<span class="badge bg-secondary">Pending</span>');
            }
        }
        
        // COO Approval
        if (pr.coo_approval) {
            if (pr.coo_approval === 'APPROVED') {
                $('#cooApproval').html('<span class="badge bg-success">Approved</span>');
                
                // Tampilkan notes jika ada
                if (pr.coo_approval_notes) {
                    $('#cooDate').html(`
                        <small class="text-muted">${new Date(pr.coo_approval_date).toLocaleDateString('id-ID', {
                            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                        })}</small>
                        <div class="approval-notes mt-2">
                            <small class="fw-bold">Notes:</small>
                            <p class="small mb-0">${pr.coo_approval_notes}</p>
                        </div>
                    `);
                } else {
                    $('#cooDate').text(new Date(pr.coo_approval_date).toLocaleDateString('id-ID', {
                        day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                    }));
                }
            } else if (pr.coo_approval === 'REJECTED') {
                $('#cooApproval').html('<span class="badge bg-danger">Rejected</span>');
            } else {
                $('#cooApproval').html('<span class="badge bg-secondary">Pending</span>');
            }
        }
    }

    // Event untuk tombol Action di PR detail
    $(document).on('click', '#openApprovalModalBtn', function() {
        console.log('Action button clicked');
        $('#prDetailModal').modal('hide');
        setTimeout(() => {
            $('#prApprovalModal').modal('show');
        }, 500);
    });

    // Event untuk tombol Approve
    $(document).on('click', '#showApproveFormBtn', function() {
        console.log('Show approve form clicked');
        $('#approveFormSection').show();
        $('#rejectFormSection').hide();
    });

    // Event untuk tombol Reject
    $(document).on('click', '#showRejectFormBtn', function() {
        console.log('Show reject form clicked');
        $('#rejectFormSection').show();
        $('#approveFormSection').hide();
    });

    // Event untuk tombol Cancel
    $(document).on('click', '.hideFormBtn', function() {
        console.log('Hide form clicked');
        $('#approveFormSection, #rejectFormSection').hide();
    });

    // Event untuk submit form approval
    $(document).on('submit', '#prApproveForm', function(e) {
            e.preventDefault();
        console.log('Approve form submitted');
        
        // Perbaiki selector untuk mengambil notes
        const notes = $('#prApprovalActionModal #approvalNotes').val().trim();
        console.log('Approval notes from textarea:', notes);
            
            // Tentukan level berdasarkan status PR saat ini
        let level = currentApprovalLevel;
        console.log('Current approval level:', level);
        
        // Panggil fungsi approve dengan notes yang benar
        approvePr(currentPrId, notes, level);
    });

    // Event untuk submit form rejection
    $(document).on('submit', '#prRejectForm', function(e) {
            e.preventDefault();
        console.log('Reject form submitted');
        
        // Perbaiki selector untuk mengambil notes
        const notes = $('#prApprovalActionModal #rejectionNotes').val().trim();
        console.log('Rejection notes from textarea:', notes);
            
            if (!notes) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please provide a reason for rejection'
                });
                return;
            }
            
        // Panggil fungsi reject dengan notes yang benar
        rejectPr(currentPrId, notes);
    });

    // Reset form saat modal ditutup
    $('#prApprovalActionModal').on('hidden.bs.modal', function() {
        console.log('Approval modal hidden');
        $('#approveFormSection, #rejectFormSection').hide();
        $('#approvalNotes, #rejectionNotes').val('');
        
        // Tampilkan kembali modal detail PR
        setTimeout(() => {
            $('#prDetailModal').modal('show');
        }, 500);
    });

    // Initialize PR functionality on document ready
    $(document).ready(function() {
        initPr();
        initApprovalModal();
        
        // Load user info saat halaman dimuat
        loadUserInfo().then(data => {
            console.log('Initial user info loaded:', data);
        }).catch(error => {
            console.error('Error loading initial user info:', error);
        });
    });

    function updateTaskStatus(taskId, newStatus) {
            Swal.fire({
            title: 'Memindahkan Task',
            text: 'Mohon tunggu sebentar...',
                allowOutsideClick: false,
            didOpen: () => {
                    Swal.showLoading();
                }
            });
            
        $.ajax({
            url: '/maintenance/task/update-status',
            type: 'POST',
            data: {
                taskId: taskId,
                status: newStatus,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Status task berhasil diperbarui',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat memperbarui status task';
                let errorTitle = 'Informasi';
                let errorIcon = 'info';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    if (xhr.status === 403) {
                        errorMessage = xhr.responseJSON.error;
                        errorTitle = 'Hak Akses';
                        errorIcon = 'warning';
                    } else if (xhr.status === 400) {
                        errorMessage = xhr.responseJSON.error;
                        errorTitle = 'Status PO';
                        errorIcon = 'info';
                    }
                }
                
            Swal.fire({
                    icon: errorIcon,
                    title: errorTitle,
                    text: errorMessage,
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    }

    // Fungsi untuk inisialisasi modal approval
    function initApprovalModal() {
        console.log('Initializing PR approval modal');
        
        // Initialize PR modal handlers
        initPrApprovalModal();
    }

    // Initialize approval modal handlers
    function initPrApprovalModal() {
        // When approve button is clicked, show approval form
        $(document).on('click', '#pr-newApproveBtn', function() {
            $('#pr-newApproveBtn, #pr-newRejectBtn').hide();
            $('#pr-newApprovalForm').show();
            $('#pr-newRejectionForm').hide();
        });
        
        // When reject button is clicked, show rejection form
        $(document).on('click', '#pr-newRejectBtn', function() {
            $('#pr-newApproveBtn, #pr-newRejectBtn').hide();
            $('#pr-newRejectionForm').show();
            $('#pr-newApprovalForm').hide();
        });
        
        // When cancel button is clicked on either form
        $(document).on('click', '.pr-cancel-form-btn', function() {
            $('#pr-newApprovalForm, #pr-newRejectionForm').hide();
            $('#pr-newApproveBtn, #pr-newRejectBtn').show();
        });
        
        // Handle approval form submission
        $(document).on('submit', '#pr-newApprovalNotesForm', function(e) {
            e.preventDefault();
            const notes = $('#pr-newApprovalNotes').val().trim();
            
            approvePr(currentPrId, notes, currentApprovalLevel);
        });
        
        // Handle rejection form submission
        $(document).on('submit', '#pr-newRejectionNotesForm', function(e) {
            e.preventDefault();
            const notes = $('#pr-newRejectionNotes').val().trim();
            
            if (!notes) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please provide a reason for rejection'
                });
                return;
            }
            
            rejectPr(currentPrId, notes);
        });
        
        // Reset forms when modal is closed
        $('#prApprovalModal').on('hidden.bs.modal', function() {
            $('#pr-newApprovalForm, #pr-newRejectionForm').hide();
            $('#pr-newApproveBtn, #pr-newRejectBtn').show();
            $('#pr-newApprovalNotes, #pr-newRejectionNotes').val('');
        });
    }
})();
