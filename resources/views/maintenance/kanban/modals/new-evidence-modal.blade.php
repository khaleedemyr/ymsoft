<!-- resources/views/maintenance/kanban/modals/new-evidence-modal.blade.php -->
<!-- New Evidence Modal -->
<div class="modal fade" id="newEvidenceModal" tabindex="-1" aria-labelledby="newEvidenceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newEvidenceModalLabel">Upload Evidence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newEvidenceForm">
                    <input type="hidden" id="newEvidenceTaskId" name="task_id">
                    
                    <!-- Tombol Aksi -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <button type="button" class="btn btn-primary w-100" id="newCapturePhotoBtn">
                                <i class="ri-camera-line me-1"></i> Ambil Foto
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-info w-100" id="newCaptureVideoBtn">
                                <i class="ri-video-line me-1"></i> Rekam Video
                            </button>
                        </div>
                    </div>
                    
                    <!-- Preview Media -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <ul class="nav nav-tabs" id="newMediaTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="newPhotoTab" data-bs-toggle="tab" data-bs-target="#newPhotoContent" type="button" role="tab" aria-controls="newPhotoContent" aria-selected="true">
                                        <i class="ri-image-line me-1"></i> Foto (<span id="newPhotoCount">0</span>)
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="newVideoTab" data-bs-toggle="tab" data-bs-target="#newVideoContent" type="button" role="tab" aria-controls="newVideoContent" aria-selected="false">
                                        <i class="ri-video-line me-1"></i> Video (<span id="newVideoCount">0</span>)
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content p-3 border border-top-0 rounded-bottom">
                                <div class="tab-pane fade show active" id="newPhotoContent" role="tabpanel" aria-labelledby="newPhotoTab">
                                    <div id="newNoPhotoMsg" class="text-center py-3">
                                        <p class="text-muted mb-0">Belum ada foto. Silakan ambil foto dengan kamera.</p>
                                    </div>
                                    <div id="newPhotoPreview" class="d-flex flex-wrap gap-2">
                                        <!-- Photo previews will be added here -->
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="newVideoContent" role="tabpanel" aria-labelledby="newVideoTab">
                                    <div id="newNoVideoMsg" class="text-center py-3">
                                        <p class="text-muted mb-0">Belum ada video. Silakan rekam video dengan kamera.</p>
                                    </div>
                                    <div id="newVideoPreview" class="d-flex flex-wrap gap-2">
                                        <!-- Video previews will be added here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="newEvidenceNotes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="newEvidenceNotes" name="notes" rows="3" placeholder="Tambahkan catatan untuk evidence ini..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="newSaveEvidenceBtn" disabled>Simpan Evidence</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kamera Baru -->
<div class="modal fade" id="newCameraModal" tabindex="-1" aria-labelledby="newCameraModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newCameraModalLabel">Kamera</h5>
                <button type="button" class="btn-close" id="newCloseCameraBtn" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="camera-container">
                    <video id="newCameraStream" class="w-100 rounded" autoplay playsinline></video>
                    <div class="video-timer d-none" id="newVideoTimer">00:00</div>
                    <div class="camera-status" id="newCameraStatus">Siap</div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-light" id="newSwitchCameraBtn">
                    <i class="ri-camera-switch-line"></i> Ganti Kamera
                </button>
                <div>
                    <button type="button" class="btn btn-secondary" id="newCancelCaptureBtn">Batal</button>
                    <button type="button" class="btn btn-danger d-none" id="newStopRecordingBtn">
                        <i class="ri-stop-circle-line"></i> Stop Rekam
                    </button>
                    <button type="button" class="btn btn-primary" id="newCaptureImageBtn">
                        <i class="ri-camera-line"></i> Ambil Foto
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Media Baru -->
<div class="modal fade" id="newPreviewModal" tabindex="-1" aria-labelledby="newPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newPreviewModalLabel">Preview Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="newPreviewContent">
                    <!-- Content will be inserted here by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .camera-container {
        position: relative;
        background-color: #000;
        border-radius: 0.25rem;
        overflow: hidden;
        aspect-ratio: 4/3;
    }
    
    .camera-status {
        position: absolute;
        bottom: 10px;
        left: 10px;
        color: #fff;
        background-color: rgba(0,0,0,0.5);
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 12px;
    }
    
    .video-timer {
        position: absolute;
        top: 10px;
        right: 10px;
        color: #fff;
        background-color: rgba(255,0,0,0.7);
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 14px;
        font-weight: bold;
    }
    
    .media-preview {
        position: relative;
        width: 120px;
        height: 120px;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .media-preview img,
    .media-preview video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .media-actions {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(0,0,0,0.5);
        display: flex;
        justify-content: space-around;
        padding: 5px;
    }
    
    .media-actions button {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 2px 5px;
        font-size: 14px;
    }
    
    .media-actions button:hover {
        color: #ffc107;
    }
</style>
