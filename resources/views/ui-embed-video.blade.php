@extends('layouts.master')
@section('title') @lang('translation.embed-video') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Base UI @endslot
@slot('title') Embed Video @endslot
@endcomponent

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Ratio Video 16:9</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Wrap any embed in<code>&lt;iframe&gt;</code> tag, in a parent
                    element, use <code>ratio-16x9</code> class to set aspect ratio 16:9. </p>
                <div>
                    <!-- 16:9 aspect ratio -->
                    <div class="ratio ratio-16x9">
                        <iframe class="rounded" src="https://www.youtube.com/embed/1y_kfWUCFDQ" title="YouTube video" allowfullscreen></iframe>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->

        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Ratio Video 4:3</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>ratio-4x3</code> class to set aspect ratio 4:3.</p>
                <!-- 4:3 aspect ratio -->
                <div class="ratio ratio-4x3">
                    <iframe class="rounded" src="https://www.youtube.com/embed/PHcgN1GTjdU" title="YouTube video" allowfullscreen></iframe>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->

        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Custom Ratios</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>--tb-aspect-ratio: 50%</code> to style element to
                    set aspect ratio 2:1.</p>
                <!-- 16:9 aspect ratio -->
                <div class="ratio" style="--tb-aspect-ratio: 50%;">
                    <iframe class="rounded" src="https://www.youtube.com/embed/2RZQN_ko0iU" title="YouTube video" allowfullscreen></iframe>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->

    </div>
    <!-- end col -->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Ratio Video 21:9</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>ratio-21x9</code> class to set aspect ratio 21:9.
                </p>
                <!-- 21:9 aspect ratio -->
                <div class="ratio ratio-21x9">
                    <iframe class="rounded" src="https://www.youtube.com/embed/Z-fV2lGKnnU" title="YouTube video" allowfullscreen></iframe>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->

        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Ratio Video 1:1</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <p class="text-muted">Use <code>ratio-1x1</code> class to set aspect ratio 1:1.</p>
                <!-- 1:1 aspect ratio -->
                <div class="ratio ratio-1x1">
                    <iframe class="rounded" src="https://www.youtube.com/embed/GfSZtaoc5bw" title="YouTube video" allowfullscreen></iframe>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div><!-- end row -->

@endsection
@section('script')
<script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
