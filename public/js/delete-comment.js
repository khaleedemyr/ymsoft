// Add event listener for delete comment button
$(document).ready(function() {
    // Log untuk debugging
    console.log('Delete comment script loaded');
    console.log('Current user ID:', $('#current-user-id').val());
    
    // Event delegasi untuk tombol delete comment
    $(document).on('click', '.delete-comment-btn', function() {
        const commentId = $(this).data('comment-id');
        const commentItem = $(`#comment-${commentId}`);
        
        console.log('Delete button clicked for comment ID:', commentId);
        
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
                        console.log('Comment deleted successfully:', response);
                        
                        // Hapus komentar dari DOM dengan animasi fade out
                        commentItem.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Reload comments jika perlu
                            const taskId = $('#commentTaskId').val();
                            if (typeof loadTaskComments === 'function') {
                                loadTaskComments(taskId);
                            }
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
                        console.error('Error deleting comment:', xhr);
                        
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
}); 