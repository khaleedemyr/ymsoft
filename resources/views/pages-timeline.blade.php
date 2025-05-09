@extends('layouts.master')
@section('title')
@lang('translation.timeline')
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Pages @endslot
@slot('title') Timeline @endslot
@endcomponent


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Vertical Timeline</h6>
            </div>
            <div class="card-body">
                <div class="acitivity-timeline acitivity-main">
                    <div class="acitivity-item d-flex">
                        <div class="flex-shrink-0">
                            <img src="build/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 lh-base">Purchased by James Price</h6>
                            <p class="text-muted mb-2">Product noise evolve smartwatch </p>
                            <small class="mb-0 text-muted">05:57 AM Today</small>
                        </div>
                    </div>
                    <div class="acitivity-item py-3 d-flex">
                        <div class="flex-shrink-0">
                            <img src="build/images/users/avatar-1.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 lh-base">Natasha Carey have liked the products</h6>
                            <p class="text-muted mb-2">Allow users to like products in your WooCommerce store.</p>
                            <small class="mb-0 text-muted">25 Dec, 2022</small>
                        </div>
                    </div>
                    <div class="acitivity-item py-3 d-flex">
                        <div class="flex-shrink-0">
                            <img src="build/images/users/avatar-3.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 lh-base">Today offers by <a href="javascript:void(0);" class="link-secondary">Digitech Galaxy</a></h6>
                            <p class="text-muted mb-2">Offer is valid on orders of $230 Or above for selected products only.</p>
                            <small class="mb-0 text-muted">12 Dec, 2022</small>
                        </div>
                    </div>
                    <div class="acitivity-item py-3 d-flex">
                        <div class="flex-shrink-0">
                            <img src="build/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 lh-base">Favorites Product</h6>
                            <p class="text-muted mb-2">Esther James have favorites product.</p>
                            <small class="mb-0 text-muted">25 Nov, 2022</small>
                        </div>
                    </div>
                    <div class="acitivity-item py-3 d-flex">
                        <div class="flex-shrink-0">
                            <img src="build/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 lh-base">Flash sale starting <span class="text-primary">Tomorrow.</span></h6>
                            <p class="text-muted mb-2">Flash sale by <a href="javascript:void(0);" class="link-secondary fw-medium">Zoetic Fashion</a></p>
                            <small class="mb-0 text-muted">22 Oct, 2022</small>
                        </div>
                    </div>
                    <div class="acitivity-item d-flex">
                        <div class="flex-shrink-0">
                            <img src="build/images/users/avatar-5.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 lh-base">Monthly sales report</h6>
                            <p class="text-muted mb-2"><span class="text-danger">2 days left</span> notification to submit the monthly sales report. <a href="javascript:void(0);" class="link-warning text-decoration-underline">Reports Builder</a></p>
                            <small class="mb-0 text-muted">15 Oct, 2022</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card text-center">
            <div class="card-header">
                <h6 class="card-title mb-0">Center Vertical Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item left">
                        <i class="icon ri-user-smile-line"></i>
                        <div class="date fs-md">15 Dec 2021</div>
                        <div class="content">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 border-end border-dashed pe-3">
                                    <img src="build/images/users/avatar-5.jpg" alt="" class="avatar-sm rounded">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="">@Erica245 <small class="text-muted fs-13 fw-normal">- 10 min Ago</small></h6>
                                    <p class="text-muted fs-md mb-2">Wish someone a sincere ‘good luck in your new job’ with these sweet messages. Make sure you pick out a good luck new job card to go with the words, and pop a beautiful bunch of flowers from our gift range in your basket, to make them feel super special.</p>
                                    <div class="hstack gap-2">
                                        <a class="btn btn-sm btn-light"><span class="me-1">&#128293;</span> 19</a>
                                        <a class="btn btn-sm btn-light"><span class="me-1">&#129321;</span> 22</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item right">
                        <i class="icon ri-user-smile-line"></i>
                        <div class="date fs-md">22 Oct 2021</div>
                        <div class="content">
                            <h6>Adding a new event with attachments</h6>
                            <p class="text-muted fs-md mb-2">Too much or too little spacing, as in the example below, can make things unpleasant for the reader.</p>
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="d-flex border border-dashed p-2 rounded position-relative">
                                        <div class="flex-shrink-0 avatar-xs">
                                            <div class="avatar-title bg-danger-subtle text-danger fs-15 rounded-circle">
                                                <i class="ri-image-2-line"></i>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">

                                        </div>
                                        <div class="flex-grow-1 overflow-hidden ms-2">
                                            <h6 class="text-truncate mb-0"><a href="javascript:void(0);" class="stretched-link">Business Template - UI/UX design</a></h6>
                                            <small>685 KB</small>
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-sm-6">
                                    <div class="d-flex border border-dashed p-2 rounded position-relative">
                                        <div class="flex-shrink-0 avatar-xs">
                                            <div class="avatar-title bg-info-subtle text-info fs-15 rounded-circle">
                                                <i class="ri-file-zip-line"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-2 overflow-hidden">
                                            <h6 class="mb-0 text-truncate"><a href="javascript:void(0);" class="stretched-link">Bank Management System - PSD</a></h6>
                                            <small>8.78 MB</small>
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                    </div>
                    <div class="timeline-item left">
                        <i class="icon ri-user-smile-line"></i>
                        <div class="date fs-md">10 Jul 2021</div>
                        <div class="content">
                            <h6>Create new project building product</h6>
                            <p class="text-muted fs-md mb-2">Every team project can have a vixon. Use the vixon to share information with your team to understand and contribute to your project.</p>
                            <div class="avatar-group">
                                <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="" data-bs-original-title="Christi">
                                    <img src="build/images/users/avatar-4.jpg" alt="" class="rounded-circle avatar-xs">
                                </a>
                                <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="" data-bs-original-title="Frank Hook">
                                    <img src="build/images/users/avatar-3.jpg" alt="" class="rounded-circle avatar-xs">
                                </a>
                                <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="" data-bs-original-title=" Ruby">
                                    <div class="avatar-xs">
                                        <div class="avatar-title rounded-circle bg-light text-primary">
                                            R
                                        </div>
                                    </div>
                                </a>
                                <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="" data-bs-original-title="more">
                                    <div class="avatar-xs">
                                        <div class="avatar-title rounded-circle">
                                            2+
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item right">
                        <i class="icon ri-user-smile-line"></i>
                        <div class="date fs-md">18 May 2021</div>
                        <div class="content">
                            <h6>Donald Palmer <small class="text-muted fs-13 fw-normal">- Has changed 2 attributes</small></h6>
                            <p class="text-muted fs-md fst-italic mb-2">" This is an awesome admin dashboard template. It is extremely well structured and uses state of the art components (e.g. one of the only templates using boostrap 5.1.3 so far). I integrated it into a Rails 6 project. Needs manual integration work of course but the template structure made it easy. "</p>
                            <div class="hstack gap-2">
                                <a class="btn btn-sm bg-light"><span class="me-1">&#128151;</span> 35</a>
                                <a class="btn btn-sm btn-light"><span class="me-1">&#128077;</span> 10</a>
                                <a class="btn btn-sm btn-light"><span class="me-1">&#128591;</span> 10</a>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item left">
                        <i class="icon ri-user-smile-line"></i>
                        <div class="date fs-md">10 Feb 2021</div>
                        <div class="content">
                            <h6>Vixon admin dashboard templates layout upload</h6>
                            <p class="text-muted fs-md mb-2">Powerful, clean & modern responsive bootstrap 5 admin template. The maximum file size for uploads in this demo :</p>
                            <div class="row border border-dashed rounded gx-2 p-2">
                                <div class="col-3">
                                    <img src="build/images/small/img-2.jpg" alt="" class="img-fluid rounded">
                                </div>
                                <!--end col-->
                                <div class="col-3">
                                    <img src="build/images/small/img-3.jpg" alt="" class="img-fluid rounded">
                                </div>
                                <!--end col-->
                                <div class="col-3">
                                    <img src="build/images/small/img-4.jpg" alt="" class="img-fluid rounded">
                                </div>
                                <!--end col-->
                                <div class="col-3">
                                    <img src="build/images/small/img-6.jpg" alt="" class="img-fluid rounded">
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                    </div>
                    <div class="timeline-item right">
                        <i class="icon ri-user-smile-line"></i>
                        <div class="date fs-md">01 Jan 2021</div>
                        <div class="content">
                            <h6>New ticket received</h6>
                            <p class="text-muted fs-md mb-2">
                                It is important for us that we receive email notifications when a ticket is created as our IT staff are mobile and will not always be looking at the dashboard for new tickets.
                            </p>
                            <a href="javascript:void(0);" class="link-primary text-decoration-underline">Read More <i class="ri-arrow-right-line"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end card-body -->
        </div>
        <!-- end card -->
    </div>
    <!--end col-->
</div>
<!-- end row -->

@endsection
@section('script')
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
