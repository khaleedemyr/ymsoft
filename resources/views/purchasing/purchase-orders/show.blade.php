@extends('layouts.master')

@section('title')
    {{ trans('translation.purchase_order.detail') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .table th {
            background-color: #f8f9fa;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                font-size: 12pt;
            }
            .no-print {
                display: none !important;
            }
            .content-wrapper {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .card {
                border: none !important;
                padding: 0 !important;
            }
            .card-header {
                display: none !important;
            }
            .shadow-sm {
                box-shadow: none !important;
            }
            
            /* Kop Surat */
            .print-header {
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #000;
                padding-bottom: 20px;
            }
            .company-name {
                font-size: 24pt;
                font-weight: bold;
                margin-bottom: 5px;
            }
            .company-address {
                font-size: 10pt;
                margin-bottom: 5px;
            }
            .document-title {
                font-size: 18pt;
                font-weight: bold;
                margin: 20px 0;
                text-align: center;
                text-decoration: underline;
            }
            
            /* Info Section */
            .po-info {
                margin: 20px 0;
                width: 100%;
            }
            .po-info td {
                padding: 3px 0;
            }
            .info-table {
                width: 100%;
                margin-bottom: 20px;
            }
            .info-table td {
                vertical-align: top;
                padding: 3px 10px;
            }
            
            /* Items Table */
            .items-table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            .items-table th,
            .items-table td {
                border: 1px solid #000;
                padding: 8px;
            }
            .items-table th {
                background-color: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
            }
            
            /* Footer */
            .print-footer {
                margin-top: 50px;
                page-break-inside: avoid;
            }
            .signature-footer {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-top: 30px;
            }
            .signature-section {
                flex: 3;
                display: flex;
                justify-content: space-between;
            }
            .qr-section {
                flex: 1;
                text-align: center;
            }
            
            /* QR Code styles */
            .qr-code-container {
                position: relative;
                text-align: center;
                margin-top: 50px;
                margin-bottom: 30px;
            }
            .qr-code-container img {
                width: 100px;
                height: 100px;
            }
            .qr-code-text {
                font-size: 8pt;
                margin-top: 5px;
            }
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Regular view content -->
    <div class="content-header no-print">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('translation.purchase_order.detail') }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Regular buttons and header -->
            <div class="row no-print">
                <div class="col-xl-12">
                    @component('components.breadcrumb')
                        @slot('li_1')
                            {{ trans('translation.purchasing.title') }}
                        @endslot
                        @slot('title')
                            {{ trans('translation.purchase_order.detail') }}
                        @endslot
                    @endcomponent

                    <div class="mb-3">
                        @php
                            $user = auth()->user();
                            $hasRole = $user->user_roles()->where('role_id', 1)->exists();
                            $hasPosition = $user->id_jabatan == 168 && $user->status == 'A';
                        @endphp
                        @if($purchaseOrder->status === 'draft' && ($hasRole || $hasPosition))
                        <button type="button" class="btn btn-success btn-approve">
                            <i class="ri-check-line align-bottom me-1"></i> 
                            {{ trans('translation.purchase_order.button.approve') }}
                        </button>
                        <button type="button" class="btn btn-danger btn-reject">
                            <i class="ri-close-line align-bottom me-1"></i>
                            {{ trans('translation.purchase_order.button.reject') }}
                        </button>
                        @endif
                        <button type="button" class="btn btn-primary btn-print">
                            <i class="ri-printer-line align-bottom me-1"></i>
                            {{ trans('translation.purchase_order.button.print') }}
                        </button>
                        <a href="{{ route('purchasing.purchase-orders.index') }}" class="btn btn-light">
                            <i class="ri-arrow-left-line align-bottom me-1"></i>
                            {{ trans('translation.purchase_order.button.back') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Print Header -->
                            <div class="print-header d-none d-print-block">
                                <div class="company-name">JUSTUS GROUP</div>
                                <div class="company-address">
                                   Bandung, Indonesia<br>
                                    Phone: 123456 | Email: purchasing@justusku.co.id
                                </div>
                            </div>
                            
                            <!-- Document Title for Print -->
                            <div class="document-title d-none d-print-block">PURCHASE ORDER</div>

                            <!-- PO Information in two columns -->
                            <table class="info-table d-none d-print-table">
                                <tr>
                                    <td width="50%">
                                        <strong>Kepada Yth:</strong><br>
                                        {{ optional($purchaseOrder->supplier)->name }}<br>
                                        {{ optional($purchaseOrder->supplier)->address ?? '' }}
                                    </td>
                                    <td width="50%">
                                        <table class="po-info">
                                            <tr>
                                                <td width="120">Nomor PO</td>
                                                <td>: {{ $purchaseOrder->po_number }}</td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal PO</td>
                                                <td>: {{ date('d/m/Y', strtotime($purchaseOrder->po_date)) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Nomor PR</td>
                                                <td>: {{ optional($purchaseOrder->purchaseRequisition)->pr_number ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Regular view info -->
                            <div class="row d-print-none">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">{{ trans('translation.purchase_order.table.po_number') }}</div>
                                        <div>{{ $purchaseOrder->po_number }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">{{ trans('translation.purchase_order.table.po_date') }}</div>
                                        <div>{{ date('d/m/Y', strtotime($purchaseOrder->po_date)) }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">{{ trans('translation.purchase_order.table.pr_number') }}</div>
                                        <div>{{ optional($purchaseOrder->purchaseRequisition)->pr_number ?? '-' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">{{ trans('translation.purchase_order.table.supplier') }}</div>
                                        <div>{{ optional($purchaseOrder->supplier)->name ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">{{ trans('translation.purchase_order.table.warehouse') }}</div>
                                        <div>{{ optional($purchaseOrder->purchaseRequisition)->warehouse->name ?? '-' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">{{ trans('translation.purchase_order.table.department') }}</div>
                                        <div>{{ optional($purchaseOrder->purchaseRequisition)->department ?? '-' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">{{ trans('translation.purchase_order.table.status') }}</div>
                                        <div>{{ trans('translation.purchase_order.status.' . $purchaseOrder->status) }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">{{ trans('translation.purchase_order.table.created_by') }}</div>
                                        <div>{{ optional($purchaseOrder->creator)->nama_lengkap ?? '-' }}</div>
                                    </div>
                                    @if($purchaseOrder->status === 'approved')
                                    <div class="info-item">
                                        <div class="info-label">{{ trans('translation.purchase_order.table.approved_by') }}</div>
                                        <div>{{ optional($purchaseOrder->approver)->nama_lengkap ?? '-' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">{{ trans('translation.purchase_order.table.approved_at') }}</div>
                                        <div>{{ $purchaseOrder->approved_at ? date('d/m/Y H:i', strtotime($purchaseOrder->approved_at)) : '-' }}</div>
                                    </div>
                                    @endif
                                    @if($purchaseOrder->status === 'cancelled')
                                    <div class="info-item">
                                        <div class="info-label">{{ trans('translation.purchase_order.table.rejected_by') }}</div>
                                        <div>{{ optional($purchaseOrder->rejector)->nama_lengkap ?? '-' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">{{ trans('translation.purchase_order.table.rejected_at') }}</div>
                                        <div>{{ $purchaseOrder->rejected_at ? date('d/m/Y H:i', strtotime($purchaseOrder->rejected_at)) : '-' }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Items Table -->
                            <table class="table table-bordered d-print-none">
                                <thead>
                                    <tr>
                                        <th>{{ trans('translation.purchase_order.table.item_sku') }}</th>
                                        <th>{{ trans('translation.purchase_order.table.item_name') }}</th>
                                        <th class="text-end">{{ trans('translation.purchase_order.table.quantity') }}</th>
                                        <th>{{ trans('translation.purchase_order.table.uom') }}</th>
                                        <th class="text-end">{{ trans('translation.purchase_order.table.price') }}</th>
                                        <th class="text-end">{{ trans('translation.purchase_order.table.subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrder->items as $item)
                                    <tr>
                                        <td>{{ optional($item->item)->sku }}</td>
                                        <td>{{ optional($item->item)->name }}</td>
                                        <td class="text-end">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                                        <td>{{ optional($item->unit)->name }}</td>
                                        <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($item->total, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>{{ trans('translation.purchase_order.table.total_amount') }}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($purchaseOrder->total, 0, ',', '.') }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>

                            <!-- Print version of items table -->
                            <table class="items-table d-none d-print-table">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th style="width: 15%">{{ trans('translation.purchase_order.table.item_code') }}</th>
                                        <th style="width: 30%">{{ trans('translation.purchase_order.table.item_name') }}</th>
                                        <th style="width: 10%" class="text-end">{{ trans('translation.purchase_order.table.quantity') }}</th>
                                        <th style="width: 10%">{{ trans('translation.purchase_order.table.uom') }}</th>
                                        <th style="width: 15%" class="text-end">{{ trans('translation.purchase_order.table.price') }}</th>
                                        <th style="width: 15%" class="text-end">{{ trans('translation.purchase_order.table.subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrder->items as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ optional($item->item)->sku }}</td>
                                        <td>{{ optional($item->item)->name }}</td>
                                        <td class="text-end">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                                        <td>{{ optional($item->unit)->name }}</td>
                                        <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($item->total, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end"><strong>{{ trans('translation.purchase_order.table.total_amount') }}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($purchaseOrder->total, 0, ',', '.') }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>

                            <!-- Print Footer -->
                            <div class="print-footer d-none d-print-block">
                                <div>Catatan:</div>
                                <ol>
                                    <li>PO ini sah apabila ada tanda tangan dari pejabat yang berwenang</li>
                                    <li>Pembayaran akan diproses setelah barang diterima dengan lengkap</li>
                                    <li>Mohon sertakan nomor PO dan perlihatkan QR Code pada saat pengiriman barang</li>
                                </ol>

                                <div class="signature-footer">
                                    <div class="signature-section">
                                        <div class="signature-box">
                                            <div>Dibuat oleh,</div>
                                            <div class="signature-line">
                                                @if(optional($purchaseOrder->creator)->signature_path)
                                                    <img src="{{ asset($purchaseOrder->creator->signature_path) }}" class="signature-image" alt="Creator Signature">
                                                @endif
                                            </div>
                                            <div>{{ optional($purchaseOrder->creator)->nama_lengkap }}</div>
                                            <div><small>{{ optional(optional($purchaseOrder->creator)->jabatan)->nama_jabatan ?? 'Purchasing' }}</small></div>
                                        </div>

                                        <div class="signature-box">
                                            <div>Disetujui oleh,</div>
                                            <div class="signature-line">
                                                @if($purchaseOrder->status === 'approved' && optional($purchaseOrder->approver)->signature_path)
                                                    <img src="{{ asset($purchaseOrder->approver->signature_path) }}" class="signature-image" alt="Approver Signature">
                                                @endif
                                            </div>
                                            <div>{{ optional($purchaseOrder->approver)->nama_lengkap ?? '________________' }}</div>
                                            <div><small>{{ optional(optional($purchaseOrder->approver)->jabatan)->nama_jabatan ?? 'Cost Control' }}</small></div>
                                        </div>
                                    </div>
                                    
                                    <div class="qr-section">
                                        <!-- QR Code -->
                                        <div class="qr-code-container">
                                            {!! QrCode::size(100)->generate($purchaseOrder->po_number) !!}
                                            <div class="qr-code-text">Scan untuk verifikasi penerimaan barang</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle Print
    document.querySelector('.btn-print')?.addEventListener('click', function() {
        window.print();
    });

    // Handle Approve
    document.querySelector('.btn-approve')?.addEventListener('click', function() {
        Swal.fire({
            title: "{{ trans('translation.purchase_order.message.confirm_approve') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{ trans('translation.purchase_order.button.approve') }}",
            cancelButtonText: "{{ trans('translation.cancel') }}",
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send approve request
                fetch(`/purchasing/purchase-orders/{{ $purchaseOrder->id }}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            showConfirmButton: true
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to process request'
                    });
                });
            }
        });
    });

    // Handle Reject
    document.querySelector('.btn-reject')?.addEventListener('click', function() {
        Swal.fire({
            title: "{{ trans('translation.purchase_order.message.confirm_reject') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{ trans('translation.purchase_order.button.reject') }}",
            cancelButtonText: "{{ trans('translation.cancel') }}",
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send reject request
                fetch(`/purchasing/purchase-orders/{{ $purchaseOrder->id }}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            showConfirmButton: true
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to process request'
                    });
                });
            }
        });
    });
});
</script>
@endsection 