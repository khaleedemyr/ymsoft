@extends('layouts.master')

@section('title')
    {{ trans('translation.daily_check.detail') }}
@endsection

@section('css')
    <style>
        .photo-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            margin: 5px;
            cursor: pointer;
        }
        
        .area-header {
            font-weight: bold;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        
        .item-card {
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .condition-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
            min-width: 80px;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .c-condition {
            background-color: #28a745;
            color: white;
        }
        
        .wm-condition {
            background-color: #ffc107;
            color: black;
        }
        
        .d-condition {
            background-color: #dc3545;
            color: white;
        }
        
        .na-condition {
            background-color: #6c757d;
            color: white;
        }
        
        /* Modal untuk gambar */
        .modal-image {
            max-width: 100%;
            max-height: 80vh;
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('translation.daily_check.detail') }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div>
                        @component('components.breadcrumb')
                            @slot('li_1')
                                {{ trans('translation.daily_check.title') }}
                            @endslot
                            @slot('title')
                                {{ trans('translation.daily_check.detail') }}
                            @endslot
                        @endcomponent

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center">
                                        <h6 class="card-title flex-grow-1 mb-0">{{ trans('translation.daily_check.information') }}</h6>
                                        <div class="flex-shrink-0">
                                            <a href="{{ route('daily-check.list') }}" class="btn btn-primary">
                                                <i class="ri-arrow-left-line align-bottom"></i> {{ trans('translation.back') }}
                                            </a>
                                            
                                            @if($dailyCheck->status == 'draft')
                                            <a href="{{ route('daily-check.create', ['outlet_id' => $dailyCheck->id_outlet]) }}" 
                                               class="btn btn-warning">
                                                <i class="ri-edit-line align-bottom"></i> {{ trans('translation.continue') }}
                                            </a>
                                            @else
                                            <a href="{{ route('daily-check.edit', $dailyCheck->id) }}" 
                                               class="btn btn-info">
                                                <i class="ri-pencil-line align-bottom"></i> {{ trans('translation.edit') }}
                                            </a>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <strong>{{ trans('translation.daily_check.table.outlet') }}:</strong>
                                                    <span>{{ optional($dailyCheck->outlet)->nama_outlet }}</span>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>{{ trans('translation.daily_check.table.date') }}:</strong>
                                                    <span>{{ $dailyCheck->date instanceof \DateTime ? $dailyCheck->date->format('d/m/Y') : date('d/m/Y', strtotime($dailyCheck->date)) }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <strong>{{ trans('translation.daily_check.table.created_by') }}:</strong>
                                                    <span>{{ optional($dailyCheck->creator)->nama_lengkap }}</span>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Status:</strong>
                                                    @if($dailyCheck->status == 'draft')
                                                        <span class="badge bg-warning">Draft</span>
                                                    @else
                                                        <span class="badge bg-success">Saved</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            @php
                                                $groupedDetails = $dailyCheck->details->groupBy(function($detail) {
                                                    return optional(optional($detail->item)->area)->name;
                                                });
                                            @endphp

                                            @foreach($groupedDetails as $areaName => $details)
                                                <div class="area-section mb-4">
                                                    <div class="area-header">
                                                        {{ $areaName ?? 'Undefined Area' }}
                                                    </div>
                                                    <div class="area-content">
                                                        @foreach($details as $detail)
                                                            <div class="item-card">
                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                        <h5>{{ optional($detail->item)->name }}</h5>
                                                                        <div class="condition-status">
                                                                            <div class="condition-badge 
                                                                                {{ $detail->condition == 'C' ? 'c-condition' : '' }}
                                                                                {{ $detail->condition == 'WM' ? 'wm-condition' : '' }}
                                                                                {{ $detail->condition == 'D' ? 'd-condition' : '' }}
                                                                                {{ $detail->condition == 'NA' ? 'na-condition' : '' }}">
                                                                                @if($detail->condition == 'C')
                                                                                    Clean
                                                                                @elseif($detail->condition == 'WM')
                                                                                    Working but Maintenance
                                                                                @elseif($detail->condition == 'D')
                                                                                    Damage
                                                                                @elseif($detail->condition == 'NA')
                                                                                    Not Available
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        <div class="mt-3">
                                                                            <div class="mb-2">
                                                                                <strong>{{ trans('translation.daily_check.time') }}:</strong>
                                                                                <span>{{ $detail->time }}</span>
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <strong>{{ trans('translation.daily_check.other_issue') }}:</strong>
                                                                                <span>{{ $detail->other_issue ?: '-' }}</span>
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <strong>{{ trans('translation.daily_check.remark') }}:</strong>
                                                                                <span>{{ $detail->remark ?: '-' }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <h6>{{ trans('translation.daily_check.photos') }}</h6>
                                                                        <div class="photo-gallery">
                                                                            @php
                                                                                $photos = $dailyCheck->photos->where('item_id', $detail->item_id);
                                                                            @endphp
                                                                            
                                                                            @forelse($photos as $photo)
                                                                                <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                                                                                     class="photo-preview" 
                                                                                     onclick="openImageModal('{{ asset('storage/' . $photo->photo_path) }}')"
                                                                                     alt="Photo">
                                                                            @empty
                                                                                <p class="text-muted">{{ trans('translation.no_photos') }}</p>
                                                                            @endforelse
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk melihat gambar -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">{{ trans('translation.daily_check.view_photo') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalImage" class="modal-image" alt="Enlarged photo">
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/js/app.js') }}"></script>
<script>
    function openImageModal(imageUrl) {
        document.getElementById('modalImage').src = imageUrl;
        var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        imageModal.show();
    }
</script>
@endsection 