// maintenance-comments.js
// File khusus untuk menangani fitur komentar maintenance task

// Variabel global untuk komentar
var commentCapturedPhotos = [];
var commentCapturedVideos = [];
var commentUploadedFiles = [];
var commentActiveStream = null;
var commentMediaRecorder = null;
var commentRecordedChunks = [];
var isSubmittingComment = false;
var isCommentSubmitSuccess = false;

// Inisialisasi komponen komentar
function initMaintenanceComments() {
    // Listen untuk klik tombol komentar
    $(document).on('click', '.task-comment-btn', function() {
        const taskId = $(this).data('task-id');
        
        // Tampilkan loading state
        $('.comments-list').html('<div class="text-center p-3"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading comments...</p></div>');
        
        // Set task ID pada form
        $('#commentTaskId').val(taskId);
        
        // Load comments
        loadTaskComments(taskId);
        
        // Tampilkan modal
        $('#taskCommentsModal').modal('show');
    });
    
    // Capture photo untuk komentar
    $('#commentCapturePhoto').on('click', function() {
        initializeCommentCamera(false);
    });
    
    // Capture video untuk komentar
    $('#commentCaptureVideo').on('click', function() {
        initializeCommentCamera(true);
    });
    
    // Trigger file upload untuk komentar
    $('#commentUploadBtn').on('click', function() {
        $('#commentFileUpload').click();
    });
    
    // Handle file upload untuk komentar
    $('#commentFileUpload').on('change', function(e) {
        if (e.target.files && e.target.files.length > 0) {
            console.log('File input change event fired, files:', e.target.files.length);
            handleCommentFileUpload(e.target.files);
            
            // Reset input agar event change bisa terpicu lagi untuk file yang sama
            $(this).val('');
        }
    });
    
    // Event untuk menghapus media di comment
    $(document).on('click', '.delete-comment-media', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const mediaWrapper = $(this).closest('.comment-media-wrapper');
        const mediaItem = mediaWrapper.find('.comment-media-item');
        const index = mediaItem.data('index');
        const type = mediaItem.data('type');
        
        if (type === 'photo') {
            URL.revokeObjectURL(commentCapturedPhotos[index].url);
            commentCapturedPhotos.splice(index, 1);
        } else if (type === 'video') {
            URL.revokeObjectURL(commentCapturedVideos[index].url);
            commentCapturedVideos.splice(index, 1);
                } else {
            // Uploaded file
            URL.revokeObjectURL(commentUploadedFiles[index].url);
            commentUploadedFiles.splice(index, 1);
        }
        
        mediaWrapper.remove();
    });
    
    // Event delegation untuk play/pause video di comment
    $(document).on('click', '.comment-play-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const videoContainer = $(this).closest('.comment-media-item');
        const video = videoContainer.find('video')[0];
        const playIcon = $(this).find('i');
        
        // Pause semua video lainnya
        $('.comment-media-item video').each(function() {
            if (this !== video && !this.paused) {
                this.pause();
                $(this).closest('.comment-media-item')
                    .find('.comment-play-btn i')
                    .removeClass('ri-pause-fill')
                    .addClass('ri-play-fill');
            }
        });
        
        if (video.paused) {
            video.play();
            playIcon.removeClass('ri-play-fill').addClass('ri-pause-fill');
        } else {
            video.pause();
            playIcon.removeClass('ri-pause-fill').addClass('ri-play-fill');
        }
    });
    
    // Reset media saat modal comment ditutup
    $('#taskCommentsModal').on('hidden.bs.modal', function() {
        // JANGAN reset data di sini, cukup clear preview saja
        // commentCapturedPhotos = []; // HAPUS BARIS INI
        // commentCapturedVideos = []; // HAPUS BARIS INI
        // commentUploadedFiles = []; // HAPUS BARIS INI
        
        // Clear preview hanya jika form sudah berhasil submit
        if (isCommentSubmitSuccess) {
            $('.comment-capture-preview').empty();
            $('.comment-upload-preview').empty();
            isCommentSubmitSuccess = false;
        }
    });
    
    // Tutup camera stream saat modal ditutup
    $('#commentCameraModal').on('hidden.bs.modal', function() {
        if (commentActiveStream) {
            commentActiveStream.getTracks().forEach(track => track.stop());
            commentActiveStream = null;
        }
    });
    
    // Form submit untuk komentar
    $('#commentForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        // Debug log
        console.log('===== SUBMIT COMMENT FORM =====');
        
        const taskId = $('#commentTaskId').val();
        const commentText = $('#commentText').val().trim();
        
        if (!commentText && commentCapturedPhotos.length === 0 && 
            commentCapturedVideos.length === 0 && commentUploadedFiles.length === 0) {
            return; // Jangan submit jika kosong dan tidak ada media
        }
        
        // Set flag submitting
        isSubmittingComment = true;
        
        // Disable form selama proses submit
        $('#commentForm button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
        
        // ---- PENDEKATAN BARU ----
        // Gunakan formData seperti di modal create task
        const formData = new FormData();
        formData.append('task_id', taskId);
        formData.append('comment', commentText);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        // Tambahkan captured photos sebagai file (ganti nama menjadi photos[] seperti di task create)
        let capturedPhotoCount = 0;
        if (commentCapturedPhotos && commentCapturedPhotos.length > 0) {
            commentCapturedPhotos.forEach(function(photo, index) {
                if (photo && photo.blob) {
                    console.log(`Adding photo ${index} to form data`);
                    // Gunakan nama field photos[] seperti di create task
                    formData.append(`photos[]`, photo.blob, `photo_${Date.now()}_${index}.jpg`);
                    capturedPhotoCount++;
                }
            });
        }
        console.log(`Total captured photos added: ${capturedPhotoCount}`);
        
        // Tambahkan captured videos (ganti nama menjadi videos[] seperti di task create)
        let capturedVideoCount = 0;
        if (commentCapturedVideos && commentCapturedVideos.length > 0) {
            commentCapturedVideos.forEach(function(video, index) {
                if (video && video.blob) {
                    // Gunakan nama field videos[] seperti di create task
                    formData.append(`videos[]`, video.blob, `video_${Date.now()}_${index}.webm`);
                    capturedVideoCount++;
                }
            });
        }
        console.log(`Total captured videos added: ${capturedVideoCount}`);
        
        // Tambahkan uploaded files (ganti nama menjadi documents[] seperti di task create)
        let uploadedFileCount = 0;
        if (commentUploadedFiles && commentUploadedFiles.length > 0) {
            commentUploadedFiles.forEach(function(item, index) {
                if (item && item.file) {
                    // Gunakan nama field documents[] seperti di create task
                    formData.append(`documents[]`, item.file, item.file.name);
                    uploadedFileCount++;
                }
            });
        }
        console.log(`Total uploaded files added: ${uploadedFileCount}`);
        
        // Log total media yang ditambahkan
        console.log(`Total media added to formData: ${capturedPhotoCount + capturedVideoCount + uploadedFileCount}`);
        
        // Debug isi formData
        console.log('FormData contents:');
        for (let [key, value] of formData.entries()) {
            console.log(`Key: ${key}, Value: ${value instanceof Blob ? 'Blob (' + value.size + ' bytes)' : value}`);
        }
        
        // Submit dengan fetch API untuk kompatibilitas lebih baik
        fetch('/maintenance/comments', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Fetch response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            // Set flag success
            isCommentSubmitSuccess = true;
            
            // Reset form
            $('#commentText').val('');
            $('.comment-capture-preview').empty();
            $('.comment-upload-preview').empty();
            
            // Reset arrays setelah berhasil
            commentCapturedPhotos = [];
            commentCapturedVideos = [];
            commentUploadedFiles = [];
            
            // Reload comments
            loadTaskComments(taskId);
            
            // Update comment count
            const commentCount = parseInt($('.task-comment-btn[data-task-id="' + taskId + '"] .badge').text() || '0') + 1;
            $('.task-comment-btn[data-task-id="' + taskId + '"]').html(`
                <i class="ri-chat-1-line me-1"></i>
                <span>Comments</span>
                <span class="badge bg-primary ms-1">${commentCount}</span>
            `);
            
            // Enable form kembali
            $('#commentForm button[type="submit"]').prop('disabled', false).html('<i class="ri-send-plane-fill me-1"></i> Add Comment');
            
            // Reset flag submitting
            isSubmittingComment = false;
            
            // Tampilkan notifikasi
            Swal.fire({
                icon: 'success',
                title: 'Comment Added',
                text: 'Your comment has been successfully added',
                timer: 2000,
                showConfirmButton: false
            });
            
            // Auto-scroll to top setelah reload comments (jika dibutuhkan tambahan)
            setTimeout(function() {
                $('.comments-list').scrollTop(0);
                console.log('Auto-scrolled to top to show newest comments');
            }, 300);
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Enable form kembali
            $('#commentForm button[type="submit"]').prop('disabled', false).html('<i class="ri-send-plane-fill me-1"></i> Add Comment');
            
            // Reset flag submitting
            isSubmittingComment = false;
            
            // Tampilkan error
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to add comment. Please try again.'
            });
        });
    });

    // Tambahkan tombol upload langsung ke preview
    $('.comment-capture-preview').on('click', '.upload-photo-btn', function(e) {
        e.preventDefault();
        const wrapper = $(this).closest('.comment-media-wrapper');
        const index = wrapper.find('.comment-media-item').data('index');
        
        if (index >= 0 && index < commentCapturedPhotos.length) {
            const photo = commentCapturedPhotos[index];
            submitCommentWithMedia(photo);
        }
    });

    // Event handler untuk upload video
    $(document).on('click', '.upload-video-btn', function(e) {
        e.preventDefault();
        const wrapper = $(this).closest('.comment-media-wrapper');
        const index = wrapper.find('.comment-media-item').data('index');
        
        if (index >= 0 && index < commentCapturedVideos.length) {
            const video = commentCapturedVideos[index];
            submitCommentWithVideo(video);
        }
    });

    // Event handler untuk upload file
    $(document).on('click', '.upload-file-btn', function(e) {
        e.preventDefault();
        const wrapper = $(this).closest('.comment-media-wrapper');
        const index = wrapper.find('.comment-media-item').data('index');
        
        if (index >= 0 && index < commentUploadedFiles.length) {
            const fileData = commentUploadedFiles[index];
            submitCommentWithFile(fileData);
        }
    });
}

// Fungsi untuk load comments dengan scroll ke atas
function loadTaskComments(taskId) {
    $.ajax({
        url: `/maintenance/comments/${taskId}`,
        method: 'GET',
        success: function(response) {
            if (response && response.length > 0) {
                let commentsHTML = '';
                
                response.forEach(comment => {
                    const commentDate = new Date(comment.created_at);
                    const formattedDate = commentDate.toLocaleString();
                    const currentUserId = $('meta[name="user-id"]').attr('content'); // Tambahkan meta tag user-id di layout
                    
                    // Process attachments if exists
                    let attachmentsHTML = '';
                    if (comment.attachments && comment.attachments.length > 0) {
                        attachmentsHTML = '<div class="comment-attachments-gallery d-flex flex-wrap gap-2 mt-2">';
                        
                        comment.attachments.forEach(attachment => {
                            // Gunakan path lengkap dari database
                            const mediaPath = `/storage/${attachment.file_path}`;
                            
                            if (attachment.file_type.startsWith('image/')) {
                                // Image
                                attachmentsHTML += `
                                    <div class="comment-attachment-item-sm">
                                        <img src="${mediaPath}" alt="${attachment.file_name}" class="img-thumbnail" 
                                             style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                             onclick="openCommentMedia('${mediaPath}', '${attachment.file_type}')">
                                    </div>
                                `;
                            } else if (attachment.file_type.startsWith('video/')) {
                                // Video
                                attachmentsHTML += `
                                    <div class="comment-attachment-item-sm position-relative">
                                        <div style="width: 80px; height: 80px;" class="img-thumbnail d-flex align-items-center justify-content-center bg-dark">
                                            <i class="ri-play-circle-line text-white" style="font-size: 30px;"></i>
                                            <div class="position-absolute w-100 h-100 top-0 start-0" 
                                                 style="cursor: pointer;"
                                                 onclick="openCommentMedia('${mediaPath}', '${attachment.file_type}')"></div>
                                        </div>
                                    </div>
                                `;
                            } else {
                                // File (document)
                                let fileIcon = 'ri-file-text-line';
                                let colorClass = 'text-secondary';
                                
                                if (attachment.file_type.includes('pdf')) {
                                    fileIcon = 'ri-file-pdf-line';
                                    colorClass = 'text-danger';
                                } else if (attachment.file_type.includes('word')) {
                                    fileIcon = 'ri-file-word-line';
                                    colorClass = 'text-primary';
                                } else if (attachment.file_type.includes('excel')) {
                                    fileIcon = 'ri-file-excel-line';
                                    colorClass = 'text-success';
                                }
                                
                                attachmentsHTML += `
                                    <div class="comment-attachment-item-sm">
                                        <div style="width: 80px; height: 80px;" 
                                             class="img-thumbnail d-flex align-items-center justify-content-center">
                                            <a href="${mediaPath}" target="_blank" download="${attachment.file_name}">
                                                <i class="${fileIcon} ${colorClass}" style="font-size: 30px;"></i>
                                            </a>
                                        </div>
                                    </div>
                                `;
                            }
                        });
                        
                        attachmentsHTML += '</div>';
                    }
                    
                    // Tambahkan tombol delete jika komentar milik user yang sedang login
                    const deleteButton = String(currentUserId) === String(comment.user_id) ? 
                        `<a href="javascript:void(0);" class="text-danger delete-comment-btn" data-comment-id="${comment.id}">
                            <i class="ri-delete-bin-line"></i>
                        </a>` : '';
                    
                    commentsHTML += `
                        <div class="comment-item" id="comment-${comment.id}">
                            <div class="comment-header d-flex justify-content-between">
                                <div class="comment-author">
                                    <strong>${comment.user_name || 'User'}</strong>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <small class="text-muted">${formattedDate}</small>
                                    ${deleteButton}
                                </div>
                            </div>
                            <div class="comment-body mt-1">
                                ${comment.comment || ''}
                                ${attachmentsHTML}
                            </div>
                            <hr>
                        </div>
                    `;
                });
                
                $('.comments-list').html(commentsHTML);
                
                // PERBAIKAN: Scroll ke posisi paling atas untuk melihat komentar terbaru
                setTimeout(function() {
                    $('.comments-list').scrollTop(0);
                    console.log('Auto-scrolled to top to show newest comments');
                }, 100);
            } else {
                $('.comments-list').html('<div class="text-center p-3"><p class="text-muted">No comments yet. Be the first to comment!</p></div>');
            }
        },
        error: function(xhr) {
            console.error('Error loading comments:', xhr);
            $('.comments-list').html('<div class="text-center p-3"><p class="text-danger">Failed to load comments. Please try again.</p></div>');
        }
    });
}

// Tambahkan event handler untuk tombol delete comment
$(document).on('click', '.delete-comment-btn', function() {
    const commentId = $(this).data('comment-id');
    const commentItem = $(`#comment-${commentId}`);
    
    // Konfirmasi penghapusan
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Komentar dan semua lampiran akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Tidak, batalkan!',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan loading
            commentItem.find('.comment-body').prepend(`
                <div class="delete-loading mb-2">
                    <div class="spinner-border spinner-border-sm text-danger" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="ms-2 text-danger">Menghapus komentar...</span>
                </div>
            `);
            
            // Kirim request hapus ke server
            $.ajax({
                url: `/maintenance/comments/${commentId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Hapus komentar dari DOM dengan animasi fade out
                    commentItem.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Reload comments jika perlu
                        const taskId = $('#commentTaskId').val();
                        loadTaskComments(taskId);
                    });
                    
                    // Notifikasi sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Komentar berhasil dihapus',
                        timer: 1500
                    });
                },
                error: function(xhr) {
                    // Hapus indikator loading
                    commentItem.find('.delete-loading').remove();
                    
                    // Notifikasi error
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menghapus komentar',
                        text: xhr.responseJSON?.error || 'Terjadi kesalahan. Silakan coba lagi.'
                    });
                }
            });
        }
    });
});

// Function untuk membuka media di modal
function openCommentMedia(mediaPath, mediaType) {
    if (mediaType.startsWith('image/')) {
        // Tampilkan gambar di modal
        Swal.fire({
            imageUrl: mediaPath,
            imageAlt: 'Media',
            imageWidth: '100%',
            imageHeight: 'auto',
            width: 'auto',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                container: 'media-viewer-container'
            }
        });
    } else if (mediaType.startsWith('video/')) {
        // Tampilkan video di modal
        Swal.fire({
            html: `<video src="${mediaPath}" controls style="max-width: 100%; max-height: 80vh;"></video>`,
            width: 'auto',
            showCloseButton: true,
            showConfirmButton: false,
            didOpen: () => {
                const video = Swal.getHtmlContainer().querySelector('video');
                video.play();
            }
        });
    }
}

// Fungsi untuk handle file upload komentar
function handleCommentFileUpload(files) {
    console.log('Handling file upload, total files:', files.length);
    
    // Buat array untuk menyimpan semua file yang akan diupload
    const uploadQueue = [];
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const url = URL.createObjectURL(file);
        
        console.log(`Processing file ${i}: ${file.name}, type: ${file.type}, size: ${file.size} bytes`);
        
        const fileData = {
            file: file,
            url: url,
            size: file.size,
            type: file.type
        };
        
        uploadQueue.push(fileData);
        
        // Tampilkan preview berdasarkan tipe file
        if (file.type.startsWith('image/')) {
            // Preview untuk gambar
            $('.comment-upload-preview').append(`
                <div class="comment-media-wrapper">
                    <div class="comment-media-item" data-index="${commentUploadedFiles.length}" data-type="file">
                        <img src="${url}" class="img-fluid">
                    </div>
                    <div class="comment-media-actions d-flex mt-2">
                        <a href="javascript:void(0);" class="delete-comment-media me-2">
                            <i class="ri-delete-bin-line"></i>
                        </a>
                        <a href="javascript:void(0);" class="upload-file-btn">
                            <i class="ri-upload-2-line"></i> Upload Now
                        </a>
                    </div>
                </div>
            `);
        } else if (file.type.startsWith('video/')) {
            // Preview untuk video
            $('.comment-upload-preview').append(`
                <div class="comment-media-wrapper">
                    <div class="comment-media-item" data-index="${commentUploadedFiles.length}" data-type="file">
                        <video src="${url}" class="img-fluid" preload="metadata"></video>
                        <div class="comment-video-overlay">
                            <a href="javascript:void(0);" class="btn btn-sm btn-light comment-play-btn">
                                <i class="ri-play-fill"></i>
                            </a>
                        </div>
                    </div>
                    <div class="comment-media-actions d-flex mt-2">
                        <a href="javascript:void(0);" class="delete-comment-media me-2">
                            <i class="ri-delete-bin-line"></i>
                        </a>
                        <a href="javascript:void(0);" class="upload-file-btn">
                            <i class="ri-upload-2-line"></i> Upload Now
                        </a>
                    </div>
                </div>
            `);
        } else {
            // Preview untuk dokumen lainnya
            let fileIcon = 'ri-file-text-line';
            let colorClass = 'text-secondary';
            
            if (file.type.includes('pdf')) {
                fileIcon = 'ri-file-pdf-line';
                colorClass = 'text-danger';
            } else if (file.type.includes('word')) {
                fileIcon = 'ri-file-word-line';
                colorClass = 'text-primary';
            } else if (file.type.includes('excel')) {
                fileIcon = 'ri-file-excel-line';
                colorClass = 'text-success';
            }
            
            $('.comment-upload-preview').append(`
                <div class="comment-media-wrapper">
                    <div class="comment-media-item" data-index="${commentUploadedFiles.length}" data-type="file">
                        <div class="d-flex align-items-center justify-content-center bg-light" style="height: 150px;">
                            <i class="${fileIcon} ${colorClass}" style="font-size: 40px;"></i>
                        </div>
                        <div class="file-name text-center mt-1 small">
                            ${file.name} (${(file.size / 1024).toFixed(1)} KB)
                        </div>
                    </div>
                    <div class="comment-media-actions d-flex mt-2 justify-content-center">
                        <a href="javascript:void(0);" class="delete-comment-media me-2">
                            <i class="ri-delete-bin-line"></i>
                        </a>
                        <a href="javascript:void(0);" class="upload-file-btn">
                            <i class="ri-upload-2-line"></i> Upload Now
                        </a>
                    </div>
                </div>
            `);
        }
        
        // Tambahkan file ke array
        commentUploadedFiles.push(fileData);
    }
    
    console.log('Files added to array. Total uploaded files:', commentUploadedFiles.length);
    
    // Jika user hanya memilih satu file, upload langsung
    if (files.length === 1) {
        submitCommentWithFile(commentUploadedFiles[commentUploadedFiles.length - 1]);
    }
}

// Fungsi inisialisasi kamera untuk comment
function initializeCommentCamera(isVideo = false) {
        if (commentActiveStream) {
        commentActiveStream.getTracks().forEach(track => track.stop());
    }

    const constraints = {
        video: {
            facingMode: 'environment',
            width: { ideal: 1280 },
            height: { ideal: 720 }
        },
        audio: isVideo // Aktifkan audio jika mode video
    };

    navigator.mediaDevices.getUserMedia(constraints)
        .then(function(stream) {
            commentActiveStream = stream;
            
            // Update modal title
            $('#commentCameraModalTitle').text(isVideo ? 'Record Video' : 'Take Photo');
            
            // Buat modal content
            const modalContent = `
                <div class="comment-camera-container">
                    <video id="commentCameraPreview" autoplay playsinline style="width: 100%; max-height: 60vh; object-fit: cover;"></video>
                    <div class="camera-controls mt-3 d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-primary" id="commentSwitchCamera">
                            <i class="ri-camera-switch-line"></i>
                        </button>
                        <button type="button" class="btn btn-success" id="commentCaptureButton">
                            <i class="ri-camera-line"></i> Capture
                        </button>
                        ${isVideo ? `
                            <button type="button" class="btn btn-danger d-none" id="commentStopRecording">
                                <i class="ri-stop-circle-line"></i> Stop
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;
            
            $('#commentCameraModalBody').html(modalContent);
            
            // Set video source
            document.getElementById('commentCameraPreview').srcObject = stream;
            
            // Show modal
            $('#commentCameraModal').modal('show');
            
            // Event handlers for camera controls
            
            // Switch camera
            $('#commentSwitchCamera').on('click', function() {
                const currentFacingMode = constraints.video.facingMode;
                constraints.video.facingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
                
                // Re-initialize camera
                $('#commentCameraModal').modal('hide');
                setTimeout(() => {
                    initializeCommentCamera(isVideo);
                }, 300);
            });
            
            // Capture button for photo/video
            $('#commentCaptureButton').on('click', function() {
                if (isVideo) {
                    // Start recording video
                    startCommentVideoRecording(stream);
                } else {
                    // Capture photo
                    captureCommentPhoto();
                }
            });
            
            // Stop recording button for video
            $(document).on('click', '#commentStopRecording', function() {
                stopCommentVideoRecording();
            });
        })
        .catch(function(err) {
            console.error('Error accessing camera:', err);
            Swal.fire({
                icon: 'error',
                title: 'Camera Error',
                text: 'Could not access the camera. Please check permissions.'
            });
        });
}

// Fungsi langsung submit foto setelah capture
function submitCommentWithMedia(photo) {
    console.log('Submitting comment with captured photo...');
        
        const taskId = $('#commentTaskId').val();
        const commentText = $('#commentText').val().trim();
        
    // Buat FormData baru
        const formData = new FormData();
        formData.append('task_id', taskId);
    formData.append('comment', commentText || 'Photo comment');
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
    // Tambahkan foto sebagai foto biasa (gunakan nama seperti di create task)
    if (photo && photo.blob) {
        console.log(`Adding photo (${photo.blob.size} bytes) to form data`);
        formData.append('photos[]', photo.blob, `photo_${Date.now()}.jpg`);
    }
    
    // Debug isi formData
    console.log('Form data contents:');
    for (let pair of formData.entries()) {
        console.log(`- ${pair[0]}: ${pair[1] instanceof Blob ? 'Blob ' + pair[1].size + ' bytes' : pair[1]}`);
    }
    
    // Tampilkan loading
        $('#commentForm button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
        
    // Lakukan request dengan fetch API
    fetch('/maintenance/comments', {
            method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
                // Reset form
                $('#commentText').val('');
                $('.comment-capture-preview').empty();
                
        // Reset array
                commentCapturedPhotos = [];
                
                // Reload comments
                loadTaskComments(taskId);
                
        // Auto-scroll to top setelah reload comments (jika dibutuhkan tambahan)
        setTimeout(function() {
            $('.comments-list').scrollTop(0);
            console.log('Auto-scrolled to top to show newest comments');
        }, 400);
                
                // Enable form kembali
                $('#commentForm button[type="submit"]').prop('disabled', false).html('<i class="ri-send-plane-fill me-1"></i> Add Comment');
                
        // Success alert
                Swal.fire({
                    icon: 'success',
                    title: 'Comment Added',
            text: 'Your comment with photo has been added successfully',
                    timer: 2000,
                    showConfirmButton: false
                });
    })
    .catch(error => {
        console.error('Error:', error);
                
                // Enable form kembali
                $('#commentForm button[type="submit"]').prop('disabled', false).html('<i class="ri-send-plane-fill me-1"></i> Add Comment');
                
        // Error alert
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
            text: 'Failed to add comment. Please try again or check console for details.'
        });
    });
}

// Modifikasi fungsi captureCommentPhoto() untuk langsung submit setelah capture
function captureCommentPhoto() {
    const video = document.getElementById('commentCameraPreview');
    if (!video) {
        console.error('Video element not found');
        return;
    }
    
    console.log('Capturing photo from video:', {
        videoWidth: video.videoWidth,
        videoHeight: video.videoHeight
    });
    
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    
    // Draw video frame to canvas
    try {
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        console.log('Image drawn to canvas successfully');
    } catch (e) {
        console.error('Error drawing image to canvas:', e);
        return;
    }
    
    // Create blob dengan callback
    canvas.toBlob(function(blob) {
        console.log('Photo captured - blob size:', blob.size, 'bytes, type:', blob.type);
        
        // Test blob akses
        const testUrl = URL.createObjectURL(blob);
        console.log('Blob test: test URL created -', testUrl);
        
        const url = URL.createObjectURL(blob);
        const index = commentCapturedPhotos.length;
        
        // Simpan ke array
        const photoData = {
            blob: blob,
            url: url,
            size: blob.size
        };
        commentCapturedPhotos.push(photoData);
        
        console.log('Photo added to array. Total captured photos:', commentCapturedPhotos.length);
        
        // Tambahkan preview
        addPhotoPreview(url, index);
        
        // Tutup camera modal
        $('#commentCameraModal').modal('hide');
        
        // PENTING: LANGSUNG SUBMIT SETELAH CAPTURE (skip form submit normal)
        submitCommentWithMedia(photoData);
        
    }, 'image/jpeg', 0.8);
}

// Modifikasi preview HTML untuk mencakup tombol upload
function addPhotoPreview(url, index) {
        $('.comment-capture-preview').append(`
            <div class="comment-media-wrapper">
                <div class="comment-media-item" data-index="${index}" data-type="photo">
                    <img src="${url}" class="img-fluid">
                </div>
            <div class="comment-media-actions d-flex">
                <a href="javascript:void(0);" class="delete-comment-media me-2">
                    <i class="ri-delete-bin-line"></i>
                </a>
                <a href="javascript:void(0);" class="upload-photo-btn">
                    <i class="ri-upload-2-line"></i> Upload Now
                </a>
            </div>
            </div>
        `);
}

// Fungsi untuk memulai recording video di comment
function startCommentVideoRecording(stream) {
    commentRecordedChunks = [];
    commentMediaRecorder = new MediaRecorder(stream);
    
    commentMediaRecorder.ondataavailable = function(e) {
        if (e.data.size > 0) {
            commentRecordedChunks.push(e.data);
        }
    };
    
    commentMediaRecorder.onstop = function() {
        // Create blob from recorded chunks
        const blob = new Blob(commentRecordedChunks, { type: 'video/webm' });
        console.log('Video recording stopped - blob size:', blob.size, 'bytes, type:', blob.type);
        
        const url = URL.createObjectURL(blob);
        const index = commentCapturedVideos.length;
        
        // Add to captured videos array
        const videoData = {
            blob: blob,
            url: url,
            size: blob.size
        };
        
        commentCapturedVideos.push(videoData);
        console.log('Video added to array. Total captured videos:', commentCapturedVideos.length);
        
        // Add to preview
        $('.comment-capture-preview').append(`
            <div class="comment-media-wrapper">
                <div class="comment-media-item" data-index="${index}" data-type="video">
                    <video src="${url}" class="img-fluid"></video>
                    <div class="comment-video-overlay">
                        <a href="javascript:void(0);" class="btn btn-sm btn-light comment-play-btn">
                            <i class="ri-play-fill"></i>
                        </a>
                    </div>
                </div>
                <div class="comment-media-actions d-flex mt-2">
                    <a href="javascript:void(0);" class="delete-comment-media me-2">
                        <i class="ri-delete-bin-line"></i>
                    </a>
                    <a href="javascript:void(0);" class="upload-video-btn">
                        <i class="ri-upload-2-line"></i> Upload Now
                    </a>
                </div>
            </div>
        `);
        
        console.log('Video preview HTML added to DOM');
        
        // Close modal
        $('#commentCameraModal').modal('hide');
        
        // Langsung upload video
        submitCommentWithVideo(videoData);
    };
    
    // Start recording
    commentMediaRecorder.start();
    console.log('Video recording started');
    
    // Update UI
    $('#commentCaptureButton').addClass('d-none');
    $('#commentStopRecording').removeClass('d-none');
}

// Fungsi untuk stop recording di comment
function stopCommentVideoRecording() {
    if (commentMediaRecorder && commentMediaRecorder.state !== 'inactive') {
        console.log('Stopping video recording...');
        commentMediaRecorder.stop();
        
        // Update UI
        $('#commentStopRecording').addClass('d-none');
        $('#commentCaptureButton').removeClass('d-none');
    }
}

// Fungsi untuk submit comment dengan video
function submitCommentWithVideo(video) {
    console.log('Submitting comment with captured video...');
    
    const taskId = $('#commentTaskId').val();
    const commentText = $('#commentText').val().trim();
    
    // Buat FormData baru
    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('comment', commentText || 'Video comment');
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    
    // Tambahkan video (gunakan nama field seperti di create task)
    if (video && video.blob) {
        console.log(`Adding video (${video.blob.size} bytes) to form data`);
        formData.append('videos[]', video.blob, `video_${Date.now()}.webm`);
    }
    
    // Debug isi formData
    console.log('Form data contents:');
    for (let pair of formData.entries()) {
        console.log(`- ${pair[0]}: ${pair[1] instanceof Blob ? 'Blob ' + pair[1].size + ' bytes' : pair[1]}`);
    }
    
    // Tampilkan loading
    $('#commentForm button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
    
    // Send dengan fetch API
    fetch('/maintenance/comments', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        // Reset form
        $('#commentText').val('');
        $('.comment-capture-preview').empty();
        
        // Reset array
        commentCapturedVideos = [];
        
        // Reload comments
        loadTaskComments(taskId);
        
        // Auto-scroll to top setelah reload comments
        setTimeout(function() {
            $('.comments-list').scrollTop(0);
            console.log('Auto-scrolled to top to show newest comments');
        }, 400);
        
        // Enable form kembali
        $('#commentForm button[type="submit"]').prop('disabled', false).html('<i class="ri-send-plane-fill me-1"></i> Add Comment');
        
        // Success alert
        Swal.fire({
            icon: 'success',
            title: 'Comment Added',
            text: 'Your comment with video has been added successfully',
            timer: 2000,
            showConfirmButton: false
        });
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Enable form kembali
        $('#commentForm button[type="submit"]').prop('disabled', false).html('<i class="ri-send-plane-fill me-1"></i> Add Comment');
        
        // Error alert
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to add comment with video. Please try again.'
        });
    });
}

// Fungsi untuk submit comment dengan file
function submitCommentWithFile(fileData) {
    console.log('Submitting comment with file:', fileData);
    
    const taskId = $('#commentTaskId').val();
    const commentText = $('#commentText').val().trim();
    
    // Buat FormData baru
    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('comment', commentText || 'File upload');
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    
    // Tentukan jenis file dan pilih field yang sesuai
    if (fileData && fileData.file) {
        const file = fileData.file;
        console.log(`Adding file: ${file.name} (${file.size} bytes, ${file.type})`);
        
        if (file.type.startsWith('image/')) {
            // Gambar - gunakan photos[]
            formData.append('photos[]', file, file.name);
            console.log('File added as photo');
        } else if (file.type.startsWith('video/')) {
            // Video - gunakan videos[]
            formData.append('videos[]', file, file.name);
            console.log('File added as video');
        } else {
            // Dokumen - gunakan documents[]
            formData.append('documents[]', file, file.name);
            console.log('File added as document');
        }
    }
    
    // Debug isi formData
    console.log('Form data contents:');
    for (let pair of formData.entries()) {
        console.log(`- ${pair[0]}: ${pair[1] instanceof Blob ? 'Blob ' + pair[1].size + ' bytes' : pair[1]}`);
    }
    
    // Tampilkan loading
    $('#commentForm button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
    
    // Kirim dengan fetch API
    fetch('/maintenance/comments', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        // Reset form
        $('#commentText').val('');
        $('.comment-upload-preview').empty();
        
        // Reset array
        commentUploadedFiles = [];
        
        // Reload comments
        loadTaskComments(taskId);
        
        // Auto-scroll to top setelah reload comments
        setTimeout(function() {
            $('.comments-list').scrollTop(0);
            console.log('Auto-scrolled to top to show newest comments');
        }, 400);
        
        // Enable form kembali
        $('#commentForm button[type="submit"]').prop('disabled', false).html('<i class="ri-send-plane-fill me-1"></i> Add Comment');
        
        // Success alert
        Swal.fire({
            icon: 'success',
            title: 'Comment Added',
            text: 'Your comment with file has been added successfully',
            timer: 2000,
            showConfirmButton: false
        });
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Enable form kembali
        $('#commentForm button[type="submit"]').prop('disabled', false).html('<i class="ri-send-plane-fill me-1"></i> Add Comment');
        
        // Error alert
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to add comment with file. Please try again.'
        });
    });
}

// Ekspos fungsi ke global scope
window.initializeCommentCamera = initializeCommentCamera;
window.openCommentMedia = openCommentMedia;
window.loadTaskComments = loadTaskComments;

// Inisialisasi saat dokumen siap
$(document).ready(function() {
    initMaintenanceComments();
});