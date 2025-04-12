@extends('layouts.master')

@section('title')
    {{ trans('translation.supplier.title') }} - {{ $supplier->name }}
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            {{ trans('translation.master_data') }}
        @endslot
        @slot('title')
            {{ trans('translation.supplier.title') }}
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">{{ $supplier->name }}</h5>
                    <div class="flex-shrink-0">
                        <a href="{{ route('master-data.suppliers.edit', $supplier->id) }}" class="btn btn-primary">
                            <i class="ri-pencil-line align-bottom me-1"></i> {{ trans('translation.supplier.edit') }}
                        </a>
                        <a href="{{ route('master-data.suppliers.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line align-bottom me-1"></i> {{ trans('translation.supplier.close') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">{{ trans('translation.supplier.code') }}</th>
                                    <td>{{ $supplier->code }}</td>
                                </tr>
                                <tr>
                                    <th>{{ trans('translation.supplier.name') }}</th>
                                    <td>{{ $supplier->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ trans('translation.supplier.contact_person') }}</th>
                                    <td>{{ $supplier->contact_person ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ trans('translation.supplier.phone') }}</th>
                                    <td>{{ $supplier->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ trans('translation.supplier.email') }}</th>
                                    <td>{{ $supplier->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ trans('translation.supplier.npwp') }}</th>
                                    <td>{{ $supplier->npwp ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ trans('translation.supplier.status') }}</th>
                                    <td>
                                        <span class="badge {{ $supplier->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $supplier->status == 'active' ? trans('translation.supplier.active') : trans('translation.supplier.inactive') }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">{{ trans('translation.supplier.address') }}</th>
                                    <td>{{ $supplier->address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ trans('translation.supplier.city') }}</th>
                                    <td>{{ $supplier->city ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ trans('translation.supplier.province') }}</th>
                                    <td>{{ $supplier->province ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ trans('translation.supplier.postal_code') }}</th>
                                    <td>{{ $supplier->postal_code ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ trans('translation.supplier.bank_name') }}</th>
                                    <td>{{ $supplier->bank_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ trans('translation.supplier.bank_account_number') }}</th>
                                    <td>{{ $supplier->bank_account_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ trans('translation.supplier.bank_account_name') }}</th>
                                    <td>{{ $supplier->bank_account_name ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 