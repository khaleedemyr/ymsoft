<!-- PO List Modal -->
<div class="modal fade" id="poListModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Purchase Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="task-number-display fw-bold"></span>
                    </div>
                    <div>
                        <button class="btn btn-success btn-sm add-new-po-btn">
                            <i class="ri-add-line"></i> Create PO
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Supplier</th>
                                <th>Created Date</th>
                                <th>Status</th>
                                <th>Total Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="poListTableBody">
                            <tr>
                                <td colspan="6" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div id="noPOMessage" class="alert alert-info d-none">
                    No purchase orders found for this task.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create PO Modal -->
<div class="modal fade" id="createPoModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Purchase Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- PR List -->
                <div id="prAccordion" class="accordion mb-3">
                    <!-- PR items will be populated here -->
                </div>

                <div class="mt-3">
                    <button type="button" class="btn btn-primary" id="groupBySupplierBtn">
                        Group by Supplier & Create PO
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk upload invoice -->
<div class="modal fade" id="uploadInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="invoiceForm">
                    <div class="mb-3">
                        <label class="form-label">Invoice Number</label>
                        <input type="text" class="form-control" name="invoice_number" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Invoice Date</label>
                        <input type="date" class="form-control" name="invoice_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Invoice File</label>
                        <input type="file" class="form-control" name="invoice_file" accept="image/*" required>
                        <small class="text-muted">Allowed file types: JPG, PNG, GIF, WEBP</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveInvoiceBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- PO Detail Modal -->
<div class="modal fade" id="poDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail PO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title mb-3">PO Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="130">PO Number</th>
                                        <td class="po-number"></td>
                                    </tr>
                                    <tr>
                                        <th>Created Date</th>
                                        <td class="created-date"></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td class="po-status"></td>
                                    </tr>
                                    <tr>
                                        <th>Created By</th>
                                        <td class="created-by"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title mb-3">Supplier Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="130">Name</th>
                                        <td class="supplier-name"></td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td class="supplier-phone"></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td class="supplier-email"></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td class="supplier-address"></td>
                                    </tr>
                                    <tr>
                                        <th>Payment Terms</th>
                                        <td class="supplier-payment-terms"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-3">Items</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Description</th>
                                        <th>Specifications</th>
                                        <th class="text-end">Quantity</th>
                                        <th>Unit</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="poItemsTableBody">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">Total:</td>
                                        <td class="text-end fw-bold total-amount"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Approval Status</h6>
                                    <!-- Static Approval Buttons -->
                                    <div id="staticApprovalButtons">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" id="staticApproveBtn" class="btn btn-success">
                                            Action <i class="ri-arrow-right-line"></i>
                                            </button>
                                          
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="approval-step">
                                            <h6>GM Finance</h6>
                                            <div id="gmFinanceApproval" class="approval-status">
                                                <span class="badge bg-secondary">Pending</span>
                                            </div>
                                            <small id="gmFinanceDate" class="text-muted"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="approval-step">
                                            <h6>Managing Director</h6>
                                            <div id="managingDirectorApproval" class="approval-status">
                                                <span class="badge bg-secondary">Pending</span>
                                            </div>
                                            <small id="managingDirectorDate" class="text-muted"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="approval-step">
                                            <h6>President Director</h6>
                                            <div id="presidentDirectorApproval" class="approval-status">
                                                <span class="badge bg-secondary">Pending</span>
                                            </div>
                                            <small id="presidentDirectorDate" class="text-muted"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rejection Notes Section -->
                <div id="rejectionNotesSection" class="mt-3 d-none">
                    <div class="card border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Rejection Notes</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <textarea id="rejectionNotes" class="form-control" rows="3" placeholder="Please provide reason for rejection"></textarea>
                            </div>
                            <div class="mt-2 text-end">
                                <button type="button" id="cancelRejectionBtn" class="btn btn-sm btn-secondary">Cancel</button>
                                <button type="button" id="confirmRejectionBtn" class="btn btn-sm btn-danger">Confirm Rejection</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approval Notes Section -->
                <div id="approvalNotesSection" class="mt-3 d-none">
                    <div class="card border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Approval Notes</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <textarea id="approvalNotes" class="form-control" rows="3" placeholder="Additional notes for approval (optional)"></textarea>
                            </div>
                            <div class="mt-2 text-end">
                                <button type="button" id="cancelApprovalBtn" class="btn btn-sm btn-secondary">Cancel</button>
                                <button type="button" id="confirmApprovalBtn" class="btn btn-sm btn-success confirm-po-approval" data-type="po">
                                    Confirm Approval
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tambahkan setelah card approval status, sebelum invoice section -->
                <div class="good-receive-section d-none">
                    <div class="card mt-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Good Receive</h6>
                                <button type="button" class="btn btn-success btn-sm" id="createGoodReceiveBtn">
                                    <i class="ri-add-line me-1"></i> Create Good Receive
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3 invoice-section d-none">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Invoice Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="130">Invoice Number</th>
                                        <td class="invoice-number"></td>
                                    </tr>
                                    <tr>
                                        <th>Invoice Date</th>
                                        <td class="invoice-date"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="invoice-image-container text-center">
                            <img src="" alt="Invoice" class="invoice-image img-fluid" style="max-height: 500px;">
                        </div>
                    </div>
                </div>

                <!-- Tambahkan setelah invoice section -->
                <div class="good-receive-info mt-3">
                    <div class="card">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Good Receive Information</h6>
                                <button type="button" class="btn btn-success btn-sm" id="createGoodReceiveBtn">
                                    <i class="ri-add-line me-1"></i> Create Good Receive
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="goodReceiveData" class="d-none">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Terima</label>
                                            <p class="receive-date mb-0"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Catatan</label>
                                            <p class="receive-notes mb-0"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label">Foto Dokumentasi</label>
                                    <div class="receive-photos d-flex gap-2 flex-wrap">
                                        <!-- Photos will be added here -->
                                    </div>
                                </div>
                            </div>
                            <div id="noGoodReceiveData" class="text-center py-3">
                                <p class="text-muted mb-0">Belum ada data good receive</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal good receive dengan camera stream -->
<div class="modal fade" id="goodReceiveModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Good Receive</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="goodReceiveForm">
                    @csrf
                    <div class="form-group">
                        <label for="receive_date">Tanggal Terima</label>
                        <input type="date" class="form-control" name="receive_date" required>
                    </div>
                    <div class="form-group">
                        <label for="receive_notes">Catatan (Opsional)</label>
                        <textarea class="form-control" name="receive_notes" rows="3"></textarea>
                    </div>
                    <div class="camera-container">
                        <video id="cameraStream" autoplay playsinline class="w-100 mb-2" style="max-height: 300px; object-fit: cover;"></video>
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-primary flex-grow-1" id="captureBtn">
                                <i class="ri-camera-line me-1"></i> Capture Photo
                            </button>
                            <button type="button" class="btn btn-secondary" id="switchCameraBtn">
                                <i class="ri-camera-switch-line"></i>
                            </button>
                        </div>
                    </div>
                    <div id="photoPreviewContainer" class="mt-3">
                        <!-- Photo previews will be added here -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveGoodReceiveBtn">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- NEW: PO Approval Modal -->
<div class="modal fade" id="poApprovalModal" tabindex="-1" aria-labelledby="poApprovalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="poApprovalModalLabel">PO Approval Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="mb-3 text-center" id="po-approvalModalPoNumber"></h6>
                    <p class="text-center" id="po-approvalModalLevel"></p>
                </div>
                
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <div class="btn-group" role="group">
                            <button type="button" id="po-newApproveBtn" class="btn btn-success">
                                <i class="ri-check-line me-1"></i> Approve
                            </button>
                            <button type="button" id="po-newRejectBtn" class="btn btn-danger">
                                <i class="ri-close-line me-1"></i> Reject
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Approval Form -->
                <div id="po-newApprovalForm" class="approval-form" style="display: none;">
                    <div class="card border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Approval Notes</h6>
                        </div>
                        <div class="card-body">
                            <form id="po-newApprovalNotesForm">
                                <div class="form-group mb-3">
                                    <textarea id="po-newApprovalNotes" name="notes" class="form-control" rows="3" placeholder="Enter approval notes (optional)"></textarea>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-sm btn-secondary me-2 po-cancel-form-btn">Cancel</button>
                                    <button type="submit" class="btn btn-sm btn-success">Confirm Approval</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Rejection Form -->
                <div id="po-newRejectionForm" class="rejection-form" style="display: none;">
                    <div class="card border border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">Rejection Reason</h6>
                        </div>
                        <div class="card-body">
                            <form id="po-newRejectionNotesForm">
                                <div class="form-group mb-3">
                                    <textarea id="po-newRejectionNotes" name="notes" class="form-control" rows="3" placeholder="Enter rejection reason (required)"></textarea>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-sm btn-secondary me-2 po-cancel-form-btn">Cancel</button>
                                    <button type="submit" class="btn btn-sm btn-danger">Confirm Rejection</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.invoice-image-container {
    margin-top: 15px;
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
}

.invoice-image {
    max-width: 100%;
    height: auto;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Untuk modal scrolling yang lebih baik */
#poDetailModal .modal-body {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
}

.photo-preview-item {
    position: relative;
    width: 250px;
    height: 250px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    overflow: hidden;
}

.photo-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-preview-item .remove-photo {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    padding: 4px;
    cursor: pointer;
    font-size: 18px;
    color: #dc3545;
}

.camera-container {
    position: relative;
    background: #000;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 1rem;
}

#cameraStream {
    width: 100%;
    max-height: 300px;
    object-fit: cover;
    background: #000;
}

.camera-controls {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1rem;
    background: rgba(0,0,0,0.5);
}

/* Untuk memastikan video selalu terlihat */
video {
    background: #000;
    display: block;
}

.photo-item {
    position: relative;
    width: 250px;
    height: 250px;
    transition: transform 0.2s;
    cursor: pointer;
    margin: 0 10px 10px 0;
}

.photo-item:hover {
    transform: scale(1.05);
}

.photo-item img {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.good-receive-info .card-header {
    background-color: #f8f9fa;
}

.good-receive-info .form-label {
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

function addPhotoPreview(photoId, photoUrl) {
    console.log('Adding photo preview for ID:', photoId);
    
    const previewContainer = document.getElementById('photoPreviewContainer');
    
    // Buat element preview baru
    const previewDiv = document.createElement('div');
    previewDiv.className = 'photo-preview-item position-relative d-inline-block me-2 mb-2';
    previewDiv.setAttribute('data-photo-id', photoId);
    
    // Buat element image
    const img = document.createElement('img');
    img.src = photoUrl;
    img.className = 'preview-image';
    img.style.width = '250px';
    img.style.height = '250px';
    img.style.objectFit = 'cover';
    img.style.borderRadius = '4px';
    
    // Buat tombol delete
    const deleteBtn = document.createElement('button');
    deleteBtn.className = 'btn btn-danger btn-sm position-absolute';
    deleteBtn.style.top = '5px';
    deleteBtn.style.right = '5px';
    deleteBtn.innerHTML = '<i class="ri-delete-bin-line"></i>';
    
    // Event listener untuk tombol delete
    deleteBtn.onclick = function(e) {
        e.preventDefault();
        e.stopPropagation();
        removePhoto(photoId);
    };
    
    // Gabungkan elements
    previewDiv.appendChild(img);
    previewDiv.appendChild(deleteBtn);
    
    // Tambahkan ke container
    previewContainer.appendChild(previewDiv);
    
    updateNoPhotosMessage();
}
</style>
