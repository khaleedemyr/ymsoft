@extends('layouts.master')

@section('title')
    {{ trans('translation.payment.add') }} | {{ config('app.name') }}
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ trans('translation.payment.form.title') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('finance.payments.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ trans('translation.payment.form.contra_bon') }}</label>
                            <select name="contra_bon_id" class="form-select" required>
                                <option value="">{{ trans('translation.payment.form.select_contra_bon') }}</option>
                                @foreach($contraBons as $contraBon)
                                    <option value="{{ $contraBon->id }}">
                                        {{ $contraBon->contra_bon_number }} - {{ $contraBon->supplier->name }}
                                        ({{ number_format($contraBon->total_amount, 0) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ trans('translation.payment.form.payment_method') }}</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="">{{ trans('translation.payment.form.select_method') }}</option>
                                <option value="cash">{{ trans('translation.payment.method.cash') }}</option>
                                <option value="transfer">{{ trans('translation.payment.method.transfer') }}</option>
                                <option value="check">{{ trans('translation.payment.method.check') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ trans('translation.payment.form.amount') }}</label>
                            <input type="number" name="amount" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ trans('translation.payment.form.payment_proof') }}</label>
                            <input type="file" name="payment_proof" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ trans('translation.payment.form.notes') }}</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('finance.payments.index') }}" class="btn btn-secondary me-1">
                                {{ trans('translation.payment.button.back') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ trans('translation.payment.button.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 