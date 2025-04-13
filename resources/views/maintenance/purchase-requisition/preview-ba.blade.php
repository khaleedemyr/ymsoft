<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview BA - {{ $ba->pr_number }}</title>
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
        .ba-title {
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
    
    <div class="ba-title">BERITA ACARA PENGAJUAN</div>
    
    <table class="info-table">
        <tr>
            <th>BA Number</th>
            <td>: {{ $ba->pr_number }}</td>
            <th>Created Date</th>
            <td>: {{ date('d M Y', strtotime($ba->created_at)) }}</td>
        </tr>
        <tr>
            <th>Task Number</th>
            <td>: {{ $ba->task->task_number }}</td>
            <th>Status</th>
            <td>: {{ $ba->status }}</td>
        </tr>
        <tr>
            <th>Created By</th>
            <td>: {{ $ba->creator->nama_lengkap }}</td>
            <th></th>
            <td></td>
        </tr>
        @if($ba->pr && $ba->pr->notes)
        <tr>
            <th>Purpose</th>
            <td colspan="3">: {{ $ba->pr->notes }}</td>
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
                @foreach($ba->items as $index => $item)
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
                @if ($item->media_path)
                    @if (in_array(pathinfo($item->media_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                        <tr>
                            <td colspan="8" style="text-align: center;">
                                <img src="/storage/app/public/{{ $item->media_path }}" alt="Media" class="img-fluid" style="max-width: 300px;">
                            </td>
                        </tr>
                    @elseif (in_array(pathinfo($item->media_path, PATHINFO_EXTENSION), ['mp4', 'webm', 'ogg']))
                        <tr>
                            <td colspan="8" style="text-align: center;">
                                <video width="300" controls>
                                    <source src="/storage/app/public/{{ $item->media_path }}" type="video/{{ pathinfo($item->media_path, PATHINFO_EXTENSION) }}">
                                    Your browser does not support the video tag.
                                </video>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="8" style="text-align: center;">
                                <a href="/storage/app/public/{{ $item->media_path }}" target="_blank" class="btn btn-primary">View Media</a>
                            </td>
                        </tr>
                    @endif
                @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="7" class="text-end">Total:</td>
                    <td class="text-end">{{ number_format($ba->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Tujuan Pembelian -->
    <div style="margin: 20px 0;">
        <h3 style="font-size: 14px; margin-bottom: 10px;">Tujuan Pembelian:</h3>
        <div style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9;">
            {{ $ba->notes }}
        </div>
    </div>

    <!-- Foto-foto Task -->
    @if($taskPhotos && $taskPhotos->count() > 0)
    <div style="margin: 20px 0;">
        <h3 style="font-size: 14px; margin-bottom: 10px;">Foto-foto Pendukung:</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            @foreach($taskPhotos as $photo)
                <div style="width: 200px; margin-bottom: 10px;">
                    <img src="{{ asset('storage/' . $photo->file_path) }}" 
                         alt="{{ $photo->file_name }}" 
                         style="width: 100%; height: 200px; object-fit: cover; border: 1px solid #ddd;">
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="approval-section">
        <table class="approval-table compact">
            <tr>
                <td>
                    <div class="approval-title">Dibuat Oleh</div>
                    @if($ba->creator->signature_path)
                        <div class="signature-image">
                            <img src="{{ asset('storage/' . $ba->creator->signature_path) }}" alt="Tanda Tangan">
                        </div>
                    @else
                        <div class="signature-line"></div>
                    @endif
                    <div>{{ $ba->creator->nama_lengkap }}</div>
                    <div class="position-label">{{ $ba->creator->jabatan->nama_jabatan ?? '' }}</div>
                </td>
                <td>
                    <!-- Kolom tengah dikosongkan untuk memberi ruang -->
                </td>
                <td>
                    <div class="approval-title">Disetujui Oleh</div>
                    @if($ba->chief_engineering_approval == 'APPROVED' && $ba->chiefEngineeringApprover->signature_path)
                        <div class="signature-image">
                            <img src="{{ asset('storage/' . $ba->chiefEngineeringApprover->signature_path) }}" alt="Tanda Tangan">
                        </div>
                    @else
                        <div class="signature-line"></div>
                    @endif
                    <div>{{ $chiefEngineering->nama_lengkap ?? 'Chief Engineering' }}</div>
                    <div class="position-label">{{ $chiefEngineering->jabatan->nama_jabatan ?? '' }}</div>
                    @if($ba->chief_engineering_approval == 'APPROVED')
                        <div class="approval-info">
                            @if($ba->chiefEngineeringApprover->id != ($chiefEngineering->id ?? 0))
                                <span class="delegate-label">Bertindak atas nama: {{ $ba->chiefEngineeringApprover->nama_lengkap }}</span>
                            @endif
                            @if($ba->chief_engineering_approval_notes)
                                <span class="approval-notes"><strong>{{ $ba->chief_engineering_approval_notes }}</strong></span>
                            @endif
                        </div>
                    @endif
                </td>
            </tr>
        </table>
        
        <table class="approval-table compact" style="margin-top: 30px;">
            <tr>
                <td width="33.33%"></td>
                <td width="33.33%">
                    <div class="approval-title">Mengetahui dan Menyetujui</div>
                    @if($ba->coo_approval == 'APPROVED' && $ba->cooApprover->signature_path)
                        <div class="signature-image">
                            <img src="{{ asset('storage/' . $ba->cooApprover->signature_path) }}" alt="Tanda Tangan">
                        </div>
                    @else
                        <div class="signature-line"></div>
                    @endif
                    <div>{{ $coo->nama_lengkap ?? 'Chief Operating Officer' }}</div>
                    <div class="position-label">{{ $coo->jabatan->nama_jabatan ?? 'Chief Operating Officer' }}</div>
                    @if($ba->coo_approval == 'APPROVED')
                        <div class="approval-info">
                            @if($ba->cooApprover->id != ($coo->id ?? 0))
                                <span class="delegate-label">Bertindak atas nama: {{ $ba->cooApprover->nama_lengkap }}</span>
                            @endif
                            @if($ba->coo_approval_notes)
                                <span class="approval-notes"><strong>{{ $ba->coo_approval_notes }}</strong></span>
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