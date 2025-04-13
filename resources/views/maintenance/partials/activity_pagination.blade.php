<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        Menampilkan {{ $activities->firstItem() ?? 0 }} - {{ $activities->lastItem() ?? 0 }} dari {{ $activities->total() }} aktivitas
    </div>
    <div>
        {{ $activities->links('pagination::bootstrap-5') }}
    </div>
</div>
