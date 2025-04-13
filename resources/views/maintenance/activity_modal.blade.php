<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Aktivitas Maintenance</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div id="activity-list">
            @foreach($activities as $activity)
                <!-- Isi item aktivitas -->
            @endforeach
        </div>
        <!-- Pagination -->
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let searchTimeout;
        let currentRequest = null;
        
        function loadActivities(page = 1) {
            if (currentRequest) {
                currentRequest.abort();
            }
            
            let search = $('#activity-search').val();
            let dateFrom = $('#date-from').val();
            let dateTo = $('#date-to').val();
            
            $('#activity-list').html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat aktivitas...</p></div>');
            
            currentRequest = $.ajax({
                url: '{{ route("maintenance.dashboard.activities") }}',
                method: 'GET',
                data: {
                    search: search,
                    date_from: dateFrom,
                    date_to: dateTo,
                    page: page
                },
                dataType: 'json',
                success: function(response) {
                    $('#activity-list').html(response.html);
                    $('#pagination-container').html(response.pagination);
                    
                    // Reinitialize pagination links
                    $('#pagination-container .pagination a').on('click', function(e) {
                        e.preventDefault();
                        let page = $(this).attr('href').split('page=')[1];
                        loadActivities(page);
                    });
                },
                error: function() {
                    $('#activity-list').html('<div class="alert alert-danger">Gagal memuat data aktivitas</div>');
                }
            });
        }
        
        // Search with debounce
        $('#activity-search').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                loadActivities();
            }, 500);
        });
        
        // Date filters
        $('#date-from, #date-to').on('change', function() {
            loadActivities();
        });
        
        // Initialize pagination
        $('#pagination-container .pagination a').on('click', function(e) {
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            loadActivities(page);
        });
    });
</script>
