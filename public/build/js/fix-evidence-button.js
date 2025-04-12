/**
 * Fix untuk tombol evidence agar menggunakan modal camera capture
 */

(function() {
    console.log('Fix evidence button script loaded');
    
    // Tunggu dokumen siap
    document.addEventListener('DOMContentLoaded', function() {
        // Hapus event handler yang ada dan tambahkan event handler baru
        function fixEvidenceButtons() {
            console.log('Fixing evidence buttons...');
            
            const buttons = document.querySelectorAll('.new-evidence-btn');
            console.log(`Found ${buttons.length} evidence buttons`);
            
            if (buttons.length === 0) {
                // Coba lagi nanti jika belum ada tombol
                setTimeout(fixEvidenceButtons, 1000);
                return;
            }
            
            // Hapus semua event handler lama
            buttons.forEach(function(button) {
                // Clone node untuk menghapus semua event handler
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
                
                // Tambahkan event handler baru
                newButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const taskId = this.dataset.taskId;
                    console.log(`Evidence button clicked for task ID: ${taskId}`);
                    
                    // Buka modal camera
                    openCameraModal(taskId);
                });
            });
        }
        
        // Buka modal camera
        function openCameraModal(taskId) {
            // Cek apakah modal ada
            let modal = document.getElementById('cameraCaptureModal');
            
            if (!modal) {
                console.error('Camera Capture Modal not found');
                alert('Sistem tidak dapat memuat modal camera. Silakan refresh halaman.');
                return;
            }
            
            // Set task ID
            document.getElementById('cameraTaskId').value = taskId;
            
            // Reset form
            resetCameraForm();
            
            // Tampilkan modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            
            // Start camera
            setTimeout(() => {
                startCamera();
            }, 500);
        }
        
        // Reset form camera
        function resetCameraForm() {
            // Reset captured media
            document.getElementById('photoGallery').innerHTML = '';
            document.getElementById('videoGallery').innerHTML = '';
            document.getElementById('photoCount').textContent = '0';
            document.getElementById('videoCount').textContent = '0';
            document.getElementById('cameraNotes').value = '';
            document.getElementById('saveCameraBtn').disabled = true;
        }
        
        // Start camera dengan mode foto default
        function startCamera() {
            const video = document.getElementById('cameraVideo');
            if (!video) return;
            
            // Stop stream yang sudah ada jika ada
            if (window.currentStream) {
                window.currentStream.getTracks().forEach(track => track.stop());
            }
            
            // Set constraints untuk kamera belakang
            const constraints = {
                video: {
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            };
            
            // Coba akses kamera
            navigator.mediaDevices.getUserMedia(constraints)
                .then(function(stream) {
                    window.currentStream = stream;
                    video.srcObject = stream;
                    
                    document.getElementById('cameraStatus').textContent = 'Kamera siap';
                    setTimeout(() => {
                        document.getElementById('cameraStatus').classList.add('d-none');
                    }, 2000);
                })
                .catch(function(err) {
                    console.error('Error accessing camera:', err);
                    document.getElementById('cameraStatus').textContent = 'Error: ' + err.message;
                    document.getElementById('cameraStatus').classList.remove('d-none');
                });
        }
        
        // Fix tombol awal dan pantau perubahan DOM
        fixEvidenceButtons();
        
        // Pasang observer untuk mendeteksi tombol evidence baru yang mungkin ditambahkan
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    fixEvidenceButtons();
                }
            });
        });
        
        // Start observing
        observer.observe(document.body, { childList: true, subtree: true });
        
        // Cek berkala untuk tombol baru
        setInterval(fixEvidenceButtons, 3000);
    });
})();
