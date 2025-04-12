<!-- Modal Evidence Capture -->
<div class="modal fade" id="evidenceCaptureModal" tabindex="-1" aria-labelledby="evidenceCaptureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="evidenceCaptureModalLabel">Upload Evidence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="evidenceCaptureForm">
                    <input type="hidden" id="evidenceTaskId" name="task_id">
                    
                    <!-- Tombol Aksi -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <button type="button" class="btn btn-primary w-100" id="capturePhotoBtn">
                                <i class="ri-camera-line me-1"></i> Ambil Foto
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-info w-100" id="captureVideoBtn">
                                <i class="ri-video-line me-1"></i> Rekam Video
                            </button>
                        </div>
                    </div>
                    
                    <!-- Preview Media -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <ul class="nav nav-tabs" id="mediaTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="photoTab" data-bs-toggle="tab" data-bs-target="#photoContent" type="button" role="tab" aria-controls="photoContent" aria-selected="true">
                                        <i class="ri-image-line me-1"></i> Foto (<span id="photoCount">0</span>)
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="videoTab" data-bs-toggle="tab" data-bs-target="#videoContent" type="button" role="tab" aria-controls="videoContent" aria-selected="false">
                                        <i class="ri-video-line me-1"></i> Video (<span id="videoCount">0</span>)
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content p-3 border border-top-0 rounded-bottom">
                                <div class="tab-pane fade show active" id="photoContent" role="tabpanel" aria-labelledby="photoTab">
                                    <div id="noPhotoMsg" class="text-center py-3">
                                        <p class="text-muted mb-0">Belum ada foto. Silakan ambil foto dengan kamera.</p>
                                    </div>
                                    <div id="photoPreview" class="d-flex flex-wrap gap-2">
                                        <!-- Photo previews will be added here -->
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="videoContent" role="tabpanel" aria-labelledby="videoTab">
                                    <div id="noVideoMsg" class="text-center py-3">
                                        <p class="text-muted mb-0">Belum ada video. Silakan rekam video dengan kamera.</p>
                                    </div>
                                    <div id="videoPreview" class="d-flex flex-wrap gap-2">
                                        <!-- Video previews will be added here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="evidenceNotes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="evidenceNotes" name="notes" rows="3" placeholder="Tambahkan catatan untuk evidence ini..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveEvidenceBtn" disabled>Simpan Evidence</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kamera -->
<div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cameraModalLabel">Kamera</h5>
                <button type="button" class="btn-close" id="closeCameraBtn" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="camera-container">
                    <video id="cameraStream" class="w-100 rounded" autoplay playsinline></video>
                    <div class="video-timer d-none" id="videoTimer">00:00</div>
                    <div class="camera-status" id="cameraStatus">Siap</div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-light" id="switchCameraBtn">
                    <i class="ri-camera-switch-line"></i> Ganti Kamera
                </button>
                <div>
                    <button type="button" class="btn btn-secondary" id="cancelCaptureBtn">Batal</button>
                    <button type="button" class="btn btn-danger d-none" id="stopRecordingBtn">
                        <i class="ri-stop-circle-line"></i> Stop Rekam
                    </button>
                    <button type="button" class="btn btn-primary" id="captureImageBtn">
                        <i class="ri-camera-line"></i> Ambil Foto
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Media -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Preview Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="previewContent">
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
