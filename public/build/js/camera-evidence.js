/**
 * Camera Evidence Capture Module
 * Untuk menangani pengambilan foto dan video langsung dari kamera
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Camera Evidence JS loaded');
    
    // Elemen modal dan form
    const cameraEvidenceModal = document.getElementById('cameraEvidenceModal');
    const captureModal = document.getElementById('captureModal');
    const cameraEvidenceTaskId = document.getElementById('cameraEvidenceTaskId');
    const cameraEvidenceNotes = document.getElementById('cameraEvidenceNotes');
    
    // Tombol dan controls
    const takeCameraPhotoBtn = document.getElementById('takeCameraPhotoBtn');
    const recordCameraVideoBtn = document.getElementById('recordCameraVideoBtn');
    const saveCameraEvidenceBtn = document.getElementById('saveCameraEvidenceBtn');
    const takePhotoBtn = document.getElementById('takePhotoBtn');
    const recordVideoBtn = document.getElementById('recordVideoBtn');
    const switchCaptureCameraBtn = document.getElementById('switchCaptureCameraBtn');
    const closeCaptureCameraBtn = document.getElementById('closeCaptureCameraBtn');
    const cancelCaptureBtn = document.getElementById('cancelCaptureBtn');
    
    // Elemen preview dan status
    const photoCountLabel = document.getElementById('photoCountLabel');
    const videoCountLabel = document.getElementById('videoCountLabel');
    const photoPreviewArea = document.getElementById('photoPreviewArea');
    const videoPreviewArea = document.getElementById('videoPreviewArea');
    const noPhotosMessage = document.getElementById('noPhotosMessage');
    const noVideosMessage = document.getElementById('noVideosMessage');
    const captureStream = document.getElementById('captureStream');
    const captureTimerDisplay = document.getElementById('captureTimerDisplay');
    const captureStatusDisplay = document.getElementById('captureStatusDisplay');
    const recordBtnText = document.getElementById('recordBtnText');
    
    // State variables
    let cameraStream = null;
    let mediaRecorder = null;
    let recordedChunks = [];
    let recordingTimer = null;
    let recordingTime = 0;
    let facingMode = 'environment'; // Default ke kamera belakang
    let captureMode = 'photo'; // 'photo' atau 'video'
    let capturedPhotos = [];
    let capturedVideos = [];
    
    // Bootstrap Modals
    let bsCameraEvidenceModal;
    let bsCaptureModal;
    
    if (cameraEvidenceModal) {
        bsCameraEvidenceModal = new bootstrap.Modal(cameraEvidenceModal);
    }
    
    if (captureModal) {
        bsCaptureModal = new bootstrap.Modal(captureModal);
    }
    
    // Pasang event listener untuk tombol evidence
    document.addEventListener('click', function(e) {
        const evidenceBtn = e.target.closest('.new-evidence-btn');
        if (!evidenceBtn) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        const taskId = evidenceBtn.dataset.taskId;
        console.log('Evidence button clicked for task ID:', taskId);
        
        if (cameraEvidenceTaskId && bsCameraEvidenceModal) {
            cameraEvidenceTaskId.value = taskId;
            resetEvidenceForm();
            bsCameraEvidenceModal.show();
        } else {
            console.error('Camera evidence modal or task ID element not found');
        }
    });
    
    // Tombol ambil foto
    if (takeCameraPhotoBtn) {
        takeCameraPhotoBtn.addEventListener('click', function() {
            captureMode = 'photo';
            prepareCamera('photo');
        });
    }
    
    // Tombol rekam video
    if (recordCameraVideoBtn) {
        recordCameraVideoBtn.addEventListener('click', function() {
            captureMode = 'video';
            prepareCamera('video');
        });
    }
    
    // Siapkan kamera
    function prepareCamera(mode) {
        if (!bsCaptureModal) {
            console.error('Capture modal not found');
            return;
        }
        
        captureMode = mode;
        
        // Setup UI
        if (mode === 'photo') {
            document.getElementById('captureModalLabel').textContent = 'Ambil Foto';
            takePhotoBtn.classList.remove('d-none');
            recordVideoBtn.classList.add('d-none');
            captureTimerDisplay.classList.add('d-none');
        } else {
            document.getElementById('captureModalLabel').textContent = 'Rekam Video';
            takePhotoBtn.classList.add('d-none');
            recordVideoBtn.classList.remove('d-none');
            recordBtnText.textContent = 'Mulai Rekam';
            recordVideoBtn.classList.remove('btn-warning');
            recordVideoBtn.classList.add('btn-danger');
            captureTimerDisplay.classList.add('d-none');
        }
        
        // Reset status
        captureStatusDisplay.textContent = 'Mempersiapkan kamera...';
        
        // Tampilkan modal
        bsCaptureModal.show();
        
        // Mulai kamera setelah modal ditampilkan
        setTimeout(() => {
            startCamera()
                .then(() => {
                    captureStatusDisplay.textContent = mode === 'photo' 
                        ? 'Siap mengambil foto' 
                        : 'Siap merekam video';
                })
                .catch(error => {
                    console.error('Failed to start camera:', error);
                    captureStatusDisplay.textContent = 'Gagal mengakses kamera';
                    
                    // Tampilkan error ke user
                    Swal.fire({
                        title: 'Error',
                        text: 'Tidak dapat mengakses kamera. Pastikan Anda memberikan izin kamera dan browser Anda mendukung fitur ini.',
                        icon: 'error'
                    });
                });
        }, 500);
    }
    
    // Mulai kamera
    async function startCamera() {
        try {
            // Stop stream yang sudah ada jika ada
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
            
            // Siapkan constraint untuk kamera
            const constraints = {
                audio: captureMode === 'video',
                video: {
                    facingMode: facingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };
            
            // Minta akses kamera
            cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
            
            // Tampilkan stream di video element
            if (captureStream) {
                captureStream.srcObject = cameraStream;
                await captureStream.play();
            }
            
            // Setup video recorder jika mode video
            if (captureMode === 'video') {
                setupVideoRecorder();
            }
            
            return true;
        } catch (error) {
            console.error('Error accessing camera:', error);
            throw error;
        }
    }
    
    // Setup video recorder
    function setupVideoRecorder() {
        if (!cameraStream) return;
        
        try {
            // Gunakan WebM untuk kompatibilitas yang lebih baik
            mediaRecorder = new MediaRecorder(cameraStream, { mimeType: 'video/webm' });
            
            mediaRecorder.ondataavailable = function(e) {
                if (e.data && e.data.size > 0) {
                    recordedChunks.push(e.data);
                }
            };
            
            mediaRecorder.onstop = function() {
                const videoBlob = new Blob(recordedChunks, { type: 'video/webm' });
                saveRecordedVideo(videoBlob);
            };
            
            recordedChunks = [];
        } catch (error) {
            console.error('Error setting up media recorder:', error);
            captureStatusDisplay.textContent = 'Gagal menyiapkan perekam video';
        }
    }
    
    // Ambil foto
    if (takePhotoBtn) {
        takePhotoBtn.addEventListener('click', function() {
            if (!captureStream || !captureStream.videoWidth) {
                console.error('Video stream not ready');
                return;
            }
            
            try {
                // Buat canvas untuk capture gambar dari video
                const canvas = document.createElement('canvas');
                canvas.width = captureStream.videoWidth;
                canvas.height = captureStream.videoHeight;
                
                // Gambar frame dari video ke canvas
                const context = canvas.getContext('2d');
                context.drawImage(captureStream, 0, 0, canvas.width, canvas.height);
                
                // Konversi ke data URL (JPEG)
                const imageDataUrl = canvas.toDataURL('image/jpeg', 0.9);
                
                // Simpan foto
                saveCapturedPhoto(imageDataUrl);
                
                // Feedback
                captureStatusDisplay.textContent = 'Foto berhasil diambil!';
                setTimeout(() => {
                    captureStatusDisplay.textContent = 'Siap mengambil foto';
                }, 1500);
                
            } catch (error) {
                console.error('Error capturing photo:', error);
                captureStatusDisplay.textContent = 'Gagal mengambil foto';
            }
        });
    }
    
    // Rekam video
    if (recordVideoBtn) {
        recordVideoBtn.addEventListener('click', function() {
            if (!mediaRecorder) {
                console.error('Media recorder not initialized');
                return;
            }
            
            if (mediaRecorder.state === 'recording') {
                // Stop recording
                mediaRecorder.stop();
                
                // Stop timer
                clearInterval(recordingTimer);
                recordingTimer = null;
                
                // Update UI
                recordBtnText.textContent = 'Mulai Rekam';
                recordVideoBtn.classList.remove('btn-warning');
                recordVideoBtn.classList.add('btn-danger');
                captureTimerDisplay.classList.add('d-none');
                captureStatusDisplay.textContent = 'Menyimpan video...';
                
            } else {
                // Start recording
                recordedChunks = [];
                mediaRecorder.start(100);
                
                // Start timer
                recordingTime = 0;
                updateRecordingTimer();
                recordingTimer = setInterval(updateRecordingTimer, 1000);
                
                // Update UI
                recordBtnText.textContent = 'Stop';
                recordVideoBtn.classList.remove('btn-danger');
                recordVideoBtn.classList.add('btn-warning');
                captureTimerDisplay.classList.remove('d-none');
                captureStatusDisplay.textContent = 'Merekam...';
            }
        });
    }
    
    // Update recording timer
    function updateRecordingTimer() {
        recordingTime++;
        const minutes = Math.floor(recordingTime / 60).toString().padStart(2, '0');
        const seconds = (recordingTime % 60).toString().padStart(2, '0');
        captureTimerDisplay.textContent = `${minutes}:${seconds}`;
    }
    
    // Switch camera
    if (switchCaptureCameraBtn) {
        switchCaptureCameraBtn.addEventListener('click', function() {
            facingMode = facingMode === 'environment' ? 'user' : 'environment';
            startCamera();
        });
    }
    
    // Close and cancel buttons
    if (closeCaptureCameraBtn) {
        closeCaptureCameraBtn.addEventListener('click', stopCameraAndHideModal);
    }
    
    if (cancelCaptureBtn) {
        cancelCaptureBtn.addEventListener('click', stopCameraAndHideModal);
    }
    
    // Stop camera and hide modal
    function stopCameraAndHideModal() {
        stopCamera();
        if (bsCaptureModal) {
            bsCaptureModal.hide();
        }
    }
    
    // Stop camera
    function stopCamera() {
        // Stop recording if active
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
        }
        
        // Stop timer if active
        if (recordingTimer) {
            clearInterval(recordingTimer);
            recordingTimer = null;
        }
        
        // Stop all tracks
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
        
        // Reset video element
        if (captureStream) {
            captureStream.srcObject = null;
        }
        
        mediaRecorder = null;
    }
    
    // Save captured photo
    function saveCapturedPhoto(dataUrl) {
        const photoId = 'photo-' + Date.now();
        capturedPhotos.push({
            id: photoId,
            dataUrl: dataUrl
        });
        
        // Update UI
        updatePhotoPreview();
        updateSaveButtonState();
    }
    
    // Save recorded video
    function saveRecordedVideo(blob) {
        const videoId = 'video-' + Date.now();
        const videoUrl = URL.createObjectURL(blob);
        
        capturedVideos.push({
            id: videoId,
            blob: blob,
            url: videoUrl
        });
        
        // Update UI
        updateVideoPreview();
        updateSaveButtonState();
        
        // Auto hide capture modal
        setTimeout(() => {
            if (bsCaptureModal) {
                bsCaptureModal.hide();
            }
        }, 1000);
    }
    
    // Update photo preview
    function updatePhotoPreview() {
        if (!photoPreviewArea || !photoCountLabel || !noPhotosMessage) return;
        
        // Update counter
        photoCountLabel.textContent = capturedPhotos.length;
        
        if (capturedPhotos.length > 0) {
            // Hide "no photos" message
            noPhotosMessage.classList.add('d-none');
            
            // Clear and rebuild preview
            photoPreviewArea.innerHTML = '';
            
            // Add previews
            capturedPhotos.forEach((photo, index) => {
                const previewItem = document.createElement('div');
                previewItem.className = 'capture-preview-item';
                previewItem.innerHTML = `
                    <img src="${photo.dataUrl}" alt="Captured Photo">
                    <div class="preview-actions">
                        <button type="button" class="view-media" data-type="photo" data-id="${photo.id}">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button type="button" class="delete-media" data-type="photo" data-id="${photo.id}">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                `;
                photoPreviewArea.appendChild(previewItem);
            });
        } else {
            // Show "no photos" message
            noPhotosMessage.classList.remove('d-none');
            photoPreviewArea.innerHTML = '';
        }
    }
    
    // Update video preview
    function updateVideoPreview() {
        if (!videoPreviewArea || !videoCountLabel || !noVideosMessage) return;
        
        // Update counter
        videoCountLabel.textContent = capturedVideos.length;
        
        if (capturedVideos.length > 0) {
            // Hide "no videos" message
            noVideosMessage.classList.add('d-none');
            
            // Clear and rebuild preview
            videoPreviewArea.innerHTML = '';
            
            // Add previews
            capturedVideos.forEach((video, index) => {
                const previewItem = document.createElement('div');
                previewItem.className = 'capture-preview-item';
                previewItem.innerHTML = `
                    <video src="${video.url}" muted></video>
                    <div class="preview-actions">
                        <button type="button" class="view-media" data-type="video" data-id="${video.id}">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button type="button" class="delete-media" data-type="video" data-id="${video.id}">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                `;
                videoPreviewArea.appendChild(previewItem);
            });
        } else {
            // Show "no videos" message
            noVideosMessage.classList.remove('d-none');
            videoPreviewArea.innerHTML = '';
        }
    }
    
    // Update save button state
    function updateSaveButtonState() {
        if (!saveCameraEvidenceBtn) return;
        
        saveCameraEvidenceBtn.disabled = capturedPhotos.length === 0 && capturedVideos.length === 0;
    }
    
    // View and delete media
    document.addEventListener('click', function(e) {
        // Handle view media
        const viewBtn = e.target.closest('.view-media');
        if (viewBtn) {
            const mediaType = viewBtn.dataset.type;
            const mediaId = viewBtn.dataset.id;
            
            if (mediaType === 'photo') {
                const photo = capturedPhotos.find(p => p.id === mediaId);
                if (photo) {
                    Swal.fire({
                        title: 'Preview Foto',
                        imageUrl: photo.dataUrl,
                        imageAlt: 'Preview foto',
                        confirmButtonText: 'Tutup'
                    });
                }
            } else if (mediaType === 'video') {
                const video = capturedVideos.find(v => v.id === mediaId);
                if (video) {
                    Swal.fire({
                        title: 'Preview Video',
                        html: `<video src="${video.url}" controls class="img-fluid"></video>`,
                        width: 800,
                        confirmButtonText: 'Tutup',
                        didOpen: (popup) => {
                            const videoElement = popup.querySelector('video');
                            if (videoElement) {
                                videoElement.play();
                            }
                        }
                    });
                }
            }
        }
        
        // Handle delete media
        const deleteBtn = e.target.closest('.delete-media');
        if (deleteBtn) {
            const mediaType = deleteBtn.dataset.type;
            const mediaId = deleteBtn.dataset.id;
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus ${mediaType === 'photo' ? 'foto' : 'video'} ini?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (mediaType === 'photo') {
                        capturedPhotos = capturedPhotos.filter(p => p.id !== mediaId);
                        updatePhotoPreview();
                    } else if (mediaType === 'video') {
                        const video = capturedVideos.find(v => v.id === mediaId);
                        if (video && video.url) {
                            URL.revokeObjectURL(video.url);
                        }
                        capturedVideos = capturedVideos.filter(v => v.id !== mediaId);
                        updateVideoPreview();
                    }
                    
                    updateSaveButtonState();
                }
            });
        }
    });
    
    // Save evidence
    if (saveCameraEvidenceBtn) {
        saveCameraEvidenceBtn.addEventListener('click', function() {
            if (!cameraEvidenceTaskId) {
                console.error('Task ID element not found');
                return;
            }
            
            const taskId = cameraEvidenceTaskId.value;
            if (!taskId) {
                console.error('Task ID is empty');
                return;
            }
            
            if (capturedPhotos.length === 0 && capturedVideos.length === 0) {
                Swal.fire({
                    title: 'Error',
                    text: 'Tidak ada foto atau video yang diambil.',
                    icon: 'error'
                });
                return;
            }
            
            // Disable save button and show loading state
            saveCameraEvidenceBtn.disabled = true;
            saveCameraEvidenceBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';
            
            // Prepare form data
            const formData = new FormData();
            formData.append('task_id', taskId);
            formData.append('notes', cameraEvidenceNotes ? cameraEvidenceNotes.value : '');
            
            // Process photos
            const photoPromises = capturedPhotos.map((photo, index) => {
                return fetch(photo.dataUrl)
                    .then(res => res.blob())
                    .then(blob => {
                        const file = new File([blob], `photo_${index}.jpg`, { type: 'image/jpeg' });
                        formData.append(`photos[${index}]`, file);
                    });
            });
            
            // Process videos
            capturedVideos.forEach((video, index) => {
                const file = new File([video.blob], `video_${index}.webm`, { type: 'video/webm' });
                formData.append(`videos[${index}]`, file);
            });
            
            // Submit after all blobs are processed
            Promise.all(photoPromises)
                .then(() => {
                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    
                    // Submit to server
                    return fetch('/maintenance/kanban/upload-evidence', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Success
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Reset state
                        capturedPhotos = [];
                        capturedVideos = [];
                        updatePhotoPreview();
                        updateVideoPreview();
                        updateSaveButtonState();
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan saat menyimpan evidence');
                    }
                })
                .catch(error => {
                    console.error('Error saving evidence:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.message || 'Terjadi kesalahan saat menyimpan evidence'
                    });
                })
                .finally(() => {
                    elements.saveCameraEvidenceBtn.disabled = false;
                    elements.saveCameraEvidenceBtn.innerHTML = 'Simpan Evidence';
                });
        });
    }
});
