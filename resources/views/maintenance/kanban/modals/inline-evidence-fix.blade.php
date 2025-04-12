<!-- resources/views/maintenance/kanban/modals/inline-evidence-fix.blade.php -->
<!-- Modal Khusus Evidence - Camera Mode -->
<div class="modal fade" id="directCameraModal" tabindex="-1" aria-labelledby="directCameraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="directCameraModalLabel">Capture Evidence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="directEvidenceForm" method="POST" action="{{ route('maintenance.kanban.upload-evidence') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="directTaskId" name="task_id">
                    
                    <!-- Area Kamera -->
                    <div class="mb-3">
                        <div class="position-relative bg-dark rounded overflow-hidden">
                            <video id="directCameraView" class="w-100" autoplay playsinline style="max-height: 60vh;"></video>
                            <div id="directCameraStatus" class="position-absolute bottom-0 start-0 m-2 text-white bg-dark bg-opacity-75 px-2 py-1 rounded d-none">
                                Mengaktifkan kamera...
                            </div>
                            <div id="directVideoTimer" class="position-absolute top-0 end-0 m-2 text-white bg-danger px-2 py-1 rounded d-none">
                                00:00
                            </div>
                        </div>
                        
                        <!-- Kontrol Kamera -->
                        <div class="d-flex justify-content-between mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="directSwitchCameraBtn">
                                <i class="ri-camera-switch-line"></i> Ganti Kamera
                            </button>
                            <div>
                                <button type="button" class="btn btn-sm btn-success" id="directPhotoBtn">
                                    <i class="ri-camera-line"></i> Ambil Foto
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" id="directVideoBtn">
                                    <i class="ri-record-circle-line"></i> Rekam Video
                                </button>
                                <button type="button" class="btn btn-sm btn-warning d-none" id="directStopBtn">
                                    <i class="ri-stop-circle-line"></i> Stop
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preview Media -->
                    <div class="mb-3">
                        <ul class="nav nav-tabs mb-2" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="directPhotosTab" data-bs-toggle="tab" data-bs-target="#directPhotosPane" type="button" role="tab" aria-selected="true">
                                    Foto (<span id="directPhotoCount">0</span>)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="directVideosTab" data-bs-toggle="tab" data-bs-target="#directVideosPane" type="button" role="tab" aria-selected="false">
                                    Video (<span id="directVideoCount">0</span>)
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content p-2 border border-top-0">
                            <div class="tab-pane fade show active" id="directPhotosPane" role="tabpanel">
                                <div id="directPhotoEmpty" class="text-center py-3 text-muted">
                                    Belum ada foto. Silakan ambil foto dari kamera.
                                </div>
                                <div id="directPhotoGallery" class="d-flex flex-wrap gap-2"></div>
                            </div>
                            <div class="tab-pane fade" id="directVideosPane" role="tabpanel">
                                <div id="directVideoEmpty" class="text-center py-3 text-muted">
                                    Belum ada video. Silakan rekam video dari kamera.
                                </div>
                                <div id="directVideoGallery" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Catatan -->
                    <div class="mb-3">
                        <label for="directEvidenceNotes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="directEvidenceNotes" name="notes" rows="3" placeholder="Tambahkan catatan untuk evidence..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="directSaveBtn" disabled>Simpan Evidence</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Media -->
<div class="modal fade" id="directPreviewModal" tabindex="-1" aria-labelledby="directPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="directPreviewModalLabel">Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="directPreviewContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Script Inline untuk menangani camera evidence -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Camera Evidence Inline Script Loaded');
    
    // Variabel state
    let capturedPhotos = [];
    let capturedVideos = [];
    let currentStream = null;
    let mediaRecorder = null;
    let recordedChunks = [];
    let recordingTimer = null;
    let recordingTime = 0;
    let facingMode = 'environment'; // Default kamera belakang
    
    // Cari tombol evidence
    const setupEvidenceButtons = function() {
        console.log('Setting up evidence buttons');
        const buttons = document.querySelectorAll('.new-evidence-btn');
        
        if (buttons.length === 0) {
            console.log('No evidence buttons found yet');
            return;
        }
        
        console.log('Found ' + buttons.length + ' evidence buttons');
        buttons.forEach(function(button) {
            // Hapus handler lama dengan cloning
            const newButton = button.cloneNode(true);
            if (button.parentNode) {
                button.parentNode.replaceChild(newButton, button);
            }
            
            // Pasang handler baru
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const taskId = this.getAttribute('data-task-id');
                console.log('Evidence button clicked for task ID: ' + taskId);
                
                openCameraEvidence(taskId);
            });
        });
    };
    
    // Buka modal kamera
    const openCameraEvidence = function(taskId) {
        // Set task ID ke form
        document.getElementById('directTaskId').value = taskId;
        
        // Reset captured media
        capturedPhotos = [];
        capturedVideos = [];
        
        // Reset UI
        document.getElementById('directPhotoGallery').innerHTML = '';
        document.getElementById('directVideoGallery').innerHTML = '';
        document.getElementById('directPhotoCount').textContent = '0';
        document.getElementById('directVideoCount').textContent = '0';
        document.getElementById('directPhotoEmpty').classList.remove('d-none');
        document.getElementById('directVideoEmpty').classList.remove('d-none');
        document.getElementById('directEvidenceNotes').value = '';
        document.getElementById('directSaveBtn').disabled = true;
        
        // Reset mode
        document.getElementById('directPhotoBtn').classList.remove('d-none');
        document.getElementById('directVideoBtn').classList.remove('d-none');
        document.getElementById('directStopBtn').classList.add('d-none');
        document.getElementById('directVideoTimer').classList.add('d-none');
        
        // Tampilkan modal
        const modal = new bootstrap.Modal(document.getElementById('directCameraModal'));
        modal.show();
        
        // Start camera
        setTimeout(startCamera, 500);
    };
    
    // Start camera
    const startCamera = function() {
        const video = document.getElementById('directCameraView');
        const status = document.getElementById('directCameraStatus');
        
        if (!video || !status) return;
        
        // Tampilkan status
        status.textContent = 'Mengaktifkan kamera...';
        status.classList.remove('d-none');
        
        // Stop stream yang sedang berjalan jika ada
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
        }
        
        // Setup constraints
        const constraints = {
            video: {
                facingMode: facingMode,
                width: { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false
        };
        
        // Request camera access
        navigator.mediaDevices.getUserMedia(constraints)
            .then(function(stream) {
                currentStream = stream;
                video.srcObject = stream;
                
                // Setup event saat video siap
                video.onloadedmetadata = function() {
                    video.play()
                        .then(function() {
                            status.textContent = 'Kamera siap';
                            setTimeout(function() {
                                status.classList.add('d-none');
                            }, 2000);
                        })
                        .catch(function(err) {
                            console.error('Error playing video:', err);
                            status.textContent = 'Error: ' + err.message;
                        });
                };
            })
            .catch(function(err) {
                console.error('Error accessing camera:', err);
                status.textContent = 'Error: ' + err.message;
            });
    };
    
    // Ambil foto
    const capturePhoto = function() {
        const video = document.getElementById('directCameraView');
        const status = document.getElementById('directCameraStatus');
        
        if (!video || !currentStream) {
            alert('Kamera tidak tersedia');
            return;
        }
        
        try {
            // Buat canvas untuk capture
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            // Draw video ke canvas
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Convert ke data URL
            const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
            
            // Simpan foto
            const photoId = 'photo-' + Date.now();
            capturedPhotos.push({
                id: photoId,
                dataUrl: dataUrl
            });
            
            // Update UI
            updatePhotoGallery();
            updateSaveButton();
            
            // Tampilkan feedback
            status.textContent = 'Foto berhasil di-capture!';
            status.classList.remove('d-none');
            setTimeout(function() {
                status.classList.add('d-none');
            }, 2000);
        } catch (err) {
            console.error('Error capturing photo:', err);
            status.textContent = 'Error: ' + err.message;
            status.classList.remove('d-none');
        }
    };
    
    // Mulai rekam video
    const startVideoRecording = function() {
        if (!currentStream) return;
        
        const videoBtn = document.getElementById('directVideoBtn');
        const photoBtn = document.getElementById('directPhotoBtn');
        const stopBtn = document.getElementById('directStopBtn');
        const timer = document.getElementById('directVideoTimer');
        const status = document.getElementById('directCameraStatus');
        
        // Dapatkan audio stream
        navigator.mediaDevices.getUserMedia({ audio: true, video: false })
            .then(function(audioStream) {
                // Gabungkan video dan audio
                const combinedStream = new MediaStream();
                
                // Tambahkan video tracks
                currentStream.getVideoTracks().forEach(track => {
                    combinedStream.addTrack(track);
                });
                
                // Tambahkan audio track
                audioStream.getAudioTracks().forEach(track => {
                    combinedStream.addTrack(track);
                });
                
                // Setup media recorder
                try {
                    mediaRecorder = new MediaRecorder(combinedStream, {
                        mimeType: 'video/webm;codecs=vp8,opus'
                    });
                } catch (e) {
                    console.warn('Codec not supported, using default', e);
                    mediaRecorder = new MediaRecorder(combinedStream);
                }
                
                // Setup event handlers
                mediaRecorder.ondataavailable = function(e) {
                    if (e.data && e.data.size > 0) {
                        recordedChunks.push(e.data);
                    }
                };
                
                mediaRecorder.onstop = function() {
                    // Buat blob dari chunks
                    const blob = new Blob(recordedChunks, { type: 'video/webm' });
                    const videoUrl = URL.createObjectURL(blob);
                    
                    // Simpan video
                    const videoId = 'video-' + Date.now();
                    capturedVideos.push({
                        id: videoId,
                        blob: blob,
                        url: videoUrl
                    });
                    
                    // Update UI
                    updateVideoGallery();
                    updateSaveButton();
                    
                    // Stop audio stream
                    audioStream.getTracks().forEach(track => track.stop());
                    
                    // Tampilkan feedback
                    status.textContent = 'Video berhasil direkam!';
                    status.classList.remove('d-none');
                    setTimeout(function() {
                        status.classList.add('d-none');
                    }, 2000);
                };
                
                // Mulai rekam
                recordedChunks = [];
                mediaRecorder.start(100);
                
                // Update UI
                videoBtn.classList.add('d-none');
                photoBtn.classList.add('d-none');
                stopBtn.classList.remove('d-none');
                
                // Mulai timer
                recordingTime = 0;
                updateRecordingTimer();
                timer.classList.remove('d-none');
                recordingTimer = setInterval(updateRecordingTimer, 1000);
                
                // Tampilkan status
                status.textContent = 'Merekam video...';
                status.classList.remove('d-none');
            })
            .catch(function(err) {
                console.error('Error accessing audio:', err);
                status.textContent = 'Error akses audio: ' + err.message;
                status.classList.remove('d-none');
            });
    };
    
    // Stop video recording
    const stopVideoRecording = function() {
        const videoBtn = document.getElementById('directVideoBtn');
        const photoBtn = document.getElementById('directPhotoBtn');
        const stopBtn = document.getElementById('directStopBtn');
        const timer = document.getElementById('directVideoTimer');
        
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
        }
        
        // Update UI
        videoBtn.classList.remove('d-none');
        photoBtn.classList.remove('d-none');
        stopBtn.classList.add('d-none');
        timer.classList.add('d-none');
        
        // Stop timer
        if (recordingTimer) {
            clearInterval(recordingTimer);
            recordingTimer = null;
        }
    };
    
    // Update recording timer
    const updateRecordingTimer = function() {
        const timer = document.getElementById('directVideoTimer');
        
        if (!timer) return;
        
        const minutes = Math.floor(recordingTime / 60).toString().padStart(2, '0');
        const seconds = (recordingTime % 60).toString().padStart(2, '0');
        timer.textContent = `${minutes}:${seconds}`;
        
        recordingTime++;
    };
    
    // Update photo gallery
    const updatePhotoGallery = function() {
        const gallery = document.getElementById('directPhotoGallery');
        const empty = document.getElementById('directPhotoEmpty');
        const count = document.getElementById('directPhotoCount');
        
        if (!gallery || !empty || !count) return;
        
        // Update counter
        count.textContent = capturedPhotos.length.toString();
        
        // Show/hide empty message
        if (capturedPhotos.length === 0) {
            empty.classList.remove('d-none');
            gallery.innerHTML = '';
            return;
        }
        
        empty.classList.add('d-none');
        gallery.innerHTML = '';
        
        // Generate items
        capturedPhotos.forEach(function(photo) {
            const div = document.createElement('div');
            div.className = 'position-relative';
            div.style.width = '100px';
            div.style.height = '100px';
            div.style.overflow = 'hidden';
            div.style.borderRadius = '4px';
            div.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
            
            div.innerHTML = `
                <img src="${photo.dataUrl}" style="width: 100%; height: 100%; object-fit: cover;">
                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.6); display: flex; justify-content: space-around; padding: 5px;">
                    <button type="button" class="btn-preview-photo" data-id="${photo.id}" style="color: white; border: none; background: none; font-size: 14px; padding: 0;">
                        <i class="ri-eye-line"></i>
                    </button>
                    <button type="button" class="btn-delete-photo" data-id="${photo.id}" style="color: white; border: none; background: none; font-size: 14px; padding: 0;">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            `;
            
            gallery.appendChild(div);
        });
        
        // Attach event handlers
        gallery.querySelectorAll('.btn-preview-photo').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                previewPhoto(id);
            });
        });
        
        gallery.querySelectorAll('.btn-delete-photo').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                deletePhoto(id);
            });
        });
    };
    
    // Update video gallery
    const updateVideoGallery = function() {
        const gallery = document.getElementById('directVideoGallery');
        const empty = document.getElementById('directVideoEmpty');
        const count = document.getElementById('directVideoCount');
        
        if (!gallery || !empty || !count) return;
        
        // Update counter
        count.textContent = capturedVideos.length.toString();
        
        // Show/hide empty message
        if (capturedVideos.length === 0) {
            empty.classList.remove('d-none');
            gallery.innerHTML = '';
            return;
        }
        
        empty.classList.add('d-none');
        gallery.innerHTML = '';
        
        // Generate items
        capturedVideos.forEach(function(video) {
            const div = document.createElement('div');
            div.className = 'position-relative';
            div.style.width = '100px';
            div.style.height = '100px';
            div.style.overflow = 'hidden';
            div.style.borderRadius = '4px';
            div.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
            
            div.innerHTML = `
                <video src="${video.url}" style="width: 100%; height: 100%; object-fit: cover;" muted></video>
                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.6); display: flex; justify-content: space-around; padding: 5px;">
                    <button type="button" class="btn-preview-video" data-id="${video.id}" style="color: white; border: none; background: none; font-size: 14px; padding: 0;">
                        <i class="ri-eye-line"></i>
                    </button>
                    <button type="button" class="btn-delete-video" data-id="${video.id}" style="color: white; border: none; background: none; font-size: 14px; padding: 0;">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            `;
            
            gallery.appendChild(div);
        });
        
        // Attach event handlers
        gallery.querySelectorAll('.btn-preview-video').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                previewVideo(id);
            });
        });
        
        gallery.querySelectorAll('.btn-delete-video').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                deleteVideo(id);
            });
        });
    };
    
    // Preview photo
    const previewPhoto = function(id) {
        const photo = capturedPhotos.find(p => p.id === id);
        if (!photo) return;
        
        const container = document.getElementById('directPreviewContent');
        const modalLabel = document.getElementById('directPreviewModalLabel');
        
        if (!container || !modalLabel) return;
        
        container.innerHTML = `<img src="${photo.dataUrl}" class="img-fluid">`;
        modalLabel.textContent = 'Preview Foto';
        
        const modal = new bootstrap.Modal(document.getElementById('directPreviewModal'));
        modal.show();
    };
    
    // Preview video
    const previewVideo = function(id) {
        const video = capturedVideos.find(v => v.id === id);
        if (!video) return;
        
        const container = document.getElementById('directPreviewContent');
        const modalLabel = document.getElementById('directPreviewModalLabel');
        
        if (!container || !modalLabel) return;
        
        container.innerHTML = `<video src="${video.url}" controls class="img-fluid"></video>`;
        modalLabel.textContent = 'Preview Video';
        
        const modal = new bootstrap.Modal(document.getElementById('directPreviewModal'));
        modal.show();
        
        // Auto play
        const videoElement = container.querySelector('video');
        if (videoElement) {
            videoElement.play().catch(e => console.error('Auto play failed:', e));
        }
    };
    
    // Delete photo
    const deletePhoto = function(id) {
        capturedPhotos = capturedPhotos.filter(p => p.id !== id);
        updatePhotoGallery();
        updateSaveButton();
    };
    
    // Delete video
    const deleteVideo = function(id) {
        const video = capturedVideos.find(v => v.id === id);
        if (video && video.url) {
            URL.revokeObjectURL(video.url);
        }
        
        capturedVideos = capturedVideos.filter(v => v.id !== id);
        updateVideoGallery();
        updateSaveButton();
    };
    
    // Update save button state
    const updateSaveButton = function() {
        const saveBtn = document.getElementById('directSaveBtn');
        if (saveBtn) {
            saveBtn.disabled = capturedPhotos.length === 0 && capturedVideos.length === 0;
        }
    };
    
    // Switch camera
    const switchCamera = function() {
        facingMode = facingMode === 'environment' ? 'user' : 'environment';
        startCamera();
    };
    
    // Save evidence
    const saveEvidence = function() {
        const taskId = document.getElementById('directTaskId').value;
        const notes = document.getElementById('directEvidenceNotes').value;
        const saveBtn = document.getElementById('directSaveBtn');
        
        if (!taskId) {
            alert('Error: Task ID tidak valid');
            return;
        }
        
        if (capturedPhotos.length === 0 && capturedVideos.length === 0) {
            alert('Mohon capture minimal satu foto atau video');
            return;
        }
        
        // Disable save button
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
        }
        
        // Create form data
        const formData = new FormData();
        formData.append('task_id', taskId);
        formData.append('notes', notes || '');
        
        // Process photos
        const photoPromises = capturedPhotos.map((photo, index) => {
            return fetch(photo.dataUrl)
                .then(res => res.blob())
                .then(blob => {
                    const file = new File([blob], `photo_${index}.jpg`, { type: 'image/jpeg' });
                    formData.append(`photos[${index}]`, file);
                });
        });
        
        // Add videos
        capturedVideos.forEach((video, index) => {
            const file = new File([video.blob], `video_${index}.webm`, { type: 'video/webm' });
            formData.append(`videos[${index}]`, file);
        });
        
        // Send data to server
        Promise.all(photoPromises)
            .then(() => {
                return fetch('/maintenance/kanban/upload-evidence', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Evidence berhasil disimpan',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert('Berhasil: ' + (data.message || 'Evidence berhasil disimpan'));
                    }
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('directCameraModal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Clean up resources
                    capturedPhotos = [];
                    
                    capturedVideos.forEach(video => {
                        if (video.url) {
                            URL.revokeObjectURL(video.url);
                        }
                    });
                    capturedVideos = [];
                    
                    // Refresh task list if needed
                    if (typeof updateTaskCards === 'function') {
                        updateTaskCards();
                    }
                } else {
                    throw new Error(data.message || 'Gagal menyimpan evidence');
                }
            })
            .catch(error => {
                console.error('Error saving evidence:', error);
                
                // Show error
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.message || 'Terjadi kesalahan saat menyimpan evidence'
                    });
                } else {
                    alert('Error: ' + (error.message || 'Terjadi kesalahan saat menyimpan evidence'));
                }
            })
            .finally(() => {
                // Reset button
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = 'Simpan Evidence';
                }
            });
    };
    
    // Pasang event handlers
    document.getElementById('directPhotoBtn').addEventListener('click', capturePhoto);
    document.getElementById('directVideoBtn').addEventListener('click', startVideoRecording);
    document.getElementById('directStopBtn').addEventListener('click', stopVideoRecording);
    document.getElementById('directSwitchCameraBtn').addEventListener('click', switchCamera);
    document.getElementById('directSaveBtn').addEventListener('click', saveEvidence);
    
    // Setup buttons
    setupEvidenceButtons();
    
    // Pantau DOM changes untuk mendeteksi tombol baru
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                setupEvidenceButtons();
            }
        });
    });
    
    // Mulai observasi
    observer.observe(document.body, { childList: true, subtree: true });
    
    // Cek buttons secara periodik
    setInterval(setupEvidenceButtons, 3000);
});
</script>
