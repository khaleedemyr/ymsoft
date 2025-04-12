@extends('layouts.master')

@section('title')
    {{ trans('translation.contra_bon.add') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .invoice-row {
            cursor: pointer;
        }
        .invoice-row.selected {
            background-color: #e8f3ff !important;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .loading-content {
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ trans('translation.contra_bon.form.title') }}</h5>
                </div>
                <div class="card-body">
                    <form id="createForm" action="{{ route('finance.contra-bons.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label required">{{ trans('translation.contra_bon.form.supplier') }}</label>
                                    <select class="form-select" name="supplier_id" id="supplier_id" required>
                                        <option value="">{{ trans('translation.contra_bon.form.select_supplier') }}</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label required">{{ trans('translation.contra_bon.form.issue_date') }}</label>
                                    <input type="date" class="form-control" name="issue_date" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label required">{{ trans('translation.contra_bon.form.due_date') }}</label>
                                    <input type="date" class="form-control" name="due_date" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <label class="form-label">{{ trans('translation.contra_bon.form.notes') }}</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="invoices-table">
                                <thead>
                                    <tr>
                                        <th width="5%"></th>
                                        <th>{{ trans('translation.contra_bon.form.invoice_number') }}</th>
                                        <th>{{ trans('translation.contra_bon.form.invoice_date') }}</th>
                                        <th>{{ trans('translation.contra_bon.form.due_date') }}</th>
                                        <th>{{ trans('translation.contra_bon.form.po_number') }}</th>
                                        <th class="text-end">{{ trans('translation.contra_bon.form.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="no-data">
                                        <td colspan="6" class="text-center">{{ trans('translation.contra_bon.message.select_supplier') }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-end">{{ trans('translation.contra_bon.form.total_amount') }}</th>
                                        <th class="text-end" id="total-amount">0</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line align-bottom me-1"></i>
                                    {{ trans('translation.save') }}
                                </button>
                                <a href="{{ route('finance.contra-bons.index') }}" class="btn btn-secondary">
                                    <i class="ri-arrow-left-line align-bottom me-1"></i>
                                    {{ trans('translation.back') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="d-flex justify-content-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            let selectedInvoices = [];

            // Format tanggal dari YYYY-MM-DD ke DD/MM/YYYY
            function formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            }

            $('#supplier_id').on('change', function() {
                const supplierId = $(this).val();
                if (!supplierId) {
                    $('#invoices-table tbody').html(`
                        <tr id="no-data">
                            <td colspan="6" class="text-center">{{ trans('translation.contra_bon.message.select_supplier') }}</td>
                        </tr>
                    `);
                    return;
                }

                // Tampilkan loading menggunakan SweetAlert
                Swal.fire({
                    title: 'Memuat Data',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Fetch invoices
                $.ajax({
                    url: `{{ url('finance/contra-bons/supplier') }}/${supplierId}/invoices`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            let html = '';
                            if (response.data && response.data.length > 0) {
                                response.data.forEach(invoice => {
                                    html += `
                                        <tr class="invoice-row" data-id="${invoice.id}" data-amount="${invoice.grand_total}">
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input invoice-checkbox">
                                            </td>
                                            <td>${invoice.invoice_number}</td>
                                            <td>${formatDate(invoice.invoice_date)}</td>
                                            <td>${formatDate(invoice.due_date)}</td>
                                            <td>${invoice.good_receive?.purchase_order?.po_number || '-'}</td>
                                            <td class="text-end">${formatNumber(invoice.grand_total)}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                html = `
                                    <tr id="no-data">
                                        <td colspan="6" class="text-center">{{ trans('translation.contra_bon.message.no_invoices') }}</td>
                                    </tr>
                                `;
                            }
                            $('#invoices-table tbody').html(html);
                            Swal.close();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ trans('translation.error') }}',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ trans('translation.error') }}',
                            text: xhr.responseJSON?.message || '{{ trans('translation.contra_bon.message.error_get_invoices') }}'
                        });
                    }
                });
            });

            // Format number function
            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }

            // Handle invoice selection
            $(document).on('change', '.invoice-checkbox', function() {
                const row = $(this).closest('tr');
                const invoiceId = row.data('id');
                const amount = parseFloat(row.data('amount'));

                if (this.checked) {
                    row.addClass('selected');
                    selectedInvoices.push({
                        id: invoiceId,
                        amount: amount
                    });
                } else {
                    row.removeClass('selected');
                    selectedInvoices = selectedInvoices.filter(inv => inv.id !== invoiceId);
                }

                updateTotal();
            });

            function updateTotal() {
                const total = selectedInvoices.reduce((sum, inv) => sum + inv.amount, 0);
                $('#total-amount').text(formatNumber(total));
            }

            // Handle form submission
            $('#createForm').on('submit', function(e) {
                e.preventDefault();

                if (selectedInvoices.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ trans('translation.error') }}',
                        text: '{{ trans('translation.contra_bon.message.select_invoices') }}'
                    });
                    return;
                }

                // Tampilkan loading saat submit
                Swal.fire({
                    title: 'Menyimpan Data',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Kumpulkan invoice_ids yang dipilih
                const invoiceIds = selectedInvoices.map(inv => inv.id);

                const formData = new FormData(this);
                // Hapus append yang lama
                // formData.append('invoices', JSON.stringify(selectedInvoices));
                
                // Tambahkan invoice_ids sebagai array
                invoiceIds.forEach((id, index) => {
                    formData.append(`invoice_ids[]`, id);
                });

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ trans('translation.success') }}',
                                text: response.message,
                                showCancelButton: false
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ trans('translation.error') }}',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ trans('translation.error') }}',
                            text: xhr.responseJSON?.message || '{{ trans('translation.contra_bon.message.error_create') }}'
                        });
                    }
                });
            });
        });
    </script>
@endsection 