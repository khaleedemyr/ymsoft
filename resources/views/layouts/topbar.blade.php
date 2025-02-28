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
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-light rounded-circle user-name-text" id="page-header-notifications-dropdown" data-bs-toggle="dropdown"  data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <i class='ti ti-bell-ringing fs-3xl'></i>
                        <span class="position-absolute topbar-badge fs-3xs translate-middle badge rounded-pill bg-danger"><span class="notification-badge">4</span><span class="visually-hidden">unread messages</span></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">

                        <div class="dropdown-head rounded-top">
                            <div class="p-3 border-bottom border-bottom-dashed">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-0 fs-lg fw-semibold"> Notifications <span class="badge bg-danger-subtle text-danger fs-sm notification-badge"> 4</span></h6>
                                        <p class="fs-md text-muted mt-1 mb-0">You have <span class="fw-semibold notification-unread">3</span> unread messages</p>
                                    </div>
                                    <div class="col-auto dropdown">
                                        <a href="javascript:void(0);" data-bs-toggle="dropdown" class="link-secondary fs-md"><i class="bi bi-three-dots-vertical"></i></a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">All Clear</a></li>
                                            <li><a class="dropdown-item" href="#">Mark all as read</a></li>
                                            <li><a class="dropdown-item" href="#">Archive All</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="py-2 ps-2" id="notificationItemsTabContent">
                            <div data-simplebar style="max-height: 300px;" class="pe-2">
                                <h6 class="text-overflow text-muted fs-sm my-2 text-uppercase notification-title">New</h6>
                                <div class="text-reset notification-item d-block dropdown-item position-relative unread-message">
                                    <div class="d-flex">
                                        <div class="avatar-xs me-3 flex-shrink-0">
                                            <span class="avatar-title bg-success-subtle text-success rounded-circle fs-lg">
                                                <i class='ti ti-gift'></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <a href="#!" class="stretched-link">
                                                <h6 class="mt-0 fs-md mb-2 lh-base">Your <b>Elite</b> author Graphic
                                                    Optimization <span class="text-secondary">reward</span> is ready!
                                                </h6>
                                            </a>
                                            <p class="mb-0 fs-xs fw-medium text-uppercase text-muted">
                                                <span><i class="ti ti-clock-hour-4"></i> Just 30 sec ago</span>
                                            </p>
                                        </div>
                                        <div class="px-2 fs-base">
                                            <div class="form-check notification-check">
                                                <input class="form-check-input" type="checkbox" value="" id="all-notification-check01">
                                                <label class="form-check-label" for="all-notification-check01"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-reset notification-item d-block dropdown-item position-relative unread-message">
                                    <div class="d-flex">
                                        <div class="position-relative me-3 flex-shrink-0">
                                            <img src="build/images/users/avatar-2.jpg" class="rounded-circle avatar-xs" alt="user-pic">
                                            <span class="active-badge position-absolute start-100 translate-middle p-1 bg-success rounded-circle">
                                                <span class="visually-hidden">New alerts</span>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <a href="#!" class="stretched-link">
                                                <h6 class="mt-0 mb-1 fs-md fw-semibold">Angela Bernier</h6>
                                            </a>
                                            <div class="fs-sm text-muted">
                                                <p class="mb-1">Answered to your comment on the cash flow forecast's graph 🔔.</p>
                                            </div>
                                            <p class="mb-0 fs-xs fw-medium text-uppercase text-muted">
                                                <span><i class="ti ti-clock-hour-4"></i> 48 min ago</span>
                                            </p>
                                        </div>
                                        <div class="px-2 fs-base">
                                            <div class="form-check notification-check">
                                                <input class="form-check-input" type="checkbox" value="" id="all-notification-check02">
                                                <label class="form-check-label" for="all-notification-check02"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-reset notification-item d-block dropdown-item position-relative unread-message">
                                    <div class="d-flex">
                                        <div class="avatar-xs me-3 flex-shrink-0">
                                            <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-lg">
                                                <i class='ti ti-message-2'></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <a href="#!" class="stretched-link">
                                                <h6 class="mt-0 mb-2 fs-md lh-base">You have received <b class="text-success">20</b> new messages in the conversation
                                                </h6>
                                            </a>
                                            <p class="mb-0 fs-xs fw-medium text-uppercase text-muted">
                                                <span><i class="ti ti-clock-hour-4"></i> 2 hrs ago</span>
                                            </p>
                                        </div>
                                        <div class="px-2 fs-base">
                                            <div class="form-check notification-check">
                                                <input class="form-check-input" type="checkbox" value="" id="all-notification-check03">
                                                <label class="form-check-label" for="all-notification-check03"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="text-overflow text-muted fs-sm my-2 text-uppercase notification-title">Read Before</h6>

                                <div class="text-reset notification-item d-block dropdown-item position-relative">
                                    <div class="d-flex">

                                        <div class="position-relative me-3 flex-shrink-0">
                                            <img src="build/images/users/avatar-8.jpg" class="rounded-circle avatar-xs" alt="user-pic">
                                            <span class="active-badge position-absolute start-100 translate-middle p-1 bg-warning rounded-circle">
                                                <span class="visually-hidden">New alerts</span>
                                            </span>
                                        </div>

                                        <div class="flex-grow-1">
                                            <a href="#!" class="stretched-link">
                                                <h6 class="mt-0 mb-1 fs-md fw-semibold">Maureen Gibson</h6>
                                            </a>
                                            <div class="fs-sm text-muted">
                                                <p class="mb-1">We talked about a project on linkedin.</p>
                                            </div>
                                            <p class="mb-0 fs-xs fw-medium text-uppercase text-muted">
                                                <span><i class="ti ti-clock-hour-4"></i> 4 hrs ago</span>
                                            </p>
                                        </div>
                                        <div class="px-2 fs-base">
                                            <div class="form-check notification-check">
                                                <input class="form-check-input" type="checkbox" value="" id="all-notification-check04">
                                                <label class="form-check-label" for="all-notification-check04"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="notification-actions" id="notification-actions">
                                <div class="d-flex text-muted justify-content-center align-items-center">
                                    Select <div id="select-content" class="text-body fw-semibold px-1">0</div> Result <button type="button" class="btn btn-link link-danger p-0 ms-2" data-bs-toggle="modal" data-bs-target="#removeNotificationModal">Remove</button>
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