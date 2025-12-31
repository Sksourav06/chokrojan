@extends('layouts.master')

@section('title', 'Loyalty Discount Report')

@section('content')
    <div class="container mt-5">

        <div class="card card-custom mb-5">
            <div class="card-header">
                <h3 class="card-title">Loyalty Discount Summary</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <strong>মোট লয়ালটি টিকিট:</strong> {{ $reportSummary['total_loyalty_tickets'] }} টি
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-success">
                            <strong>মোট ডিসকাউন্ট প্রদান:</strong>
                            {{ number_format($reportSummary['total_discount_given'], 2) }} ৳
                        </div>
                    </div>
                </div>

                {{-- Date Filter Form (Optional) --}}
                <form method="GET" action="{{ route('admin.reports.loyalty_report') }}" class="form-row align-items-end">
                    <div class="col-md-4 form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <button type="submit" class="btn btn-primary">Filter Report</button>
                        <a href="{{ route('admin.reports.loyalty_report') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-header">
                <h3 class="card-title">Discounted Tickets Detail</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center">
                        <thead>
                            <tr>
                                <th>PNR No.</th>
                                <th>Issue Date</th>
                                <th>Journey Date</th>
                                <th>Customer</th>
                                <th>Number</th>
                                <th>Route</th>
                                <th>Seats</th>
                                <th>Discount (৳)</th>
                                <th>Grand Total (৳)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->pnr_no }}</td>
                                    <td>{{ \Carbon\Carbon::parse($ticket->issue_date)->format('Y-m-d h:i A') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($ticket->journey_date)->format('Y-m-d') }}</td>
                                    <td>{{ $ticket->customer_name }}</td>
                                    <td> {{ $ticket->customer_mobile }}</td>
                                    <td>{{ $ticket->fromStation->name ?? 'N/A' }} to {{ $ticket->toStation->name ?? 'N/A' }}
                                    </td>
                                    <td>{{ $ticket->seat_numbers }} ({{ $ticket->seats_count }})</td>
                                    <td class="text-success">{{ number_format($ticket->discount_amount, 2) }}</td>
                                    <td>{{ number_format($ticket->grand_total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No loyalty tickets found for this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
@endsection