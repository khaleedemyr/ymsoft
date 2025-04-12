@extends('layouts.master')

@section('title')
    @lang('translation.packing-lists.title')
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div>
                @component('components.breadcrumb')
                    @slot('li_1')
                        Warehouse
                    @endslot
                    @slot('title')
                        Packing List
                    @endslot
                @endcomponent

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="card-title mb-0 flex-grow-1">@lang('translation.packing-lists.title')</h5>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('packing-lists.create') }}" class="btn btn-primary">
                                        <i class="ri-add-line align-bottom me-1"></i> @lang('translation.packing-lists.create')
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-nowrap" id="packingListTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Nomor PL</th>
                                                <th>Tanggal</th>
                                                <th>Gudang</th>
                                                <th>Status</th>
                                                <th>Dibuat Oleh</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($packingLists as $index => $pl)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $pl->pl_number }}</td>
                                                    <td>{{ $pl->created_at->format('d/m/Y') }}</td>
                                                    <td>{{ $pl->warehouse->name }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $pl->status_color }}">
                                                            {{ $pl->status_label }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $pl->creator->nama_lengkap }}</td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-info view-details"
                                                                    data-id="{{ $pl->id }}">
                                                                <i class="ri-eye-line"></i>
                                                            </button>
                                                            @if($pl->status === 'draft')
                                                                <a href="{{ route('packing-lists.edit', $pl->id) }}" 
                                                                   class="btn btn-sm btn-primary">
                                                                    <i class="ri-pencil-line"></i>
                                                                </a>
                                                                <button type="button"
                                                                        class="btn btn-sm btn-danger delete-item"
                                                                        data-id="{{ $pl->id }}">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Packing List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detailContent"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#packingListTable').DataTable({
                pageLength: 10,
                order: [[2, 'desc']], // Sort by date
                columnDefs: [
                    { orderable: false, targets: [6] } // Disable sorting for action column
                ]
            });

            // Handle view detail
            $(document).on('click', '.view-details', function() {
                var id = $(this).data('id');
                
                // Reset dan tampilkan loading di modal
                $('#detailContent').html('<div class="text-center"><div class="spinner-border" role="status"></div></div>');
                
                // Tampilkan modal
                var detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
                detailModal.show();
                
                // Ambil data
                $.get(`{{ url('packing-lists') }}/${id}/details`, function(response) {
                    if (response.success) {
                        $('#detailContent').html(response.html);
                    } else {
                        $('#detailContent').html(`
                            <div class="alert alert-danger">
                                ${response.message || 'Terjadi kesalahan saat memuat data'}
                            </div>
                        `);
                    }
                }).fail(function(xhr) {
                    $('#detailContent').html(`
                        <div class="alert alert-danger">
                            ${xhr.responseJSON?.message || 'Terjadi kesalahan saat memuat data'}
                        </div>
                    `);
                });
            });

            // Handle delete
            $(document).on('click', '.delete-item', function() {
                var id = $(this).data('id');
                
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('packing-lists') }}/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Terhapus!',
                                        'Data berhasil dihapus.',
                                        'success'
                                    ).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Gagal!',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Gagal!',
                                    xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 