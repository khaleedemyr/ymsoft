<div class="modal fade" id="modal-task">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Task Baru</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="task-form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Outlet <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="outlet_id" id="outlet_id" required>
                                    <option value="">Pilih Outlet</option>
                                    @foreach($outlets as $outlet)
                                        <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ruko <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="ruko_id" id="ruko_id" required>
                                    <option value="">Pilih Ruko</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Priority <span class="text-danger">*</span></label>
                                <select class="form-control" name="priority" required>
                                    @foreach(App\Models\MaintenanceTask::PRIORITIES as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Label <span class="text-danger">*</span></label>
                                <select class="form-control" name="label" required>
                                    @foreach(App\Models\MaintenanceTask::LABELS as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Due Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="due_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Assigned To <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="assigned_to" required>
                            <option value="">Pilih Staff</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        width: '100%'
    });

    // Handle Outlet Change - Update Ruko Options
    $('#outlet_id').change(function() {
        var outletId = $(this).val();
        $('#ruko_id').empty().append('<option value="">Pilih Ruko</option>');
        
        if (outletId) {
            $.ajax({
                url: '/get-ruko-by-outlet/' + outletId,
                type: 'GET',
                success: function(data) {
                    $.each(data, function(key, value) {
                        $('#ruko_id').append('<option value="' + value.id_ruko + '">' + value.nama + '</option>');
                    });
                }
            });
        }
    });

    // Handle Form Submit
    $('#task-form').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("maintenance.tasks.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#modal-task').modal('hide');
                    $('#tasks-table').DataTable().ajax.reload();
                    toastr.success('Task berhasil dibuat');
                    $('#task-form')[0].reset();
                } else {
                    toastr.error('Gagal membuat task');
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    toastr.error(value[0]);
                });
            }
        });
    });
});
</script>
@endpush
