@extends('layouts.master')
@section('title')
@lang('translation.Apex_Polar_area_Chart')
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1')
Apexcharts
@endslot
@slot('title')
Polararea Charts
@endslot
@endcomponent

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Basic Polararea Chart</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="basic_polar_area" data-colors='["--tb-primary", "--tb-success", "--tb-warning","--tb-danger", "--tb-info", "--tb-success", "--tb-primary", "--tb-dark", "--tb-secondary"]' class="apex-charts" dir="ltr"></div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">PolarArea Monochrome</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="monochrome_polar_area" class="apex-charts" dir="ltr"></div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->
@endsection
@section('script')
<script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/apexcharts-polararea.init.js') }}"></script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
