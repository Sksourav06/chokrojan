@extends('layouts.master')

@section('title', 'Overall Trip Sheet Report')

@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content" bis_skin_checked="1">

        {{-- Page Header / Subheader --}}
        <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;" bis_skin_checked="1">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap"
                bis_skin_checked="1">
                <div class="d-flex align-items-center flex-wrap container-fluid px-0" bis_skin_checked="1">
                    <h4 class="text-success font-weight-bold my-2 mr-5">
                        Trip Sheet Report
                        <small></small>
                    </h4>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column-fluid" bis_skin_checked="1">
            <div class=" container-fluid " bis_skin_checked="1">
                <div class="card card-custom" bis_skin_checked="1">
                    <div class="card-header" bis_skin_checked="1">
                        <h3 class="card-title">
                            Overall Trip Sheet Report
                        </h3>
                    </div>
                    <div class="card-body" bis_skin_checked="1">
                        <div class="row" bis_skin_checked="1">
                            <div class="col-md-12" bis_skin_checked="1">
                                <div class="card border bg-success-o-20 mb-10" bis_skin_checked="1">
                                    <div class="card-body py-0" bis_skin_checked="1">
                                        {{-- Filter Form --}}
                                        <form id="filter-form" method="GET"
                                            action="{{ route('admin.reports.trip_sheet_report') }}">
                                            <div class="row mt-5" bis_skin_checked="1">
                                                <div class="col-md-2" bis_skin_checked="1"></div>

                                                {{-- Route List Filter --}}
                                                <div class="col-md-4 col-6 px-2" bis_skin_checked="1">
                                                    <div class="form-group " bis_skin_checked="1">
                                                        <label for="route" class="">Route List</label>
                                                        <select type="text" id="route" autocomplete="off"
                                                            class="form-control " name="route" data-size="10"
                                                            data-live-search="true">
                                                            <option value="">All Route...</option>
                                                            {{-- Assuming $routes is passed from the controller --}}
                                                            @foreach ($routes as $route)
                                                                                                                <option value="{{ $route->id }}" {{ request('route') == $route->id
                                                                ? 'selected' : '' }}>
                                                                                                                    {{ $route->name }}
                                                                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Date Filter --}}
                                                <div class="col-md-2 col-6 px-2" bis_skin_checked="1">
                                                    <div class="form-group">
                                                        <label for="date" class="required">Date</label>
                                                        <input type="text" id="date" class="form-control" name="date"
                                                            value="{{ request('date', date('d-m-Y')) }}" required>
                                                    </div>
                                                </div>

                                                {{-- Filter Button --}}
                                                <div class="col-md-2 col-6 px-2" bis_skin_checked="1">
                                                    <button type="submit"
                                                        class="btn btn-pill btn-block btn-primary px-5 mt-8">Filter</button>
                                                </div>

                                                {{-- Print Button --}}
                                                <div class="col-md-2 col-6 px-2 text-right" bis_skin_checked="1">
                                                    <button type="button" id="print" onclick="PrintSalesReport();"
                                                        class="btn btn-pill btn-info px-5 mt-8">Print</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Report Detail Table --}}
                        <div id="sales-report-table" bis_skin_checked="1">
                            <div style="width:100%; float:left; margin-bottom:15px;" bis_skin_checked="1">
                                <div style="width:50%; float:left;" bis_skin_checked="1">
                                    <h3 class="box-title" style="margin:0px;">
                                        [{{ request('route') ? 'Selected Route' : 'All Route' }}] Schedule Wise Trip Sheet
                                        Report
                                    </h3>
                                </div>
                                <div style="width:50%; float:right; text-align:right; font-size:12px;" bis_skin_checked="1">
                                    Date: <strong>{{ request('date', date('d-m-Y')) }}</strong>
                                </div>
                            </div>

                            <div style="width:100%; float: left;" bis_skin_checked="1">
                                <div class="table-responsive-lg" bis_skin_checked="1">
                                    <table class="table table-bordered table-striped dataTable" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th width="5%" style="text-align:center;">#</th>
                                                <th width="15%" style="text-align:left;">Coach Info</th>
                                                <th width="10%" style="text-align:left;">Seat Plan</th>
                                                <th width="25%" style="text-align:left;">Route Info</th>
                                                <th width="20%" style="text-align:left;">From ⟹ To</th>
                                                <th width="10%" style="text-align:center;">Seat Status</th>
                                                <th width="6%" style="text-align:center;">Status</th>
                                                <th width="9%" style="text-align:center;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Assuming $schedules is the main data array/collection passed by the
                                            controller --}}
                                            @php
                                                $totalSold = 0;
                                                $totalAvailable = 0;
                                                $totalSeats = 0;
                                            @endphp

                                            @forelse ($schedules as $index => $schedule)
                                                @php
                                                    // Dummy calculation based on your provided data structure
                                                    $sold = rand(0, 36);
                                                    $booked = rand(0, 5);
                                                    $total = 36; // Example total seats
                                                    $available = $total - $sold - $booked;

                                                    $totalSold += $sold;
                                                    $totalAvailable += $available;
                                                    $totalSeats += $total;
                                                @endphp

                                                <tr class="{{ $schedule->status == 'cancelled' ? 'bg-inactive' : '' }}">
                                                    <td style="text-align:center;">{{ $index + 1 }}</td>
                                                    <td style="text-align:left;">
                                                        {{-- Coach Name --}}
                                                        <span class="label label-inline label-light-danger label-lg"
                                                            id="coach-{{ $schedule->id }}">
                                                            {{ $schedule->name }}
                                                        </span><br>

                                                        {{-- Bus Type --}}
                                                        <span class="label label-inline label-light-warning">
                                                            {{ $schedule->bus_type }}
                                                        </span>
                                                    </td>
                                                    <td style="text-align:left;">
                                                        <br>
                                                    </td>
                                                    <td style="text-align:left;">
                                                        {{ $schedule->route_name }}
                                                    </td>
                                                    <td style="text-align:left;">
                                                        <i
                                                            class="fas fa-map-marker-alt pr-2 text-success"></i>{{ $schedule->start_station }}
                                                        <i
                                                            class="far fa-clock px-2 text-success"></i>{{ $schedule->start_time }}<br>
                                                        <i
                                                            class="fas fa-map-marker-alt pr-2 text-danger"></i>{{ $schedule->end_station }}
                                                        <i
                                                            class="far fa-clock px-2 text-danger"></i>{{ $schedule->arrival_time }}
                                                    </td>

                                                    <td style="text-align:center;">
                                                        {{ $sold }} / {{ $total }}
                                                    </td>
                                                    <td class="text-capitalize" style="text-align:center;">
                                                        {{ $schedule->status }}
                                                    </td>
                                                    <td style="text-align:center;">
                                                        {{-- Trip Sheet Button --}}
                                                        <button type="button"
                                                            class="btn btn-sm btn-pill btn-outline-success px-3"
                                                            onclick="showTripSheet({{ $schedule->id }});">Trip Sheet</button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-danger">No trip schedules found for
                                                        the selected date/route.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr style="background:#d2d3db;">
                                                <th style="text-align:right;" colspan="5">Total:</th>
                                                <th style="text-align:left;" colspan="3">
                                                    <span class="text-success" title="Total Sold / Total Seats"
                                                        data-toggle="tooltip" data-placement="top">({{ $totalSold }} /
                                                        {{ $totalSeats }}) <i
                                                            class="fa fa-bus text-success">{{ count($schedules) }}</i></span>
                                                    {{-- Add other required summaries here --}}
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal for detailed Trip Sheet (Example structure maintained) --}}
                <div class="modal fade" id="tripSheetModal" tabindex="-1" role="dialog"
                    aria-labelledby="tripSheetModalLabel" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div id="tripSheetModalContent" class="modal-content">
                            {{-- Detailed Trip Sheet Content will be loaded here via AJAX when showTripSheet() is called
                            --}}
                        </div>
                    </div>
                </div>
                <link rel="stylesheet"
                    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
                <script
                    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>

                {{-- Javascript helper functions (You should keep these in a separate .js file, but included here for
                completeness) --}}
                <script type="text/javascript">
                    // Function to show the detailed trip sheet modal (requires backend implementation)
                    window.showTripSheet = function (tripId) {
                        $('#tripSheetModalContent').html("<div class='text-center p-5'>Loading Trip Sheet...</div>");
                        $('#tripSheetModal').modal('show');

                        // AJAX call to load detailed trip sheet
                        $.ajax({
                            url: "/admin/ticket-issue/trip-sheet/" + tripId,
                            type: "GET",
                            success: function (res) {
                                if (res.status) {
                                    $('#tripSheetModalContent').html(res.html);
                                } else {
                                    $('#tripSheetModalContent').html("<div class='alert alert-danger'>Error loading trip sheet details.</div>");
                                }
                            },
                            error: function (xhr) {
                                $('#tripSheetModalContent').html("<div class='alert alert-danger'>Network error loading trip sheet.</div>");
                            }
                        });
                    };

                    // Print functions (as defined in your original HTML)
                    function PrintSalesReport() {
                        // Logic for printing overall report (you need to define this)
                        alert("Printing Overall Sales Report...");
                    }

                    $(document).ready(function () {
                        $('#date').datepicker({
                            format: 'dd-mm-yyyy', // format same as backend expects
                            autoclose: true,
                            todayHighlight: true
                        });
                    });
                    $(document).ready(function () {
                        $('.selectpicker').selectpicker();
                    });
                </script>
            </div>
        </div>
    </div>
@endsection