@extends('layouts.master')

@section('title')
    Detail Item
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Detail Item</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Kode Item</th>
                            <td>{{ $item->sku }}</td>
                        </tr>
                        <tr>
                            <th>Nama Item</th>
                            <td>{{ $item->name }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>{{ optional($item->category)->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Sub Kategori</th>
                            <td>{{ optional($item->subcategory)->name ?? '-' }}</td>
                        </tr>
                        <!-- Tambahkan field lainnya sesuai kebutuhan -->
                    </table>
                </div>

                <div class="mt-3">
                    <button type="button" class="btn btn-primary me-2" onclick="window.print()">
                        <i class="ri-printer-line"></i> Print
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.close()">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    @media print {
        .btn { display: none; }
        /* Tambahkan style lain untuk print jika diperlukan */
    }
</style>
@endsection
