@extends('layouts.master')

@section('title', 'Bus List')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        {{-- Subheader --}}
        <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                    <h4 class="text-success font-weight-bold my-2 mr-5">
                        Bus Manager
                        <small></small>
                    </h4>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Bus List</h3>
                    <div class="card-toolbar">
                        <a class="btn btn-outline-primary" href="{{ route('admin.buses.create') }}">
                            <span class="fa fa-plus"></span> Create New Bus
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive-lg">
                        <table id="data_table" class="table table-bordered table-striped dataTable">
                            <thead>
                                <tr>
                                    <th style="width: 15%;">Registration Number</th>
                                    <th style="width: 10%;">Make Year</th>
                                    <th style="width: 15%;">Model</th>
                                    <th style="width: 10%;">Bus Type</th>
                                    <th style="width: 25%;">Seat Plan</th>
                                    <th style="width: 10%;">Status</th>
                                    <th class="text-center" style="width: 15%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($buses as $bus)
                                    <tr>
                                        <td class="sorting_1">{{ $bus->registration_number }}</td>
                                        <td>{{ $bus->make_year ?? 'N/A' }}</td>
                                        <td>{{ $bus->model_name ?? 'N/A' }}</td>
                                        <td>{{ $bus->bus_type ?? 'N/A' }}</td>
                                        <td>
                                            @if ($bus->seatLayout)
                                                {{ $bus->seatLayout->name }} (Seats: {{ $bus->seatLayout->total_seats ?? 'N/A' }})
                                            @else
                                                <span class="text-danger">Layout Missing</span>
                                            @endif
                                        </td>
                                        <td class="text-capitalize">{{ $bus->status }}</td>
                                        <td class="text-center">
                                            {{-- Edit Button --}}
                                            <a title="Edit" class="btn btn-icon btn-sm btn-outline-primary"
                                                href="{{ route('admin.buses.edit', $bus->id) }}">
                                                <i class="far fa-edit"></i>
                                            </a>

                                            {{-- ⭐ NEW: Delete Button (Uses DELETE method) ⭐ --}}
                                            <form method="POST" action="{{ route('admin.buses.destroy', $bus->id) }}"
                                                style="display: inline;"
                                                onsubmit="return confirm('Are you sure you want to delete this bus ({{ $bus->registration_number }})?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" title="Delete Bus"
                                                    class="btn btn-icon btn-sm btn-outline-danger">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No buses found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
@endsection