<!-- Simple Evidence Modal -->
<div class="modal fade" id="simpleEvidenceModal" tabindex="-1" aria-labelledby="simpleEvidenceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="simpleEvidenceModalLabel">Upload Evidence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <button type="button" class="btn btn-primary w-100" id="takeCameraPhotoBtn">
                            <i class="ri-camera-line me-1"></i> Ambil Foto
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-info w-100" id="recordCameraVideoBtn">
                            <i class="ri-video-line me-1"></i> Rekam Video
                        </button>
                    </div>
                </div>
                
                <!-- Media Previews -->
                <div class="row mb-3">
                    <div class="col-12">
                        <h6>Foto & Video</h6>
                        <div class="media-preview-container">
                            <!-- Tab navigation -->
                            <ul class="nav nav-tabs" id="mediaTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="photos-tab" data-bs-toggle="tab" data-bs-target="#photos" type="button" role="tab" aria-controls="photos" aria-selected="true">
                                        <i class="ri-image-line me-1"></i> Foto (<span id="photoCount">0</span>)
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="videos-tab" data-bs-toggle="tab" data-bs-target="#videos" type="button" role="tab" aria-controls="videos" aria-selected="false">
                                        <i class="ri-video-line me-1"></i> Video (<span id="videoCount">0</span>)
                                    </button>
                                </li>
                            </ul>
                            
                            <!-- Tab content -->
                            <div class="tab-content p-3 border border-top-0 rounded-bottom bg-light" id="mediaTabContent">
                                <div class="tab-pane fade show active" id="photos" role="tabpanel" aria-labelledby="photos-tab">
                                    <div class="photo-preview d-flex flex-wrap gap-2">
                                        <div id="noPhotosMessage" class="text-center w-100 py-3">
                                            <p class="text-muted mb-0">Belum ada foto. Silakan ambil foto dengan kamera.</p>
                                        </div>
                                        <!-- Photo previews will be added here -->
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="videos" role="tabpanel" aria-labelledby="videos-tab">
                                    <div class="video-preview d-flex flex-wrap gap-2">
                                        <div id="noVideosMessage" class="text-center w-100 py-3">
                                            <p class="text-muted mb-0">Belum ada video. Silakan rekam video dengan kamera.</p>
                                        </div>
                                        <!-- Video previews will be added here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notes -->
                <div class="mb-3">
                    <label for="evidenceNotes" class="form-label">Catatan</label>
                    <textarea class="form-control" id="evidenceNotes" rows="3" placeholder="Tambahkan catatan untuk evidence ini..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="evidenceSaveBtn">Simpan Evidence</button>
            </div>
        </div>
    </div>
</div>

<!-- Camera Modal -->
<div class="modal fade" id="simpleCameraModal" tabindex="-1" aria-labelledby="simpleCameraModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="simpleCameraModalLabel">Kamera</h5>
                <button type="button" class="btn-close" id="closeCameraModalBtn" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <style>
                    #simpleCameraStream.loading {
                        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 50 50'%3E%3Cpath fill='%23fff' d='M25.251,6.461c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615V6.461z'%3E%3CanimateTransform attributeType='xml' attributeName='transform' type='rotate' from='0 25 25' to='360 25 25' dur='0.6s' repeatCount='indefinite'/%3E%3C/path%3E%3C/svg%3E");
                        background-position: center;
                        background-repeat: no-repeat;
                        background-size: 50px;
                        background-color: #000;
                    }
                    .simple-camera-container {
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
                </style>
                <div class="simple-camera-container">
                    <video id="simpleCameraStream" class="w-100 rounded loading" autoplay playsinline muted></video>
                    <div class="camera-status" id="simpleCameraStatus">Memuat kamera...</div>
                    <div class="video-timer d-none" id="videoTimer">00:00</div>
                </div>
                
                <!-- Browser Support Warning -->
                <div class="alert alert-warning mt-3 d-none" id="simpleBrowserWarning">
                    <strong>Peringatan!</strong> 
                    <span id="simpleBrowserMessage">Browser Anda mungkin tidak mendukung akses kamera secara penuh.</span>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-light" id="switchSimpleCameraBtn">
                    <i class="ri-camera-switch-line"></i> Ganti Kamera
                </button>
                <div>
                    <button type="button" class="btn btn-secondary" id="cancelCameraBtn">Batal</button>
                    <!-- Button for video recording, initially hidden -->
                    <button type="button" class="btn btn-danger d-none" id="stopVideoRecordingBtn">
                        <i class="ri-stop-circle-line"></i> Stop Rekam
                    </button>
                    <!-- Button for photo capture, initially shown -->
                    <button type="button" class="btn btn-primary" id="takePictureBtn">
                        <i class="ri-camera-line"></i> Ambil Foto
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Media Modal -->
<div class="modal fade" id="previewMediaModal" tabindex="-1" aria-labelledby="previewMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewMediaModalLabel">Preview Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="previewMediaContent">
                    <!-- Content will be inserted here by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Evidence Baru -->
<div class="modal fade" id="evidenceModal" tabindex="-1" aria-labelledby="evidenceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="evidenceModalLabel">Upload Evidence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="evidenceForm">
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
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Tambahkan catatan untuk evidence ini..."></textarea>
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
