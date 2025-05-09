@extends('layouts.master')
@section('title') @lang('translation.badges') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Base UI @endslot
@slot('title') Badges @endslot
@endcomponent

<div class="row">
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Default Badges</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Use the <code>badge</code> class to set a default badge.</p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-primary">Primary</span>
                    <span class="badge bg-secondary">Secondary</span>
                    <span class="badge bg-success">Success</span>
                    <span class="badge bg-info">Info</span>
                    <span class="badge bg-warning">Warning</span>
                    <span class="badge bg-danger">Danger</span>
                    <span class="badge bg-dark">Dark</span>
                    <span class="badge bg-light text-body">Light</span>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>

    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Subtle Badges </h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Use the <code>badge-subtle-</code> class with the
                    below-mentioned variation to create a subtle badge. </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-primary-subtle text-primary">Primary</span>
                    <span class="badge bg-secondary-subtle text-secondary">Secondary</span>
                    <span class="badge bg-success-subtle text-success">Success</span>
                    <span class="badge bg-info-subtle text-info">Info</span>
                    <span class="badge bg-warning-subtle text-warning">Warning</span>
                    <span class="badge bg-danger-subtle text-danger">Danger</span>
                    <span class="badge bg-dark-subtle text-body">Dark</span>
                    <span class="badge bg-light-subtle text-body">Light</span>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
</div>

<div class="row">
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Outline Badges </h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Use the <code>bg-body-secondary</code>, <code>border</code>
                    <code>border-*</code>, <code>text-*</code> class with the below-mentioned
                    variation to create a badge with the outline.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-body-secondary border border-primary text-primary">Primary</span>
                    <span class="badge bg-body-secondary border border-secondary text-secondary">Secondary</span>
                    <span class="badge bg-body-secondary border border-success text-success">Success</span>
                    <span class="badge bg-body-secondary border border-info text-info">Info</span>
                    <span class="badge bg-body-secondary border border-warning text-warning">Warning</span>
                    <span class="badge bg-body-secondary border border-danger text-danger">Danger</span>
                    <span class="badge bg-body-secondary border border-dark text-body">Dark</span>
                    <span class="badge bg-body-secondary border border-light text-body">Light</span>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>

    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Rounded Pill Badges </h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Use the <code>rounded-pill</code> class to make badges more
                    rounded with a larger border-radius.</p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge rounded-pill bg-primary">Primary</span>
                    <span class="badge rounded-pill bg-secondary">Secondary</span>
                    <span class="badge rounded-pill bg-success">Success</span>
                    <span class="badge rounded-pill bg-info">Info</span>
                    <span class="badge rounded-pill bg-warning">Warning</span>
                    <span class="badge rounded-pill bg-danger">Danger</span>
                    <span class="badge rounded-pill bg-dark">Dark</span>
                    <span class="badge rounded-pill bg-light">Light</span>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
</div>

<div class="row">
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Rounded Pill Badges with subtle effect </h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Use the <code>rounded-pill badge-subtle-</code> class with the
                    below-mentioned variation to create a badge more rounded with a soft background.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge rounded-pill bg-primary-subtle text-primary">Primary</span>
                    <span class="badge rounded-pill bg-secondary-subtle text-secondary">Secondary</span>
                    <span class="badge rounded-pill bg-success-subtle text-success">Success</span>
                    <span class="badge rounded-pill bg-info-subtle text-info">Info</span>
                    <span class="badge rounded-pill bg-warning-subtle text-warning">Warning</span>
                    <span class="badge rounded-pill bg-danger-subtle text-danger">Danger</span>
                    <span class="badge rounded-pill bg-dark-subtle text-body">Dark</span>
                    <span class="badge rounded-pill bg-light-subtle text-body">Light</span>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>

    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Subtle Border Badges</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">
                    Use the <code>badge-border</code> and <code>badge-subtle-</code> with below
                    mentioned modifier classes to make badges with border & subtle background.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-primary-subtle text-primary badge-border">Primary</span>
                    <span class="badge bg-secondary-subtle text-secondary badge-border">Secondary</span>
                    <span class="badge bg-success-subtle text-success badge-border">Success</span>
                    <span class="badge bg-danger-subtle text-danger badge-border">Danger</span>
                    <span class="badge bg-warning-subtle text-warning badge-border">Warning</span>
                    <span class="badge bg-info-subtle text-info badge-border">Info</span>
                    <span class="badge bg-dark-subtle text-body badge-border">Dark</span>
                    <span class="badge bg-light-subtle text-body badge-border">Light</span>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>


</div>

<div class="row">
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Outline Pill Badges </h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">
                    Use the <code>rounded-pill</code> class with the below-mentioned
                    variation to create a outline Pill badge.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-transparent rounded-pill border border-primary text-primary">Primary</span>
                    <span class="badge bg-transparent rounded-pill border border-secondary text-secondary">Secondary</span>
                    <span class="badge bg-transparent rounded-pill border border-success text-success">Success</span>
                    <span class="badge bg-transparent rounded-pill border border-info text-info">Info</span>
                    <span class="badge bg-transparent rounded-pill border border-warning text-warning">Warning</span>
                    <span class="badge bg-transparent rounded-pill border border-danger text-danger">Danger</span>
                    <span class="badge bg-transparent rounded-pill border border-dark text-body">Dark</span>
                    <span class="badge bg-transparent rounded-pill border border-light text-body">Light</span>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>

    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Label Badges </h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">
                    Use the <code>badge-label</code> class to create a badge with the label.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge badge-label bg-primary"><i class="ti ti-point-filled"></i>
                        Primary</span>
                    <span class="badge badge-label bg-secondary"><i class="ti ti-point-filled"></i> Secondary</span>
                    <span class="badge badge-label bg-success"><i class="ti ti-point-filled"></i>
                        Success</span>
                    <span class="badge badge-label bg-danger"><i class="ti ti-point-filled"></i>
                        Danger</span>
                    <span class="badge badge-label bg-warning"><i class="ti ti-point-filled"></i>
                        Warning</span>
                    <span class="badge badge-label bg-info"><i class="ti ti-point-filled"></i>
                        Info</span>
                    <span class="badge badge-label bg-dark"><i class="ti ti-point-filled"></i>
                        Dark</span>
                    <span class="badge badge-label bg-light"><i class="ti ti-point-filled"></i>
                        Light</span>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
</div>

<div class="row">
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Gradient Badges</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">
                    Use the <code>badge-gradient-*</code> class to create a gradient styled badge.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge badge-gradient-primary">Primary</span>
                    <span class="badge badge-gradient-secondary">Secondary</span>
                    <span class="badge badge-gradient-success">Success</span>
                    <span class="badge badge-gradient-danger">Danger</span>
                    <span class="badge badge-gradient-warning">Warning</span>
                    <span class="badge badge-gradient-info">Info</span>
                    <span class="badge badge-gradient-dark">Dark</span>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Button Position Badges</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use the below utilities to modify a badge and position it in
                    the corner of a link or button.</p>
                <div class="d-flex flex-wrap gap-3">
                    <button type="button" class="btn btn-primary position-relative">
                        Mails <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">+99
                            <span class="visually-hidden">unread messages</span></span>
                    </button>

                    <button type="button" class="btn btn-light position-relative">
                        Alerts <span class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-danger p-1"><span class="visually-hidden">unread messages</span></span>
                    </button>

                    <button type="button" class="btn btn-primary position-relative btn-icon rounded">
                        <i class="ti ti-mail"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-danger p-1"><span class="visually-hidden">unread messages</span></span>
                    </button>

                    <button type="button" class="btn btn-light position-relative btn-icon rounded-circle">
                        <i class="ti ti-bell"></i>
                    </button>

                    <button type="button" class="btn btn-light position-relative btn-icon rounded-circle">
                        <i class="ti ti-adjustments-alt"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-success p-1"><span class="visually-hidden">unread messages</span></span>
                    </button>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>

    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Badges With Button</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Badges can be used as part of buttons to provide a counter.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary">
                        Notifications <span class="badge bg-success ms-1">4</span>
                    </button>
                    <button type="button" class="btn btn-success">
                        Messages <span class="badge bg-danger ms-1">2</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary">
                        Draft <span class="badge bg-success ms-1">2</span>
                    </button>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>

    <div class="col-xxl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Badges with Heading</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Example of the badge used in the HTML Heading.</p>
                <h1>Example heading <span class="badge bg-secondary align-middle">New</span></h1>
                <h2>Example heading <span class="badge bg-secondary align-middle">New</span></h2>
                <h3>Example heading <span class="badge bg-secondary align-middle">New</span></h3>
                <h4>Example heading <span class="badge bg-secondary align-middle">New</span></h4>
                <h5>Example heading <span class="badge bg-secondary align-middle">New</span></h5>
                <h6 class="mb-0">Example heading <span class="badge bg-secondary align-middle">New</span></h6>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

@endsection
@section('script')
<script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
