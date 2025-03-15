@extends('layouts.master')

@section('title')
    {{ __('translation.floor_order.show.title') }}
@endsection

@section('css')
    <style>
        .category-row {
            background-color: #f8f9fa !important;
        }
        
        .category-row td {
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            color: #1f1f1f;
        }
        
        .subcategory-row td {
            font-weight: 600 !important;
            color: #495057;
            background-color: #fff !important;
        }
        
        .item-row td {
            font-weight: normal;
            color: #495057;
        }
        
        .subcategory-row td:first-child {
            padding-left: 25px !important;
        }
        
        .item-row td:first-child {
            padding-left: 50px !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('translation.floor_order.show.title') }}</h4>
                </div>
                <div class="card-body">
                    <!-- Info FO -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="35%">{{ __('translation.floor_order.show.fo_number') }}</th>
                                    <td>: {{ $floorOrder->fo_number }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('translation.floor_order.show.order_date') }}</th>
                                    <td>: {{ \Carbon\Carbon::parse($floorOrder->order_date)->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('translation.floor_order.show.warehouse') }}</th>
                                    <td>: {{ optional($floorOrder->warehouse)->name ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="35%">{{ __('translation.floor_order.show.status') }}</th>
                                    <td>: {{ ucfirst($floorOrder->status) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('translation.floor_order.show.arrival_date') }}</th>
                                    <td>: {{ $floorOrder->arrival_date ? \Carbon\Carbon::parse($floorOrder->arrival_date)->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('translation.floor_order.show.created_by') }}</th>
                                    <td>: {{ optional($floorOrder->creator)->nama_lengkap ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('translation.floor_order.show.table.name') }}</th>
                                    <th style="width: 150px">{{ __('translation.floor_order.show.table.code') }}</th>
                                    <th style="width: 100px">{{ __('translation.floor_order.show.table.qty') }}</th>
                                    <th style="width: 100px">{{ __('translation.floor_order.show.table.unit') }}</th>
                                    <th style="width: 150px">{{ __('translation.floor_order.show.table.price') }}</th>
                                    <th style="width: 150px">{{ __('translation.floor_order.show.table.subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupedItems as $category => $subcategories)
                                    @foreach($subcategories as $subcategory => $categoryItems)
                                        <tr class="category-row">
                                            <td colspan="6" class="fw-bold">{{ $category }} - {{ $subcategory }}</td>
                                        </tr>
                                        @foreach($categoryItems as $item)
                                            <tr>
                                                <td>{{ $item['name'] }}</td>
                                                <td>{{ $item['sku'] }}</td>
                                                <td class="text-end">{{ number_format($item['qty'], 0) }}</td>
                                                <td>{{ $item['medium_unit'] }}</td>
                                                <td class="text-end">Rp {{ number_format($item['price'], 0) }}</td>
                                                <td class="text-end">Rp {{ number_format($item['total'], 0) }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('translation.floor_order.show.no_items') }}</td>
                                    </tr>
                                @endforelse
                                <tr class="table-light fw-bold">
                                    <td colspan="5" class="text-end">{{ __('translation.floor_order.show.table.total') }}:</td>
                                    <td class="text-end">Rp {{ number_format($floorOrder->total_amount, 0) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('floor-orders.index') }}" class="btn btn-light">{{ __('translation.floor_order.show.buttons.back') }}</a>
                                @if($floorOrder->status === 'draft')
                                    <a href="{{ route('floor-orders.edit', $floorOrder->id) }}" class="btn btn-primary">{{ __('translation.floor_order.show.buttons.edit') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 