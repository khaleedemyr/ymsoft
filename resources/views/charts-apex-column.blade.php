@extends('layouts.master')
@section('title')
@lang('translation.Apex_Column_Chart')
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1')
Apexcharts
@endslot
@slot('title')
Column Charts
@endslot
@endcomponent

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Basic Column Charts</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="column_chart" data-colors='["--tb-danger", "--tb-primary", "--tb-success"]' class="apex-charts" dir="ltr"></div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Column with Data Labels</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="column_chart_datalabel" data-colors='["--tb-success"]' class="apex-charts" dir="ltr"></div>
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
                <h4 class="card-title mb-0">Stacked Column Charts</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="column_stacked" data-colors='["--tb-primary", "--tb-success", "--tb-warning", "--tb-danger"]' class="apex-charts" dir="ltr"></div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Stacked Column 100</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="column_stacked_chart" data-colors='["--tb-primary", "--tb-success", "--tb-warning"]' class="apex-charts" dir="ltr"></div>
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
                <h4 class="card-title mb-0">Column with Markers</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="column_markers" data-colors='["--tb-success", "--tb-primary"]' class="apex-charts" dir="ltr"></div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Column with Rotated Labels</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="column_rotated_labels" data-colors='["--tb-info"]' class="apex-charts" dir="ltr"></div>
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
                <h4 class="card-title mb-0">Column with Negative Values</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="column_nagetive_values" data-colors='["--tb-success", "--tb-danger", "--tb-warning"]' class="apex-charts" dir="ltr"></div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Range Column Chart</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="column_range" data-colors='["--tb-primary", "--tb-success"]' class="apex-charts" dir="ltr"></div>
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
                <h4 class="card-title mb-0">Dynamic Loaded Chart</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="dynamicloadedchart-wrap" dir="ltr">
                    <div id="chart-year" data-colors='["--tb-primary", "--tb-success", "--tb-warning", "--tb-danger", "--tb-dark", "--tb-info"]' class="apex-charts"></div>
                    <div id="chart-quarter" class="apex-charts"></div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Distributed Columns Chart</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="column_distributed" data-colors='["--tb-primary", "--tb-success", "--tb-warning", "--tb-danger", "--tb-dark", "--tb-info"]' class="apex-charts" dir="ltr"></div>
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
                <h4 class="card-title mb-0">Column with Group Label</h4>
            </div><!-- end card header -->

            <div class="card-body">
                <div id="column_group_labels" data-colors='["--tb-info"]' class="apex-charts" dir="ltr"></div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->
@endsection
@section('script')
<script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.0/dayjs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.0/plugin/quarterOfYear.min.js"></script>

<script src="{{ URL::asset('build/js/pages/apexcharts-column.init.js') }}"></script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
