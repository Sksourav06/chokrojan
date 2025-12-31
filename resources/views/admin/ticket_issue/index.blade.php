@extends('layouts.master')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        {{-- Search/Filter Card --}}
        <div class="card p-3">
            <form method="GET" action="{{ route('admin.ticket_issue.index') }}" class="row g-2">

                <div class="col-md-3">
                    <select name="from_station" class="form-control">
                        <option value="">From All Stations</option>
                        @foreach($stationFareList as $station)
                            <option value="{{ $station }}" {{ request('from_station') == $station ? 'selected' : '' }}>
                                {{ $station }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="to_station" class="form-control">
                        <option value="">To All Stations</option>
                        @foreach($stationFareList as $station)
                            <option value="{{ $station }}" {{ request('to_station') == $station ? 'selected' : '' }}>
                                {{ $station }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-6 px-2 ">
                    <input type="date" name="date" id="date" class="form-control"
                        value="{{ request('date') ?? date('Y-m-d') }}" {{-- ✅ Max attribute restricts date selection in
                        future --}} max="{{ $maxDate ?? date('Y-m-d') }}" {{-- ✅ FIX: Min attribute allows counter to view
                        previous days up to $minDate --}} min="{{ $minDate ?? date('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <input type="text" name="coach_number" class="form-control" placeholder="Search Coach"
                        value="{{ request('coach_number') }}">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-success w-100">Search</button>
                </div>

            </form>
        </div>

        <br>

        {{-- Trip List Table --}}
        <div class="card p-0">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-success">
                    <tr>
                        <th width="120">Coach</th>
                        <th width="180">Departure</th>
                        <th width="180">Arrival</th>
                        <th width="80">Sold</th>
                        <th width="80">Booked</th>
                        <th width="80">Available</th>
                        <th width="90">Fare</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trips as $trip)
                        <tr class="trip-row" data-id="{{ $trip->id }}"
                            data-coach="{{ $trip->bus->registration_number ?? $trip->bus->coach_number ?? '-' }}"
                            style="cursor:pointer;">

                            {{-- Coach / Route Details --}}
                            <td>
                                {{ $trip->route->name ?? 'N/A' }}
                                <span class="label label-inline label-light-danger" style="min-width: 70%; font-size: 15px;">
                                    {{ $trip->name ?? 'N/A' }}
                                </span>
                                <span class="text-nowrap label label-inline label-light-info"
                                    style="min-width: 40%; font-size: 13px;">
                                    <i class="fas fa-bus fa-1x text-info mr-2"></i>{{ $trip->bus->registration_number ?? '--' }}
                                </span>
                                <span class="badge bg-info">{{ $trip->bus_type ?? 'N/A' }}</span>
                            </td>

                            {{-- Departure Column --}}
                            <td>
                                <div class="fw-bold">{{ request('from_station') ?? $trip->startStation->name ?? 'N/A' }}</div>
                                <div class="small text-danger">
                                    {{ \Carbon\Carbon::parse($trip->start_time)->format('d M Y, h:i A') }}
                                </div>
                            </td>

                            {{-- **FIXED & CLEANED:** Arrival Column (Robust Time Calculation) --}}
                            @php
                                $toStationName = request('to_station');
                                // 1. Try to find the Station ID based on the name from the request
                                $toStationId = \App\Models\Station::where('name', $toStationName)->value('id');

                                $totalMinutes = 0;
                                $toStationDisplay = $toStationName ?? $trip->endStation->name ?? 'N/A';

                                // 2. Calculate time only if a destination station is selected and found
                                if ($toStationId && $trip->route && $trip->route->routeStationSequences) {

                                    $destinationSequence = $trip->route->routeStationSequences->first(function ($seq) use ($toStationId) {
                                        return $seq->station_id == $toStationId;
                                    });

                                    // 3. Calculate total minutes if the destination is a stop on this route
                                    if ($destinationSequence) {
                                        $requiredTime = $destinationSequence->required_time ?? '00:00:00';

                                        // Convert HH:MM:SS to total minutes
                                        $timeParts = explode(':', $requiredTime);
                                        $hours = (int) ($timeParts[0] ?? 0);
                                        $minutes = (int) ($timeParts[1] ?? 0);

                                        // Total travel time from the route's starting point
                                        $totalMinutes = ($hours * 60) + $minutes;
                                    }
                                }

                                // Fallback: If no destination selected/found, use the trip's default end station time
                                if (!$totalMinutes) {
                                    $destinationSequence = $trip->route->routeStationSequences->first(function ($seq) use ($trip) {
                                        return $seq->station_id == $trip->end_station_id;
                                    });
                                    if ($destinationSequence) {
                                        $requiredTime = $destinationSequence->required_time ?? '00:00:00';
                                        $timeParts = explode(':', $requiredTime);
                                        $hours = (int) ($timeParts[0] ?? 0);
                                        $minutes = (int) ($timeParts[1] ?? 0);
                                        $totalMinutes = ($hours * 60) + $minutes;
                                    }
                                }

                                // 4. Calculate Arrival Time (Start Time + Total Minutes)
                                $calculatedArrivalTime = \Carbon\Carbon::parse($trip->start_time)->addMinutes($totalMinutes);

                            @endphp

                            <td>
                                <div class="fw-bold">{{ $toStationDisplay }}</div>
                                <div class="small text-success">
                                    {{ $calculatedArrivalTime->format('d M Y, h:i A') }}
                                </div>
                            </td>

                            {{-- Seats and Fare Calculation (Unchanged) --}}
                            @php
                                // Seats calculation
                                $soldSeats = $trip->soldSeats ?? collect();
                                $soldCount = $soldSeats->where('status_label', 'Sold')->sum('seats_count');
                                $bookedCount = $soldSeats->where('status_label', 'Booked')->sum('seats_count');
                                $totalSeats = $trip->seat_layout->total_seats ?? 0;
                                $availableSeats = $totalSeats - ($soldCount + $bookedCount);

                                // Fare calculation based on selected stations
                                $fromStationId = \App\Models\Station::where('name', request('from_station'))->value('id') ?? $trip->start_station_id;
                                $toStationId = \App\Models\Station::where('name', request('to_station'))->value('id') ?? $trip->end_station_id;

                                $fareObj = $trip->routeFares->first(function ($f) use ($fromStationId, $toStationId) {
                                    return $f->origin_station_id == $fromStationId && $f->destination_station_id == $toStationId;
                                });

                                $fare = $fareObj?->price ?? $trip->route->fare ?? 0;
                            @endphp

                            {{-- Seats --}}
                            <td class="fw-bold">{{ $soldCount }}</td>
                            <td class="fw-bold">{{ $bookedCount }}</td>
                            <td class="fw-bold text-primary">{{ $availableSeats }}</td>

                            {{-- Fare --}}
                            <td class="fw-bold">{{ number_format($fare) }}</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No trips found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {

            // 🔥 Trip row click
            $(document).on('click', '.trip-row', function () {

                const tripId = $(this).data('id');
                const clickedRow = $(this);
                const targetRowId = 'trip-ui-row-' + tripId;

                // Column count auto detect (SAFE)
                const COLSPAN_COUNT = clickedRow.children('td').length;

                // ❌ Close other opened UIs
                $('tr[id^="trip-ui-row-"]').not('#' + targetRowId).remove();

                // Toggle same row
                if ($('#' + targetRowId).length) {
                    $('#' + targetRowId).remove();
                    return;
                }

                // Create UI row
                clickedRow.after(`
                                    <tr id="${targetRowId}">
                                        <td colspan="${COLSPAN_COUNT}" class="p-0">
                                            <div class="text-center p-4">
                                                <div class="spinner spinner-success spinner-lg"></div>
                                                <p class="mt-3">Loading Ticket Issue Interface...</p>
                                            </div>
                                        </td>
                                    </tr>
                                `);

                // Scroll
                $('html, body').animate({
                    scrollTop: $('#' + targetRowId).offset().top - 80
                }, 400);

                // 🔥 AJAX load UI
                $.ajax({
                    url: "/admin/ticket-issue-ui/" + tripId,
                    type: "GET",
                    success: function (res) {

                        // Inject HTML
                        $('#' + targetRowId + ' td').html(res);

                        // 🔥 CRITICAL GLOBAL SET
                        window.TRIP_ID = tripId;

                        // 🔥 INIT dynamic UI
                        if (typeof initTicketIssueUI === 'function') {
                            initTicketIssueUI();
                        }

                    },
                    error: function (xhr) {
                        $('#' + targetRowId + ' td').html(`
                                            <div class="alert alert-danger m-3">
                                                Failed to load ticket UI<br>
                                                ${xhr.status} - ${xhr.statusText}
                                            </div>
                                        `);
                    }
                });
            });

        });
        $(document).ready(function () {
            // Initialize Selectpicker (Dropdowns)
            $('.selectpicker').selectpicker();

            // Initialize Datepicker
            $('.datepicker').datepicker();

            // Form Submission UI feedback

        });
    </script>

@endpush