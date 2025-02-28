@extends('layouts.master')

@section('title')
    @lang('translation.sales.preview')
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
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
                        Preview Penjualan
                    @endslot
                @endcomponent

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h6 class="card-title flex-grow-1 mb-0">Preview Data Penjualan</h6>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('sales.upload') }}" class="btn btn-light me-1">
                                        <i class="ri-arrow-left-line align-bottom me-1"></i> Kembali
                                    </a>
                                    <button type="button" class="btn btn-success" id="btnSave">
                                        <i class="ri-save-line align-bottom me-1"></i> Simpan
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(isset($salesData) && count($salesData) > 0)
                                    <div class="accordion" id="customerAccordion">
                                        @foreach($salesData as $customer => $invoices)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ str_replace(' ', '', $customer) }}">
                                                        {{ $customer }}
                                                    </button>
                                                </h2>
                                                <div id="collapse{{ str_replace(' ', '', $customer) }}" class="accordion-collapse collapse" data-bs-parent="#customerAccordion">
                                                    <div class="accordion-body">
                                                        <div class="table-responsive">
                                                            <table id="previewTable" class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Customer</th>
                                                                        <th>Invoice</th>
                                                                        <th>Delivery Number</th>
                                                                        <th>Total</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($invoices as $invoiceDoc => $invoice)
                                                                        @php
                                                                            // Hitung total dari items
                                                                            $total = 0;
                                                                            if (isset($invoice['items']) && is_array($invoice['items'])) {
                                                                                foreach ($invoice['items'] as $item) {
                                                                                    $total += $item['amount'] ?? 0;
                                                                                }
                                                                            }
                                                                        @endphp
                                                                        <tr 
                                                                            data-customer-id="{{ $invoice['customer_id'] ?? '' }}"
                                                                            data-sales-date="{{ $invoice['sales_date'] ?? '' }}"
                                                                            data-items="{{ json_encode($invoice['items'] ?? []) }}"
                                                                            data-total="{{ $total }}"
                                                                        >
                                                                            <td>{{ $customer }}</td>
                                                                            <td>{{ $invoiceDoc }}</td>
                                                                            <td>{{ $invoice['delivery_number'] ?? '' }}</td>
                                                                            <td class="text-right">{{ number_format($total, 0, ',', '.') }}</td>
                                                                            <td>
                                                                                <button type="button" 
                                                                                        class="btn btn-sm btn-link text-info view-detail"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#detailModal"
                                                                                        data-customer="{{ $customer }}"
                                                                                        data-invoice="{{ $invoiceDoc }}">
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
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        Tidak ada data untuk ditampilkan
                                    </div>
                                @endif
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
                    <h5 class="modal-title">Detail Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="invoiceDetails"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Handle detail view
            $('.view-detail').click(function() {
                let customer = $(this).data('customer');
                let invoice = $(this).data('invoice');
                let items = @json($salesData)[customer][invoice]['items'];
                
                let content = `
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
                
                items.forEach(item => {
                    content += `
                        <tr>
                            <td>${item.sub_category}</td>
                            <td>${item.item}</td>
                            <td class="text-end">${item.quantity}</td>
                            <td>${item.uom}</td>
                            <td class="text-end">${numberFormat(item.price)}</td>
                            <td class="text-end">${numberFormat(item.amount)}</td>
                        </tr>`;
                });
                
                content += `</tbody></table></div>`;
                
                $('#invoiceDetails').html(content);
            });

            // Fungsi untuk mengambil data dari tabel
            function collectTableData() {
                let salesData = {};
                
                // Loop through each row in preview table
                $('#previewTable tbody tr').each(function() {
                    const $row = $(this);
                    const customerName = $row.find('td:eq(0)').text().trim();
                    const invoiceNumber = $row.find('td:eq(1)').text().trim();
                    const deliveryNumber = $row.find('td:eq(2)').text().trim();
                    const customerId = $row.data('customer-id');
                    const salesDate = $row.data('sales-date');
                    const items = $row.data('items');
                    const total = $row.data('total');

                    // Initialize customer if not exists
                    if (!salesData[customerName]) {
                        salesData[customerName] = {};
                    }

                    // Add invoice data
                    salesData[customerName][invoiceNumber] = {
                        customer_id: customerId,
                        sales_date: salesDate,
                        delivery_number: deliveryNumber,
                        items: items,
                        total: total
                    };
                });

                return salesData;
            }

            // Fungsi untuk mengirim data
            function saveSalesData() {
                // Collect data from table
                const salesData = collectTableData();

                // Validate if data exists
                if (Object.keys(salesData).length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Tidak ada data untuk disimpan'
                    });
                    return;
                }

                // Tampilkan loading
                Swal.fire({
                    title: 'Menyimpan Data',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Log data yang akan dikirim (untuk debugging)
                console.log('Data yang akan dikirim:', salesData);

                // Kirim data
                $.ajax({
                    url: '/sales/store',
                    method: 'POST',
                    dataType: 'json',
                    contentType: 'application/json',
                    data: JSON.stringify({ sales: salesData }),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '/sales';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Terjadi kesalahan saat menyimpan data'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                        
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            console.error('Error parsing response:', xhr.responseText);
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    }
                });
            }

            // Event handler untuk tombol save
            $('#btnSave').on('click', function(e) {
                e.preventDefault();
                saveSalesData();
            });

            function numberFormat(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }
        });
    </script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection 