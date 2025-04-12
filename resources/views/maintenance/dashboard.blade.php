@extends('layouts.master')
@section('title')
    Dashboard Maintenance
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/datatables/datatables.min.css') }}" rel="stylesheet">
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

    <div class="row">
        <div class="col-xl-7">
            <div class="card">
                <div class="card-header d-flex align-items-center flex-wrap gap-3">
                    <h5 class="card-title mb-0 flex-grow-1">Aktivitas Maintenance</h5>
                    <div>
                        <button type="button" class="btn btn-subtle-secondary btn-sm">
                            ALL
                        </button>
                        <button type="button" class="btn btn-subtle-secondary btn-sm">
                            1M
                        </button>
                        <button type="button" class="btn btn-subtle-secondary btn-sm">
                            6M
                        </button>
                        <button type="button" class="btn btn-subtle-primary btn-sm">
                            1Y
                        </button>
                    </div>
                </div>
                <div class="card-body ps-0">
                    <div id="maintenance_activity_chart" data-colors='["--tb-secondary", "--tb-success", "--tb-danger"]'
                        class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="d-flex align-items-center mb-3">
                <h6 class="card-title flex-grow-1 mb-0">Evidence Terbaru</h6>
                <a href="#!" class="fs-sm flex-shrink-0">Lihat Semua <i class="ti ti-arrow-narrow-right"></i></a>
            </div>
            <!-- Swiper untuk media evidence -->
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    @foreach($recentEvidenceMedia as $media)
                    <div class="swiper-slide">
                        <div class="card bg-body-secondary border-0">
                            <div class="card-body p-2">
                                <div class="p-3 bg-body">
                                    @if($media['type'] == 'image')
                                        <img src="{{ asset('storage/'.$media['path']) }}" alt="Evidence Image" class="img-fluid px-3" style="height: 150px; object-fit: cover;">
                                    @else
                                        <div class="position-relative" style="height: 150px;">
                                            <video src="{{ asset('storage/'.$media['path']) }}" class="w-100 h-100" style="object-fit: cover;"></video>
                                            <div class="position-absolute top-50 start-50 translate-middle">
                                                <i class="ri-play-circle-fill text-white fs-1"></i>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <span class="float-end fw-semibold clearfix">Task #{{ $media['task_number'] }}</span>
                                    <h6><a href="#">{{ Str::limit($media['task_title'], 20) }}</a></h6>
                                    <p class="text-muted fs-md mb-0">{{ $media['created_at']->format('d M, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Maintenance Tasks Terbaru</h4>
                    <div class="flex-shrink-0">
                        <div class="dropdown card-header-dropdown">
                            <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <span class="fw-semibold text-uppercase fs-sm">Filter: </span><span
                                    class="text-muted">Hari Ini<i class="ti ti-chevron-down ms-1"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">Hari Ini</a>
                                <a class="dropdown-item" href="#">Kemarin</a>
                                <a class="dropdown-item" href="#">7 Hari Terakhir</a>
                                <a class="dropdown-item" href="#">30 Hari Terakhir</a>
                                <a class="dropdown-item" href="#">Bulan Ini</a>
                                <a class="dropdown-item" href="#">Bulan Lalu</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive table-card mt-0">
                        <table class="table table-centered align-middle table-nowrap mb-0" id="tasksTable">
                            <thead class="text-muted table-active">
                                <tr>
                                    <th scope="col">Task ID</th>
                                    <th scope="col">Judul</th>
                                    <th scope="col">Assigned To</th>
                                    <th scope="col">Prioritas</th>
                                    <th scope="col">Tanggal Dibuat</th>
                                    <th scope="col">Due Date</th>
                                    <th scope="col">Lokasi</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTasks as $task)
                                <tr>
                                    <td>
                                        <a href="#" class="fw-medium link-primary">{{ $task->task_number }}</a>
                                    </td>
                                    <td>
                                        <h6><a href="#">{{ Str::limit($task->title, 30) }}</a></h6>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($task->creator)
                                            <div class="flex-shrink-0 me-2">
                                                <div class="avatar-xxs rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
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
                                            @else
                                            <div class="flex-grow-1">Belum Ditugaskan</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($task->priority == 'HIGH')
                                            <span class="badge bg-danger-subtle text-danger">Tinggi</span>
                                        @elseif($task->priority == 'MEDIUM')
                                            <span class="badge bg-warning-subtle text-warning">Sedang</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success">Rendah</span>
                                        @endif
                                    </td>
                                    <td>{{ $task->created_at->format('d M, Y') }}</td>
                                    <td>{{ $task->due_date ? date('d M, Y', strtotime($task->due_date)) : '-' }}</td>
                                    <td>{{ $task->location }}</td>
                                    <td>
                                        @switch($task->status)
                                            @case('TASK')
                                                <span class="badge bg-info-subtle text-info">New</span>
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
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-icon btn-sm btn-subtle-secondary" disabled>
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-icon btn-sm btn-subtle-secondary" disabled>
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="align-items-center mt-4 pt-2 justify-content-between d-flex">
                        <div class="flex-shrink-0">
                            <div class="text-muted">
                                Menampilkan <span class="fw-semibold">{{ count($recentTasks) }}</span> dari <span class="fw-semibold">{{ $totalTasks }}</span> Task
                            </div>
                        </div>
                        <ul class="pagination pagination-separated pagination-sm mb-0">
                            <li class="page-item disabled">
                                <a href="#" class="page-link">←</a>
                            </li>
                            <li class="page-item active">
                                <a href="#" class="page-link">1</a>
                            </li>
                            <li class="page-item">
                                <a href="#" class="page-link">2</a>
                            </li>
                            <li class="page-item">
                                <a href="#" class="page-link">3</a>
                            </li>
                            <li class="page-item">
                                <a href="#" class="page-link">→</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-3 col-lg-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Aktivitas Terbaru</h4>
                    <a href="#" class="fs-sm flex-shrink-0">Lihat Semua <i class="ti ti-arrow-narrow-right"></i></a>
                </div>

                <div data-simplebar style="max-height: 380px;">
                    @foreach($recentActivities as $activity)
                    <div class="p-3 border-bottom border-bottom-dashed">
                        <div class="d-flex align-items-center gap-2">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-light text-primary rounded d-flex align-items-center justify-content-center">
                                        @php
                                            $name = $activity->user->name ?? 'User';
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
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    @if(isset($activity->user) && is_object($activity->user) && isset($activity->user->name))
                                        {{ $activity->user->name }}
                                    @else
                                        System
                                    @endif
                                    <span class="text-muted fs-sm">
                                        @if(is_object($activity) || is_array($activity))
                                            {{ $activity->action ?? $activity['action'] ?? 'Activity' }}
                                            @if(isset($activity->description) || (is_array($activity) && isset($activity['description'])))
                                                - {{ $activity->description ?? $activity['description'] }}
                                            @endif
                                        @else
                                            Activity
                                        @endif
                                    </span>
                                </h6>
                                <p class="fs-13 text-muted mb-0">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="#" class="btn btn-icon btn-sm btn-subtle-info">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="col-xxl-3 col-lg-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Task Stats per Kategori</h4>
                    <div class="flex-shrink-0">
                        <div class="dropdown card-header-dropdown">
                            <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <span class="text-muted">Laporan<i class="ti ti-chevron-down ms-1"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">Download Laporan</a>
                                <a class="dropdown-item" href="#">Export</a>
                                <a class="dropdown-item" href="#">Import</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @foreach($labelStats as $label)
                    <div class="mb-4">
                        <span class="badge bg-dark-subtle text-body float-end">{{ $label['percentage'] }}%</span>
                        <h6 class="mb-2">{{ $label['name'] }}</h6>
                        <div class="progress progress-sm" role="progressbar" aria-label="{{ $label['name'] }}"
                            aria-valuenow="{{ $label['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar {{ $label['color'] }} progress-bar-striped progress-bar-animated"
                                style="width: {{ $label['percentage'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="col-xxl-6">
            <div class="card">
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

    <!-- Section untuk Overdue Tasks Detail -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Task Overdue</h4>
                    <a href="#" class="fs-sm flex-shrink-0">Lihat Semua <i class="ti ti-arrow-narrow-right"></i></a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless align-middle mb-0">
                            <thead class="text-muted table-light">
                                <tr>
                                    <th scope="col">Task</th>
                                    <th scope="col">Due Date</th>
                                    <th scope="col">Terlambat</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Prioritas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($overdueTasksList as $task)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ $task->task_number }}</h6>
                                                <p class="fs-sm text-muted mb-0">{{ Str::limit($task->title, 30) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger">
                                            {{ \Carbon\Carbon::parse($task->due_date)->diffForHumans(null, true) }}
                                        </span>
                                    </td>
                                    <td>
                                        @switch($task->status)
                                            @case('TASK')
                                                <span class="badge bg-info-subtle text-info">New</span>
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
                                            @default
                                                <span class="badge bg-light">{{ $task->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($task->priority == 'HIGH')
                                            <span class="badge bg-danger-subtle text-danger">Tinggi</span>
                                        @elseif($task->priority == 'MEDIUM')
                                            <span class="badge bg-warning-subtle text-warning">Sedang</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success">Rendah</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
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
@endsection
@section('script')
    <!-- apexcharts -->
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
            activity: null
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
            
            // Bersihkan elemen DOM
            document.getElementById('task_status_chart').innerHTML = '';
            document.getElementById('maintenance_categories_chart').innerHTML = '';
            document.getElementById('maintenance_activity_chart').innerHTML = '';
        }
        
        function initSwiper() {
            // Evidence Swiper
            new Swiper(".mySwiper", {
                slidesPerView: 1,
                spaceBetween: 20,
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                breakpoints: {
                    640: { slidesPerView: 2 },
                    768: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 }
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev"
                }
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
                    type: 'donut',
                    height: 300
                },
                labels: taskStatusLabels,
                colors: ['#3498db', '#f39c12', '#e74c3c', '#2ecc71', '#9b59b6', '#1abc9c'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: function() {
                                        return taskStatusSeries.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                legend: {
                    position: 'bottom'
                }
            };
            
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
    </script>
@endsection
