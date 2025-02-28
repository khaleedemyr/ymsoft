@extends('layouts.master')

@section('title')
    @lang('translation.item.title')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        /* Style untuk search box */
        .search-box {
            min-width: 200px;
        }
        
        .dataTables_filter {
            display: none;
        }
        
        /* Style untuk toggle switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #2196F3;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(30px);
        }

        .toggle-label {
            margin-left: 10px;
            line-height: 30px;
        }

        /* Style untuk price inputs */
        .price-row {
            margin-bottom: 10px;
        }

        .remove-price {
            margin-top: 32px;
        }

        /* Style untuk container checkbox */
        #regionSelection .row, #outletSelection .row {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 10px;
            margin: 0;
            background: #fff;
        }

        /* Style untuk scrollbar */
        #regionSelection .row::-webkit-scrollbar,
        #outletSelection .row::-webkit-scrollbar {
            width: 5px;
        }

        #regionSelection .row::-webkit-scrollbar-track,
        #outletSelection .row::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        #regionSelection .row::-webkit-scrollbar-thumb,
        #outletSelection .row::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 5px;
        }

        /* Style untuk checkbox container */
        .checkbox-container {
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        /* Style untuk checkbox items */
        .form-check {
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .form-check:hover {
            background-color: #f8f9fa;
        }

        .form-check-label {
            cursor: pointer;
            user-select: none;
        }

        .dropzone-container {
            min-height: 150px;
            border: 2px dashed #dee2e6;
            background: #f8f9fa;
            cursor: pointer;
            text-align: center;
            padding: 20px;
        }

        .image-preview-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 5px;
        }

        .image-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }

        .image-preview-overlay {
            position: absolute;
            top: 0;
            right: 0;
            background: rgba(255, 0, 0, 0.7);
            padding: 4px;
            border-radius: 0 4px 0 4px;
            cursor: pointer;
        }

        .delete-image {
            color: white;
        }

        .image-preview-wrapper:hover .image-preview-overlay {
            display: flex;
        }

        .item-images-container {
            position: relative;
            cursor: pointer;
        }

        .item-thumbnail {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .item-thumbnail:hover {
            transform: scale(1.1);
        }

        .default-thumbnail {
            width: 40px;
            height: 40px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .image-count {
            position: absolute;
            bottom: -5px;
            right: -5px;
            background-color: #0d6efd;
            color: white;
            border-radius: 10px;
            padding: 0 6px;
            font-size: 10px;
            font-weight: bold;
        }

        .carousel-item img {
            max-height: 500px;
            width: auto;
            margin: 0 auto;
            object-fit: contain;
        }

        #imageCarousel .carousel-item {
            text-align: center;
            background-color: #f8f9fa;
            padding: 20px;
        }

        #imageCarousel img {
            max-height: 500px;
            max-width: 100%;
            object-fit: contain;
            margin: 0 auto;
        }

        .carousel-control-prev,
        .carousel-control-next {
            background-color: rgba(0,0,0,0.5);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
        }

        .image-count {
            font-size: 10px;
            padding: 2px 6px;
            min-width: 20px;
            height: 20px;
            line-height: 16px;
            text-align: center;
        }

        .image-thumbnails {
            max-width: 100%;
            scrollbar-width: thin;
        }

        .image-thumbnails img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .image-thumbnails img.active {
            border-color: #0d6efd;
        }

        #imageCarousel .carousel-item img {
            max-height: 500px;
            width: auto;
            margin: 0 auto;
            object-fit: contain;
        }

        /* Tambahkan styling untuk content */
        .description-content,
        .specification-content {
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            min-height: 60px;
            white-space: pre-wrap;
        }

        /* Tambahkan CSS untuk styling scroll */
        .description-cell, .specification-cell {
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            font-size: 0.875rem;
            border: 1px solid #dee2e6;
        }

        /* Custom scrollbar styling */
        .description-cell::-webkit-scrollbar,
        .specification-cell::-webkit-scrollbar {
            width: 6px;
        }

        .description-cell::-webkit-scrollbar-track,
        .specification-cell::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .description-cell::-webkit-scrollbar-thumb,
        .specification-cell::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .description-cell::-webkit-scrollbar-thumb:hover,
        .specification-cell::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .content-wrapper {
            background-color: #f8f9fa;
            padding: 4px 6px;
            border-radius: 3px;
            border: 1px solid #dee2e6;
            width: 190px; /* Lebih kecil */
            min-height: 50px; /* Lebih pendek */
            font-size: 0.75rem; /* Font lebih kecil */
        }

        .content-cell {
            word-wrap: break-word;
            white-space: pre-line;
            line-height: 1.2; /* Line height lebih kecil */
        }

        .content-truncate, .content-full {
            margin-bottom: 2px;
        }

        .toggle-content {
            font-size: 0.7rem;
            color: #0d6efd;
            text-decoration: none;
            cursor: pointer;
            display: block;
            line-height: 1;
        }

        .toggle-content:hover {
            text-decoration: underline;
        }

        /* Styling untuk expanded state */
        .content-full {
            position: absolute;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            padding: 6px;
            z-index: 1;
            width: 190px;
            max-height: 200px;
            overflow-y: auto;
            font-size: 0.75rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Custom scrollbar - lebih kecil */
        .content-full::-webkit-scrollbar {
            width: 4px;
        }

        .content-full::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        .content-full::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 2px;
        }

        .content-full::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .image-preview-item {
            transition: transform 0.2s;
        }

        .image-preview-item:hover {
            transform: scale(1.05);
        }

        .modal-body {
            max-height: 80vh;
            overflow-y: auto;
        }

        .image-preview-container {
            max-width: 150px;
        }

        .main-preview {
            transition: transform 0.2s;
            cursor: pointer;
        }

        .main-preview:hover {
            transform: scale(1.1);
        }

        .additional-image-link {
            transition: transform 0.2s;
            display: inline-block;
        }

        .additional-image-link:hover {
            transform: scale(1.1);
        }

        .additional-images {
            max-width: 140px;
        }

        .image-preview-container {
            max-width: 250px;
        }

        .images-grid {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .additional-images {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .image-link {
            transition: transform 0.2s;
            display: inline-block;
        }

        .image-link:hover {
            transform: scale(1.1);
            z-index: 1;
        }

        .more-images {
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
        }

        .more-images:hover {
            background-color: rgba(0,0,0,0.7) !important;
        }

        /* Tambahkan styles untuk scrollable form */
        .modal-dialog-scrollable {
            max-height: 90vh;
        }

        .modal-body-scroll {
            max-height: calc(90vh - 120px); /* 120px untuk header dan footer modal */
            overflow-y: auto;
            padding-right: 10px;
        }

        /* Custom scrollbar styling */
        .modal-body-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .modal-body-scroll::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .modal-body-scroll::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Pastikan form groups memiliki spacing yang konsisten */
        .form-group {
            margin-bottom: 1rem;
        }

        .image-preview-container {
            max-width: 200px; /* Sesuaikan dengan 3 gambar */
        }

        .images-grid {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .image-link {
            transition: transform 0.2s;
            display: inline-block;
        }

        .image-link:hover {
            transform: scale(1.1);
            z-index: 1;
        }

        .more-images {
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
        }

        .more-images:hover {
            background-color: rgba(0,0,0,0.7) !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div>
                @component('components.breadcrumb')
                    @slot('li_1')
                        Master Data
                    @endslot
                    @slot('title')
                        @lang('translation.item.title')
                    @endslot
                @endcomponent

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card" id="itemList">
                            <div class="card-header d-flex align-items-center">
                                <h6 class="card-title flex-grow-1 mb-0">@lang('translation.item.list')</h6>
                                <div class="flex-shrink-0">
                                    <div class="d-flex flex-wrap gap-2">
                                        <!-- Toggle switch -->
                                        <div class="d-flex align-items-center">
                                            <label class="toggle-switch">
                                                <input type="checkbox" id="statusToggle" checked>
                                                <span class="toggle-slider"></span>
                                            </label>
                                            <span class="toggle-label" id="statusLabel">@lang('translation.item.show_active')</span>
                                        </div>
                                        <!-- Search box -->
                                        <div class="search-box">
                                            <input type="text" class="form-control search" placeholder="@lang('translation.item.search_placeholder')">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                        <button type="button" class="btn btn-primary add-btn" data-bs-toggle="modal" data-bs-target="#modal-item">
                                            <i class="ri-add-line align-bottom me-1"></i> @lang('translation.item.add')
                                        </button>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-download-2-line align-bottom me-1"></i> @lang('translation.export')
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('items.export-excel') }}">
                                                        <i class="ri-file-excel-2-line align-bottom me-1"></i> @lang('translation.export_excel')
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('items.export-pdf') }}">
                                                        <i class="ri-file-pdf-line align-bottom me-1"></i> @lang('translation.export_pdf')
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-upload-2-line align-bottom me-1"></i> @lang('translation.import')
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('items.template') }}">
                                                        <i class="ri-file-download-line align-bottom me-1"></i> @lang('translation.download_template')
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" id="uploadBtn">
                                                        <i class="ri-upload-2-line align-bottom me-1"></i> @lang('translation.upload')
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div>
                                    <div class="table-responsive table-card mb-1">
                                        <table id="datatable" class="table align-middle table-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Gambar</th>
                                                    <th>@lang('translation.item.sku')</th>
                                                    <th>@lang('translation.item.name')</th>
                                                    <th>@lang('translation.item.category')</th>
                                                    <th>@lang('translation.item.sub_category')</th>
                                                    <th>@lang('translation.item.small_unit')</th>
                                                    <th>@lang('translation.item.medium_unit')</th>
                                                    <th>@lang('translation.item.large_unit')</th>
                                                    <th>@lang('translation.item.status')</th>
                                                    <th>@lang('translation.item.description')</th>
                                                    <th>@lang('translation.item.action')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($items as $key => $item)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td style="width: 200px;"> <!-- Kurangi sedikit width karena hanya 3 gambar per row -->
                                                        <div class="d-flex align-items-start gap-2">
                                                            @if($item->images->isNotEmpty())
                                                                <div class="image-preview-container">
                                                                    <div class="images-grid">
                                                                        @php
                                                                            $allImages = $item->images;
                                                                            $totalImages = $allImages->count();
                                                                            $firstRow = $allImages->take(3);
                                                                            $secondRow = $allImages->skip(3)->take(3);
                                                                        @endphp

                                                                        <!-- First row -->
                                                                        <div class="d-flex gap-1">
                                                                            @foreach($firstRow as $image)
                                                                                <a href="{{ url('storage/' . $image->path) }}" 
                                                                                   target="_blank" 
                                                                                   class="image-link">
                                                                                    <img src="{{ url('storage/' . $image->path) }}" 
                                                                                         class="rounded"
                                                                                         style="width: 45px; height: 45px; object-fit: cover;"
                                                                                         alt="{{ $item->name }}"
                                                                                         loading="lazy">
                                                                                </a>
                                                                            @endforeach
                                                                        </div>
                                                                        
                                                                        <!-- Second row only if more than 3 images -->
                                                                        @if($totalImages > 3)
                                                                            <div class="d-flex gap-1 mt-1">
                                                                                @foreach($secondRow as $image)
                                                                                    <a href="{{ url('storage/' . $image->path) }}" 
                                                                                       target="_blank" 
                                                                                       class="image-link">
                                                                                        <img src="{{ url('storage/' . $image->path) }}" 
                                                                                             class="rounded"
                                                                                             style="width: 45px; height: 45px; object-fit: cover;"
                                                                                             alt="{{ $item->name }}"
                                                                                             loading="lazy">
                                                                                    </a>
                                                                                @endforeach
                                                                                
                                                                                @if($totalImages > 6)
                                                                                    <div class="more-images rounded d-flex align-items-center justify-content-center"
                                                                                         style="width: 45px; height: 45px; background-color: rgba(0,0,0,0.5);">
                                                                                        <span class="text-white">+{{ $totalImages - 6 }}</span>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                                                     style="width: 45px; height: 45px;">
                                                                    <i class="ri-image-line text-muted"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>{{ $item->sku }}</td>
                                                    <td>{{ $item->name }}</td>
                                                    <td>{{ $item->category->name }}</td>
                                                    <td>{{ $item->subCategory->name }}</td>
                                                    <td>{{ $item->smallUnit->name }}</td>
                                                    <td>{{ $item->mediumUnit->name }}</td>
                                                    <td>{{ $item->largeUnit->name }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $item->status == 'active' ? 'success' : 'danger' }}">
                                                            {{ $item->status == 'active' ? __('translation.item.active') : __('translation.item.inactive') }}
                                                        </span>
                                                    </td>
                                                    <td style="width: 400px;">
                                                        <div class="d-flex gap-1">
                                                            <div class="content-wrapper flex-grow-1">
                                                                <small>
                                                                    <strong class="text-muted" style="font-size: 0.7rem;">Deskripsi:</strong><br>
                                                                    <div class="content-cell description-{{ $item->id }}">
                                                                        <div class="content-truncate">
                                                                            {{ Str::limit($item->description, 50) ?: 'Tidak ada deskripsi' }}
                                                                        </div>
                                                                        @if(strlen($item->description) > 50)
                                                                            <div class="content-full" style="display: none;">
                                                                                {{ $item->description }}
                                                                            </div>
                                                                            <a href="javascript:void(0)" 
                                                                               class="toggle-content" 
                                                                               data-target="description-{{ $item->id }}"
                                                                               data-show-less="false">
                                                                                ...more
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </small>
                                                            </div>
                                                            <div class="content-wrapper flex-grow-1">
                                                                <small>
                                                                    <strong class="text-muted" style="font-size: 0.7rem;">Spesifikasi:</strong><br>
                                                                    <div class="content-cell specification-{{ $item->id }}">
                                                                        <div class="content-truncate">
                                                                            {{ Str::limit($item->specification, 50) ?: 'Tidak ada spesifikasi' }}
                                                                        </div>
                                                                        @if(strlen($item->specification) > 50)
                                                                            <div class="content-full" style="display: none;">
                                                                                {{ $item->specification }}
                                                                            </div>
                                                                            <a href="javascript:void(0)" 
                                                                               class="toggle-content" 
                                                                               data-target="specification-{{ $item->id }}"
                                                                               data-show-less="false">
                                                                                ...more
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="hstack gap-2">
                                                            <button class="btn btn-sm btn-info view-prices-btn" 
                                                                    data-id="{{ $item->id }}"
                                                                    data-name="{{ $item->name }}"
                                                                    data-prices="{{ json_encode($item->prices) }}">
                                                                <i class="ri-money-dollar-circle-line align-bottom"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-warning edit-btn"
                                                                    data-item="{{ $item }}"
                                                                    data-prices="{{ json_encode($item->prices) }}"
                                                                    data-availabilities="{{ json_encode($item->availabilities) }}">
                                                                <i class="ri-pencil-line"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-{{ $item->status == 'active' ? 'danger' : 'success' }} toggle-status-btn"
                                                                    data-id="{{ $item->id }}"
                                                                    data-status="{{ $item->status }}">
                                                                <i class="ri-{{ $item->status == 'active' ? 'close' : 'check' }}-fill align-bottom"></i>
                                                            </button>
                                                            <a href="javascript:void(0)" onclick="showAvailability({{ $item->id }})" class="text-info" title="Lihat Availability">
                                                                <i class="fas fa-map-marker-alt"></i>
                                                            </a>
                                                        </div>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal-item" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">@lang('translation.item.add')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-scroll">
                    <form id="itemForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="item_id" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">@lang('translation.item.category')</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">@lang('translation.item.select_category')</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sub_category_id" class="form-label">@lang('translation.item.sub_category')</label>
                                    <select class="form-select" id="sub_category_id" name="sub_category_id" required>
                                        <option value="">@lang('translation.item.select_sub_category')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sku" class="form-label">@lang('translation.item.sku')</label>
                                    <input type="text" class="form-control" id="sku" name="sku" required maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">@lang('translation.item.name')</label>
                                    <input type="text" class="form-control" id="name" name="name" required maxlength="100">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">@lang('translation.item.description')</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="small_unit_id" class="form-label">@lang('translation.item.small_unit')</label>
                                    <select class="form-select" id="small_unit_id" name="small_unit_id" required>
                                        <option value="">@lang('translation.item.select_unit')</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="medium_unit_id" class="form-label">@lang('translation.item.medium_unit')</label>
                                    <select class="form-select" id="medium_unit_id" name="medium_unit_id" required>
                                        <option value="">@lang('translation.item.select_unit')</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="large_unit_id" class="form-label">@lang('translation.item.large_unit')</label>
                                    <select class="form-select" id="large_unit_id" name="large_unit_id" required>
                                        <option value="">@lang('translation.item.select_unit')</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="medium_conversion_qty" class="form-label">@lang('translation.item.medium_conversion_qty')</label>
                                    <input type="number" class="form-control" id="medium_conversion_qty" name="medium_conversion_qty" required min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="small_conversion_qty" class="form-label">@lang('translation.item.small_conversion_qty')</label>
                                    <input type="number" class="form-control" id="small_conversion_qty" name="small_conversion_qty" required min="0" step="0.01">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="specification" class="form-label">Spesifikasi</label>
                            <textarea class="form-control" id="specification" name="specification" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">Gambar Produk</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                            <div id="imagePreviewContainer" class="d-flex flex-wrap gap-2 mt-2">
                                <!-- Preview gambar akan ditampilkan di sini -->
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">@lang('translation.item.prices')</label>
                            <div class="price-container">
                                <div class="price-row mb-3">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <select name="region_id[]" class="form-select" required>
                                                <option value="">Pilih Region</option>
                                                @foreach($regions as $region)
                                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="number" name="price[]" class="form-control" required min="0" step="0.01" placeholder="Harga">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-price-row">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-3">
                                <button type="button" class="btn btn-success add-price-row">
                                    <i class="ri-add-line"></i> Tambah Harga
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">@lang('translation.item.availability')</label>
                            <div class="mb-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="availability_type" id="availAll" value="all" checked>
                                    <label class="form-check-label" for="availAll">Semua</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="availability_type" id="availRegion" value="region">
                                    <label class="form-check-label" for="availRegion">Region</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="availability_type" id="availOutlet" value="outlet">
                                    <label class="form-check-label" for="availOutlet">Outlet</label>
                                </div>
                            </div>

                            <!-- Region Selection -->
                            <div id="regionSelection" class="mb-3" style="display:none;">
                                <label class="form-label">Pilih Region</label>
                                <div class="row" style="max-height: 200px; overflow-y: auto;">
                                    @foreach($regions as $region)
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="region_ids[]" 
                                                   value="{{ $region->id }}" id="region{{ $region->id }}">
                                            <label class="form-check-label" for="region{{ $region->id }}">
                                                {{ $region->name }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Outlet Selection -->
                            <div id="outletSelection" class="mb-3" style="display:none;">
                                <label class="form-label">Pilih Outlet</label>
                                <div class="checkbox-container">
                                    <div class="row">
                                        @if($outlets->count() > 0)
                                            @foreach($outlets as $outlet)
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="outlet_ids[]" 
                                                           value="{{ $outlet->id_outlet }}" 
                                                           id="outlet{{ $outlet->id_outlet }}">
                                                    <label class="form-check-label" for="outlet{{ $outlet->id_outlet }}">
                                                        {{ $outlet->nama_outlet }}
                                                        @if($outlet->lokasi)
                                                            <small class="d-block text-muted">{{ $outlet->lokasi }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        @else
                                            <div class="col-12">
                                                <div class="alert alert-info mb-0">
                                                    Tidak ada outlet yang tersedia
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveBtn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal prices -->
    <div class="modal fade" id="modal-prices" tabindex="-1" aria-labelledby="modal-prices-title" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-prices-title">Detail Item: <span id="item-name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Prices Section -->
                    <h6 class="mb-3">Daftar Harga</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('translation.item.region_column')</th>
                                    <th>@lang('translation.item.price_column')</th>
                                </tr>
                            </thead>
                            <tbody id="prices-table-body">
                            </tbody>
                        </table>
                    </div>

                    <!-- Availability Section -->
                    <h6 class="mb-3">Availability</h6>
                    <div id="availability-content">
                    </div>

                  
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('translation.item.close')</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan modal untuk upload dan preview -->
    <div class="modal fade" id="modal-import" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('translation.import_preview')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">@lang('translation.select_file')</label>
                            <input type="file" class="form-control" name="file" accept=".xlsx,.xls">
                        </div>
                    </form>
                    
                    <div id="previewArea" style="display:none">
                        <h6>@lang('translation.total_data'): <span id="totalData">0</span></h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Nama</th>
                                        <th>Kategori</th>
                                        <th>Sub Kategori</th>
                                        <th>Satuan</th>
                                        <th>Harga</th>
                                    </tr>
                                </thead>
                                <tbody id="previewBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('translation.close')</button>
                    <button type="button" class="btn btn-success" id="importBtn" style="display:none">
                        @lang('translation.import')
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update loading overlay dengan progress bar -->
    <div id="loading-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
        <div class="d-flex flex-column justify-content-center align-items-center h-100">
            <div class="text-light mb-3">Mengimport data...</div>
            <div class="progress w-50">
                <div id="import-progress" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%"></div>
            </div>
            <div class="text-light mt-2">
                <span id="current-progress">0</span> dari <span id="total-items">0</span> data
            </div>
        </div>
    </div>

    <!-- Tambahkan modal untuk menampilkan availability -->
    <div class="modal fade" id="modal-availability" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Item Availability</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 id="item-name" class="font-weight-bold mb-3"></h6>
                    <div id="availability-content">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan modal untuk preview gambar -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gambar <span id="image-item-name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <!-- Images will be inserted here by JavaScript -->
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-center gap-2 mb-3">
                            <button type="button" class="btn btn-primary download-current-image">
                                <i class="ri-download-line me-1"></i> Download Gambar Ini
                            </button>
                        </div>
                        <div class="image-thumbnails d-flex gap-2 overflow-auto py-2">
                            <!-- Thumbnails will be inserted here by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal prices - hapus bagian deskripsi & spesifikasi -->
    <div class="modal fade" id="priceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Harga <span id="price-item-name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Price List -->
                    <h6 class="mb-3">Daftar Harga</h6>
                    <div id="price-list"></div>

                    <!-- Availability List -->
                    <h6 class="mb-3 mt-4">Ketersediaan</h6>
                    <div id="availability-list"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan modal ini di luar tabel, tapi masih dalam halaman yang sama -->
    <div class="modal fade" id="modal-images" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="imageCarousel" class="carousel slide">
                        <div class="carousel-inner">
                            <!-- Images will be inserted here -->
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                    <div class="image-thumbnails d-flex justify-content-center gap-2 p-3 bg-light">
                        <!-- Thumbnails will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan ini sebelum outlet selection untuk debugging -->
    @php
        \Log::info('Jumlah outlet di view: ' . $outlets->count());
        if($outlets->count() > 0) {
            \Log::info('Contoh outlet pertama: ' . json_encode($outlets->first()));
        }
    @endphp

    <!-- Modal sederhana untuk preview images -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Image Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="image-grid d-flex flex-wrap gap-3 justify-content-center">
                        <!-- Images will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            let table = $('#datatable').DataTable({
                dom: 't<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                ordering: true,
                pageLength: 10,
                language: {
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "<i class='ri-arrow-left-s-line'></i>",
                        next: "<i class='ri-arrow-right-s-line'></i>",
                    }
                }
            });

            // Search functionality
            $('.search').keyup(function() {
                table.search($(this).val()).draw();
            });

            // Toggle status switch yang diperbaiki
            $('#statusToggle').on('change', function() {
                let showActive = $(this).prop('checked');
                $('#statusLabel').text(showActive ? '@lang("translation.item.show_active")' : '@lang("translation.item.show_inactive")');
                
                // Reset existing filters
                $.fn.dataTable.ext.search = [];
                
                // Add new filter
                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        let statusCell = $(table.cell(dataIndex, 9).node()); // Menggunakan cell API
                        let isActive = statusCell.find('.badge').hasClass('bg-success');
                        return showActive ? isActive : !isActive;
                    }
                );
                
                table.draw();
            });

            // Set initial state dan trigger filter
            let initialShowActive = true; // atau sesuaikan dengan kebutuhan default
            $('#statusToggle').prop('checked', initialShowActive).trigger('change');

            // Load sub categories when category is selected
            $('#category_id').change(function() {
                let categoryId = $(this).val();
                if (categoryId) {
                    $.ajax({
                        url: "{{ route('items.get-sub-categories') }}",
                        type: 'GET',
                        data: { category_id: categoryId },
                        success: function(data) {
                            let options = '<option value="">@lang("translation.item.select_sub_category")</option>';
                            data.forEach(function(subCategory) {
                                options += `<option value="${subCategory.id}">${subCategory.name}</option>`;
                            });
                            $('#sub_category_id').html(options);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            console.error('Response:', xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Gagal mengambil data sub kategori'
                            });
                        }
                    });
                } else {
                    $('#sub_category_id').html('<option value="">@lang("translation.item.select_sub_category")</option>');
                }
            });

            // Add price row
            $('#add-price').click(function() {
                let priceRow = `
                    <div class="row price-row">
                        <div class="col-md-5">
                            <select class="form-select" name="prices[][region_id]" required>
                                <option value="">@lang('translation.item.region')</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="number" class="form-control" name="prices[][price]" required min="0" step="0.01" placeholder="@lang('translation.item.price')">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-danger remove-price">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#price-container').append(priceRow);
            });

            // Remove price row
            $(document).on('click', '.remove-price', function() {
                $(this).closest('.price-row').remove();
            });

            // Reset form dengan benar
            function resetForm() {
                $('#itemForm')[0].reset();
                uploadedFiles = [];
                $('#imagePreviewContainer').empty();
            }

            // Handle file input change dengan validasi
            let uploadedFiles = [];
            $('#images').on('change', function(e) {
                const files = Array.from(e.target.files);
                const maxSize = 2048 * 1024; // 2MB
                
                files.forEach(file => {
                    if (!file.type.startsWith('image/')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: `File ${file.name} bukan gambar yang valid`
                        });
                        return;
                    }
                    
                    if (file.size > maxSize) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: `File ${file.name} terlalu besar (max 2MB)`
                        });
                        return;
                    }
                    
                    uploadedFiles.push(file);
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = `
                            <div class="image-preview-wrapper">
                                <img src="${e.target.result}" class="image-preview" alt="Preview">
                                <div class="image-preview-overlay">
                                    <i class="ri-delete-bin-line delete-image"></i>
                                </div>
                            </div>
                        `;
                        $('#imagePreviewContainer').append(preview);
                    };
                    reader.readAsDataURL(file);
                });
                
                // Reset input file agar bisa upload file yang sama berulang kali
                this.value = '';
            });

            // Reset form saat modal ditutup
            $('#modal-item').on('hidden.bs.modal', function() {
                resetForm();
            });

            // Handle image deletion dengan index yang benar
            $(document).on('click', '.delete-image', function() {
                const index = $(this).closest('.image-preview-wrapper').index();
                uploadedFiles.splice(index, 1);
                $(this).closest('.image-preview-wrapper').remove();
            });

            // Modify form submission
            $('#itemForm').on('submit', function(e) {
                e.preventDefault();
                
                // Gunakan FormData untuk handling file upload
                let formData = new FormData(this);
                const itemId = formData.get('id');
                
                if (itemId) {
                    formData.append('_method', 'PUT');
                }

                // Handle prices
                let prices = [];
                $('.price-row').each(function() {
                    const regionId = $(this).find('select[name="region_id[]"]').val();
                    const price = $(this).find('input[name="price[]"]').val();
                    
                    if (regionId && price) {
                        prices.push({
                            region_id: regionId,
                            price: parseFloat(price.replace(/[^0-9.]/g, ''))
                        });
                    }
                });
                formData.append('prices', JSON.stringify(prices));

                // Handle availability
                const availabilityType = $('[name="availability_type"]:checked').val() || 'all';
                formData.set('availability_type', availabilityType);

                if (availabilityType === 'region') {
                    let regionIds = [];
                    $('[name="region_ids[]"]:checked').each(function() {
                        regionIds.push($(this).val());
                    });
                    formData.delete('region_ids[]');
                    regionIds.forEach(id => {
                        formData.append('region_ids[]', id);
                    });
                } else if (availabilityType === 'outlet') {
                    let outletIds = [];
                    $('[name="outlet_ids[]"]:checked').each(function() {
                        outletIds.push($(this).val());
                    });
                    formData.delete('outlet_ids[]');
                    outletIds.forEach(id => {
                        formData.append('outlet_ids[]', id);
                    });
                }

                // Handle images
                if (uploadedFiles && uploadedFiles.length > 0) {
                    formData.delete('images[]'); // Hapus images yang ada
                    uploadedFiles.forEach(file => {
                        formData.append('images[]', file);
                    });
                }

                // Log formData untuk debugging
                console.log('Files being sent:', uploadedFiles);
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }

                $.ajax({
                    url: itemId ? `/items/${itemId}` : '/items',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.success) {
                            $('#modal-item').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log('Error response:', xhr.responseJSON);
                        let errors = xhr.responseJSON?.errors;
                        let errorMessage = '';
                        
                        if (errors) {
                            Object.entries(errors).forEach(([key, messages]) => {
                                errorMessage += `${key}: ${messages.join(', ')}\n`;
                            });
                        } else {
                            errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    }
                });
            });

            // Edit button handler
            $(document).on('click', '.edit-btn', function() {
                let item = $(this).data('item');
                let prices = $(this).data('prices');
                let availabilities = $(this).data('availabilities');
                
                console.log('Item data:', item);
                console.log('Prices data:', prices);
                console.log('Availabilities data:', availabilities);

                // Reset form
                $('#itemForm')[0].reset();
                
                // Set item data
                $('#item_id').val(item.id);
                $('#category_id').val(item.category_id);
                
                // Load sub categories
                setTimeout(() => {
                    loadSubCategories(item.category_id, item.sub_category_id);
                }, 100);
                
                // Set other fields...
                $('#sku').val(item.sku);
                $('#name').val(item.name);
                $('#description').val(item.description);
                $('#specification').val(item.specification);
                $('#small_unit_id').val(item.small_unit_id);
                $('#medium_unit_id').val(item.medium_unit_id);
                $('#large_unit_id').val(item.large_unit_id);
                $('#medium_conversion_qty').val(item.medium_conversion_qty);
                $('#small_conversion_qty').val(item.small_conversion_qty);

                // Set availability data
                if (availabilities && availabilities.length > 0) {
                    let availability = availabilities[0];
                    
                    // Set radio button
                    $(`input[name="availability_type"][value="${availability.availability_type}"]`).prop('checked', true);
                    
                    // Show appropriate container based on type
                    toggleAvailabilityContainer(availability.availability_type);
                    
                    // Set checkboxes based on type
                    if (availability.availability_type === 'region') {
                        let regionIds = availabilities.map(a => a.region_id);
                        $('[name="region_ids[]"]').each(function() {
                            $(this).prop('checked', regionIds.includes(parseInt($(this).val())));
                        });
                    } else if (availability.availability_type === 'outlet') {
                        let outletIds = availabilities.map(a => a.outlet_id);
                        $('[name="outlet_ids[]"]').each(function() {
                            $(this).prop('checked', outletIds.includes(parseInt($(this).val())));
                        });
                    }
                }

                // Set prices
                $('.price-row:not(:first)').remove();
                $('.price-row:first select').val('');
                $('.price-row:first input').val('');

                if (prices && prices.length > 0) {
                    prices.forEach((price, index) => {
                        if (index === 0) {
                            $('.price-row:first select[name="region_id[]"]').val(price.region_id);
                            $('.price-row:first input[name="price[]"]').val(price.price);
                        } else {
                            let newRow = $('.price-row:first').clone();
                            newRow.find('select[name="region_id[]"]').val(price.region_id);
                            newRow.find('input[name="price[]"]').val(price.price);
                            newRow.appendTo('.price-container');
                        }
                    });
                }

                // Update modal title and show modal
                $('#modal-title').text('Edit Item');
                $('#modal-item').modal('show');
            });

            // Add price row button handler
            $(document).on('click', '.add-price-row', function() {
                let newRow = $('.price-row:first').clone();
                newRow.find('select').val('');
                newRow.find('input').val('');
                newRow.appendTo('.price-container');
            });

            // Remove price row button handler
            $(document).on('click', '.remove-price-row', function() {
                if ($('.price-row').length > 1) {
                    $(this).closest('.price-row').remove();
                }
            });

            // Handle toggle status button
            $(document).on('click', '.toggle-status-btn', function() {
                let id = $(this).data('id');
                let currentStatus = $(this).data('status');
                let newStatus = currentStatus === 'active' ? 'inactive' : 'active';
                let confirmMessage = currentStatus === 'active' 
                    ? '@lang("translation.item.confirm_deactivate")'
                    : '@lang("translation.item.confirm_activate")';

                Swal.fire({
                    title: 'Konfirmasi',
                    text: confirmMessage,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/items/${id}/toggle-status`,
                            method: 'PUT',
                            data: {
                                status: newStatus,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if(response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        location.reload();
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                                });
                            }
                        });
                    }
                });
            });

            // Handle view prices button
            $(document).on('click', '.view-prices-btn', function() {
                let itemId = $(this).data('id');
                let itemName = $(this).data('name');
                let prices = $(this).data('prices');
                
                $('#item-name').text(itemName);
                
                // Populate prices table
                let tableBody = $('#prices-table-body');
                tableBody.empty();
                
                prices.forEach(price => {
                    tableBody.append(`
                        <tr>
                            <td>${price.region.name}</td>
                            <td class="text-end">${formatRupiah(price.price)}</td>
                        </tr>
                    `);
                });

                // Fetch and populate availability data
                $.ajax({
                    url: `/items/${itemId}/availability`,
                    method: 'GET',
                    success: function(response) {
                        let content = '';
                        
                        if (response.availabilities.length === 0) {
                            content = '<div class="alert alert-info">Tidak ada data availability</div>';
                        } else {
                            content = '<div class="table-responsive"><table class="table table-bordered">';
                            content += '<thead><tr><th>Tipe</th><th>Detail</th></tr></thead><tbody>';
                            
                            response.availabilities.forEach(function(availability) {
                                let detail = '';
                                if (availability.availability_type === 'all') {
                                    detail = 'Semua Outlet';
                                } else if (availability.availability_type === 'region') {
                                    detail = `Region: ${availability.region_name}`;
                                } else if (availability.availability_type === 'outlet') {
                                    detail = `Outlet: ${availability.outlet_name}`;
                                }
                                
                                content += `<tr>
                                    <td>${availability.availability_type.toUpperCase()}</td>
                                    <td>${detail}</td>
                                </tr>`;
                            });
                            
                            content += '</tbody></table></div>';
                        }
                        
                        $('#availability-content').html(content);
                    },
                    error: function(xhr) {
                        $('#availability-content').html(
                            '<div class="alert alert-danger">Gagal memuat data availability</div>'
                        );
                    }
                });
                
                $('#modal-prices').modal('show');
            });

            function formatRupiah(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(amount);
            }

            $('#uploadBtn').click(function() {
                $('#modal-import').modal('show');
            });

            $('input[name="file"]').change(function() {
                let formData = new FormData($('#uploadForm')[0]);
                
                $.ajax({
                    url: "{{ route('items.preview') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#totalData').text(response.total);
                        
                        let tbody = $('#previewBody');
                        tbody.empty();
                        
                        response.data.forEach(function(item) {
                            let prices = item.prices.map(function(price) {
                                return `Region ${price.region_id}: Rp ${Number(price.price).toLocaleString('id-ID')}`;
                            }).join('<br>');
                            
                            tbody.append(`
                                <tr>
                                    <td>${item.sku}</td>
                                    <td>${item.name}</td>
                                    <td>${item.category_id}</td>
                                    <td>${item.sub_category_id}</td>
                                    <td>
                                        Small: ${item.small_unit_id}<br>
                                        Medium: ${item.medium_unit_id}<br>
                                        Large: ${item.large_unit_id}
                                    </td>
                                    <td>${prices}</td>
                                </tr>
                            `);
                        });
                        
                        $('#previewArea').show();
                        $('#importBtn').show();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                        });
                    }
                });
            });

            $('#importBtn').click(function() {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: '@lang("translation.confirm_import")',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const preview = @json(session('import_data') ?? []);
                        const totalItems = preview.length;
                        let currentItem = 0;
                        
                        // Reset dan tampilkan loading dengan progress
                        $('#import-progress').css('width', '0%');
                        $('#current-progress').text('0');
                        $('#total-items').text(totalItems);
                        $('#loading-overlay').show();
                        
                        // Simulasi progress selama proses import
                        const progressInterval = setInterval(() => {
                            if (currentItem < totalItems) {
                                currentItem++;
                                const progress = (currentItem / totalItems) * 90; // Max 90% sebelum selesai
                                $('#import-progress').css('width', progress + '%');
                                $('#current-progress').text(currentItem);
                            }
                        }, 500);
                        
                        $.ajax({
                            url: "{{ route('items.import') }}",
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                clearInterval(progressInterval);
                                
                                // Set progress 100% when complete
                                $('#import-progress').css('width', '100%');
                                $('#current-progress').text(totalItems);
                                
                                setTimeout(() => {
                                    $('#loading-overlay').hide();
                                    
                                    if(response.success) {
                                        $('#modal-import').modal('hide');
                                        
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: '@lang("translation.success_import")',
                                            showConfirmButton: false,
                                            timer: 1500
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: response.message || '@lang("translation.failed_import")',
                                            showConfirmButton: true
                                        });
                                    }
                                }, 500);
                            },
                            error: function(xhr) {
                                clearInterval(progressInterval);
                                $('#loading-overlay').hide();
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || '@lang("translation.failed_import")',
                                    showConfirmButton: true
                                });
                            }
                        });
                    }
                });
            });

            // Fungsi untuk toggle container availability
            function toggleAvailabilityContainer(type) {
                console.log('Toggling container for type:', type);
                
                // Hide all containers first
                $('#regionSelection, #outletSelection').hide();
                
                // Uncheck all checkboxes
                $('input[name="region_ids[]"], input[name="outlet_ids[]"]').prop('checked', false);
                
                // Show appropriate container
                if (type === 'region') {
                    $('#regionSelection').show();
                } else if (type === 'outlet') {
                    $('#outletSelection').show();
                }
            }

            // Update radio button handler
            $('input[name="availability_type"]').on('change', function() {
                toggleAvailabilityContainer($(this).val());
            });

            // Debug untuk memastikan outlet data tersedia
            console.log('Jumlah outlet:', $('input[name="outlet_ids[]"]').length);

            // Update reset form
            $('#modal-item').on('hidden.bs.modal', function () {
                $('#itemForm')[0].reset();
                $('#item_id').val('');
                $('#modal-title').text('@lang("translation.item.add")');
                $('#price-container').empty();
                $('#sub_category_id').html('<option value="">@lang("translation.item.select_sub_category")</option>');
                $('input[name="region_ids[]"]').prop('checked', false);
                $('input[name="outlet_ids[]"]').prop('checked', false);
            });

            console.log('Debug outlets:', @json($outlets));

            // Debug untuk melihat nilai yang dipilih
            $('input[name="availability_type"]').on('change', function() {
                console.log('Selected availability type:', $(this).val());
                if ($(this).val() === 'outlet') {
                    console.log('Selected outlets:', $('input[name="outlet_ids[]"]:checked').map(function() {
                        return $(this).val();
                    }).get());
                }
            });

            // Cache untuk menyimpan data gambar
            const imageCache = new Map();
            
            // Handle click pada tombol view all images
            $('.view-all-images').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const $button = $(this);
                const images = $button.data('images');
                
                console.log('Preview clicked, images:', images); // Debug log
                
                // Reset modal content
                $('#imageCarousel .carousel-inner').empty();
                $('.image-thumbnails').empty();
                
                // Tambahkan semua gambar ke carousel dan thumbnails
                images.forEach((image, index) => {
                    // Add to carousel
                    const carouselItem = $('<div>').addClass('carousel-item');
                    if (index === 0) carouselItem.addClass('active');
                    
                    const img = $('<img>')
                        .addClass('d-block w-100')
                        .attr('src', image.path)
                        .attr('alt', 'Preview')
                        .css({
                            'max-height': '500px',
                            'object-fit': 'contain'
                        });
                    
                    carouselItem.append(img);
                    $('#imageCarousel .carousel-inner').append(carouselItem);
                    
                    // Add to thumbnails
                    const thumbnail = $('<img>')
                        .attr('src', image.path)
                        .attr('alt', `Thumbnail ${index + 1}`)
                        .addClass(index === 0 ? 'active' : '')
                        .click(function() {
                            $('#imageCarousel').carousel(index);
                            $('.image-thumbnails img').removeClass('active');
                            $(this).addClass('active');
                        });
                    
                    $('.image-thumbnails').append(thumbnail);
                });

                // Show modal
                $('#modal-images').modal('show');
            });
            
            // Handle thumbnail clicks
            $(document).on('click', '.thumbnail-img', function() {
                const index = $(this).data('bs-slide-to');
                $('#imageCarousel').carousel(index);
                // Update thumbnail border
                $('.thumbnail-img').css('border', 'none');
                $(this).css('border', '2px solid #0d6efd');
            });
            
            // Update thumbnail border when carousel slides
            $('#imageCarousel').on('slide.bs.carousel', function(e) {
                $('.thumbnail-img').css('border', 'none');
                $(`.thumbnail-img[data-bs-slide-to="${e.to}"]`).css('border', '2px solid #0d6efd');
            });

            $('.toggle-content').click(function() {
                const target = $(this).data('target');
                const isShowLess = $(this).data('show-less');
                const cell = $('.' + target);
                
                // Tutup semua content-full yang terbuka lainnya
                if (!isShowLess) {
                    $('.content-full').hide();
                    $('.toggle-content').text('Lihat selengkapnya').data('show-less', false);
                }
                
                if (isShowLess) {
                    // Show less
                    cell.find('.content-truncate').show();
                    cell.find('.content-full').hide();
                    $(this).text('Lihat selengkapnya');
                    $(this).data('show-less', true);
                } else {
                    // Show more
                    cell.find('.content-truncate').hide();
                    cell.find('.content-full').show();
                    $(this).text('Lihat lebih sedikit');
                    $(this).data('show-less', false);
                }
            });

            // Tutup expanded content ketika klik di luar
            $(document).click(function(event) {
                if (!$(event.target).closest('.content-cell').length && 
                    !$(event.target).hasClass('toggle-content')) {
                    $('.content-full').hide();
                    $('.content-truncate').show();
                    $('.toggle-content').text('Lihat selengkapnya').data('show-less', false);
                }
            });
        });

        function showAvailability(itemId) {
            $('#modal-availability').modal('show');
            
            $.ajax({
                url: `/items/${itemId}/availability`,
                method: 'GET',
                success: function(response) {
                    $('#item-name').text(response.item_name);
                    let content = '';
                    
                    if (response.availabilities.length === 0) {
                        content = '<div class="alert alert-info">Tidak ada data availability</div>';
                    } else {
                        content = '<div class="table-responsive"><table class="table table-bordered">';
                        content += '<thead><tr><th>Tipe</th><th>Detail</th></tr></thead><tbody>';
                        
                        response.availabilities.forEach(function(availability) {
                            let detail = '';
                            if (availability.availability_type === 'all') {
                                detail = 'Semua Outlet';
                            } else if (availability.availability_type === 'region') {
                                detail = `Region: ${availability.region_name}`;
                            } else if (availability.availability_type === 'outlet') {
                                detail = `Outlet: ${availability.outlet_name}`;
                            }
                            
                            content += `<tr>
                                <td>${availability.availability_type.toUpperCase()}</td>
                                <td>${detail}</td>
                            </tr>`;
                        });
                        
                        content += '</tbody></table></div>';
                    }
                    
                    $('#availability-content').html(content);
                },
                error: function(xhr) {
                    $('#availability-content').html(
                        '<div class="alert alert-danger">Gagal memuat data availability</div>'
                    );
                }
            });
        }

        // Definisikan fungsi loadSubCategories
        function loadSubCategories(categoryId, selectedId = null) {
            if (categoryId) {
                $.ajax({
                    url: `/categories/${categoryId}/sub-categories`,
                    method: 'GET',
                    success: function(response) {
                        let options = '<option value="">Pilih Sub Kategori</option>';
                        response.forEach(function(subCategory) {
                            options += `<option value="${subCategory.id}" ${selectedId == subCategory.id ? 'selected' : ''}>${subCategory.name}</option>`;
                        });
                        $('#sub_category_id').html(options);
                    },
                    error: function(xhr) {
                        console.error('Error loading sub categories:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat sub kategori'
                        });
                    }
                });
            } else {
                $('#sub_category_id').html('<option value="">Pilih Sub Kategori</option>');
            }
        }

        // Event handler untuk perubahan kategori
        $('#category_id').on('change', function() {
            loadSubCategories($(this).val());
        });

        function showPriceModal(itemId) {
            console.log('Opening modal for item ID:', itemId); // Debug item ID

            $.ajax({
                url: `/items/${itemId}/prices`,
                method: 'GET',
                beforeSend: function() {
                    console.log('Sending request to:', `/items/${itemId}/prices`);
                },
                success: function(response) {
                    console.log('Full AJAX Response:', response);
                    
                    // Basic data
                    $('#price-item-name').text(response.item_name || '');
                    
                    // Prices
                    let priceHtml = '<div class="table-responsive"><table class="table table-bordered">';
                    priceHtml += '<thead><tr><th>Region</th><th>Harga</th></tr></thead><tbody>';
                    if (response.prices && response.prices.length) {
                        response.prices.forEach(function(price) {
                            priceHtml += `<tr><td>${price.region_name}</td><td>Rp ${price.price}</td></tr>`;
                        });
                    }
                    priceHtml += '</tbody></table></div>';
                    $('#price-list').html(priceHtml);
                    
                    // Availabilities
                    let availHtml = '<ul class="list-group">';
                    if (response.availabilities && response.availabilities.length) {
                        response.availabilities.forEach(function(avail) {
                            let availText = '';
                            if (avail.availability_type === 'all') {
                                availText = 'Semua Region dan Outlet';
                            } else if (avail.availability_type === 'region') {
                                availText = `Region: ${avail.region_name}`;
                            } else {
                                availText = `Outlet: ${avail.outlet_name}`;
                            }
                            availHtml += `<li class="list-group-item">${availText}</li>`;
                        });
                    }
                    availHtml += '</ul>';
                    $('#availability-list').html(availHtml);
                    
                    // Description & Specification dengan debug info
                    console.log('Description data:', {
                        raw: response.raw_description,
                        formatted: response.description
                    });
                    console.log('Specification data:', {
                        raw: response.raw_specification,
                        formatted: response.specification
                    });

                    // Tampilkan data description
                    if (response.description || response.raw_description) {
                        $('.description-content').html(`
                            <div class="border p-2 rounded">
                                ${response.description || response.raw_description || '<em>Tidak ada deskripsi</em>'}
                            </div>
                        `);
                    } else {
                        $('.description-content').html('<em class="text-muted">Tidak ada deskripsi</em>');
                    }

                    // Tampilkan data specification
                    if (response.specification || response.raw_specification) {
                        $('.specification-content').html(`
                            <div class="border p-2 rounded">
                                ${response.specification || response.raw_specification || '<em>Tidak ada spesifikasi</em>'}
                            </div>
                        `);
                    } else {
                        $('.specification-content').html('<em class="text-muted">Tidak ada spesifikasi</em>');
                    }

                    // Tampilkan modal
                    $('#priceModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: `Gagal memuat data: ${error}`
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi modal
            const imageModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
            
            // Handle click event
            document.addEventListener('click', function(e) {
                if (e.target.closest('.show-images')) {
                    e.preventDefault();
                    const button = e.target.closest('.show-images');
                    const images = JSON.parse(button.dataset.images);
                    
                    // Clear existing images
                    const imageGrid = document.querySelector('.image-grid');
                    imageGrid.innerHTML = '';
                    
                    // Add all images
                    images.forEach(image => {
                        const imgElement = document.createElement('div');
                        imgElement.className = 'image-preview-item';
                        imgElement.innerHTML = `
                            <img src="${image.path}" 
                                 alt="Preview" 
                                 class="img-fluid rounded" 
                                 style="max-width: 200px; height: 200px; object-fit: cover; cursor: pointer"
                                 onclick="window.open('${image.path}', '_blank')">
                        `;
                        imageGrid.appendChild(imgElement);
                    });
                    
                    // Show modal
                    imageModal.show();
                }
            });

            // Buat main preview juga bisa diklik untuk membuka di tab baru
            document.querySelectorAll('.main-preview').forEach(img => {
                img.addEventListener('click', function() {
                    window.open(this.src, '_blank');
                });
            });
        });
    </script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 