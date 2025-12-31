@extends('layouts.master')

@section('title', 'User Manager')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        {{-- Page Header: User Manager (Matching the light green/teal style) --}}
        <div class="subheader subheader-solid" style="background:#d3f9ee;">
            <div class="container-fluid d-flex align-items-center justify-content-between">
                <h4 class="text-success font-weight-bold my-2">User Manager</h4>
            </div>
        </div>

        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">

                <div class="card card-custom">
                    <div class="card-header">
                        <h3 class="card-title">User List</h3>
                        <div class="card-toolbar">
                            {{-- Button to create a new user --}}
                            <a class="btn btn-primary" href="{{ route('admin.users.create') }}">
                                <span class="fa fa-plus"></span> Create New User
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive-lg">
                            {{-- The table where Datatables will be initialized --}}
                            <table id="kt_datatable" class="table-bordered table-striped gy-5 gs-7 bordered dataTable">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 px-7">
                                        {{-- Table Headers --}}
                                        <th width="15%">Name</th>
                                        <th width="10%">Mobile</th>
                                        <th width="10%">Username</th>
                                        <th width="15%">Roles</th>
                                        <th width="35%">Station / Counter</th>
                                        <th width="8%">Status</th>
                                        <th class="text-center" width="7%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @forelse($users as $user)

                                        @php
                                            // 1. Robustly fetch related data (assuming standard Eloquent relationships)
                                            $counter = $user->counter ?? null;
                                            $station = $counter?->station ?? null;
                                            $status = $user->status ?? 'N/A';

                                            // 2. Row background style: Inactive/Blocked rows have a light red background (as seen in screenshots)
                                            $rowClass = ($status == 'inactive' || $status == 'blocked') ? 'bg-light-danger' : '';

                                            // 3. Status badge styling: Active is green/teal, Blocked/Inactive is red/danger
                                            $badgeClass = ($status == 'active') ? 'badge-success' : (($status == 'blocked' || $status == 'inactive') ? 'badge-danger' : 'badge-secondary');

                                            // 4. Action button styling and logic
                                            $actionEditClass = 'btn-outline-success';   // Green Edit button
                                            $actionLogoutClass = 'btn-outline-warning'; // Orange Logout button
                                            $isLogoutDisabled = ($status != 'active');
                                        @endphp

                                        <tr class="{{ $rowClass }}" role="row">

                                            {{-- Name --}}
                                            <td>{{ $user->name ?? '--' }}</td>

                                            {{-- Mobile --}}
                                            <td>{{ $user->mobile_number ?? '--' }}</td>

                                            {{-- Username --}}
                                            <td>{{ $user->username ?? '--' }}</td>

                                            {{-- Roles --}}
                                            <td>
                                                <ul class="pl-4 mb-0" style="list-style-type: disc;">
                                                    @forelse($user->roles as $role)
                                                        <li>{{ $role->name }}</li>
                                                    @empty
                                                        <li>N/A</li>
                                                    @endforelse
                                                </ul>
                                            </td>

                                            {{-- Station / Counter --}}
                                            <td class="text-capitalize">
                                                @if($counter)
                                                    {{ $station->name ?? 'N/A Station' }} ⇒ {{ $counter->name ?? 'N/A Counter' }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>

                                            {{-- Status --}}
                                            <td class="text-capitalize">
                                                {{-- Replicating the Active/Blocked/Inactive badge style --}}
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            </td>

                                            {{-- Action --}}
                                            <td class="text-center">

                                                {{-- Edit Button --}}
                                                <a title="Edit" class="btn btn-icon btn-sm {{ $actionEditClass }}"
                                                    href="{{ route('admin.users.edit', $user->id) }}">
                                                    <i class="far fa-edit"></i>
                                                </a>

                                                {{-- Force Logout Button --}}
                                                <a title="Force Logout"
                                                    class="btn btn-icon btn-sm {{ $actionLogoutClass }} {{ $isLogoutDisabled ? 'disabled' : '' }}"
                                                    href="{{ route('admin.users.logout', $user->id) }}">
                                                    <i class="fas fa-sign-out-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No users found.</td>
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
@endsection

@push('scripts')
    {{-- DataTables JS is required to render the search/entries controls as seen in the UI --}}
    <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        // Ensure jQuery is loaded and the DOM is ready
        $(document).ready(function () {

            // Check if Datatables plugin is available before calling it
            if ($.fn.DataTable) {

                // Use the standard jQuery selector to initialize Datatables
                $('#kt_datatable').DataTable({
                    "language": {
                        "lengthMenu": "Show _MENU_",
                    },
                    "dom":
                        "<'row mb-2'" +
                        "<'col-sm-6 d-flex align-items-center justify-content-start dt-toolbar'l>" +
                        "<'col-sm-6 d-flex align-items-center justify-content-end dt-toolbar'f>" +
                        ">" +

                        "<'table-responsive'tr>" +

                        "<'row'" +
                        "<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i>" +
                        "<'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
                        ">",

                    // Add other general table configurations previously established:
                    responsive: true,
                    autoWidth: false,
                    // Assuming the Action column is index 6, adjust if your column count is different
                    columnDefs: [
                        { targets: 6, orderable: false, searchable: false }
                    ]
                });
            } else {
                console.error("Datatables plugin not found.");
            }
        });
    </script>
@endpush