@extends('layouts.master')

@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        {{-- Subheader --}}
        <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                    <h4 class="text-success font-weight-bold my-2 mr-5">
                        Booking Report
                    </h4>
                </div>
            </div>
        </div>
        <div class="content d-flex flex-column flex-column-fluid">
            <div class="container-fluid">

                {{-- Filter Card --}}
                <div class="card card-custom mb-5">
                    <div class="card-header">
                        <h3 class="card-title">Overall Booking Report</h3>
                    </div>
                    <div class="card-body">
                        <form id="filter-form" method="GET" action="{{ route('admin.reports.booking') }}">
                            <div class="row">
                                {{-- Filter By --}}
                                <div class="col-md-2 col-4 px-2">
                                    <div class="form-group">
                                        <label for="filter_by">Filter By</label>
                                        <select id="filter_by" name="filter_by" class="form-control">
                                            <option value="issue_date" {{ ($filterType ?? '') === 'issue_date' ? 'selected' : '' }}>Issue Date</option>
                                            <option value="journey_date" {{ ($filterType ?? '') === 'journey_date' ? 'selected' : '' }}>Journey Date</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- From Date --}}
                                <div class="col-md-2 col-4 px-2">
                                    <div class="form-group">
                                        <label for="from_date">From Date</label>
                                        <input type="date" id="from_date" name="from_date" class="form-control"
                                            value="{{ $fromDate ?? now()->format('Y-m-d') }}" required>
                                    </div>
                                </div>

                                {{-- To Date --}}
                                <div class="col-md-2 col-4 px-2">
                                    <div class="form-group">
                                        <label for="to_date">To Date</label>
                                        <input type="date" id="to_date" name="to_date" class="form-control"
                                            value="{{ $toDate ?? now()->format('Y-m-d') }}" required>
                                    </div>
                                </div>

                                {{-- Counter --}}
                                <div class="col-md-2 col-6 px-2">
                                    <div class="form-group">
                                        <label for="counter_id">Counter List</label>
                                        <select id="counter_id" name="counter_id" class="form-control">
                                            <option value="">All Counter...</option>
                                            @foreach($counters as $counter)
                                                <option value="{{ $counter->id }}" {{ ($counterId ?? '') == $counter->id ? 'selected' : '' }}>{{ $counter->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Counter Master --}}
                                <div class="col-md-2 col-6 px-2">
                                    <div class="form-group">
                                        <label for="master_id">Counter Master List</label>
                                        <select id="master_id" name="master_id" class="form-control">
                                            <option value="">All Counter Master...</option>
                                            @foreach($masters as $master)
                                                <option value="{{ $master->id }}" {{ ($masterId ?? '') == $master->id ? 'selected' : '' }}>{{ $master->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Filter Button --}}
                                <div class="col-md-1 col-6 px-2">
                                    <button type="submit" class="btn btn-pill btn-block btn-primary mt-8">Filter</button>
                                </div>

                                {{-- Print Button --}}
                                <div class="col-md-1 col-6 px-2 text-right">
                                    <button type="button" class="btn btn-pill btn-info mt-8"
                                        onclick="PrintBookingReport();">Print</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                {{-- Booked Tickets Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Ticket No</th>
                                <th>Passenger Info</th>
                                <th>Booked By</th>
                                <th>Booked Date</th>
                                <th>Journey Date</th>
                                <th>From - To</th>
                                <th>Coach</th>
                                <th>Seat</th>
                                <th>Fare</th>
                                <th>⊕ & ⊝</th>
                                <th>Net Fare</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalSeats = 0;
                                $totalFare = 0;
                                $totalDiscount = 0;
                                $totalCommission = 0;
                                $netTotal = 0;
                            @endphp

                            @foreach($tickets as $key => $ticket)
                                @php
                                    // Row-level calculations
                                    $seatCount = $ticket->seats_count ?? 0;
                                    $fare = $ticket->fare ?? 0; // Standardizing to 'fare' column
                                    $discount = $ticket->discount_amount ?? 0;
                                    $commission = $ticket->callerman_commission ?? 0;

                                    // Calculate Net Fare for the row
                                    $netFare = max($fare - $discount + $commission, 0);

                                    // Accumulate Grand Totals
                                    $totalSeats += $seatCount;
                                    $totalFare += $fare;
                                    $totalDiscount += $discount;
                                    $totalCommission += $commission;
                                    $netTotal += $netFare;
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ ltrim($ticket->pnr_no ?? '', 'INV-') }}</td>
                                    <td class="text-left">
                                        <div class="fw-bold">{{ $ticket->customer_name }}</div>
                                        <small>{{ $ticket->customer_mobile ?? '--' }}</small>
                                    </td>
                                    <td>{{ optional($ticket->issuedBy)->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($ticket->issue_date)->format('d-M-Y h:i A') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($ticket->schedule->start_time)->format('d-M-Y') }}</td>
                                    <td class="text-left">{{ $ticket->fromStation->name ?? 'N/A' }} -
                                        {{ $ticket->toStation->name ?? 'N/A' }}</td>
                                    <td>{{ $ticket->schedule->name ?? 'N/A' }}</td>
                                    <td>{{ $ticket->seat_numbers }}</td>
                                    <td class="text-right">{{ number_format($fare, 0) }}</td>
                                    <td>
                                        <span class="text-danger">⊝ Discount: {{ number_format($discount, 0) }}</span> <br>
                                        <span class="text-primary">⊕ Commission: {{ number_format($commission, 0) }}</span>
                                    </td>
                                    <td class="text-success fw-bold text-right">{{ number_format($netFare, 0) }}</td>
                                </tr>
                            @endforeach

                            @if($tickets->count())
                                <tr class="fw-bold bg-light">
                                    <td colspan="8" class="text-end">Grand Total:</td>
                                    <td class="text-center">{{ $totalSeats }}</td>
                                    <td class="text-right">{{ number_format($totalFare, 0) }}</td>
                                    <td>
                                        <span class="text-danger">⊝ {{ number_format($totalDiscount, 0) }}</span> <br>
                                        <span class="text-primary">⊕ {{ number_format($totalCommission, 0) }}</span>
                                    </td>
                                    <td class="text-success text-right" style="border-top: 2px double #198754;">
                                        {{ number_format($netTotal, 0) }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <script>
            function PrintBookingReport() {
                window.print();
            }
        </script>
    </div>

@endsection