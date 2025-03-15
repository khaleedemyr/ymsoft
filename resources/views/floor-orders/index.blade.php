@extends('layouts.master')

@section('title')
    {{ __('translation.floor_order.index.title') }}
@endsection

@section('css')
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    
    <style>
        .search-box {
            min-width: 200px;
        }
        
        .dataTables_filter {
            display: none;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        @media (max-width: 576px) {
            .card-tools {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .search-box {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('translation.floor_order.index.title') }}</h1>
                    <h2 class="text-muted">{{ auth()->user()->outlet->nama_outlet ?? __('translation.floor_order.index.outlet_not_found') }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div>
                        @component('components.breadcrumb')
                            @slot('li_1')
                                Transaksi
                            @endslot
                            @slot('title')
                                Floor Order
                            @endslot
                        @endcomponent

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center">
                                        <h6 class="card-title flex-grow-1 mb-0">{{ __('translation.floor_order.index.subtitle') }}</h6>
                                        <div class="flex-shrink-0">
                                            <div class="d-flex flex-wrap gap-2">
                                                <div class="search-box">
                                                    <input type="text" class="form-control search" placeholder="{{ __('translation.floor_order.index.search_placeholder') }}">
                                                </div>
                                                <div class="input-group" style="width: auto;">
                                                    <input type="date" class="form-control" name="from" id="from">
                                                    <input type="date" class="form-control" name="to" id="to">
                                                    <button type="button" id="filter" class="btn btn-primary">{{ __('translation.floor_order.index.filter') }}</button>
                                                    <button type="button" id="refresh" class="btn btn-default">{{ __('translation.floor_order.index.refresh') }}</button>
                                                </div>
                                                <a href="{{ route('floor-orders.create') }}" class="btn btn-primary">
                                                    <i class="ri-add-line align-bottom me-1"></i> {{ __('translation.floor_order.index.add_new') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="table-responsive table-card">
                                            <table class="table table-bordered" id="floorOrderTable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('translation.floor_order.index.table.no') }}</th>
                                                        <th>{{ __('translation.floor_order.index.table.fo_number') }}</th>
                                                        <th>{{ __('translation.floor_order.index.table.order_date') }}</th>
                                                        <th>{{ __('translation.floor_order.index.table.order_time') }}</th>
                                                        <th>{{ __('translation.floor_order.index.table.warehouse') }}</th>
                                                        <th>{{ __('translation.floor_order.index.table.created_by') }}</th>
                                                        <th>{{ __('translation.floor_order.index.table.total_amount') }}</th>
                                                        <th>{{ __('translation.floor_order.index.table.status') }}</th>
                                                        <th>{{ __('translation.floor_order.index.table.action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($floorOrders as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $item->fo_number }}</td>
                                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                                        <td>{{ $item->created_at->format('H:i') }}</td>
                                                        <td>{{ optional($item->warehouse)->name ?? '-' }}</td>
                                                        <td>{{ optional($item->user)->nama_lengkap ?? '-' }}</td>
                                                        <td class="text-end">{{ number_format($item->total_amount, 0, ',', '.') }}</td>
                                                        <td>
                                                            @switch($item->status)
                                                                @case('draft')
                                                                    <span class="badge bg-warning text-dark">Draft</span>
                                                                    @break
                                                                @case('saved')
                                                                    <span class="badge bg-info">Saved</span>
                                                                    @break
                                                                @case('completed')
                                                                    <span class="badge bg-success">Completed</span>
                                                                    @break
                                                                @case('cancelled')
                                                                    <span class="badge bg-danger">Cancelled</span>
                                                                    @break
                                                                @default
                                                                    <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                @if($item->status === 'draft')
                                                                    <a href="{{ route('floor-orders.edit', $item->id) }}" 
                                                                       class="btn btn-sm btn-primary">
                                                                        <i class="ri-pencil-line"></i>
                                                                    </a>

                                                                    <button type="button" 
                                                                            onclick="saveDraft('{{ $item->id }}', '{{ $item->fo_number }}')"
                                                                            class="btn btn-sm btn-success">
                                                                        <i class="ri-save-line"></i> Save
                                                                    </button>
                                                                @endif

                                                                <a href="{{ route('floor-orders.show', $item->id) }}" 
                                                                   class="btn btn-sm btn-info">
                                                                    <i class="ri-eye-line"></i>
                                                                </a>

                                                                @if(in_array($item->status, ['draft', 'saved']))
                                                                    <button type="button" 
                                                                            onclick="deleteFloorOrder('{{ $item->id }}', '{{ $item->fo_number }}')"
                                                                            class="btn btn-sm btn-danger">
                                                                        <i class="ri-delete-bin-line"></i>
                                                                    </button>
                                                                @endif

                                                                
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-primary">
                                                        <td colspan="6" class="text-end fw-bold">{{ __('translation.floor_order.index.table.grand_total') }}:</td>
                                                        <td class="text-end fw-bold" id="grandTotal">0</td>
                                                        <td colspan="2"></td>
                                                    </tr>
                                                    
                                                    <!-- Grand Total per Status -->
                                                  <!--  @foreach($floorOrders->groupBy('status') as $status => $orders)
                                                    <tr class="table-secondary">
                                                        <td colspan="5" class="text-end">Total {{ ucfirst($status) }}:</td>
                                                        <td class="text-end">{{ number_format($orders->sum('total_amount'), 0, ',', '.') }}</td>
                                                        <td colspan="2"></td>
                                                    </tr>
                                                    @endforeach-->
                                                </tfoot>
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
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
<script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Custom date range filter
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var from = $('#from').val();
        var to = $('#to').val();
        var date = data[2]; // index 2 adalah kolom tanggal order

        if (from === '' && to === '') return true;
        
        if (from !== '' && to !== '') {
            // Convert date format dd/mm/yyyy to yyyy-mm-dd for comparison
            var parts = date.split('/');
            var dateStr = parts[2] + '-' + parts[1] + '-' + parts[0];
            return (dateStr >= from && dateStr <= to);
        }
        
        return true;
    });

    var table = $('#floorOrderTable').DataTable({
        dom: 'rt<"bottom"ip>',
        pageLength: 10,
        ordering: true,
        responsive: true,
        order: [[2, 'desc']], // Order by tanggal order descending
    });

    // Live search
    $('.search').keyup(function() {
        table.search($(this).val()).draw();
    });

    // Date filter
    $('#filter').click(function() {
        table.draw();
    });

    // Refresh button
    $('#refresh').click(function() {
        // Clear inputs
        $('#from').val('');
        $('#to').val('');
        $('.search').val('');
        
        // Reset table
        table
            .search('')
            .order([[2, 'desc']])
            .draw();
    });

    // Set default date range (hari ini)
    var today = new Date().toISOString().split('T')[0];
    $('#from').val(today);
    $('#to').val(today);
    
    // Trigger filter on load
    table.draw();

    // Delete confirmation
    $('.delete-btn').click(function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Fungsi untuk format number
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Fungsi untuk parse number dari format Indonesia
    function parseNumber(str) {
        return parseFloat(str.replace(/\./g, '')) || 0;
    }

    // Fungsi untuk update grand total
    function updateGrandTotal() {
        let total = 0;
        
        // Cek semua baris yang visible
        $('#floorOrderTable tbody tr:visible').each(function() {
            // Sesuaikan dengan index kolom total_amount
            let amount = $(this).find('td:eq(5)').text();
            total += parseNumber(amount);
        });
        
        // Update tampilan grand total
        $('#grandTotal').text(formatNumber(total));
        
        console.log('Grand Total Updated:', total); // Debug log
    }

    // Cek apakah menggunakan DataTable
    let dataTableInstance = $('#floorOrderTable').DataTable();
    
    if (typeof dataTableInstance !== 'undefined') {
        // Jika menggunakan DataTable
        console.log('Using DataTable');
        
        dataTableInstance.on('draw.dt', function() {
            console.log('DataTable redrawn');
            updateGrandTotal();
        });
    } else {
        // Jika tidak menggunakan DataTable
        console.log('Using regular table');
        
        // Monitor perubahan pada input pencarian
        $('.search').on('keyup', function() {
            console.log('Search input changed');
            updateGrandTotal();
        });
        
        // Monitor perubahan pada filter tanggal
        $('input[name="daterange"]').on('apply.daterangepicker', function() {
            console.log('Date filter changed');
            updateGrandTotal();
        });
    }

    // Hitung grand total awal
    updateGrandTotal();
    
    console.log('Script initialized'); // Debug log
});
</script>

<script>
    // Setup CSRF token untuk semua request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Fungsi Save Draft
    window.saveDraft = function(id, foNumber) {
        Swal.fire({
            title: '{{ __("translation.floor_order.index.confirm.save_title") }}',
            text: '{{ __("translation.floor_order.index.confirm.save_text") }}'.replace(':number', foNumber),
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '{{ __("translation.floor_order.index.action.save") }}',
            cancelButtonText: '{{ __("translation.floor_order.index.action.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/floor-orders/${id}/save-draft`,
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("translation.floor_order.index.success.title") }}',
                                text: '{{ __("translation.floor_order.index.success.status_changed") }}'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('{{ __("translation.floor_order.index.error.title") }}', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('{{ __("translation.floor_order.index.error.title") }}', '{{ __("translation.floor_order.index.error.general") }}', 'error');
                    }
                });
            }
        });
    }

    // Fungsi Delete
    window.deleteFloorOrder = function(id, foNumber) {
        Swal.fire({
            title: '{{ __("translation.floor_order.index.confirm.delete_title") }}',
            text: '{{ __("translation.floor_order.index.confirm.delete_text") }}'.replace(':number', foNumber),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __("translation.floor_order.index.action.delete") }}',
            cancelButtonText: '{{ __("translation.floor_order.index.action.cancel") }}',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/floor-orders/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("translation.floor_order.index.success.title") }}',
                                text: '{{ __("translation.floor_order.index.success.deleted") }}'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('{{ __("translation.floor_order.index.error.title") }}', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseJSON);
                        Swal.fire('{{ __("translation.floor_order.index.error.title") }}', '{{ __("translation.floor_order.index.error.delete") }}', 'error');
                    }
                });
            }
        });
    }
</script>

<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
