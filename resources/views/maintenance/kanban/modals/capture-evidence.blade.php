<!-- Capture Evidence Modal -->
<div class="modal fade" id="captureEvidenceModal" tabindex="-1" aria-labelledby="captureEvidenceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="captureEvidenceModalLabel">Capture Evidence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-primary w-100" id="capturePhotoBtn">
                            <i class="ri-camera-line me-1"></i> Ambil Foto
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-info w-100" id="startVideoBtn">
                            <i class="ri-video-line me-1"></i> Rekam Video
                        </button>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6>Preview</h6>
                    <div class="capture-preview d-flex flex-wrap gap-2">
                        <!-- Preview foto dan video akan ditampilkan di sini -->
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" id="captureEvidenceNotes" rows="3" placeholder="Tambahkan catatan untuk evidence ini..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveEvidenceBtn">Simpan Evidence</button>
            </div>
        </div>
    </div>
</div>

<!-- Camera Modal -->
<div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cameraModalLabel">Kamera</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <style>
                    #cameraStream.loading {
                        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 50 50'%3E%3Cpath fill='%23fff' d='M25.251,6.461c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615V6.461z'%3E%3CanimateTransform attributeType='xml' attributeName='transform' type='rotate' from='0 25 25' to='360 25 25' dur='0.6s' repeatCount='indefinite'/%3E%3C/path%3E%3C/svg%3E");
                        background-position: center;
                        background-repeat: no-repeat;
                        background-size: 50px;
                        background-color: #000;
                        min-height: 200px;
                    }
                    .camera-container {
                        position: relative;
                        min-height: 200px;
                        background-color: #000;
                        border-radius: 0.25rem;
                        overflow: hidden;
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
                </style>
                <div class="camera-container">
                    <video id="cameraStream" class="w-100 rounded loading" autoplay playsinline muted></video>
                    <div class="camera-status" id="cameraStatus">Memuat kamera...</div>
                </div>
                
                <!-- Browser Support Warning -->
                <div class="alert alert-warning mt-3 d-none" id="browserSupportWarning">
                    <strong>Peringatan!</strong> 
                    <span id="browserSupportMessage">Browser Anda mungkin tidak mendukung akses kamera secara penuh.</span>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-light" id="switchCameraBtn">
                    <i class="ri-camera-switch-line"></i> Ganti Kamera
                </button>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger d-none" id="stopVideoBtn">
                        <i class="ri-stop-circle-line"></i> Stop Rekam
                    </button>
                    <button type="button" class="btn btn-primary" id="captureBtn">
                        <i class="ri-camera-line"></i> Ambil Foto
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Media Preview -->
<div class="modal fade" id="mediaPreviewModal" tabindex="-1" aria-labelledby="mediaPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mediaPreviewModalLabel">Preview Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="mediaPreviewContent">
                    <!-- Konten akan diisi oleh JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div> 