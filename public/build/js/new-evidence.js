/**
 * new-evidence.js
 * Script untuk menangani fungsionalitas evidence yang baru
 */

(function() {
    console.log('New Evidence JS loaded');
    
    // Simpan referensi ke semua element yang kita butuhkan
    const elements = {
        // Modals
        evidenceModal: document.getElementById('newEvidenceModal'),
        cameraModal: document.getElementById('newCameraModal'),
        previewModal: document.getElementById('newPreviewModal'),
        
        // Form elements
        evidenceForm: document.getElementById('newEvidenceForm'),
        taskIdInput: document.getElementById('newEvidenceTaskId'),
        notesInput: document.getElementById('newEvidenceNotes'),
        
        // Buttons
        capturePhotoBtn: document.getElementById('newCapturePhotoBtn'),
        captureVideoBtn: document.getElementById('newCaptureVideoBtn'),
        saveEvidenceBtn: document.getElementById('newSaveEvidenceBtn'),
        captureImageBtn: document.getElementById('newCaptureImageBtn'),
        stopRecordingBtn: document.getElementById('newStopRecordingBtn'),
        switchCameraBtn: document.getElementById('newSwitchCameraBtn'),
        closeCameraBtn: document.getElementById('newCloseCameraBtn'),
        cancelCaptureBtn: document.getElementById('newCancelCaptureBtn'),
        
        // Camera elements
        cameraStream: document.getElementById('newCameraStream'),
        videoTimer: document.getElementById('newVideoTimer'),
        cameraStatus: document.getElementById('newCameraStatus'),
        
        // Preview elements
        photoCount: document.getElementById('newPhotoCount'),
        videoCount: document.getElementById('newVideoCount'),
        photoPreview: document.getElementById('newPhotoPreview'),
        videoPreview: document.getElementById('newVideoPreview'),
        noPhotoMsg: document.getElementById('newNoPhotoMsg'),
        noVideoMsg: document.getElementById('newNoVideoMsg'),
        previewContent: document.getElementById('newPreviewContent')
    };
    
    // State variables
    let state = {
        stream: null,
        mediaRecorder: null,
        recordedChunks: [],
        timerInterval: null,
        recordingTime: 0,
        facingMode: 'environment', // Default ke kamera belakang
        captureMode: 'photo',
        photos: [], // Array untuk menyimpan foto yang di-capture (base64)
        videos: [] // Array untuk menyimpan video yang direkam (Blob)
    };
    
    // Bootstrap modals
    let modals = {};
    
    // Inisialisasi
    function init() {
        // Pastikan semua element yang dibutuhkan tersedia
        if (!validateElements()) {
            console.error('Some required elements are missing. Evidence functionality will not work.');
            return;
        }
        
        // Inisialisasi Bootstrap modals
        modals.evidenceModal = new bootstrap.Modal(elements.evidenceModal);
        modals.cameraModal = new bootstrap.Modal(elements.cameraModal);
        modals.previewModal = new bootstrap.Modal(elements.previewModal);
        
        // Pasang event listeners
        setupEventListeners();
        
        console.log('New Evidence initialized successfully');
    }
    
    // Validasi keberadaan element yang dibutuhkan
    function validateElements() {
        for (const key in elements) {
            if (!elements[key]) {
                console.error(`Required element not found: ${key}`);
                return false;
            }
        }
        return true;
    }
    
    // Setup event listeners
    function setupEventListeners() {
        // Click event untuk tombol evidence di task card
        document.addEventListener('click', function(e) {
            // Cari tombol dengan class .new-evidence-btn
            const evidenceBtn = e.target.closest('.new-evidence-btn');
            if (evidenceBtn) {
                e.preventDefault();
                e.stopPropagation();
                
                const taskId = evidenceBtn.dataset.taskId;
                console.log('Evidence button clicked for task ID:', taskId);
                
                // Set task ID ke form dan reset state
                elements.taskIdInput.value = taskId;
                resetState();
                
                // Tampilkan modal
                modals.evidenceModal.show();
            }
        });
        
        // Click event untuk tombol ambil foto
        elements.capturePhotoBtn.addEventListener('click', function() {
            state.captureMode = 'photo';
            document.getElementById('newCameraModalLabel').textContent = 'Ambil Foto';
            elements.captureImageBtn.classList.remove('d-none');
            elements.stopRecordingBtn.classList.add('d-none');
            elements.videoTimer.classList.add('d-none');
            
            startCamera().then(() => {
                modals.cameraModal.show();
            }).catch(error => {
                console.error('Error starting camera:', error);
                showError('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses.');
            });
        });
        
        // Click event untuk tombol rekam video
        elements.captureVideoBtn.addEventListener('click', function() {
            state.captureMode = 'video';
            document.getElementById('newCameraModalLabel').textContent = 'Rekam Video';
            elements.captureImageBtn.classList.add('d-none');
            elements.stopRecordingBtn.classList.remove('d-none');
            elements.videoTimer.classList.remove('d-none');
            
            startCamera().then(() => {
                modals.cameraModal.show();
            }).catch(error => {
                console.error('Error starting camera:', error);
                showError('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses.');
            });
        });
        
        // Capture image button
        elements.captureImageBtn.addEventListener('click', capturePhoto);
        
        // Stop recording button
        elements.stopRecordingBtn.addEventListener('click', toggleRecording);
        
        // Switch camera button
        elements.switchCameraBtn.addEventListener('click', switchCamera);
        
        // Close and cancel camera buttons
        elements.closeCameraBtn.addEventListener('click', function() {
            stopCamera();
            modals.cameraModal.hide();
        });
        
        elements.cancelCaptureBtn.addEventListener('click', function() {
            stopCamera();
            modals.cameraModal.hide();
        });
        
        // Save evidence button
        elements.saveEvidenceBtn.addEventListener('click', saveEvidence);
        
        // Modal hide events untuk cleanup
        elements.evidenceModal.addEventListener('hidden.bs.modal', resetForm);
        elements.cameraModal.addEventListener('hidden.bs.modal', stopCamera);
        
        // Preview dan delete media
        document.addEventListener('click', function(e) {
            // Preview media
            if (e.target.closest('.preview-media')) {
                const previewBtn = e.target.closest('.preview-media');
                const id = previewBtn.dataset.id;
                previewMedia(id);
            }
            
            // Delete media
            if (e.target.closest('.delete-media')) {
                const deleteBtn = e.target.closest('.delete-media');
                const id = deleteBtn.dataset.id;
                deleteMedia(id);
            }
        });
    }
    
    // Start camera
    async function startCamera() {
        try {
            elements.cameraStatus.textContent = 'Mengaktifkan kamera...';
            
            const constraints = {
                audio: state.captureMode === 'video',
                video: {
                    facingMode: state.facingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };
            
            // Stop current stream if exists
            if (state.stream) {
                state.stream.getTracks().forEach(track => track.stop());
            }
            
            state.stream = await navigator.mediaDevices.getUserMedia(constraints);
            elements.cameraStream.srcObject = state.stream;
            
            if (state.captureMode === 'video') {
                // Setup media recorder
                state.mediaRecorder = new MediaRecorder(state.stream, { mimeType: 'video/webm' });
                
                state.mediaRecorder.ondataavailable = function(e) {
                    if (e.data.size > 0) {
                        state.recordedChunks.push(e.data);
                    }
                };
                
                state.mediaRecorder.onstop = function() {
                    const videoBlob = new Blob(state.recordedChunks, { type: 'video/webm' });
                    saveVideo(videoBlob);
                };
                
                state.recordedChunks = [];
                state.recordingTime = 0;
                updateTimer();
                elements.cameraStatus.textContent = 'Siap merekam';
            } else {
                elements.cameraStatus.textContent = 'Siap mengambil foto';
            }
            
            return true;
        } catch (error) {
            console.error('Error starting camera:', error);
            throw error;
        }
    }
    
    // Capture photo
    function capturePhoto() {
        try {
            const canvas = document.createElement('canvas');
            canvas.width = elements.cameraStream.videoWidth;
            canvas.height = elements.cameraStream.videoHeight;
            
            const context = canvas.getContext('2d');
            context.drawImage(elements.cameraStream, 0, 0, canvas.width, canvas.height);
            
            const imageDataUrl = canvas.toDataURL('image/jpeg');
            savePhoto(imageDataUrl);
            
            // Feedback untuk user
            elements.cameraStatus.textContent = 'Foto berhasil diambil!';
            setTimeout(() => {
                elements.cameraStatus.textContent = 'Siap mengambil foto';
            }, 2000);
        } catch (error) {
            console.error('Error capturing photo:', error);
            elements.cameraStatus.textContent = 'Gagal mengambil foto';
        }
    }
    
    // Toggle video recording
    function toggleRecording() {
        if (state.mediaRecorder && state.mediaRecorder.state === 'recording') {
            // Stop recording
            state.mediaRecorder.stop();
            elements.cameraStatus.textContent = 'Menyimpan video...';
            
            // Stop timer
            clearInterval(state.timerInterval);
            state.timerInterval = null;
            
            // Reset UI
            elements.stopRecordingBtn.innerHTML = '<i class="ri-video-line me-1"></i> Rekam Video';
            elements.stopRecordingBtn.classList.remove('btn-danger');
            elements.stopRecordingBtn.classList.add('btn-success');
            
            setTimeout(() => {
                elements.cameraStatus.textContent = 'Siap merekam';
                elements.videoTimer.textContent = '00:00';
                state.recordingTime = 0;
            }, 1000);
        } else if (state.mediaRecorder) {
            // Start recording
            state.recordedChunks = [];
            state.mediaRecorder.start(100);
            elements.cameraStatus.textContent = 'Merekam...';
            
            // Start timer
            state.recordingTime = 0;
            updateTimer();
            state.timerInterval = setInterval(updateTimer, 1000);
            
            // Update button text
            elements.stopRecordingBtn.innerHTML = '<i class="ri-stop-circle-line me-1"></i> Stop Rekam';
            elements.stopRecordingBtn.classList.remove('btn-success');
            elements.stopRecordingBtn.classList.add('btn-danger');
        }
    }
    
    // Update video timer
    function updateTimer() {
        const minutes = Math.floor(state.recordingTime / 60).toString().padStart(2, '0');
        const seconds = (state.recordingTime % 60).toString().padStart(2, '0');
        elements.videoTimer.textContent = `${minutes}:${seconds}`;
        
        if (state.timerInterval) state.recordingTime++;
    }
    
    // Switch camera
    function switchCamera() {
        state.facingMode = state.facingMode === 'environment' ? 'user' : 'environment';
        startCamera();
    }
    
    // Stop camera
    function stopCamera() {
        if (state.stream) {
            state.stream.getTracks().forEach(track => track.stop());
            state.stream = null;
        }
        
        if (state.mediaRecorder && state.mediaRecorder.state === 'recording') {
            state.mediaRecorder.stop();
        }
        
        if (state.timerInterval) {
            clearInterval(state.timerInterval);
            state.timerInterval = null;
        }
    }
    
    // Save captured photo
    function savePhoto(dataUrl) {
        const id = 'photo-' + Date.now();
        state.photos.push({
            id: id,
            dataUrl: dataUrl
        });
        
        updatePhotoPreview();
        updateSaveButtonState();
    }
    
    // Save recorded video
    function saveVideo(blob) {
        const id = 'video-' + Date.now();
        const url = URL.createObjectURL(blob);
        
        state.videos.push({
            id: id,
            blob: blob,
            url: url
        });
        
        updateVideoPreview();
        updateSaveButtonState();
        
        // Auto close camera after recording
        modals.cameraModal.hide();
    }
    
    // Update photo preview
    function updatePhotoPreview() {
        elements.photoCount.textContent = state.photos.length;
        
        if (state.photos.length > 0) {
            elements.noPhotoMsg.classList.add('d-none');
            elements.photoPreview.innerHTML = '';
            
            state.photos.forEach(photo => {
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
                elements.photoPreview.appendChild(div);
            });
        } else {
            elements.noPhotoMsg.classList.remove('d-none');
            elements.photoPreview.innerHTML = '';
        }
    }
    
    // Update video preview
    function updateVideoPreview() {
        elements.videoCount.textContent = state.videos.length;
        
        if (state.videos.length > 0) {
            elements.noVideoMsg.classList.add('d-none');
            elements.videoPreview.innerHTML = '';
            
            state.videos.forEach(video => {
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
                elements.videoPreview.appendChild(div);
            });
        } else {
            elements.noVideoMsg.classList.remove('d-none');
            elements.videoPreview.innerHTML = '';
        }
    }
    
    // Preview media
    function previewMedia(id) {
        if (id.startsWith('photo-')) {
            const photo = state.photos.find(p => p.id === id);
            if (photo) {
                elements.previewContent.innerHTML = `<img src="${photo.dataUrl}" class="img-fluid" alt="Preview">`;
                document.getElementById('newPreviewModalLabel').textContent = 'Preview Foto';
                modals.previewModal.show();
            }
        } else if (id.startsWith('video-')) {
            const video = state.videos.find(v => v.id === id);
            if (video) {
                elements.previewContent.innerHTML = `
                    <video src="${video.url}" controls class="img-fluid"></video>
                `;
                document.getElementById('newPreviewModalLabel').textContent = 'Preview Video';
                modals.previewModal.show();
                
                // Auto play
                const videoElement = elements.previewContent.querySelector('video');
                if (videoElement) videoElement.play();
            }
        }
    }
    
    // Delete media
    function deleteMedia(id) {
        if (id.startsWith('photo-')) {
            state.photos = state.photos.filter(p => p.id !== id);
            updatePhotoPreview();
        } else if (id.startsWith('video-')) {
            const video = state.videos.find(v => v.id === id);
            if (video && video.url) {
                URL.revokeObjectURL(video.url);
            }
            state.videos = state.videos.filter(v => v.id !== id);
            updateVideoPreview();
        }
        
        updateSaveButtonState();
    }
    
    // Update save button state
    function updateSaveButtonState() {
        elements.saveEvidenceBtn.disabled = state.photos.length === 0 && state.videos.length === 0;
    }
    
    // Reset state
    function resetState() {
        clearMedia();
        updateSaveButtonState();
    }
    
    // Reset form
    function resetForm() {
        elements.notesInput.value = '';
        elements.taskIdInput.value = '';
        resetState();
    }
    
    // Clear media
    function clearMedia() {
        // Release video object URLs
        state.videos.forEach(video => {
            if (video.url) URL.revokeObjectURL(video.url);
        });
        
        state.photos = [];
        state.videos = [];
        updatePhotoPreview();
        updateVideoPreview();
    }
    
    // Save evidence
    function saveEvidence() {
        if (state.photos.length === 0 && state.videos.length === 0) {
            showError('Tidak ada foto atau video yang diupload.');
            return;
        }
        
        elements.saveEvidenceBtn.disabled = true;
        elements.saveEvidenceBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';
        
        const formData = new FormData();
        formData.append('task_id', elements.taskIdInput.value);
        formData.append('notes', elements.notesInput.value);
        
        // Handle photos
        const photoPromises = state.photos.map((photo, index) => {
            return fetch(photo.dataUrl)
                .then(res => res.blob())
                .then(blob => {
                    const file = new File([blob], `photo_${index}.jpg`, { type: 'image/jpeg' });
                    formData.append(`photos[${index}]`, file);
                });
        });
        
        // Handle videos
        state.videos.forEach((video, index) => {
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
                    modals.evidenceModal.hide();
                    resetForm();
                    
                    // Refresh task if needed
                    if (typeof refreshTask === 'function') {
                        refreshTask(elements.taskIdInput.value);
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
                elements.saveEvidenceBtn.disabled = false;
                elements.saveEvidenceBtn.innerHTML = 'Simpan Evidence';
            });
    }
    
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
    
    // Initialize the module
    document.addEventListener('DOMContentLoaded', init);
    
    // Return public API
    return {
        init: init,
        resetForm: resetForm
    };
})();
