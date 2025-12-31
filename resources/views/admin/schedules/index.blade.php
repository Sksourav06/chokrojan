@extends('layouts.master')

@section('title', 'Schedule List')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        {{-- Subheader --}}
        <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                    <h4 class="text-success font-weight-bold my-2 mr-5">
                        Schedule
                        <small></small>
                    </h4>
                </div>
            </div>
        </div>
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="card card-custom">
                    <div class="card-header">
                        <h3 class="card-title">Schedule List</h3>
                        <div class="card-toolbar">
                            <a class="btn btn-outline-primary" href="{{ route('admin.schedules.create') }}">
                                <span class="fa fa-plus"></span> Create New Schedule
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                {{-- Filter Form --}}
                                <div class="card border bg-success-o-20 mb-5">
                                    <div class="card-body py-0">
                                        <form id="filter-form" method="GET" action="{{ route('admin.schedules.index') }}">
                                            @csrf
                                            <div class="row mt-5">
                                                <div class="col-md-2"></div>

                                                {{-- Filter by Zone --}}
                                                <div class="col-md-2 col-6 px-2">
                                                    <div class="form-group">
                                                        <select id="zone" class="form-control " name="zone"
                                                            data-live-search="true">
                                                            <option value="">All Zone...</option>
                                                            @foreach($zones as $id => $name)
                                                                <option value="{{ $id }}" {{ request('zone') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Filter by Route --}}
                                                <div class="col-md-4 col-6 px-2">
                                                    <div class="form-group">
                                                        <select id="route" class="form-control " name="route"
                                                            data-live-search="true">
                                                            <option value="">All Route...</option>
                                                            @foreach($routes as $id => $name)
                                                                <option value="{{ $id }}" {{ request('route') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Filter Button --}}
                                                <div class="col-md-2 col-6 px-2">
                                                    <button type="submit"
                                                        class="btn btn-pill btn-block btn-primary">Filter</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Schedule Table --}}
                        <div class="table-responsive-lg">
                            <table id="data_table" class="table table-bordered table-striped dataTable">
                                <thead>
                                    <tr role="row">
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 15%;">Coach Info</th>
                                        <th style="width: 10%;">Seat Plan</th>
                                        <th style="width: 35%;">Route</th>
                                        <th style="width: 10%;">Start</th>
                                        <th style="width: 10%;">End</th>
                                        <th style="width: 5%;">Status</th>
                                        <th class="text-center" style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($schedules as $schedule)
                                        <tr class="{{ $schedule->status != 'active' ? 'bg-inactive' : '' }}">
                                            <td class="sorting_1">{{ $schedule->id }}</td>
                                            <td>
                                                <span
                                                    class="label label-inline label-light-danger label-lg">{{ $schedule->name }}</span><br>
                                                <span
                                                    class="label label-inline label-light-warning">{{ $schedule->bus_type }}</span>
                                                <span class="label label-inline label-light-info"><i
                                                        class="fa fa-bus text-info fa-1x mr-2"></i>{{ $schedule->bus->registration_number ?? '--' }}</span>
                                            </td>
                                            <td>{{ $schedule->seatLayout->name ?? 'N/A' }}</td>
                                            <td>
                                                {{ $schedule->route->name ?? 'N/A' }}<br>
                                                {{-- Display Start and End Stations --}}
                                                @php
                                                    $stations = $schedule->route->stations->sortBy('pivot.sequence_order');
                                                    $startStation = $stations->first();
                                                    $endStation = $stations->last();
                                                @endphp
                                                <span
                                                    class="label label-inline label-light-success">{{ $startStation->name ?? 'N/A' }}</span>
                                                <span
                                                    class="label label-inline label-light-danger">{{ $endStation->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                {{ $schedule->startStation->name ?? 'No Start Station' }}<br>
                                                <span class="text-success">
                                                    {{ $schedule->start_time_only ? $schedule->start_time_only->format('h:i A') : '--:--' }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $schedule->endStation->name ?? 'No End Station' }}<br>
                                                <span class="text-danger">
                                                    {{ $schedule->end_time_only ? $schedule->end_time_only->format('h:i A') : '--:--' }}
                                                </span>
                                            </td>
                                            <td class="text-capitalize">{{ $schedule->status }}</td>
                                            <td class="text-center">
                                                <a title="Edit" class="btn btn-icon btn-sm btn-outline-primary"
                                                    href="{{ route('admin.schedules.edit', $schedule->id) }}">
                                                    <i class="far fa-edit"></i>
                                                </a>
                                                {{-- Copy button logic is complex but defined in your HTML --}}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No schedules found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal (Copy Schedule Modal HTML remains separate, as provided by the user) --}}
@endsection