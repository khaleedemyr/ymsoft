@extends('layouts.master')
@section('title') @lang('translation.images') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Base UI @endslot
@slot('title') Images @endslot
@endcomponent

<div class="row">
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Image Rounded & Circle</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Use
                    <code>rounded</code> class and <code>rounded-circle</code> class to show an
                    image with a round border and rounded shape respectively.
                </p>
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <img class="rounded" alt="200x200" width="200" src="build/images/small/img-4.jpg" data-holder-rendered="true">
                    </div><!-- end col -->
                    <div class="col-md-6">
                        <div class="mt-4 mt-md-0">
                            <img class="rounded-circle avatar-xl" alt="200x200" src="build/images/users/avatar-4.jpg" data-holder-rendered="true">
                        </div>
                    </div><!-- end col -->
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->

    </div>

    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Image Thumbnails</h4>
            </div><!-- end card header -->
            <div class="card-body">

                <p class="text-muted">Use <code>img-thumbnail</code> class to give an image rounded
                    1px border appearance.</p>
                <div class="row">
                    <div class="col-6">
                        <img class="img-thumbnail" alt="200x200" width="200" src="build/images/small/img-3.jpg" data-holder-rendered="true">
                    </div><!-- end col -->
                    <div class="col-6">
                        <div class="mt-4 mt-md-0">
                            <img class="img-thumbnail rounded-circle avatar-xl" alt="200x200" src="build/images/users/avatar-3.jpg" data-holder-rendered="true">
                        </div>
                    </div><!-- end col -->
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Image Sizes</h4>
            </div><!-- end card header -->

            <div class="card-body">

                <p class="text-muted mb-4">Use <code>avatar-xxs</code>, <code>avatar-xs</code>,
                    <code>avatar-sm</code>, <code>avatar-md</code>, <code>avatar-lg</code>,
                    <code>avatar-xl</code> class for different image sizes.
                </p>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row g-3">
                            <div class="col-xxl-2 col-md-4 col-6">
                                <div>
                                    <img src="build/images/users/avatar-2.jpg" alt="" class="rounded avatar-xxs">
                                    <p class="mt-2 mb-lg-0"><code>avatar-xxs</code></p>
                                </div>
                            </div><!-- end col -->
                            <div class="col-xxl-2 col-md-4 col-6">
                                <div>
                                    <img src="build/images/users/avatar-10.jpg" alt="" class="rounded avatar-xs">
                                    <p class="mt-2 mb-lg-0"><code>avatar-xs</code></p>
                                </div>
                            </div><!-- end col -->
                            <div class="col-xxl-2 col-md-4 col-6">
                                <div>
                                    <img src="build/images/users/avatar-3.jpg" alt="" class="rounded avatar-sm">
                                    <p class="mt-2 mb-lg-0"><code>avatar-sm</code></p>
                                </div>
                            </div><!-- end col -->
                            <div class="col-xxl-2 col-md-4 col-6">
                                <div>
                                    <img src="build/images/users/avatar-4.jpg" alt="" class="rounded avatar-md">
                                    <p class="mt-2  mb-lg-0"><code>avatar-md</code></p>
                                </div>
                            </div><!-- end col -->
                            <div class="col-xxl-2 col-md-4 col-6">
                                <div>
                                    <img src="build/images/users/avatar-5.jpg" alt="" class="rounded avatar-lg">
                                    <p class="mt-2 mb-lg-0"><code>avatar-lg</code></p>
                                </div>
                            </div><!-- end col -->
                            <div class="col-xxl-2 col-md-4 col-6">
                                <div>
                                    <img src="build/images/users/avatar-8.jpg" alt="" class="rounded avatar-xl">
                                    <p class="mt-2 mb-lg-0"><code>avatar-xl</code></p>
                                </div>
                            </div><!-- end col -->
                        </div><!-- end row -->
                    </div><!-- end col -->

                    <div class="col-md-12">
                        <div class="row g-3 mt-5">
                            <div class="col-xxl-2 col-md-4 col-6">
                                <div>
                                    <img src="build/images/users/avatar-2.jpg" alt="" class="rounded-circle avatar-xxs">
                                    <p class="mt-2 mb-lg-0"><code>avatar-xxs</code></p>
                                </div>
                            </div><!-- end col -->
                            <div class="col-xxl-2 col-md-4 col-6">
                                <div>
                                    <img src="build/images/users/avatar-10.jpg" alt="" class="rounded-circle avatar-xs">
                                    <p class="mt-2 mb-lg-0"><code>avatar-xs</code></p>
                                </div>
                            </div><!-- end col -->
                            <div class="col-xxl-2 col-md-4 col-6">
                                <div>
                                    <img src="build/images/users/avatar-3.jpg" alt="" class="rounded-circle avatar-sm">
                                    <p class="mt-2 mb-lg-0"><code>avatar-sm</code></p>
                                </div>
                            </div><!-- end col -->
                            <div class="col-xxl-2 col-md-4 col-6">
                                <div>
                                    <img src="build/images/users/avatar-4.jpg" alt="" class="rounded-circle avatar-md">
                                    <p class="mt-2  mb-lg-0"><code>avatar-md</code></p>
                                </div>
                            </div><!-- end col -->
                            <div class="col-xxl-2 col-md-4 col-6">
                                <div>
                                    <img src="build/images/users/avatar-5.jpg" alt="" class="rounded-circle avatar-lg">
                                    <p class="mt-2 mb-lg-0"><code>avatar-lg</code></p>
                                </div>
                            </div><!-- end col -->
                            <div class="col-xxl-2 col-md-4 col-6">
                                <div>
                                    <img src="build/images/users/avatar-8.jpg" alt="" class="rounded-circle avatar-xl">
                                    <p class="mt-2 mb-lg-0"><code>avatar-xl</code></p>
                                </div>
                            </div><!-- end col -->
                        </div><!-- end row -->
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Avatar With Content</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <p class="text-muted">Use
                    <code>avatar-xxs, avatar-xs,avatar-sm,avatar-md,avatar-lg,avatar-xl</code> class
                    to set different avatar content.
                </p>
                <div class="row g-3">
                    <div class="col-xxl-2 col-md-4 col-6">
                        <div class="avatar-xxs mt-3">
                            <div class="avatar-title rounded bg-primary-subtle text-primary fs-3xs">
                                XXS
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-md-4 col-6">
                        <div class="avatar-xs mt-3">
                            <div class="avatar-title rounded bg-secondary-subtle text-secondary">
                                XS
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-md-4 col-6">
                        <div class="avatar-sm mt-3">
                            <div class="avatar-title rounded bg-success-subtle text-success fs-md">
                                SM
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-md-4 col-6">
                        <div class="avatar-md mt-3">
                            <div class="avatar-title rounded bg-info-subtle text-info fs-lg">
                                MD
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-md-4 col-6">
                        <div class="avatar-lg mt-3">
                            <div class="avatar-title rounded bg-warning-subtle text-warning fs-3xl">
                                LG
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-md-4 col-6">
                        <div class="avatar-xl mt-3">
                            <div class="avatar-title rounded bg-danger-subtle text-danger fs-4xl">
                                XL
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Avatar Group</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mt-lg-0 mt-3">
                            <p class="text-muted">Use <code>avatar-group</code> class to show avatar
                                images with the group.</p>
                            <div class="avatar-group">
                                <div class="avatar-group-item">
                                    <img src="build/images/users/avatar-4.jpg" alt="" class="rounded-circle avatar-sm">
                                </div>
                                <div class="avatar-group-item">
                                    <img src="build/images/users/avatar-5.jpg" alt="" class="rounded-circle avatar-sm">
                                </div>
                                <div class="avatar-group-item">
                                    <div class="avatar-sm">
                                        <div class="avatar-title rounded-circle bg-light text-primary">
                                            A
                                        </div>
                                    </div>
                                </div>
                                <div class="avatar-group-item">
                                    <img src="build/images/users/avatar-2.jpg" alt="" class="rounded-circle avatar-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end col-->

                    <div class="col-lg-6">
                        <div class="mt-lg-0 mt-3">
                            <p class="text-muted">Use <code>avatar-group</code> class with
                                <code>data-bs-toggle="tooltip"</code> to show avatar group images
                                with tooltip.
                            </p>
                            <div class="avatar-group">
                                <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-placement="top" title="Christi">
                                    <img src="build/images/users/avatar-4.jpg" alt="" class="rounded-circle avatar-sm">
                                </a>
                                <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-placement="top" title="Frank Hook">
                                    <img src="build/images/users/avatar-3.jpg" alt="" class="rounded-circle avatar-sm">
                                </a>
                                <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-placement="top" title="Christi">
                                    <div class="avatar-sm">
                                        <div class="avatar-title rounded-circle bg-light text-primary">
                                            C
                                        </div>
                                    </div>
                                </a>
                                <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-placement="top" title="mORE">
                                    <div class="avatar-sm">
                                        <div class="avatar-title rounded-circle">
                                            2+
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div><!-- end card body -->
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->

<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Figures</h4>
            </div><!-- end card header -->

            <div class="card-body">

                <p class="card-title-desc text-muted">Use the included <code>figure</code>,
                    <code>figure-img</code> and <code>figure-caption</code> classes to provide some
                    baseline styles for the HTML5 <code>&lt;figure&gt;</code> and
                    <code>&lt;figcaption&gt;</code> elements.
                </p>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <figure class="figure mb-0">
                            <img src="build/images/small/img-4.jpg" class="figure-img img-fluid rounded" alt="...">
                            <figcaption class="figure-caption">A caption for the above image.
                            </figcaption>
                        </figure>
                    </div>
                    <div class="col-sm-6">
                        <figure class="figure mb-0">
                            <img src="build/images/small/img-5.jpg" class="figure-img img-fluid rounded" alt="...">
                            <figcaption class="figure-caption text-end">A caption for the above
                                image.</figcaption>
                        </figure>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Responsive Images</h4>
            </div><!-- end card header -->

            <div class="card-body">

                <p class="card-title-desc text-muted">Responsive Images with
                    <code>img-fluid,max-width: 100%; and height: auto;</code> to the image so that
                    it scales with the parent width.
                </p>
                <div>
                    <img src="build/images/small/img-2.jpg" class="img-fluid" alt="Responsive image">
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div><!-- end col -->

</div>
<!--end row-->

@endsection
@section('script')
<script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
