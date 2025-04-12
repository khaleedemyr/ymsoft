@extends('layouts.master')

@section('title')
    {{ trans('translation.contra_bon.list') }}
@endsection

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="invoiceList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">{{ trans('translation.contra_bon.list') }}</h5>
                        <div class="flex-shrink-0">
                            <a href="{{ route('finance.contra-bons.create') }}" class="btn btn-primary">
                                <i class="ri-add-line align-bottom me-1"></i>
                                {{ trans('translation.contra_bon.add') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body bg-soft-light border border-dashed border-start-0 border-end-0">
                    <div class="table-responsive">
                        <table id="contra-bon-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{ trans('translation.contra_bon.index.table.contra_bon_number') }}</th>
                                    <th>{{ trans('translation.contra_bon.index.table.supplier') }}</th>
                                    <th>{{ trans('translation.contra_bon.index.table.issue_date') }}</th>
                                    <th>{{ trans('translation.contra_bon.index.table.due_date') }}</th>
                                    <th class="text-end">{{ trans('translation.contra_bon.index.table.total_amount') }}</th>
                                    <th>{{ trans('translation.contra_bon.index.table.status') }}</th>
                                    <th>{{ trans('translation.contra_bon.index.table.created_by') }}</th>
                                    <th>{{ trans('translation.contra_bon.index.table.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contraBons as $contraBon)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $contraBon->contra_bon_number }}</td>
                                        <td>{{ $contraBon->supplier->name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($contraBon->issue_date)) }}</td>
                                        <td>{{ date('d/m/Y', strtotime($contraBon->due_date)) }}</td>
                                        <td class="text-end">{{ number_format($contraBon->total_amount, 0) }}</td>
                                        <td>{!! trans('translation.contra_bon.status.' . $contraBon->status) !!}</td>
                                        <td>{{ $contraBon->createdBy->nama_lengkap ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('finance.contra-bons.show', $contraBon->id) }}" 
                                                   class="btn btn-sm btn-info"
                                                   data-bs-toggle="tooltip"
                                                   title="{{ trans('translation.contra_bon.show.title') }}">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">{{ trans('translation.no_data') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            $('#contra-bon-table').DataTable({
                order: [[2, 'desc']],
                pageLength: 10,
                language: {
                    url: "{{ URL::asset('build/libs/datatables/id.json') }}"
                },
            });
        });
    </script>
@endsection 