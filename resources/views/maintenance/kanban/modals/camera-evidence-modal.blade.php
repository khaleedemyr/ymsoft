<div class="modal fade" id="cameraEvidenceModal" tabindex="-1" aria-labelledby="cameraEvidenceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cameraEvidenceModalLabel">Capture Evidence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cameraEvidenceTaskId">
                
                <!-- Tombol Mode -->
                <div class="row mb-3">
                    <div class="col-6">
                        <button type="button" class="btn btn-primary w-100" id="photoCaptureBtn">
                            <i class="ri-camera-line me-1"></i> Mode Foto
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-info w-100" id="videoCaptureBtn">
                            <i class="ri-video-line me-1"></i> Mode Video
                        </button>
                    </div>
                </div>
                
                <!-- Camera Stream -->
                <div class="mb-4">
                    <div class="position-relative" style="background: #000; border-radius: 6px; overflow: hidden;">
                        <video id="cameraPreview" style="width: 100%; display: block;" autoplay playsinline></video>
                        <div id="videoTimerDisplay" class="position-absolute top-0 end-0 bg-danger text-white px-2 py-1 m-2 rounded d-none">00:00</div>
                        <div id="cameraStatus" class="position-absolute bottom-0 start-0 bg-dark bg-opacity-75 text-white px-2 py-1 m-2 rounded d-none">Siap</div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <button type="button" class="btn btn-sm btn-light" id="switchCameraBtn">
                            <i class="ri-camera-switch-line me-1"></i> Ganti Kamera
                        </button>
                        <div>
                            <button type="button" class="btn btn-sm btn-success" id="takePictureBtn">
                                <i class="ri-camera-line me-1"></i> Ambil Foto
                            </button>
                            <button type="button" class="btn btn-sm btn-danger d-none" id="recordVideoBtn">
                                <i class="ri-record-circle-line me-1"></i> Mulai Rekam
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Media Preview -->
                <div class="row mb-3">
                    <div class="col-12">
                        <h6>Media</h6>
                        <ul class="nav nav-pills mb-2" id="mediaTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="capturedPhotosTab" data-bs-toggle="pill" data-bs-target="#capturedPhotosContent" type="button" role="tab" aria-selected="true">
                                    Foto (<span id="photoCounter">0</span>)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="capturedVideosTab" data-bs-toggle="pill" data-bs-target="#capturedVideosContent" type="button" role="tab" aria-selected="false">
                                    Video (<span id="videoCounter">0</span>)
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content p-2 border rounded">
                            <div class="tab-pane fade show active" id="capturedPhotosContent" role="tabpanel">
                                <div id="noPhotosMessage" class="text-center text-muted p-3">
                                    Belum ada foto. Silakan ambil foto dari kamera.
                                </div>
                                <div id="capturedPhotosContainer" class="d-flex flex-wrap gap-2"></div>
                            </div>
                            <div class="tab-pane fade" id="capturedVideosContent" role="tabpanel">
                                <div id="noVideosMessage" class="text-center text-muted p-3">
                                    Belum ada video. Silakan merekam video dari kamera.
                                </div>
                                <div id="capturedVideosContainer" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notes -->
                <div class="mb-3">
                    <label for="cameraEvidenceNotes" class="form-label">Catatan</label>
                    <textarea class="form-control" id="cameraEvidenceNotes" rows="3" placeholder="Tambahkan catatan untuk evidence..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveEvidenceBtn" disabled>Simpan Evidence</button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="mediaPreviewModal" tabindex="-1" aria-labelledby="mediaPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mediaPreviewModalLabel">Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="previewContainer"></div>
            </div>
        </div>
    </div>
</div>

<style>
.media-thumb {
    position: relative;
    width: 100px;
    height: 100px;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.media-thumb img,
.media-thumb video {
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
    font-size: 14px;
    cursor: pointer;
}

.media-actions button:hover {
    color: #ffc107;
}
</style>
