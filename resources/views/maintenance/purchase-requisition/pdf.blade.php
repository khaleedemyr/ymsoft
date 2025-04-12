<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Requisition - {{ $pr->pr_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 400px;
            max-height: 150px;
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
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('build/images/logo/LOGO_JUSTUS_GROUP_1024X500.jpg') }}" class="logo">
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
        <table class="approval-table">
            <tr>
                <td>
                    <div class="approval-title">Dibuat Oleh</div>
                    <div class="signature-line"></div>
                    <div>{{ $pr->creator->nama_lengkap }}</div>
                </td>
                <td>
                    <div class="approval-title">Disetujui Oleh</div>
                    <div class="signature-line"></div>
                    <div>Chief Engineering</div>
                    @if($pr->chief_engineering_approval == 'APPROVED')
                    <div>
                        {{ optional($pr->chiefEngineeringApprover)->nama_lengkap }}
                        <br>
                        {{ date('d M Y', strtotime($pr->chief_engineering_approval_date)) }}
                    </div>
                    @endif
                </td>
                <td>
                    <div class="approval-title">Disetujui Oleh</div>
                    <div class="signature-line"></div>
                    <div>Purchasing Manager</div>
                    @if($pr->purchasing_manager_approval == 'APPROVED')
                    <div>
                        {{ optional($pr->purchasingManagerApprover)->nama_lengkap }}
                        <br>
                        {{ date('d M Y', strtotime($pr->purchasing_manager_approval_date)) }}
                    </div>
                    @endif
                </td>
            </tr>
        </table>
        
        <table class="approval-table" style="margin-top: 20px;">
            <tr>
                <td colspan="3">
                    <div class="approval-title">Disetujui Oleh</div>
                    <div class="signature-line"></div>
                    <div>COO</div>
                    @if($pr->coo_approval == 'APPROVED')
                    <div>
                        {{ optional($pr->cooApprover)->nama_lengkap }}
                        <br>
                        {{ date('d M Y', strtotime($pr->coo_approval_date)) }}
                    </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
