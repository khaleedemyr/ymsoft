/**
 * evidence-capture.js
 * Script untuk menangani fungsionalitas capture evidence (foto dan video)
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Evidence Capture JS loaded');
    
    // Elements
    const evidenceCaptureModal = document.getElementById('evidenceCaptureModal');
    if (!evidenceCaptureModal) {
        console.error('Modal #evidenceCaptureModal tidak ditemukan!');
        return;
    }
    
    const cameraModal = document.getElementById('cameraModal');
    const previewModal = document.getElementById('previewModal');
    
    // Buttons & Inputs
    const capturePhotoBtn = document.getElementById('capturePhotoBtn');
    const captureVideoBtn = document.getElementById('captureVideoBtn');
    const saveEvidenceBtn = document.getElementById('saveEvidenceBtn');
    const evidenceTaskId = document.getElementById('evidenceTaskId');
    const notesField = document.getElementById('evidenceNotes');
    
    // Camera elements
    const cameraStream = document.getElementById('cameraStream');
    const captureImageBtn = document.getElementById('captureImageBtn');
    const stopRecordingBtn = document.getElementById('stopRecordingBtn');
    const videoTimer = document.getElementById('videoTimer');
    const cameraStatus = document.getElementById('cameraStatus');
    const switchCameraBtn = document.getElementById('switchCameraBtn');
    const closeCameraBtn = document.getElementById('closeCameraBtn');
    const cancelCaptureBtn = document.getElementById('cancelCaptureBtn');
    
    // Counters and preview elements
    const photoCount = document.getElementById('photoCount');
    const videoCount = document.getElementById('videoCount');
    const photoPreview = document.getElementById('photoPreview');
    const videoPreview = document.getElementById('videoPreview');
    const noPhotoMsg = document.getElementById('noPhotoMsg');
    const noVideoMsg = document.getElementById('noVideoMsg');
    
    // Variables
    let stream = null;
    let mediaRecorder = null;
    let recordedChunks = [];
    let timerInterval = null;
    let recordingTime = 0;
    let currentFacingMode = 'environment'; // Default to back camera
    let captureMode = 'photo'; // 'photo' or 'video'
    let photos = []; // Array to store captured photos (base64)
    let videos = []; // Array to store recorded videos (Blob)
    
    // Bootstrap Modals
    const bsEvidenceModal = new bootstrap.Modal(evidenceCaptureModal);
    const bsCameraModal = new bootstrap.Modal(cameraModal);
    const bsPreviewModal = new bootstrap.Modal(previewModal);
    
    // Function to handle task evidence button click
    $(document).on('click', '.new-evidence-btn', function(e) {
        console.log('New evidence button clicked');
        e.preventDefault();
        e.stopPropagation();
        
        const taskId = $(this).data('task-id');
        console.log('Task ID:', taskId);
        evidenceTaskId.value = taskId;
        
        // Reset all state
        clearCaptures();
        resetFormState();
        
        // Show evidence modal
        bsEvidenceModal.show();
    });
    
    // Open camera for photo capture
    capturePhotoBtn.addEventListener('click', function() {
        captureMode = 'photo';
        document.getElementById('cameraModalLabel').textContent = 'Ambil Foto';
        captureImageBtn.classList.remove('d-none');
        stopRecordingBtn.classList.add('d-none');
        videoTimer.classList.add('d-none');
        
        startCamera().then(() => {
            bsCameraModal.show();
        }).catch(error => {
            console.error('Error starting camera:', error);
            showError('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
        });
    });
    
    // Open camera for video recording
    captureVideoBtn.addEventListener('click', function() {
        captureMode = 'video';
        document.getElementById('cameraModalLabel').textContent = 'Rekam Video';
        captureImageBtn.classList.add('d-none');
        stopRecordingBtn.classList.remove('d-none');
        videoTimer.classList.remove('d-none');
        
        startCamera().then(() => {
            bsCameraModal.show();
        }).catch(error => {
            console.error('Error starting camera:', error);
            showError('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
        });
    });
    
    // Start camera stream
    async function startCamera() {
        try {
            cameraStatus.textContent = 'Mengaktifkan kamera...';
            
            const constraints = {
                audio: captureMode === 'video',
                video: {
                    facingMode: currentFacingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };
            
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            cameraStream.srcObject = stream;
            
            if (captureMode === 'video') {
                // Setup media recorder
                mediaRecorder = new MediaRecorder(stream, { mimeType: 'video/webm' });
                
                mediaRecorder.ondataavailable = function(e) {
                    if (e.data.size > 0) {
                        recordedChunks.push(e.data);
                    }
                };
                
                mediaRecorder.onstop = function() {
                    const videoBlob = new Blob(recordedChunks, { type: 'video/webm' });
                    saveVideo(videoBlob);
                };
                
                recordedChunks = [];
                recordingTime = 0;
                updateTimer();
                cameraStatus.textContent = 'Siap merekam';
            } else {
                cameraStatus.textContent = 'Siap mengambil foto';
            }
            
            return true;
        } catch (error) {
            console.error('Error starting camera:', error);
            throw error;
        }
    }
    
    // Capture image
    captureImageBtn.addEventListener('click', function() {
        try {
            const canvas = document.createElement('canvas');
            canvas.width = cameraStream.videoWidth;
            canvas.height = cameraStream.videoHeight;
            
            const context = canvas.getContext('2d');
            context.drawImage(cameraStream, 0, 0, canvas.width, canvas.height);
            
            const imageDataUrl = canvas.toDataURL('image/jpeg');
            savePhoto(imageDataUrl);
            
            // Feedback for user
            cameraStatus.textContent = 'Foto berhasil diambil!';
            setTimeout(() => {
                cameraStatus.textContent = 'Siap mengambil foto';
            }, 2000);
        } catch (error) {
            console.error('Error capturing photo:', error);
            cameraStatus.textContent = 'Gagal mengambil foto';
        }
    });
    
    // Start/stop video recording
    stopRecordingBtn.addEventListener('click', function() {
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
            cameraStatus.textContent = 'Menyimpan video...';
            
            // Stop timer
            clearInterval(timerInterval);
            timerInterval = null;
            
            // Reset UI
            stopRecordingBtn.textContent = 'Rekam Video';
            stopRecordingBtn.classList.remove('btn-danger');
            stopRecordingBtn.classList.add('btn-primary');
            
            setTimeout(() => {
                cameraStatus.textContent = 'Siap merekam';
                videoTimer.textContent = '00:00';
                recordingTime = 0;
            }, 1000);
        } else if (mediaRecorder) {
            // Start recording
            recordedChunks = [];
            mediaRecorder.start(100);
            cameraStatus.textContent = 'Merekam...';
            
            // Start timer
            recordingTime = 0;
            updateTimer();
            timerInterval = setInterval(updateTimer, 1000);
            
            // Update button text
            stopRecordingBtn.textContent = 'Stop Rekam';
            stopRecordingBtn.classList.remove('btn-primary');
            stopRecordingBtn.classList.add('btn-danger');
        }
    });
    
    // Update video timer
    function updateTimer() {
        const minutes = Math.floor(recordingTime / 60).toString().padStart(2, '0');
        const seconds = (recordingTime % 60).toString().padStart(2, '0');
        videoTimer.textContent = `${minutes}:${seconds}`;
        
        if (timerInterval) recordingTime++;
    }
    
    // Switch camera (front/back)
    switchCameraBtn.addEventListener('click', function() {
        currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
        startCamera();
    });
    
    // Close camera
    closeCameraBtn.addEventListener('click', function() {
        stopCamera();
        bsCameraModal.hide();
    });
    
    // Cancel capture
    cancelCaptureBtn.addEventListener('click', function() {
        stopCamera();
        bsCameraModal.hide();
    });
    
    // Stop camera
    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
        }
        
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }
    }
    
    // Save captured photo
    function savePhoto(dataUrl) {
        const id = 'photo-' + Date.now();
        photos.push({
            id: id,
            dataUrl: dataUrl
        });
        
        updatePhotoPreview();
        updateSaveButtonState();
        
        // Auto close camera after capture if needed
        // bsCameraModal.hide();
    }
    
    // Save recorded video
    function saveVideo(blob) {
        const id = 'video-' + Date.now();
        const url = URL.createObjectURL(blob);
        
        videos.push({
            id: id,
            blob: blob,
            url: url
        });
        
        updateVideoPreview();
        updateSaveButtonState();
        
        // Auto close camera after recording
        bsCameraModal.hide();
    }
    
    // Update photo preview
    function updatePhotoPreview() {
        photoCount.textContent = photos.length;
        
        if (photos.length > 0) {
            noPhotoMsg.classList.add('d-none');
            photoPreview.innerHTML = '';
            
            photos.forEach(photo => {
                const div = document.createElement('div');
                div.className = 'media-preview';
                div.innerHTML = `
                    <img src="${photo.dataUrl}" alt="Captured photo">
                    <div class="media-actions">
                        <button type="button" class="preview-media" data-id="${photo.id}">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button type="button" class="delete-media" data-id="${photo.id}">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                `;
                photoPreview.appendChild(div);
            });
        } else {
            noPhotoMsg.classList.remove('d-none');
            photoPreview.innerHTML = '';
        }
    }
    
    // Update video preview
    function updateVideoPreview() {
        videoCount.textContent = videos.length;
        
        if (videos.length > 0) {
            noVideoMsg.classList.add('d-none');
            videoPreview.innerHTML = '';
            
            videos.forEach(video => {
                const div = document.createElement('div');
                div.className = 'media-preview';
                div.innerHTML = `
                    <video src="${video.url}" muted></video>
                    <div class="media-actions">
                        <button type="button" class="preview-media" data-id="${video.id}">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button type="button" class="delete-media" data-id="${video.id}">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                `;
                videoPreview.appendChild(div);
            });
        } else {
            noVideoMsg.classList.remove('d-none');
            videoPreview.innerHTML = '';
        }
    }
    
    // Preview media (photo/video)
    $(document).on('click', '.preview-media', function() {
        const id = $(this).data('id');
        const previewContent = document.getElementById('previewContent');
        
        if (id.startsWith('photo-')) {
            const photo = photos.find(p => p.id === id);
            if (photo) {
                previewContent.innerHTML = `<img src="${photo.dataUrl}" class="img-fluid" alt="Preview">`;
                previewModalLabel.textContent = 'Preview Foto';
                bsPreviewModal.show();
            }
        } else if (id.startsWith('video-')) {
            const video = videos.find(v => v.id === id);
            if (video) {
                previewContent.innerHTML = `
                    <video src="${video.url}" controls class="img-fluid"></video>
                `;
                previewModalLabel.textContent = 'Preview Video';
                bsPreviewModal.show();
                
                // Auto play
                const videoElement = previewContent.querySelector('video');
                if (videoElement) videoElement.play();
            }
        }
    });
    
    // Delete media (photo/video)
    $(document).on('click', '.delete-media', function() {
        const id = $(this).data('id');
        
        if (id.startsWith('photo-')) {
            photos = photos.filter(p => p.id !== id);
            updatePhotoPreview();
        } else if (id.startsWith('video-')) {
            const video = videos.find(v => v.id === id);
            if (video && video.url) {
                URL.revokeObjectURL(video.url);
            }
            videos = videos.filter(v => v.id !== id);
            updateVideoPreview();
        }
        
        updateSaveButtonState();
    });
    
    // Update save button state
    function updateSaveButtonState() {
        saveEvidenceBtn.disabled = photos.length === 0 && videos.length === 0;
    }
    
    // Reset form state
    function resetFormState() {
        notesField.value = '';
        evidenceTaskId.value = '';
        updateSaveButtonState();
    }
    
    // Clear captures
    function clearCaptures() {
        // Clean up video URLs
        videos.forEach(video => {
            if (video.url) URL.revokeObjectURL(video.url);
        });
        
        photos = [];
        videos = [];
        updatePhotoPreview();
        updateVideoPreview();
    }
    
    // Save evidence
    saveEvidenceBtn.addEventListener('click', function() {
        if (photos.length === 0 && videos.length === 0) {
            showError('Tidak ada foto atau video yang diupload.');
            return;
        }
        
        saveEvidenceBtn.disabled = true;
        saveEvidenceBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';
        
        const formData = new FormData();
        formData.append('task_id', evidenceTaskId.value);
        formData.append('notes', notesField.value);
        
        // Handle photos
        const photoPromises = photos.map((photo, index) => {
            return fetch(photo.dataUrl)
                .then(res => res.blob())
                .then(blob => {
                    const file = new File([blob], `photo_${index}.jpg`, { type: 'image/jpeg' });
                    formData.append(`photos[${index}]`, file);
                });
        });
        
        // Handle videos
        videos.forEach((video, index) => {
            const file = new File([video.blob], `video_${index}.webm`, { type: 'video/webm' });
            formData.append(`videos[${index}]`, file);
        });
        
        // Wait for all blobs to be processed
        Promise.all(photoPromises)
            .then(() => {
                // Send data to server
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
                    // Success
                    showSuccess(data.message);
                    bsEvidenceModal.hide();
                    clearCaptures();
                    resetFormState();
                    
                    // Refresh task if needed
                    if (typeof refreshTask === 'function') {
                        refreshTask(evidenceTaskId.value);
                    }
                } else {
                    // Error
                    showError(data.message);
                }
            })
            .catch(error => {
                console.error('Error saving evidence:', error);
                showError('Terjadi kesalahan saat menyimpan evidence. Silakan coba lagi.');
            })
            .finally(() => {
                saveEvidenceBtn.disabled = false;
                saveEvidenceBtn.innerHTML = 'Simpan Evidence';
            });
    });
    
    // Show success message
    function showSuccess(message) {
        Swal.fire({
            title: 'Berhasil!',
            text: message,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    }
    
    // Show error message
    function showError(message) {
        Swal.fire({
            title: 'Error!',
            text: message,
            icon: 'error'
        });
    }
    
    // Clean up on modal hide
    evidenceCaptureModal.addEventListener('hidden.bs.modal', function() {
        resetFormState();
    });
    
    cameraModal.addEventListener('hidden.bs.modal', function() {
        stopCamera();
    });
});
