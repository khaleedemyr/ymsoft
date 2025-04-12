@extends('layouts.master')

@section('title')
    {{ trans('translation.payment.view') }} | {{ config('app.name') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ trans('translation.payment.detail.title') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">{{ trans('translation.payment.table.payment_number') }}</label>
                            <p>{{ $payment->payment_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">{{ trans('translation.payment.table.status') }}</label>
                            <p>{{ trans('translation.payment.status.' . $payment->status) }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">{{ trans('translation.payment.table.contra_bon') }}</label>
                            <p>{{ $payment->contraBon->contra_bon_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">{{ trans('translation.payment.table.supplier') }}</label>
                            <p>{{ $payment->contraBon->supplier->name }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">{{ trans('translation.payment.table.payment_method') }}</label>
                            <p>{{ trans('translation.payment.method.' . $payment->payment_method) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">{{ trans('translation.payment.table.amount') }}</label>
                            <p>{{ number_format($payment->amount, 0) }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="fw-bold">{{ trans('translation.payment.detail.payment_proof') }}</label>
                            @if($payment->payment_proof)
                                <div>
                                    <a href="{{ asset('storage/' . $payment->payment_proof) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="ri-eye-line"></i> {{ trans('translation.payment.detail.view_proof') }}
                                    </a>
                                </div>
                            @else
                                <p>{{ trans('translation.payment.detail.no_proof') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="fw-bold">{{ trans('translation.payment.form.notes') }}</label>
                            <p>{{ $payment->notes ?: '-' }}</p>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('finance.payments.index') }}" class="btn btn-secondary me-1">
                            {{ trans('translation.payment.button.back') }}
                        </a>
                        @if($payment->status === 'pending' && \App\Helpers\UserHelper::canApprovePurchaseInvoice())
                            <button type="button" class="btn btn-success me-1" id="btn-approve">
                                {{ trans('translation.payment.button.approve') }}
                            </button>
                            <button type="button" class="btn btn-danger" id="btn-reject">
                                {{ trans('translation.payment.button.reject') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // Handle approve button
            $('#btn-approve').on('click', function() {
                Swal.fire({
                    title: "{{ trans('translation.payment.message.confirm_approve') }}",
                    text: "{{ trans('translation.payment.message.confirm_approve_message') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "{{ trans('translation.yes') }}",
                    cancelButtonText: "{{ trans('translation.no') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('finance.payments.approve', $payment->id) }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: "{{ trans('translation.success') }}",
                                        text: response.message,
                                        icon: 'success',
                                        showCancelButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "{{ trans('translation.error') }}",
                                    text: xhr.responseJSON?.message || "{{ trans('translation.payment.message.error_approve') }}",
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });

            // Handle reject button
            $('#btn-reject').on('click', function() {
                Swal.fire({
                    title: "{{ trans('translation.payment.message.confirm_reject') }}",
                    text: "{{ trans('translation.payment.message.confirm_reject_message') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "{{ trans('translation.yes') }}",
                    cancelButtonText: "{{ trans('translation.no') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('finance.payments.reject', $payment->id) }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: "{{ trans('translation.success') }}",
                                        text: response.message,
                                        icon: 'success',
                                        showCancelButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "{{ trans('translation.error') }}",
                                    text: xhr.responseJSON?.message || "{{ trans('translation.payment.message.error_reject') }}",
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection 