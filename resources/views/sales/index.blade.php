@extends('layouts.master')

@section('title')
    @lang('translation.sales.list')
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div>
            @component('components.breadcrumb')
                @slot('li_1')
                    Transaksi
                @endslot
                @slot('title')
                    Penjualan
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-lg-12">
                    <div class="card" id="salesList">
                        <div class="card-header d-flex align-items-center">
                            <h6 class="card-title flex-grow-1 mb-0">@lang('translation.sales.list')</h6>
                            <div class="flex-shrink-0">
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="input-group" style="width: 250px;">
                                        <input type="text" class="form-control" id="dateRange" placeholder="Pilih rentang tanggal">
                                        <button class="btn btn-primary" type="button" id="applyDate">
                                            <i class="ri-filter-line align-bottom"></i> Filter
                                        </button>
                                        <button class="btn btn-outline-secondary" type="button" id="clearDate">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                    <a href="{{ route('sales.upload') }}" class="btn btn-primary">
                                        <i class="ri-upload-line align-bottom me-1"></i> @lang('translation.sales.upload')
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div>
                                <div class="table-responsive table-card mb-1">
                                    <table class="table align-middle table-nowrap" id="salesTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Customer</th>
                                                <th>Invoice</th>
                                                <th>Tanggal</th>
                                                <th>No. Delivery</th>
                                                <th>Total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sales as $index => $sale)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $sale->customer->name }}</td>
                                                    <td>{{ $sale->invoice_document }}</td>
                                                    <td data-sort="{{ $sale->sales_date }}">
                                                        {{ \Carbon\Carbon::parse($sale->sales_date)->format('d/m/Y') }}
                                                    </td>
                                                    <td>{{ $sale->delivery_number }}</td>
                                                    <td class="text-end">{{ number_format($sale->details->sum('amount'), 0, ',', '.') }}</td>
                                                    <td>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-link text-info view-details" 
                                                                data-sale-id="{{ $sale->id }}">
                                                            <i class="ri-eye-line fs-5"></i>
                                                        </button>
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
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="saleDetails"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#salesTable').DataTable({
            pageLength: 10,
            order: [[3, 'desc']], // Sort by date
            columnDefs: [
                { orderable: false, targets: [6] } // Disable sorting for action column
            ]
        });

        // Initialize date range picker
        var fp = flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "d/m/Y"
        });

        // Apply date filter
        $('#applyDate').click(function() {
            var dates = $('#dateRange').val();
            if (!dates) {
                return;
            }

            var dateRange = dates.split(" to ");
            if (dateRange.length !== 2) {
                return;
            }

            var startDate = moment(dateRange[0], "DD/MM/YYYY");
            var endDate = moment(dateRange[1], "DD/MM/YYYY");

            // Filter DataTable
            table.draw();
        });

        // Clear date filter
        $('#clearDate').click(function() {
            fp.clear();
            table.draw();
        });

        // Custom date range filter
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var dates = $('#dateRange').val();
            if (!dates) {
                return true;
            }

            var dateRange = dates.split(" to ");
            if (dateRange.length !== 2) {
                return true;
            }

            var startDate = moment(dateRange[0], "DD/MM/YYYY");
            var endDate = moment(dateRange[1], "DD/MM/YYYY");
            var dateStr = data[3]; // Date column index
            var date = moment(dateStr, "DD/MM/YYYY");

            return date.isBetween(startDate, endDate, 'day', '[]');
        });

        // Perbaiki handler untuk view detail
        $(document).on('click', '.view-details', function() {
            var saleId = $(this).data('sale-id');
            
            // Reset dan tampilkan loading di modal
            $('#saleDetails').html('<div class="text-center"><div class="spinner-border" role="status"></div></div>');
            
            // Tampilkan modal
            var detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
            detailModal.show();
            
            // Ambil data
            $.get(`{{ url('sales') }}/${saleId}/details`, function(response) {
                let content = `
                    <div class="mb-3">
                        <strong>Customer:</strong> ${response.customer}<br>
                        <strong>Invoice:</strong> ${response.invoice_document}<br>
                        <strong>Tanggal:</strong> ${response.sales_date}<br>
                        <strong>No. Delivery:</strong> ${response.delivery_number}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sub Kategori</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>`;
                
                let total = 0;
                response.details.forEach(item => {
                    content += `
                        <tr>
                            <td>${item.sub_category}</td>
                            <td>${item.item}</td>
                            <td class="text-end">${item.quantity}</td>
                            <td>${item.uom}</td>
                            <td class="text-end">${numberFormat(item.price)}</td>
                            <td class="text-end">${numberFormat(item.amount)}</td>
                        </tr>`;
                    total += parseFloat(item.amount);
                });
                
                content += `
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                    <td class="text-end"><strong>${numberFormat(total)}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>`;
                
                $('#saleDetails').html(content);
            }).fail(function(xhr) {
                $('#saleDetails').html(`
                    <div class="alert alert-danger">
                        ${xhr.responseJSON?.message || 'Terjadi kesalahan saat memuat data'}
                    </div>
                `);
            });
        });

        // Bersihkan modal saat ditutup
        $('#detailModal').on('hidden.bs.modal', function() {
            $('#saleDetails').html('');
        });

        function numberFormat(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    });
    </script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
