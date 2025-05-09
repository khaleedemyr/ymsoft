@extends('layouts.master')
@section('title') @lang('translation.placeholders') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Base UI @endslot
@slot('title') Placeholders @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Default Placeholder</h4>
            </div><!-- end card header -->

            <div class="card-body">

                <p class="text-muted">In the example below, we take a typical card component and
                    recreate it with placeholders applied to create a “loading card”.</p>

                <div class="row justify-content-center">
                    <div class="col-xl-7">
                        <div class="row justify-content-between">
                            <div class="col-lg-5 col-sm-6">
                                <div class="card">
                                    <img src="build/images/small/img-1.jpg" class="card-img-top" alt="card img">

                                    <div class="card-body">
                                        <h5 class="card-title">Card title</h5>
                                        <p class="card-text">Some quick example text to build on the
                                            card title and make up the bulk of the card's content.
                                        </p>
                                        <a href="#" class="btn btn-primary">Go somewhere</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 col-sm-6">
                                <div class="card" aria-hidden="true">
                                    <img src="build/images/small/img-2.jpg" class="card-img-top" alt="card dummy img">
                                    <div class="card-body">
                                        <h5 class="card-title placeholder-glow">
                                            <span class="placeholder col-6"></span>
                                        </h5>
                                        <p class="card-text placeholder-glow">
                                            <span class="placeholder col-7"></span>
                                            <span class="placeholder col-4"></span>
                                            <span class="placeholder col-4"></span>
                                            <span class="placeholder col-6"></span>
                                        </p>
                                        <a href="#" tabindex="-1" class="btn btn-primary disabled placeholder col-6"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

<div class="row">
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Width</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>w-25,w-50,w-75</code> or <code>w-100</code> class to
                    placeholder class to set different widths to the placeholder.</p>
                <div>
                    <span class="placeholder w-50"></span>
                    <span class="placeholder w-75"></span>
                    <span class="placeholder w-25"></span>
                    <span class="placeholder w-100"></span>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
    <div class="col-xxl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Sizing</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>placeholder-lg</code>, <code>placeholder-sm</code>,
                    or <code>placeholder-xs</code> class to placeholder class to set different size
                    placeholder.</p>
                <div class="row gap-0">
                    <div class="col-12">
                        <span class="placeholder placeholder-lg w-100"></span>
                    </div>
                    <div class="col-12">
                        <span class="placeholder w-100"></span>
                    </div>
                    <div class="col-12">
                        <span class="placeholder placeholder-sm w-100"></span>
                    </div>
                    <div class="col-12">
                        <span class="placeholder placeholder-xs w-100"></span>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!--end col-->
</div><!-- end row -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Color</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>bg-</code> class with the below-mentioned color
                    variation to set a custom color.</p>
                <div class="row g-2">
                    <div class="col-12">
                        <span class="placeholder w-100"></span>
                    </div>
                    <!--end col-->
                    <div class="col-12">
                        <span class="placeholder bg-primary w-100"></span>
                    </div>
                    <!--end col-->
                    <div class="col-12">
                        <span class="placeholder bg-secondary w-100"></span>
                    </div>
                    <!--end col-->
                    <div class="col-12">
                        <span class="placeholder bg-success w-100"></span>
                    </div>
                    <!--end col-->
                    <div class="col-12">
                        <span class="placeholder bg-danger w-100"></span>
                    </div>
                    <!--end col-->
                    <div class="col-12">
                        <span class="placeholder bg-warning w-100"></span>
                    </div>
                    <!--end col-->
                    <div class="col-12">
                        <span class="placeholder bg-info w-100"></span>
                    </div>
                    <!--end col-->
                    <div class="col-12">
                        <span class="placeholder bg-light w-100"></span>
                    </div>
                    <!--end col-->
                    <div class="col-12">
                        <span class="placeholder bg-dark w-100"></span>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!--end row-->
@endsection
@section('script')
<script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
