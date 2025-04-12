<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="build/images/logo-sm.png" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="build/images/logo-dark.png" alt="" height="22">
                        </span>
                    </a>

                    <a href="index" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="build/images/logo-sm.png" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="build/images/logo-light.png" alt="" height="22">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>

            <div class="d-flex align-items-center">

                <div class="dropdown ms-1 topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-light rounded-circle user-name-text" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     @switch(Session::get('lang'))
                        @case('id')
                        <img src="{{ URL::asset('build/images/flags/id.svg') }}" class="rounded" alt="Header Language" height="20">
                        @break
                        @default
                        <img src="{{ URL::asset('build/images/flags/us.svg') }}" class="rounded" alt="Header Language" height="20">
                        @endswitch
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                
                        <!-- item-->
                        <a href="{{ url('index/en') }}" class="dropdown-item notify-item language py-2" data-lang="en" title="English">
                            <img src="{{ URL::asset('build/images/flags/us.svg') }}" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">English</span>
                        </a>
                
                        <!-- item-->
                        <a href="{{ url('index/id') }}" class="dropdown-item notify-item language" data-lang="id" title="Indonesian">
                            <img src="{{ URL::asset('build/images/flags/id.svg') }}" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">Bahasa Indonesia</span>
                        </a>
                    </div>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-light rounded-circle user-name-text" data-toggle="fullscreen">
                        <i class='ti ti-arrows-maximize fs-3xl'></i>
                    </button>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-light rounded-circle user-name-text" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <i class='ti ti-bell-ringing fs-3xl'></i>
                        <span class="position-absolute topbar-badge fs-3xs translate-middle badge rounded-pill bg-danger notification-badge d-none">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">
                        <div class="dropdown-head rounded-top">
                            <div class="p-3 border-bottom border-bottom-dashed">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-0 fs-lg fw-semibold">Notifikasi <span class="badge bg-danger-subtle text-danger fs-sm notification-badge">0</span></h6>
                                        <p class="fs-md text-muted mt-1 mb-0">Anda memiliki <span class="fw-semibold notification-unread">0</span> pesan belum dibaca</p>
                                    </div>
                                    <div class="col-auto dropdown">
                                        <a href="javascript:void(0);" data-bs-toggle="dropdown" class="link-secondary fs-md"><i class="bi bi-three-dots-vertical"></i></a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item mark-all-read" href="#">Tandai semua dibaca</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="py-2 ps-2" id="notificationItemsTabContent">
                            <div data-simplebar style="max-height: 300px;" class="pe-2">
                                <!-- Notification items will be loaded here -->
                                <div class="text-center p-3">
                                    <p class="text-muted">Memuat notifikasi...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown ms-sm-3 topbar-head-dropdown dropdown-hover-end header-item topbar-user">
                    <button type="button" class="btn shadow-none btn-icon" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            @php
                                $avatar = auth()->user()->jenis_kelamin === 'L' ? 'avatar-3.jpg' : 'avatar-4.jpg';
                            @endphp
                            <img class="rounded-circle header-profile-user" src="{{ asset('build/images/users/' . $avatar) }}" alt="Header Avatar">
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">
                            Welcome {{ auth()->user()->nama_lengkap }}!<br>
                            {{ auth()->user()->outlet->nama_outlet ?? 'N/A' }}<br>
                            {{ auth()->user()->divisi->nama_divisi ?? 'N/A' }}<br>
                            {{ auth()->user()->jabatan->nama_jabatan ?? 'N/A' }}
                        </h6>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item fs-sm" href="#" data-bs-toggle="modal" data-bs-target="#signatureModal">
                            <i class="bi bi-pencil-square text-muted align-middle me-1"></i> 
                            <span class="align-middle">@lang('translation.signature')</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item fs-sm" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right text-muted align-middle me-1"></i> 
                            <span class="align-middle" data-key="t-logout">@lang('translation.logout')</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="wrapper"></div>

<!-- removeNotificationModal -->
<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
            </div>
            <div class="modal-body p-md-5">
                <div class="text-center">
                    <div class="text-danger">
                        <i class="bi bi-trash display-4"></i>
                    </div>
                    <div class="mt-4 fs-base">
                        <h4 class="mb-1">Are you sure ?</h4>
                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Signature Modal -->
<div id="signatureModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('translation.signature')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="signatureForm" action="{{ route('signature.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="signatureFile" class="form-label">@lang('translation.upload_signature')</label>
                        <input type="file" class="form-control" id="signatureFile" name="signatureFile" accept="image/*" onchange="previewFile()">
                        <img id="filePreview" src="" alt="File Preview" class="img-fluid mt-2" style="display: none; max-width: 300px; height: auto;">
                    </div>
                    <div class="mb-3">
                        <label for="signatureDraw" class="form-label">@lang('translation.draw_signature')</label>
                        <canvas id="signatureDraw" class="border" width="400" height="200"></canvas>
                        <input type="hidden" id="signatureData" name="signatureData">
                        <img id="drawPreview" src="" alt="Draw Preview" class="img-fluid mt-2" style="display: none; max-width: 300px; height: auto;">
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="clearCanvas()">@lang('translation.clear')</button>
                    <button type="submit" class="btn btn-primary">@lang('translation.save_signature')</button>
                </form>
                <!-- Pratinjau tanda tangan yang sudah ada -->
                @if(auth()->user()->signature_path)
                    <div class="mt-3">
                        <h6>@lang('translation.current_signature'):</h6>
                        <img src="{{ asset('storage/' . auth()->user()->signature_path) }}" alt="Current Signature" class="img-fluid mt-2" style="max-width: 300px; height: auto;">
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const signaturePath = "{{ session('signaturePath') }}";
        if (signaturePath) {
            const savedPreview = document.getElementById('savedPreview');
            savedPreview.src = "{{ asset('storage') }}/" + signaturePath;
            savedPreview.style.display = 'block';

            // Tampilkan SweetAlert
            Swal.fire({
                title: 'Success!',
                text: '@lang('translation.success_message')',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        }
    });

    // JavaScript for drawing on canvas
    const canvas = document.getElementById('signatureDraw');
    const ctx = canvas.getContext('2d');
    let drawing = false;

    canvas.addEventListener('mousedown', (event) => {
        drawing = true;
        draw(event);
    });
    canvas.addEventListener('mouseup', () => {
        drawing = false;
        ctx.beginPath();
        updateDrawPreview();
    });
    canvas.addEventListener('mousemove', draw);

    function draw(event) {
        if (!drawing) return;
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';
        ctx.lineTo(event.offsetX, event.offsetY);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(event.offsetX, event.offsetY);
    }

    function clearCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        updateDrawPreview();
    }

    function updateDrawPreview() {
        const drawPreview = document.getElementById('drawPreview');
        drawPreview.src = canvas.toDataURL();
        drawPreview.style.display = 'block';
    }

    function previewFile() {
        const file = document.getElementById('signatureFile').files[0];
        const reader = new FileReader();
        reader.onloadend = function() {
            const filePreview = document.getElementById('filePreview');
            filePreview.src = reader.result;
            filePreview.style.display = 'block';
        }
        if (file) {
            reader.readAsDataURL(file);
        }
    }

    document.getElementById('signatureForm').addEventListener('submit', function() {
        document.getElementById('signatureData').value = canvas.toDataURL();
    });
</script>

<!-- Tambahkan elemen pratinjau untuk tanda tangan yang disimpan -->
<img id="savedPreview" src="" alt="Saved Signature Preview" class="img-fluid mt-2" style="display: none; max-width: 300px; height: auto;">

<script>
// Fungsi untuk memuat notifikasi
function loadNotifications() {
    console.log("Memuat notifikasi...");
    $.ajax({
        url: '/notifications/get',
        type: 'GET',
        success: function(response) {
            console.log("Response notifikasi:", response);
            if (response.success && response.notifications) {
                renderNotifications(response.notifications);
                updateNotificationBadge(response.unread_count);
            } else {
                console.error("Format response tidak sesuai:", response);
            }
        },
        error: function(error) {
            console.error('Error loading notifications:', error);
        }
    });
}

// Render notifikasi
function renderNotifications(notifications) {
    var container = $('#notificationItemsTabContent').find('[data-simplebar]');
    container.empty();
    
    if (notifications.length === 0) {
        container.html('<div class="text-center p-3"><p class="text-muted">Tidak ada notifikasi</p></div>');
        return;
    }
    
    // Kelompokkan notifikasi berdasarkan status baca
    var unreadNotifications = notifications.filter(n => n.is_read === 0 || n.is_read === false);
    var readNotifications = notifications.filter(n => n.is_read === 1 || n.is_read === true);
    
    // Render notifikasi belum dibaca
    if (unreadNotifications.length > 0) {
        container.append('<h6 class="text-overflow text-muted fs-sm my-2 text-uppercase notification-title">Baru</h6>');
        unreadNotifications.forEach(function(notification) {
            container.append(createNotificationItem(notification, true));
        });
    }
    
    // Render notifikasi sudah dibaca
    if (readNotifications.length > 0) {
        container.append('<h6 class="text-overflow text-muted fs-sm my-2 text-uppercase notification-title">Sebelumnya</h6>');
        readNotifications.forEach(function(notification) {
            container.append(createNotificationItem(notification, false));
        });
    }
}

// Buat item notifikasi
function createNotificationItem(notification, isUnread) {
    var iconClass = getNotificationIcon(notification.type);
    var timeAgo = formatTimeAgo(notification.created_at);
    var unreadClass = isUnread ? 'unread-message' : '';
    
    return `
    <div class="text-reset notification-item d-block dropdown-item position-relative ${unreadClass}" data-id="${notification.id}">
        <div class="d-flex notification-item-clickable" data-notification-id="${notification.id}" 
             data-task-id="${notification.task_id}" data-message="${notification.message}">
            <div class="avatar-xs me-3 flex-shrink-0">
                <span class="avatar-title bg-info-subtle text-info rounded-circle fs-lg">
                    <i class="${iconClass}"></i>
                </span>
            </div>
            <div class="flex-grow-1">
                <div class="stretched-link">
                    <h6 class="mt-0 fs-md mb-2 lh-base">${notification.message}</h6>
                </div>
                <p class="mb-0 fs-xs fw-medium text-uppercase text-muted">
                    <span><i class="ti ti-clock-hour-4"></i> ${timeAgo}</span>
                </p>
            </div>
        </div>
    </div>`;
}

// Pilih ikon berdasarkan tipe notifikasi
function getNotificationIcon(type) {
    switch(type) {
        case 'TASK_CREATED': return 'ti ti-clipboard-plus';
        case 'TASK_UPDATED': return 'ti ti-pencil';
        case 'STATUS_CHANGED': return 'ti ti-arrows-exchange';
        case 'COMMENT_ADDED': return 'ti ti-message-circle-2';
        case 'PRIORITY_CHANGED': return 'ti ti-flag';
        case 'MEMBER_ADDED': return 'ti ti-users';
        case 'PR_CREATED': return 'ti ti-file-invoice';
        default: return 'ti ti-bell';
    }
}

// Format waktu yang lalu
function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    if (seconds < 60) return 'baru saja';
    
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes} menit yang lalu`;
    
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours} jam yang lalu`;
    
    const days = Math.floor(hours / 24);
    if (days < 7) return `${days} hari yang lalu`;
    
    return date.toLocaleDateString('id-ID');
}

// Update badge jumlah notifikasi
function updateNotificationBadge(count) {
    $('.notification-badge').text(count);
    $('.notification-unread').text(count);
    
    if (count === 0) {
        $('.notification-badge').addClass('d-none');
    } else {
        $('.notification-badge').removeClass('d-none');
    }
}

// Tandai notifikasi sebagai sudah dibaca
function markNotificationAsRead(notificationId) {
    $.ajax({
        url: '/notifications/mark-as-read/' + notificationId,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                // Perbarui tampilan notifikasi dan badge
                loadNotifications();
            } else {
                console.error('Gagal menandai notifikasi sebagai dibaca:', response);
            }
        },
        error: function(xhr) {
            console.error('Error marking notification as read:', xhr);
        }
    });
}

// Handler klik notifikasi untuk menampilkan modal
$(document).on('click', '.notification-item-clickable', function() {
    var notificationId = $(this).data('notification-id');
    var taskId = $(this).data('task-id');
    var message = $(this).data('message');
    
    // Tandai notifikasi sebagai dibaca
    markNotificationAsRead(notificationId);
    
    // Isi konten modal
    $('.notification-detail-content').html(`
        <div class="mb-3">
            <p class="mb-2 fs-md">${message}</p>
            <div class="d-flex align-items-center">
                <span class="badge bg-info me-2">Task #${taskId}</span>
                <small class="text-muted">${formatTimeAgo(new Date())}</small>
            </div>
        </div>
        
        <div class="task-preview-container mt-3">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat detail task...</p>
            </div>
        </div>
    `);
    
    // Setel URL untuk tombol View Task
    $('.view-task-btn').attr('data-task-id', taskId);
    
    // Tutup dropdown notifikasi
    $('#page-header-notifications-dropdown').dropdown('hide');
    
    // Tampilkan modal
    var notificationModal = new bootstrap.Modal(document.getElementById('notificationDetailModal'));
    notificationModal.show();
    
    // Muat preview task
    loadTaskPreview(taskId);
});

// Handler untuk tombol View Task
$(document).on('click', '.view-task-btn', function() {
    var taskId = $(this).attr('data-task-id');
    window.location.href = '/maintenance/tasks/' + taskId;
});

// Fungsi untuk memuat preview task
function loadTaskPreview(taskId) {
    $.ajax({
        url: '/maintenance/tasks/' + taskId + '/preview',
        type: 'GET',
        success: function(response) {
            console.log('Task preview response:', response); // Tambahkan log untuk debugging
            
            // Render preview task
            if (response.success) {
                var task = response.task;
                var priorityName = task.priority_name || 'Unknown Priority';
                var priorityClass = getPriorityClass(priorityName);
                var statusClass = getStatusClass(task.status);
                var labelHtml = '';
                
                // Tambahkan label jika ada
                if (task.label_name) {
                    var labelStyle = task.label_color ? 
                        `background-color: ${task.label_color}; color: white;` : 
                        'background-color: #6c757d; color: white;';
                    
                    labelHtml = `
                    <div class="mb-2">
                        <span class="badge" style="${labelStyle}">${task.label_name}</span>
                    </div>`;
                }
                
                $('.task-preview-container').html(`
                    <div class="card border">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">${task.title}</h6>
                            <span class="badge ${statusClass}">${task.status}</span>
                        </div>
                        <div class="card-body">
                            <div class="mb-2 d-flex justify-content-between">
                                <span class="badge ${priorityClass}">${priorityName}</span>
                                <small class="text-muted">Due: ${formatDate(task.due_date)}</small>
                            </div>
                            ${labelHtml}
                            <p class="card-text">${task.description.substring(0, 150)}${task.description.length > 150 ? '...' : ''}</p>
                            <div class="d-flex justify-content-between mt-3">
                                <small class="text-muted">Dibuat oleh: ${task.creator_name || 'User'}</small>
                                <div>
                                    <small class="text-muted me-2"><i class="ti ti-message-circle"></i> ${task.comment_count}</small>
                                    <small class="text-muted"><i class="ti ti-photo"></i> ${task.media_count}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            } else {
                $('.task-preview-container').html(`
                    <div class="alert alert-warning">
                        Gagal memuat detail task
                    </div>
                `);
            }
        },
        error: function(xhr) {
            console.error('Error loading task preview:', xhr);
            $('.task-preview-container').html(`
                <div class="alert alert-danger">
                    Error: ${xhr.status} ${xhr.statusText}
                </div>
            `);
        }
    });
}

// Update function getPriorityClass untuk menggunakan priorityName
function getPriorityClass(priorityName) {
    if (!priorityName) return 'bg-secondary-subtle text-secondary';
    
    priorityName = priorityName.toUpperCase();
    
    if (priorityName.includes('URGENT') || priorityName.includes('HIGH')) {
        return 'bg-danger-subtle text-danger';
    } else if (priorityName.includes('IMPORTANT') || priorityName.includes('MEDIUM')) {
        return 'bg-warning-subtle text-warning';
    } else if (priorityName.includes('NORMAL')) {
        return 'bg-info-subtle text-info';
    } else if (priorityName.includes('LOW')) {
        return 'bg-success-subtle text-success';
    } else {
        return 'bg-secondary-subtle text-secondary';
    }
}

// Fungsi untuk mendapatkan kelas warna berdasarkan status
function getStatusClass(status) {
    switch(status) {
        case 'TASK':
            return 'bg-secondary text-white';
        case 'PR':
        case 'PO':
            return 'bg-primary text-white';
        case 'IN_PROGRESS':
            return 'bg-warning text-dark';
        case 'IN_REVIEW':
            return 'bg-info text-white';
        case 'DONE':
            return 'bg-success text-white';
        default:
            return 'bg-secondary text-white';
    }
}

// Format tanggal
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}

// Tandai semua notifikasi sebagai sudah dibaca
$(document).on('click', '.mark-all-read', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '/notifications/mark-all-read',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                loadNotifications();
            }
        }
    });
});

// Muat notifikasi saat dokumen siap
$(document).ready(function() {
    console.log("Dokumen siap, memuat notifikasi...");
    loadNotifications();
    
    // Auto refresh notifikasi setiap 1 menit
    setInterval(loadNotifications, 60000);
});
</script>

<!-- Modal untuk detail notifikasi -->
<div class="modal fade" id="notificationDetailModal" tabindex="-1" role="dialog" aria-labelledby="notificationDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="notificationDetailModalLabel">Detail Notifikasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="notification-detail-content">
          <!-- Isi notifikasi akan dimuat di sini -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<style>
    .notification-item-clickable {
        cursor: pointer;
    }
    
    .notification-item-clickable:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }
    
    .task-preview-container {
        max-height: 300px;
        overflow-y: auto;
    }
    
    /* Animasi saat notifikasi dibaca */
    .notification-item.unread-message.read-transition {
        animation: fadeToRead 0.5s ease-in-out forwards;
    }
    
    @keyframes fadeToRead {
        from {
            background-color: rgba(13, 110, 253, 0.08);
        }
        to {
            background-color: transparent;
        }
    }
</style>