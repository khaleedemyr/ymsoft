<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview PR - {{ $pr->pr_number }}</title>
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
        .pr-title {
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
    </style>
</head>
<body>
    <div class="fixed-buttons">
        <button onclick="window.print();" class="btn btn-primary" title="Cetak">
            <i class="ri-printer-line"></i>
        </button>
        <button onclick="window.close();" class="btn btn-secondary" title="Tutup">
            <i class="ri-close-line"></i>
        </button>
    </div>

    <div class="header">
        <img src="{{ asset('build/images/logo/LOGO_JUSTUS_GROUP_1024X500.jpg') }}" class="logo">
    </div>
    
    <div class="pr-title">PURCHASE REQUISITION</div>
    
    <table class="info-table">
        <tr>
            <th>PR Number</th>
            <td>: {{ $pr->pr_number }}</td>
            <th>Created Date</th>
            <td>: {{ date('d M Y', strtotime($pr->created_at)) }}</td>
        </tr>
        <tr>
            <th>Task Number</th>
            <td>: {{ $pr->task->task_number }}</td>
            <th>Status</th>
            <td>: {{ $pr->status }}</td>
        </tr>
        <tr>
            <th>Created By</th>
            <td>: {{ $pr->creator->nama_lengkap }}</td>
            <th></th>
            <td></td>
        </tr>
        @if($pr->notes)
        <tr>
            <th>Notes</th>
            <td colspan="3">: {{ $pr->notes }}</td>
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
                @foreach($pr->items as $index => $item)
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
                    <td class="text-end">{{ number_format($pr->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div class="approval-section">
        <table class="approval-table compact">
            <tr>
                <td>
                    <div class="approval-title">Dibuat Oleh</div>
                    @if($pr->creator->signature_path)
                        <div class="signature-image">
                            <img src="/storage/app/public/{{ $pr->creator->signature_path }}" alt="Tanda Tangan">
                        </div>
                    @else
                        <div class="signature-line"></div>
                    @endif
                    <div>{{ $pr->creator->nama_lengkap }}</div>
                    <div class="position-label">{{ $pr->creator->jabatan->nama_jabatan ?? '' }}</div>
                </td>
                <td>
                    <div class="approval-title">Disetujui Oleh</div>
                    @if($pr->chief_engineering_approval == 'APPROVED' && $pr->chiefEngineeringApprover->signature_path)
                        <div class="signature-image">
                            <img src="/storage/app/public/{{ $pr->chiefEngineeringApprover->signature_path }}" alt="Tanda Tangan">
                        </div>
                    @else
                        <div class="signature-line"></div>
                    @endif
                    <div>{{ $chiefEngineering->nama_lengkap ?? 'Chief Engineering' }}</div>
                    <div class="position-label">{{ $chiefEngineering->jabatan->nama_jabatan ?? '' }}</div>
                    @if($pr->chief_engineering_approval == 'APPROVED')
                        <div class="approval-info">
                            @if($pr->chiefEngineeringApprover->id != ($chiefEngineering->id ?? 0))
                                <span class="delegate-label">Bertindak atas nama: {{ $pr->chiefEngineeringApprover->nama_lengkap }}</span>
                            @endif
                            @if($pr->chief_engineering_approval_notes)
                                <span class="approval-notes"><strong>{{ $pr->chief_engineering_approval_notes }}</strong></span>
                            @endif
                        </div>
                    @endif
                </td>
                <td>
                    <div class="approval-title">Disetujui Oleh</div>
                    @if($pr->purchasing_manager_approval == 'APPROVED' && $pr->purchasingManagerApprover->signature_path)
                        <div class="signature-image">
                            <img src="/storage/app/public/{{ $pr->purchasingManagerApprover->signature_path }}" alt="Tanda Tangan">
                        </div>
                    @else
                        <div class="signature-line"></div>
                    @endif
                    <div>{{ $purchasingManager->nama_lengkap ?? 'Purchasing Manager' }}</div>
                    <div class="position-label">{{ $purchasingManager->jabatan->nama_jabatan ?? '' }}</div>
                    @if($pr->purchasing_manager_approval == 'APPROVED')
                        <div class="approval-info">
                            @if($pr->purchasingManagerApprover->id != ($purchasingManager->id ?? 0))
                                <span class="delegate-label">Bertindak atas nama: {{ $pr->purchasingManagerApprover->nama_lengkap }}</span>
                            @endif
                            @if($pr->purchasing_manager_approval_notes)
                                <span class="approval-notes"><strong>{{ $pr->purchasing_manager_approval_notes }}</strong></span>
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
                    @if($pr->coo_approval == 'APPROVED' && $pr->cooApprover->signature_path)
                        <div class="signature-image">
                            <img src="/storage/app/public/{{ $pr->cooApprover->signature_path }}" alt="Tanda Tangan">
                        </div>
                    @else
                        <div class="signature-line"></div>
                    @endif
                    <div>{{ $coo->nama_lengkap ?? 'Chief Operating Officer' }}</div>
                    <div class="position-label">{{ $coo->jabatan->nama_jabatan ?? 'Chief Operating Officer' }}</div>
                    @if($pr->coo_approval == 'APPROVED')
                        <div class="approval-info">
                            @if($pr->cooApprover->id != ($coo->id ?? 0))
                                <span class="delegate-label">Bertindak atas nama: {{ $pr->cooApprover->nama_lengkap }}</span>
                            @endif
                            @if($pr->coo_approval_notes)
                                <span class="approval-notes"><strong>{{ $pr->coo_approval_notes }}</strong></span>
                            @endif
                        </div>
                    @endif
                </td>
                <td width="33.33%"></td>
            </tr>
        </table>
    </div>
</body>
</html>
