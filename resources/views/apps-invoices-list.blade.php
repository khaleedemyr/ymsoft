@extends('layouts.master')
@section('title')
@lang('translation.invoice-list')
@endsection
@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1')
Invoice
@endslot
@slot('title')
Invoice List
@endslot
@endcomponent



<div class="row row-cols-5 gx-3">
    <div class="col-lg col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-xs flex-shrink-0">
                        <div class="avatar-title bg-body-secondary text-primary border border-primary-subtle rounded-circle">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-0">Total Invoices</p>
                    </div>
                </div>
                <h4 class="mb-0"><span class="counter-value" data-target="8956">0</span> <small class="text-success fs-xs fw-normal ms-1"><i class="bi bi-arrow-up align-baseline"></i> 12.09%</small></h4>
            </div>
        </div>
    </div>
    <div class="col-lg col-sm-6 col-12">
        <div class="card bg-primary">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-xs flex-shrink-0">
                        <div class="avatar-title bg-body-secondary text-primary border border-primary-subtle rounded-circle">
                            <i class="bi bi-file-earmark-check"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-white text-opacity-75 mb-0">Paid Invoices</p>
                    </div>
                </div>
                <h4 class="text-white mb-0"><span class="counter-value" data-target="4519">0</span> <small class="text-warning fs-xs fw-normal ms-1"><i class="bi bi-arrow-up align-baseline"></i> 6.57%</small></h4>
            </div>
        </div>
    </div>
    <div class="col-lg col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-xs flex-shrink-0">
                        <div class="avatar-title bg-body-secondary text-warning border border-warning-subtle rounded-circle">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-0">Pending Invoices</p>
                    </div>
                </div>
                <h4 class="mb-0"><span class="counter-value" data-target="2648">0</span> <small class="text-success fs-xs fw-normal ms-1"><i class="bi bi-arrow-up align-baseline"></i> 4.07%</small></h4>
            </div>
        </div>
    </div>
    <div class="col-lg col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-xs flex-shrink-0">
                        <div class="avatar-title bg-body-secondary text-danger border border-danger-subtle rounded-circle">
                            <i class="bi bi-file-earmark-plus"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-0">Overdue Invoices</p>
                    </div>
                </div>
                <h4 class="mb-0"><span class="counter-value" data-target="871">0</span> <small class="text-danger fs-xs fw-normal ms-1"><i class="bi bi-arrow-down align-baseline"></i> 3.49%</small></h4>
            </div>
        </div>
    </div>
    <div class="col-lg col-sm-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-xs flex-shrink-0">
                        <div class="avatar-title bg-body-secondary text-info border border-info-subtle rounded-circle">
                            <i class="bi bi-file-earmark-minus"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-0">Draft Invoices</p>
                    </div>
                </div>
                <h4 class="mb-0"><span class="counter-value" data-target="871">0</span> <small class="text-danger fs-xs fw-normal ms-1"><i class="bi bi-arrow-down align-baseline"></i> 3.49%</small></h4>
            </div>
        </div>
    </div>
</div>
<!--end row-->

<div class="row" id="invoiceList">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <div class="col-xl-2 col-md-3">
                        <div class="search-box">
                            <input type="text" class="form-control search" placeholder="Search for invoice, date, client or something...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-xl-2 col-md-3">
                        <div>
                            <input type="date" class="form-control" id="exampleInputdate" placeholder="Select date range" data-provider="flatpickr" data-date-format="d M, Y" data-range-date="true">
                        </div>
                    </div>
                    <div class="col-md-auto ms-auto">
                        <div class="hstack gap-2 mt-2 mt-md-0">
                            <button class="btn btn-subtle-info"><i class="bi bi-funnel"></i></button>
                            <button class="btn btn-subtle-danger d-none" id="remove-actions" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
                            <a href="apps-invoices-create" class="btn btn-secondary"><i class="bi bi-plus-circle align-baseline me-1"></i> Add Invoice</a>
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div>
            <div class="card-body mt-3">
                <div class="table-responsive table-card">
                    <table class="table table-centered align-middle table-custom-effect table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                        <label class="form-check-label" for="checkAll"></label>
                                    </div>
                                </th>
                                <th scope="col" class="sort cursor-pointer" data-sort="invoice_id">ID</th>
                                <th scope="col" class="sort cursor-pointer" data-sort="customer_name">Customer Name</th>
                                <th scope="col" class="sort cursor-pointer" data-sort="email">Email</th>
                                <th scope="col" class="sort cursor-pointer" data-sort="create_date">Create Date</th>
                                <th scope="col" class="sort cursor-pointer" data-sort="due_date">Due Date</th>
                                <th scope="col" class="sort cursor-pointer" data-sort="amount">Amount</th>
                                <th scope="col" class="sort cursor-pointer" data-sort="status">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody class="list form-check-all" id="invoice-list-data">
                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                    <div class="noresult" style="display: none">
                        <div class="text-center py-4">
                            <i class="ph-magnifying-glass fs-1 text-primary"></i>
                            <h5 class="mt-2">Sorry! No Result Found</h5>
                            <p class="text-muted mb-0">We've searched more than 6+ invoice We did not find any invoice for you search.</p>
                        </div>
                    </div>
                </div>
                <div class="row align-items-center mt-4 pt-3" id="pagination-element">
                    <div class="col-sm">
                        <div class="text-muted text-center text-sm-start">
                            Showing <span class="fw-semibold">10</span> of <span class="fw-semibold">15</span> Results
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-sm-auto mt-3 mt-sm-0">
                        <div class="pagination-wrap hstack justify-content-center gap-2">
                            <a class="page-item pagination-prev disabled" href="#">
                                Previous
                            </a>
                            <ul class="pagination listjs-pagination mb-0"></ul>
                            <a class="page-item pagination-next" href="#">
                                Next
                            </a>
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

<!-- deleteRecordModal -->
<div id="deleteRecordModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" id="deleteRecord-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-md-5">
                <div class="text-center">
                    <div class="text-danger">
                        <i class="bi bi-trash display-5"></i>
                    </div>
                    <div class="mt-4">
                        <h4 class="mb-2">Are you sure ?</h4>
                        <p class="text-muted mx-3 mb-0">Are you sure you want to remove this record ?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 pt-2 mb-2">
                    <button type="button" class="btn w-sm btn-light btn-hover" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger btn-hover" id="delete-record">Yes, Delete It!</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /deleteRecordModal -->


@endsection
@section('script')
<script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/invoice-list.init.js') }}"></script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
