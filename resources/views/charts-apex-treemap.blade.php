@extends('layouts.master')
@section('title') @lang('translation.Apex_Treemap_Chart') @endsection
@section('content')

@component('components.breadcrumb')
@slot('li_1') Apexcharts @endslot
@slot('title') Treemap Charts @endslot
@endcomponent


<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Basic Treemap Charts</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="basic_treemap" data-colors='["--tb-success"]' class="apex-charts" dir="ltr"></div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Multi-Dimensional Treemap Chart</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="multi_treemap" data-colors='["--tb-primary","--tb-success"]' class="apex-charts" dir="ltr"></div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Distributed Treemap Chart (Different Color for each Cell)</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="distributed_treemap" data-colors='["--tb-primary", "--tb-secondary", "--tb-success", "--tb-info","--tb-warning", "--tb-danger"]' class="apex-charts" dir="ltr"></div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Treemap Chart with Color Ranges</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="color_range_treemap" data-colors='["--tb-info","--tb-danger"]' class="apex-charts" dir="ltr"></div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->


@endsection
@section('script')
<script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/apexcharts-treemap.init.js') }}"></script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
