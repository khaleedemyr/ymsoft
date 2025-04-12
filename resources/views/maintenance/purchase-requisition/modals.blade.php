<!-- PR List Modal -->
<div class="modal fade" id="prListModal" tabindex="-1" aria-labelledby="prListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prListModalLabel">Purchase Requisitions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between mb-3">
                    <h6 class="task-number-display"></h6>
                    <button class="btn btn-sm btn-primary add-new-pr-btn">
                        <i class="ri-add-line me-1"></i> Add New PR
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>PR Number</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="prListTableBody">
                            <!-- PR list will be loaded here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Grand Total:</td>
                                <td id="prListGrandTotal" class="fw-bold text-primary"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div id="noPrMessage" class="text-center p-3 d-none">
                    <p class="text-muted mb-2">No purchase requisitions found for this task.</p>
                    <button class="btn btn-sm btn-primary add-new-pr-btn">
                        <i class="ri-add-line me-1"></i> Create First PR
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Create PR Modal -->
<div class="modal fade" id="createPrModal" tabindex="-1" aria-labelledby="createPrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPrModalLabel">Create Purchase Requisition</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="prForm" novalidate>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="prTaskNumber" class="form-label">Task Number</label>
                                <input type="text" class="form-control" id="prTaskNumber" readonly>
                                <input type="hidden" id="prTaskId" name="task_id">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="prNumber" class="form-label">PR Number</label>
                                <input type="text" class="form-control" id="prNumber" name="pr_number" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="prNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="prNotes" name="notes" rows="2"></textarea>
                    </div>
                    
                    <h6 class="mt-4 mb-3">PR Items</h6>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" id="prItemsTable">
                            <thead>
                                <tr>
                                    <th width="20%">Item Name</th>
                                    <th width="20%">Description</th>
                                    <th width="15%">Specifications</th>
                                    <th width="10%">Quantity</th>
                                    <th width="10%">Unit</th>
                                    <th width="10%">Price</th>
                                    <th width="10%">Subtotal</th>
                                    <th width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="prItemsTableBody">
                                <!-- Initial row -->
                                <tr class="pr-item-row">
                                    <td>
                                        <input type="text" class="form-control item-name" name="items[0][item_name]" required>
                                    </td>
                                    <td>
                                        <textarea class="form-control item-description" name="items[0][description]" rows="2"></textarea>
                                    </td>
                                    <td>
                                        <textarea class="form-control item-specifications" name="items[0][specifications]" rows="2"></textarea>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control item-qty" name="items[0][quantity]" step="0.01" min="0.01" value="1" required>
                                    </td>
                                    <td>
                                        <select class="form-select item-unit" name="items[0][unit_id]" required>
                                            <option value="">Select Unit</option>
                                            <!-- Units will be loaded dynamically -->
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control item-price" name="items[0][price]" step="0.01" min="0" value="0" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control item-subtotal" name="items[0][subtotal]" step="0.01" min="0" value="0" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger delete-row-btn">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Total:</strong></td>
                                    <td>
                                        <input type="number" class="form-control" id="prTotalAmount" name="total_amount" value="0" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success add-row-btn">
                                            <i class="ri-add-line"></i> Add
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save PR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PR Detail Modal -->
<div class="modal fade" id="prDetailModal" tabindex="-1" aria-labelledby="prDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prDetailModalLabel">Purchase Requisition Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">PR Number</th>
                                <td width="60%" id="detailPrNumber"></td>
                            </tr>
                            <tr>
                                <th>Task Number</th>
                                <td id="detailTaskNumber"></td>
                            </tr>
                            <tr>
                                <th>Created By</th>
                                <td id="detailCreatedBy"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Status</th>
                                <td width="60%" id="detailStatus"></td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td id="detailCreatedAt"></td>
                            </tr>
                            <tr>
                                <th>Total Amount</th>
                                <td id="detailTotalAmount"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Notes</h6>
                            </div>
                            <div class="card-body">
                                <p id="detailNotes" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Approval Section -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Approval Status</h6>
                                    <button id="openApprovalModalBtn" class="btn btn-sm btn-primary" style="display: none;">
                                        Action <i class="ri-arrow-right-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="approval-step">
                                            <h6>Chief Engineering</h6>
                                            <div id="chiefEngineeringApproval" class="approval-status">
                                                <span class="badge bg-secondary">Pending</span>
                                            </div>
                                            <small id="chiefEngineeringDate" class="text-muted"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="approval-step">
                                            <h6>Purchasing Manager</h6>
                                            <div id="purchasingManagerApproval" class="approval-status">
                                                <span class="badge bg-secondary">Pending</span>
                                            </div>
                                            <small id="purchasingManagerDate" class="text-muted"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="approval-step">
                                            <h6>COO</h6>
                                            <div id="cooApproval" class="approval-status">
                                                <span class="badge bg-secondary">Pending</span>
                                            </div>
                                            <small id="cooDate" class="text-muted"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h6 class="mt-4 mb-3">PR Items</h6>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="prDetailItemsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th>Description</th>
                                <th>Specifications</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="prDetailItemsTableBody">
                            <!-- Items will be loaded here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7" class="text-end"><strong>Total:</strong></td>
                                <td id="detailFooterTotal"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- NEW: Approval Action Modal -->
<div class="modal fade" id="prApprovalModal" tabindex="-1" aria-labelledby="prApprovalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prApprovalModalLabel">PR Approval Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="mb-3 text-center" id="pr-approvalModalPrNumber"></h6>
                    <p class="text-center" id="pr-approvalModalLevel"></p>
                </div>
                
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <div class="btn-group" role="group">
                            <button type="button" id="pr-newApproveBtn" class="btn btn-success">
                                <i class="ri-check-line me-1"></i> Approve
                            </button>
                            <button type="button" id="pr-newRejectBtn" class="btn btn-danger">
                                <i class="ri-close-line me-1"></i> Reject
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Approval Form -->
                <div id="pr-newApprovalForm" class="approval-form" style="display: none;">
                    <div class="card border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Approval Notes</h6>
                        </div>
                        <div class="card-body">
                            <form id="pr-newApprovalNotesForm">
                                <div class="form-group mb-3">
                                    <textarea id="pr-newApprovalNotes" name="notes" class="form-control" rows="3" placeholder="Enter approval notes (optional)"></textarea>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-sm btn-secondary me-2 pr-cancel-form-btn">Cancel</button>
                                    <button type="submit" class="btn btn-sm btn-success">Confirm Approval</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Rejection Form -->
                <div id="pr-newRejectionForm" class="rejection-form" style="display: none;">
                    <div class="card border border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">Rejection Reason</h6>
                        </div>
                        <div class="card-body">
                            <form id="pr-newRejectionNotesForm">
                                <div class="form-group mb-3">
                                    <textarea id="pr-newRejectionNotes" name="notes" class="form-control" rows="3" placeholder="Enter rejection reason (required)"></textarea>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-sm btn-secondary me-2 pr-cancel-form-btn">Cancel</button>
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

<!-- Simple PR Approval Modal -->
<div class="modal fade" id="prApprovalActionModal" tabindex="-1" aria-labelledby="prApprovalActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prApprovalActionModalLabel">PR Approval Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" id="showApproveFormBtn">
                            <i class="ri-check-line me-1"></i> Approve
                        </button>
                        <button type="button" class="btn btn-danger" id="showRejectFormBtn">
                            <i class="ri-close-line me-1"></i> Reject
                        </button>
                    </div>
                </div>

                <!-- Approve Form -->
                <div id="approveFormSection" style="display: none;">
                    <form id="prApproveForm">
                        <div class="mb-3">
                            <label for="approvalNotes" class="form-label">Approval Notes</label>
                            <textarea class="form-control" id="approvalNotes" rows="3" placeholder="Enter approval notes (optional)"></textarea>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary me-2 hideFormBtn">Cancel</button>
                            <button type="submit" class="btn btn-success">Confirm Approval</button>
                        </div>
                    </form>
                </div>

                <!-- Reject Form -->
                <div id="rejectFormSection" style="display: none;">
                    <form id="prRejectForm">
                        <div class="mb-3">
                            <label for="rejectionNotes" class="form-label">Rejection Reason</label>
                            <textarea class="form-control" id="rejectionNotes" rows="3" placeholder="Enter rejection reason (required)" required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary me-2 hideFormBtn">Cancel</button>
                            <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Styling for PR Approval */
.approval-step {
    padding: 15px;
    text-align: center;
    border-radius: 5px;
    background-color: #f8f9fa;
    margin-bottom: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.approval-step h6 {
    margin-bottom: 10px;
    font-weight: 600;
}

.approval-status {
    margin: 10px 0;
}

.approval-status .badge {
    padding: 6px 10px;
    font-size: 0.85rem;
}

small.text-muted {
    font-size: 0.75rem;
    display: block;
}

.approval-notes {
    background-color: #f8f9fa;
    border-radius: 4px;
    padding: 8px;
    margin-top: 8px;
}

#newRejectionForm textarea:focus {
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    border-color: #f5c6cb;
}
</style>
