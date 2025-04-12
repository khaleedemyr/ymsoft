<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview PO - {{ $po->po_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 20px;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 400px;
            max-height: 150px;
            display: block;
            margin: 0 auto;
        }
        .po-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table th {
            text-align: left;
            width: 150px;
            padding: 5px;
            vertical-align: top;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f2f2f2;
        }
        .items-table .text-end {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
        }
        .approval-section {
            margin-top: 30px;
        }
        .approval-table {
            width: 100%;
            border-collapse: collapse;
        }
        .approval-table td {
            padding: 5px;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
        }
        .approval-title {
            font-weight: bold;
            margin-bottom: 60px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 60px auto 5px auto;
        }
        .fixed-buttons {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 100;
        }
        .btn {
            display: inline-block;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 14px;
            line-height: 1.5;
            border-radius: 0.25rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            margin-left: 5px;
            text-decoration: none;
        }
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-secondary {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }
        @media print {
            .fixed-buttons {
                display: none;
            }
        }
        .approval-delegate {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 11px;
            border: 1px dashed #ddd;
        }
        .approval-notes {
            display: block;
            margin-top: 5px;
            font-style: italic;
            color: #666;
        }
        .approval-date {
            margin-top: 10px;
            font-weight: bold;
        }
        .approval-table.compact {
            margin-bottom: 0;
        }
        .approval-title {
            font-weight: bold;
            margin-bottom: 40px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 40px auto 5px auto;
        }
        .position-label {
            font-size: 10px;
            color: #555;
            margin-bottom: 5px;
        }
        .approval-info {
            font-size: 10px;
            margin-top: 5px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .delegate-label {
            color: #444;
        }
        .approval-date {
            color: #555;
        }
        .approval-notes {
            margin-top: 2px;
            color: #000;
            font-style: italic;
        }
        .signature-image {
            height: 75px;
            margin: 5px auto;
            width: 85%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .signature-image img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            transform: scale(1.2);
        }
        .signature-placeholder {
            opacity: 0.5;
        }
        .invoice-section {
            page-break-before: always;
            margin-top: 30px;
        }
        .invoice-container {
            max-width: 100%;
            margin: 0 auto;
        }
        .invoice-image {
            max-width: 100%;
            height: auto;
            margin: 20px 0;
        }
        .invoice-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .invoice-info table {
            width: 100%;
            margin-bottom: 0;
        }
        .invoice-info th {
            width: 150px;
            padding: 5px;
        }
        .btn-invoice {
            background-color: #17a2b8;
            color: white;
        }
        @media print {
            .po-content {
                display: block;
            }
            .invoice-only {
                display: none;
            }
            .invoice-section {
                display: block;
            }
            body.invoice-page .po-content {
                display: none !important;
            }
            body.invoice-page .invoice-only {
                display: block !important;
            }
            body.invoice-page .invoice-section {
                page-break-before: avoid;
                margin-top: 0;
            }
        }
    </style>
</head>
<body class="{{ request()->get('invoice_only') ? 'invoice-page' : '' }}">
    <div class="fixed-buttons">
        <button onclick="window.print();" class="btn btn-primary" title="Cetak">
            <i class="ri-printer-line"></i>
        </button>
        <button onclick="window.close();" class="btn btn-secondary" title="Tutup">
            <i class="ri-close-line"></i>
        </button>
    </div>

    <div class="po-content">
        <div class="header">
            <img src="{{ asset('build/images/logo/LOGO_JUSTUS_GROUP_1024X500.jpg') }}" class="logo">
        </div>
        
        <div class="po-title">PURCHASE ORDER</div>
        
        <table class="info-table">
            <tr>
                <th>PO Number</th>
                <td>: {{ $po->po_number }}</td>
                <th>Created Date</th>
                <td>: {{ date('d M Y', strtotime($po->created_at)) }}</td>
            </tr>
            <tr>
                <th>Task Number</th>
                <td>: {{ $po->task->task_number }}</td>
                <th>Status</th>
                <td>: {{ $po->status }}</td>
            </tr>
            <tr>
                <th>Created By</th>
                <td>: {{ $po->creator->nama_lengkap }}</td>
                <th>Supplier</th>
                <td>: {{ $po->supplier->name }}</td>
            </tr>
            @if($po->notes)
            <tr>
                <th>Notes</th>
                <td colspan="3">: {{ $po->notes }}</td>
            </tr>
            @endif
        </table>
        
        <div style="margin-top: 20px;">
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No.</th>
                        <th width="20%">Item Name</th>
                        <th width="15%">Description</th>
                        <th width="15%">Specifications</th>
                        <th class="text-center" width="8%">Quantity</th>
                        <th class="text-center" width="7%">Unit</th>
                        <th class="text-end" width="15%">Price</th>
                        <th class="text-end" width="15%">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($po->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->specifications }}</td>
                        <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-center">{{ $item->unit->name }}</td>
                        <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="7" class="text-end">Total:</td>
                        <td class="text-end">{{ number_format($po->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="approval-section">
            <table class="approval-table compact">
                <tr>
                    <td>
                        <div class="approval-title">Dibuat Oleh</div>
                        @if($po->creator->signature_path)
                            <div class="signature-image">
                                <img src="{{ asset('storage/' . $po->creator->signature_path) }}" alt="Tanda Tangan">
                            </div>
                        @else
                            <div class="signature-line"></div>
                        @endif
                        <div>{{ $po->creator->nama_lengkap }}</div>
                        <div class="position-label">{{ $po->creator->jabatan->nama_jabatan ?? '' }}</div>
                    </td>
                    <td>
                        <div class="approval-title">Disetujui Oleh</div>
                        @if($po->gm_finance_approval == 'APPROVED' && $po->gmFinanceApprover->signature_path)
                            <div class="signature-image">
                                <img src="{{ asset('storage/' . $po->gmFinanceApprover->signature_path) }}" alt="Tanda Tangan">
                            </div>
                        @else
                            <div class="signature-line"></div>
                        @endif
                        <div>{{ $gmFinance->nama_lengkap ?? 'GM Finance' }}</div>
                        <div class="position-label">{{ $gmFinance->jabatan->nama_jabatan ?? '' }}</div>
                        @if($po->gm_finance_approval == 'APPROVED')
                            <div class="approval-info">
                                @if($po->gmFinanceApprover->id != ($gmFinance->id ?? 0))
                                    <span class="delegate-label">Bertindak atas nama: {{ $po->gmFinanceApprover->nama_lengkap }}</span>
                                @endif
                                @if($po->gm_finance_approval_notes)
                                    <span class="approval-notes"><strong>{{ $po->gm_finance_approval_notes }}</strong></span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="approval-title">Disetujui Oleh</div>
                        @if($po->managing_director_approval == 'APPROVED' && $po->managingDirectorApprover->signature_path)
                            <div class="signature-image">
                                <img src="{{ asset('storage/' . $po->managingDirectorApprover->signature_path) }}" alt="Tanda Tangan">
                            </div>
                        @else
                            <div class="signature-line"></div>
                        @endif
                        <div>{{ $managingDirector->nama_lengkap ?? 'Managing Director' }}</div>
                        <div class="position-label">{{ $managingDirector->jabatan->nama_jabatan ?? '' }}</div>
                        @if($po->managing_director_approval == 'APPROVED')
                            <div class="approval-info">
                                @if($po->managingDirectorApprover->id != ($managingDirector->id ?? 0))
                                    <span class="delegate-label">Bertindak atas nama: {{ $po->managingDirectorApprover->nama_lengkap }}</span>
                                @endif
                                @if($po->managing_director_approval_notes)
                                    <span class="approval-notes"><strong>{{ $po->managing_director_approval_notes }}</strong></span>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
            </table>
            
            <table class="approval-table compact" style="margin-top: 10px;">
                <tr>
                    <td width="33.33%"></td>
                    <td width="33.33%">
                        <div class="approval-title">Mengetahui dan Menyetujui</div>
                        @if($po->president_director_approval == 'APPROVED' && $po->presidentDirectorApprover->signature_path)
                            <div class="signature-image">
                                <img src="{{ asset('storage/' . $po->presidentDirectorApprover->signature_path) }}" alt="Tanda Tangan">
                            </div>
                        @else
                            <div class="signature-line"></div>
                        @endif
                        <div>{{ $presidentDirector->nama_lengkap ?? 'President Director' }}</div>
                        <div class="position-label">{{ $presidentDirector->jabatan->nama_jabatan ?? 'President Director' }}</div>
                        @if($po->president_director_approval == 'APPROVED')
                            <div class="approval-info">
                                @if($po->presidentDirectorApprover->id != ($presidentDirector->id ?? 0))
                                    <span class="delegate-label">Bertindak atas nama: {{ $po->presidentDirectorApprover->nama_lengkap }}</span>
                                @endif
                                @if($po->president_director_approval_notes)
                                    <span class="approval-notes"><strong>{{ $po->president_director_approval_notes }}</strong></span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td width="33.33%"></td>
                </tr>
            </table>
        </div>

        @if($po->invoice_file_path && !request()->get('invoice_only'))
        <div class="invoice-section">
            <h3 style="text-align: center; margin-bottom: 20px;">INVOICE</h3>
            <div class="invoice-info">
                <table>
                    <tr>
                        <th>Invoice Number</th>
                        <td>: {{ $po->invoice_number }}</td>
                    </tr>
                    <tr>
                        <th>Invoice Date</th>
                        <td>: {{ date('d M Y', strtotime($po->invoice_date)) }}</td>
                    </tr>
                </table>
            </div>
            <div class="invoice-container">
                <img src="{{ asset('storage/' . $po->invoice_file_path) }}" 
                     alt="Invoice {{ $po->invoice_number }}"
                     class="invoice-image">
            </div>
        </div>
        @endif
    </div>

    @if($po->invoice_file_path && request()->get('invoice_only'))
    <div class="invoice-only">
        <div class="header">
            <img src="{{ asset('build/images/logo/LOGO_JUSTUS_GROUP_1024X500.jpg') }}" class="logo">
        </div>
        
        <h3 style="text-align: center; margin: 20px 0;">INVOICE - {{ $po->po_number }}</h3>
        
        <div class="invoice-info">
            <table>
                <tr>
                    <th>PO Number</th>
                    <td>: {{ $po->po_number }}</td>
                </tr>
                <tr>
                    <th>Invoice Number</th>
                    <td>: {{ $po->invoice_number }}</td>
                </tr>
                <tr>
                    <th>Invoice Date</th>
                    <td>: {{ date('d M Y', strtotime($po->invoice_date)) }}</td>
                </tr>
                <tr>
                    <th>Supplier</th>
                    <td>: {{ $po->supplier->name }}</td>
                </tr>
            </table>
        </div>

        <div class="invoice-container">
            <img src="{{ asset('storage/' . $po->invoice_file_path) }}" 
                 alt="Invoice {{ $po->invoice_number }}"
                 class="invoice-image">
        </div>
    </div>
    @endif
</body>
</html>
