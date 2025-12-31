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
                                <th style="min-width: 40px">#</th>
                                <th>Ticket No</th>
                                <th>Passenger Info</th>
                                <th>Cancelled By</th>
                                <th>Cancelled At</th>
                                <th>Journey Date</th>
                                <th>Route/Coach</th>
                                <th>Seats</th>
                                <th>Fare</th>
                                <th>(-) Deductions</th>
                                <th>(+) Additions</th>
                                <th>Net Fare</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalSeats = 0;
                                $totalFare = 0;
                                $totalDeductions = 0;
                                $totalAdditions = 0;
                                $totalNet = 0;
                            @endphp

                            @foreach($cancelledTickets as $key => $cancellation)
                                @php
                                    $ticket = $cancellation->ticket;
                                    $seatCount = $ticket->seats_count ?? 0;
                                    $fare = $ticket->fare ?? 0;
                                    $disc = $ticket->discount_amount ?? 0;
                                    $counterComm = $ticket->counter_commission_amount ?? 0;
                                    $callermanComm = $ticket->callerman_commission ?? 0;
                                    $goods = $ticket->goods_charge ?? 0;

                                    // Net Fare Logic: (Fare + Callerman + Goods) - (Discount + Counter Commission)
                                    $netFare = ($fare + $callermanComm + $goods) - ($disc + $counterComm);

                                    $totalSeats += $seatCount;
                                    $totalFare += $fare;
                                    $totalDeductions += ($disc + $counterComm);
                                    $totalAdditions += ($callermanComm + $goods);
                                    $totalNet += $netFare;
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ ltrim($ticket->pnr_no ?? '') }}</td>
                                    <td class="text-left">
                                        <div class="fw-bold">{{ $ticket->customer_name }}</div>
                                        <small>{{ $ticket->customer_mobile ?? '--' }}</small>
                                    </td>
                                    <td>{{ optional($cancellation->cancelledByUser)->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($cancellation->cancelled_at)->format('d-M-Y h:i A') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($ticket->schedule->start_time)->format('d-M-Y') }}</td>
                                    <td>{{ $ticket->fromStation->name ?? 'N/A' }} - {{ $ticket->toStation->name ?? 'N/A' }}</td>
                                    <td>{{ $ticket->schedule->name ?? 'N/A' }}</td>
                                    <td>{{ $ticket->seat_numbers }}</td>
                                    <td class="text-right">{{ number_format($ticket->fare ?? 0, 0) }}</td>
                                    <td>
                                        ⊝ Discount: {{ number_format($totalDeductions, 0) }} <br>
                                        ⊕ Counter Com.: {{ number_format($totalAdditions, 0) }}
                                    </td>
                                    <td class="text-success fw-bold">{{ number_format($totalNet, 0) }}</td>
                                </tr>
                            @endforeach

                            @if($cancelledTickets->count())
                                <tr class="fw-bold text-success">
                                    <td colspan="8" class="text-end">Total:</td>
                                    <td>{{ $totalSeats }}</td>
                                    <td>{{ number_format($totalFare, 0) }}</td>
                                    <td>
                                        ⊝ {{ number_format($totalDeductions, 0) }} <br>
                                        ⊕ {{ number_format($totalAdditions, 0) }}
                                    </td>
                                    <td>{{ number_format($totalNet, 0) }}</td>
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