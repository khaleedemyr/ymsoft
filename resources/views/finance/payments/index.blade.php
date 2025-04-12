@extends('layouts.master')

@section('title')
    {{ trans('translation.payment.list') }} | {{ config('app.name') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">{{ trans('translation.payment.list') }}</h5>
                        <div class="flex-shrink-0">
                            <a href="{{ route('finance.payments.create') }}" class="btn btn-primary">
                                <i class="ri-add-line align-bottom me-1"></i>
                                {{ trans('translation.payment.add') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="payments-table">
                            <thead>
                                <tr>
                                    <th>{{ trans('translation.payment.table.payment_number') }}</th>
                                    <th>{{ trans('translation.payment.table.contra_bon') }}</th>
                                    <th>{{ trans('translation.payment.table.supplier') }}</th>
                                    <th>{{ trans('translation.payment.table.payment_method') }}</th>
                                    <th>{{ trans('translation.payment.table.amount') }}</th>
                                    <th>{{ trans('translation.payment.table.status') }}</th>
                                    <th>{{ trans('translation.payment.table.created_by') }}</th>
                                    <th>{{ trans('translation.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_number }}</td>
                                        <td>{{ $payment->contraBon->contra_bon_number }}</td>
                                        <td>{{ $payment->contraBon->supplier->name }}</td>
                                        <td>{{ trans('translation.payment.method.' . $payment->payment_method) }}</td>
                                        <td class="text-end">{{ number_format($payment->amount, 0) }}</td>
                                        <td>{{ trans('translation.payment.status.' . $payment->status) }}</td>
                                        <td>{{ $payment->createdBy->nama_lengkap ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('finance.payments.show', $payment->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 