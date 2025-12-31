@extends('layouts.master')

@section('title', 'Staff Manager')

@push('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet">
@endpush
@push('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet">
    <style>
        /* 1. Fix the boxy 'length' dropdown */
        .dataTables_length select {
            width: auto;
            display: inline-block;
            padding: 0.45rem 1.75rem 0.45rem 0.75rem;
            font-size: 0.9rem;
            font-weight: 400;
            line-height: 1.5;
            color: #3F4254;
            background-color: #ffffff;
            border: 1px solid #E4E6EF;
            border-radius: 0.42rem !important;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3e%3cpath fill='%23B5B5C3' d='M2 0L0 2h4zm0 5L0 3h4z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 8px 10px;
        }

        /* 2. Fix sorting icon alignment and appearance */
        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_desc:after {
            opacity: 0.6;
            content: "\f0dc";
            /* FontAwesome sort icon */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            font-size: 0.8rem;
            right: 10px !important;
        }

        /* 3. Give the table header more breathing room */
        .table.table-head-custom thead th {
            padding-top: 1rem;
            padding-bottom: 1rem;
            vertical-align: middle;
            background-color: #F3F6F9;
            border-bottom: 1px solid #EBEDF3 !important;
        }
    </style>
@endpush
@section('content')
    <div class="subheader subheader-solid" style="background:#d3f9ee;">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <h4 class="text-success font-weight-bold my-2">Staff Manager</h4>
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">

            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">Staff List</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('admin.staffs.create') }}" class="btn btn-outline-primary">
                            <i class="fa fa-plus"></i> Create New Staff
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <table id="kt_datatable" class="table table-bordered table-striped nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Designation</th>
                                <th>Job Type</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staffs as $staff)
                                <tr>
                                    <td>{{ $staff->name }}</td>
                                    <td>{{ $staff->mobile_number }}</td>
                                    <td>{{ $staff->designation->name ?? 'N/A' }}</td>
                                    <td class="text-capitalize">{{ $staff->job_type }}</td>
                                    <td class="text-capitalize">{{ $staff->status }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.staffs.edit', $staff->id) }}"
                                            class="btn btn-icon btn-sm btn-outline-primary">
                                            <i class="far fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#kt_datatable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[0, 'asc']],
                columnDefs: [
                    { targets: 5, orderable: false, searchable: false }
                ],
                // This 'dom' configuration cleans up the layout of the search and dropdown
                dom: `<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                                     "<'row'<'col-sm-12'tr>>" +
                                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
                language: {
                    'lengthMenu': 'Display _MENU_',
                },
            });
        });
    </script>
@endpush