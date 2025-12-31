@extends('layouts.master')


@section('content')
    <div class="d-flex flex-column-fluid">
        <div class=" container-fluid ">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Overall Route Sales Summary</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card border bg-success-o-20 mb-10">
                                <div class="card-body py-0">
                                    {{-- Filter Form (POST method is assumed for Laravel, but GET is used here to match the
                                    action URL) --}}
                                    <form id="filter-form" method="GET"
                                        action="{{ route('admin.reports.route_sales_summary') }}">
                                        @csrf
                                        <div class="row mt-5">
                                            <div class="col-md-4 col-6 px-2">
                                                <div class="form-group">
                                                    <label for="route">Route List</label>
                                                    <select type="text" id="route" autocomplete="off" class="form-control "
                                                        name="route" data-size="10" data-live-search="true">
                                                        <option value="">All Route...</option>
                                                        {{-- Loop through all available routes --}}
                                                        @foreach ($routes as $route)
                                                            <option value="{{ $route->id }}" {{ (request('route') == $route->id) ? 'selected' : '' }}>
                                                                {{ $route->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
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
                                            <div class="col-md-2 col-6 px-2">
                                                <button type="submit"
                                                    class="btn btn-pill btn-block btn-primary px-5 mt-8">Filter</button>
                                            </div>
                                            <div class="col-md-2 col-6 px-2 text-right">
                                                <button type="button" id="print" onclick="PrintSalesReport();"
                                                    class="btn btn-pill btn-info px-5 mt-8">Print</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DYNAMIC REPORT TABLE AREA --}}
                    <div id="sales-report-table">
                        <div style="width:100%; float:left; margin-bottom:15px;">
                            <div style="width:70%; float:left;">
                                <h3 class="box-title" style="margin:0px;">
                                    [{{ $allRoutesName ?? 'All Route' }}] Counter Wise Sales Summary Report
                                </h3>
                            </div>
                            <div style="width:30%; float:right; text-align:right; font-size:12px;">
                                From: <strong>{{ $fromDate ?? 'N/A' }}</strong>, To: <strong>{{ $toDate ?? 'N/A' }}</strong>
                            </div>
                        </div>

                        <div style="width:100%; float: left;">
                            <div class="table-responsive-lg">
                                <table class="table table-bordered table-striped dataTable" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th width="5%" style="text-align:center;">#</th>
                                            <th width="20%" style="text-align:left;">Counter Name</th>
                                            <th width="15%" style="text-align:center;">Sold Seats</th>
                                            <th width="15%" style="text-align:center;">Amount</th>
                                            <th width="15%" style="text-align:center;">Commission</th>
                                            <th width="15%" style="text-align:center;">Discount</th>
                                            <th width="15%" style="text-align:center;">Net Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Initializing Totals --}}
                                        @php
                                            $totalSeats = 0;
                                            $totalAmount = 0;
                                            $totalCommission = 0;
                                            $totalDiscount = 0;
                                            $totalNetAmount = 0;
                                        @endphp

                                        @forelse ($counterSales as $key => $sale)
                                            {{-- Update running totals --}}
                                            @php
                                                $totalSeats += $sale->sold_seats;
                                                $totalAmount += $sale->amount;
                                                $totalCommission += $sale->commission;
                                                $totalDiscount += $sale->discount;
                                                $totalNetAmount += $sale->net_amount; // Calculated in controller
                                            @endphp
                                            <tr>
                                                <td style="text-align:center;">{{ $key + 1 }}</td>
                                                <td style="text-align:left;">
                                                    @php
                                                        $counterType = $sale->counter_type ?? 'Own';
                                                        $isCommission = ($counterType === 'Commission');

                                                        // Font Awesome icon
                                                        $iconClass = $isCommission ? 'copyright' : 'registered';

                                                        // Text color (Bootstrap classes)
                                                        $textColor = $isCommission ? 'text-danger' : 'text-success';
                                                        $titleText = $isCommission ? 'Commission Counter' : 'Own Counter';
                                                    @endphp

                                                    {{-- Icon --}}
                                                    <i title="{{ $titleText }}"
                                                        class="far fa-{{ $iconClass }} fa-1x {{ $textColor }}"></i>

                                                    {{-- Counter Name --}}
                                                    {{ $sale->counter_name ?? 'N/A Counter' }}
                                                </td>

                                                <td style="text-align:center;">{{ $sale->sold_seats }}</td>
                                                <td style="text-align:center;">{{ number_format($sale->amount) }}</td>
                                                <td style="text-align:center;">{{ number_format($sale->commission) }}</td>
                                                <td style="text-align:center;">{{ number_format($sale->discount) }}</td>
                                                <td style="text-align:center;">{{ number_format($sale->net_amount) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No sales data found for the selected filter
                                                    criteria.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr style="background:#f6f6f6;">
                                            <th style="text-align:center;">{{ $counterSales->count() }}</th>
                                            <th style="text-align:right;">Total :</th>
                                            <th style="text-align:center;">{{ $totalSeats }}</th>
                                            <th style="text-align:center;">{{ number_format($totalAmount) }}</th>
                                            <th style="text-align:center;">{{ number_format($totalCommission) }}</th>
                                            <th style="text-align:center;">{{ number_format($totalDiscount) }}</th>
                                            <th style="text-align:center;">{{ number_format($totalNetAmount) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal for Trip Sheet (Included to complete the structure) --}}
    <div class="modal fade" id="tripSheetModal" tabindex="-1" role="dialog" aria-labelledby="tripSheetModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div id="tripSheetModalContent" class="modal-content">
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
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