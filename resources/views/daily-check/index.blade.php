@extends('layouts.master')

@section('title')
    Daily Check Report
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        
        /* Style untuk tampilan mobile */
        @media (max-width: 768px) {
            .table > tbody > tr > td {
                min-width: 50px;
                font-size: 14px;
                padding: 8px 4px;
            }
            
            .condition-group {
                display: flex;
                gap: 10px;
                justify-content: space-between;
                margin-bottom: 10px;
            }

            .condition-item {
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .mobile-row {
                background: #f8f9fa;
                padding: 10px;
                margin-bottom: 10px;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }

            .mobile-label {
                font-weight: bold;
                margin-bottom: 5px;
            }

            .mobile-value {
                margin-bottom: 10px;
            }

            .camera-preview {
                width: 100%;
                max-width: 300px;
                margin: 10px 0;
            }

            .image-preview {
                max-width: 100px;
                height: auto;
                margin: 5px 0;
            }

            .btn-capture {
                padding: 4px 8px;
                font-size: 12px;
            }
        }

        /* Style untuk radio button */
        .condition-radio {
            display: none;
        }

        .condition-label {
            display: inline-block;
            padding: 4px 8px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            margin: 2px;
            font-size: 14px;
        }

        .condition-radio:checked + .condition-label {
            background: #556ee6;
            color: white;
            border-color: #556ee6;
        }

        .photo-preview-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 5px;
        }

        .photo-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .area-header {
            cursor: pointer;
            user-select: none;
            position: relative;
        }

        .area-header:hover {
            background-color: #e9ecef;
        }

        .area-header i {
            transition: transform 0.2s ease;
        }

        .area-header.collapsed i {
            transform: rotate(-90deg);
        }

        .area-content {
            transition: all 0.3s ease-in-out;
        }

        /* Mobile specific styles */
        .mobile-area-header {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            user-select: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mobile-area-header:hover {
            background: #e9ecef;
        }

        .mobile-area-header i {
            transition: transform 0.2s ease;
        }

        .mobile-area-header.collapsed i {
            transform: rotate(-90deg);
        }

        .mobile-area-content {
            transition: all 0.3s ease-in-out;
            padding-left: 15px;
        }
        
        /* Camera modal styles */
        .camera-modal .modal-dialog {
            max-width: 500px;
        }
        
        .camera-container {
            position: relative;
            width: 100%;
            height: 300px;
            background-color: #000;
            overflow: hidden;
            border-radius: 8px;
        }
        
        #camera-stream {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .camera-controls {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        
        .captured-photos {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
            max-height: 100px;
            overflow-y: auto;
        }
        
        .captured-photo {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .captured-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .captured-photo .remove-photo {
            position: absolute;
            top: 2px;
            right: 2px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            color: #dc3545;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Daily Check
        @endslot
        @slot('title')
            Daily Check Report
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title flex-grow-1">RESTAURANT DAILY CHECK REPORT</h4>
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-3">
                                    <span class="fw-bold">{{ auth()->user()->nama_lengkap }}</span>
                                    <span class="text-muted ms-1">({{ auth()->user()->jabatan->nama_jabatan ?? 'Tidak ada jabatan' }})</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold">Outlet:</span>
                                <span class="ms-2">{{ $outlet->nama_outlet }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('daily-check.store') }}" method="POST" id="dailyCheckForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
                        <input type="hidden" name="id_outlet" value="{{ $outlet->id_outlet }}">
                        <input type="hidden" name="status" value="draft">
                        
                        <!-- Autosave Indicator -->
                        <div class="autosave-indicator mb-3 text-end">
                            <div class="d-inline-block" id="autosaveStatus">
                                <span class="badge bg-info">
                                    <i class="ri-save-line me-1"></i> Siap menyimpan otomatis
                                </span>
                            </div>
                        </div>
                        
                        <!-- Desktop View -->
                        <div class="d-none d-md-block">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">NO</th>
                                            <th>AREA</th>
                                            <th colspan="4" class="text-center">CONDITION</th>
                                            <th>OTHER ISSUE</th>
                                            <th>TIME</th>
                                            <th>PHOTO</th>
                                            <th>REMARK</th>
                                        </tr>
                                        <tr>
                                            <th colspan="2"></th>
                                            <th class="text-center">C</th>
                                            <th class="text-center">WM</th>
                                            <th class="text-center">D</th>
                                            <th class="text-center">NA</th>
                                            <th colspan="4"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($areas as $area)
                                            <tr>
                                                <td colspan="11" class="area-header" 
                                                    onclick="toggleArea('area_{{ $area->id }}')"
                                                    data-area-id="{{ $area->id }}">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ri-arrow-down-s-line me-2"></i>
                                                        <span class="fw-bold">{{ $area->name }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tbody id="area_{{ $area->id }}" class="area-content">
                                                @foreach($area->items as $index => $item)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>{{ $item->name }}</td>
                                                        @foreach(['C', 'WM', 'D', 'NA'] as $condition)
                                                            <td class="text-center">
                                                                <input type="radio" 
                                                                       class="condition-radio"
                                                                       id="condition_{{ $item->id }}_{{ $condition }}"
                                                                       name="checks[{{ $item->id }}][condition]" 
                                                                       value="{{ $condition }}" 
                                                                       required>
                                                                <label class="condition-label" for="condition_{{ $item->id }}_{{ $condition }}">
                                                                    {{ $condition }}
                                                                </label>
                                                            </td>
                                                        @endforeach
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm" 
                                                                   name="checks[{{ $item->id }}][other_issue]" value=""
                                                                   placeholder="Tambahkan issue">
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control form-control-sm" 
                                                                   name="checks[{{ $item->id }}][time]" required>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-primary btn-capture"
                                                                    onclick="openCameraModal({{ $item->id }})">
                                                                <i class="ri-camera-line"></i>
                                                            </button>
                                                            <input type="file" id="photo_{{ $item->id }}" 
                                                                   name="checks[{{ $item->id }}][photos][]" 
                                                                   accept="image/*" 
                                                                   class="d-none"
                                                                   multiple>
                                                            <div id="preview_{{ $item->id }}" class="photo-preview-container"></div>
                                                        </td>
                                                        <td>
                                                            <textarea class="form-control form-control-sm" 
                                                                   name="checks[{{ $item->id }}][remark]" 
                                                                   placeholder="Tambahkan catatan"></textarea>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile View -->
                        <div class="d-md-none">
                            @foreach($areas as $area)
                                <div class="mobile-section mb-4">
                                    <div class="mobile-area-header" 
                                         onclick="toggleArea('mobile_area_{{ $area->id }}')"
                                         data-area-id="{{ $area->id }}">
                                        <span class="fw-bold">{{ $area->name }}</span>
                                        <i class="ri-arrow-down-s-line"></i>
                                    </div>
                                    <div id="mobile_area_{{ $area->id }}" class="mobile-area-content">
                                        @foreach($area->items as $index => $item)
                                            <div class="mobile-row">
                                                <div class="mobile-label">{{ $index + 1 }}. {{ $item->name }}</div>
                                                
                                                <div class="condition-group">
                                                    @foreach(['C', 'WM', 'D', 'NA'] as $condition)
                                                        <div class="condition-item">
                                                            <input type="radio" 
                                                                   class="condition-radio"
                                                                   id="mobile_condition_{{ $item->id }}_{{ $condition }}"
                                                                   name="checks[{{ $item->id }}][condition]" 
                                                                   value="{{ $condition }}" 
                                                                   required>
                                                            <label class="condition-label" 
                                                                   for="mobile_condition_{{ $item->id }}_{{ $condition }}">
                                                                {{ $condition }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div class="form-group mt-2">
                                                    <label class="mobile-label">Other Issue</label>
                                                    <input type="text" class="form-control" 
                                                           name="checks[{{ $item->id }}][other_issue]" value=""
                                                           placeholder="Tambahkan issue">
                                                </div>

                                                <div class="form-group mt-2">
                                                    <label class="mobile-label">Time</label>
                                                    <input type="time" class="form-control" 
                                                           name="checks[{{ $item->id }}][time]" required>
                                                </div>

                                                <div class="form-group mt-2">
                                                    <label class="mobile-label">Photo</label>
                                                    <div>
                                                        <button type="button" class="btn btn-primary btn-sm btn-capture"
                                                                onclick="openCameraModal({{ $item->id }})">
                                                            <i class="ri-camera-line"></i> Ambil Foto
                                                        </button>
                                                        <input type="file" id="mobile_photo_{{ $item->id }}" 
                                                               name="checks[{{ $item->id }}][photos][]" 
                                                               accept="image/*" 
                                                               class="d-none"
                                                               multiple>
                                                        <div id="mobile_preview_{{ $item->id }}" class="photo-preview-container mt-2"></div>
                                                    </div>
                                                </div>

                                                <div class="form-group mt-2">
                                                    <label class="mobile-label">Remark</label>
                                                    <textarea class="form-control" 
                                                           name="checks[{{ $item->id }}][remark]" 
                                                           placeholder="Tambahkan catatan"></textarea>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary" id="submitBtn">Simpan Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <!-- Camera Modal -->
    <div class="modal fade camera-modal" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cameraModalLabel">Ambil Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="camera-container">
                        <video id="camera-stream" autoplay playsinline></video>
                        <div class="camera-controls">
                            <button type="button" class="btn btn-primary" id="capture-btn">
                                <i class="ri-camera-line"></i> Ambil Foto
                            </button>
                        </div>
                    </div>
                    <div class="captured-photos" id="captured-photos"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="save-photos-btn">Simpan Foto</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Variabel global untuk menyimpan stream kamera dan item ID yang aktif
        let cameraStream = null;
        let activeItemId = null;
        let capturedPhotos = [];
        let autosaveTimeout;
        let autosaveInProgress = false;
        
        // Fungsi untuk membuka modal kamera
        function openCameraModal(itemId) {
            activeItemId = itemId;
            capturedPhotos = [];
            document.getElementById('captured-photos').innerHTML = '';
            
            // Tampilkan modal
            const cameraModal = new bootstrap.Modal(document.getElementById('cameraModal'));
            cameraModal.show();
            
            // Mulai stream kamera
            startCamera();
        }
        
        // Fungsi untuk memulai stream kamera
        async function startCamera() {
            try {
                // Hentikan stream yang ada jika ada
                if (cameraStream) {
                    cameraStream.getTracks().forEach(track => track.stop());
                }
                
                // Minta akses kamera
                cameraStream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'environment',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    } 
                });
                
                // Tampilkan stream di video element
                const videoElement = document.getElementById('camera-stream');
                videoElement.srcObject = cameraStream;
                
                // Event listener untuk tombol capture
                document.getElementById('capture-btn').addEventListener('click', capturePhoto);
                
                // Event listener untuk tombol simpan
                document.getElementById('save-photos-btn').addEventListener('click', savePhotos);
                
                // Event listener untuk menutup modal
                document.getElementById('cameraModal').addEventListener('hidden.bs.modal', function () {
                    if (cameraStream) {
                        cameraStream.getTracks().forEach(track => track.stop());
                    }
                });
                
            } catch (error) {
                console.error('Error accessing camera:', error);
                alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin kamera.');
            }
        }
        
        // Fungsi untuk mengambil foto
        function capturePhoto() {
            const videoElement = document.getElementById('camera-stream');
            const canvas = document.createElement('canvas');
            canvas.width = videoElement.videoWidth;
            canvas.height = videoElement.videoHeight;
            
            // Gambar frame video ke canvas
            const context = canvas.getContext('2d');
            context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
            
            // Konversi canvas ke data URL
            const photoDataUrl = canvas.toDataURL('image/jpeg');
            
            // Tambahkan foto ke array
            capturedPhotos.push(photoDataUrl);
            
            // Tampilkan preview foto
            displayCapturedPhoto(photoDataUrl);
        }
        
        // Fungsi untuk menampilkan foto yang diambil
        function displayCapturedPhoto(photoDataUrl) {
            const capturedPhotosContainer = document.getElementById('captured-photos');
            
            const photoDiv = document.createElement('div');
            photoDiv.className = 'captured-photo';
            photoDiv.innerHTML = `
                <img src="${photoDataUrl}" alt="Captured photo">
                <div class="remove-photo" onclick="removeCapturedPhoto(this)">×</div>
            `;
            
            capturedPhotosContainer.appendChild(photoDiv);
        }
        
        // Fungsi untuk menghapus foto yang diambil
        function removeCapturedPhoto(element) {
            const photoDiv = element.parentElement;
            const photoIndex = Array.from(photoDiv.parentElement.children).indexOf(photoDiv);
            
            // Hapus dari array
            capturedPhotos.splice(photoIndex, 1);
            
            // Hapus dari DOM
            photoDiv.remove();
        }
        
        // Fungsi untuk menyimpan foto
        function savePhotos() {
            if (capturedPhotos.length === 0) {
                alert('Tidak ada foto yang diambil');
                return;
            }
            
            const isMobile = window.innerWidth < 768;
            const inputId = isMobile ? `mobile_photo_${activeItemId}` : `photo_${activeItemId}`;
            const previewId = isMobile ? `mobile_preview_${activeItemId}` : `preview_${activeItemId}`;
            
            // Buat File objects dari data URL
            const files = capturedPhotos.map((dataUrl, index) => {
                // Konversi data URL ke Blob
                const res = dataUrl.split(';base64,');
                const contentType = res[0].split(':')[1];
                const b64Data = res[1];
                const byteCharacters = atob(b64Data);
                const byteArrays = [];
                
                for (let i = 0; i < byteCharacters.length; i++) {
                    byteArrays.push(byteCharacters.charCodeAt(i));
                }
                
                const byteArray = new Uint8Array(byteArrays);
                const blob = new Blob([byteArray], { type: contentType });
                
                // Buat File object
                return new File([blob], `photo_${index}.jpg`, { type: contentType });
            });
            
            // Buat DataTransfer object untuk mensimulasikan input file
            const dataTransfer = new DataTransfer();
            files.forEach(file => dataTransfer.items.add(file));
            
            // Set files ke input file
            const input = document.getElementById(inputId);
            input.files = dataTransfer.files;
            
            // Tampilkan preview
            displayPhotosPreview(files, previewId);
            
            // Tutup modal
            bootstrap.Modal.getInstance(document.getElementById('cameraModal')).hide();
        }
        
        // Fungsi untuk menampilkan preview foto
        function displayPhotosPreview(files, previewId) {
            const previewContainer = document.getElementById(previewId);
            previewContainer.innerHTML = '';
            
            files.forEach((file, index) => {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const photoDiv = document.createElement('div');
                    photoDiv.className = 'position-relative d-inline-block me-2 mb-2';
                    photoDiv.innerHTML = `
                        <img src="${e.target.result}" class="photo-preview" alt="Preview">
                        <button type="button" class="btn btn-danger btn-sm position-absolute" 
                                style="top: -5px; right: -5px; padding: 0px 4px;"
                                onclick="removePhoto(${activeItemId}, this)">×</button>
                    `;
                    previewContainer.appendChild(photoDiv);
                }
                
                reader.readAsDataURL(file);
            });
        }
        
        // Fungsi untuk menghapus foto dari preview
        function removePhoto(itemId, button) {
            const isMobile = window.innerWidth < 768;
            const inputId = isMobile ? `mobile_photo_${itemId}` : `photo_${itemId}`;
            const previewId = isMobile ? `mobile_preview_${itemId}` : `preview_${itemId}`;
            
            // Hapus elemen foto dari preview
            button.closest('.position-relative').remove();
            
            // Jika tidak ada foto lagi, kosongkan input file
            const previewContainer = document.getElementById(previewId);
            if (previewContainer.children.length === 0) {
                document.getElementById(inputId).value = '';
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const outletSelect = document.querySelector('select[name="outlet_id"]');
            const outletHidden = document.querySelector('input[name="id_outlet"]');
            const submitBtn = document.getElementById('submitBtn');
            const autosaveStatus = document.getElementById('autosaveStatus');
            
            // Fungsi untuk menampilkan status autosave
            function updateAutosaveStatus(status, message) {
                autosaveStatus.innerHTML = '';
                
                if (status === 'saving') {
                    autosaveStatus.innerHTML = `
                        <span class="badge bg-warning">
                            <i class="ri-refresh-line me-1 fa-spin"></i> Menyimpan...
                        </span>
                    `;
                } else if (status === 'success') {
                    autosaveStatus.innerHTML = `
                        <span class="badge bg-success">
                            <i class="ri-check-line me-1"></i> Tersimpan
                        </span>
                    `;
                    
                    // Reset ke status normal setelah 3 detik
                    setTimeout(() => {
                        autosaveStatus.innerHTML = `
                            <span class="badge bg-info">
                                <i class="ri-save-line me-1"></i> Siap menyimpan otomatis
                            </span>
                        `;
                    }, 3000);
                } else if (status === 'error') {
                    autosaveStatus.innerHTML = `
                        <span class="badge bg-danger">
                            <i class="ri-error-warning-line me-1"></i> Gagal: ${message || 'Error tidak diketahui'}
                        </span>
                    `;
                    
                    // Reset ke status normal setelah 5 detik
                    setTimeout(() => {
                        autosaveStatus.innerHTML = `
                            <span class="badge bg-info">
                                <i class="ri-save-line me-1"></i> Siap menyimpan otomatis
                            </span>
                        `;
                    }, 5000);
                }
            }
            
            // Fungsi untuk mengisi waktu otomatis
            function setTimeForCondition(radioElement) {
                const row = $(radioElement).closest('tr') || $(radioElement).closest('.mobile-row');
                const timeInput = row.find('input[type="time"]');
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                timeInput.val(`${hours}:${minutes}`);
                
                // Trigger autosave setelah mengisi waktu
                triggerAutosave();
            }

            // Fungsi untuk trigger autosave
            function triggerAutosave() {
                // Jika sudah ada autosave yang sedang berjalan, jangan lakukan lagi
                if (autosaveInProgress) {
                    return;
                }
                
                clearTimeout(autosaveTimeout);
                autosaveTimeout = setTimeout(function() {
                    console.log('Triggering autosave...');
                    
                    // Buat FormData baru
                    const formData = new FormData();
                    
                    // Tambahkan data dasar yang diperlukan
                    formData.append('id_outlet', $('input[name="id_outlet"]').val());
                    formData.append('date', $('input[name="date"]').val());
                    formData.append('status', $('input[name="status"]').val());
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    
                    // Cari semua item yang perlu disimpan (baik yang sudah memiliki condition atau field lain yang berubah)
                    const itemsToSave = new Set();
                    
                    // Tambahkan semua item yang sudah memiliki condition
                    $('input[type="radio"][name^="checks["]:checked').each(function() {
                        const name = $(this).attr('name');
                        const itemId = name.match(/\[(\d+)\]/)[1];
                        itemsToSave.add(itemId);
                    });
                    
                    // Tambahkan semua item yang memiliki other_issue terisi
                    $('input[name^="checks["][name$="[other_issue]"]').each(function() {
                        if ($(this).val()) {
                            const name = $(this).attr('name');
                            const itemId = name.match(/\[(\d+)\]/)[1];
                            itemsToSave.add(itemId);
                        }
                    });
                    
                    // Tambahkan semua item yang memiliki remark terisi
                    $('textarea[name^="checks["][name$="[remark]"]').each(function() {
                        if ($(this).val()) {
                            const name = $(this).attr('name');
                            const itemId = name.match(/\[(\d+)\]/)[1];
                            itemsToSave.add(itemId);
                        }
                    });
                    
                    // Jika tidak ada item untuk disimpan, batalkan autosave
                    if (itemsToSave.size === 0) {
                        console.log('No items to save, skipping autosave');
                        return;
                    }
                    
                    console.log('Items to save:', Array.from(itemsToSave));
                    
                    // Untuk setiap item yang perlu disimpan, kumpulkan semua datanya
                    itemsToSave.forEach(itemId => {
                        // Cek condition (mungkin belum dipilih)
                        const conditionRadio = $(`input[name="checks[${itemId}][condition]"]:checked`);
                        if (conditionRadio.length > 0) {
                            formData.append(`checks[${itemId}][condition]`, conditionRadio.val());
                        } else {
                            // Jika condition belum dipilih, gunakan NA
                            formData.append(`checks[${itemId}][condition]`, 'NA');
                        }
                        
                        // Ambil other_issue (bahkan jika kosong)
                        const otherIssueInput = $(`input[name="checks[${itemId}][other_issue]"]`);
                        formData.append(`checks[${itemId}][other_issue]`, otherIssueInput.val() || '');
                        console.log(`Appending other_issue for item ${itemId}: "${otherIssueInput.val() || ''}"`);
                        
                        // Ambil remark (bahkan jika kosong)
                        const remarkInput = $(`textarea[name="checks[${itemId}][remark]"]`);
                        formData.append(`checks[${itemId}][remark]`, remarkInput.val() || '');
                        console.log(`Appending remark for item ${itemId}: "${remarkInput.val() || ''}"`);
                        
                        // Ambil time (jika ada) atau gunakan waktu sekarang
                        const timeInput = $(`input[name="checks[${itemId}][time]"]`);
                        if (timeInput.val()) {
                            formData.append(`checks[${itemId}][time]`, timeInput.val());
                        } else {
                            // Jika time belum diisi, gunakan waktu sekarang
                            const now = new Date();
                            const time = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
                            formData.append(`checks[${itemId}][time]`, time);
                        }
                        
                        // Cari file uploads
                        const photoInput = $(`input[type="file"][name="checks[${itemId}][photos][]"]`)[0];
                        if (photoInput && photoInput.files.length > 0) {
                            for (let i = 0; i < photoInput.files.length; i++) {
                                formData.append(`checks[${itemId}][photos][]`, photoInput.files[i]);
                                console.log(`Appending photo for item ${itemId}`);
                            }
                        }
                    });
                    
                    // Log form data untuk debugging
                    console.log('Form data yang akan dikirim:');
                    for (let pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }
                    
                    // Set status autosave sedang berjalan
                    autosaveInProgress = true;
                    updateAutosaveStatus('saving');
                    
                    $.ajax({
                        url: '{{ route("daily-check.autosave") }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            autosaveInProgress = false;
                            
                            if (response.success) {
                                console.log('Autosave berhasil:', response);
                                // Update status autosave
                                updateAutosaveStatus('success');
                            } else {
                                console.error('Autosave gagal:', response);
                                // Update status autosave
                                updateAutosaveStatus('error', response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            autosaveInProgress = false;
                            console.error('Error autosave:', error);
                            console.error('Response:', xhr.responseText);
                            
                            // Update status autosave
                            updateAutosaveStatus('error', xhr.responseJSON?.message || error);
                        }
                    });
                }, 1000); // Delay 1 detik sebelum autosave
            }

            // Event listener untuk outlet selection
            if (outletSelect) {
                outletSelect.addEventListener('change', function() {
                    outletHidden.value = this.value;
                    submitBtn.disabled = !this.value;
                    triggerAutosave();
                });
            }

            // Form validation before submit
            document.getElementById('dailyCheckForm').addEventListener('submit', function(e) {
                if (outletSelect && !outletSelect.value) {
                    e.preventDefault();
                    alert('Silakan pilih outlet terlebih dahulu');
                    outletSelect.focus();
                } else {
                    // Ubah status menjadi 'saved' saat submit
                    document.querySelector('input[name="status"]').value = 'saved';
                    
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menyimpan data',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }
            });

            // Event listener untuk radio button
            document.querySelectorAll('input[type="radio"][name^="checks["]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    console.log('Radio changed:', this.name, this.value);
                    setTimeForCondition(this);
                });
            });

            // Event listener untuk input text dan time
            document.querySelectorAll('input[type="text"][name^="checks["], input[type="time"][name^="checks["]').forEach(function(input) {
                input.addEventListener('change', function() {
                    console.log('Input changed:', this.name, this.value);
                    triggerAutosave();
                });
                
                // Tambahkan event keyup untuk input text dengan delay
                if(input.type === "text") {
                    let typingTimer;
                    input.addEventListener('keyup', function() {
                        console.log('Input keyup:', this.name, this.value);
                        clearTimeout(typingTimer);
                        typingTimer = setTimeout(() => {
                            triggerAutosave();
                        }, 500); // Tunggu 500ms setelah user berhenti mengetik
                    });
                    
                    // Batalkan timer jika user mulai mengetik lagi
                    input.addEventListener('keydown', function() {
                        clearTimeout(typingTimer);
                    });
                }
            });

            // Event listener untuk textarea
            document.querySelectorAll('textarea[name^="checks["]').forEach(function(textarea) {
                textarea.addEventListener('change', function() {
                    console.log('Textarea changed:', this.name, this.value);
                    triggerAutosave();
                });
                
                // Tambahkan event keyup dengan delay
                let typingTimer;
                textarea.addEventListener('keyup', function() {
                    console.log('Textarea keyup:', this.name, this.value);
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => {
                        triggerAutosave();
                    }, 500); // Tunggu 500ms setelah user berhenti mengetik
                });
                
                // Batalkan timer jika user mulai mengetik lagi
                textarea.addEventListener('keydown', function() {
                    clearTimeout(typingTimer);
                });
            });
            
            // Cek jika ada input file, tambahkan event listener
            document.querySelectorAll('input[type="file"][name^="checks["]').forEach(function(fileInput) {
                fileInput.addEventListener('change', function() {
                    console.log('File input changed:', this.name);
                    triggerAutosave();
                });
            });
            
            // Lakukan autosave awal untuk memastikan data tersimpan sebagai draft
            setTimeout(triggerAutosave, 2000);
        });

        // Store expanded state
        let expandedAreas = new Set();

        function toggleArea(areaId) {
            const content = document.getElementById(areaId);
            const header = content.previousElementSibling.querySelector('.area-header') || 
                          document.querySelector(`[onclick*="${areaId}"]`);
            const icon = header.querySelector('i');

            if (content.style.display === 'none') {
                // Expand
                content.style.display = '';
                header.classList.remove('collapsed');
                expandedAreas.add(areaId);
            } else {
                // Collapse
                content.style.display = 'none';
                header.classList.add('collapsed');
                expandedAreas.delete(areaId);
            }

            // Save state to localStorage
            localStorage.setItem('expandedAreas', JSON.stringify(Array.from(expandedAreas)));
        }

        // Initialize areas on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Restore expanded state from localStorage
            try {
                const savedState = JSON.parse(localStorage.getItem('expandedAreas')) || [];
                expandedAreas = new Set(savedState);
            } catch (e) {
                expandedAreas = new Set();
            }

            // Apply initial state
            document.querySelectorAll('.area-content, .mobile-area-content').forEach(content => {
                const areaId = content.id;
                const header = content.previousElementSibling.querySelector('.area-header') || 
                              document.querySelector(`[onclick*="${areaId}"]`);

                if (!expandedAreas.has(areaId)) {
                    content.style.display = 'none';
                    header.classList.add('collapsed');
                }
            });

            // ... existing DOMContentLoaded code ...
        });

        // Add keyboard support
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                const activeElement = document.activeElement;
                if (activeElement.classList.contains('area-header') || 
                    activeElement.classList.contains('mobile-area-header')) {
                    e.preventDefault();
                    activeElement.click();
                }
            }
        });
    </script>
@endsection 