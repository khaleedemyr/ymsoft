@extends('layouts.master')
@section('title') @lang('translation.colored-links') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Base UI @endslot
@slot('title') Colored Links @endslot
@endcomponent

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Link colors</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">You can use the <code>.link-*</code> classes to colorize
                    links. Unlike the <a href="ui-colors.html"><code>.text-*</code> classes</a>,
                    these classes have a <code>:hover</code> and <code>:focus</code> state. Some of
                    the link styles use a relatively light foreground color, and should only be used
                    on a dark background in order to have sufficient contrast.</p>
                <div>
                    <p><a href="#" class="link-primary">Primary link</a></p>
                    <p><a href="#" class="link-secondary">Secondary link</a></p>
                    <p><a href="#" class="link-success">Success link</a></p>
                    <p><a href="#" class="link-danger">Danger link</a></p>
                    <p><a href="#" class="link-warning">Warning link</a></p>
                    <p><a href="#" class="link-info">Info link</a></p>
                    <p><a href="#" class="link-light text-body">Light link</a></p>
                    <p><a href="#" class="link-dark text-body">Dark link</a></p>
                    <p><a href="#" class="link-body-emphasis mb-0">Emphasis link</a></p>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Link utilities</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Colored links can also be modified by our link utilities.</p>
                <div>
                    <p><a href="#" class="link-primary link-offset-2 text-decoration-underline link-underline-opacity-25 link-underline-opacity-100-hover">Primary
                            link</a></p>
                    <p><a href="#" class="link-secondary link-offset-2 text-decoration-underline link-underline-opacity-25 link-underline-opacity-100-hover">Secondary
                            link</a></p>
                    <p><a href="#" class="link-success link-offset-2 text-decoration-underline link-underline-opacity-25 link-underline-opacity-100-hover">Success
                            link</a></p>
                    <p><a href="#" class="link-danger link-offset-2 text-decoration-underline link-underline-opacity-25 link-underline-opacity-100-hover">Danger
                            link</a></p>
                    <p><a href="#" class="link-warning link-offset-2 text-decoration-underline link-underline-opacity-25 link-underline-opacity-100-hover">Warning
                            link</a></p>
                    <p><a href="#" class="link-info link-offset-2 text-decoration-underline link-underline-opacity-25 link-underline-opacity-100-hover">Info
                            link</a></p>
                    <p><a href="#" class="link-light text-body link-offset-2 text-decoration-underline link-underline-opacity-25 link-underline-opacity-100-hover">Light
                            link</a></p>
                    <p><a href="#" class="link-dark text-body link-offset-2 text-decoration-underline link-underline-opacity-25 link-underline-opacity-100-hover">Dark
                            link</a></p>
                    <p><a href="#" class="link-body-emphasis link-offset-2 text-decoration-underline link-underline-opacity-25 link-underline-opacity-75-hover mb-0">Emphasis
                            link</a></p>
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
                <h4 class="card-title mb-0">Link Opacity</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p>Change the alpha opacity of the link <code>rgba()</code> color value with
                    utilities. Please be aware that changes to a color’s opacity can lead to links
                    with <a href="https://getbootstrap.com/docs/5.3/getting-started/accessibility/#color-contrast" target="_blank"><em>insufficient</em> contrast</a>.</p>
                <div>
                    <p><a class="link-opacity-10" href="#">Link opacity 10</a></p>
                    <p><a class="link-opacity-25" href="#">Link opacity 25</a></p>
                    <p><a class="link-opacity-50" href="#">Link opacity 50</a></p>
                    <p><a class="link-opacity-75" href="#">Link opacity 75</a></p>
                    <p class="mb-0"><a class="link-opacity-100" href="#">Link opacity 100</a></p>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Link Opacity Hover</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">You can even change the opacity level on hover.</p>
                <div>
                    <p><a class="link-opacity-10-hover" href="#">Link hover opacity 10</a></p>
                    <p><a class="link-opacity-25-hover" href="#">Link hover opacity 25</a></p>
                    <p><a class="link-opacity-50-hover" href="#">Link hover opacity 50</a></p>
                    <p><a class="link-opacity-75-hover" href="#">Link hover opacity 75</a></p>
                    <p class="mb-0"><a class="link-opacity-100-hover" href="#">Link hover opacity
                            100</a></p>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="row">
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Underline color</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Change the underline’s color independent of the link text
                    color.</p>
                <div>
                    <p><a href="#" class="text-decoration-underline link-underline-primary">Primary
                            underline</a></p>
                    <p><a href="#" class="text-decoration-underline link-underline-secondary">Secondary
                            underline</a></p>
                    <p><a href="#" class="text-decoration-underline link-underline-success">Success
                            underline</a></p>
                    <p><a href="#" class="text-decoration-underline link-underline-danger">Danger
                            underline</a></p>
                    <p><a href="#" class="text-decoration-underline link-underline-warning">Warning
                            underline</a></p>
                    <p><a href="#" class="text-decoration-underline link-underline-info">Info
                            underline</a></p>
                    <p><a href="#" class="text-decoration-underline link-underline-light">Light
                            underline</a></p>
                    <p class="mb-0"><a href="#" class="text-decoration-underline link-underline-dark">Dark underline</a>
                    </p>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Underline opacity</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Change the underline’s opacity. Requires adding
                    <code>.link-underline</code> to first set an <code>rgba()</code> color we use to
                    then modify the alpha opacity.
                </p>
                <div>
                    <p><a class="link-offset-2 text-decoration-underline link-underline link-underline-opacity-0" href="#">Underline opacity 0</a></p>
                    <p><a class="link-offset-2 text-decoration-underline link-underline link-underline-opacity-10" href="#">Underline opacity 10</a></p>
                    <p><a class="link-offset-2 text-decoration-underline link-underline link-underline-opacity-25" href="#">Underline opacity 25</a></p>
                    <p><a class="link-offset-2 text-decoration-underline link-underline link-underline-opacity-50" href="#">Underline opacity 50</a></p>
                    <p><a class="link-offset-2 text-decoration-underline link-underline link-underline-opacity-75" href="#">Underline opacity 75</a></p>
                    <p class="mb-0"><a class="link-offset-2 text-decoration-underline link-underline link-underline-opacity-100" href="#">Underline opacity 100</a></p>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
    <div class="col-xxl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Underline offset</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Change the underline’s distance from your text. Offset is set
                    in <code>em</code> units to automatically scale with the element’s current
                    <code>font-size</code>.
                </p>
                <div>
                    <p><a href="#">Default link</a></p>
                    <p><a class="text-decoration-underline link-offset-1" href="#">Offset 1 link</a>
                    </p>
                    <p><a class="text-decoration-underline link-offset-2" href="#">Offset 2 link</a>
                    </p>
                    <p class="mb-0"><a class="text-decoration-underline link-offset-3" href="#">Offset 3 link</a></p>
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
                <h4 class="card-title mb-0">Icon link</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Take a regular <code>&lt;a&gt;</code> element, add
                    <code>.icon-link</code>, and insert an icon on either the left or right of your
                    link text. The icon is automatically sized, placed, and colored.
                </p>
                <div class="hstack d-block d-md-flex gap-4">
                    <a class="icon-link" href="#">
                        <i class="bi bi-box-seam align-baseline"></i> Icon link
                    </a>

                    <a class="icon-link" href="#">
                        Icon link <i class="bi bi-arrow-right"></i>
                    </a>

                    <a class="icon-link icon-link-hover" href="#">
                        Icon link <i class="bi bi-arrow-right"></i>
                    </a>
                    <a class="icon-link icon-link-hover" style="--tb-icon-link-transform: translate3d(0, -.125rem, 0);" href="#">
                        <i class="bi bi-clipboard"></i> Icon link
                    </a>
                    <a class="icon-link icon-link-hover" style="--tb-link-hover-color-rgb: var(--tb-success-rgb);" href="#">
                        Icon link <i class="bi bi-arrow-right"></i>
                    </a>
                    <a class="icon-link icon-link-hover link-success text-decoration-underline link-underline-success link-underline-opacity-25" href="#">
                        Icon link <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div>

@endsection
@section('script')
<script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
