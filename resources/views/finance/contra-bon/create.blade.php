@section('script')
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            $('#supplier_id').on('change', function() {
                let supplierId = $(this).val();
                
                if (supplierId) {
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

                    $.ajax({
                        url: "{{ route('finance.contra-bon.get-supplier-invoices') }}",
                        type: 'GET',
                        data: { supplier_id: supplierId },
                        success: function(response) {
                            // Tutup loading
                            Swal.close();
                            
                            let tbody = $('#invoicesTable tbody');
                            tbody.empty();
                            
                            if (response.data.length > 0) {
                                response.data.forEach(function(invoice) {
                                    let row = `
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input invoice-checkbox" name="invoice_ids[]" value="${invoice.id}">
                                                </div>
                                            </td>
                                            <td>${invoice.number}</td>
                                            <td>${invoice.invoice_date}</td>
                                            <td>${invoice.due_date}</td>
                                            <td class="text-end">${formatNumber(invoice.total)}</td>
                                            <td>${invoice.purchase_order ? invoice.purchase_order.number : '-'}</td>
                                        </tr>
                                    `;
                                    tbody.append(row);
                                });
                            } else {
                                tbody.append('<tr><td colspan="6" class="text-center">Tidak ada faktur yang tersedia</td></tr>');
                            }
                        },
                        error: function(xhr) {
                            // Tutup loading
                            Swal.close();
                            
                            Swal.fire({
                                title: 'Error!',
                                text: 'Gagal memuat data faktur',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });

            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }
        });
    </script>
@endsection 