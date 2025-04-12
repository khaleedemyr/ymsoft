/**
 * Maintenance Evidence JavaScript
 * Untuk menangani foto & video evidence
 */

const EvidenceApp = (function() {
    // Variabel private dalam closure
    let capturedPhotos = [];
    let capturedVideos = [];
    let currentStream = null;
    let mediaRecorder = null;
    let recordedChunks = [];
    let facingMode = 'environment'; // Default: kamera belakang
    let videoMode = false; // Mode video (true) atau foto (false)
    
    // Flag untuk menandai apakah kamera sedang diakses
    let isCameraAccessInProgress = false;
    
    // Inisialisasi aplikasi
    function init() {
        // Periksa dukungan browser terlebih dahulu
        checkBrowserSupport();
        
        // Setup event listener
        setupEventListeners();
    }
    
    // Setup semua event listener
    function setupEventListeners() {
        // Buka modal evidence saat tombol ditekan
        $(document).on('click', '.capture-evidence, .capture-evidence-btn', function() {
            const taskId = $(this).data('task-id');
            $('#captureEvidenceModal').data('task-id', taskId);
            $('#captureEvidenceModal').modal('show');
        });
        
        // Tombol ambil foto
        $('#capturePhotoBtn').on('click', function() {
            videoMode = false;
            openCamera();
        });
        
        // Tombol rekam video
        $('#startVideoBtn').on('click', function() {
            videoMode = true;
            openCamera();
        });
        
        // Tombol ambil foto dalam modal kamera
        $('#captureBtn').on('click', function() {
            capturePhoto();
        });
        
        // Tombol stop rekam video
        $('#stopVideoBtn').on('click', function() {
            stopRecording();
        });
        
        // Tombol ganti kamera
        $('#switchCameraBtn').on('click', function() {
            switchCamera();
        });
        
        // Tombol simpan evidence
        $('#saveEvidenceBtn').on('click', function() {
            saveEvidence();
        });
        
        // Hentikan kamera saat modal camera ditutup
        $('#cameraModal').on('hidden.bs.modal', function() {
            stopCamera();
        });
        
        // Reset form saat modal evidence ditutup
        $('#captureEvidenceModal').on('hidden.bs.modal', function() {
            resetForm();
        });
        
        // Hapus media dari preview
        $(document).on('click', '.delete-media', function() {
            const type = $(this).data('type');
            const index = $(this).data('index');
            
            if (type === 'photo') {
                // Hapus URL dan blob
                URL.revokeObjectURL(capturedPhotos[index].url);
                capturedPhotos.splice(index, 1);
            } else if (type === 'video') {
                // Hapus URL dan blob
                URL.revokeObjectURL(capturedVideos[index].url);
                capturedVideos.splice(index, 1);
            }
            
            // Hapus element dari DOM
            $(this).closest('.media-preview-item').remove();
            
            // Update index pada preview yang tersisa
            updatePreviewIndices();
        });
        
        // Preview media dalam modal
        $(document).on('click', '.preview-media', function() {
            const type = $(this).data('type');
            const index = $(this).data('index');
            const content = $('#mediaPreviewContent');
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
            
            $('#mediaPreviewModal').modal('show');
        });
    }
    
    // Fungsi untuk memeriksa dukungan browser
    function checkBrowserSupport() {
        // Periksa apakah getUserMedia didukung
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            console.error('Browser tidak mendukung getUserMedia API');
            
            // Tampilkan dialog jika browser tidak mendukung
            $(document).ready(function() {
                // Tambahkan listener khusus untuk tombol evidence
                $(document).on('click', '.capture-evidence, .capture-evidence-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    alert('Browser Anda tidak mendukung akses kamera. Silakan gunakan browser lain seperti Chrome, Firefox, atau Edge terbaru.');
                    return false;
                });
            });
        }
        
        // Periksa jika di iOS WebView yang tidak mendukung getUserMedia
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        const isWebView = /(iPhone|iPod|iPad).*AppleWebKit(?!.*Safari)/i.test(navigator.userAgent);
        
        if (isIOS && isWebView) {
            console.warn('Detected iOS WebView which may not support camera');
            
            $(document).ready(function() {
                // Tambahkan listener khusus untuk tombol evidence
                $(document).on('click', '.capture-evidence, .capture-evidence-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    alert('Untuk menggunakan kamera pada iOS, buka aplikasi melalui browser Safari.');
                    return false;
                });
            });
        }
    }
    
    // Fungsi untuk membuka kamera
    function openCamera() {
        // Cegah multiple call saat masih loading
        if (isCameraAccessInProgress) {
            console.log('Camera access already in progress');
            return;
        }
        
        // Deteksi khusus untuk iOS
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        
        // Jika mode foto di iOS, gunakan metode alternatif
        if (isIOS && !videoMode) {
            // Gunakan input file sebagai fallback untuk iOS
            iOSCameraWorkaround();
            return;
        }

        // Reset kamera jika sudah ada yang aktif
        stopCamera();
        
        // Set flag
        isCameraAccessInProgress = true;
        
        // Tampilkan tombol yang sesuai berdasarkan mode
        if (videoMode) {
            $('#captureBtn').addClass('d-none');
            $('#stopVideoBtn').removeClass('d-none');
        } else {
            $('#captureBtn').removeClass('d-none');
            $('#stopVideoBtn').addClass('d-none');
        }
        
        // Tampilkan modal kamera
        $('#cameraModal').modal('show');
        
        // Aktifkan kamera setelah modal ditampilkan
        $('#cameraModal').on('shown.bs.modal', function(e) {
            startCamera();
            $(this).off('shown.bs.modal'); // Hapus event setelah dipanggil
        });
    }
    
    // Fungsi untuk mengaktifkan kamera
    function startCamera() {
        console.log('Starting camera...');
        
        // Update status
        updateCameraStatus('Memuat kamera...');
        
        // Tambahkan indikator loading
        const videoElement = document.getElementById('cameraStream');
        videoElement.classList.add('loading');
        
        // Deteksi browser
        const isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
        const isFirefox = /Firefox/.test(navigator.userAgent);
        const isEdge = /Edg/.test(navigator.userAgent);
        const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        const isAndroid = /Android/.test(navigator.userAgent);
        
        // Tampilkan peringatan untuk browser tertentu
        if (isSafari || isIOS) {
            showBrowserWarning('Safari dan iOS mungkin memiliki keterbatasan akses kamera.');
        } else if (!isChrome && !isFirefox && !isEdge) {
            showBrowserWarning('Browser yang direkomendasikan: Chrome, Firefox, atau Edge.');
        }
        
        // Tambahkan pesan loading
        const loadingText = $('<div>', {
            class: 'position-absolute top-50 start-50 translate-middle text-white loading-indicator',
            text: 'Memuat kamera...',
            css: {
                zIndex: 10,
                backgroundColor: 'rgba(0,0,0,0.5)',
                padding: '10px',
                borderRadius: '5px'
            }
        });
        
        // Hapus teks loading yang mungkin sudah ada
        $('.loading-indicator').remove();
        
        $(videoElement).parent().append(loadingText);
        
        // Konfigurasi kamera yang berbeda berdasarkan browser
        let constraints = {
            video: {
                facingMode: facingMode,
            },
            audio: videoMode // Audio hanya aktif untuk mode video
        };
        
        // Turunkan resolusi berdasarkan device
        if (isIOS || isSafari) {
            // iOS/Safari sering bermasalah dengan resolusi tinggi
            constraints.video.width = { ideal: 320 };
            constraints.video.height = { ideal: 240 };
        } else if (isAndroid) {
            // Android umumnya menangani resolusi menengah
            constraints.video.width = { ideal: 640 };
            constraints.video.height = { ideal: 480 };
        } else {
            // Browser desktop dapat menangani resolusi lebih tinggi
            constraints.video.width = { ideal: 1280 };
            constraints.video.height = { ideal: 720 };
        }
        
        // Tambahkan timeout untuk mengatasi masalah browser yang tidak responsif
        let cameraTimeout = setTimeout(() => {
            console.error('Camera access timeout after 5 seconds');
            updateCameraStatus('Waktu habis');
            loadingText.text('Waktu habis. Coba refresh browser atau izinkan akses kamera');
            videoElement.classList.remove('loading');
            
            // Reset flag
            isCameraAccessInProgress = false;
            
            // Tambahkan tombol untuk mencoba lagi
            const retryBtn = $('<button>', {
                class: 'btn btn-warning mt-2',
                text: 'Coba Lagi',
                click: function() {
                    $(this).remove();
                    startCamera();
                }
            });
            
            loadingText.append($('<br>'));
            loadingText.append(retryBtn);
            
        }, 5000); // 5 detik timeout
        
        console.log('Requesting camera with constraints:', constraints);
        updateCameraStatus('Meminta izin kamera...');
        
        // Akses kamera dengan penanganan error yang lebih baik
        navigator.mediaDevices.getUserMedia(constraints)
            .then(function(stream) {
                console.log('Camera access successful');
                updateCameraStatus('Kamera aktif');
                
                // Berhasil mendapatkan stream
                clearTimeout(cameraTimeout);
                loadingText.remove();
                
                // Simpan stream untuk digunakan nanti
                currentStream = stream;
                
                // Tampilkan stream pada video element
                videoElement.srcObject = stream;
                videoElement.classList.remove('loading');
                
                // Reset flag
                isCameraAccessInProgress = false;
                
                // Tambahkan event listener untuk memastikan video berhasil diputar
                videoElement.onloadedmetadata = function() {
                    console.log('Video metadata loaded');
                    updateCameraStatus('Video stream siap');
                    
                    // Putar video dengan penanganan error
                    videoElement.play()
                        .then(() => {
                            console.log('Video playback started');
                            // Jika mode video, langsung mulai rekam
                            if (videoMode) {
                                updateCameraStatus('Merekam video...');
                                startRecording(stream);
                            } else {
                                updateCameraStatus('Siap mengambil foto');
                            }
                        })
                        .catch(function(error) {
                            console.error('Error playing video:', error);
                            updateCameraStatus('Error: ' + error.message);
                            alert('Gagal memulai video stream. Error: ' + error.message);
                        });
                };
                
                // Tambahkan event untuk mendeteksi jika stream berhenti secara tiba-tiba
                stream.getVideoTracks()[0].onended = function() {
                    console.log('Video track ended');
                    updateCameraStatus('Koneksi kamera terputus');
                    alert('Koneksi kamera terputus');
                    $('#cameraModal').modal('hide');
                };
            })
            .catch(function(error) {
                // Gagal mengakses kamera
                console.error('Error accessing camera:', error);
                clearTimeout(cameraTimeout);
                
                // Reset flag
                isCameraAccessInProgress = false;
                
                let errorMessage = 'Gagal mengakses kamera. ';
                
                // Tambahkan pesan berdasarkan jenis error
                if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                    errorMessage += 'Anda perlu mengizinkan akses kamera pada browser.';
                    updateCameraStatus('Akses kamera ditolak');
                } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
                    errorMessage += 'Tidak dapat menemukan kamera. Pastikan kamera terhubung.';
                    updateCameraStatus('Kamera tidak ditemukan');
                } else if (error.name === 'NotReadableError' || error.name === 'TrackStartError') {
                    errorMessage += 'Kamera mungkin sedang digunakan oleh aplikasi lain.';
                    updateCameraStatus('Kamera sedang digunakan');
                } else if (error.name === 'OverconstrainedError') {
                    errorMessage += 'Resolusi kamera tidak didukung, coba gunakan resolusi yang lebih rendah.';
                    updateCameraStatus('Resolusi tidak didukung');
                } else {
                    errorMessage += 'Error: ' + error.message;
                    updateCameraStatus('Error: ' + error.message);
                }
                
                loadingText.html(errorMessage + '<br>');
                
                // Tambahkan tombol untuk mencoba lagi
                const retryBtn = $('<button>', {
                    class: 'btn btn-warning mt-2',
                    text: 'Coba Lagi',
                    click: function() {
                        $(this).remove();
                        startCamera();
                    }
                });
                
                // Tambahkan tombol tutup
                const closeBtn = $('<button>', {
                    class: 'btn btn-danger mt-2 ms-2',
                    text: 'Tutup',
                    click: function() {
                        $('#cameraModal').modal('hide');
                    }
                });
                
                loadingText.append(retryBtn);
                loadingText.append(closeBtn);
                
                // Optional: Log informasi browser untuk debugging
                console.log('Browser info:', navigator.userAgent);
                console.log('Camera error detail:', error);
            });
    }
    
    // Fungsi untuk mengambil foto
    function capturePhoto() {
        const videoElement = document.getElementById('cameraStream');
        
        // Cek apakah video element dan stream sudah siap
        if (!videoElement || !videoElement.srcObject) {
            console.error('Video element tidak tersedia atau tidak ada stream');
            return;
        }
        
        // Buat canvas untuk mengambil frame dari video
        const canvas = document.createElement('canvas');
        canvas.width = videoElement.videoWidth;
        canvas.height = videoElement.videoHeight;
        
        // Gambar frame video ke canvas
        const context = canvas.getContext('2d');
        context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
        
        // Konversi ke blob dengan kualitas 0.8
        canvas.toBlob(function(blob) {
            // Buat URL dari blob
            const imageUrl = URL.createObjectURL(blob);
            
            // Simpan blob dan URL
            capturedPhotos.push({
                blob: blob,
                url: imageUrl
            });
            
            // Tambahkan preview
            addPhotoPreview(imageUrl, capturedPhotos.length - 1);
            
            // Tutup modal kamera
            $('#cameraModal').modal('hide');
        }, 'image/jpeg', 0.8);
    }
    
    // Fungsi untuk menambahkan preview foto
    function addPhotoPreview(imageUrl, index) {
        const previewItem = $('<div>', {
            class: 'media-preview-item card',
            'data-type': 'photo',
            'data-index': index,
            css: {
                width: '150px'
            }
        });
        
        const cardBody = $('<div>', {
            class: 'card-body p-2'
        });
        
        const img = $('<img>', {
            src: imageUrl,
            class: 'img-fluid rounded mb-2',
            css: {
                height: '100px',
                objectFit: 'cover',
                width: '100%',
                cursor: 'pointer'
            }
        });
        
        const previewBtn = $('<button>', {
            type: 'button',
            class: 'btn btn-sm btn-info preview-media',
            'data-type': 'photo',
            'data-index': index,
            html: '<i class="ri-eye-line"></i>'
        });
        
        const deleteBtn = $('<button>', {
            type: 'button',
            class: 'btn btn-sm btn-danger ms-1 delete-media',
            'data-type': 'photo',
            'data-index': index,
            html: '<i class="ri-delete-bin-line"></i>'
        });
        
        const btnGroup = $('<div>', {
            class: 'd-flex justify-content-between'
        }).append(previewBtn, deleteBtn);
        
        cardBody.append(img, btnGroup);
        previewItem.append(cardBody);
        
        // Event untuk preview saat klik gambar
        img.on('click', function() {
            previewBtn.trigger('click');
        });
        
        // Tambahkan ke container
        $('.capture-preview').append(previewItem);
    }
    
    // Fungsi untuk mulai rekam video dengan penanganan error yang lebih baik
    function startRecording(stream) {
        // Reset recorded chunks
        recordedChunks = [];
        
        updateCameraStatus('Memulai rekaman video...');
        
        // Deteksi format yang didukung
        const mimeTypes = [
            'video/webm;codecs=vp9,opus',
            'video/webm;codecs=vp8,opus',
            'video/webm;codecs=h264,opus',
            'video/webm',
            'video/mp4'
        ];
        
        let options = null;
        
        // Cari format yang didukung
        for (let i = 0; i < mimeTypes.length; i++) {
            if (MediaRecorder.isTypeSupported(mimeTypes[i])) {
                options = { mimeType: mimeTypes[i] };
                break;
            }
        }
        
        try {
            // Buat MediaRecorder dengan format yang didukung
            mediaRecorder = new MediaRecorder(stream, options);
            console.log('MediaRecorder created with', options ? options.mimeType : 'default');
            updateCameraStatus('Merekam: ' + (options ? options.mimeType : 'format default'));
        } catch (e) {
            console.error('MediaRecorder error:', e);
            updateCameraStatus('Error rekaman: ' + e.message);
            alert('Browser Anda tidak mendukung perekaman video: ' + e.message);
            $('#cameraModal').modal('hide');
            return;
        }
        
        // Event untuk menyimpan data rekaman
        mediaRecorder.ondataavailable = function(e) {
            if (e.data && e.data.size > 0) {
                recordedChunks.push(e.data);
            }
        };
        
        // Event untuk menangani error
        mediaRecorder.onerror = function(e) {
            console.error('MediaRecorder error:', e);
            updateCameraStatus('Error rekaman: ' + e.error);
            alert('Error saat merekam video: ' + e.error);
            stopRecording();
        };
        
        // Event saat rekaman selesai
        mediaRecorder.onstop = function() {
            if (recordedChunks.length === 0) {
                console.error('No recorded chunks available');
                alert('Tidak ada data video yang terekam');
                return;
            }
            
            try {
                // Buat blob dari chunks
                const videoBlob = new Blob(recordedChunks, {
                    type: options ? options.mimeType : 'video/webm'
                });
                
                // Buat URL dari blob
                const videoUrl = URL.createObjectURL(videoBlob);
                
                // Simpan blob dan URL
                capturedVideos.push({
                    blob: videoBlob,
                    url: videoUrl,
                    type: options ? options.mimeType : 'video/webm'
                });
                
                // Tambahkan preview
                addVideoPreview(videoUrl, capturedVideos.length - 1);
                
                // Tutup modal kamera
                $('#cameraModal').modal('hide');
            } catch (e) {
                console.error('Error creating video blob:', e);
                alert('Error saat membuat video: ' + e.message);
            }
        };
        
        // Mulai rekam dengan chunk 1 detik
        try {
            mediaRecorder.start(1000);
            console.log('MediaRecorder started');
        } catch (e) {
            console.error('Error starting MediaRecorder:', e);
            alert('Error saat memulai rekaman: ' + e.message);
        }
    }
    
    // Fungsi untuk stop rekam video
    function stopRecording() {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }
    }
    
    // Fungsi untuk menambahkan preview video
    function addVideoPreview(videoUrl, index) {
        const previewItem = $('<div>', {
            class: 'media-preview-item card',
            'data-type': 'video',
            'data-index': index,
            css: {
                width: '150px'
            }
        });
        
        const cardBody = $('<div>', {
            class: 'card-body p-2'
        });
        
        // Buat video thumbnail dengan durasi 0
        const video = $('<video>', {
            src: videoUrl,
            class: 'img-fluid rounded mb-2',
            css: {
                height: '100px',
                objectFit: 'cover',
                width: '100%',
                cursor: 'pointer'
            }
        });
        
        // Tambahkan badge video
        const videoBadge = $('<span>', {
            class: 'position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger',
            html: '<i class="ri-video-line"></i>',
            css: {
                zIndex: 1
            }
        });
        
        const videoThumb = $('<div>', {
            class: 'position-relative'
        }).append(video, videoBadge);
        
        const previewBtn = $('<button>', {
            type: 'button',
            class: 'btn btn-sm btn-info preview-media',
            'data-type': 'video',
            'data-index': index,
            html: '<i class="ri-play-line"></i>'
        });
        
        const deleteBtn = $('<button>', {
            type: 'button',
            class: 'btn btn-sm btn-danger ms-1 delete-media',
            'data-type': 'video',
            'data-index': index,
            html: '<i class="ri-delete-bin-line"></i>'
        });
        
        const btnGroup = $('<div>', {
            class: 'd-flex justify-content-between'
        }).append(previewBtn, deleteBtn);
        
        cardBody.append(videoThumb, btnGroup);
        previewItem.append(cardBody);
        
        // Event untuk preview saat klik video
        video.on('click', function() {
            previewBtn.trigger('click');
        });
        
        // Tambahkan ke container
        $('.capture-preview').append(previewItem);
        
        // Set frame pada 1 detik untuk thumbnail
        setTimeout(() => {
            video[0].currentTime = 1;
        }, 100);
    }
    
    // Fungsi untuk update index pada preview setelah hapus
    function updatePreviewIndices() {
        // Update index pada elemen foto
        $('.media-preview-item[data-type="photo"]').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('.preview-media, .delete-media').attr('data-index', index);
        });
        
        // Update index pada elemen video
        $('.media-preview-item[data-type="video"]').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('.preview-media, .delete-media').attr('data-index', index);
        });
    }
    
    // Fungsi untuk ganti kamera (depan/belakang)
    function switchCamera() {
        // Toggle facingMode
        facingMode = facingMode === 'environment' ? 'user' : 'environment';
        
        // Restart kamera
        if (currentStream) {
            stopCamera();
            startCamera();
        }
    }
    
    // Fungsi untuk menghentikan kamera
    function stopCamera() {
        console.log('Stopping camera...');
        
        // Reset flag
        isCameraAccessInProgress = false;
        
        // Hentikan semua track pada stream
        if (currentStream) {
            try {
                currentStream.getTracks().forEach(track => {
                    console.log('Stopping track:', track.kind);
                    track.stop();
                });
                currentStream = null;
            } catch (e) {
                console.error('Error stopping camera:', e);
            }
        }
        
        // Reset video element
        const videoElement = document.getElementById('cameraStream');
        if (videoElement) {
            try {
                if (videoElement.srcObject) {
                    videoElement.srcObject = null;
                }
                videoElement.classList.remove('loading');
                // Hapus teks loading jika ada
                $('.loading-indicator').remove();
            } catch (e) {
                console.error('Error resetting video element:', e);
            }
        }
        
        // Hentikan MediaRecorder jika aktif
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            try {
                mediaRecorder.stop();
            } catch (e) {
                console.error('Error stopping media recorder:', e);
            }
        }
    }
    
    // Fungsi untuk menyimpan evidence
    function saveEvidence() {
        // Ambil task ID dan notes
        const taskId = $('#captureEvidenceModal').data('task-id');
        // Gunakan document.getElementById alih-alih jQuery untuk konsistensi
        const notes = document.getElementById('captureEvidenceNotes').value;
        
        console.log('saveEvidence() dipanggil dengan:');
        console.log('- taskId:', taskId);
        console.log('- notes:', notes);
        console.log('- notes.length:', notes ? notes.length : 0);
        console.log('- capturedPhotos:', capturedPhotos.length);
        console.log('- capturedVideos:', capturedVideos.length);
        
        // Validasi minimal ada 1 foto atau video
        if (capturedPhotos.length === 0 && capturedVideos.length === 0) {
            alert('Minimal harus ada 1 foto atau video sebagai evidence.');
            return;
        }
        
        // Debug HTML untuk melihat apakah elemen notes ada
        console.log('Evidence notes element exists:', document.getElementById('captureEvidenceNotes') !== null);
        console.log('Element HTML:', document.getElementById('captureEvidenceNotes') ? document.getElementById('captureEvidenceNotes').outerHTML : 'not found');
        
        // Siapkan form data untuk upload
        const formData = new FormData();
        formData.append('task_id', taskId);
        // Pastikan notes tidak null dengan menggunakan empty string sebagai fallback
        formData.append('notes', notes || '');
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        // Log formData untuk debugging
        console.log('FormData entries:');
        // Untuk keperluan debugging, cetak semua entry
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + (typeof pair[1] === 'string' ? pair[1] : '[Object File]'));
        }
        
        // Tambahkan foto ke form data
        capturedPhotos.forEach((photo, index) => {
            formData.append(`photos[${index}]`, photo.blob, `photo_${index}.jpg`);
        });
        
        // Tambahkan video ke form data
        capturedVideos.forEach((video, index) => {
            formData.append(`videos[${index}]`, video.blob, `video_${index}.webm`);
        });
        
        // Tampilkan loading
        Swal.fire({
            title: 'Menyimpan Evidence',
            text: 'Mohon tunggu...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Kirim ke server
        $.ajax({
            url: '/maintenance/kanban/upload-evidence',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Evidence berhasil disimpan:', response);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Evidence berhasil disimpan'
                });
                
                // Reset form dan tutup modal
                resetForm();
                $('#captureEvidenceModal').modal('hide');
                
                // Reload halaman jika diperlukan
                if (response.reload) {
                    window.location.reload();
                }
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
    
    // Fungsi untuk reset form
    function resetForm() {
        // Hapus semua URL object
        capturedPhotos.forEach(photo => URL.revokeObjectURL(photo.url));
        capturedVideos.forEach(video => URL.revokeObjectURL(video.url));
        
        // Reset array
        capturedPhotos = [];
        capturedVideos = [];
        
        // Bersihkan preview
        $('.capture-preview').empty();
        
        // Reset notes
        $('#captureEvidenceNotes').val('');
    }
    
    // Fungsi untuk kompatibilitas dengan kode existing
    function initializeCamera(isVideo = false) {
        videoMode = isVideo;
        openCamera();
    }
    
    // Fungsi compatibility untuk capturePhoto
    function doCapture() {
        capturePhoto();
    }
    
    // Fungsi compatibility untuk stopVideoRecording
    function stopVideoRecording() {
        stopRecording();
    }
    
    // Fungsi untuk mencoba metode akses kamera alternatif untuk iOS
    function iOSCameraWorkaround() {
        // Buat input file untuk memilih gambar dari galeri atau kamera
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';
        fileInput.capture = 'camera'; // Gunakan kamera jika tersedia
        
        // Tambahkan event listener untuk perubahan file
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Konversi ke blob
                const blob = new Blob([file], { type: file.type });
                const imageUrl = URL.createObjectURL(blob);
                
                // Simpan ke array foto
                capturedPhotos.push({
                    blob: blob,
                    url: imageUrl
                });
                
                // Tambahkan ke preview
                addPhotoPreview(imageUrl, capturedPhotos.length - 1);
            }
        });
        
        // Klik input file
        fileInput.click();
    }
    
    // Fungsi untuk memperbarui status kamera
    function updateCameraStatus(status) {
        const statusElement = document.getElementById('cameraStatus');
        if (statusElement) {
            statusElement.textContent = status;
        }
    }
    
    // Fungsi untuk menampilkan peringatan browser
    function showBrowserWarning(message) {
        const warningElement = document.getElementById('browserSupportWarning');
        const messageElement = document.getElementById('browserSupportMessage');
        
        if (warningElement && messageElement) {
            messageElement.textContent = message;
            warningElement.classList.remove('d-none');
        }
    }
    
    // Return API publik
    return {
        init,
        // Untuk kompatibilitas kode lama
        initializeCamera,
        capturePhoto: doCapture,
        stopVideoRecording
    };
})();

// Inisialisasi saat dokumen siap
$(document).ready(function() {
    EvidenceApp.init();
});

// Compatibility dengan kode lama
window.initializeCamera = EvidenceApp.initializeCamera;
window.capturePhoto = EvidenceApp.capturePhoto;
window.stopVideoRecording = EvidenceApp.stopVideoRecording; 