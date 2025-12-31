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
                    <h4 class="text-success font-weight-bold my-2 mr-5">Sales Report</h4>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column-fluid" bis_skin_checked="1">
            <div class="container-fluid" bis_skin_checked="1">
                <div class="card card-custom" bis_skin_checked="1">
                    <div class="card-header" bis_skin_checked="1">
                        <h3 class="card-title">Overall Sales Report</h3>
                    </div>
                    <div class="card-body" bis_skin_checked="1">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card border bg-success-o-20 mb-10">
                                    <div class="card-body py-0">
                                        <form id="filter-form" method="GET" action="{{ route('admin.reports.sales') }}">
                                            <div class="row mt-5">
                                                <!-- Filter By -->
                                                <div class="col-md-2 col-4 px-2">
                                                    <div class="form-group">
                                                        <label for="filter_by">Filter By</label>
                                                        <select id="filter_by" name="filter_by" class="form-control">
                                                            <option value="issued_at" {{ $filterType === 'issued_at' ? 'selected' : '' }}>Issue Date</option>
                                                            <option value="journey_date" {{ $filterType === 'journey_date' ? 'selected' : '' }}>Journey Date</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- From Date -->
                                                <div class="col-md-2 col-4 px-2">
                                                    <div class="form-group">
                                                        <label for="from_date" class="required">From Date</label>
                                                        <input type="date" id="from_date" name="from_date"
                                                            class="form-control rounded-0" value="{{ $fromDate }}" required>
                                                    </div>
                                                </div>
                                                <!-- To Date -->
                                                <div class="col-md-2 col-4 px-2">
                                                    <div class="form-group">
                                                        <label for="to_date" class="required">To Date</label>
                                                        <input type="date" id="to_date" name="to_date"
                                                            class="form-control rounded-0" value="{{ $toDate }}" required>
                                                    </div>
                                                </div>
                                                <!-- Counter Dropdown -->
                                                <div class="col-md-2 col-6 px-2">
                                                    <div class="form-group">
                                                        <label for="counter_id">Counter List</label>
                                                        <select id="counter_id" name="counter_id" class="form-control">
                                                            <option value="">All Counter...</option>
                                                            @foreach($counters as $counter)
                                                                <option value="{{ $counter->id }}" {{ $counterId == $counter->id ? 'selected' : '' }}>{{ $counter->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- Counter Master Dropdown -->
                                                <div class="col-md-2 col-6 px-2">
                                                    <div class="form-group">
                                                        <label for="master_id">Counter Master List</label>
                                                        <select id="master_id" name="master_id" class="form-control">
                                                            <option value="">All Counter Master...</option>
                                                            @foreach($masters as $master)
                                                                <option value="{{ $master->id }}" {{ $masterId == $master->id ? 'selected' : '' }}>{{ $master->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- Filter Button -->
                                                <div class="col-md-1 col-6 px-2">
                                                    <button type="submit"
                                                        class="btn btn-pill btn-block btn-primary px-5 mt-8">Filter</button>
                                                </div>
                                                <!-- Print Button -->
                                                <div class="col-md-1 col-6 px-2 text-right">
                                                    <button type="button" id="print" onclick="PrintSalesReport();"
                                                        class="btn btn-pill btn-info px-5 mt-8">Print</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Ticket No</th>
                                        <th>Passenger Info</th>
                                        <th>Issue By</th>
                                        <th>Issue Date</th>
                                        <th>Journey Date</th>
                                        <th>From - To</th>
                                        <th>Coach</th>
                                        <th>Seats</th>
                                        <th>Fare</th>
                                        <th>⊕ & ⊝</th>
                                        <th>Net Fare</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        // হিসাবের শুরুতে সব ভেরিয়েবল রিসেট করা হচ্ছে
                                        $row = 1;
                                        $totalSeats = 0;
                                        $totalFare = 0;
                                        $totalDiscount = 0;
                                        $totalCommission = 0;
                                        $totalCounterCommission = 0;
                                        $totalGoodsCharge = 0;
                                        $netTotal = 0;
                                    @endphp

                                    @foreach($tickets as $ticket)
                                        @php
                                            // ১. সিট সংখ্যা নির্ধারণ
                                            $seatNumbers = $ticket->seat_numbers ?? '';
                                            if ($seatNumbers && $seatNumbers !== 'No seats assigned') {
                                                $seatNumbersData = explode(',', $seatNumbers);
                                                $seatCount = count($seatNumbersData);
                                            } else {
                                                $seatNumbers = 'No seats assigned';
                                                $seatCount = 0;
                                            }

                                            // ২. ডাটাবেজ কলাম থেকে মান সংগ্রহ (Null থাকলে ০ ধরা হচ্ছে)
                                            $fare = $ticket->fare ?? 0;
                                            $discount = $ticket->discount_amount ?? 0;
                                            $callermanComm = $ticket->callerman_commission ?? 0;
                                            $counterComm = $ticket->counter_commission_amount ?? 0;
                                            $goodsCharge = $ticket->goods_charge ?? 0;

                                            // যদি সিট না থাকে তবে আর্থিক মানগুলো ০
                                            if ($seatCount == 0) {
                                                $fare = 0;
                                                $discount = 0;
                                                $callermanComm = 0;
                                                $counterComm = 0;
                                                $goodsCharge = 0;
                                            }

                                            // ৩. নেট ফেয়ার ক্যালকুলেশন:
                                            // সূত্র: (ভাড়া + গুডস চার্জ + কলারম্যান কমিশন) - (ডিসকাউন্ট + কাউন্টার কমিশন)
                                            $calculatedNet = ($fare + $goodsCharge + $callermanComm) - ($discount + $counterComm);
                                            $netFare = $ticket->grand_total ?? max($calculatedNet, 0);

                                            // ৪. গ্র্যান্ড টোটাল সামারি আপডেট
                                            $totalSeats += $seatCount;
                                            $totalFare += $fare;
                                            $totalDiscount += $discount;
                                            $totalCommission += $callermanComm;
                                            $totalCounterCommission += $counterComm;
                                            $totalGoodsCharge += $goodsCharge;
                                            $netTotal += $netFare;
                                        @endphp

                                        <tr>
                                            <td>{{ $row++ }}</td>

                                            {{-- PNR নাম্বার --}}
                                            <td>{{ ltrim($ticket->pnr_no, 'PNR-') }}</td>

                                            <td>
                                                <div class="fw-bold">{{ $ticket->customer_name }}</div>
                                                <small class="text-muted">{{ $ticket->customer_mobile }}</small>
                                            </td>

                                            <td>{{ $ticket->issuedBy->name ?? 'N/A' }}</td>

                                            <td><small>{{ \Carbon\Carbon::parse($ticket->issue_date)->format('d-M-y h:i A') }}</small>
                                            </td>
                                            <td><small>{{ \Carbon\Carbon::parse($ticket->schedule->start_time)->format('d-M-y h:i A') }}</small>
                                            </td>

                                            <td><small>{{ $ticket->fromStation->name }} - {{ $ticket->toStation->name }}</small>
                                            </td>
                                            <td>{{ $ticket->schedule->name ?? 'N/A' }}</td>

                                            <td>{{ $seatNumbers }}</td>

                                            {{-- মূল ভাড়া --}}
                                            <td>{{ number_format($fare, 0) }}</td>

                                            {{-- ডিসকাউন্ট ও কমিশন সেকশন (+) পার্টে গুডস চার্জ যোগ করা হয়েছে --}}
                                            <td>
                                                <div class="text-nowrap" style="font-size: 0.85rem;">
                                                    <span class="text-danger">(-)
                                                        {{ number_format($discount + $counterComm, 0) }}</span> <br>
                                                    <span class="text-primary">(+)
                                                        {{ number_format($callermanComm + $goodsCharge, 0) }}</span>
                                                </div>
                                            </td>

                                            {{-- নিট ফেয়ার --}}
                                            <td class="fw-bold text-success text-end">{{ number_format($netFare, 0) }}</td>
                                        </tr>
                                    @endforeach

                                    {{-- গ্র্যান্ড টোটাল সামারি সারি --}}
                                    @if($tickets->count())
                                        <tr class="fw-bold bg-light">
                                            <td colspan="8" class="text-end">Grand Total:</td>
                                            <td class="text-center">{{ $totalSeats }}</td>
                                            <td>{{ number_format($totalFare, 0) }}</td>
                                            <td>
                                                <div style="font-size: 0.8rem;">
                                                    <span class="text-danger">(-)
                                                        {{ number_format($totalDiscount + $totalCounterCommission, 0) }}</span><br>
                                                    <span class="text-primary">(+)
                                                        {{ number_format($totalCommission + $totalGoodsCharge, 0) }}</span>
                                                </div>
                                            </td>
                                            {{-- চূড়ান্ত নেট টোটাল --}}
                                            <td class="text-success text-end fs-5"
                                                style="border-top: 2px solid #198754; border-bottom: 3px double #198754;">
                                                {{ number_format($netTotal, 0) }}
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection