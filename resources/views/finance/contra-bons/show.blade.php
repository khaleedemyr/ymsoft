@extends('layouts.master')

@section('title')
    {{ trans('translation.contra_bon.show.title') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">{{ trans('translation.contra_bon.show.title') }}</h5>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <a href="{{ route('finance.contra-bons.index') }}" class="btn btn-light">
                                    <i class="ri-arrow-left-line align-bottom me-1"></i>
                                    {{ trans('translation.back') }}
                                </a>
                                @if($contraBon->status === 'draft' && \App\Helpers\UserHelper::canApproveContraBon())
                                    <button type="button" class="btn btn-success" id="btn-approve">
                                        <i class="ri-check-line align-bottom me-1"></i>
                                        {{ trans('translation.contra_bon.button.approve') }}
                                    </button>
                                @endif
                                @if($contraBon->status === 'approved')
                                    <button type="button" class="btn btn-primary" id="btn-mark-paid">
                                        <i class="ri-money-dollar-circle-line align-bottom me-1"></i>
                                        {{ trans('translation.contra_bon.button.mark_as_paid') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="35%">{{ trans('translation.contra_bon.show.info.contra_bon_number') }}</td>
                                    <td>: {{ $contraBon->contra_bon_number }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('translation.contra_bon.show.info.supplier') }}</td>
                                    <td>: {{ $contraBon->supplier->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('translation.contra_bon.show.info.issue_date') }}</td>
                                    <td>: {{ date('d/m/Y', strtotime($contraBon->issue_date)) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('translation.contra_bon.show.info.due_date') }}</td>
                                    <td>: {{ date('d/m/Y', strtotime($contraBon->due_date)) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('translation.contra_bon.show.info.status') }}</td>
                                    <td>: {{ trans('translation.contra_bon.status.' . $contraBon->status) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('translation.contra_bon.show.info.notes') }}</td>
                                    <td>: {{ $contraBon->notes ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('translation.contra_bon.show.info.created_by') }}</td>
                                    <td>: {{ optional($contraBon->createdBy)->nama_lengkap ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('translation.contra_bon.show.info.created_at') }}</td>
                                    <td>: {{ date('d/m/Y H:i:s', strtotime($contraBon->created_at)) }}</td>
                                </tr>
                                @if($contraBon->approved_by)
                                <tr>
                                    <td>{{ trans('translation.contra_bon.show.info.approved_by') }}</td>
                                    <td>: {{ optional($contraBon->approvedBy)->nama_lengkap ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('translation.contra_bon.show.info.approved_at') }}</td>
                                    <td>: {{ $contraBon->approved_at ? date('d/m/Y H:i:s', strtotime($contraBon->approved_at)) : '-' }}</td>
                                </tr>
                                @endif
                                @if($contraBon->paid_by)
                                <tr>
                                    <td>{{ trans('translation.contra_bon.show.info.paid_by') }}</td>
                                    <td>: {{ optional($contraBon->paidBy)->nama_lengkap ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('translation.contra_bon.show.info.paid_at') }}</td>
                                    <td>: {{ $contraBon->paid_at ? date('d/m/Y H:i:s', strtotime($contraBon->paid_at)) : '-' }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ trans('translation.contra_bon.show.invoices.table.invoice_number') }}</th>
                                    <th>{{ trans('translation.contra_bon.show.invoices.table.invoice_date') }}</th>
                                    <th>{{ trans('translation.contra_bon.show.invoices.table.due_date') }}</th>
                                    <th>{{ trans('translation.contra_bon.show.invoices.table.po_number') }}</th>
                                    <th class="text-end">{{ trans('translation.contra_bon.show.invoices.table.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contraBon->invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ date('d/m/Y', strtotime($invoice->invoice_date)) }}</td>
                                        <td>{{ date('d/m/Y', strtotime($invoice->due_date)) }}</td>
                                        <td>{{ $invoice->goodReceive->purchaseOrder->po_number ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($invoice->pivot->amount, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">{{ trans('translation.contra_bon.show.invoices.total_amount') }}</th>
                                    <th class="text-end">{{ number_format($contraBon->total_amount, 0) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($contraBon->status !== 'draft')
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>{{ trans('translation.contra_bon.show.status_history.table.status') }}</th>
                                                <th>{{ trans('translation.contra_bon.show.status_history.table.by') }}</th>
                                                <th>{{ trans('translation.contra_bon.show.status_history.table.at') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($contraBon->approved_by)
                                                <tr>
                                                    <td>{{ trans('translation.contra_bon.status.approved') }}</td>
                                                    <td>{{ optional($contraBon->approvedBy)->nama_lengkap ?? '-' }}</td>
                                                    <td>{{ $contraBon->approved_at ? date('d/m/Y H:i:s', strtotime($contraBon->approved_at)) : '-' }}</td>
                                                </tr>
                                            @endif
                                            @if($contraBon->paid_by)
                                                <tr>
                                                    <td>{{ trans('translation.contra_bon.status.paid') }}</td>
                                                    <td>{{ optional($contraBon->paidBy)->nama_lengkap ?? '-' }}</td>
                                                    <td>{{ $contraBon->paid_at ? date('d/m/Y H:i:s', strtotime($contraBon->paid_at)) : '-' }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
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
                    title: "{{ trans('translation.contra_bon.message.confirm_approve') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "{{ trans('translation.yes') }}",
                    cancelButtonText: "{{ trans('translation.no') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('finance.contra-bons.approve', $contraBon->id) }}",
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
                                } else {
                                    Swal.fire({
                                        title: "{{ trans('translation.error') }}",
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "{{ trans('translation.error') }}",
                                    text: xhr.responseJSON?.message || "{{ trans('translation.contra_bon.message.error_approve') }}",
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });

            // Handle mark as paid button
            $('#btn-mark-paid').on('click', function() {
                Swal.fire({
                    title: "{{ trans('translation.contra_bon.message.confirm_mark_as_paid') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "{{ trans('translation.yes') }}",
                    cancelButtonText: "{{ trans('translation.no') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('finance.contra-bons.mark-as-paid', $contraBon->id) }}",
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
                                } else {
                                    Swal.fire({
                                        title: "{{ trans('translation.error') }}",
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "{{ trans('translation.error') }}",
                                    text: xhr.responseJSON?.message || "{{ trans('translation.contra_bon.message.error_mark_as_paid') }}",
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