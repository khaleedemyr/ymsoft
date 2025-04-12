<div class="modal fade" id="directEvidenceModal" tabindex="-1" aria-labelledby="directEvidenceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="directEvidenceModalLabel">Upload Evidence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="directEvidenceForm" action="{{ route('maintenance.kanban.upload-evidence') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="directEvidenceTaskId" name="task_id">
                    
                    <div class="mb-3">
                        <label for="photoFiles" class="form-label">Upload Foto</label>
                        <input type="file" class="form-control" id="photoFiles" name="photos[]" accept="image/*" multiple>
                        <small class="text-muted">Anda dapat memilih beberapa foto sekaligus</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="videoFiles" class="form-label">Upload Video</label>
                        <input type="file" class="form-control" id="videoFiles" name="videos[]" accept="video/*" multiple>
                        <small class="text-muted">Anda dapat memilih beberapa video sekaligus</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="directEvidenceNotes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="directEvidenceNotes" name="notes" rows="3" placeholder="Tambahkan catatan untuk evidence ini..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="directSaveEvidenceBtn">Simpan Evidence</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set up save button handler
    const saveBtn = document.getElementById('directSaveEvidenceBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            const form = document.getElementById('directEvidenceForm');
            const photoFiles = document.getElementById('photoFiles');
            const videoFiles = document.getElementById('videoFiles');
            
            // Validate form
            if ((photoFiles.files.length === 0 && videoFiles.files.length === 0)) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Mohon upload setidaknya satu foto atau video',
                    icon: 'error'
                });
                return;
            }
            
            // Submit form
            form.submit();
        });
    }
});
</script>
