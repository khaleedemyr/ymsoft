@extends('layouts.master')
@section('title')
@lang('translation.pricing')
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Pages @endslot
@slot('title') Pricing @endslot
@endcomponent


<div class="row justify-content-center mt-2">
    <div class="col-xl-4">
        <div class="text-center mb-4 pb-2">
            <h4 class="fw-semibold fs-4xl text-capitalize">Our plans for your Business</h4>
            <p class="text-muted mb-4 fs-base">Simple pricing. No hidden fees. Advanced features for you business.</p>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row justify-content-center">
    <div class="col-xxl-7 col-lg-12 col-md-6">
        <div class="card bg-body-secondary border-0">
            <div class="card-body p-4 m-3">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <div class="d-flex gap-3 h-100">
                            <div class="avatar-sm flex-shrink-0">
                                <div class="avatar-title bg-secondary-subtle rounded text-secondary fs-4">
                                    <i class="ti ti-plane-tilt"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 d-flex flex-column h-100">
                                <h5>Professional</h5>
                                <p class="text-muted">For users who want to do more.</p>
                                <h3 class="fw-bold mt-auto mb-0">$69.99 <span class="fw-normal text-muted fs-sm">/ Monthly</span></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <p class="text-muted fs-md fw-medium mb-2">Featured Include:</p>
                        <ul class="list-unstyled vstack gap-2 mb-0">
                            <li><i class="ti ti-progress-check me-1 text-success fs-lg"></i> <b>Unlimited</b> Projects</li>
                            <li><i class="ti ti-progress-check me-1 text-success fs-lg"></i> Share with <b>5</b> team members</li>
                            <li><i class="ti ti-progress-check me-1 text-success fs-lg"></i> Sync across devices</li>
                            <li><i class="ti ti-progress-check me-1 text-success fs-lg"></i> Scalable Bandwidth</li>
                            <li><i class="ti ti-progress-check me-1 text-success fs-lg"></i> <b>5</b> FTP Login</li>
                        </ul>
                    </div>
                    <div class="col-lg-3">
                        <button type="button" class="btn btn-info w-100">Change Plan</button>
                        <p class="fs-sm text-muted mt-1 mb-0">Use promo code: #VIXON20</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-7 col-lg-12 col-md-6">
        <div class="card bg-body-secondary border-0 ribbon-box ribbon-fill">
            <div class="ribbon ribbon-danger">Hot</div>
            <div class="card-body p-4 m-3">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <div class="d-flex gap-3 h-100">
                            <div class="avatar-sm flex-shrink-0">
                                <div class="avatar-title bg-warning-subtle rounded text-warning fs-4">
                                    <i class="ti ti-award"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 d-flex flex-column h-100">
                                <h5>Enterprise</h5>
                                <p class="text-muted">Your entire team in one place</p>
                                <h3 class="fw-bold mt-auto mb-0">$79.99 <span class="fw-normal text-muted fs-sm">/ Monthly</span></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <p class="text-muted fs-md fw-medium mb-2">Featured Include:</p>
                        <ul class="list-unstyled vstack gap-2 mb-0">
                            <li><i class="ti ti-progress-check me-1 text-success fs-lg"></i> Everything in Pro Plan</li>
                            <li><i class="ti ti-progress-check me-1 text-success fs-lg"></i> <b>Unlimited</b> Projects</li>
                            <li><i class="ti ti-progress-check me-1 text-success fs-lg"></i> <b>30</b> day version history</li>
                            <li><i class="ti ti-progress-check me-1 text-success fs-lg"></i> Share with <b>10</b> team members</li>
                            <li><i class="ti ti-progress-check me-1 text-success fs-lg"></i> <b>8</b> FTP Login</li>
                        </ul>
                    </div>
                    <div class="col-lg-3">
                        <button type="button" class="btn btn-danger w-100" disabled>Current Plan</button>
                        <p class="fs-sm text-muted mt-1 mb-0">Use promo code: #VIXON20</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-7 col-lg-12 col-md-6">
        <div class="card bg-body-secondary border-0">
            <div class="card-body p-4 m-3">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <div class="d-flex gap-3 h-100">
                            <div class="avatar-sm flex-shrink-0">
                                <div class="avatar-title bg-secondary-subtle rounded text-secondary fs-4">
                                    <i class="ti ti-plane-tilt"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 d-flex flex-column h-100">
                                <h5>Unlimited</h5>
                                <p class="text-muted">Run your company on your teams</p>
                                <h3 class="fw-bold mt-auto mb-0">$99.99 <span class="fw-normal text-muted fs-sm">/ Monthly</span></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <p class="text-muted fs-md fw-medium mb-2">Featured Include:</p>
                        <ul class="list-unstyled vstack gap-2 mb-0">
                            <li><i class="ti ti-progress-check me-1 text-success fs-lg"></i> Everything in Enterprise Plan</li>
                            <li><i class="ti ti-progress-check me-1 text-success fs-lg"></i> <b>Unlimited</b> Projects</li>
                            <li><i class="ti ti-progress-check me-1 text-success fs-lg"></i> Share with <b>Unlimited</b> team members</li>
                            <li><i class="ti ti-progress-check me-1 text-success fs-lg"></i> <b>Unlimited</b> Storage</li>
                            <li><i class="ti ti-progress-check me-1 text-success fs-lg"></i> Admin tools</li>
                        </ul>
                    </div>
                    <div class="col-lg-3">
                        <button type="button" class="btn btn-info w-100">Change Plan</button>
                        <p class="fs-sm text-muted mt-1 mb-0">Use promo code: #VIXON20</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end row-->

<div class="row justify-content-center mt-5">
    <div class="col-lg-5">
        <div class="text-center mb-5">
            <h3 class="fw-semibold fs-4xl text-capitalize">Our plans for your Business</h3>
            <p class="text-muted fs-base">Simple pricing. No hidden fees. Advanced features for you business.</p>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-xxl-3 col-lg-6">
        <div class="card card-animate border-0 border-top border-2 border-success rounded-0 rounded-bottom">
            <div class="card-body m-2 p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <h5 class="mb-0 fw-semibold">Starter</h5>
                    </div>
                    <div class="ms-auto">
                        <h2 class="mb-0">$19 <small class="fs-sm text-muted">/Month</small></h2>
                    </div>
                </div>

                <p class="text-muted">The perfect way to get started and get used to our tools.</p>
                <ul class="list-unstyled vstack gap-3">
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>3</b> Projects
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>299</b> Customers
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                Scalable Bandwidth
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>5</b> FTP Login
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="mt-3 pt-2">
                    <a href="javascript:void(0);" class="btn btn-danger disabled w-100">Your Current Plan</a>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->

    <div class="col-xxl-3 col-lg-6">
        <div class="card card-animate border-0 border-top border-2 border-secondary rounded-0 rounded-bottom">
            <div class="card-body m-2 p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <h5 class="mb-0 fw-semibold">Professional</h5>
                    </div>
                    <div class="ms-auto">
                        <h2 class="mb-0">$29 <small class="fs-sm text-muted">/Month</small></h2>
                    </div>
                </div>
                <p class="text-muted">Excellent for scaling teams to build culture. Special plan for professional business.</p>
                <ul class="list-unstyled vstack gap-3">
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>8</b> Projects
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>449</b> Customers
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                Scalable Bandwidth
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>7</b> FTP Login
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>24/7</b> Support
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="mt-3 pt-2">
                    <a href="javascript:void(0);" class="btn btn-info w-100">Change Plan</a>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->

    <div class="col-xxl-3 col-lg-6">
        <div class="card card-animate border-0 border-top border-2 border-primary rounded-0 rounded-bottom">
            <div class="card-body m-2 p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <h5 class="mb-0 fw-semibold">Enterprise</h5>
                    </div>
                    <div class="ms-auto">
                        <h2 class="mb-0">$39 <small class="fs-sm text-muted">/Month</small></h2>
                    </div>
                </div>
                <p class="text-muted">This plan is for those who have a team already and running a large business.</p>
                <ul class="list-unstyled vstack gap-3">
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>15</b> Projects
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>Unlimited</b> Customers
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                Scalable Bandwidth
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>12</b> FTP Login
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>24/7</b> Support
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>35GB</b> Storage
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="mt-3 pt-2">
                    <a href="javascript:void(0);" class="btn btn-info w-100">Change Plan</a>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->

    <div class="col-xxl-3 col-lg-6">
        <div class="card card-animate border-0 border-top border-2 border-info rounded-0 rounded-bottom">
            <div class="p-1 bg-info bg-opacity-10 text-center text-info fw-semibold"><span><i class="ri-flashlight-fill align-bottom me-1"></i> Most Popular Choice</span></div>
            <div class="card-body m-2 p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <h5 class="mb-0 fw-semibold">Unlimited</h5>
                    </div>
                    <div class="ms-auto">
                        <h2 class="mb-0">$49 <small class="fs-sm text-muted">/Month</small></h2>
                    </div>
                </div>
                <p class="text-muted">For most businesses that want to optimize web queries.</p>
                <ul class="list-unstyled vstack gap-3">
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>Unlimited</b> Projects
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>Unlimited</b> Customers
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                Scalable Bandwidth
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>Unlimited</b> FTP Login
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>24/7</b> Support
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <b>Unlimited</b> Storage
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex">
                            <div class="flex-shrink-0 text-success me-1">
                                <i class="ri-checkbox-circle-fill fs-base align-middle"></i>
                            </div>
                            <div class="flex-grow-1">
                                Domain
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="mt-3 pt-2">
                    <a href="javascript:void(0);" class="btn btn-info w-100">Change Plan</a>
                </div>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->
@endsection
@section('script')
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
