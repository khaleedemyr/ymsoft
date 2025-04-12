/**
 * Camera Capture Handler
 * Untuk menangani capture foto dan video dari kamera
 */

(function() {
    // State variables
    let capturedPhotos = [];
    let capturedVideos = [];
    let stream = null;
    let mediaRecorder = null;
    let recordedChunks = [];
    let timerId = null;
    let recordingSeconds = 0;
    let facingMode = 'environment'; // Default ke kamera belakang
    
    // Inisialisasi setelah DOM siap
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil referensi ke elements
        const modal = document.getElementById('cameraCaptureModal');
        if (!modal) return;
        
        // Handle modal events
        modal.addEventListener('shown.bs.modal', function() {
            // Start camera
            startCamera();
        });
        
        modal.addEventListener('hidden.bs.modal', function() {
            // Stop camera when modal is closed
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
            }
            
            if (timerId) {
                clearInterval(timerId);
                timerId = null;
            }
        });
        
        // Button event handlers
        document.getElementById('capturePhotoBtn').addEventListener('click', capturePhoto);
        document.getElementById('recordVideoBtn').addEventListener('click', startVideoRecording);
        document.getElementById('stopRecordBtn').addEventListener('click', stopVideoRecording);
        document.getElementById('switchCameraBtn').addEventListener('click', switchCamera);
        document.getElementById('saveCameraBtn').addEventListener('click', saveEvidence);
        
        // Media actions via delegation
        document.getElementById('photoGallery').addEventListener('click', handleMediaAction);
        document.getElementById('videoGallery').addEventListener('click', handleMediaAction);
    });
    
    // Start camera
    function startCamera() {
        const videoElement = document.getElementById('cameraVideo');
        const statusElement = document.getElementById('cameraStatus');
        
        if (!videoElement || !statusElement) return;
        
        // Show status
        statusElement.textContent = 'Mengaktifkan kamera...';
        statusElement.classList.remove('d-none');
        
        // Stop any existing stream
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        
        // Set up constraints - default to back camera
        const constraints = {
            video: {
                facingMode: facingMode,
                width: { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false  // Audio only needed for video recording
        };
        
        // Request camera access
        navigator.mediaDevices.getUserMedia(constraints)
            .then(function(mediaStream) {
                stream = mediaStream;
                videoElement.srcObject = mediaStream;
                videoElement.onloadedmetadata = function() {
                    videoElement.play()
                        .then(() => {
                            statusElement.textContent = 'Kamera siap';
                            setTimeout(() => {
                                statusElement.classList.add('d-none');
                            }, 2000);
                        })
                        .catch(error => {
                            console.error('Error playing video:', error);
                            statusElement.textContent = 'Error: ' + error.message;
                        });
                };
            })
            .catch(function(err) {
                console.error('Error accessing camera:', err);
                statusElement.textContent = 'Error: ' + err.message;
                statusElement.classList.remove('d-none');
            });
    }
    
    // Capture photo
    function capturePhoto() {
        const video = document.getElementById('cameraVideo');
        const statusElement = document.getElementById('cameraStatus');
        
        if (!video || !stream) {
            if (statusElement) {
                statusElement.textContent = 'Kamera tidak tersedia';
                statusElement.classList.remove('d-none');
            }
            return;
        }
        
        try {
            // Create canvas for capturing
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            // Draw video frame to canvas
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Convert to data URL
            const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
            
            // Add to captured photos array
            const photoId = 'photo-' + Date.now();
            capturedPhotos.push({
                id: photoId,
                dataUrl: dataUrl
            });
            
            // Update UI
            updatePhotoGallery();
            updateSaveButtonState();
            
            // Show capture feedback
            if (statusElement) {
                statusElement.textContent = 'Foto berhasil ditangkap!';
                statusElement.classList.remove('d-none');
                setTimeout(() => {
                    statusElement.classList.add('d-none');
                }, 2000);
            }
        } catch (error) {
            console.error('Error capturing photo:', error);
            if (statusElement) {
                statusElement.textContent = 'Error: ' + error.message;
                statusElement.classList.remove('d-none');
            }
        }
    }
    
    // Start video recording
    function startVideoRecording() {
        if (!stream) return;
        
        // Get elements
        const recordBtn = document.getElementById('recordVideoBtn');
        const stopBtn = document.getElementById('stopRecordBtn');
        const timerElement = document.getElementById('recordingTimer');
        const statusElement = document.getElementById('cameraStatus');
        
        // Get audio stream for video recording
        navigator.mediaDevices.getUserMedia({ audio: true, video: false })
            .then(audioStream => {
                // Combine video and audio tracks
                const combinedStream = new MediaStream();
                
                // Add video tracks from existing stream
                stream.getVideoTracks().forEach(track => {
                    combinedStream.addTrack(track);
                });
                
                // Add audio track
                audioStream.getAudioTracks().forEach(track => {
                    combinedStream.addTrack(track);
                });
                
                // Create media recorder
                try {
                    mediaRecorder = new MediaRecorder(combinedStream, {
                        mimeType: 'video/webm;codecs=vp8,opus'
                    });
                } catch (e) {
                    mediaRecorder = new MediaRecorder(combinedStream);
                }
                
                // Set up event handlers
                mediaRecorder.ondataavailable = function(e) {
                    if (e.data && e.data.size > 0) {
                        recordedChunks.push(e.data);
                    }
                };
                
                mediaRecorder.onstop = function() {
                    // Create blob from chunks
                    const blob = new Blob(recordedChunks, { type: 'video/webm' });
                    
                    // Save video
                    const videoId = 'video-' + Date.now();
                    const url = URL.createObjectURL(blob);
                    
                    capturedVideos.push({
                        id: videoId,
                        blob: blob,
                        url: url
                    });
                    
                    // Update UI
                    updateVideoGallery();
                    updateSaveButtonState();
                    
                    // Stop audio stream
                    audioStream.getTracks().forEach(track => track.stop());
                    
                    // Show feedback
                    if (statusElement) {
                        statusElement.textContent = 'Video berhasil direkam!';
                        statusElement.classList.remove('d-none');
                        setTimeout(() => {
                            statusElement.classList.add('d-none');
                        }, 2000);
                    }
                };
                
                // Start recording
                recordedChunks = [];
                mediaRecorder.start(100);
                
                // Update UI
                if (recordBtn) recordBtn.classList.add('d-none');
                if (stopBtn) stopBtn.classList.remove('d-none');
                
                // Start timer
                recordingSeconds = 0;
                updateRecordingTimer();
                if (timerElement) timerElement.classList.remove('d-none');
                timerId = setInterval(updateRecordingTimer, 1000);
                
                // Show status
                if (statusElement) {
                    statusElement.textContent = 'Merekam video...';
                    statusElement.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error accessing audio:', error);
                if (statusElement) {
                    statusElement.textContent = 'Error akses mikrofon: ' + error.message;
                    statusElement.classList.remove('d-none');
                }
            });
    }
    
    // Stop video recording
    function stopVideoRecording() {
        const recordBtn = document.getElementById('recordVideoBtn');
        const stopBtn = document.getElementById('stopRecordBtn');
        const timerElement = document.getElementById('recordingTimer');
        
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
        }
        
        // Update UI
        if (recordBtn) recordBtn.classList.remove('d-none');
        if (stopBtn) stopBtn.classList.add('d-none');
        if (timerElement) timerElement.classList.add('d-none');
        
        // Stop timer
        if (timerId) {
            clearInterval(timerId);
            timerId = null;
        }
    }
    
    // Update recording timer
    function updateRecordingTimer() {
        const timerElement = document.getElementById('recordingTimer');
        if (!timerElement) return;
        
        const minutes = Math.floor(recordingSeconds / 60).toString().padStart(2, '0');
        const seconds = (recordingSeconds % 60).toString().padStart(2, '0');
        timerElement.textContent = `${minutes}:${seconds}`;
        
        recordingSeconds++;
    }
    
    // Switch camera (front/back)
    function switchCamera() {
        facingMode = facingMode === 'environment' ? 'user' : 'environment';
        startCamera();
    }
    
    // Update photo gallery
    function updatePhotoGallery() {
        const gallery = document.getElementById('photoGallery');
        const noPhotosMsg = document.getElementById('noPhotosMsg');
        const photoCount = document.getElementById('photoCount');
        
        if (!gallery || !noPhotosMsg || !photoCount) return;
        
        // Update counter
        photoCount.textContent = capturedPhotos.length.toString();
        
        // Show/hide empty message
        if (capturedPhotos.length === 0) {
            noPhotosMsg.classList.remove('d-none');
            gallery.innerHTML = '';
            return;
        }
        
        noPhotosMsg.classList.add('d-none');
        gallery.innerHTML = '';
        
        // Add each photo
        capturedPhotos.forEach(photo => {
            const div = document.createElement('div');
            div.className = 'media-item';
            div.innerHTML = `
                <img src="${photo.dataUrl}" alt="Captured photo">
                <div class="media-actions">
                    <button type="button" data-action="view" data-id="${photo.id}" title="Preview">
                        <i class="ri-eye-line"></i>
                    </button>
                    <button type="button" data-action="delete" data-id="${photo.id}" title="Delete">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            `;
            gallery.appendChild(div);
        });
    }
    
    // Update video gallery
    function updateVideoGallery() {
        const gallery = document.getElementById('videoGallery');
        const noVideosMsg = document.getElementById('noVideosMsg');
        const videoCount = document.getElementById('videoCount');
        
        if (!gallery || !noVideosMsg || !videoCount) return;
        
        // Update counter
        videoCount.textContent = capturedVideos.length.toString();
        
        // Show/hide empty message
        if (capturedVideos.length === 0) {
            noVideosMsg.classList.remove('d-none');
            gallery.innerHTML = '';
            return;
        }
        
        noVideosMsg.classList.add('d-none');
        gallery.innerHTML = '';
        
        // Add each video
        capturedVideos.forEach(video => {
            const div = document.createElement('div');
            div.className = 'media-item';
            div.innerHTML = `
                <video src="${video.url}" muted></video>
                <div class="media-actions">
                    <button type="button" data-action="view" data-id="${video.id}" title="Preview">
                        <i class="ri-eye-line"></i>
                    </button>
                    <button type="button" data-action="delete" data-id="${video.id}" title="Delete">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            `;
            gallery.appendChild(div);
        });
    }
    
    // Handle media actions (view/delete)
    function handleMediaAction(e) {
        const button = e.target.closest('button[data-action]');
        if (!button) return;
        
        const action = button.dataset.action;
        const id = button.dataset.id;
        
        if (action === 'view') {
            previewMedia(id);
        } else if (action === 'delete') {
            deleteMedia(id);
        }
    }
    
    // Preview media
    function previewMedia(id) {
        const previewContainer = document.getElementById('previewContainer');
        const previewModalLabel = document.getElementById('previewModalLabel');
        
        if (!previewContainer || !previewModalLabel) return;
        
        let media = null;
        
        if (id.startsWith('photo-')) {
            media = capturedPhotos.find(p => p.id === id);
            if (media) {
                previewContainer.innerHTML = `<img src="${media.dataUrl}" class="img-fluid" alt="Preview">`;
                previewModalLabel.textContent = 'Preview Foto';
            }
        } else if (id.startsWith('video-')) {
            media = capturedVideos.find(v => v.id === id);
            if (media) {
                previewContainer.innerHTML = `<video src="${media.url}" controls class="img-fluid"></video>`;
                previewModalLabel.textContent = 'Preview Video';
                
                // Auto play
                const video = previewContainer.querySelector('video');
                if (video) video.play().catch(e => console.error('Auto-play failed:', e));
            }
        }
        
        if (media) {
            const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
            previewModal.show();
        }
    }
    
    // Delete media
    function deleteMedia(id) {
        if (id.startsWith('photo-')) {
            capturedPhotos = capturedPhotos.filter(p => p.id !== id);
            updatePhotoGallery();
        } else if (id.startsWith('video-')) {
            const video = capturedVideos.find(v => v.id === id);
            if (video && video.url) {
                URL.revokeObjectURL(video.url);
            }
            capturedVideos = capturedVideos.filter(v => v.id !== id);
            updateVideoGallery();
        }
        
        updateSaveButtonState();
    }
    
    // Update save button state
    function updateSaveButtonState() {
        const saveBtn = document.getElementById('saveCameraBtn');
        if (saveBtn) {
            saveBtn.disabled = capturedPhotos.length === 0 && capturedVideos.length === 0;
        }
    }
    
    // Save evidence
    function saveEvidence() {
        const taskId = document.getElementById('cameraTaskId').value;
        const notes = document.getElementById('cameraNotes').value;
        const saveBtn = document.getElementById('saveCameraBtn');
        
        if (!taskId) {
            showNotification('error', 'ID Task tidak valid');
            return;
        }
        
        if (capturedPhotos.length === 0 && capturedVideos.length === 0) {
            showNotification('error', 'Tidak ada foto atau video yang di-capture');
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
        
        // Convert photos to files
        const photoPromises = capturedPhotos.map((photo, index) => {
            return fetch(photo.dataUrl)
                .then(res => res.blob())
                .then(blob => {
                    const file = new File([blob], `photo_${index}.jpg`, { type: 'image/jpeg' });
                    formData.append(`photos[${index}]`, file);
                });
        });
        
        // Add videos to form data
        capturedVideos.forEach((video, index) => {
            const file = new File([video.blob], `video_${index}.webm`, { type: 'video/webm' });
            formData.append(`videos[${index}]`, file);
        });
        
        // Process all photo promises then submit
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
                    showNotification('success', data.message || 'Evidence berhasil disimpan');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('cameraCaptureModal'));
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
                    
                    // Refresh task data if needed
                    if (typeof refreshTaskData === 'function') {
                        refreshTaskData();
                    }
                } else {
                    throw new Error(data.message || 'Gagal menyimpan evidence');
                }
            })
            .catch(error => {
                console.error('Error saving evidence:', error);
                showNotification('error', error.message || 'Terjadi kesalahan saat menyimpan evidence');
            })
            .finally(() => {
                // Re-enable save button
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = 'Simpan Evidence';
                }
            });
    }
    
    // Show notification
    function showNotification(type, message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Berhasil!' : 'Error!',
                text: message,
                timer: type === 'success' ? 2000 : undefined,
                showConfirmButton: type !== 'success'
            });
        } else {
            alert(message);
        }
    }
})();
