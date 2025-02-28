@extends('layouts.master')

@section('title')
    @lang('translation.sales.upload')
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            z-index: 9999;
        }

        .loading-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
        }

        .loading-spinner {
            width: 4rem;
            height: 4rem;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div>
                @component('components.breadcrumb')
                    @slot('li_1')
                        Transaksi
                    @endslot
                    @slot('title')
                        @lang('translation.sales.upload')
                    @endslot
                @endcomponent

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h6 class="card-title flex-grow-1 mb-0">@lang('translation.sales.upload_file')</h6>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('sales.template') }}" class="btn btn-soft-info me-2">
                                        <i class="ri-download-2-line align-bottom me-1"></i> Download Template
                                    </a>
                                    <a href="{{ route('sales.index') }}" class="btn btn-light">
                                        <i class="ri-arrow-left-line align-bottom me-1"></i> Kembali
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="uploadForm" action="{{ route('sales.preview') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="file" class="form-label">File Excel</label>
                                                <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls">
                                                <small class="text-muted">Format file: .xlsx, .xls</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary" id="btnPreview">
                                                <i class="ri-eye-line align-bottom me-1"></i> Preview
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay">
        <div class="loading-content">
            <div class="spinner-border text-light loading-spinner mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h5 class="text-light">Memproses Data...</h5>
            <p class="text-light mb-0">Mohon tunggu sebentar</p>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();
                
                var formData = new FormData(this);
                var $submitButton = $('#btnPreview');
                
                // Disable semua input dan button
                $('input, button').prop('disabled', true);
                $('#loading-overlay').fadeIn();
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.error) {
                            // Enable kembali input dan button
                            $('input, button').prop('disabled', false);
                            $('#loading-overlay').fadeOut();
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error
                            });
                        } else if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    },
                    error: function(xhr) {
                        // Enable kembali input dan button
                        $('input, button').prop('disabled', false);
                        $('#loading-overlay').fadeOut();
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat upload file'
                        });
                    }
                });
            });
        });
    </script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 