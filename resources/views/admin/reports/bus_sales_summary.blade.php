@extends('layouts.master')

@section('content')
<div class="d-flex flex-column-fluid">
    <div class=" container-fluid ">
        <div class="card card-custom">
            <div class="card-header">
                <h3 class="card-title">
                    Overall Bus Sales Summary
                </h3>
            </div>
            <div class="card-body">
                
                {{-- 1. FILTER SECTION --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border bg-success-o-20 mb-10">
                            <div class="card-body py-0">
                                {{-- 🚨 ACTION: Form action should use dynamic route helper --}}
                                <form id="filter-form" method="GET" action="{{ route('admin.reports.bus-sales-summary') }}">
                                    @csrf 
                                    <div class="row mt-5">
                                        <div class="col-md-2"></div>
                                        
                                        {{-- Bus List Filter (Dropdown) --}}
                                        <div class="col-md-4 col-6 px-2">
                                            <div class="form-group">
                                                <label for="bus_id" class="">Bus List</label>
                                                {{-- FIX: Removed extra dropdown HTML nesting --}}
                                                <select id="bus_id" class="form-control " name="bus_id" data-size="10" data-live-search="true">
                                                    <option value="">All Bus...</option>
                                                    {{-- Using $allBuses for data and $selectedBusId for retention --}}
                                                    @foreach($allBuses ?? [] as $bus)
                                                        <option value="{{ $bus->id }}" 
                                                            {{ ($selectedBusId ?? '') == $bus->id ? 'selected' : '' }}>
                                                            {{ $bus->registration_number }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Date Filter (Single Input) --}}
                                        <div class="col-md-2 col-6 px-2">
                                            <div class="form-group">
                                                <label for="date" class="required">Date</label>
                                                {{-- 🚨 FIX 1: Retain the selected date using $inputDate --}}
                                                <input type="text" id="date" class="form-control datepicker" 
                                                    placeholder="Enter date" name="date" 
                                                    value="{{ $inputDate ?? date('d-m-Y') }}" required readonly="readonly">
                                            </div>
                                        </div>

                                        <div class="col-md-2 col-6 px-2">
                                            <button type="submit" class="btn btn-pill btn-block btn-primary px-5 mt-8">Filter</button>
                                        </div>
                                        <div class="col-md-2 col-6 px-2 text-right">
                                            <button type="button" id="print" onclick="PrintSalesReport();" class="btn btn-pill btn-info px-5 mt-8">Print</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- 2. SALES REPORT TABLE --}}
                <div id="sales-report-table">
                    @php
                        $grandTotalSoldSeats = 0;
                        $grandTotalAmount = 0;
                        $counter = 0;
                        // Total table columns = 6
                    @endphp

                    <div style="width:100%; float:left; margin-bottom:15px;">
                        <div style="width:50%; float:left;">
                            <h3 class="box-title" style="margin:0px;">
                                [{{ $selectedBusName ?? 'All Bus' }}] Coach Wise Sales Summary Report
                            </h3>
                        </div>
                        <div style="width:50%; float:right; text-align:right; font-size:12px;">
                            Date: <strong>{{ $inputDate ?? date('d-m-Y') }}</strong>
                        </div>
                    </div>

                    <div style="width:100%; float: left;">
                        <table class="table table-bordered dataTable" style="width:100%;">
                            <thead>
                                <tr style="background:#f6f6f6;">
                                    <th width="10%" style="text-align:center;">#</th>
                                    <th width="15%" style="text-align:center;">Bus Number</th>
                                    <th width="15%" style="text-align:center;">Coach Number</th>
                                    <th width="30%" style="text-align:left;">Route</th>
                                    <th width="15%" style="text-align:center;">Sold Seats</th>
                                    <th width="15%" style="text-align:center;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
    @php
        $counter = 0;
        $grandTotalSoldSeats = 0;
        $grandTotalAmount = 0;
    @endphp

    @forelse ($busSales as $sale)
        @php
            $counter++;
            $grandTotalSoldSeats += $sale->sold_seats;
            $grandTotalAmount += $sale->amount;
        @endphp

        <tr>
            <td class="text-center">{{ $counter }}</td>
            <td class="text-center">{{ $sale->registration_number ?? 'N/A' }}</td>
            <td class="text-center">{{ $sale->coach_number ?? 'N/A' }}</td>
            <td class="text-left">{{ $sale->full_route_name ?? 'N/A' }}</td>
            <td class="text-center">{{ $sale->sold_seats }}</td>
            <td class="text-center">{{ number_format($sale->amount, 0) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-danger">No sales data found for the selected criteria.</td>
        </tr>
    @endforelse
</tbody>

                            <tfoot>
                                <tr style="background:#f6f6f6;">
                                    <th style="text-align:center;">{{ $counter }}</th>
                                    <th style="text-align:right;" colspan="3">Total :</th>
                                    <th style="text-align:center;">{{ $grandTotalSoldSeats }}</th>
                                    <th style="text-align:center;">{{ number_format($grandTotalAmount, 0) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 🚨 FIX 3: Combine datepicker initialization and use consistent class --}}
<script>
    $(document).ready(function () {
        // Apply datepicker to the single 'date' input
        $('#date').datepicker({
            format: 'dd-mm-yyyy', 
            autoclose: true,
            todayHighlight: true
        });
        
        // Initialize Bootstrap Select for the dropdown
        $('.selectpicker').selectpicker();
    });
</script>
@endsection