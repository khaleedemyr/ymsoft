@extends('layouts.master')

@section('title')
    {{ trans('translation.supplier.edit') }}
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            {{ trans('translation.master_data') }}
        @endslot
        @slot('title')
            {{ trans('translation.supplier.edit') }}
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ trans('translation.supplier.edit') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('master-data.suppliers.update', $supplier->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ trans('translation.supplier.code') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code', $supplier->code) }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ trans('translation.supplier.name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $supplier->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ trans('translation.supplier.contact_person') }}</label>
                                <input type="text" class="form-control @error('contact_person') is-invalid @enderror" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}">
                                @error('contact_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ trans('translation.supplier.phone') }}</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $supplier->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ trans('translation.supplier.email') }}</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $supplier->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ trans('translation.supplier.npwp') }}</label>
                                <input type="text" class="form-control @error('npwp') is-invalid @enderror" name="npwp" value="{{ old('npwp', $supplier->npwp) }}">
                                @error('npwp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">{{ trans('translation.supplier.address') }}</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="3">{{ old('address', $supplier->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ trans('translation.supplier.city') }}</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ old('city', $supplier->city) }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ trans('translation.supplier.province') }}</label>
                                <input type="text" class="form-control @error('province') is-invalid @enderror" name="province" value="{{ old('province', $supplier->province) }}">
                                @error('province')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ trans('translation.supplier.postal_code') }}</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" name="postal_code" value="{{ old('postal_code', $supplier->postal_code) }}">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <h5 class="mt-4 mb-3">{{ trans('translation.supplier.bank_info') }}</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ trans('translation.supplier.bank_name') }}</label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror" name="bank_name" value="{{ old('bank_name', $supplier->bank_name) }}">
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ trans('translation.supplier.bank_account_number') }}</label>
                                <input type="text" class="form-control @error('bank_account_number') is-invalid @enderror" name="bank_account_number" value="{{ old('bank_account_number', $supplier->bank_account_number) }}">
                                @error('bank_account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ trans('translation.supplier.bank_account_name') }}</label>
                                <input type="text" class="form-control @error('bank_account_name') is-invalid @enderror" name="bank_account_name" value="{{ old('bank_account_name', $supplier->bank_account_name) }}">
                                @error('bank_account_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ trans('translation.supplier.status') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                    <option value="active" {{ old('status', $supplier->status) == 'active' ? 'selected' : '' }}>{{ trans('translation.supplier.active') }}</option>
                                    <option value="inactive" {{ old('status', $supplier->status) == 'inactive' ? 'selected' : '' }}>{{ trans('translation.supplier.inactive') }}</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('master-data.suppliers.index') }}" class="btn btn-secondary">{{ trans('translation.supplier.close') }}</a>
                            <button type="submit" class="btn btn-primary">{{ trans('translation.supplier.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 