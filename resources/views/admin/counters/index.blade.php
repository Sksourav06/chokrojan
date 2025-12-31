@extends('layouts.master')

@section('title', 'Counter List')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        {{-- Subheader --}}
        <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                    <h4 class="text-success font-weight-bold my-2 mr-5">
                        Counter
                        <small></small>
                    </h4>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Counter List</h3>
                    <div class="card-toolbar">
                        <a class="btn btn-outline-primary" href="{{ route('admin.counters.create') }}">
                            <span class="fa fa-plus"></span> Create New Counter
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive-lg">
                        <table id="kt_datatable" class="table table-bordered table-striped dataTable">
                            <thead>
                                <tr role="row">
                                    <th style="width: 10%;">Station</th>
                                    <th style="width: 15%;">Counter Name</th>
                                    <th style="width: 8%;">Type</th>
                                    <th style="width: 25%;">Route Wise Comm.</th>
                                    <th style="width: 10%;">Credit Limit</th>
                                    <th style="width: 10%;">Credit Balance</th>
                                    <th style="width: 10%;">Permitted Credit</th>
                                    <th style="width: 5%;">Status</th>
                                    <th class="text-center" style="width: 7%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($counters as $counter)
                                    <tr class="{{ $loop->even ? 'even' : 'odd' }}">
                                        <td class="sorting_1">{{ $counter->station->name ?? 'N/A' }}</td>
                                        <td>{{ $counter->name }}</td>
                                        <td>{{ $counter->counter_type }}</td>
                                        <td>
                                            @if ($counter->routes->isNotEmpty())
                                                <ul class="pl-3 mb-0" style="list-style-type: none;">
                                                    {{-- ⭐ Route Wise Commission Placeholder ⭐ --}}
                                                    @foreach ($counter->routes as $route)
                                                        <li>
                                                            <small>{{ $route->name }} (AC =
                                                                <span
                                                                    class="text-success">{{ number_format($route->pivot->ac_commission, 0) }}
                                                                    ৳</span>, NonAC =
                                                                <span
                                                                    class="text-danger">{{ number_format($route->pivot->non_ac_commission, 0) }}
                                                                    ৳</span>)
                                                            </small>
                                                        </li>
                                                    @endforeach
                                            @else
                                                    N/A (Add Routes/Commissions)
                                                @endif
                                        </td>
                                        <td>{{ number_format($counter->credit_limit, 0) }}</td>
                                        <td>{{ number_format($counter->credit_balance, 0) }}</td>
                                        <td>{{ number_format($counter->permitted_credit, 0) }}</td>
                                        <td class="text-capitalize">{{ $counter->status }}</td>
                                        <td class="text-center">
                                            <a title="Edit" class="btn btn-icon btn-sm btn-outline-primary"
                                                href="{{ route('admin.counters.edit', $counter->id) }}">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.counters.destroy', $counter->id) }}"
                                                style="display: inline;"
                                                onsubmit="return confirm('Are you sure you want to delete counter {{ $counter->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete Counter"
                                                    class="btn btn-icon btn-sm btn-outline-danger">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No counters found.</td>
                                    </tr>
                                @endforelse
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
                if ($.fn.DataTable) {
                    // Initialize DataTables
                    const dataTable = $('#kt_datatable').DataTable({
                        responsive: true,
                        autoWidth: false,
                        lengthChange: false, // Disable default length change as we handle it manually
                        searching: false,    // Disable default search as we handle it manually
                        order: [[0, 'asc']],
                        pageLength: 25, // Default page length
                        columnDefs: [
                            // Action column is the 7th column (index 6)
                            { targets: 6, orderable: false, searchable: false }
                        ]
                    });

                    // Manually link custom search and length controls (to replicate the visual output)
                    $('#data_table_length_select').on('change', function () {
                        dataTable.page.len($(this).val()).draw();
                    });

                    $('#data_table_search_input').on('keyup', function () {
                        dataTable.search(this.value).draw();
                    });

                } else {
                    console.error("DataTables plugin is missing or failed to load.");
                }
            });
        </script>
    @endpush