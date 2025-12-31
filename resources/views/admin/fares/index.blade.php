@extends('layouts.master')

@section('title', 'Fare List')

@section('content')
    <div class="container-fluid">
        <div class="card card-custom">
            <div class="card-header">
                <h3 class="card-title">Fare List</h3>
                <div class="card-toolbar">
                    <a class="btn btn-outline-primary" href="{{ route('admin.fares.create') }}">
                        <span class="fa fa-plus"></span> Create New Fare
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
                                <th style="width: 15%;">Name</th>
                                <th style="width: 20%;">Route</th>
                                <th style="width: 8%;">Bus Type</th>
                                <th style="width: 15%;">Seat Plan</th>
                                {{-- Seat Types column removed as it's usually defined by Seat Plan --}}
                                <th style="width: 25%;">Fares (Station Pairs)</th>
                                <th style="width: 10%;">Date Period</th>
                                <th style="width: 5%;">Status</th>
                                <th class="text-center" style="width: 5%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($fares as $fare)
                                <tr class="{{ $fare->status == 'inactive' ? 'bg-inactive' : '' }}">
                                    <td class="sorting_1">{{ $fare->name }}</td>
                                    <td>{{ $fare->route->name ?? 'N/A' }}</td>
                                    <td>{{ $fare->bus_type }}</td>
                                    <td>{{ $fare->seatLayout->name ?? 'N/A' }}</td>

                                    {{-- Fares Column: Loop through the station prices --}}
                                    <td>
                                        @forelse ($fare->stationPrices as $price)
                                            {{ $price->origin->name ?? '?' }} - {{ $price->destination->name ?? '?' }} =
                                            <strong>[{{ number_format($price->price, 0) }}]</strong>
                                            <br>
                                        @empty
                                            <span class="text-danger">No prices set</span>
                                        @endforelse
                                    </td>

                                    <td>
                                        {{ $fare->start_date ? \Carbon\Carbon::parse($fare->start_date)->format('d-m-Y') : 'All Time' }}
                                        <br>
                                        {{ $fare->end_date ? \Carbon\Carbon::parse($fare->end_date)->format('d-m-Y') : '' }}
                                    </td>
                                    <td class="text-capitalize">{{ $fare->status }}</td>
                                    <td class="text-center">
                                        <a title="Edit" class="btn btn-icon btn-sm btn-outline-primary"
                                            href="{{ route('admin.fares.edit', $fare->id) }}">
                                            <i class="far fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No fares found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection