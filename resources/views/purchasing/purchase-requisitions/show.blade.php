@extends('layouts.master')

@section('title')
    Detail Purchase Requisition
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Purchasing
        @endslot
        @slot('title')
            Detail Purchase Requisition
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title mb-0 flex-grow-1">Purchase Requisition</h4>
                        <div class="flex-shrink-0">
                            @php
                                $user = auth()->user();
                                $userJabatanId = $user->id_jabatan;
                                
                                // Debug untuk memeriksa data
                                \Log::info('User Data:', [
                                    'user_id' => $user->id,
                                    'jabatan_id' => $userJabatanId,
                                    'roles' => $user->user_roles->pluck('role_id')->toArray()
                                ]);

                                // User bisa approve sebagai SSD jika:
                                // 1. Memiliki jabatan Asst. Manager SSD (172) atau Manager SSD (161), ATAU
                                // 2. Memiliki role_id = 1
                                $canApproveAsSSD = in_array($userJabatanId, [172, 161]) || 
                                                 $user->user_roles->contains('role_id', 1);
                                
                                // User bisa approve sebagai CC jika:
                                // 1. Memiliki jabatan Cost Control Manager (167), ATAU
                                // 2. Memiliki role_id = 1
                                $canApproveAsCC = $userJabatanId == 167 || 
                                                $user->user_roles->contains('role_id', 1);

                                \Log::info('Approval Access:', [
                                    'canApproveAsSSD' => $canApproveAsSSD,
                                    'canApproveAsCC' => $canApproveAsCC,
                                    'current_status' => $pr->status
                                ]);
                            @endphp

                            {{-- Tombol Approve SSD --}}
                            @if(($pr->status === 'draft' || $pr->status === null) && $canApproveAsSSD)
                                <button type="button" class="btn btn-success btn-sm me-2" onclick="approvePR('ssd')">
                                    <i class="ri-check-line"></i> Approve SSD
                                </button>
                            @endif

                            {{-- Tombol Approve Cost Control --}}
                            @if($pr->status === 'approved_ssd' && $canApproveAsCC)
                                <button type="button" class="btn btn-success btn-sm me-2" onclick="approvePR('cc')">
                                    <i class="ri-check-line"></i> Approve Cost Control
                                </button>
                            @endif

                            {{-- Tombol Edit hanya muncul jika status masih draft --}}
                            @if($pr->status === 'draft' || $pr->status === null)
                                <a href="{{ route('purchasing.purchase-requisitions.edit', $pr->id) }}" class="btn btn-primary btn-sm me-2">
                                    <i class="ri-edit-2-line"></i> Edit
                                </a>
                            @endif
                            
                            <a href="{{ route('purchasing.purchase-requisitions.index') }}" class="btn btn-secondary btn-sm">
                                <i class="ri-arrow-left-line"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="30%">Nomor PR</td>
                                    <td width="5%">:</td>
                                    <td><strong>{{ $pr->pr_number }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>{{ date('d/m/Y', strtotime($pr->date)) }}</td>
                                </tr>
                                <tr>
                                    <td>Departemen</td>
                                    <td>:</td>
                                    <td>{{ $pr->department }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="30%">Status</td>
                                    <td width="5%">:</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'draft' => 'warning',
                                                'submitted' => 'info',
                                                'approved' => 'success',
                                                'rejected' => 'danger'
                                            ];
                                            $statusColor = $statusColors[$pr->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }}">
                                            {{ ucfirst($pr->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Dibuat Oleh</td>
                                    <td>:</td>
                                    <td>{{ $pr->creator->nama_lengkap ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Diminta Oleh</td>
                                    <td>:</td>
                                    <td>{{ $pr->requester->nama_lengkap ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($pr->notes)
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Catatan:</strong>
                                <p class="mb-0">{{ $pr->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">No</th>
                                            <th>Item</th>
                                            <th width="25%">Informasi Stok & Konversi</th>
                                            <th width="10%" class="text-center">Qty</th>
                                            <th width="15%">Satuan</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pr->items as $index => $item)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ $item->item->name }}</td>
                                                <td>
                                                    @php
                                                        $inventory = $item->item->inventories->first();
                                                        $stockOnHand = $inventory ? $inventory->stock_on_hand : 0;
                                                        
                                                        // Log data untuk setiap item yang ditampilkan
                                                        \Log::info('View Item Stock:', [
                                                            'item_id' => $item->item_id,
                                                            'item_name' => $item->item->name,
                                                            'inventory_exists' => !is_null($inventory),
                                                            'stock_on_hand' => $stockOnHand,
                                                            'inventory_data' => $inventory ? $inventory->toArray() : null,
                                                            'all_inventories' => $item->item->inventories->toArray()
                                                        ]);
                                                    @endphp

                                                    {{-- Stock Dasar --}}
                                                    <div class="stock-info">
                                                        <strong>Stok Dasar:</strong> 
                                                        {{ number_format($stockOnHand, 0) }} {{ $item->item->smallUnit->name ?? '-' }}
                                                    </div>

                                                    {{-- Konversi Menengah --}}
                                                    @if($item->item->mediumUnit)
                                                        @php
                                                            // 1 Pack = 250 Gram
                                                            $mediumConversion = $item->item->small_conversion_qty ?: 1;  // Menggunakan small_conversion_qty
                                                            
                                                            // Hitung berapa Pack (12510 / 250 = 50 Pack)
                                                            $mediumQty = floor($stockOnHand / $mediumConversion);
                                                            // Sisa dalam Gram (12510 % 250 = 10 Gram)
                                                            $smallRemainder = $stockOnHand % $mediumConversion;

                                                            \Log::info('Medium Conversion Calculation:', [
                                                                'item_id' => $item->item_id,
                                                                'stock_on_hand' => $stockOnHand,
                                                                'medium_conversion' => $mediumConversion,
                                                                'medium_qty' => $mediumQty,
                                                                'small_remainder' => $smallRemainder
                                                            ]);
                                                        @endphp
                                                        <div class="stock-info">
                                                            <strong>Konversi Menengah:</strong><br>
                                                            {{ number_format($mediumQty, 0) }} {{ $item->item->mediumUnit->name }}
                                                            @if($smallRemainder > 0)
                                                                + {{ number_format($smallRemainder, 0) }} {{ $item->item->smallUnit->name }}
                                                            @endif
                                                            <br>
                                                            <span class="unit-info">
                                                                (1 {{ $item->item->mediumUnit->name }} = 
                                                                {{ number_format($mediumConversion, 0) }} {{ $item->item->smallUnit->name }})
                                                            </span>
                                                        </div>
                                                    @endif

                                                    {{-- Konversi Besar --}}
                                                    @if($item->item->largeUnit)
                                                        @php
                                                            // Karena 1 Pack Besar = 1 Pack Sedang = 250 Gram
                                                            $largeConversion = $item->item->small_conversion_qty ?: 1;  // Sama dengan medium conversion
                                                            
                                                            // Hitung berapa Pack Besar (12510 / 250 = 50 Pack)
                                                            $largeQty = floor($stockOnHand / $largeConversion);
                                                            // Sisa dalam Gram (12510 % 250 = 10 Gram)
                                                            $smallQty = $stockOnHand % $largeConversion;

                                                            \Log::info('Large Conversion Calculation:', [
                                                                'item_id' => $item->item_id,
                                                                'stock_on_hand' => $stockOnHand,
                                                                'large_conversion' => $largeConversion,
                                                                'large_qty' => $largeQty,
                                                                'small_qty' => $smallQty
                                                            ]);
                                                        @endphp
                                                        <div class="stock-info">
                                                            <strong>Konversi Besar:</strong><br>
                                                            {{ number_format($largeQty, 0) }} {{ $item->item->largeUnit->name }}
                                                            @if($smallQty > 0)
                                                                + {{ number_format($smallQty, 0) }} {{ $item->item->smallUnit->name }}
                                                            @endif
                                                            <br>
                                                            <span class="unit-info">
                                                                (1 {{ $item->item->largeUnit->name }} = 
                                                                1 {{ $item->item->mediumUnit->name }} = 
                                                                {{ number_format($largeConversion, 0) }} {{ $item->item->smallUnit->name }})
                                                            </span>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ number_format($item->quantity, 0) }}</td>
                                                <td>{{ $item->unit->name }}</td>
                                                <td>{{ $item->notes ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada data item</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
function approvePR(type) {
    let title = type === 'ssd' ? 'Approve SSD' : 'Approve Cost Control';
    
    Swal.fire({
        title: title,
        text: 'Apakah Anda yakin ingin menyetujui PR ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Setuju',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('purchasing.purchase-requisitions.approve', $pr->id) }}",
                type: 'POST',
                data: {
                    type: type
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Sukses!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    let message = 'Terjadi kesalahan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        title: 'Error!',
                        text: message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}
</script>
@endsection

@section('css')
<style>
    .unit-info {
        font-size: 12px;
        color: #6c757d;
    }

    .stock-info {
        border-left: 3px solid #0ab39c;
        padding-left: 8px;
        margin: 4px 0;
    }
</style>
@endsection 