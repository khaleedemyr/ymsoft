@extends('layouts.master')
@section('title') @lang('translation.buttons') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Base UI @endslot
@slot('title') Buttons @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Default Buttons</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use the<code> btn</code> class to show the default button
                    style.</p>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary">Primary</button>
                    <button type="button" class="btn btn-secondary">Secondary</button>
                    <button type="button" class="btn btn-success">Success</button>
                    <button type="button" class="btn btn-info">Info</button>
                    <button type="button" class="btn btn-warning">Warning</button>
                    <button type="button" class="btn btn-danger">Danger</button>
                    <button type="button" class="btn btn-dark">Dark</button>
                    <button type="button" class="btn btn-link">Link</button>
                    <button type="button" class="btn btn-light">Light</button>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Outline Buttons</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>btn-outline-</code> class with the below-mentioned
                    variation to create a button with the outline.</p>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-primary">Primary</button>
                    <button type="button" class="btn btn-outline-secondary">Secondary</button>
                    <button type="button" class="btn btn-outline-success">Success</button>
                    <button type="button" class="btn btn-outline-info">Info</button>
                    <button type="button" class="btn btn-outline-warning">Warning</button>
                    <button type="button" class="btn btn-outline-danger">Danger</button>
                    <button type="button" class="btn btn-outline-dark text-body">Dark</button>
                    <button type="button" class="btn btn-outline-light">Light</button>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
</div>

<div class="row">
    <div class="col-lg-21">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Rounded Buttons</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use the <code>rounded-pill </code>class to make a rounded
                    button.</p>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn rounded-pill btn-primary">Primary</button>
                    <button type="button" class="btn rounded-pill btn-secondary">Secondary</button>
                    <button type="button" class="btn rounded-pill btn-success">Success</button>
                    <button type="button" class="btn rounded-pill btn-info">Info</button>
                    <button type="button" class="btn rounded-pill btn-warning">Warning</button>
                    <button type="button" class="btn rounded-pill btn-danger">Danger</button>
                    <button type="button" class="btn rounded-pill btn-dark">Dark</button>
                    <button type="button" class="btn rounded-pill btn-light">Light</button>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Subtle Buttons</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>btn-subtle-</code> class with the below-mentioned
                    variation to create a button with the subtle background.</p>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-subtle-primary">Primary</button>
                    <button type="button" class="btn btn-subtle-secondary">Secondary</button>
                    <button type="button" class="btn btn-subtle-success">Success</button>
                    <button type="button" class="btn btn-subtle-info">Info</button>
                    <button type="button" class="btn btn-subtle-warning">Warning</button>
                    <button type="button" class="btn btn-subtle-danger">Danger</button>
                    <button type="button" class="btn btn-subtle-dark text-body">Dark</button>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Ghost Buttons</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>btn-ghost-</code> class with the below-mentioned
                    variation to create a button with the transparent background.</p>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-ghost-primary">Primary</button>
                    <button type="button" class="btn btn-ghost-secondary">Secondary</button>
                    <button type="button" class="btn btn-ghost-success">Success</button>
                    <button type="button" class="btn btn-ghost-info">Info</button>
                    <button type="button" class="btn btn-ghost-warning">Warning</button>
                    <button type="button" class="btn btn-ghost-danger">Danger</button>
                    <button type="button" class="btn btn-ghost-dark text-body">Dark</button>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Gradient Buttons</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>bg-gradient </code>class to create a gradient
                    button.</p>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary bg-gradient">Primary</button>
                    <button type="button" class="btn btn-secondary bg-gradient">Secondary</button>
                    <button type="button" class="btn btn-success bg-gradient">Success</button>
                    <button type="button" class="btn btn-info bg-gradient">Info</button>
                    <button type="button" class="btn btn-warning bg-gradient">Warning</button>
                    <button type="button" class="btn btn-danger bg-gradient">Danger</button>
                    <button type="button" class="btn btn-dark bg-gradient">Dark</button>
                    <button type="button" class="btn btn-light bg-gradient">Light</button>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Buttons with Label</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>btn-label </code>class to create a button with the
                    label.</p>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                            <a href="javascript:void(0);" class="btn btn-primary btn-label">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="ri-user-smile-line label-icon align-middle fs-lg me-2"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        Primary
                                    </div>
                                </div>
                            </a>
                            <button type="button" class="btn btn-success btn-label"><i class="ri-check-double-line label-icon align-middle fs-lg me-2"></i>
                                Success</button>
                            <button type="button" class="btn btn-warning btn-label"><i class="ri-error-warning-line label-icon align-middle fs-lg me-2 "></i>
                                Warning</button>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-lg-4">
                        <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                            <button type="button" class="btn btn-primary btn-label rounded-pill"><i class="ri-user-smile-line label-icon align-middle rounded-pill fs-lg me-2"></i>
                                Primary</button>
                            <button type="button" class="btn btn-success btn-label rounded-pill"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-lg me-2"></i>
                                Success</button>
                            <button type="button" class="btn btn-warning btn-label rounded-pill"><i class="ri-error-warning-line label-icon align-middle rounded-pill fs-lg me-2 "></i>
                                Warning</button>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-lg-4">
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-primary btn-label right"><i class="ri-user-smile-line label-icon align-middle fs-lg ms-2"></i>
                                Primary</button>
                            <button type="button" class="btn btn-success btn-label right rounded-pill"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-lg ms-2"></i>
                                Success</button>
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Load More Buttons</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Example of loading buttons.</p>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="hstack flex-wrap gap-2 mb-3 mb-lg-0">
                            <button class="btn btn-outline-primary btn-load">
                                <span class="d-flex align-items-center">
                                    <span class="spinner-border flex-shrink-0" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </span>
                                    <span class="flex-grow-1 ms-2">
                                        Loading...
                                    </span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-success btn-load">
                                <span class="d-flex align-items-center">
                                    <span class="spinner-border flex-shrink-0" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </span>
                                    <span class="flex-grow-1 ms-2">
                                        Loading...
                                    </span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-load">
                                <span class="d-flex align-items-center">
                                    <span class="spinner-grow flex-shrink-0" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </span>
                                    <span class="flex-grow-1 ms-2">
                                        Loading...
                                    </span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-danger btn-load">
                                <span class="d-flex align-items-center">
                                    <span class="spinner-grow flex-shrink-0" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </span>
                                    <span class="flex-grow-1 ms-2">
                                        Loading...
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-lg-6">
                        <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                            <button class="btn btn-outline-primary btn-load">
                                <span class="d-flex align-items-center">
                                    <span class="flex-grow-1 me-2">
                                        Loading...
                                    </span>
                                    <span class="spinner-border flex-shrink-0" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-success btn-load">
                                <span class="d-flex align-items-center">
                                    <span class="flex-grow-1 me-2">
                                        Loading...
                                    </span>
                                    <span class="spinner-border flex-shrink-0" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-load">
                                <span class="d-flex align-items-center">
                                    <span class="flex-grow-1 me-2">
                                        Loading...
                                    </span>
                                    <span class="spinner-grow flex-shrink-0" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-info btn-load">
                                <span class="d-flex align-items-center">
                                    <span class="flex-grow-1 me-2">
                                        Loading...
                                    </span>
                                    <span class="spinner-grow flex-shrink-0" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Custom Toggle Buttons</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Example of toggle buttons.</p>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <button type="button" class="btn btn-primary custom-toggle active" data-bs-toggle="button">
                                <span class="icon-on"><i class="ri-alarm-line align-bottom"></i>
                                    Active</span>
                                <span class="icon-off">Unactive</span>
                            </button>
                            <button type="button" class="btn btn-secondary custom-toggle active" data-bs-toggle="button">
                                <span class="icon-on"><i class="ri-user-add-line align-bottom me-1"></i>
                                    Connect</span>
                                <span class="icon-off"><i class="ri-check-fill align-bottom me-1"></i>
                                    Unconnect</span>
                            </button>
                            <button type="button" class="btn btn-success custom-toggle" data-bs-toggle="button">
                                <span class="icon-on"><i class="ri-thumb-up-line align-bottom me-1"></i> Yes</span>
                                <span class="icon-off"><i class="ri-thumb-down-line align-bottom me-1"></i> No</span>
                            </button>
                            <button type="button" class="btn btn-warning custom-toggle active" data-bs-toggle="button">
                                <span class="icon-on"><i class="ri-add-line align-bottom me-1"></i>
                                    Follow</span>
                                <span class="icon-off"><i class="ri-user-unfollow-line align-bottom me-1"></i>
                                    Unfollow</span>
                            </button>
                            <button type="button" class="btn btn-danger custom-toggle" data-bs-toggle="button">
                                <span class="icon-on">On</span>
                                <span class="icon-off">Off</span>
                            </button>
                            <button type="button" class="btn btn-dark custom-toggle" data-bs-toggle="button">
                                <span class="icon-on"><i class="ri-bookmark-line align-bottom me-1"></i>
                                    Bookmark</span>
                                <span class="icon-off"><i class="ri-bookmark-3-fill align-bottom me-1"></i>
                                    Unbookmark</span>
                            </button>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-lg-6">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <button type="button" class="btn btn-outline-primary custom-toggle" data-bs-toggle="button">
                                <span class="icon-on">Active</span>
                                <span class="icon-off">Unactive</span>
                            </button>
                            <button type="button" class="btn btn-outline-secondary custom-toggle" data-bs-toggle="button">
                                <span class="icon-on"><i class="ri-add-line align-bottom me-1"></i>
                                    Follow</span>
                                <span class="icon-off"><i class="ri-user-unfollow-line align-bottom me-1"></i>
                                    Unfollow</span>
                            </button>
                            <button type="button" class="btn btn-outline-success custom-toggle active" data-bs-toggle="button">
                                <span class="icon-on">On</span>
                                <span class="icon-off">Off</span>
                            </button>
                            <button type="button" class="btn btn-subtle-warning custom-toggle active" data-bs-toggle="button">
                                <span class="icon-on">Follow</span>
                                <span class="icon-off">Unfollow</span>
                            </button>
                            <button type="button" class="btn btn-subtle-danger custom-toggle" data-bs-toggle="button">
                                <span class="icon-on">On</span>
                                <span class="icon-off">Off</span>
                            </button>
                            <button type="button" class="btn btn-dark custom-toggle active" data-bs-toggle="button">
                                <span class="icon-on"><i class="ri-bookmark-line align-bottom"></i></span>
                                <span class="icon-off"><i class="ri-bookmark-3-fill align-bottom"></i></span>
                            </button>
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Base class</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Bootstrap has a base <code>.btn</code> class that sets up
                    basic styles such as padding and content alignment. By default,
                    <code>.btn</code> controls have a transparent border and background color, and
                    lack any explicit focus and hover styles.
                </p>
                <button type="button" class="btn">Base class</button>
            </div><!-- end card-body -->
            <div class="card-body bg-light border-bottom border-top bg-opacity-25">
                <h5 class="fs-xs text-muted mb-0">HTML Preview</h5>
            </div>
            <div class="card-body">
                <pre class="language-markup"><code>&lt;button type=&quot;button&quot; class=&quot;btn&quot;&gt;Base class&lt;/button&gt;</code></pre>
            </div>
        </div><!-- end card -->
    </div>
    <!--end col-->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Custom sizing with CSS variables</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">You can even roll your own custom sizing with CSS variables:
                </p>
                <button type="button" class="btn btn-primary" style="--tb-btn-padding-y: .25rem; --tb-btn-padding-x: .5rem; --tb-btn-font-size: .75rem;">
                    Custom button
                </button>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Buttons Sizes</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>btn-lg</code> class to create a large size button
                    and <code>btn-sm</code> class to create a small size button.</p>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <!-- Large Button -->
                    <button type="button" class="btn btn-primary btn-lg">Large button</button>
                    <button type="button" class="btn btn-light btn-lg">Large button</button>

                    <!-- Small Button -->
                    <button type="button" class="btn btn-primary btn-sm">Small button</button>
                    <button type="button" class="btn btn-light btn-sm">Small button</button>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Buttons Width</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>w-xs,w-sm,w-md,w-lg</code> class to make different
                    sized buttons respectively.
                    <p>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-primary w-xs">Xs</button>
                            <button type="button" class="btn btn-danger w-sm">Small</button>
                            <button type="button" class="btn btn-warning w-md">Medium</button>
                            <button type="button" class="btn btn-success w-lg">Large</button>
                        </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center">
                <h4 class="card-title mb-0">Buttons Tag</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>btn</code> class with different HTML elements.
                    (though some browsers may apply a slightly different rendering)
                    <p>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-primary" href="#" role="button">Link</a>
                            <button class="btn btn-success" type="submit">Button</button>
                            <input class="btn btn-info" type="button" value="Input">
                            <input class="btn btn-danger" type="submit" value="Submit">
                            <input class="btn btn-warning" type="reset" value="Reset">
                        </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Buttons Toggle Status</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">
                    Use <code>data-bs-toggle="button"</code> to toggle a button’s active state.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="button" aria-pressed="false">
                        Single toggle
                    </button>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Buttons Grid</h4>
            </div><!-- end card header -->
            <div class="card-body">

                <p class="text-muted">
                    Use <code>d-grid</code> class to create a full-width block button.
                </p>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" type="button">Button</button>
                    <button class="btn btn-primary" type="button">Button</button>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Checkbox & Radio Buttons</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">
                    Combine button-like <code>checkbox and radio</code> <a href="https://getbootstrap.com/docs/5.1/forms/checks-radios/">toggle
                        buttons</a> into a seamless looking button group.
                </p>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                        <input type="checkbox" class="btn-check" id="btncheck1" checked="">
                        <label class="btn btn-primary mb-0" for="btncheck1">Checkbox 1</label>

                        <input type="checkbox" class="btn-check" id="btncheck2">
                        <label class="btn btn-primary mb-0" for="btncheck2">Checkbox 2</label>

                        <input type="checkbox" class="btn-check" id="btncheck3">
                        <label class="btn btn-primary mb-0" for="btncheck3">Checkbox 3</label>
                    </div>

                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" name="btnradio" id="btnradio1" checked="">
                        <label class="btn btn-outline-secondary mb-0" for="btnradio1">Radio
                            1</label>

                        <input type="radio" class="btn-check" name="btnradio" id="btnradio2">
                        <label class="btn btn-outline-secondary mb-0" for="btnradio2">Radio
                            2</label>

                        <input type="radio" class="btn-check" name="btnradio" id="btnradio3">
                        <label class="btn btn-outline-secondary mb-0" for="btnradio3">Radio
                            3</label>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Buttons Group</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use the <code>btn-group </code> class in the parent class to
                    wrap a series of buttons.</p>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-primary">Left</button>
                            <button type="button" class="btn btn-primary">Middle</button>
                            <button type="button" class="btn btn-primary">Right</button>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-sm-6">
                        <div class="btn-group mt-4 mt-sm-0" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-light btn-icon"><i class="ri-align-right"></i></button>
                            <button type="button" class="btn btn-light btn-icon"><i class="ri-align-center"></i></button>
                            <button type="button" class="btn btn-light btn-icon"><i class="ri-align-left"></i></button>
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Icon Buttons</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>btn-icon</code> class to wrap icon in button</p>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="hstack gap-2 ">
                            <button type="button" class="btn btn-primary btn-icon"><i class="ri-map-pin-line"></i></button>
                            <button type="button" class="btn btn-danger btn-icon"><i class="ri-delete-bin-5-line"></i></button>
                            <button type="button" class="btn btn-success btn-icon"><i class="ri-check-double-line"></i></button>
                            <button type="button" class="btn btn-light btn-icon"><i class="ri-brush-2-fill"></i></button>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-sm-6">
                        <div class="hstack gap-2 mt-4 mt-sm-0">
                            <button type="button" class="btn btn-outline-primary btn-icon"><i class="ri-24-hours-fill"></i></button>
                            <button type="button" class="btn btn-outline-danger btn-icon"><i class="ri-customer-service-2-line"></i></button>
                            <button type="button" class="btn btn-outline-success btn-icon"><i class="ri-mail-send-line"></i></button>
                            <button type="button" class="btn btn-outline-warning btn-icon"><i class="ri-menu-2-line"></i></button>
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Buttons Toolbar</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>btn-toolbar</code> class to combine sets of button
                    groups into more complex components.</p>
                <div class="d-flex flex-wrap gap-3">
                    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                        <div class="btn-group me-2" role="group" aria-label="First group">
                            <button type="button" class="btn btn-light">1</button>
                            <button type="button" class="btn btn-light">2</button>
                            <button type="button" class="btn btn-light">3</button>
                            <button type="button" class="btn btn-light">4</button>
                        </div>
                        <div class="btn-group me-2" role="group" aria-label="Second group">
                            <button type="button" class="btn btn-light">5</button>
                            <button type="button" class="btn btn-light">6</button>
                            <button type="button" class="btn btn-light">7</button>
                        </div>
                        <div class="btn-group" role="group" aria-label="Third group">
                            <button type="button" class="btn btn-light">8</button>
                        </div>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Button Group Sizing</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>btn-group-</code> class with the below-mentioned
                    variation to set the different sizes of button groups.</p>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="btn-group btn-group-lg" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-primary">Left</button>
                        <button type="button" class="btn btn-primary">Middle</button>
                        <button type="button" class="btn btn-primary">Right</button>
                    </div>
                    <div class="btn-group mt-2" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-light">Left</button>
                        <button type="button" class="btn btn-light">Middle</button>
                        <button type="button" class="btn btn-light">Right</button>
                    </div>
                    <div class="btn-group btn-group-sm mt-2" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-secondary">Left</button>
                        <button type="button" class="btn btn-secondary">Middle</button>
                        <button type="button" class="btn btn-secondary">Right</button>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col+-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Vertical Variation</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Make a set of buttons appear <code>vertically</code> stacked
                    .Split button dropdowns are not supported here.</p>
                <div class="row gy-3">
                    <div class="col-auto">
                        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <button type="button" class="btn btn-primary">1</button>
                            <button type="button" class="btn btn-primary">2</button>
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Dropdown
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                                    <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-auto">
                        <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
                            <button type="button" class="btn btn-light">Button</button>
                            <div class="btn-group" role="group">
                                <button id="btnGroupVerticalDrop1" type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Dropdown
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupVerticalDrop1">
                                    <a class="dropdown-item" href="#">Dropdown link</a>
                                    <a class="dropdown-item" href="#">Dropdown link</a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-light">Button</button>
                            <button type="button" class="btn btn-light">Button</button>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group-vertical" role="group" aria-label="Vertical radio toggle button group">
                            <input type="radio" class="btn-check" name="vbtn" id="vbtn-radio1" checked="">
                            <label class="btn btn-outline-secondary" for="vbtn-radio1">Radio
                                1</label>
                            <input type="radio" class="btn-check" name="vbtn" id="vbtn-radio2">
                            <label class="btn btn-outline-secondary" for="vbtn-radio2">Radio
                                2</label>
                            <input type="radio" class="btn-check" name="vbtn" id="vbtn-radio3">
                            <label class="btn btn-outline-secondary" for="vbtn-radio3">Radio
                                3</label>
                        </div>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Focus Ring</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Click directly on the link below to see the focus ring in
                    action, or into the example below and then press <kbd>Tab</kbd>.</p>
                <div class="d-flex gap-3">
                    <a href="#!" class="d-inline-flex focus-ring py-1 px-2 text-decoration-none border rounded-2">
                        Focus ring
                    </a>
                    <!--custom focus ring color-->
                    <a href="#!" class="d-inline-flex focus-ring py-1 px-2 text-decoration-none border rounded-2" style="--tb-focus-ring-color: rgba(118, 93, 255, .25)">
                        Custom focus ring
                    </a>
                    <!---custom blurry offset focus ring-->
                    <a href="#!" class="d-inline-flex focus-ring py-1 px-2 text-decoration-none border rounded-2" style="--tb-focus-ring-x: 10px; --tb-focus-ring-y: 10px; --tb-focus-ring-blur: 4px">
                        Blurry offset focus ring
                    </a>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Focus Ring Utilities</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">In addition to <code>.focus-ring</code>, we have several
                    <code>.focus-ring-*</code> utilities to modify the helper class defaults. Modify
                    the color with any of our <a href="ui-colors.html">theme colors</a>. Note that
                    the light and dark variants may not be visible on all background colors given
                    current color mode support.
                </p>
                <div class="row">
                    <div class="col-lg-6">
                        <p><a href="#!" class="d-inline-flex focus-ring focus-ring-primary py-1 px-2 text-decoration-none border rounded-2">Primary
                                focus</a></p>
                        <p><a href="#!" class="d-inline-flex focus-ring focus-ring-secondary py-1 px-2 text-decoration-none border rounded-2">Secondary
                                focus</a></p>
                        <p><a href="#!" class="d-inline-flex focus-ring focus-ring-success py-1 px-2 text-decoration-none border rounded-2">Success
                                focus</a></p>
                        <p class="mb-lg-0"><a href="#!" class="d-inline-flex focus-ring focus-ring-danger py-1 px-2 text-decoration-none border rounded-2">Danger
                                focus</a></p>
                    </div>
                    <!--end col-->
                    <div class="col-lg-6">
                        <p><a href="#!" class="d-inline-flex focus-ring focus-ring-warning py-1 px-2 text-decoration-none border rounded-2">Warning
                                focus</a></p>
                        <p><a href="#!" class="d-inline-flex focus-ring focus-ring-info py-1 px-2 text-decoration-none border rounded-2">Info
                                focus</a></p>
                        <p><a href="#!" class="d-inline-flex focus-ring focus-ring-light py-1 px-2 text-decoration-none border rounded-2">Light
                                focus</a></p>
                        <p class="mb-0"><a href="#!" class="d-inline-flex focus-ring focus-ring-dark py-1 px-2 text-decoration-none border rounded-2">Dark
                                focus</a></p>
                    </div>
                    <!--end col-->
                </div><!-- end row -->
            </div>
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
