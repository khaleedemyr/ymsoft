@extends('layouts.master')
@section('title') @lang('translation.Apex_Candlstick_Chart') @endsection
@section('content')

@component('components.breadcrumb')
@slot('li_1') Apexcharts @endslot
@slot('title') Candlestick Charts @endslot
@endcomponent

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Basic Candlestick Chart</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="basic_candlestick" data-colors='["--tb-success", "--tb-danger"]' class="apex-charts" dir="ltr"></div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Candlestick Synced with Brush Chart (Combo)</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div>
                    <div id="combo_candlestick" data-colors='["--tb-info", "--tb-danger"]' class="apex-charts" dir="ltr"></div>
                    <div id="combo_candlestick_chart" data-colors='["--tb-warning", "--tb-danger"]' class="apex-charts" dir="ltr"></div>
                </div>
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
                <h4 class="card-title mb-0">Category X-Axis</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="category_candlestick" data-colors='["--tb-success", "--tb-danger"]' class="apex-charts" dir="ltr"></div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Candlestick with line</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="candlestick_with_line" data-colors='["--tb-success", "--tb-danger", "--tb-info", "--tb-warning"]' class="apex-charts" dir="ltr"></div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->
@endsection
@section('script')
<script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

<script src="https://apexcharts.com/samples/assets/ohlc.js"></script>
<!-- for Category x-axis chart -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.8.17/dayjs.min.js"></script>

<script src="{{ URL::asset('build/js/pages/apexcharts-candlestick.init.js') }}"></script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
