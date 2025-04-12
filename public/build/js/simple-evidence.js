/**
 * Simple Evidence JavaScript
 * Untuk menangani foto & video evidence dengan lebih sederhana
 */

const SimpleEvidenceApp = (function() {
    // Private variables
    let capturedPhotos = [];
    let capturedVideos = [];
    let currentStream = null;
    let mediaRecorder = null;
    let recordedChunks = [];
    let facingMode = 'environment'; // Default: kamera belakang
    let videoMode = false; // Mode video (true) atau foto (false)
    let evidenceTaskId = null; // ID task yang sedang dibuatkan evidence
    let recordingTimer = null; // Timer untuk menghitung durasi rekaman
    let recordingStartTime = null; // Waktu mulai rekaman
    
    // Inisialisasi aplikasi
    function init() {
        // Make sure Bootstrap is available
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap is not loaded! Evidence functionality will not work.');
            return;
        }
        
        // Setup event listeners
        setupEventListeners();
    }
    
    // Setup semua event listener
    function setupEventListeners() {
        // Open simple evidence modal when button is clicked
        $(document).on('click', '.simple-evidence-btn, .new-evidence-btn', function() {
            evidenceTaskId = $(this).data('task-id');
            $('#simpleEvidenceModal').modal('show');
        });
        
        // Take photo button
        $('#takeCameraPhotoBtn').on('click', function() {
            openCamera(false);
        });
        
        // Record video button
        $('#recordCameraVideoBtn').on('click', function() {
            openCamera(true);
        });
        
        // Take picture button (in camera modal)
        $('#takePictureBtn').on('click', function() {
            capturePhoto();
        });
        
        // Start/stop video recording button
        $('#stopVideoRecordingBtn').on('click', function() {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                stopRecording();
            } else {
                // Start recording if not already recording
                startRecording(currentStream);
            }
        });
        
        // Switch camera button
        $('#switchSimpleCameraBtn').on('click', function() {
            switchCamera();
        });
        
        // Cancel button in camera modal
        $('#cancelCameraBtn, #closeCameraModalBtn').on('click', function() {
            stopCamera();
            $('#simpleCameraModal').modal('hide');
        });
        
        // Save evidence button
        $('#evidenceSaveBtn').on('click', function() {
            saveEvidence();
        });
        
        // Delete media
        $(document).on('click', '.delete-media-btn', function() {
            const type = $(this).data('type');
            const index = $(this).data('index');
            
            if (type === 'photo') {
                // Remove from array and revoke URL
                URL.revokeObjectURL(capturedPhotos[index].url);
                capturedPhotos.splice(index, 1);
                
                // Update UI
                updatePhotoPreview();
            } else if (type === 'video') {
                // Remove from array and revoke URL
                URL.revokeObjectURL(capturedVideos[index].url);
                capturedVideos.splice(index, 1);
                
                // Update UI
                updateVideoPreview();
            }
        });
        
        // Preview media
        $(document).on('click', '.preview-media-btn', function() {
            const type = $(this).data('type');
            const index = $(this).data('index');
            const content = $('#previewMediaContent');
            content.empty();
            
            if (type === 'photo') {
                if (capturedPhotos[index]) {
                    const img = $('<img>', {
                        src: capturedPhotos[index].url,
                        class: 'img-fluid'
                    });
                    content.append(img);
                }
            } else if (type === 'video') {
                if (capturedVideos[index]) {
                    const video = $('<video>', {
                        src: capturedVideos[index].url,
                        controls: true,
                        autoplay: true,
                        class: 'img-fluid'
                    });
                    content.append(video);
                }
            }
            
            $('#previewMediaModal').modal('show');
        });
        
        // Reset when modal is hidden
        $('#simpleEvidenceModal').on('hidden.bs.modal', function() {
            resetForm();
        });
    }
    
    // Open camera with specified mode
    function openCamera(isVideoMode) {
        videoMode = isVideoMode;
        
        // Show appropriate button
        if (videoMode) {
            $('#takePictureBtn').addClass('d-none');
            $('#stopVideoRecordingBtn').removeClass('d-none');
            $('#simpleCameraModalLabel').text('Rekam Video');
            
            // Set tombol rekam video ke mode "Mulai Rekam"
            $('#stopVideoRecordingBtn').html('<i class="ri-record-circle-line me-1"></i> Mulai Rekam');
            $('#stopVideoRecordingBtn').removeClass('btn-danger').addClass('btn-success');
        } else {
            $('#takePictureBtn').removeClass('d-none');
            $('#stopVideoRecordingBtn').addClass('d-none');
            $('#simpleCameraModalLabel').text('Ambil Foto');
        }
        
        // Hide timer
        $('#videoTimer').addClass('d-none');
        
        // Show camera modal
        $('#simpleCameraModal').modal('show');
        
        // Reset status and warnings
        $('#simpleCameraStatus').text('Memuat kamera...');
        $('#simpleBrowserWarning').addClass('d-none');
        
        // Add loading class
        $('#simpleCameraStream').addClass('loading');
        
        // Request camera access
        navigator.mediaDevices.getUserMedia({
            video: { 
                facingMode: facingMode,
                width: { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: videoMode
        })
        .then(function(stream) {
            currentStream = stream;
            
            // Connect stream to video element
            const videoElement = document.getElementById('simpleCameraStream');
            videoElement.srcObject = stream;
            
            // Set up play event
            videoElement.onloadedmetadata = function() {
                videoElement.play()
                .then(function() {
                    // Remove loading class
                    $('#simpleCameraStream').removeClass('loading');
                    
                    // Update status
                    $('#simpleCameraStatus').text(videoMode ? 'Siap untuk merekam video' : 'Siap untuk mengambil foto');
                    
                    // PERUBAHAN: Tidak langsung mulai rekaman video
                    // if (videoMode) {
                    //     startRecording(stream);
                    // }
                })
                .catch(function(error) {
                    $('#simpleCameraStatus').text('Error: ' + error.message);
                    $('#simpleBrowserWarning').removeClass('d-none');
                    $('#simpleBrowserMessage').text('Error saat memulai video: ' + error.message);
                });
            };
        })
        .catch(function(error) {
            console.error('Error accessing camera:', error);
            $('#simpleCameraStatus').text('Error: ' + error.message);
            $('#simpleBrowserWarning').removeClass('d-none');
            $('#simpleBrowserMessage').text('Error akses kamera: ' + error.message);
        });
    }
    
    // Capture photo from camera
    function capturePhoto() {
        const videoElement = document.getElementById('simpleCameraStream');
        
        // Check if video is ready
        if (!videoElement || !videoElement.srcObject) {
            alert('Kamera belum siap. Silakan coba lagi.');
            return;
        }
        
        // Create canvas to capture frame
        const canvas = document.createElement('canvas');
        canvas.width = videoElement.videoWidth;
        canvas.height = videoElement.videoHeight;
        
        // Draw video frame to canvas
        const context = canvas.getContext('2d');
        context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
        
        // Convert to blob
        canvas.toBlob(function(blob) {
            // Create URL from blob
            const imageUrl = URL.createObjectURL(blob);
            
            // Save photo data
            capturedPhotos.push({
                blob: blob,
                url: imageUrl
            });
            
            // Update photo preview
            updatePhotoPreview();
            
            // Close camera modal
            stopCamera();
            $('#simpleCameraModal').modal('hide');
            
            // Switch to photos tab
            $('#photos-tab').tab('show');
        }, 'image/jpeg', 0.85);
    }
    
    // Start video recording
    function startRecording(stream) {
        // Ubah tampilan tombol
        $('#stopVideoRecordingBtn').html('<i class="ri-stop-circle-line me-1"></i> Stop Rekam');
        $('#stopVideoRecordingBtn').removeClass('btn-success').addClass('btn-danger');
        
        // Reset recorded chunks and timer
        recordedChunks = [];
        recordingStartTime = new Date();
        
        // Update status
        $('#simpleCameraStatus').text('Merekam video...');
        
        // Show and reset timer
        $('#videoTimer').removeClass('d-none').text('00:00');
        
        // Detect supported formats
        const mimeTypes = [
            'video/webm;codecs=vp9,opus',
            'video/webm;codecs=vp8,opus',
            'video/webm;codecs=h264,opus',
            'video/webm',
            'video/mp4'
        ];
        
        let options = null;
        
        // Find supported format
        for (let i = 0; i < mimeTypes.length; i++) {
            if (MediaRecorder.isTypeSupported(mimeTypes[i])) {
                options = { mimeType: mimeTypes[i] };
                break;
            }
        }
        
        try {
            // Create MediaRecorder with supported format
            mediaRecorder = new MediaRecorder(stream, options);
        } catch (e) {
            console.error('Error creating MediaRecorder:', e);
            $('#simpleCameraStatus').text('Error: ' + e.message);
            return;
        }
        
        // Event for saving recorded data
        mediaRecorder.ondataavailable = function(e) {
            if (e.data && e.data.size > 0) {
                recordedChunks.push(e.data);
            }
        };
        
        // Event for handling errors
        mediaRecorder.onerror = function(e) {
            $('#simpleCameraStatus').text('Error: ' + e.error);
            stopRecording();
        };
        
        // Event for when recording is stopped
        mediaRecorder.onstop = function() {
            if (recordedChunks.length === 0) {
                alert('Tidak ada data video yang terekam');
                return;
            }
            
            try {
                // Create blob from chunks
                const videoBlob = new Blob(recordedChunks, {
                    type: options ? options.mimeType : 'video/webm'
                });
                
                // Create URL from blob
                const videoUrl = URL.createObjectURL(videoBlob);
                
                // Save video data
                capturedVideos.push({
                    blob: videoBlob,
                    url: videoUrl,
                    type: options ? options.mimeType : 'video/webm'
                });
                
                // Update video preview
                updateVideoPreview();
                
                // Switch to videos tab
                $('#videos-tab').tab('show');
                
                // Stop timer
                clearInterval(recordingTimer);
                
            } catch (e) {
                console.error('Error creating video:', e);
                alert('Error saat membuat video: ' + e.message);
            }
        };
        
        // Start recording with chunks every 1 second
        try {
            mediaRecorder.start(1000);
            
            // Start timer
            recordingTimer = setInterval(updateRecordingTimer, 1000);
            
        } catch (e) {
            console.error('Error starting recording:', e);
            $('#simpleCameraStatus').text('Error: ' + e.message);
        }
    }
    
    // Update recording timer
    function updateRecordingTimer() {
        const elapsed = Math.floor((new Date() - recordingStartTime) / 1000);
        const minutes = Math.floor(elapsed / 60).toString().padStart(2, '0');
        const seconds = (elapsed % 60).toString().padStart(2, '0');
        $('#videoTimer').text(`${minutes}:${seconds}`);
    }
    
    // Stop video recording
    function stopRecording() {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
            $('#simpleCameraStatus').text('Menyimpan video...');
            
            // Reset tombol
            $('#stopVideoRecordingBtn').html('<i class="ri-record-circle-line me-1"></i> Mulai Rekam');
            $('#stopVideoRecordingBtn').removeClass('btn-danger').addClass('btn-success');
            
            // Stop timer
            clearInterval(recordingTimer);
            
            // Sembunyikan timer
            $('#videoTimer').addClass('d-none');
            
            // Stop and close camera after a short delay to ensure recording is saved
            setTimeout(function() {
                stopCamera();
                $('#simpleCameraModal').modal('hide');
            }, 500);
        }
    }
    
    // Update photo preview
    function updatePhotoPreview() {
        const photoContainer = $('.photo-preview');
        photoContainer.empty();
        
        // Update counter
        $('#photoCount').text(capturedPhotos.length);
        
        if (capturedPhotos.length === 0) {
            photoContainer.append(`
                <div id="noPhotosMessage" class="text-center w-100 py-3">
                    <p class="text-muted mb-0">Belum ada foto. Silakan ambil foto dengan kamera.</p>
                </div>
            `);
            return;
        }
        
        // Add each photo preview
        capturedPhotos.forEach((photo, index) => {
            const photoCard = `
                <div class="card media-preview-item" style="width: 150px;">
                    <div class="card-body p-2">
                        <img src="${photo.url}" class="img-fluid rounded mb-2" style="height: 100px; object-fit: cover; width: 100%; cursor: pointer;">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-sm btn-info preview-media-btn" data-type="photo" data-index="${index}">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-media-btn" data-type="photo" data-index="${index}">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            photoContainer.append(photoCard);
        });
    }
    
    // Update video preview
    function updateVideoPreview() {
        const videoContainer = $('.video-preview');
        videoContainer.empty();
        
        // Update counter
        $('#videoCount').text(capturedVideos.length);
        
        if (capturedVideos.length === 0) {
            videoContainer.append(`
                <div id="noVideosMessage" class="text-center w-100 py-3">
                    <p class="text-muted mb-0">Belum ada video. Silakan rekam video dengan kamera.</p>
                </div>
            `);
            return;
        }
        
        // Add each video preview
        capturedVideos.forEach((video, index) => {
            const videoCard = `
                <div class="card media-preview-item" style="width: 150px;">
                    <div class="card-body p-2">
                        <video src="${video.url}" class="img-fluid rounded mb-2" style="height: 100px; object-fit: cover; width: 100%; cursor: pointer;"></video>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-sm btn-info preview-media-btn" data-type="video" data-index="${index}">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-media-btn" data-type="video" data-index="${index}">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            videoContainer.append(videoCard);
        });
    }
    
    // Switch camera (front/back)
    function switchCamera() {
        // Toggle facing mode
        facingMode = facingMode === 'environment' ? 'user' : 'environment';
        
        // If stream is active, restart camera with new facing mode
        if (currentStream) {
            stopCamera();
            openCamera(videoMode);
        }
    }
    
    // Stop camera stream
    function stopCamera() {
        // Stop all tracks in current stream
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
        
        // Stop media recorder if active
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }
        
        // Stop timer
        if (recordingTimer) {
            clearInterval(recordingTimer);
            recordingTimer = null;
        }
        
        // Clear video element source
        const videoElement = document.getElementById('simpleCameraStream');
        if (videoElement && videoElement.srcObject) {
            videoElement.srcObject = null;
        }
    }
    
    // Save evidence to server
    function saveEvidence() {
        // Validate
        if (capturedPhotos.length === 0 && capturedVideos.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Anda harus menambahkan minimal 1 foto atau video'
            });
            return;
        }
        
        if (!evidenceTaskId) {
            console.error('No task ID set');
            return;
        }
        
        // Get notes
        const notes = $('#evidenceNotes').val();
        
        // Create FormData object
        const formData = new FormData();
        formData.append('task_id', evidenceTaskId);
        formData.append('notes', notes);
        
        // Add photos
        capturedPhotos.forEach((photo, index) => {
            if (photo.blob) {
                formData.append(`photos[${index}]`, photo.blob, `photo_${index}.jpg`);
            }
        });
        
        // Add videos
        capturedVideos.forEach((video, index) => {
            if (video.blob) {
                formData.append(`videos[${index}]`, video.blob, `video_${index}.webm`);
            }
        });
        
        // Tambahkan CSRF token ke FormData
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        // Show loading
        Swal.fire({
            title: 'Menyimpan Evidence',
            text: 'Mohon tunggu...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Send to server
        $.ajax({
            url: '/maintenance/kanban/upload-evidence',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Evidence berhasil disimpan:', response);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Evidence berhasil disimpan'
                });
                
                // Reset form and close modal
                resetForm();
                $('#simpleEvidenceModal').modal('hide');
                
                // Reload page if needed
                // window.location.reload();
            },
            error: function(xhr) {
                console.error('Gagal menyimpan evidence:', xhr.responseText);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal menyimpan evidence: ' + (xhr.responseJSON?.message || xhr.statusText)
                });
            }
        });
    }
    
    // Reset form
    function resetForm() {
        // Revoke all object URLs
        capturedPhotos.forEach(photo => URL.revokeObjectURL(photo.url));
        capturedVideos.forEach(video => URL.revokeObjectURL(video.url));
        
        // Reset arrays
        capturedPhotos = [];
        capturedVideos = [];
        
        // Clear previews
        updatePhotoPreview();
        updateVideoPreview();
        
        // Reset notes
        $('#evidenceNotes').val('');
        
        // Reset task ID
        evidenceTaskId = null;
    }
    
    // Public API
    return {
        init: init
    };
})();

// Initialize app when document is ready
$(document).ready(function() {
    SimpleEvidenceApp.init();
});
