@extends('layouts.master')
@section('title') @lang('translation.Apex_Radar_Chart') @endsection
@section('content')

@component('components.breadcrumb')
@slot('li_1') Apexcharts @endslot
@slot('title') Radar Charts @endslot
@endcomponent

                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Basic Radar Chart</h4>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <div id="basic_radar" data-colors='["--tb-success"]' class="apex-charts" dir="ltr"></div>
                                </div><!-- end card-body -->
                            </div><!-- end card -->
                        </div>
                        <!-- end col -->

                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Radar Chart - Multiple series</h4>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <div id="multi_radar" data-colors='["--tb-danger", "--tb-success", "--tb-primary"]' class="apex-charts" dir="ltr"></div>
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
                                    <h4 class="card-title mb-0">Radar Chart - Polygon Fill</h4>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <div id="polygon_radar" data-colors='["--tb-info"]' class="apex-charts" dir="ltr"></div>
                                </div><!-- end card-body -->
                            </div><!-- end card -->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->

              @endsection
@section('script')
<script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/apexcharts-radar.init.js') }}"></script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection