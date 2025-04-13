@extends('layouts.master')
@section('title')
    Dashboard Maintenance
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/datatables/datatables.min.css') }}" rel="stylesheet">
    <style>
        /* Pastikan video dalam modal responsive */
        .modal .ratio-16x9 {
            width: 100%;
        }
        
        /* Pointer untuk judul evidence */
        .evidence-title {
            cursor: pointer;
            color: var(--tb-primary);
            text-decoration: none;
        }
        
        .evidence-title:hover {
            text-decoration: underline;
        }
        
        /* Style untuk carousel thumbnail */
        .carousel-indicators {
            margin-bottom: 0;
            position: static;
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
        }
        
        .carousel-indicators button {
            width: auto !important;
            height: auto !important;
            opacity: 0.7;
            border: 2px solid transparent !important;
            transition: all 0.3s;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .carousel-indicators button.active {
            opacity: 1;
            border-color: var(--tb-primary) !important;
        }
        
        .carousel-indicators .thumbnail-preview {
            width: 50px;
            height: 30px;
            background-color: #f8f9fa;
            border-radius: 0.2rem;
        }
        
        /* Mengatur ukuran kontrol carousel */
        .carousel-control-prev, .carousel-control-next {
            width: 5%;
            background-color: rgba(0,0,0,0.1);
            height: 60px;
            top: 50%;
            transform: translateY(-50%);
            border-radius: 0.3rem;
        }
        
        /* Style untuk thumbnail slider */
        .thumbnail-slider-container {
            position: relative;
            padding: 0 40px;
            max-width: 80%;
            margin: 0 auto;
        }
        
        .thumbnail-slider-wrapper {
            overflow: hidden;
            position: relative;
        }
        
        .thumbnail-slider {
            display: flex;
            transition: transform 0.3s ease;
        }
        
        .thumbnail-item {
            flex: 0 0 calc(20% - 8px);
            margin: 0 4px;
            height: 60px;
            cursor: pointer;
            opacity: 0.6;
            border: 2px solid transparent;
            border-radius: 4px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .thumbnail-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 2px;
        }
        
        .thumbnail-item.active {
            opacity: 1;
            border-color: var(--tb-primary);
        }
        
        .video-thumbnail {
            width: 100%;
            height: 100%;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 2px;
        }
        
        .thumbnail-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
            background-color: rgba(0,0,0,0.1);
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #555;
            cursor: pointer;
            z-index: 2;
        }
        
        .thumbnail-nav.prev {
            left: 0;
        }
        
        .thumbnail-nav.next {
            right: 0;
        }
        
        .thumbnail-nav:hover {
            background-color: rgba(0,0,0,0.2);
        }
        
        /* Styling untuk media carousel */
        .mediaSwiper {
            padding-bottom: 50px;
        }
        
        .mediaSwiper .swiper-slide {
            height: 220px;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .media-card {
            width: 100%;
            height: 100%;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .media-card img, .video-thumbnail {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .video-thumbnail {
            background-color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .play-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 3rem;
            opacity: 0.8;
        }
        
        .media-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 100%);
            padding: 10px;
            color: white;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        
        .media-info {
            font-size: 0.875rem;
        }
        
        .media-preview-container {
            max-height: 70vh;
        }
        
        #mediaContent img, #mediaContent video {
            max-width: 100%;
            max-height: 60vh;
            object-fit: contain;
        }
        
        .swiper-slide {
            transition: transform 0.3s;
            opacity: 0.7;
        }
        
        .swiper-slide-active {
            transform: scale(1.1);
            opacity: 1;
            z-index: 10;
        }
        
        .swiper {
            padding: 20px 0;
        }
        
        .media-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .swiper-slide-active .media-card {
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        /* Styling untuk Evidence Terbaru */
        .mySwiper {
            position: relative;
            padding-bottom: 30px; /* Beri ruang untuk pagination */
        }
        
        /* Styling untuk Media Gallery */
        .mediaSwiper {
            position: relative;
            padding: 20px 0 40px;
        }
        
        /* Efek "aktif" untuk slide tengah */
        .mediaSwiper .swiper-slide {
            transition: transform 0.3s;
            opacity: 0.7;
        }
        
        .mediaSwiper .swiper-slide-active {
            transform: scale(1.1);
            opacity: 1;
            z-index: 10;
        }
        
        .media-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .mediaSwiper .swiper-slide-active .media-card {
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        /* Styling umum untuk kedua swiper */
        .mySwiper, .mediaSwiper {
            padding: 20px 0 50px;
        }
        
        .mySwiper .swiper-slide, .mediaSwiper .swiper-slide {
            transition: transform 0.3s;
            opacity: 0.7;
            width: 280px; /* Untuk memastikan tampilan 'auto' berfungsi dengan baik */
            height: 220px;
        }
        
        .mySwiper .swiper-slide-active, .mediaSwiper .swiper-slide-active {
            transform: scale(1.1);
            opacity: 1;
            z-index: 10;
        }
        
        .media-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
            height: 100%;
            width: 100%;
        }
        
        .mySwiper .swiper-slide-active .media-card,
        .mediaSwiper .swiper-slide-active .media-card {
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        /* Memastikan ukuran konten dalam slider konsisten */
        .media-card img, .video-thumbnail {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Styling untuk navigasi */
        .swiper-button-next, .swiper-button-prev {
            color: var(--tb-primary);
            background-color: rgba(255, 255, 255, 0.8);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .swiper-button-next:after, .swiper-button-prev:after {
            font-size: 18px;
        }
        
        /* Styling untuk pagination */
        .swiper-pagination-bullet {
            background: var(--tb-primary);
            opacity: 0.5;
        }
        
        .swiper-pagination-bullet-active {
            opacity: 1;
        }

        /* Styling untuk Gallery Modal */
        .gallery-container {
            margin-left: -10px;
            margin-right: -10px;
        }

        .gallery-item {
            padding: 0 10px;
        }

        .gallery-card {
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: 100%;
        }

        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }

        .gallery-img-wrapper {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 100%; /* Aspect ratio 1:1 */
            overflow: hidden;
        }

        .gallery-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .video-thumbnail {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gallery-date {
            position: absolute;
            bottom: 5px;
            right: 5px;
            z-index: 2;
        }

        .fs-xs {
            font-size: 0.75rem;
        }

        #mediaSearchInput::placeholder {
            opacity: 0.6;
        }

        /* Media preview modal styles */
        #galleryMediaContent img {
            max-height: 70vh;
            max-width: 100%;
        }

        #galleryMediaContent video {
            max-height: 70vh;
            width: 100%;
        }

        /* Styling for folder structure */
        .folder {
            border-radius: 10px;
            background-color: #f8f9fa;
            cursor: pointer;
            transition: all 0.2s ease;
            height: 100%;
        }

        .folder:hover {
            background-color: #e9ecef;
            transform: translateY(-3px);
        }

        .folder-icon {
            font-size: 2.5rem;
            color: var(--tb-primary);
        }

        .folder-title {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
            color: var(--tb-body-color);
        }

        .folder-counter {
            font-size: 0.75rem;
            color: var(--tb-secondary);
        }

        .breadcrumb-item a {
            cursor: pointer;
        }

        /* Style untuk breadcrumb */
        .breadcrumb-folder {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .breadcrumb-folder .breadcrumb-item a {
            color: #495057;
            text-decoration: none;
            cursor: pointer;
        }

        .breadcrumb-folder .breadcrumb-item a:hover {
            color: #0d6efd;
            text-decoration: underline;
        }

        .breadcrumb-folder .breadcrumb-item.active a {
            color: #6c757d;
            pointer-events: none;
        }

        /* Hanya tampilkan arrow di antara item */
        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
            color: #6c757d;
        }

        /* Evidence modal styles */
        .folder {
            cursor: pointer;
            transition: all 0.2s;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        
        .folder:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-5px);
            background-color: #e9ecef;
        }
        
        .folder-title {
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }
        
        .breadcrumb-folder {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .breadcrumb-folder .breadcrumb-item a {
            cursor: pointer;
            color: #495057;
            text-decoration: none;
        }
        
        .breadcrumb-folder .breadcrumb-item a:hover {
            color: #0d6efd;
            text-decoration: underline;
        }
        
        #evidenceFolderBreadcrumb a {
            cursor: pointer !important;
        }
        
        #galleryEvidenceContent img {
            max-width: 100%;
            max-height: 70vh;
        }
        
        #galleryEvidenceContent video {
            max-width: 100%;
            max-height: 70vh;
        }

        /* Style untuk tombol back */
        #evidenceBackBtn {
            font-weight: 500;
            min-width: 100px;
            transition: all 0.2s;
        }

        #evidenceBackBtn:hover {
            background-color: #6c757d;
            color: white;
        }

        #evidenceCurrentPath {
            font-size: 0.9rem;
            padding-left: 10px;
            border-left: 2px solid #dee2e6;
            margin-left: 10px;
            display: inline-block;
            line-height: 1.2;
            vertical-align: middle;
        }
    </style>
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboards
        @endslot
        @slot('title')
            Maintenance
        @endslot
    @endcomponent

    <!-- Filter periode waktu di bagian atas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2">Data Dashboard</h6>
                            <p class="text-muted mb-0">
                                Data diambil dari: <span class="fw-medium">{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</span>
                            </p>
                        </div>
                        <div class="hstack gap-2">
                            <a href="{{ url('maintenance/dashboard?period=1d') }}" class="btn {{ $period == '1d' ? 'btn-primary' : 'btn-subtle-secondary' }} btn-sm">
                                Hari Ini
                            </a>
                            <a href="{{ url('maintenance/dashboard?period=7d') }}" class="btn {{ $period == '7d' ? 'btn-primary' : 'btn-subtle-secondary' }} btn-sm">
                                7 Hari
                            </a>
                            <a href="{{ url('maintenance/dashboard?period=30d') }}" class="btn {{ $period == '30d' ? 'btn-primary' : 'btn-subtle-secondary' }} btn-sm">
                                30 Hari
                            </a>
                            <a href="{{ url('maintenance/dashboard?period=90d') }}" class="btn {{ $period == '90d' ? 'btn-primary' : 'btn-subtle-secondary' }} btn-sm">
                                90 Hari
                            </a>
                            <a href="{{ url('maintenance/dashboard?period=1y') }}" class="btn {{ $period == '1y' ? 'btn-primary' : 'btn-subtle-secondary' }} btn-sm">
                                1 Tahun
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-5">
            <div class="row">
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="avatar-sm float-end">
                                <div class="avatar-title bg-primary-subtle text-primary fs-3xl rounded"><i
                                        class="ti ti-tool"></i></div>
                            </div>
                            <p class="fs-md text-uppercase text-muted mb-0">Total Maintenance Tasks</p>

                            <h4 class="my-4"><span class="counter-value" data-target="{{ $totalTasks }}">{{ $totalTasks }}</span></h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="avatar-sm float-end">
                                <div class="avatar-title bg-warning-subtle text-warning fs-3xl rounded"><i
                                        class="ti ti-clock"></i></div>
                            </div>
                            <p class="fs-md text-uppercase text-muted mb-0">Tasks Dalam Progress</p>

                            <h4 class="my-4"><span class="counter-value" data-target="{{ $inProgressTasks }}">{{ $inProgressTasks }}</span></h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="avatar-sm float-end">
                                <div class="avatar-title bg-success-subtle text-success fs-3xl rounded"><i
                                        class="ti ti-check"></i></div>
                            </div>
                            <p class="fs-md text-uppercase text-muted mb-0">Completed Tasks</p>

                            <h4 class="my-4"><span class="counter-value" data-target="{{ $completedTasks }}">{{ $completedTasks }}</span></h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="avatar-sm float-end">
                                <div class="avatar-title bg-secondary-subtle text-secondary fs-3xl rounded"><i
                                        class="ti ti-file-invoice"></i></div>
                            </div>
                            <p class="fs-md text-uppercase text-muted mb-0">Purchase Orders</p>

                            <h4 class="my-4"><span class="counter-value" data-target="{{ $totalPurchaseOrders }}">{{ $totalPurchaseOrders }}</span></h4>
                        </div>
                    </div>
                </div>
                
                <!-- Card untuk Task Due Date Hari Ini -->
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="avatar-sm float-end">
                                <div class="avatar-title bg-info-subtle text-info fs-3xl rounded"><i
                                        class="ti ti-calendar-event"></i></div>
                            </div>
                            <p class="fs-md text-uppercase text-muted mb-0">Due Date Hari Ini</p>

                            <h4 class="my-4"><span class="counter-value" data-target="{{ $dueTodayTasks }}">{{ $dueTodayTasks }}</span></h4>
                            <p class="text-info fs-sm mb-0">Task yang jatuh tempo hari ini</p>
                        </div>
                    </div>
                </div>
                
                <!-- Card untuk Task Overdue -->
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <p class="fs-md text-uppercase text-muted mb-0">Task Overdue</p>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#overdueTasksModal">
                                        <i class="ti ti-list me-1"></i> Lihat Detail
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="fs-2xl mb-2"><span class="counter-value" data-target="{{ $overdueTasks }}">{{ $overdueTasks }}</span></h4>
                                    <p class="text-danger fs-sm mb-0">Task yang lewat due date</p>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <div class="avatar-title bg-danger-subtle text-danger fs-3xl rounded">
                                        <i class="ti ti-alarm"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-7">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h6 class="card-title flex-grow-1 mb-0">Task Status</h6>
                            <div class="flex-shrink-0">
                                <a href="{{ route('maintenance.task-status-report') }}?period={{ $period }}" class="btn btn-subtle-info btn-sm">
                                    <i class="bi bi-file-earmark-text me-1 align-baseline"></i> Generate Reports
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="task_status_chart" 
                                data-colors='["--tb-primary", "--tb-info", "--tb-warning", "--tb-success", "--tb-danger", "--tb-secondary"]'
                                class="apex-charts" dir="ltr"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h6 class="card-title flex-grow-1 mb-0">Kategori Maintenance</h6>
                            <div class="dropdown flex-shrink-0">
                                <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <span class="text-muted fs-lg"><i class="ti ti-dots align-middle"></i></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">Hari Ini</a>
                                    <a class="dropdown-item" href="#">Minggu Ini</a>
                                    <a class="dropdown-item" href="#">Bulan Ini</a>
                                    <a class="dropdown-item" href="#">Tahun Ini</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="maintenance_categories_chart" data-colors='["--tb-primary", "--tb-success", "--tb-warning", "--tb-danger", "--tb-info"]'
                                class="apex-charts" dir="ltr"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menambahkan row untuk Tasks Terbaru -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Tasks Terbaru</h4>
                    <div class="flex-shrink-0">
                        <a href="#" class="btn btn-subtle-info btn-sm">
                            <i class="ri-list-check-line align-middle"></i> Lihat Semua Tasks
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Task Number</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Outlet</th>
                                    <th scope="col">Assigned To</th>
                                    <th scope="col">Due Date</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTasks as $task)
                                <tr>
                                    <td>
                                        <a href="#" class="fw-medium link-primary">{{ $task->task_number }}</a>
                                    </td>
                                    <td>{{ Str::limit($task->title, 30) }}</td>
                                    <td>
                                        @php
                                            $outletInfo = DB::table('tbl_data_outlet')
                                                ->where('id_outlet', $task->id_outlet)
                                                ->first();
                                                
                                            $rukoInfo = null;
                                            if ($task->id_outlet == 1) {
                                                $rukoInfo = DB::table('tbl_data_ruko')
                                                    ->where('id_ruko', $task->id_ruko)
                                                    ->first();
                                            }
                                            
                                            $location = '';
                                            if ($outletInfo) {
                                                $location = $outletInfo->nama_outlet;
                                                if ($task->id_outlet == 1 && $rukoInfo && !empty($rukoInfo->nama_ruko)) {
                                                    $location .= ' - ' . $rukoInfo->nama_ruko;
                                                }
                                            } else {
                                                $location = 'Tidak ada';
                                            }
                                        @endphp
                                        {{ $location }}
                                    </td>
                                    <td>
                                        @php
                                            $members = DB::table('maintenance_members')
                                                ->join('users', 'maintenance_members.user_id', '=', 'users.id')
                                                ->where('maintenance_members.task_id', $task->id)
                                                ->select('users.nama_lengkap')
                                                ->get();
                                                
                                            $memberInitials = [];
                                            foreach($members as $member) {
                                                $nama = $member->nama_lengkap ?? '';
                                                $inisial = '';
                                                $namaParts = explode(' ', $nama);
                                                foreach($namaParts as $part) {
                                                    if (!empty($part)) {
                                                        $inisial .= strtoupper(substr($part, 0, 1));
                                                    }
                                                }
                                                $memberInitials[] = $inisial;
                                            }
                                        @endphp
                                        
                                        @if(count($memberInitials) > 0)
                                            @foreach($memberInitials as $inisial)
                                                <span class="badge bg-primary-subtle text-primary me-1">{{ $inisial }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Tidak ada</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->due_date)
                                            @php
                                                $dueDate = \Carbon\Carbon::parse($task->due_date);
                                                $now = \Carbon\Carbon::now();
                                                $isOverdue = $dueDate < $now && $task->status != 'DONE';
                                                $isDueToday = $dueDate->isToday();
                                            @endphp
                                            
                                            @if($isOverdue)
                                                <span class="badge bg-danger-subtle text-danger">
                                                    {{ $dueDate->format('d M Y') }} (Overdue)
                                                </span>
                                            @elseif($isDueToday)
                                                <span class="badge bg-warning-subtle text-warning">
                                                    Hari Ini
                                                </span>
                                            @else
                                                {{ $dueDate->format('d M Y') }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($task->status)
                                            @case('TASK')
                                                <span class="badge bg-info-subtle text-info">To Do</span>
                                                @break
                                            @case('IN_PROGRESS')
                                                <span class="badge bg-primary-subtle text-primary">In Progress</span>
                                                @break
                                            @case('PR')
                                                <span class="badge bg-warning-subtle text-warning">PR</span>
                                                @break
                                            @case('PO')
                                                <span class="badge bg-secondary-subtle text-secondary">PO</span>
                                                @break
                                            @case('IN_REVIEW')
                                                <span class="badge bg-danger-subtle text-danger">In Review</span>
                                                @break
                                            @case('DONE')
                                                <span class="badge bg-success-subtle text-success">Done</span>
                                                @break
                                            @default
                                                <span class="badge bg-light">{{ $task->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-icon btn-primary">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="avatar-md mx-auto mb-3">
                                            <div class="avatar-title bg-light text-primary rounded-circle h1">
                                                <i class="ti ti-clipboard-check"></i>
                                            </div>
                                        </div>
                                        <h5 class="mb-1">Tidak ada task terbaru!</h5>
                                        <p class="text-muted mb-0">Belum ada task yang dibuat dalam periode ini.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-7">
            <div class="card">
                <div class="card-header d-flex align-items-center flex-wrap">
                    <h5 class="card-title mb-0 flex-grow-1">Aktivitas Maintenance</h5>
                </div>
                <div class="card-body ps-0">
                    <div id="maintenance_activity_chart" data-colors='["--tb-secondary", "--tb-success", "--tb-danger"]'
                        class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Evidence Terbaru</h4>
                    <div class="flex-shrink-0">
                        <a href="#" class="fs-sm flex-shrink-0" data-bs-toggle="modal" data-bs-target="#allEvidenceModal">
                            Lihat Semua <i class="ti ti-arrow-narrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Swiper untuk media evidence -->
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">
                            @foreach($recentEvidenceMedia as $index => $media)
                            <div class="swiper-slide">
                                <div class="media-card position-relative">
                                    @if($media['type'] == 'image')
                                        <img src="/storage/app/public/{{$media['path']}}" class="img-fluid rounded" alt="{{ $media['file_name'] }}">
                                    @else
                                        <div class="video-thumbnail position-relative rounded">
                                            <img src="{{ asset('build/images/video-thumbnail.jpg') }}" class="img-fluid rounded" alt="Video Thumbnail">
                                            <div class="play-icon">
                                                <i class="ri-play-circle-line"></i>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="media-overlay">
                                        <div class="media-info p-2">
                                            <h6 class="mb-1 text-white">{{ Str::limit($media['task_title'], 30) }}</h6>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary">{{ $media['task_number'] }}</span>
                                                <span class="badge bg-info">{{ $media['created_at']->format('d M, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <a href="#" class="media-link stretched-link" 
                                       data-bs-toggle="modal" 
                                       data-bs-target="#evidenceModal{{ $index }}">
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination evidence-pagination"></div>
                        <div class="swiper-button-next evidence-button-next"></div>
                        <div class="swiper-button-prev evidence-button-prev"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row untuk Carousel Semua Media - dipindahkan langsung di bawah Aktivitas Maintenance -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Galeri Media Maintenance</h4>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-subtle-info btn-sm" data-bs-toggle="modal" data-bs-target="#allMediaModal">
                            <i class="ri-gallery-line align-middle"></i> Lihat Semua Media
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Swiper Carousel -->
                    <div class="swiper mediaSwiper">
                        <div class="swiper-wrapper">
                            @foreach($allMedia as $media)
                            <div class="swiper-slide">
                                <div class="media-card position-relative">
                                    @if($media->type == 'image')
                                        <img src="/storage/app/public/{{ $media->file_path }}" class="img-fluid rounded" alt="{{ $media->file_name }}">
                                    @else
                                        <div class="video-thumbnail position-relative rounded">
                                            <img src="{{ asset('build/images/video-thumbnail.jpg') }}" class="img-fluid rounded" alt="Video Thumbnail">
                                            <div class="play-icon">
                                                <i class="ri-play-circle-line"></i>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="media-overlay">
                                        <div class="media-info p-2">
                                            <h6 class="mb-1 text-white">{{ Str::limit($media->task_title, 30) }}</h6>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary">{{ $media->task_number }}</span>
                                                <span class="badge bg-info">{{ $media->location ?? 'Outlet tidak diketahui' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <a href="#" class="media-link stretched-link" 
                                       data-bs-toggle="modal" 
                                       data-bs-target="#mediaPreviewModal" 
                                       data-media-id="{{ $media->id }}" 
                                       data-media-type="{{ $media->type }}" 
                                       data-media-src="/storage/app/public/{{ $media->file_path }}"
                                       data-media-title="{{ $media->task_title }}"
                                       data-task-number="{{ $media->task_number }}"
                                       data-outlet="{{ $media->location }}">
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination media-pagination"></div>
                        <div class="swiper-button-next media-button-next"></div>
                        <div class="swiper-button-prev media-button-prev"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-6 col-lg-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Aktivitas Terbaru</h4>
                    <a href="{{ route('maintenance.dashboard.activities') }}" class="fs-sm flex-shrink-0" target="_blank">
                        Lihat Semua <i class="ti ti-arrow-narrow-right"></i>
                    </a>
                </div>

                <div data-simplebar style="max-height: 380px;">
                    @foreach($recentActivities as $activity)
                    <div class="p-3 border-bottom border-bottom-dashed">
                        <div class="d-flex align-items-center gap-2">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-light text-primary rounded d-flex align-items-center justify-content-center">
                                        @php
                                            $name = $activity->user_name ?? 'User';
                                            $nameParts = explode(' ', $name);
                                            $initials = '';
                                            foreach ($nameParts as $part) {
                                                if(strlen($part) > 0) {
                                                    $initials .= substr($part, 0, 1);
                                                }
                                            }
                                            $initials = substr($initials, 0, 2);
                                        @endphp
                                        {{ $initials }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    {{ $activity->user_name ?? 'System' }}
                                    <span class="text-muted fs-sm">
                                        {{ $activity->activity_type }}
                                        @if($activity->description)
                                            - {{ $activity->description }}
                                        @endif
                                    </span>
                                </h6>
                                <p class="fs-13 text-muted mb-0">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                @if($activity->task_number)
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ $activity->task_number }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="col-xxl-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Tasks per Member</h4>
                    <div class="flex-shrink-0">
                        <div class="dropdown card-header-dropdown">
                            <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <span class="text-muted">Laporan<i class="ti ti-chevron-down ms-1"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item export-all-tasks" href="{{ route('maintenance.dashboard.export-tasks-by-member') }}?period={{ $period }}">Export Semua Tasks</a>
                                <div class="dropdown-divider"></div>
                                @foreach($tasksByMember as $member)
                                    <a class="dropdown-item" href="{{ route('maintenance.dashboard.export-tasks-by-member') }}?period={{ $period }}&member_id={{ $member->user_id }}">
                                        Export Tasks: {{ $member->member_name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div id="tasks_by_member_chart" 
                        data-colors='["--tb-primary", "--tb-success"]'
                        class="apex-charts" dir="ltr" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-3 col-lg-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Purchase Orders Terbaru</h4>
                    <div class="flex-shrink-0">
                        <a href="#" class="btn btn-subtle-info btn-sm">
                            <i class="ri-file-list-3-line align-middle"></i> Lihat Semua PO
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive table-card mt-0">
                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                            <thead class="text-muted table-active">
                                <tr>
                                    <th scope="col">PO Number</th>
                                    <th scope="col">Terkait Task</th>
                                    <th scope="col">Tanggal Dibuat</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Vendor</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPurchaseOrders as $po)
                                <tr>
                                    <td>
                                        <a href="#" class="fw-medium link-primary">{{ $po->po_number }}</a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($po->task)
                                            <div class="flex-shrink-0 me-2">
                                                <i class="ri-task-line text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <a href="#" class="text-reset">
                                                    {{ $po->task->task_number }}
                                                </a>
                                            </div>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $po->created_at->format('d M, Y') }}</td>
                                    <td>
                                        <span class="text-secondary">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td>{{ $po->vendor_name }}</td>
                                    <td>
                                        @switch($po->status)
                                            @case('DRAFT')
                                                <span class="badge bg-info-subtle text-info">Draft</span>
                                                @break
                                            @case('SUBMITTED')
                                                <span class="badge bg-warning-subtle text-warning">Submitted</span>
                                                @break
                                            @case('APPROVED')
                                                <span class="badge bg-success-subtle text-success">Approved</span>
                                                @break
                                            @case('REJECTED')
                                                <span class="badge bg-danger-subtle text-danger">Rejected</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary-subtle text-secondary">{{ $po->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-icon btn-primary">
                                            <i class="ti ti-eye"></i>
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

   

    <!-- Modal Overdue Tasks -->
    <div class="modal fade" id="overdueTasksModal" tabindex="-1" aria-labelledby="overdueTasksModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger-subtle">
                    <h5 class="modal-title text-danger" id="overdueTasksModalLabel">
                        <i class="ti ti-alarm me-1"></i> Daftar Task Overdue
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="overdueTasksTable">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Task Number</th>
                                    <th scope="col">Outlet</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Member</th>
                                    <th scope="col">Due Date</th>
                                    <th scope="col">Terlambat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($overdueTasksList as $task)
                                <tr>
                                    <td>
                                        <span class="fw-medium">{{ $task->task_number }}</span>
                                    </td>
                                    <td>{{ $task->location ?? 'Tidak ada' }}</td>
                                    <td>{{ Str::limit($task->title, 30) }}</td>
                                    <td>
                                        @if($task->creator)
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    <div class="avatar-xs rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                                        @php
                                                            $name = $task->creator->name ?? 'User';
                                                            $nameParts = explode(' ', $name);
                                                            $initials = '';
                                                            foreach ($nameParts as $part) {
                                                                $initials .= substr($part, 0, 1);
                                                            }
                                                            $initials = substr($initials, 0, 2);
                                                        @endphp
                                                        {{ $initials }}
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">{{ $task->creator->name }}</div>
                                            </div>
                                        @else
                                            <span class="text-muted">Tidak ada</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger">
                                            {{ \Carbon\Carbon::parse($task->due_date)->diffForHumans(null, true) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="avatar-md mx-auto mb-2">
                                            <div class="avatar-title bg-light text-success rounded-circle h1">
                                                <i class="ti ti-check-circle"></i>
                                            </div>
                                        </div>
                                        <h5 class="mb-1">Tidak ada task yang overdue!</h5>
                                        <p class="text-muted mb-0">Semua task dalam jalur waktu yang tepat.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-danger" onclick="window.location.href='#'">Lihat Semua</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Evidence -->
    @foreach($recentEvidenceMedia as $index => $media)
    <div class="modal fade" id="evidenceModal{{ $index }}" tabindex="-1" aria-labelledby="evidenceModalLabel{{ $index }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="evidenceModalLabel{{ $index }}">
                        {{ $media['task_title'] }} - Task #{{ $media['task_number'] }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    @if($media['type'] == 'image')
                        <img src="/storage/app/public/{{$media['path']}}" alt="Evidence Image" class="img-fluid rounded" style="max-height: 70vh;">
                    @else
                        <div class="ratio ratio-16x9">
                            <video src="/storage/app/public/{{$media['path']}}" controls class="rounded"></video>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <a href="/storage/app/public/{{$media['path']}}" download="{{ $media['file_name'] ?? 'evidence_'.$media['id'] }}" class="btn btn-primary">
                        <i class="ti ti-download me-1"></i> Download
                    </a>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Modal All Evidence Gallery -->
    <div class="modal fade" id="allEvidenceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Galeri Evidence Maintenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <!-- Filter controls -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="evidenceSearchInput" placeholder="Cari berdasarkan task atau outlet...">
                                    <button class="btn btn-primary" type="button" id="evidenceSearchBtn">
                                        <i class="ti ti-search"></i> Cari
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="evidenceFilterSelect">
                                    <option value="all">Semua Evidence</option>
                                    <option value="image">Gambar</option>
                                    <option value="video">Video</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-outline-primary" id="toggleEvidenceViewBtn">
                                    <i class="ri-list-check-line me-1"></i> <span>Tampilan Datar</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Breadcrumb for folder navigation -->
                        <nav aria-label="breadcrumb" class="breadcrumb-folder">
                            <ol class="breadcrumb mb-0" id="evidenceFolderBreadcrumb">
                                <li class="breadcrumb-item active" aria-current="page" data-level="root">
                                    <a href="javascript:void(0);"><i class="ri-home-line me-1"></i> Root</a>
                                </li>
                            </ol>
                        </nav>
                        
                        <!-- Loading indicator -->
                        <div class="row d-none" id="evidenceLoadingIndicator">
                            <div class="col-12 text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Memuat evidence...</p>
                            </div>
                        </div>
                        
                        <!-- Folder View container -->
                        <div class="row" id="evidenceFolderContainer">
                            <!-- Folders will be generated by JavaScript -->
                        </div>
                        
                        <!-- Gallery content - only visible in flat view or inside a task folder -->
                        <div class="row gallery-container d-none" id="evidenceGalleryContainer">
                            <!-- Media items will be displayed here -->
                        </div>
                        
                        <!-- Empty state if no media found -->
                        <div class="row d-none" id="evidenceEmptyMessage">
                            <div class="col-12 text-center py-5">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-light text-primary rounded-circle display-5">
                                        <i class="ri-image-line"></i>
                                    </div>
                                </div>
                                <h5>Tidak ada evidence yang ditemukan</h5>
                                <p class="text-muted">Coba ubah filter pencarian Anda</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Evidence Preview Modal -->
    <div class="modal fade" id="galleryEvidencePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Evidence</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="media-preview-container">
                        <div id="galleryEvidenceContent" class="text-center">
                            <!-- Content will be set by JavaScript -->
                        </div>
                        <div class="media-details mt-3">
                            <h5 id="galleryEvidenceTaskTitle"></h5>
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-primary" id="galleryEvidenceTaskNumber"></span>
                                <span class="badge bg-info" id="galleryEvidenceOutlet"></span>
                            </div>
                            <p class="text-muted mt-2 mb-0" id="galleryEvidenceDate"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="galleryEvidenceDownloadBtn" class="btn btn-primary" download>
                        <i class="ti ti-download me-1"></i> Download
                    </a>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Preview Media -->
    <div class="modal fade" id="mediaPreviewModal" tabindex="-1" aria-labelledby="mediaPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mediaPreviewModalLabel">Preview Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="media-preview-container">
                        <div id="mediaContent" class="text-center">
                            <!-- Konten media akan diisi melalui JavaScript -->
                        </div>
                        <div class="media-details mt-3">
                            <h5 id="mediaTitle"></h5>
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-primary" id="mediaTaskNumber"></span>
                                <span class="badge bg-info" id="mediaOutlet"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="mediaDownloadBtn" class="btn btn-primary" download>
                        <i class="ti ti-download me-1"></i> Download
                    </a>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal All Media Gallery -->
    <div class="modal fade" id="allMediaModal" tabindex="-1" aria-labelledby="allMediaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allMediaModalLabel">Galeri Media Maintenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <!-- Filter controls -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="mediaSearchInput" placeholder="Cari berdasarkan task atau outlet...">
                                    <button class="btn btn-primary" type="button" id="mediaSearchBtn">
                                        <i class="ti ti-search"></i> Cari
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="mediaFilterSelect">
                                    <option value="all">Semua Media</option>
                                    <option value="image">Gambar</option>
                                    <option value="video">Video</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-outline-primary" id="toggleViewBtn">
                                    <i class="ri-list-check-line me-1"></i> <span>Tampilan Datar</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Breadcrumb for folder navigation -->
                        <nav aria-label="breadcrumb" class="breadcrumb-folder mb-3">
                            <ol class="breadcrumb mb-0" id="folderBreadcrumb" style="cursor: pointer;">
                                <li class="breadcrumb-item active" aria-current="page" data-level="root">
                                    <a href="javascript:void(0);"><i class="ri-home-line me-1"></i> Root</a>
                                </li>
                            </ol>
                        </nav>
                        
                        <!-- Loading indicator -->
                        <div class="row d-none" id="loadingIndicator">
                            <div class="col-12 text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Memuat media...</p>
                            </div>
                        </div>
                        
                        <!-- Folder View container -->
                        <div class="row" id="folderContainer">
                            <!-- Folders will be generated by JavaScript -->
                        </div>
                        
                        <!-- Gallery content - only visible in flat view or inside a task folder -->
                        <div class="row gallery-container d-none" id="galleryContainer">
                            <!-- Media items will be displayed here -->
                        </div>
                        
                        <!-- Empty state if no media found after filtering -->
                        <div class="row empty-gallery-message d-none">
                            <div class="col-12 text-center py-5">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-light text-primary rounded-circle display-5">
                                        <i class="ri-image-line"></i>
                                    </div>
                                </div>
                                <h5>Tidak ada media yang ditemukan</h5>
                                <p class="text-muted">Coba ubah filter pencarian Anda</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Media Preview Modal (for full screen preview within gallery) -->
    <div class="modal fade" id="galleryMediaPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="galleryMediaTitle">Preview Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="media-preview-container">
                        <div id="galleryMediaContent" class="text-center">
                            <!-- Content will be set by JavaScript -->
                        </div>
                        <div class="media-details mt-3">
                            <h5 id="galleryMediaTaskTitle"></h5>
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-primary" id="galleryMediaTaskNumber"></span>
                                <span class="badge bg-info" id="galleryMediaOutlet"></span>
                            </div>
                            <p class="text-muted mt-2 mb-0" id="galleryMediaDate"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="galleryMediaDownloadBtn" class="btn btn-primary" download>
                        <i class="ti ti-download me-1"></i> Download
                    </a>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <!-- Script libraries -->
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables/datatables.min.js') }}"></script>
    
    <!-- dashboard init -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    
    <script>
        // Variable global untuk menyimpan referensi chart
        var globalCharts = {
            taskStatus: null,
            category: null,
            activity: null,
            memberTasks: null
        };
        
        // Flag untuk mencegah inisialisasi double
        var chartsInitialized = false;
        
        function initCharts() {
            // Cek jika chart sudah diinisialisasi sebelumnya
            if (chartsInitialized) {
                console.log("Charts sudah diinisialisasi, lewati");
                return;
            }
            
            console.log("Mulai inisialisasi chart");
            
            // Bersihkan chart yang sudah ada sebelumnya
            destroyExistingCharts();
            
            // 1. Evidence Swiper
            initSwiper();
            
            // 2. Task Status Chart
            initTaskStatusChart();
            
            // 3. Kategori Maintenance Chart
            initCategoryChart();
            
            // 4. Activity Chart
            initActivityChart();
            
            // 5. Tasks By Member Chart
            initTasksByMemberChart();
            
            // Set flag bahwa chart sudah diinisialisasi
            chartsInitialized = true;
            console.log("Semua chart berhasil diinisialisasi");
        }
        
        function destroyExistingCharts() {
            console.log("Menghapus chart yang sudah ada");
            
            // Hapus semua chart yang sudah ada
            if (globalCharts.taskStatus) {
                console.log("Menghapus task status chart");
                globalCharts.taskStatus.destroy();
                globalCharts.taskStatus = null;
            }
            
            if (globalCharts.category) {
                console.log("Menghapus category chart");
                globalCharts.category.destroy();
                globalCharts.category = null;
            }
            
            if (globalCharts.activity) {
                console.log("Menghapus activity chart");
                globalCharts.activity.destroy();
                globalCharts.activity = null;
            }
            
            if (globalCharts.memberTasks) {
                console.log("Menghapus member tasks chart");
                globalCharts.memberTasks.destroy();
                globalCharts.memberTasks = null;
            }
            
            // Bersihkan elemen DOM
            document.getElementById('task_status_chart').innerHTML = '';
            document.getElementById('maintenance_categories_chart').innerHTML = '';
            document.getElementById('maintenance_activity_chart').innerHTML = '';
            
            if (document.getElementById('tasks_by_member_chart')) {
                document.getElementById('tasks_by_member_chart').innerHTML = '';
            }
        }
        
        function initSwiper() {
            // Evidence Swiper - menggunakan efek coverflow yang sama dengan media carousel
            // Tapi dengan maksimal 3 slide yang ditampilkan
            new Swiper(".mySwiper", {
                effect: "coverflow",
                grabCursor: true,
                centeredSlides: true,
                slidesPerView: "auto",
                coverflowEffect: {
                    rotate: 5,
                    stretch: 0,
                    depth: 100,
                    modifier: 1,
                    slideShadows: true,
                },
                pagination: {
                    el: ".evidence-pagination",
                    clickable: true,
                    dynamicBullets: true,
                },
                navigation: {
                    nextEl: ".evidence-button-next",
                    prevEl: ".evidence-button-prev",
                },
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                loop: true,
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20,
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 30,
                    },
                    1024: {
                        slidesPerView: 3, // Mengurangi jumlah slide yang ditampilkan menjadi 3
                        spaceBetween: 30,
                    },
                },
            });
        }
        
        function initTaskStatusChart() {
            var taskStatusElement = document.getElementById('task_status_chart');
            if (!taskStatusElement) {
                console.log("Elemen task_status_chart tidak ditemukan");
                return;
            }
            
            var taskStatusSeries = [
                {{ $taskStatusData['todo'] ?? 0 }},
                {{ $taskStatusData['inProgress'] ?? 0 }},
                {{ $taskStatusData['pr'] ?? 0 }},
                {{ $taskStatusData['po'] ?? 0 }},
                {{ $taskStatusData['inReview'] ?? 0 }},
                {{ $taskStatusData['done'] ?? 0 }}
            ];
            
            var taskStatusLabels = ['Todo', 'In Progress', 'PR', 'PO', 'In Review', 'Done'];
            
            var taskStatusOptions = {
                series: taskStatusSeries,
                chart: {
                    id: 'task-status-chart',
                    type: 'polarArea',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                labels: taskStatusLabels,
                colors: ['#3498db', '#f39c12', '#e74c3c', '#2ecc71', '#9b59b6', '#1abc9c'],
                stroke: {
                    width: 1,
                    colors: ['#fff']
                },
                fill: {
                    opacity: 0.8
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 280
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                plotOptions: {
                    polarArea: {
                        rings: {
                            strokeWidth: 0
                        },
                        spokes: {
                            strokeWidth: 0
                        }
                    }
                },
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " task" + (val !== 1 ? "s" : "");
                        }
                    }
                }
            };
            
            // Hapus chart lama jika ada
            if (globalCharts.taskStatus) {
                globalCharts.taskStatus.destroy();
            }
            
            // Buat chart baru
            globalCharts.taskStatus = new ApexCharts(taskStatusElement, taskStatusOptions);
            globalCharts.taskStatus.render();
        }
        
        function initCategoryChart() {
            var categoryElement = document.getElementById('maintenance_categories_chart');
            if (!categoryElement) {
                console.log("Elemen maintenance_categories_chart tidak ditemukan");
                return;
            }
            
            @php
            $categorySeriesData = [];
            $categoryLabels = [];
            $categoryColors = [];
            
            if(!empty($categoryData)) {
                foreach($categoryData as $category) {
                    $categorySeriesData[] = $category->total ?? 0;
                    $categoryLabels[] = $category->name ?? 'Tidak ada label';
                    $categoryColors[] = $category->color ?? '#3498db';
                }
            }
            @endphp
            
            var categorySeriesData = @json($categorySeriesData);
            var categoryLabels = @json($categoryLabels);
            var categoryColors = @json($categoryColors);
            
            var totalCategory = categorySeriesData.reduce((a, b) => a + b, 0);
            var categoryPercents = categorySeriesData.map(function(value) {
                return totalCategory > 0 ? Math.round((value / totalCategory) * 100) : 0;
            });
            
            var categoryOptions = {
                series: categoryPercents,
                chart: {
                    id: 'category-chart',
                    height: 300,
                    type: 'radialBar',
                },
                plotOptions: {
                    radialBar: {
                        offsetY: 0,
                        startAngle: 0,
                        endAngle: 270,
                        hollow: {
                            margin: 5,
                            size: '30%',
                            background: 'transparent',
                        },
                        dataLabels: {
                            name: {
                                show: true,
                            },
                            value: {
                                show: true,
                                formatter: function(val) {
                                    return val + '%';
                                }
                            }
                        }
                    }
                },
                colors: categoryColors,
                labels: categoryLabels,
                legend: {
                    show: true,
                    position: 'bottom'
                }
            };
            
            globalCharts.category = new ApexCharts(categoryElement, categoryOptions);
            globalCharts.category.render();
        }
        
        function initActivityChart() {
            var activityElement = document.getElementById('maintenance_activity_chart');
            if (!activityElement) {
                console.log("Elemen maintenance_activity_chart tidak ditemukan");
                return;
            }
            
            var monthlyData = [
                @foreach($activityData ?? [] as $data)
                {
                    month: '{{ $data['month'] ?? '' }}',
                    created: {{ $data['created'] ?? 0 }},
                    completed: {{ $data['completed'] ?? 0 }}
                },
                @endforeach
            ];
            
            var months = monthlyData.map(function(item) { return item.month; });
            var createdData = monthlyData.map(function(item) { return item.created; });
            var completedData = monthlyData.map(function(item) { return item.completed; });
            
            var activityOptions = {
                series: [
                    {
                        name: 'Dibuat',
                        data: createdData
                    },
                    {
                        name: 'Diselesaikan',
                        data: completedData
                    }
                ],
                chart: {
                    id: 'activity-chart',
                    height: 350,
                    type: 'line',
                    toolbar: {
                        show: false
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: months,
                },
                markers: {
                    size: 4
                },
                colors: ['#3498db', '#2ecc71']
            };
            
            globalCharts.activity = new ApexCharts(activityElement, activityOptions);
            globalCharts.activity.render();
        }
        
        function initTasksByMemberChart() {
            var chartElement = document.getElementById('tasks_by_member_chart');
            if (!chartElement) {
                console.log("Elemen tasks_by_member_chart tidak ditemukan");
                return;
            }
            
            // Data dari controller
            var memberData = @json($tasksByMember);
            
            // Siapkan series dan labels
            var memberNames = [];
            var totalTasksData = [];
            var completedTasksData = [];
            
            for (var i = 0; i < memberData.length; i++) {
                memberNames.push(memberData[i].member_name);
                totalTasksData.push(memberData[i].total_tasks);
                completedTasksData.push(memberData[i].completed_tasks);
            }
            
            // Opsi chart
            var memberChartOptions = {
                series: [{
                    name: 'Total Tasks',
                    data: totalTasksData
                }, {
                    name: 'Completed Tasks',
                    data: completedTasksData
                }],
                chart: {
                    id: 'member-tasks-chart',
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    },
                    stacked: false
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        columnWidth: '55%',
                        borderRadius: 4,
                        dataLabels: {
                            position: 'top',
                        },
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val;
                    },
                    offsetX: 20,
                    style: {
                        fontSize: '12px',
                        colors: ['#304758']
                    }
                },
                colors: ['#3498db', '#2ecc71'],
                xaxis: {
                    categories: memberNames,
                    labels: {
                        formatter: function (val) {
                            return val
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Members',
                    },
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " tasks";
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    offsetY: 0
                }
            };
            
            // Buat chart
            globalCharts.memberTasks = new ApexCharts(chartElement, memberChartOptions);
            globalCharts.memberTasks.render();
        }
        
        // Jalankan hanya sekali saat DOM sudah siap
        document.addEventListener("DOMContentLoaded", function() {
            console.log("DOM Content Loaded");
            
            // Cek apakah window.ApexCharts tersedia
            if (typeof ApexCharts === 'undefined') {
                console.error("ApexCharts tidak tersedia!");
                return;
            }
            
            // Inisialisasi chart setelah delay singkat (memastikan DOM benar-benar siap)
            setTimeout(function() {
                initCharts();
            }, 100);
            
            // Inisialisasi datatables untuk tabel overdue tasks di modal
            if (document.getElementById('overdueTasksTable')) {
                new DataTable('#overdueTasksTable', {
                    lengthChange: false,
                    pageLength: 5,
                    language: {
                        search: "Cari:",
                        zeroRecords: "Tidak ada data yang ditemukan",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                        infoFiltered: "(difilter dari _MAX_ total entri)",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        }
                    }
                });
            }
        });
        
        // Tambahan untuk menghindari duplikasi saat page direfresh
        window.addEventListener('beforeunload', function() {
            destroyExistingCharts();
            chartsInitialized = false;
        });
        
        // Tambahan untuk mencegah chart dobel karena turbolinks/pjax jika ada
        document.addEventListener('turbolinks:before-render', function() {
            destroyExistingCharts();
            chartsInitialized = false;
        });

        // Fungsi untuk tombol Generate Report
        document.addEventListener('DOMContentLoaded', function() {
            const generateReportBtn = document.getElementById('generateReportBtn');
            if (generateReportBtn) {
                generateReportBtn.addEventListener('click', function(e) {
                    // Simpan teks original
                    const originalText = this.innerHTML;
                    
                    // Ubah ke loading state
                    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...';
                    this.disabled = true;
                    
                    // Redirect ke URL asli setelah menampilkan loading
                    setTimeout(() => {
                        window.location.href = this.getAttribute('href');
                        
                        // Kembalikan tampilan tombol setelah 2 detik
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.disabled = false;
                        }, 2000);
                    }, 500);
                    
                    // Prevent default
                    e.preventDefault();
                });
            }
        });

        // Script untuk mengelola video pada modal
        document.addEventListener('DOMContentLoaded', function() {
            // Pause semua video ketika modal tertutup
            const evidenceModals = document.querySelectorAll('[id^="evidenceModal"]');
            evidenceModals.forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function() {
                    const videos = this.querySelectorAll('video');
                    videos.forEach(video => {
                        video.pause();
                    });
                });
            });
        });

        // Script untuk mengelola thumbnail slider
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.getElementById('allEvidenceCarousel');
            const thumbnailSlider = document.querySelector('.thumbnail-slider');
            const thumbnailItems = document.querySelectorAll('.thumbnail-item');
            const prevButton = document.querySelector('.thumbnail-nav.prev');
            const nextButton = document.querySelector('.thumbnail-nav.next');
            
            // Ukuran thumbnail dan gap
            const thumbnailSize = 20; // 20% dari lebar thumbnail container
            const visibleThumbnails = 5;
            const totalThumbnails = thumbnailItems.length;
            let currentPosition = 0;
            
            // Fungsi untuk menggeser thumbnail slider
            function slideThumbnails(position) {
                if (position < 0) position = 0;
                if (position > totalThumbnails - visibleThumbnails) {
                    position = totalThumbnails - visibleThumbnails;
                }
                currentPosition = position;
                
                // Geser thumbnail slider
                const translateX = -currentPosition * (100 / visibleThumbnails);
                thumbnailSlider.style.transform = `translateX(${translateX}%)`;
                
                // Update status tombol navigasi
                if (prevButton) prevButton.disabled = currentPosition === 0;
                if (nextButton) nextButton.disabled = currentPosition >= totalThumbnails - visibleThumbnails;
            }
            
            // Tambahkan event listener untuk tombol navigasi thumbnail
            if (prevButton) {
                prevButton.addEventListener('click', () => {
                    slideThumbnails(currentPosition - 1);
                });
            }
            
            if (nextButton) {
                nextButton.addEventListener('click', () => {
                    slideThumbnails(currentPosition + 1);
                });
            }
            
            // Tambahkan event listener untuk thumbnail
            thumbnailItems.forEach((item, index) => {
                item.addEventListener('click', () => {
                    // Aktifkan thumbnail yang diklik
                    thumbnailItems.forEach(thumb => thumb.classList.remove('active'));
                    item.classList.add('active');
                    
                    // Geser carousel ke slide yang dipilih
                    const carouselInstance = bootstrap.Carousel.getInstance(carousel);
                    carouselInstance.to(index);
                });
            });
            
            // Tangani perubahan slide carousel
            carousel.addEventListener('slide.bs.carousel', event => {
                const slideIndex = event.to;
                
                // Aktifkan thumbnail yang sesuai
                thumbnailItems.forEach((thumb, index) => {
                    thumb.classList.toggle('active', index === slideIndex);
                });
                
                // Geser thumbnail slider untuk memastikan thumbnail aktif terlihat
                if (slideIndex < currentPosition) {
                    // Jika slide baru sebelum tampilan thumbnail saat ini
                    slideThumbnails(slideIndex);
                } else if (slideIndex >= currentPosition + visibleThumbnails) {
                    // Jika slide baru setelah tampilan thumbnail saat ini
                    slideThumbnails(slideIndex - visibleThumbnails + 1);
                }
            });
            
            // Pause semua video ketika berganti slide
            carousel.addEventListener('slide.bs.carousel', function () {
                const videos = this.querySelectorAll('video');
                videos.forEach(video => {
                    video.pause();
                });
            });
            
            // Pause semua video ketika modal tertutup
            document.getElementById('allEvidenceModal').addEventListener('hidden.bs.modal', function () {
                const videos = this.querySelectorAll('video');
                videos.forEach(video => {
                    video.pause();
                });
            });
            
            // Inisialisasi thumbnails
            slideThumbnails(0);
        });

        // Inisialisasi Swiper untuk media carousel
        new Swiper(".mediaSwiper", {
            effect: "coverflow",
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: "auto",
            coverflowEffect: {
                rotate: 5,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: true,
            },
            pagination: {
                el: ".media-pagination",
                clickable: true,
                dynamicBullets: true,
            },
            navigation: {
                nextEl: ".media-button-next",
                prevEl: ".media-button-prev",
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            loop: true,
            breakpoints: {
                640: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
                1024: {
                    slidesPerView: 5,
                    spaceBetween: 30,
                },
            },
        });

        // Handle preview modal untuk media
        $('#mediaPreviewModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var mediaType = button.data('media-type');
            var mediaSrc = button.data('media-src');
            var mediaTitle = button.data('media-title');
            var taskNumber = button.data('task-number');
            var outlet = button.data('outlet');
            
            var modal = $(this);
            var mediaContent = modal.find('#mediaContent');
            
            // Reset content
            mediaContent.empty();
            
            // Create media element based on type
            if (mediaType === 'image') {
                mediaContent.html(`<img src="${mediaSrc}" class="img-fluid" alt="${mediaTitle}">`);
            } else {
                mediaContent.html(`
                    <video controls class="w-100">
                        <source src="${mediaSrc}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                `);
            }
            
            // Update details
            modal.find('#mediaTitle').text(mediaTitle);
            modal.find('#mediaTaskNumber').text(taskNumber);
            modal.find('#mediaOutlet').text(outlet);
            
            // Set download button attributes
            var downloadBtn = modal.find('#mediaDownloadBtn');
            downloadBtn.attr('href', mediaSrc);
            
            // Set filename for download - extract filename from URL or use title
            var fileName = '';
            if (mediaSrc) {
                // Extract filename from path
                var pathParts = mediaSrc.split('/');
                fileName = pathParts[pathParts.length - 1];
            } else {
                // Fallback to title if path can't be parsed
                fileName = mediaType === 'image' ? 'image.jpg' : 'video.mp4';
            }
            
            // Set download attribute with filename
            downloadBtn.attr('download', fileName);
        });

        // Stop video playback when modal is closed
        $('#mediaPreviewModal').on('hidden.bs.modal', function () {
            var video = $(this).find('video');
            if (video.length > 0) {
                video[0].pause();
            }
        });

        // Functionality for All Media Gallery Modal
        $(document).ready(function() {
            var allMedia = [];
            var currentPath = 'root';
            var flatView = false;
            
            // Prepare and organize media data when modal is shown
            $('#allMediaModal').on('show.bs.modal', function() {
                $('#loadingIndicator').removeClass('d-none');
                $('#folderContainer, #galleryContainer').addClass('d-none');
                
                // Reset search and filters
                $('#mediaSearchInput').val('');
                $('#mediaFilterSelect').val('all');
                flatView = false;
                $('#toggleViewBtn span').text('Tampilan Datar');
                
                // Reset navigation
                currentPath = 'root';
                $('#folderBreadcrumb').html('<li class="breadcrumb-item active" aria-current="page" data-level="root"><a><i class="ri-home-line me-1"></i> Root</a></li>');
                
                // Collect and organize all media
                allMedia = [];
                
                @foreach($allMedia as $media)
                allMedia.push({
                    id: '{{ $media->id }}',
                    type: '{{ $media->type }}',
                    file_path: '/storage/app/public/{{ $media->file_path }}',
                    file_name: '{{ $media->file_name }}',
                    task_id: '{{ $media->task_id }}',
                    task_number: '{{ $media->task_number }}',
                    task_title: '{{ Str::limit($media->task_title, 30) }}',
                    outlet: '{{ $media->location }}',
                    created_at: '{{ \Carbon\Carbon::parse($media->created_at)->format("Y-m-d") }}',
                    created_at_display: '{{ \Carbon\Carbon::parse($media->created_at)->format("d M Y") }}',
                });
                @endforeach
                
                // Display root folders (outlets)
                refreshView();
            });
            
            // Toggle between folder view and flat view
            $('#toggleViewBtn').on('click', function() {
                flatView = !flatView;
                $(this).find('span').text(flatView ? 'Tampilan Folder' : 'Tampilan Datar');
                refreshView();
            });
            
            // Handle breadcrumb navigation
            $(document).on('click', '.breadcrumb-item a', function() {
                var $item = $(this).parent();
                var level = $item.data('level');
                
                // Don't do anything if it's the active item
                if ($item.hasClass('active')) return;
                
                // Update currentPath
                currentPath = level;
                
                // Remove all breadcrumb items after this one
                $item.nextAll().remove();
                $item.addClass('active').attr('aria-current', 'page');
                
                // Refresh view
                refreshView();
            });
            
            // Handle folder click
            $(document).on('click', '.folder', function() {
                var path = $(this).data('path');
                var name = $(this).data('name');
                var level = $(this).data('level');
                
                // Update breadcrumb
                $('#folderBreadcrumb').find('.active').removeClass('active').removeAttr('aria-current');
                $('#folderBreadcrumb').append('<li class="breadcrumb-item active" aria-current="page" data-level="' + path + '"><a>' + name + '</a></li>');
                
                // Update currentPath
                currentPath = path;
                
                // Refresh view
                refreshView();
            });
            
            // Search and filter functionality
            $('#mediaSearchBtn, #mediaSearchInput').on('input keyup', function() {
                refreshView();
            });
            
            $('#mediaFilterSelect').on('change', function() {
                refreshView();
            });
            
            // Handle media item click
            $(document).on('click', '.view-media-btn', function() {
                var mediaId = $(this).data('media-id');
                var mediaType = $(this).data('media-type');
                var mediaSrc = $(this).data('media-src');
                var mediaTitle = $(this).data('media-title');
                var taskNumber = $(this).data('task-number');
                var outlet = $(this).data('outlet');
                var createdAt = $(this).data('created-at');
                
                var modal = $('#galleryMediaPreviewModal');
                var mediaContent = modal.find('#galleryMediaContent');
                
                // Reset content
                mediaContent.empty();
                
                // Create media element based on type
                if (mediaType === 'image') {
                    mediaContent.html(`<img src="${mediaSrc}" class="img-fluid" alt="${mediaTitle}">`);
                } else {
                    mediaContent.html(`
                        <video controls class="w-100">
                            <source src="${mediaSrc}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    `);
                }
                
                // Update details
                modal.find('#galleryMediaTaskTitle').text(mediaTitle);
                modal.find('#galleryMediaTaskNumber').text(taskNumber);
                modal.find('#galleryMediaOutlet').text(outlet);
                modal.find('#galleryMediaDate').text('Tanggal: ' + createdAt);
                
                // Set download button attributes
                var downloadBtn = modal.find('#galleryMediaDownloadBtn');
                downloadBtn.attr('href', mediaSrc);
                
                // Extract filename from URL or use title
                var fileName = '';
                if (mediaSrc) {
                    var pathParts = mediaSrc.split('/');
                    fileName = pathParts[pathParts.length - 1];
                } else {
                    fileName = mediaType === 'image' ? 'image.jpg' : 'video.mp4';
                }
                
                // Set download attribute with filename
                downloadBtn.attr('download', fileName);
                
                // Show the modal
                modal.modal('show');
            });
            
            // Stop video playback when gallery preview modal is closed
            $('#galleryMediaPreviewModal').on('hidden.bs.modal', function () {
                var video = $(this).find('video');
                if (video.length > 0) {
                    video[0].pause();
                }
            });
            
            // Function to refresh the view based on current path, search, and filter
            function refreshView() {
                var searchText = $('#mediaSearchInput').val().toLowerCase();
                var filterType = $('#mediaFilterSelect').val();
                
                $('#loadingIndicator').removeClass('d-none');
                $('#folderContainer, #galleryContainer').addClass('d-none');
                
                setTimeout(function() {
                    if (flatView) {
                        // Show all media in flat view
                        displayFlatView(searchText, filterType);
                    } else if (currentPath === 'root') {
                        // Show outlet folders at root level
                        displayOutletFolders(searchText);
                    } else if (currentPath.indexOf('/') === -1) {
                        // Show date folders inside an outlet
                        displayDateFolders(currentPath, searchText);
                    } else if (currentPath.split('/').length === 2) {
                        // Show task folders inside a date folder
                        displayTaskFolders(currentPath, searchText);
                    } else {
                        // Show media inside a task folder
                        displayTaskMedia(currentPath, searchText, filterType);
                    }
                    
                    $('#loadingIndicator').addClass('d-none');
                }, 300); // Small delay for better UX
            }
            
            // Function to display outlet folders
            function displayOutletFolders(searchText) {
                var outlets = {};
                var filteredMedia = filterMedia(allMedia, searchText, 'all');
                
                // Count media per outlet
                filteredMedia.forEach(function(media) {
                    if (!outlets[media.outlet]) {
                        outlets[media.outlet] = {
                            name: media.outlet,
                            count: 0
                        };
                    }
                    outlets[media.outlet].count++;
                });
                
                // Sort outlets by name
                var sortedOutlets = Object.values(outlets).sort(function(a, b) {
                    return a.name.localeCompare(b.name);
                });
                
                var $container = $('#folderContainer').empty();
                
                if (sortedOutlets.length === 0) {
                    $('.empty-gallery-message').removeClass('d-none');
                } else {
                    $('.empty-gallery-message').addClass('d-none');
                    
                    // Build outlet folders
                    sortedOutlets.forEach(function(outlet) {
                        var folderHtml = `
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
                            <div class="folder d-flex flex-column align-items-center justify-content-center p-3" 
                                 data-path="${outlet.name}" 
                                 data-name="${outlet.name}" 
                                 data-level="outlet">
                                <div class="folder-icon mb-2">
                                    <i class="ri-building-line"></i>
                                </div>
                                <div class="folder-title text-center">${outlet.name}</div>
                                <div class="folder-counter">${outlet.count} media</div>
                            </div>
                        </div>`;
                        $container.append(folderHtml);
                    });
                }
                
                $container.removeClass('d-none');
            }
            
            // Function to display date folders
            function displayDateFolders(outletName, searchText) {
                var dates = {};
                var filteredMedia = filterMedia(allMedia, searchText, 'all').filter(function(media) {
                    return media.outlet === outletName;
                });
                
                // Count media per date
                filteredMedia.forEach(function(media) {
                    if (!dates[media.created_at]) {
                        dates[media.created_at] = {
                            name: media.created_at,
                            display: media.created_at_display,
                            count: 0
                        };
                    }
                    dates[media.created_at].count++;
                });
                
                // Sort dates (newest first)
                var sortedDates = Object.values(dates).sort(function(a, b) {
                    return b.name.localeCompare(a.name); // Descending sort
                });
                
                var $container = $('#folderContainer').empty();
                
                if (sortedDates.length === 0) {
                    $('.empty-gallery-message').removeClass('d-none');
                } else {
                    $('.empty-gallery-message').addClass('d-none');
                    
                    // Build date folders
                    sortedDates.forEach(function(date) {
                        var folderHtml = `
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
                            <div class="folder d-flex flex-column align-items-center justify-content-center p-3" 
                                 data-path="${outletName}/${date.name}" 
                                 data-name="${date.display}" 
                                 data-level="date">
                                <div class="folder-icon mb-2">
                                    <i class="ri-calendar-line"></i>
                                </div>
                                <div class="folder-title text-center">${date.display}</div>
                                <div class="folder-counter">${date.count} media</div>
                            </div>
                        </div>`;
                        $container.append(folderHtml);
                    });
                }
                
                $container.removeClass('d-none');
            }
            
            // Function to display task folders
            function displayTaskFolders(path, searchText) {
                var pathParts = path.split('/');
                var outletName = pathParts[0];
                var date = pathParts[1];
                
                var tasks = {};
                var filteredMedia = filterMedia(allMedia, searchText, 'all').filter(function(media) {
                    return media.outlet === outletName && media.created_at === date;
                });
                
                // Count media per task
                filteredMedia.forEach(function(media) {
                    var taskKey = media.task_number + ' - ' + media.task_title;
                    if (!tasks[taskKey]) {
                        tasks[taskKey] = {
                            name: taskKey,
                            task_number: media.task_number,
                            count: 0
                        };
                    }
                    tasks[taskKey].count++;
                });
                
                // Sort tasks by task number
                var sortedTasks = Object.values(tasks).sort(function(a, b) {
                    return a.task_number.localeCompare(b.task_number);
                });
                
                var $container = $('#folderContainer').empty();
                
                if (sortedTasks.length === 0) {
                    $('.empty-gallery-message').removeClass('d-none');
                } else {
                    $('.empty-gallery-message').addClass('d-none');
                    
                    // Build task folders
                    sortedTasks.forEach(function(task) {
                        var folderHtml = `
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
                            <div class="folder d-flex flex-column align-items-center justify-content-center p-3" 
                                 data-path="${path}/${task.task_number}" 
                                 data-name="${task.name}" 
                                 data-level="task">
                                <div class="folder-icon mb-2">
                                    <i class="ri-file-list-line"></i>
                                </div>
                                <div class="folder-title text-center">${task.name}</div>
                                <div class="folder-counter">${task.count} media</div>
                            </div>
                        </div>`;
                        $container.append(folderHtml);
                    });
                }
                
                $container.removeClass('d-none');
            }
            
            // Function to display media inside a task folder
            function displayTaskMedia(path, searchText, filterType) {
                var pathParts = path.split('/');
                var outletName = pathParts[0];
                var date = pathParts[1];
                var taskNumber = pathParts[2];
                
                var filteredMedia = filterMedia(allMedia, searchText, filterType).filter(function(media) {
                    return media.outlet === outletName && 
                           media.created_at === date && 
                           media.task_number === taskNumber;
                });
                
                displayMedia(filteredMedia);
            }
            
            // Function to display all media in flat view
            function displayFlatView(searchText, filterType) {
                var filteredMedia = filterMedia(allMedia, searchText, filterType);
                displayMedia(filteredMedia);
            }
            
            // Function to display media items
            function displayMedia(mediaItems) {
                var $container = $('#galleryContainer').empty();
                
                if (mediaItems.length === 0) {
                    $('.empty-gallery-message').removeClass('d-none');
                    $container.addClass('d-none');
                } else {
                    $('.empty-gallery-message').addClass('d-none');
                    
                    // Build media items
                    mediaItems.forEach(function(media) {
                        var mediaHtml = `
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2 gallery-item mb-4">
                            <div class="gallery-card h-100">
                                <div class="gallery-img-wrapper">
                                    ${media.type === 'image' 
                                        ? `<img src="${media.file_path}" class="gallery-img" alt="${media.file_name}">`
                                        : `<div class="video-thumbnail">
                                            <i class="ri-video-line text-white fs-1"></i>
                                          </div>`
                                    }
                                    <div class="gallery-date">
                                        <span class="badge bg-dark text-white">
                                            ${media.created_at_display}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <h6 class="card-title fs-sm mb-1">Task #${media.task_number}</h6>
                                    <p class="card-text text-muted fs-xs mb-1">${media.task_title}</p>
                                    <p class="card-text fs-xs mb-2">
                                        <i class="ri-map-pin-line"></i> ${media.outlet}
                                    </p>
                                    <button class="btn btn-sm btn-primary w-100 view-media-btn" 
                                            data-media-id="${media.id}" 
                                            data-media-type="${media.type}" 
                                            data-media-src="${media.file_path}"
                                            data-media-title="${media.task_title}"
                                            data-task-number="${media.task_number}"
                                            data-outlet="${media.outlet}"
                                            data-created-at="${media.created_at_display}">
                                        <i class="ri-eye-line"></i> Lihat
                                    </button>
                                </div>
                            </div>
                        </div>`;
                        $container.append(mediaHtml);
                    });
                    
                    $container.removeClass('d-none');
                }
            }
            
            // Function to filter media based on search text and media type
            function filterMedia(mediaList, searchText, filterType) {
                return mediaList.filter(function(media) {
                    var matchesSearch = searchText === '' || 
                                       media.task_number.toLowerCase().includes(searchText) || 
                                       media.task_title.toLowerCase().includes(searchText) || 
                                       media.outlet.toLowerCase().includes(searchText);
                                       
                    var matchesFilter = filterType === 'all' || media.type === filterType;
                    
                    return matchesSearch && matchesFilter;
                });
            }
        });

        // Functionality for All Evidence Gallery Modal
        $(document).ready(function() {
            var allEvidence = [];
            var currentEvidencePath = 'root';
            var flatEvidenceView = false;
            
            // Tampilkan outlet folders ketika modal dibuka
            $('#allEvidenceModal').on('show.bs.modal', function() {
                // Reset UI
                $('#evidenceLoadingIndicator').removeClass('d-none');
                $('#evidenceFolderContainer, #evidenceGalleryContainer').addClass('d-none');
                $('#evidenceEmptyMessage').addClass('d-none');
                
                // Reset search and filters
                $('#evidenceSearchInput').val('');
                $('#evidenceFilterSelect').val('all');
                flatEvidenceView = false;
                $('#toggleEvidenceViewBtn span').text('Tampilan Datar');
                
                // Reset navigation
                currentEvidencePath = 'root';
                $('#evidenceFolderBreadcrumb').html('<li class="breadcrumb-item active" aria-current="page" data-level="root"><a href="javascript:void(0);"><i class="ri-home-line me-1"></i> Root</a></li>');
                
                // Langsung panggil displayOutletFolders() tanpa menggunakan route get-evidence-folders
                displayOutletFolders();
            });
            
            // Fungsi untuk menampilkan folder outlet
            function displayOutletFolders() {
                $('#evidenceLoadingIndicator').removeClass('d-none');
                $('#evidenceFolderContainer, #evidenceGalleryContainer').addClass('d-none');
                $('#evidenceEmptyMessage').addClass('d-none');
                
                // Ambil data outlet
                $.ajax({
                    url: '{{ route("maintenance.dashboard.get-evidence-outlets") }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var outlets = response.data;
                        var folderContainer = $('#evidenceFolderContainer');
                        folderContainer.empty();
                        
                        if (!outlets || outlets.length === 0) {
                            $('#evidenceEmptyMessage').removeClass('d-none');
                            $('#evidenceLoadingIndicator').addClass('d-none');
                            return;
                        }
                        
                        folderContainer.removeClass('d-none');
                        $('#evidenceLoadingIndicator').addClass('d-none');
                        
                        // Buat folder untuk setiap outlet
                        outlets.forEach(function(outlet) {
                            var folderItem = `
                                <div class="col-md-3 col-sm-4 col-6 mb-4">
                                    <div class="folder evidence-folder text-center p-3" data-path="${outlet.id_outlet}" data-name="${outlet.nama_outlet}">
                                        <div class="folder-icon mb-2">
                                            <i class="ri-folder-5-line"></i>
                                        </div>
                                        <h6 class="folder-title">${outlet.nama_outlet}</h6>
                                        <span class="folder-counter">${outlet.count} evidence</span>
                                    </div>
                                </div>
                            `;
                            folderContainer.append(folderItem);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading outlet data:", error);
                        $('#evidenceEmptyMessage').removeClass('d-none');
                        $('#evidenceLoadingIndicator').addClass('d-none');
                    }
                });
            }
            
            // Handle click pada folder outlet
            $(document).on('click', '.evidence-folder', function() {
                var outletId = $(this).data('path');
                var outletName = $(this).data('name');
                
                console.log('Clicked outlet folder:', outletId, outletName);
                
                // Tampilkan loading
                $('#evidenceLoadingIndicator').removeClass('d-none');
                $('#evidenceFolderContainer, #evidenceGalleryContainer').addClass('d-none');
                
                // Reset breadcrumb dan tambahkan item baru
                $('#evidenceFolderBreadcrumb .breadcrumb-item').removeClass('active').removeAttr('aria-current');
                
                // Pastikan item root tetap ada
                if ($('#evidenceFolderBreadcrumb .breadcrumb-item[data-level="root"]').length === 0) {
                    $('#evidenceFolderBreadcrumb').html('<li class="breadcrumb-item" data-level="root"><a href="javascript:void(0);"><i class="ri-home-line me-1"></i> Root</a></li>');
                }
                
                // Tambahkan item outlet dan tandai sebagai active
                var outletItem = `<li class="breadcrumb-item active" aria-current="page" data-level="${outletId}"><a href="javascript:void(0);">${outletName}</a></li>`;
                $('#evidenceFolderBreadcrumb').append(outletItem);
                
                // Update current path
                currentEvidencePath = outletId;
                console.log('New current path:', currentEvidencePath);
                
                // Navigate ke folder sesuai jenis outlet
                if (outletId == 1) {
                    // Outlet pusat
                    displayRukoFolders(outletId);
                } else {
                    // Outlet lain
                    displayDateFolders(outletId);
                }
            });
            
            // Fungsi untuk menampilkan folder ruko (hanya untuk outlet pusat)
            function displayRukoFolders(outletId, outletName) {
                $('#evidenceLoadingIndicator').removeClass('d-none');
                $('#evidenceFolderContainer').addClass('d-none');
                
                // Ambil data ruko dari server via AJAX
                $.ajax({
                    url: '{{ route("maintenance.dashboard.get-evidence-rukos") }}',
                    type: 'GET',
                    data: { outlet_id: outletId },
                    dataType: 'json',
                    success: function(response) {
                        var rukos = response.data;
                        var folderContainer = $('#evidenceFolderContainer');
                        folderContainer.empty();
                        
                        if (rukos.length === 0) {
                            $('#evidenceEmptyMessage').removeClass('d-none');
                            $('#evidenceLoadingIndicator').addClass('d-none');
                            return;
                        }
                        
                        folderContainer.removeClass('d-none');
                        $('#evidenceLoadingIndicator').addClass('d-none');
                        
                        // Buat folder untuk setiap ruko
                        rukos.forEach(function(ruko) {
                            var path = outletId + '/' + ruko.id_ruko;
                            var folderItem = `
                                <div class="col-md-3 col-sm-4 col-6 mb-4">
                                    <div class="folder evidence-ruko-folder text-center p-3" data-path="${path}" data-name="${ruko.nama_ruko}">
                                        <div class="folder-icon mb-2">
                                            <i class="ri-folder-5-line"></i>
                                        </div>
                                        <h6 class="folder-title">${ruko.nama_ruko}</h6>
                                        <span class="folder-counter">${ruko.count} evidence</span>
                                    </div>
                                </div>
                            `;
                            folderContainer.append(folderItem);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading ruko data:", error);
                        $('#evidenceEmptyMessage').removeClass('d-none');
                        $('#evidenceLoadingIndicator').addClass('d-none');
                    }
                });
            }
            
            // Handle click pada folder ruko
            $(document).on('click', '.evidence-ruko-folder', function() {
                var path = $(this).data('path');
                var rukoName = $(this).data('name');
                
                console.log('Clicked ruko folder:', path, rukoName);
                
                // Tampilkan loading
                $('#evidenceLoadingIndicator').removeClass('d-none');
                $('#evidenceFolderContainer, #evidenceGalleryContainer').addClass('d-none');
                
                // Hapus kelas active dari breadcrumb item terakhir
                $('#evidenceFolderBreadcrumb .breadcrumb-item.active').removeClass('active').removeAttr('aria-current');
                
                // Tambahkan item ruko dan tandai sebagai active
                var rukoItem = `<li class="breadcrumb-item active" aria-current="page" data-level="${path}"><a href="javascript:void(0);">${rukoName}</a></li>`;
                $('#evidenceFolderBreadcrumb').append(rukoItem);
                
                // Update current path
                currentEvidencePath = path;
                console.log('New current path:', currentEvidencePath);
                
                // Parse path untuk mendapatkan outlet dan ruko ID
                var parts = path.split('/');
                // Tampilkan tanggal folders
                displayDateFolders(parts[0], parts[1]);
            });
            
            // Fungsi untuk menampilkan folder tanggal
            function displayDateFolders(outletId, rukoId) {
                $('#evidenceLoadingIndicator').removeClass('d-none');
                $('#evidenceFolderContainer').addClass('d-none');
                
                // Parameter untuk ajax request
                var params = { outlet_id: outletId };
                if (rukoId && outletId == 1) {
                    params.ruko_id = rukoId;
                }
                
                // Ambil data tanggal dari server via AJAX
                $.ajax({
                    url: '{{ route("maintenance.dashboard.get-evidence-dates") }}',
                    type: 'GET',
                    data: params,
                    dataType: 'json',
                    success: function(response) {
                        var dates = response.data;
                        var folderContainer = $('#evidenceFolderContainer');
                        folderContainer.empty();
                        
                        if (dates.length === 0) {
                            $('#evidenceEmptyMessage').removeClass('d-none');
                            $('#evidenceLoadingIndicator').addClass('d-none');
                            return;
                        }
                        
                        folderContainer.removeClass('d-none');
                        $('#evidenceLoadingIndicator').addClass('d-none');
                        
                        // Buat folder untuk setiap tanggal
                        dates.forEach(function(date) {
                            var path = currentEvidencePath + '/' + date.date;
                            var folderItem = `
                                <div class="col-md-3 col-sm-4 col-6 mb-4">
                                    <div class="folder evidence-date-folder text-center p-3" data-path="${path}" data-name="${date.display_date}">
                                        <div class="folder-icon mb-2">
                                            <i class="ri-folder-5-line"></i>
                                        </div>
                                        <h6 class="folder-title">${date.display_date}</h6>
                                        <span class="folder-counter">${date.count} evidence</span>
                                    </div>
                                </div>
                            `;
                            folderContainer.append(folderItem);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading date data:", error);
                        $('#evidenceEmptyMessage').removeClass('d-none');
                        $('#evidenceLoadingIndicator').addClass('d-none');
                    }
                });
            }
            
            // Handle click pada folder tanggal
            $(document).on('click', '.evidence-date-folder', function() {
                var path = $(this).data('path');
                var dateName = $(this).data('name');
                
                // Update breadcrumb
                $('#evidenceFolderBreadcrumb').find('.active').removeClass('active').removeAttr('aria-current');
                $('#evidenceFolderBreadcrumb').append(`<li class="breadcrumb-item active" aria-current="page" data-level="${path}"><a>${dateName}</a></li>`);
                
                // Update current path
                currentEvidencePath = path;
                
                // Tampilkan task folders
                displayTaskFolders(path);
            });
            
            // Fungsi untuk menampilkan folder task
            function displayTaskFolders(path) {
                $('#evidenceLoadingIndicator').removeClass('d-none');
                $('#evidenceFolderContainer').addClass('d-none');
                
                var pathParts = path.split('/');
                var outletId = pathParts[0];
                var params = { 
                    outlet_id: outletId,
                    date: pathParts[pathParts.length - 1]
                };
                
                // Jika ada ruko_id (untuk outlet pusat)
                if (pathParts.length > 2 && outletId == 1) {
                    params.ruko_id = pathParts[1];
                }
                
                // Ambil data task dari server via AJAX
                $.ajax({
                    url: '{{ route("maintenance.dashboard.get-evidence-tasks") }}',
                    type: 'GET',
                    data: params,
                    dataType: 'json',
                    success: function(response) {
                        var tasks = response.data;
                        var folderContainer = $('#evidenceFolderContainer');
                        folderContainer.empty();
                        
                        if (tasks.length === 0) {
                            $('#evidenceEmptyMessage').removeClass('d-none');
                            $('#evidenceLoadingIndicator').addClass('d-none');
                            return;
                        }
                        
                        folderContainer.removeClass('d-none');
                        $('#evidenceLoadingIndicator').addClass('d-none');
                        
                        // Buat folder untuk setiap task
                        tasks.forEach(function(task) {
                            var taskPath = path + '/' + task.id;
                            var folderName = task.task_number + ' - ' + task.title;
                            var folderItem = `
                                <div class="col-md-3 col-sm-4 col-6 mb-4">
                                    <div class="folder evidence-task-folder text-center p-3" data-path="${taskPath}" data-name="${folderName}" data-task-id="${task.id}">
                                        <div class="folder-icon mb-2">
                                            <i class="ri-folder-5-line"></i>
                                        </div>
                                        <h6 class="folder-title">${folderName}</h6>
                                        <span class="folder-counter">${task.count} evidence</span>
                                    </div>
                                </div>
                            `;
                            folderContainer.append(folderItem);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading task data:", error);
                        $('#evidenceEmptyMessage').removeClass('d-none');
                        $('#evidenceLoadingIndicator').addClass('d-none');
                    }
                });
            }
            
            // Handle click pada folder task
            $(document).on('click', '.evidence-task-folder', function() {
                var path = $(this).data('path');
                var taskName = $(this).data('name');
                var taskId = $(this).data('task-id');
                
                // Update breadcrumb
                $('#evidenceFolderBreadcrumb').find('.active').removeClass('active').removeAttr('aria-current');
                $('#evidenceFolderBreadcrumb').append(`<li class="breadcrumb-item active" aria-current="page" data-level="${path}"><a>${taskName}</a></li>`);
                
                // Update current path
                currentEvidencePath = path;
                
                // Tampilkan evidence files
                displayEvidenceFiles(taskId);
            });
            
            // Fungsi untuk menampilkan file evidence
            function displayEvidenceFiles(taskId) {
                $('#evidenceLoadingIndicator').removeClass('d-none');
                $('#evidenceFolderContainer').addClass('d-none');
                $('#evidenceGalleryContainer').addClass('d-none');
                
                // Ambil data evidence dari server via AJAX
                $.ajax({
                    url: '{{ route("maintenance.dashboard.get-evidence-files") }}',
                    type: 'GET',
                    data: { task_id: taskId },
                    dataType: 'json',
                    success: function(response) {
                        var evidenceFiles = response.data;
                        var galleryContainer = $('#evidenceGalleryContainer');
                        galleryContainer.empty();
                        
                        if (evidenceFiles.length === 0) {
                            $('#evidenceEmptyMessage').removeClass('d-none');
                            $('#evidenceLoadingIndicator').addClass('d-none');
                            return;
                        }
                        
                        galleryContainer.removeClass('d-none');
                        $('#evidenceLoadingIndicator').addClass('d-none');
                        
                        // Buat gallery item untuk setiap file
                        evidenceFiles.forEach(function(file) {
                            var itemType = file.type;
                            var mediaPreview = '';
                            
                            if (itemType === 'image') {
                                mediaPreview = `<img src="${file.file_path}" class="card-img-top" alt="${file.file_name}" style="height: 200px; object-fit: cover;">`;
                            } else {
                                mediaPreview = `
                                    <div class="video-thumbnail position-relative" style="height: 200px;">
                                        <img src="{{ asset('build/images/video-thumbnail.jpg') }}" class="card-img-top h-100" style="object-fit: cover;" alt="Video">
                                        <div class="play-icon">
                                            <i class="ri-play-circle-line"></i>
                                        </div>
                                    </div>
                                `;
                            }
                            
                            var galleryItem = `
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                    <div class="card h-100">
                                        <div class="gallery-img-wrapper">
                                            ${mediaPreview}
                                        </div>
                                        <div class="card-body">
                                            <h6 class="card-title">${file.notes || 'Evidence'}</h6>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="badge bg-primary">${file.task_number}</span>
                                                <span class="badge bg-info">${file.created_at}</span>
                                            </div>
                                            <p class="card-text small text-muted mb-2">${file.outlet_name}</p>
                                            <button class="btn btn-sm btn-primary w-100 view-evidence-file-btn" 
                                                data-evidence-id="${file.id}"
                                                data-evidence-type="${itemType}"
                                                data-evidence-src="${file.file_path}"
                                                data-evidence-title="${file.notes || 'Evidence'}"
                                                data-task-number="${file.task_number}"
                                                data-task-title="${file.task_title}"
                                                data-outlet="${file.outlet_name}"
                                                data-created-at="${file.created_at}">
                                                <i class="ri-eye-line me-1"></i> Lihat
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                            galleryContainer.append(galleryItem);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading evidence files:", error);
                        $('#evidenceEmptyMessage').removeClass('d-none');
                        $('#evidenceLoadingIndicator').addClass('d-none');
                    }
                });
            }
            
            // Handler untuk tombol view evidence file
            $(document).on('click', '.view-evidence-file-btn', function() {
                var evidenceId = $(this).data('evidence-id');
                var evidenceType = $(this).data('evidence-type');
                var evidenceSrc = $(this).data('evidence-src');
                var evidenceTitle = $(this).data('evidence-title');
                var taskNumber = $(this).data('task-number');
                var taskTitle = $(this).data('task-title');
                var outlet = $(this).data('outlet');
                var createdAt = $(this).data('created-at');
                
                var modal = $('#galleryEvidencePreviewModal');
                var evidenceContent = modal.find('#galleryEvidenceContent');
                
                // Reset content
                evidenceContent.empty();
                
                // Create evidence element based on type
                if (evidenceType === 'image') {
                    evidenceContent.html(`<img src="${evidenceSrc}" class="img-fluid" alt="${taskTitle}">`);
                } else {
                    evidenceContent.html(`
                        <video controls class="w-100">
                            <source src="${evidenceSrc}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    `);
                }
                
                // Update details
                modal.find('#galleryEvidenceTaskTitle').text(taskTitle);
                modal.find('#galleryEvidenceTaskNumber').text(taskNumber);
                modal.find('#galleryEvidenceOutlet').text(outlet);
                modal.find('#galleryEvidenceDate').text('Tanggal: ' + createdAt);
                
                // Set download button attributes
                var downloadBtn = modal.find('#galleryEvidenceDownloadBtn');
                downloadBtn.attr('href', evidenceSrc);
                downloadBtn.attr('download', evidenceTitle);
                
                // Show the modal
                modal.modal('show');
            });
            
            // Handle breadcrumb navigation
            $(document).on('click', '#evidenceFolderBreadcrumb .breadcrumb-item a', function(e) {
                e.preventDefault(); // Penting untuk mencegah perilaku default
                console.log('Breadcrumb clicked');
                
                var $item = $(this).parent();
                var level = $item.data('level');
                
                console.log('Clicked breadcrumb level:', level);
                
                // Jangan lakukan apa-apa jika item sudah aktif
                if ($item.hasClass('active')) {
                    console.log('Item already active, no action');
                    return;
                }
                
                // Tampilkan loading indicator
                $('#evidenceLoadingIndicator').removeClass('d-none');
                $('#evidenceFolderContainer, #evidenceGalleryContainer, #evidenceEmptyMessage').addClass('d-none');
                
                // Hapus kelas active dari semua item dan tambahkan ke item yang diklik
                $('#evidenceFolderBreadcrumb .breadcrumb-item').removeClass('active').removeAttr('aria-current');
                $item.addClass('active').attr('aria-current', 'page');
                
                // Hapus semua item setelah yang diklik
                $item.nextAll().remove();
                
                // Update currentPath
                currentEvidencePath = level;
                console.log('New current path:', currentEvidencePath);
                
                // Navigasi berdasarkan level
                if (level === 'root') {
                    // Root level - tampilkan daftar outlet
                    displayOutletFolders();
                } else {
                    // Parse path parts
                    var parts = level.toString().split('/');
                    console.log('Path parts:', parts);
                    
                    if (parts.length === 1) {
                        // We're at outlet level
                        var outletId = parts[0];
                        if (outletId == 1) {
                            // Outlet pusat - tampilkan daftar ruko
                            displayRukoFolders(outletId);
                        } else {
                            // Outlet lain - tampilkan daftar tanggal
                            displayDateFolders(outletId);
                        }
                    } else if (parts.length === 2) {
                        if (parts[0] == 1) {
                            // Outlet pusat + ruko - tampilkan daftar tanggal
                            displayDateFolders(parts[0], parts[1]);
                        } else {
                            // Outlet lain + tanggal - tampilkan daftar task
                            displayTaskFolders(level);
                        }
                    } else if (parts.length === 3) {
                        if (parts[0] == 1) {
                            // Outlet pusat + ruko + tanggal - tampilkan daftar task
                            displayTaskFolders(level);
                        } else {
                            // Outlet lain + tanggal + task - tampilkan evidence
                            displayEvidenceFiles(parts[2]);
                        }
                    } else if (parts.length === 4) {
                        // Outlet pusat + ruko + tanggal + task - tampilkan evidence
                        displayEvidenceFiles(parts[3]);
                    }
                }
            });
            
            // Toggle view button
            $('#toggleEvidenceViewBtn').on('click', function() {
                flatEvidenceView = !flatEvidenceView;
                $(this).find('span').text(flatEvidenceView ? 'Tampilan Folder' : 'Tampilan Datar');
                
                if (flatEvidenceView) {
                    displayAllEvidenceFlat();
                } else {
                    if (currentEvidencePath === 'root') {
                        displayOutletFolders();
                    } else {
                        // Kembali ke view saat ini
                        var parts = currentEvidencePath.split('/');
                        if (parts.length === 1) {
                            if (parts[0] == 1) {
                                displayRukoFolders(parts[0]);
                            } else {
                                displayDateFolders(parts[0]);
                            }
                        } else if (parts.length === 2) {
                            if (parts[0] == 1) {
                                displayDateFolders(parts[0], parts[1]);
                            } else {
                                displayTaskFolders(currentEvidencePath);
                            }
                        } else if (parts.length === 3) {
                            if (parts[0] == 1) {
                                displayTaskFolders(currentEvidencePath);
                            } else {
                                displayEvidenceFiles(parts[2]);
                            }
                        } else if (parts.length === 4) {
                            displayEvidenceFiles(parts[3]);
                        }
                    }
                }
            });
            
            // Tampilkan semua evidence dalam view datar
            function displayAllEvidenceFlat() {
                $('#evidenceLoadingIndicator').removeClass('d-none');
                $('#evidenceFolderContainer').addClass('d-none');
                $('#evidenceGalleryContainer').addClass('d-none');
                
                var searchText = $('#evidenceSearchInput').val().toLowerCase();
                var filterType = $('#evidenceFilterSelect').val();
                
                // Parameter untuk request
                var params = {};
                if (searchText) {
                    params.search = searchText;
                }
                if (filterType !== 'all') {
                    params.type = filterType;
                }
                
                // Ambil semua evidence files dari server
                $.ajax({
                    url: '{{ route("maintenance.dashboard.get-all-evidence") }}',
                    type: 'GET',
                    data: params,
                    dataType: 'json',
                    success: function(response) {
                        var evidenceFiles = response.data;
                        var galleryContainer = $('#evidenceGalleryContainer');
                        galleryContainer.empty();
                        
                        if (evidenceFiles.length === 0) {
                            $('#evidenceEmptyMessage').removeClass('d-none');
                            galleryContainer.addClass('d-none');
                            $('#evidenceFolderContainer').addClass('d-none');
                            return;
                        }
                        
                        galleryContainer.removeClass('d-none');
                        $('#evidenceFolderContainer').addClass('d-none');
                        $('#evidenceEmptyMessage').addClass('d-none');
                        
                        // Buat gallery items
                        evidenceFiles.forEach(function(item) {
                            var galleryItem = `
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                    <div class="card h-100">
                                        <div class="gallery-img-wrapper" style="height: 200px;">
                                            ${item.type === 'image' 
                                                ? `<img src="${item.file_path}" class="card-img-top" alt="${item.task_title}" style="height: 100%; object-fit: cover;">` 
                                                : `<div class="video-thumbnail position-relative h-100">
                                                    <img src="{{ asset('build/images/video-thumbnail.jpg') }}" class="card-img-top h-100" alt="Video">
                                                    <div class="play-icon">
                                                        <i class="ri-play-circle-line"></i>
                                                    </div>
                                                </div>`
                                            }
                                        </div>
                                        <div class="card-body">
                                            <h6 class="card-title">${item.task_title}</h6>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="badge bg-primary">${item.task_number}</span>
                                                <span class="badge bg-info">${item.created_at_display}</span>
                                            </div>
                                            <p class="card-text small text-muted">${item.outlet}</p>
                                            <button class="btn btn-sm btn-primary w-100 view-evidence-btn" 
                                                    data-evidence-id="${item.id}"
                                                    data-evidence-type="${item.type}"
                                                    data-evidence-src="${item.file_path}"
                                                    data-evidence-title="${item.task_title}"
                                                    data-task-number="${item.task_number}"
                                                    data-outlet="${item.outlet}"
                                                    data-created-at="${item.created_at_display}">
                                                <i class="ri-eye-line me-1"></i> Lihat
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                            galleryContainer.append(galleryItem);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading evidence data:", error);
                        $('#evidenceEmptyMessage').removeClass('d-none');
                        $('#evidenceLoadingIndicator').addClass('d-none');
                    }
                });
            }
        });

        // Filter pencarian
        $('#evidenceSearchBtn').on('click', function() {
            var searchText = $('#evidenceSearchInput').val().toLowerCase();
            console.log('Search clicked with text:', searchText);
            
            $('#evidenceLoadingIndicator').removeClass('d-none');
            $('#evidenceFolderContainer, #evidenceGalleryContainer').addClass('d-none');
            
            if (flatEvidenceView) {
                // Dalam tampilan datar, cari di seluruh evidence
                displayAllEvidenceFlat(searchText, $('#evidenceFilterSelect').val());
            } else if (currentEvidencePath === 'root') {
                // Di level root, filter outlet berdasarkan nama
                filterOutletsBySearch(searchText);
            } else {
                // Di level lain, kembali ke root dan lakukan pencarian
                currentEvidencePath = 'root';
                $('#evidenceFolderBreadcrumb').html('<li class="breadcrumb-item active" aria-current="page" data-level="root"><a><i class="ri-home-line me-1"></i> Root</a></li>');
                filterOutletsBySearch(searchText);
            }
        });
        
        // Search on input
        $('#evidenceSearchInput').on('keyup', function(e) {
            if (e.key === 'Enter') {
                var searchText = $(this).val().toLowerCase();
                if (flatEvidenceView) {
                    displayAllEvidenceFlat();
                } else {
                    if (currentEvidencePath === 'root') {
                        displayOutletFolders();
                    }
                }
            }
        });
        
        // Filter berdasarkan tipe file
        $('#evidenceFilterSelect').on('change', function() {
            if (flatEvidenceView) {
                displayAllEvidenceFlat();
            }
        });
        
        // Stop video playback when gallery preview modal is closed
        $('#galleryEvidencePreviewModal').on('hidden.bs.modal', function () {
            var video = $(this).find('video');
            if (video.length > 0) {
                video[0].pause();
            }
        });

        // Tambahkan di bagian atas script Anda
        function logPath() {
            console.log('Current path:', currentEvidencePath);
            console.log('Breadcrumb items:', $('#evidenceFolderBreadcrumb li').length);
            $('#evidenceFolderBreadcrumb li').each(function(i) {
                console.log(`Breadcrumb ${i}:`, $(this).data('level'), $(this).text());
            });
        }

        // Tambahkan panggilan logPath() di setiap event handler breadcrumb dan folder click
    </script>
@endsection

