<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Barang YMSoft</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 18pt;
        }
        .header p {
            margin: 5px 0;
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            page-break-inside: auto;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px;
            font-size: 8pt;
            text-align: left;
            word-break: break-word;
            max-width: 150px;
        }
        th {
            background-color: #f0f0f0;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }
        .footer p {
            margin: 2px 0;
        }
        .page-break {
            page-break-after: always;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DAFTAR BARANG YMSOFT</h1>
        <p>Tanggal: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    @php
        // Hitung total halaman yang dibutuhkan
        $itemsPerPage = 25;
        $totalPages = ceil($items->count() / $itemsPerPage);
    @endphp

    @foreach($items->chunk($itemsPerPage) as $chunk)
    <table>
        @if($loop->first)
        <thead>
            <tr>
                <th class="text-center" width="30">No</th>
                <th width="60">SKU</th>
                <th width="100">Nama</th>
                <th width="80">Kategori</th>
                <th width="80">Sub Kategori</th>
                <th width="60">Satuan Kecil</th>
                <th width="60">Satuan Sedang</th>
                <th width="60">Satuan Besar</th>
                <th width="50">Status</th>
                <th width="120">Harga</th>
            </tr>
        </thead>
        @endif
        <tbody>
            @foreach($chunk as $key => $item)
            <tr>
                <td class="text-center">{{ ($loop->parent->index * $itemsPerPage) + $loop->iteration }}</td>
                <td>{{ $item->sku }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category->name }}</td>
                <td>{{ $item->subCategory->name }}</td>
                <td>{{ $item->smallUnit->name }}</td>
                <td>{{ $item->mediumUnit->name }}</td>
                <td>{{ $item->largeUnit->name }}</td>
                <td class="text-center">
                    {{ $item->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                </td>
                <td>
                    @foreach($item->prices as $price)
                        {{ $price->region->name }}: Rp {{ number_format($price->price, 0, ',', '.') }}@if(!$loop->last),@endif
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
    @endforeach

    <div class="footer">
        <p>Halaman {PAGE_NUM} dari {PAGE_COUNT}</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Dicetak oleh: {{ auth()->user()->name }}</p>
    </div>
</body>
</html> 