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
            Edit Daily Check Report
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
                                <span class="ms-2">{{ $dailyCheck->outlet->nama_outlet }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('daily-check.update', $dailyCheck->id) }}" method="POST" id="dailyCheckForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="date" value="{{ $dailyCheck->date }}">
                        <input type="hidden" name="id_outlet" value="{{ $dailyCheck->id_outlet }}">
                        <input type="hidden" name="status" id="status" value="{{ $dailyCheck->status }}">
                        
                        <!-- Status Select -->
                        <div class="mb-3">
                            <label for="statusSelect" class="form-label">Status</label>
                            <select class="form-select" id="statusSelect">
                                <option value="draft" {{ $dailyCheck->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="saved" {{ $dailyCheck->status == 'saved' ? 'selected' : '' }}>Saved</option>
                            </select>
                        </div>
                        
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
                                                    @php
                                                        $detail = $dailyCheck->details->firstWhere('item_id', $item->id);
                                                        $condition = $detail ? trim($detail->condition) : 'NA';
                                                        $otherIssue = $detail ? $detail->other_issue : '';
                                                        $time = $detail ? $detail->time : '';
                                                        $remark = $detail ? $detail->remark : '';
                                                        $photos = $detail ? $detail->photos : collect();
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>{{ $item->name }}</td>
                                                        @foreach(['C', 'WM', 'D', 'NA'] as $conditionType)
                                                            <td class="text-center">
                                                                <input type="radio" 
                                                                       class="condition-radio"
                                                                       id="condition_{{ $item->id }}_{{ $conditionType }}"
                                                                       name="checks[{{ $item->id }}][condition]" 
                                                                       value="{{ $conditionType }}" 
                                                                       {{ $condition == $conditionType ? 'checked="checked"' : '' }}
                                                                       required>
                                                                <label class="condition-label" for="condition_{{ $item->id }}_{{ $conditionType }}">
                                                                    {{ $conditionType }}
                                                                </label>
                                                            </td>
                                                        @endforeach
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm" 
                                                                   name="checks[{{ $item->id }}][other_issue]" 
                                                                   value="{{ $otherIssue }}"
                                                                   placeholder="Tambahkan issue">
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control form-control-sm" 
                                                                   name="checks[{{ $item->id }}][time]" 
                                                                   value="{{ $time }}"
                                                                   required>
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
                                                            <div id="preview_{{ $item->id }}" class="photo-preview-container">
                                                                @foreach($photos as $photo)
                                                                    <div class="position-relative" style="width: 60px; height: 60px;">
                                                                        <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                                                                             class="photo-preview" 
                                                                             onclick="openImageModal('{{ asset('storage/' . $photo->photo_path) }}')"
                                                                             alt="Photo">
                                                                        <span class="photo-action" onclick="deletePhoto({{ $photo->id }}, this)">
                                                                            <i class="ri-delete-bin-line"></i>
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <textarea class="form-control form-control-sm" 
                                                                   name="checks[{{ $item->id }}][remark]" 
                                                                   placeholder="Tambahkan catatan">{{ $remark }}</textarea>
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
                                            @php
                                                $detail = $dailyCheck->details->firstWhere('item_id', $item->id);
                                                $condition = $detail ? trim($detail->condition) : 'NA';
                                                $otherIssue = $detail ? $detail->other_issue : '';
                                                $time = $detail ? $detail->time : '';
                                                $remark = $detail ? $detail->remark : '';
                                                $photos = $detail ? $detail->photos : collect();
                                            @endphp
                                            <div class="mobile-row">
                                                <div class="mobile-label">{{ $index + 1 }}. {{ $item->name }}</div>
                                                
                                                <div class="condition-group">
                                                    @foreach(['C', 'WM', 'D', 'NA'] as $conditionType)
                                                        <div class="condition-item">
                                                            <input type="radio" 
                                                                   class="condition-radio"
                                                                   id="mobile_condition_{{ $item->id }}_{{ $conditionType }}"
                                                                   name="checks[{{ $item->id }}][condition]" 
                                                                   value="{{ $conditionType }}" 
                                                                   {{ $condition == $conditionType ? 'checked="checked"' : '' }}
                                                                   required>
                                                            <label class="condition-label" 
                                                                   for="mobile_condition_{{ $item->id }}_{{ $conditionType }}">
                                                                {{ $conditionType }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div class="form-group mt-2">
                                                    <label class="mobile-label">Other Issue</label>
                                                    <input type="text" class="form-control" 
                                                           name="checks[{{ $item->id }}][other_issue]" 
                                                           value="{{ $otherIssue }}"
                                                           placeholder="Tambahkan issue">
                                                </div>

                                                <div class="form-group mt-2">
                                                    <label class="mobile-label">Time</label>
                                                    <input type="time" class="form-control" 
                                                           name="checks[{{ $item->id }}][time]" 
                                                           value="{{ $time }}"
                                                           required>
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
                                                        <div id="mobile_preview_{{ $item->id }}" class="photo-preview-container mt-2">
                                                            @foreach($photos as $photo)
                                                                <div class="position-relative" style="width: 60px; height: 60px;">
                                                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                                                                         class="photo-preview" 
                                                                         onclick="openImageModal('{{ asset('storage/' . $photo->photo_path) }}')"
                                                                         alt="Photo">
                                                                    <span class="photo-action" onclick="deletePhoto({{ $photo->id }}, this)">
                                                                        <i class="ri-delete-bin-line"></i>
                                                                    </span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group mt-2">
                                                    <label class="mobile-label">Remark</label>
                                                    <textarea class="form-control" 
                                                           name="checks[{{ $item->id }}][remark]" 
                                                           placeholder="Tambahkan catatan">{{ $remark }}</textarea>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-end mt-3">
                            <a href="{{ route('daily-check.list') }}" class="btn btn-secondary me-2">
                                <i class="ri-arrow-left-line me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnSave">
                                <i class="ri-save-line me-1"></i> Simpan Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal untuk preview gambar -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Preview Gambar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modalImage" class="modal-image">
                </div>
            </div>
        </div>
    </div>
    
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
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/dropzone/dropzone-min.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
        // Variabel global untuk menyimpan stream kamera dan item ID yang aktif
        let cameraStream = null;
        let activeItemId = null;
        let capturedPhotos = [];
        let autosaveTimer;
        let lastSavedData = '';
        
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
        
        // Open image modal
        function openImageModal(imageSrc) {
            $('#modalImage').attr('src', imageSrc);
            const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
            imageModal.show();
        }
        
        // Delete photo
        function deletePhoto(photoId, element) {
            if (confirm('Apakah Anda yakin ingin menghapus foto ini?')) {
                $.ajax({
                    url: "{{ url('/daily-check/delete-photo') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        photo_id: photoId
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remove photo element
                            $(element).parent().remove();
                        } else {
                            alert('Error deleting photo: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Error deleting photo. Please try again.');
                    }
                });
            }
        }
        
        // Update hidden status field dari select
        function updateStatusFromSelect() {
            const status = $('#statusSelect').val();
            $('#status').val(status);
        }
        
        // Setup autosave
        function setupAutosave() {
            // Capture initial form data
            lastSavedData = $('#dailyCheckForm').serialize();
            
            // Listen for form changes
            $('#dailyCheckForm').on('change', 'input, textarea, select', function() {
                clearTimeout(autosaveTimer);
                updateAutosaveStatus('typing');
                
                autosaveTimer = setTimeout(function() {
                    const currentData = $('#dailyCheckForm').serialize();
                    if (currentData !== lastSavedData) {
                        saveForm('draft');
                    }
                }, 30000); // 30 seconds delay
            });
            
            // Clear autosave timer if user actively saves
            $('#btnSave').click(function() {
                clearTimeout(autosaveTimer);
            });
        }
        
        // Update autosave status indicator
        function updateAutosaveStatus(status) {
            const statusElem = $('#autosaveStatus');
            const textElem = $('#autosaveText');
            
            statusElem.removeClass('saving saved');
            
            if (status === 'typing') {
                statusElem.addClass('saving');
                textElem.text('Perubahan belum disimpan');
            } else if (status === 'saving') {
                statusElem.addClass('saving');
                textElem.text('Menyimpan...');
            } else if (status === 'saved') {
                statusElem.addClass('saved');
                textElem.text('Tersimpan pada ' + new Date().toLocaleTimeString());
            }
        }
        
        // Save form
        function saveForm(status) {
            // Update status
            $('#status').val(status);
            
            // Get form data
            const formData = new FormData($('#dailyCheckForm')[0]);
            
            // Add any additional fields as needed
            formData.append('_method', 'PUT');
            
            // Show saving status
            updateAutosaveStatus('saving');
            
            // Log form data for debugging
            console.log('Form Data:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            $.ajax({
                url: "{{ route('daily-check.update', $dailyCheck->id) }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log('Response success:', response);
                    if (response.success) {
                        // Update last saved data
                        lastSavedData = $('#dailyCheckForm').serialize();
                        
                        // Show saved status
                        updateAutosaveStatus('saved');
                        
                        // If user deliberately saved (not autosave)
                        if (status === 'saved') {
                            $('#statusSelect').val('saved');
                            alert('Data berhasil disimpan!');
                            window.location.href = "{{ route('daily-check.list') }}";
                        }
                    } else {
                        alert('Error saving data: ' + response.message);
                    }
                },
                error: function(xhr) {
                    console.log('Response error:', xhr.responseText);
                    // Handle validation errors
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = 'Terjadi kesalahan:';
                        for (const field in errors) {
                            errorMessage += '\n- ' + errors[field][0];
                        }
                        alert(errorMessage);
                    } else {
                        alert('Error saving data. Please try again.');
                    }
                }
            });
        }
        
        // Toggle area collapse pada mobile dan desktop
        $('.area-header, .mobile-area-header').click(function() {
            $(this).toggleClass('collapsed');
        });

        // Initialization for toggling areas
        function toggleArea(areaId) {
            const content = document.getElementById(areaId);
            if (content) {
                if (content.style.display === 'none') {
                    content.style.display = '';
                } else {
                    content.style.display = 'none';
                }
            }
        }
        window.toggleArea = toggleArea;
        
        // Event listener untuk perubahan status
        $('#statusSelect').change(function() {
            updateStatusFromSelect();
        });
        
        // Event for photo upload
        $(document).on('change', '.photo-upload', function(e) {
            handlePhotoUpload(e);
        });
        
        // Tombol Save
        $('#btnSave').click(function(e) {
            e.preventDefault();
            saveForm('saved');
        });
        
        // Jalankan saat dokumen siap
        document.addEventListener('DOMContentLoaded', function() {
            // Setup form
            updateStatusFromSelect();
            setupAutosave();
            
            // Set radio button berdasarkan data yang ada
            document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
                const name = radio.name;
                const value = radio.value;
                const itemId = name.match(/\[(\d+)\]/)[1];
                const detail = @json($dailyCheck->details);
                const itemDetail = detail.find(d => d.item_id == itemId);
                
                if (itemDetail && itemDetail.condition === value) {
                    radio.checked = true;
                }
            });

            // Event handler untuk radio button
            document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    const name = this.name;
                    const value = this.value;
                    
                    // Sync semua radio button dengan nama yang sama
                    document.querySelectorAll(`input[name="${name}"]`).forEach(function(r) {
                        r.checked = (r.value === value);
                    });
                });
            });

            // Inisialisasi preview foto yang sudah ada untuk desktop dan mobile
            const details = @json($dailyCheck->details);
            
            details.forEach(function(detail) {
                if (detail.photos && detail.photos.length > 0) {
                    // Desktop view
                    const desktopPreviewId = `preview_${detail.item_id}`;
                    const desktopContainer = document.getElementById(desktopPreviewId);
                    
                    // Mobile view
                    const mobilePreviewId = `mobile_preview_${detail.item_id}`;
                    const mobileContainer = document.getElementById(mobilePreviewId);
                    
                    detail.photos.forEach(function(photo) {
                        const photoHtml = `
                            <div class="position-relative" style="width: 60px; height: 60px;">
                                <img src="{{ asset('storage/') }}/${photo.photo_path}" 
                                     class="photo-preview" 
                                     onclick="openImageModal('{{ asset('storage/') }}/${photo.photo_path}')"
                                     alt="Photo">
                                <span class="photo-action" onclick="deletePhoto(${photo.id}, this)">
                                    <i class="ri-delete-bin-line"></i>
                                </span>
                            </div>
                        `;
                        
                        // Tambahkan ke desktop view jika container ada
                        if (desktopContainer) {
                            desktopContainer.innerHTML += photoHtml;
                        }
                        
                        // Tambahkan ke mobile view jika container ada
                        if (mobileContainer) {
                            mobileContainer.innerHTML += photoHtml;
                        }
                    });
                }
            });
        });
    </script>
@endsection 