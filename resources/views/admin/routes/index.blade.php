@extends('layouts.master')

@section('title', 'Route List')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        {{-- Subheader --}}
        <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                    <h4 class="text-success font-weight-bold my-2 mr-5">
                        Route
                        <small></small>
                    </h4>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Route List</h3>
                    <div class="card-toolbar">
                        <a class="btn btn-outline-primary" href="{{ route('admin.routes.create') }}">
                            <span class="fa fa-plus"></span> Create New Route
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
                                <tr role="row">
                                    <th style="width: 20%;">Route Name</th>
                                    <th style="width: 10%;">Zone</th>
                                    <th style="width: 55%;">Route Stations</th>
                                    <th style="width: 5%;">Status</th>
                                    <th class="text-center" style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($routes as $route)
                                    <tr class="{{ $loop->even ? 'even' : 'odd' }}">
                                        <td class="sorting_1">{{ $route->name }}</td>
                                        <td>{{ $route->zone->name ?? 'N/A' }}</td>
                                        <td>
                                            @if ($route->stations->isNotEmpty())
                                                @foreach ($route->stations as $station)
                                                    {{-- Success label for Start Station, Danger for End Station --}}
                                                    @php
                                                        $labelClass = 'light-info';
                                                        if ($station->pivot->sequence_order == 1) {
                                                            $labelClass = 'light-success'; // Start station
                                                        } elseif ($station->pivot->sequence_order == $route->stations->count()) {
                                                            $labelClass = 'light-danger'; // End station
                                                        }
                                                    @endphp
                                                    <span class="label label-inline label-{{ $labelClass }} mr-1">
                                                        {{ $station->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-danger">No Stations Assigned</span>
                                            @endif
                                        </td>
                                        <td class="text-capitalize">
                                            <span
                                                class="label label-inline label-light-{{ $route->status == 'active' ? 'success' : 'danger' }}">
                                                {{ $route->status }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a title="Edit" class="btn btn-icon btn-sm btn-outline-primary"
                                                href="{{ route('admin.routes.edit', $route->id) }}">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            {{-- Delete Form (not shown here, but implemented in controller) --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No routes found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
@endsection