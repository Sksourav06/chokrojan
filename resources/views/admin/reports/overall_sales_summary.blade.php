@extends('layouts.master')


@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                <h4 class="text-success font-weight-bold my-2 mr-5">
                    Counter Sales Summary
                    <small></small>
                </h4>
            </div>
        </div>
    </div>

    {{-- Script functions remain outside the main content block for clarity --}}

    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">
                        Overall Counter Sales Summary
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card border bg-success-o-20 mb-10">
                                <div class="card-body py-0">
                                    {{-- 🚨 ACTION: Route name assumed to be 'admin.reports.overall-sales-summary' --}}
                                    <form id="filter-form" method="GET" action="{{ route('admin.reports.overall-sales-summary') }}">
                                        @csrf {{-- CSRF token for security --}}
                                        <div class="row mt-5">
                                            
                                            {{-- 1. Counter List Filter (Assuming $allCounters is passed to the view) --}}
                                            <div class="col-md-3 col-6 px-2">
                                                <div class="form-group">
                                                    <label for="counter_id" class="">Counter List</label>
                                                    {{-- 🚨 FIX: Load Counter list dynamically --}}
                                                    <select id="counter_id" class="form-control " name="counter_id" data-size="10" data-live-search="true">
                                                        <option value="">All Counter...</option>
                                                        @foreach($allCounters ?? [] as $counter)
                                                            <option value="{{ $counter->id }}" 
                                                                {{ ($selectedCounterId ?? '') == $counter->id ? 'selected' : '' }}>
                                                                {{ $counter->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            {{-- 2. From Date Filter --}}
                                           <div class="col-md-2 col-3 px-2">
                                                <div class="form-group">
                                                    <label for="from_date" class="required">
                                                        <i class="far fa-star text-danger fa-sm" title="Required"
                                                            data-toggle="tooltip" data-placement="top"></i> From Date
                                                    </label>
                                                    <input type="text" id="from_date" class="form-control rounded-0"
                                                        placeholder="Enter from date" name="from_date"
                                                        value="{{ $fromDate ?? date('d-m-Y') }}" required=""
                                                        readonly="readonly">
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-3 px-2">
                                                <div class="form-group">
                                                    <label for="to_date" class="required">
                                                        <i class="far fa-star text-danger fa-sm" title="Required"
                                                            data-toggle="tooltip" data-placement="top"></i> To Date
                                                    </label>
                                                    <input type="text" id="to_date" class="form-control rounded-0"
                                                        placeholder="Enter to date" name="to_date"
                                                        value="{{ $toDate ?? date('d-m-Y') }}" required=""
                                                        readonly="readonly">
                                                </div>
                                            </div>
                                            
                                            {{-- 4. Filter Button --}}
                                            <div class="col-md-2 col-6 px-2">
                                                <button type="submit" class="btn btn-pill btn-block btn-primary px-5 mt-8">Filter</button>
                                            </div>
                                            
                                            {{-- 5. Print Button --}}
                                            <div class="col-md-2 col-6 px-2 text-right">
                                                <button type="button" id="print" onclick="PrintSalesReport();" class="btn btn-pill btn-info px-5 mt-8">Print</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
<!-- $allRoutesName ?? -->
                    <div id="sales-report-table">
                        <div style="width:100%; float:left; margin-bottom:15px;">
                            <div style="width:70%; float:left;">
                                <h3 class="box-title" style="margin:0px;">
                                    [{{  'All Counter' }}] Sales Summary Report 
                                </h3>
                            </div>
                            <div style="width:30%; float:right; text-align:right; font-size:12px;">
                                From: <strong>{{ $fromDate ?? 'N/A' }}</strong>, To: <strong>{{ $toDate ?? 'N/A' }}</strong>
                            </div>
                        </div>

                        {{-- 🚨 FIX 1: Initialize Global Grand Totals (used in the final table) --}}
                        @php
                            $globalGrandTotalSeats = 0;
                            $globalTotalAmount = 0;
                            $globalTotalDiscount = 0;
                            $globalTotalCommission = 0; // Total Commission (Counter + Callerman)
                            $globalTotalNet = 0;
                        @endphp
                        
                        {{-- Grouping starts here (Outer loop over unique counters) --}}
                        @foreach($counterSales->groupBy('issue_counter_id') as $counterId => $salesGroup)
                            
                            @php
                                // Initialize local totals for the current counter's group
                                $counterTotalSeats = 0;
                                $counterTotalAmount = 0;
                                $counterTotalCommission = 0;
                                $counterTotalDiscount = 0;
                                $counterTotalNet = 0;
                                
                                $firstSale = $salesGroup->first();
                                $counterName = $firstSale->counter_name ?? 'N/A Counter';
                                
                                // Dynamic Counter Type Icon Logic
                                $counterType = $firstSale->counter_type ?? 'Own'; 
                                $isCommission = ($counterType === 'Commission');
                                $iconClass = $isCommission ? 'copyright' : 'registered';
                                $textColor = $isCommission ? 'text-danger' : 'text-success';
                                 $titleText = $isCommission ? 'Commission Counter' : 'Own Counter';
                            @endphp
                         
                            {{-- Counter Header H4 Block --}}
                            <div style="width:100%; float: left;">
                                <h4>
                                    <i title="{{ $titleText }}" class="far fa-{{ $iconClass }} fa-1x {{ $textColor }}"></i>
                                    {{ $counterName }}
                                </h4>
                            </div>

                            {{-- Bus Wise Table --}}
                            <div class="table-responsive-lg">
                                <table class="table table-bordered dataTable" style="width:100%;">
                                    <thead>
                                        <tr style="background:#f6f6f6;">
                                            <th width="5%" style="text-align:center;">#</th>
                                            <th width="20%" style="text-align:left;">Bus Number</th>
                                            <th width="15%" style="text-align:center;">Sold Seats</th>
                                            <th width="15%" style="text-align:center;">Amount</th>
                                            <th width="15%" style="text-align:center;">Commission</th>
                                            <th width="15%" style="text-align:center;">Discount</th>
                                            <th width="15%" style="text-align:center;">Net Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($salesGroup as $key => $sale)
                                            @php
                                                // FIX 2: Correct Commission Aggregation from Controller
                                                $commTotal = ($sale->counter_commission_total ?? 0) + ($sale->callerman_commission_total ?? 0);
                                                $netAmount = ($sale->amount ?? 0) - ($sale->discount ?? 0) - $commTotal;

                                                // Update local counter totals
                                                $counterTotalSeats += $sale->sold_seats;
                                                $counterTotalAmount += $sale->amount ?? 0;
                                                $counterTotalDiscount += $sale->discount ?? 0;
                                                $counterTotalCommission += $commTotal;
                                                $counterTotalNet += $netAmount;
                                            @endphp
                                            <tr>
                                                <td style="text-align:center;">{{ $key + 1 }}</td>
                                                <td style="text-align:left;">{{ $sale->registration_number }}</td>
                                                <td style="text-align:center;">{{ $sale->sold_seats }}</td>
                                                <td style="text-align:center;">{{ number_format($sale->amount ?? 0, 0) }}</td>
                                                <td style="text-align:center;">{{ number_format($commTotal, 0) }}</td>
                                                <td style="text-align:center;">{{ number_format($sale->discount ?? 0, 0) }}</td>
                                                <td style="text-align:center;">{{ number_format($netAmount, 0) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        {{-- Row showing the total for the CURRENT COUNTER --}}
                                        <tr style="background:#f6f6f6;">
                                            <th style="text-align:center;" colspan="2">Total ({{ $counterName }}) :</th>
                                            <th style="text-align:center;">{{ $counterTotalSeats }}</th>
                                            <th style="text-align:center;">{{ number_format($counterTotalAmount, 0) }}</th>
                                            <th style="text-align:center;">{{ number_format($counterTotalCommission, 0) }}</th>
                                            <th style="text-align:center;">{{ number_format($counterTotalDiscount, 0) }}</th>
                                            <th style="text-align:center;">{{ number_format($counterTotalNet, 0) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            {{-- 🚨 FIX 3: Update GLOBAL Grand Totals here (used in the final table) --}}
                            @php
                                $globalGrandTotalSeats += $counterTotalSeats;
                                $globalTotalAmount += $counterTotalAmount;
                                $globalTotalDiscount += $counterTotalDiscount;
                                $globalTotalCommission += $counterTotalCommission;
                                $globalTotalNet += $counterTotalNet;
                            @endphp
                            
                        @endforeach
                        {{-- End of Counter Grouping Loop --}}


                        {{-- 🚨 FIX 4: Final Grand Total Table (Overall Summary Table) --}}
                        <div class="table-responsive-lg">
                            <table class="table table-bordered dataTable" style="width:100%; margin-top: 20px;">
                                <tfoot>
                                    <tr style="background:#cccccc; font-size: 16px;">
                                        <th width="25%" style="text-align:left; padding: 10px;">Grand Total :</th>
                                        <th width="15%" style="text-align:center; padding: 10px;">{{ $globalGrandTotalSeats }}</th>
                                        <th width="15%" style="text-align:center; padding: 10px;">{{ number_format($globalTotalAmount, 0) }}</th>
                                        <th width="15%" style="text-align:center; padding: 10px;">{{ number_format($globalTotalDiscount, 0) }}</th>
                                        <th width="15%" style="text-align:center; padding: 10px;">{{ number_format($globalTotalCommission, 0) }}</th>
                                        <th width="15%" style="text-align:center; padding: 10px;"></th>
                                        <th width="15%" style="text-align:center; padding: 10px;">{{ number_format($globalTotalNet, 0) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- </div>
                </div> {{-- card-body ends --}}
            </div>
        </div>
    </div>
</div> -->

<script>
        $(document).ready(function () {
            $('#from_date').datepicker({
                format: 'dd-mm-yyyy', // format same as backend expects
                autoclose: true,
                todayHighlight: true
            });
        });
        $(document).ready(function () {
            $('#to_date').datepicker({
                format: 'dd-mm-yyyy', // format same as backend expects
                autoclose: true,
                todayHighlight: true
            });
        });
        $(document).ready(function () {
            $('.selectpicker').selectpicker();
        });
    </script>
@endsection