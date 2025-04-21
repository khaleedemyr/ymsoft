@extends('layouts.master')

@section('title') @lang('translation.calendar') @endsection

@section('css')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    .fc-event {
        cursor: pointer;
    }
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }
    .initials-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #3498db;
        color: white;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .activity-title {
        font-weight: 700;
        color: #222;
        font-size: 1rem;
        margin-bottom: 0.3rem;
        line-height: 1.4;
    }
    .activity-description {
        margin-bottom: 0.25rem;
        color: #555;
        font-size: 0.85rem;
    }
    .activity-time {
        font-size: 0.75rem;
        color: #777;
    }
    .acitivity-item {
        padding: 0.75rem 0.5rem;
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 0.5rem;
    }
    .acitivity-item:last-child {
        border-bottom: none;
    }
    .activity-header {
        color: #1a73e8;
        font-size: 1.1rem;
        font-weight: 600;
        padding-bottom: 5px;
        border-bottom: 2px solid #e1e1e1;
        margin-bottom: 10px;
        position: sticky;
        top: 0;
        background-color: #fff;
        z-index: 5;
        padding-top: 10px;
    }
    .content-scrollable {
        max-height: 350px;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 5px;
        margin-top: 10px;
        width: 100%;
    }
    .section-title-wrapper {
        margin-bottom: 5px;
        background-color: #fff;
        position: sticky;
        top: 0;
        z-index: 10;
        padding-bottom: 5px;
        width: 100%;
    }
</style>
@endsection

@section('content')

<div class="calendar-wrapper d-lg-flex gap-5">

    <div class="card mb-4 mt-2 calendar-event-card">
        <div class="card-body">
            <button type="button" class="btn btn-primary w-100 mb-4" id="btn-new-event"><i class="ti ti-plus me-1"></i> Buat Event Baru</button>
            <div class="mb-4" style="display: none;">
                <div id="external-events">
                    <p class="text-muted">Drag and drop event atau klik di kalender</p>
                    <div class="vstack gap-2">
                        <div class="external-event fc-event bg-success-subtle text-success text-center" data-class="bg-success-subtle">
                            <i class="ti ti-circle-dot-filled font-size-11 text-success me-2"></i>Event Baru
                        </div>
                        <div class="external-event fc-event bg-info-subtle text-info text-center" data-class="bg-info-subtle">
                            <i class="ti ti-circle-dot-filled font-size-11 text-info me-2"></i>Meeting
                        </div>
                        <div class="external-event fc-event bg-warning-subtle text-warning text-center" data-class="bg-warning-subtle">
                            <i class="ti ti-circle-dot-filled font-size-11 text-warning me-2"></i>Laporan
                        </div>
                        <div class="external-event fc-event bg-danger-subtle text-danger text-center" data-class="bg-danger-subtle">
                            <i class="ti ti-circle-dot-filled font-size-11 text-danger me-2"></i>Maintenance
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="section-title-wrapper">
                    <h5 class="mb-2 fs-lg activity-header">Event Mendatang</h5>
                    <div class="d-flex justify-content-between">
                        <p class="text-muted mb-2">Jangan lewatkan event yang sudah dijadwalkan</p>
                        <button type="button" class="btn btn-sm btn-soft-primary mb-2" id="reload-upcoming">
                            <i class="mdi mdi-refresh"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="content-scrollable" data-simplebar id="upcoming-events">
                    <!-- Event mendatang akan dimuat di sini -->
                    <div class="d-flex justify-content-center p-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="section-title-wrapper">
                    <h5 class="mb-2 fs-lg activity-header">Aktivitas Terbaru</h5>
                    <div class="d-flex justify-content-between">
                        <p class="text-muted mb-2">Aktivitas terakhir pada kalender</p>
                        <button type="button" class="btn btn-sm btn-soft-primary mb-2" id="reload-activities">
                            <i class="mdi mdi-refresh"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="content-scrollable" data-simplebar id="recent-activities">
                    <!-- Aktivitas terbaru akan dimuat di sini -->
                    <div class="d-flex justify-content-center p-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-100">
        <div class="alert alert-info alert-dismissible fade show mt-2" role="alert">
            <strong>Info!</strong> Anda dapat melihat dan mengelola event di kalender ini.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <div class="card card-h-100">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <select class="form-select" id="outletFilter">
                            <option value="">Semua Outlet</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="statusFilter">
                            <option value="">Semua Status</option>
                            <option value="Tentative">Tentative</option>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Definitive">Definitive</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary" id="loadDataBtn">
                            <i class="fas fa-sync"></i> Load Data
                        </button>
                    </div>
                </div>
                <div id="calendar"></div>
            </div>
        </div>

        <!-- Tabel Daftar Event -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Event</h5>
                <div>
                    <button type="button" class="btn btn-sm btn-success" id="exportExcel">
                        <i class="ri-file-excel-line"></i> Export Excel
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" id="exportPdf">
                        <i class="ri-file-pdf-line"></i> Export PDF
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="eventTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th class="text-center">Company Name</th>
                                <th class="text-center">Segment</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">PIC</th>
                                <th class="text-center">PIC Positions</th>
                                <th class="text-center">Pax</th>
                                <th class="text-center">Event Type</th>
                                <th class="text-center">Outlet</th>
                                <th class="text-center">Estimation Revenue</th>
                            </tr>
                        </thead>
                        <tbody id="event-table-body">
                            <!-- Data akan dimuat di sini -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="7" class="text-end">Total</th>
                                <th class="text-center" id="total-pax">0</th>
                                <th></th>
                                <th class="text-center">Total</th>
                                <th class="text-end" id="total-revenue">Rp 0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Event -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header p-3 bg-info-subtle">
                <h5 class="modal-title" id="modal-title">Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body p-4">
                <form class="needs-validation" name="event-form" id="eventForm" novalidate>
                    <div class="text-end">
                        <a href="#" class="btn btn-sm btn-subtle-primary" id="edit-event-btn" data-id="edit-event" role="button">Edit</a>
                    </div>
                    <div class="event-details">
                        <div class="d-flex mb-2">
                            <div class="flex-grow-1 d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <i class="ri-calendar-event-line text-muted fs-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="d-block fw-semibold mb-0" id="event-start-date-tag"></h6>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0 me-3">
                                <i class="ri-discuss-line text-muted fs-lg"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="d-block text-muted mb-0" id="event-title-tag"></p>
                                <p class="d-block text-muted mb-0" id="event-company-tag"></p>
                                <p class="d-block text-muted mb-0" id="event-segment-tag"></p>
                                <p class="d-block text-muted mb-0" id="event-status-tag"></p>
                                <p class="d-block text-muted mb-0" id="event-pic-tag"></p>
                                <p class="d-block text-muted mb-0" id="event-outlet-tag"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row event-form">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Outlet</label>
                                <select class="form-select" name="outlet_id" id="event-outlet" required>
                                    <option value="">Pilih Outlet</option>
                                    @foreach($outlets as $outlet)
                                        <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Mohon pilih outlet</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Area</label>
                                <input type="text" class="form-control" name="area" id="event-area" placeholder="Area">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Event</label>
                                <input class="form-control" placeholder="Masukkan nama event" type="text" name="title" id="event-title" required value="">
                                <div class="invalid-feedback">Mohon masukkan nama event yang valid</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Perusahaan</label>
                                <input class="form-control" placeholder="Masukkan nama perusahaan" type="text" name="company_name" id="event-company" required>
                                <div class="invalid-feedback">Mohon masukkan nama perusahaan</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Segment</label>
                                <select class="form-select" name="segment" id="event-segment" required>
                                    <option value="">Pilih Segment</option>
                                    <option value="Corporate">Corporate</option>
                                    <option value="Government">Government</option>
                                    <option value="Individual">Individual</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="invalid-feedback">Mohon pilih segment</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Tanggal & Waktu Event</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <input type="text" id="event-start-date" class="form-control flatpickr flatpickr-input" placeholder="Pilih tanggal" readonly required>
                                            <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <input id="timepicker1" type="text" class="form-control flatpickr flatpickr-input" placeholder="Waktu mulai" readonly required>
                                            <span class="input-group-text"><i class="ri-time-line"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <input id="timepicker2" type="text" class="form-control flatpickr flatpickr-input" placeholder="Waktu selesai" readonly required>
                                            <span class="input-group-text"><i class="ri-time-line"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" id="event-status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Tentative">Tentative</option>
                                    <option value="Confirmed">Confirmed</option>
                                    <option value="Definitive">Definitive</option>
                                </select>
                                <div class="invalid-feedback">Mohon pilih status</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">PIC</label>
                                <input type="text" class="form-control" name="pic_name" id="event-pic" placeholder="Nama PIC" required>
                                <div class="invalid-feedback">Mohon masukkan nama PIC</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">PIC Position</label>
                                <input type="text" class="form-control" name="pic_position" id="event-pic-position" placeholder="Posisi PIC" required>
                                <div class="invalid-feedback">Mohon masukkan posisi PIC</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="pic_phone" id="event-phone" placeholder="Nomor Telepon" required>
                                <div class="invalid-feedback">Mohon masukkan nomor telepon</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pax</label>
                                <input type="number" class="form-control" name="pax" id="event-pax" placeholder="Jumlah Pax" required>
                                <div class="invalid-feedback">Mohon masukkan jumlah pax</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Event Type</label>
                                <select class="form-select" name="event_type" id="event-type" required>
                                    <option value="">Pilih Tipe Event</option>
                                    <option value="Meeting">Meeting</option>
                                    <option value="Conference">Conference</option>
                                    <option value="Workshop">Workshop</option>
                                    <option value="Seminar">Seminar</option>
                                    <option value="Wedding">Wedding</option>
                                    <option value="Birthday">Birthday</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="invalid-feedback">Mohon pilih tipe event</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estimation Revenue</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" name="estimation_revenue" id="event-revenue" placeholder="Estimasi Revenue" required>
                                </div>
                                <div class="invalid-feedback">Mohon masukkan estimasi revenue</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" id="event-description" name="description" placeholder="Masukkan keterangan tambahan" rows="3" spellcheck="false"></textarea>
                            </div>
                        </div>
                        <input type="hidden" id="event_id" name="event_id">
                    </div>
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" id="btn-delete-event"><i class="ri-delete-bin-line align-bottom"></i> Hapus</button>
                        <button type="submit" class="btn btn-success" id="btn-save-event">Simpan Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ URL::asset('build/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/fullcalendar/index.global.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    
    // Tambahkan event listener untuk menangani penutupan modal
    var eventModalEl = document.getElementById('eventModal');
    if (eventModalEl) {
        eventModalEl.addEventListener('hidden.bs.modal', function () {
            console.log('Modal hidden event fired');
            // Hapus backdrop yang mungkin tersisa
            var backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            
            // Bersihkan class pada body
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    }
    
    // Format currency input
    $('#event-revenue').on('input', function() {
        // Remove non-digit characters
        var value = $(this).val().replace(/\D/g, '');
        // Format with thousand separator
        $(this).val(new Intl.NumberFormat('id-ID').format(value));
    });
    
    // Inisialisasi flatpickr untuk datepicker
    flatpickr("#event-start-date", {
        dateFormat: "Y-m-d",
        allowInput: true
    });

    flatpickr("#timepicker1, #timepicker2", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });
    
    // Event handler untuk tombol "Buat Event Baru" - FIXED
    document.getElementById('btn-new-event').addEventListener('click', function() {
        console.log('Tombol Buat Event Baru diklik');
        
        // Reset form
        document.getElementById('eventForm').reset();
        
        // Set nilai default
        document.getElementById('event_id').value = '';
        document.getElementById('modal-title').textContent = 'Tambah Event';
        document.getElementById('btn-save-event').textContent = 'Simpan Event';
        document.getElementById('btn-delete-event').style.display = 'none';
        
        // Set tanggal default
        var today = new Date();
        var formattedDate = today.toISOString().split('T')[0];
        document.getElementById('event-start-date').value = formattedDate;
        document.getElementById('timepicker1').value = '09:00';
        document.getElementById('timepicker2').value = '17:00';
        
        // Tampilkan modal
        var myModal = new bootstrap.Modal(document.getElementById('eventModal'));
        myModal.show();
    });
    
    // Event handler untuk tombol close modal
    $(document).on('click', '[data-bs-dismiss="modal"]', function() {
        $('#eventModal').modal('hide');
        
        // Secara manual membersihkan backdrop dan modal-open
        setTimeout(function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('overflow', '');
            $('body').css('padding-right', '');
        }, 200);
    });
    
    // Event handler untuk tombol Edit
    $('#edit-event-btn').on('click', function(e) {
        e.preventDefault();
        console.log('Edit button clicked');
        $('.event-details').hide();
        $('.event-form').show();
    });
    
    // Inisialisasi Calendar
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'id',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(info, successCallback, failureCallback) {
            // Tampilkan loading overlay
            var calendarContainer = document.getElementById('calendar');
            var loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'loading-overlay';
            loadingOverlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
            calendarContainer.appendChild(loadingOverlay);
            
            // Ambil nilai filter
            var outletId = document.getElementById('outletFilter').value || '';
            var status = document.getElementById('statusFilter').value || '';
            
            $.ajax({
                url: '{{ route("calendar.events") }}',
                type: 'GET',
                data: {
                    start: info.startStr,
                    end: info.endStr,
                    outlet_id: outletId,
                    status: status
                },
                success: function(response) {
                    // Hapus loading overlay
                    calendarContainer.removeChild(loadingOverlay);
                    
                    console.log('Calendar events loaded:', response.length, 'events');
                    successCallback(response);
                    
                    // Update data lain
                    updateUpcomingEvents();
                    updateRecentActivities();
                },
                error: function(xhr) {
                    // Hapus loading overlay
                    calendarContainer.removeChild(loadingOverlay);
                    
                    console.error('Error loading events:', xhr);
                    failureCallback('Terjadi kesalahan saat memuat data');
                    
                    // Tampilkan pesan error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat memuat data dari server.'
                    });
                }
            });
        },
        // Menonaktifkan fitur drag & drop dan resize
        editable: false,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        select: function(arg) {
            // Reset form
            $('#eventForm')[0].reset();
            
            // Set nilai default
            $('#event_id').val('');
            $('#modal-title').text('Tambah Event');
            $('#btn-save-event').text('Simpan Event');
            $('#btn-delete-event').hide();
            
            // Set tanggal dari kalender
            $('#event-start-date').val(arg.startStr);
            $('#timepicker1').val('09:00');
            $('#timepicker2').val('17:00');
            
            // Tampilkan modal dengan jQuery
            $('#eventModal').modal('show');
        },
        eventClick: function(info) {
            // Reset form
            $('#eventForm')[0].reset();
            
            console.log('Event clicked:', info.event);
            
            // Set nilai dari event yang dipilih
            $('#event_id').val(info.event.id);
            $('#event-title').val(info.event.title);
            $('#event-company').val(info.event.extendedProps.company_name);
            $('#event-segment').val(info.event.extendedProps.segment);
            $('#event-area').val(info.event.extendedProps.area);
            $('#event-start-date').val(info.event.startStr.split('T')[0]);
            $('#event-status').val(info.event.extendedProps.status);
            $('#event-pic').val(info.event.extendedProps.pic_name);
            $('#event-pic-position').val(info.event.extendedProps.pic_position);
            $('#event-phone').val(info.event.extendedProps.pic_phone);
            $('#event-pax').val(info.event.extendedProps.pax);
            $('#event-type').val(info.event.extendedProps.event_type);
            $('#event-outlet').val(info.event.extendedProps.outlet_id);
            $('#event-revenue').val(new Intl.NumberFormat('id-ID').format(info.event.extendedProps.estimation_revenue));
            $('#event-description').val(info.event.extendedProps.description);
            
            $('#modal-title').text('Edit Event');
            $('#btn-save-event').text('Update Event');
            $('#btn-delete-event').show();
            
            // Tampilkan modal dengan jQuery
            $('#eventModal').modal('show');
        }
    });
    
    calendar.render();

    // Load data saat tombol Load Data diklik
    document.getElementById('loadDataBtn').addEventListener('click', function() {
        console.log('Load Data button clicked');
        calendar.refetchEvents();
    });

    // Load data saat filter berubah
    document.getElementById('outletFilter').addEventListener('change', function() {
        console.log('Outlet filter changed:', this.value);
        // Tidak langsung me-refresh, biarkan user klik tombol Load Data
    });
    
    document.getElementById('statusFilter').addEventListener('change', function() {
        console.log('Status filter changed:', this.value);
        // Tidak langsung me-refresh, biarkan user klik tombol Load Data
    });

    // Simpan event
    document.getElementById('eventForm').addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
        
        // Nonaktifkan tombol submit untuk mencegah double submit
        document.getElementById('btn-save-event').disabled = true;
        console.log('Submit button disabled');
        
        // Format nilai revenue (hapus separator ribuan)
        var revenue = document.getElementById('event-revenue').value.replace(/\./g, '');
        
        // Buat FormData dari form
        var form = document.getElementById('eventForm');
        var formData = new FormData(form);
        
        // Update nilai revenue yang sudah diformat
        formData.delete('estimation_revenue');
        formData.append('estimation_revenue', revenue);
        
        // Tambahkan tanggal dan waktu
        formData.append('start_date', document.getElementById('event-start-date').value + ' ' + document.getElementById('timepicker1').value);
        formData.append('end_date', document.getElementById('event-start-date').value + ' ' + document.getElementById('timepicker2').value);
        
        // Tambahkan CSRF token
        formData.append('_token', '{{ csrf_token() }}');
        
        var eventId = document.getElementById('event_id').value;
        console.log('Event ID:', eventId);
        
        // URL dan metode HTTP berdasarkan event_id
        var url = eventId 
            ? '{{ url("calendar") }}/' + eventId 
            : '{{ url("calendar") }}';
        var method = eventId ? 'PUT' : 'POST';
        console.log('Request URL:', url);
        console.log('Request Method:', method);
        
        // Jika method adalah PUT, tambahkan _method field untuk Laravel
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }
        
        // Tampilkan loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Log formData values for debugging
        console.log('Form data being sent:');
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Kirim data dengan fetch API
        fetch(url, {
            method: method === 'PUT' ? 'POST' : method, // Untuk PUT, gunakan POST dengan _method=PUT
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            // Tutup loading
            Swal.close();
            console.log('Server response:', data);
            
            // Aktifkan tombol kembali
            document.getElementById('btn-save-event').disabled = false;
            
            if (data.success) {
                // Tutup modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('eventModal'));
                modal.hide();
                
                // Refresh kalender
                calendar.refetchEvents();
                
                // Perbarui data aktivitas dan event mendatang
                updateUpcomingEvents();
                updateRecentActivities();
                
                // Tampilkan pesan sukses
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    timer: 1500
                });
            } else {
                // Tampilkan pesan error
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Terjadi kesalahan saat menyimpan data'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Tutup loading
            Swal.close();
            
            // Aktifkan tombol kembali
            document.getElementById('btn-save-event').disabled = false;
            
            // Tampilkan pesan error
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menyimpan data'
            });
        });
    });

    // Hapus event
    document.getElementById('btn-delete-event').addEventListener('click', function() {
        var eventId = document.getElementById('event_id').value;
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Event yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Kirim request delete dengan fetch API
                fetch('{{ url("calendar") }}/' + eventId, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Tutup loading
                    Swal.close();
                    
                    if (data.success) {
                        // Tutup modal
                        var modal = bootstrap.Modal.getInstance(document.getElementById('eventModal'));
                        modal.hide();
                        
                        // Refresh kalender
                        calendar.refetchEvents();
                        
                        // Tampilkan pesan sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            timer: 1500
                        });
                    } else {
                        // Tampilkan pesan error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Terjadi kesalahan saat menghapus data'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Tutup loading
                    Swal.close();
                    
                    // Tampilkan pesan error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menghapus data'
                    });
                });
            }
        });
    });

    // Fungsi untuk memperbarui event mendatang
    function updateUpcomingEvents() {
        // Tampilkan loading indicator
        document.getElementById('upcoming-events').innerHTML = '<div class="d-flex justify-content-center p-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        
        console.log('Fetching upcoming events from:', '{{ route("calendar.upcoming") }}');
        
        fetch('{{ route("calendar.upcoming") }}')
        .then(response => {
            console.log('Upcoming events response status:', response.status);
            if (!response.ok) {
                throw new Error('Response not OK: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Upcoming events loaded:', data);
            
            let html = '';
            
            if (data.length > 0) {
                data.forEach(function(event) {
                    html += `
                    <div class="card mb-3 border-0 border-bottom w-100">
                        <div class="card-body p-1 pb-3">
                            <div class="row align-items-center">
                                <div class="col-2">
                                    <div class="fw-bolder"><span class="fw-medium">${event.start_date}</span></div>
                                </div>
                                <div class="col-10 border-start border-4 ${event.category} ps-3 rounded">
                                    <h6 class="card-title fs-md">${event.title}</h6>
                                    <p class="text-muted text-truncate mb-0">${event.company_name || ''} - ${event.segment || ''}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                });
                
                // Update content
                const upcomingContainer = document.getElementById('upcoming-events');
                upcomingContainer.innerHTML = html;
                
                // Auto scroll jika ada lebih dari 3 items
                if (data.length > 3) {
                    // Pastikan container memiliki proper scrolling
                    upcomingContainer.style.overflowY = 'auto';
                    upcomingContainer.style.overflowX = 'hidden';
                    
                    // Scroll ke bawah untuk menampilkan item terakhir
                    setTimeout(() => {
                        upcomingContainer.scrollTop = upcomingContainer.scrollHeight;
                    }, 500);
                }
            } else {
                html = '<div class="alert alert-info">Tidak ada event yang akan datang.</div>';
                document.getElementById('upcoming-events').innerHTML = html;
            }
        })
        .catch(error => {
            console.error('Error loading upcoming events:', error);
            document.getElementById('upcoming-events').innerHTML = '<div class="alert alert-danger">Gagal memuat data event mendatang.</div>';
            
            // Tampilkan notifikasi yang lebih detail
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat Event Mendatang',
                text: 'Terjadi kesalahan saat memuat event mendatang. Silahkan coba lagi atau hubungi administrator.',
                footer: '<small>Error: ' + error.message + '</small>'
            });
        });
    }

    // Fungsi untuk memperbarui aktivitas terbaru
    function updateRecentActivities() {
        // Tampilkan loading indicator
        document.getElementById('recent-activities').innerHTML = '<div class="d-flex justify-content-center p-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        
        console.log('Fetching recent activities from:', '{{ route("calendar.activities") }}');
        
        fetch('{{ route("calendar.activities") }}')
        .then(response => {
            console.log('Recent activities response status:', response.status);
            if (!response.ok) {
                throw new Error('Response not OK: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Recent activities loaded:', data);
            
            let html = '';
            
            if (data.length > 0) {
                data.forEach(function(activity) {
                    // Buat inisial dari nama
                    let initials = activity.user_name ? getInitials(activity.user_name) : "U";
                    // Gunakan warna yang berbeda berdasarkan nama
                    let bgColor = stringToColor(activity.user_name || "User");
                    
                    html += `
                    <div class="acitivity-item d-flex w-100">
                        <div class="flex-shrink-0">
                            <div class="initials-avatar" style="background-color: ${bgColor}">${initials}</div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="activity-title">${activity.title}</h6>
                            <p class="activity-description text-muted">${activity.description}</p>
                            <small class="activity-time">${activity.created_at}</small>
                        </div>
                    </div>
                    `;
                });
                
                const activitiesContainer = document.getElementById('recent-activities');
                activitiesContainer.innerHTML = html;
                
                // Auto scroll jika ada lebih dari 3 activities
                if (data.length > 3) {
                    // Pastikan container memiliki proper scrolling
                    activitiesContainer.style.overflowY = 'auto';
                    activitiesContainer.style.overflowX = 'hidden';
                    
                    // Scroll ke bawah untuk menampilkan item terakhir
                    setTimeout(() => {
                        activitiesContainer.scrollTop = activitiesContainer.scrollHeight;
                    }, 500);
                }
            } else {
                html = '<div class="alert alert-info">Belum ada aktivitas terbaru.</div>';
                document.getElementById('recent-activities').innerHTML = html;
            }
        })
        .catch(error => {
            console.error('Error loading recent activities:', error);
            document.getElementById('recent-activities').innerHTML = '<div class="alert alert-danger">Gagal memuat data aktivitas terbaru.</div>';
            
            // Tampilkan notifikasi yang lebih detail
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat Aktivitas Terbaru',
                text: 'Terjadi kesalahan saat memuat aktivitas terbaru. Silahkan coba lagi atau hubungi administrator.',
                footer: '<small>Error: ' + error.message + '</small>'
            });
        });
    }
    
    // Fungsi untuk mendapatkan inisial dari nama
    function getInitials(name) {
        if (!name) return "U";
        const words = name.trim().split(' ');
        if (words.length === 1) return words[0].charAt(0).toUpperCase();
        return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase();
    }
    
    // Fungsi untuk menghasilkan warna berdasarkan string
    function stringToColor(str) {
        if (!str) return "#3498db";
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        let color = '#';
        for (let i = 0; i < 3; i++) {
            let value = (hash >> (i * 8)) & 0xFF;
            color += ('00' + value.toString(16)).substr(-2);
        }
        return color;
    }

    // Load data awal
    updateUpcomingEvents();
    updateRecentActivities();
    loadTableData();
    
    // Event listener untuk tombol reload upcoming events
    document.getElementById('reload-upcoming').addEventListener('click', function() {
        updateUpcomingEvents();
    });
    
    // Event listener untuk tombol reload activities
    document.getElementById('reload-activities').addEventListener('click', function() {
        updateRecentActivities();
    });

    // Event listener untuk tombol Load Data, sekaligus refresh tabel
    document.getElementById('loadDataBtn').addEventListener('click', function() {
        calendar.refetchEvents();
        loadTableData();
    });

    // Event listener untuk tombol export Excel
    document.getElementById('exportExcel').addEventListener('click', function() {
        exportTableToExcel();
    });

    // Event listener untuk tombol export PDF
    document.getElementById('exportPdf').addEventListener('click', function() {
        exportTableToPdf();
    });

    // Fungsi untuk memuat data tabel
    function loadTableData() {
        // Tampilkan loading di tabel
        document.getElementById('event-table-body').innerHTML = '<tr><td colspan="11" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
        
        // Ambil nilai filter yang sama dengan kalender
        var outletId = document.getElementById('outletFilter').value || '';
        var status = document.getElementById('statusFilter').value || '';
        
        // Buat query string untuk filter
        var queryParams = new URLSearchParams();
        if (outletId) queryParams.append('outlet_id', outletId);
        if (status) queryParams.append('status', status);
        
        // URL dengan query string
        var url = '{{ route("calendar.events") }}';
        if (queryParams.toString()) {
            url += '?' + queryParams.toString();
        }
        
        // Ambil data event dari server
        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Table data loaded:', data.length, 'events');
            
            let html = '';
            let totalPax = 0;
            let totalRevenue = 0;
            
            if (data.length > 0) {
                // Urutkan data berdasarkan tanggal
                data.sort((a, b) => new Date(a.start) - new Date(b.start));
                
                // Buat baris tabel untuk setiap event
                data.forEach((event, index) => {
                    // Hitung total
                    totalPax += parseInt(event.extendedProps.pax || 0);
                    totalRevenue += parseInt(event.extendedProps.estimation_revenue || 0);
                    
                    // Format tanggal
                    const eventDate = new Date(event.start);
                    const formattedDate = `${eventDate.getDate()}-${eventDate.toLocaleString('default', {month: 'short'})}-${eventDate.getFullYear().toString().substr(-2)}`;
                    
                    // Format revenue dengan Rp dan pemisah ribuan
                    const formattedRevenue = formatRupiah(event.extendedProps.estimation_revenue || 0);
                    
                    html += `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td>${event.extendedProps.company_name || '-'}</td>
                        <td>${event.extendedProps.segment || '-'}</td>
                        <td class="text-center">${formattedDate}</td>
                        <td class="text-center">${event.extendedProps.status || '-'}</td>
                        <td>${event.extendedProps.pic_name || '-'}</td>
                        <td>${event.extendedProps.pic_position || '-'}</td>
                        <td class="text-center">${event.extendedProps.pax || '0'}</td>
                        <td>${event.extendedProps.event_type || '-'}</td>
                        <td>${event.extendedProps.outlet_name || '-'}</td>
                        <td class="text-end">${formattedRevenue}</td>
                    </tr>
                    `;
                });
            } else {
                html = '<tr><td colspan="11" class="text-center">Tidak ada data event</td></tr>';
            }
            
            // Update tabel
            document.getElementById('event-table-body').innerHTML = html;
            document.getElementById('total-pax').textContent = totalPax;
            document.getElementById('total-revenue').textContent = formatRupiah(totalRevenue);
        })
        .catch(error => {
            console.error('Error loading table data:', error);
            document.getElementById('event-table-body').innerHTML = '<tr><td colspan="11" class="text-center text-danger">Gagal memuat data</td></tr>';
        });
    }

    // Fungsi untuk format Rupiah
    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    // Fungsi untuk export tabel ke Excel
    function exportTableToExcel() {
        // Implementasi export Excel bisa ditambahkan di sini
        // Sebagai contoh sederhana, kita gunakan alert
        Swal.fire({
            title: 'Export Excel',
            text: 'Fitur export Excel akan segera tersedia',
            icon: 'info'
        });
    }

    // Fungsi untuk export tabel ke PDF
    function exportTableToPdf() {
        // Implementasi export PDF bisa ditambahkan di sini
        // Sebagai contoh sederhana, kita gunakan alert
        Swal.fire({
            title: 'Export PDF',
            text: 'Fitur export PDF akan segera tersedia',
            icon: 'info'
        });
    }
});
</script>
@endsection 