@extends('layouts.master')

@section('title')
    Maintenance Tasks
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    
    <style>
        .search-box {
            min-width: 200px;
        }
        
        .dataTables_filter {
            display: none;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Task Card Styles */
        .task-card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .task-card .card-body {
            padding: 1.25rem;
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .task-number {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .task-priority {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .priority-high {
            background: #ffe5e5;
            color: #dc3545;
        }

        .priority-medium {
            background: #fff4e5;
            color: #fd7e14;
        }

        .priority-low {
            background: #e5f5e9;
            color: #198754;
        }

        .task-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #344767;
        }

        .task-meta {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .meta-item i {
            font-size: 1rem;
            color: #344767;
        }

        .task-status {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-open {
            background: #e5f0ff;
            color: #0d6efd;
        }

        .status-in-progress {
            background: #fff4e5;
            color: #fd7e14;
        }

        .status-completed {
            background: #e5f5e9;
            color: #198754;
        }

        .task-actions {
            display: flex;
            gap: 0.5rem;
        }

        .task-actions button {
            padding: 0.375rem;
            border-radius: 8px;
            border: none;
            background: #f8f9fa;
            color: #344767;
            transition: all 0.2s;
        }

        .task-actions button:hover {
            background: #e9ecef;
        }

        /* Filter Section */
        .filter-section {
            background: #fff;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .filter-item {
            position: relative;
        }

        .filter-item i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .filter-item select,
        .filter-item input {
            padding-left: 2.5rem;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state img {
            width: 200px;
            margin-bottom: 1.5rem;
        }

        .empty-state h5 {
            color: #344767;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .task-meta {
                flex-direction: column;
                gap: 0.5rem;
            }

            .task-actions {
                flex-wrap: wrap;
            }

            .filter-section .row > div {
                margin-bottom: 1rem;
            }
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Maintenance Tasks</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Filter Section -->
            <div class="filter-section">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="filter-item">
                            <i class="ri-building-line"></i>
                            <select class="form-select" id="id_outlet">
                                <option value="">Select Outlet</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="filter-item">
                            <i class="ri-home-line"></i>
                            <select class="form-select" id="id_ruko">
                                <option value="">Select Ruko</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="filter-item">
                            <i class="ri-flag-line"></i>
                            <select class="form-select" id="status_filter">
                                <option value="">All Status</option>
                                <option value="OPEN">Open</option>
                                <option value="IN_PROGRESS">In Progress</option>
                                <option value="COMPLETED">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="filter-item">
                            <i class="ri-search-line"></i>
                            <input type="text" class="form-control search" placeholder="Search tasks...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks Grid -->
            <div class="row" id="tasksContainer">
                <!-- Task cards will be inserted here -->
            </div>

            <!-- Empty State -->
            <div class="empty-state" id="emptyState" style="display: none;">
                <img src="{{ asset('images/empty-tasks.svg') }}" alt="No tasks">
                <h5>No Tasks Found</h5>
                <p>There are no maintenance tasks matching your filters</p>
                <button class="btn btn-primary" onclick="resetFilters()">
                    <i class="ri-refresh-line me-1"></i> Reset Filters
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Task Detail Modal -->
<div class="modal fade" id="taskDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">Task Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="taskDetailContent"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
<script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
// Task card template
function createTaskCard(task) {
    return `
        <div class="col-md-6 col-lg-4">
            <div class="task-card card">
                <div class="card-body">
                    <div class="task-header">
                        <span class="task-number">#${task.task_number}</span>
                        <span class="task-priority priority-${task.priority.toLowerCase()}">${task.priority}</span>
                    </div>
                    <h5 class="task-title">${task.title}</h5>
                    <div class="task-meta">
                        <div class="meta-item">
                            <i class="ri-building-line"></i>
                            <span>${task.outlet_name}</span>
                        </div>
                        <div class="meta-item">
                            <i class="ri-calendar-line"></i>
                            <span>${moment(task.due_date).format('DD MMM YYYY')}</span>
                        </div>
                        <div class="meta-item">
                            <i class="ri-user-line"></i>
                            <span>${task.assigned_to_name}</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="task-status status-${task.status.toLowerCase()}">${task.status}</span>
                        <div class="task-actions">
                            <button onclick="viewTask(${task.id})" class="btn-icon">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button onclick="updateStatus(${task.id})" class="btn-icon">
                                <i class="ri-edit-line"></i>
                            </button>
                            <button onclick="completeTask(${task.id})" class="btn-icon text-success">
                                <i class="ri-check-line"></i>
                            </button>
                            ${task.status === 'IN_REVIEW' ? `
                            <button onclick="openCaptureEvidenceModal(${task.id})" class="btn-icon text-primary">
                                <i class="ri-camera-line"></i>
                            </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Load tasks
function loadTasks() {
    const outletId = $('#id_outlet').val();
    const rukoId = $('#id_ruko').val();
    const status = $('#status_filter').val();
    const search = $('.search').val();

    $.ajax({
        url: '/maintenance/tasks/data',
        data: { outlet_id: outletId, ruko_id: rukoId, status, search },
        success: function(response) {
            const container = $('#tasksContainer');
            container.empty();

            if (response.data.length === 0) {
                $('#emptyState').show();
                return;
            }

            $('#emptyState').hide();
            response.data.forEach(task => {
                container.append(createTaskCard(task));
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}

// Event handlers
$(document).ready(function() {
    loadTasks();

    // Filter change handlers
    $('#id_outlet, #id_ruko, #status_filter').change(loadTasks);
    $('.search').on('keyup', _.debounce(loadTasks, 300));

    // Responsive handlers
    $(window).resize(function() {
        adjustLayout();
    });
});

// View task details
function viewTask(taskId) {
    $.get(`/maintenance/tasks/${taskId}`, function(response) {
        $('#taskDetailContent').html(`
            <div class="task-detail-content">
                <!-- Add detailed task content here -->
            </div>
        `);
        $('#taskDetailModal').modal('show');
    });
}

// Helper functions
function adjustLayout() {
    const width = $(window).width();
    if (width < 768) {
        // Adjust for mobile
        $('.task-card').addClass('mb-3');
    } else {
        // Adjust for desktop
        $('.task-card').removeClass('mb-3');
    }
}

function resetFilters() {
    $('#id_outlet, #id_ruko, #status_filter').val('');
    $('.search').val('');
    loadTasks();
}
</script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
<script src="{{ URL::asset('build/js/maintenance-evidence.js') }}"></script>
@endsection
