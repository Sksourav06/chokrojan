@extends('layouts.master')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                    <div class="col-md-12 px-2">
                        <form name="datefilter" id="datefilter" method="GET" action="">
                            @csrf
                            <div class="row mt-4">
                                <div class="col-md-9 col-6 px-2">
                                    <h4 class="text-success font-weight-bold my-3 mr-5">
                                        Dashboard
                                    </h4>
                                </div>
                                <div class="col-md-3 col-6 px-2" title="Select Date" data-toggle="tooltip"
                                    data-placement="top">
                                    <div class="input-group" style="flex-wrap: inherit;">
                                        <div class="input-group-prepend pb-4">
                                            <button type="button" class="btn btn-block btn-outline-primary px-4"
                                                onclick="moveToPreviousDateDashboard()">
                                                <span class="fa fa-angle-double-left fa-lg"></span>
                                            </button>
                                        </div>

                                        <input type="hidden" name="date_prev_next" id="date_prev_next" value="">

                                        <div class="form-group mb-0">
                                            <input type="text" id="filter_date" class="form-control rounded-0 text-center"
                                                name="filter_date" value="07-11-2025" required readonly>
                                        </div>

                                        <div class="input-group-append pb-4">
                                            <button type="button" class="btn btn-block btn-outline-primary px-4"
                                                onclick="moveToNextDateDashboard()">
                                                <span class="fa fa-angle-double-right fa-lg"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">

                <div class="card card-custom">
                    <div class="card-header">
                        <h3 class="card-title">
                            Daily Overall View [&nbsp;<span
                                class="text-warning">{{ $today->format('D, j M Y') }}</span>&nbsp;]
                        </h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 px-6">
                                <div class="row">
                                    <div class="col-xl-2 col-md-4 col-sm-6 px-2">
                                        <div class="card card-custom bg-green mb-3">
                                            <div class="card-body p-3 text-white">
                                                <span class="font-size-h1 font-weight-bold">{{ $activeStations }}</span>
                                                <i class="fas fa-map-marker-alt fa-3x text-white-50 float-right"></i>
                                                <span class="font-size-h5 text-light mt-4 d-block">Active Stations</span>
                                            </div>
                                        </div>
                                        <div class="card card-custom bg-light-green mb-3">
                                            <div class="card-body p-3 text-white">
                                                <span class="font-size-h1 font-weight-bold">43</span>
                                                <i class="fas fa-map-marker-alt fa-3x text-white-50 float-right"></i>
                                                <span class="font-size-h5 text-light mt-4 d-block">Running Stations</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-2 col-md-4 col-sm-6 px-2">
                                        <div class="card card-custom bg-yellow mb-3">
                                            <div class="card-body p-3 text-white">
                                                <span class="font-size-h1 font-weight-bold">{{ $activeCounters }}</span>
                                                <i class="fas fa-store-alt fa-3x text-white-50 float-right"></i>
                                                <span class="font-size-h5 text-light mt-4 d-block">Active Counters</span>
                                            </div>
                                        </div>
                                        <div class="card card-custom bg-light-yellow mb-3">
                                            <div class="card-body p-3 text-white">
                                                <span class="font-size-h1 font-weight-bold">{{ $runningCounters }}</span>
                                                <i class="fas fa-store-alt fa-3x text-white-50 float-right"></i>
                                                <span class="font-size-h5 text-light mt-4 d-block">Running Counters</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-2 col-md-4 col-sm-6 px-2">
                                        <div class="card card-custom bg-orange mb-3">
                                            <div class="card-body p-3 text-white">
                                                <span class="font-size-h1 font-weight-bold">{{ $activeRoutes }}</span>
                                                <i class="fas fa-route fa-3x text-white-50 float-right"></i>
                                                <span class="font-size-h5 text-light mt-4 d-block">Active Routes</span>
                                            </div>
                                        </div>
                                        <div class="card card-custom bg-light-orange mb-3">
                                            <div class="card-body p-3 text-white">
                                                <span class="font-size-h1 font-weight-bold">25</span>
                                                <i class="fas fa-route fa-3x text-white-50 float-right"></i>
                                                <span class="font-size-h5 text-light mt-4 d-block">Running Routes</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-2 col-md-4 col-sm-6 px-2">
                                        <div class="card card-custom bg-red mb-3">
                                            <div class="card-body p-3 text-white">
                                                <span class="font-size-h1 font-weight-bold">{{ $dailySchedules }}</span>
                                                <i class="fas fa-list-ol fa-3x text-white-50 float-right"></i>
                                                <span class="font-size-h5 text-light mt-4 d-block">Daily Schedules</span>
                                            </div>
                                        </div>
                                        <div class="card card-custom bg-light-red mb-3">
                                            <div class="card-body p-3 text-white">
                                                <span class="font-size-h1 font-weight-bold">{{ $runningSchedules }}</span>
                                                <i class="fas fa-list-ol fa-3x text-white-50 float-right"></i>
                                                <span class="font-size-h5 text-light mt-4 d-block">Running Schedules</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-2 col-md-4 col-sm-6 px-2">
                                        <div class="card card-custom bg-purple mb-3">
                                            <div class="card-body p-3 text-white">
                                                <span class="font-size-h1 font-weight-bold">{{ $dailyTrips }}</span>
                                                <i class="fas fa-shipping-fast fa-3x text-white-50 float-right"></i>
                                                <span class="font-size-h5 text-light mt-4 d-block">Daily Trips</span>
                                            </div>
                                        </div>
                                        <div class="card card-custom bg-light-purple mb-3">
                                            <div class="card-body p-3 text-white">
                                                <span class="font-size-h1 font-weight-bold">206</span>
                                                <i class="fas fa-shipping-fast fa-3x text-white-50 float-right"></i>
                                                <span class="font-size-h5 text-light mt-4 d-block">Running Trips</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-2 col-md-4 col-sm-6 px-2">
                                        <div class="card card-custom bg-violet mb-3">
                                            <div class="card-body p-3 text-white">
                                                <span class="font-size-h1 font-weight-bold">43</span>
                                                <i class="fas fa-bus fa-3x text-white-50 float-right"></i>
                                                <span class="font-size-h5 text-light mt-4 d-block">Active Buses</span>
                                            </div>
                                        </div>
                                        <div class="card card-custom bg-light-violet mb-3">
                                            <div class="card-body p-3 text-white">
                                                <span class="font-size-h1 font-weight-bold">1</span>
                                                <i class="fas fa-bus fa-3x text-white-50 float-right"></i>
                                                <span class="font-size-h5 text-light mt-4 d-block">Running Buses</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-6">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <h4 class="text-primary mb-4">
                                            Monthly Sales [ <span
                                                class="text-warning">{{ $currentMonth->format('F Y') }}</span> ]
                                        </h4>
                                        <div id="monthly_sales_chart" style="min-height: 255px;">
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <h4 class="text-primary mb-4">
                                            Daily Sales [ <span class="text-warning">{{ $today->format('D, j M Y') }}</span>
                                            ]
                                        </h4>
                                        <div id="daily_sales" class="pt-2">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr class="bg-light">
                                                        <th width="50%">Sales Category</th>
                                                        <th width="20%" class="text-center">Seats</th>
                                                        <th width="30%" class="text-center">Amount (Tk)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-success font-weight-bold py-6">Ticket Issued</td>
                                                        <td class="text-center py-5">
                                                            <span id="issued_seats"
                                                                class="label label-inline label-success label-lg">401</span>
                                                        </td>
                                                        <td class="text-center py-5">
                                                            <span id="issued_amount"
                                                                class="label label-inline label-success label-lg">2,97,570</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-warning font-weight-bold py-6">Ticket Booked</td>
                                                        <td class="text-center py-5">
                                                            <span id="booked_seats"
                                                                class="label label-inline label-warning label-lg">110</span>
                                                        </td>
                                                        <td class="text-center py-5">
                                                            <span id="booked_amount"
                                                                class="label label-inline label-warning label-lg">81,130</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-danger font-weight-bold py-6">Ticket Canceled</td>
                                                        <td class="text-center py-5">
                                                            <span id="cancelled_seats"
                                                                class="label label-inline label-danger label-lg">15</span>
                                                        </td>
                                                        <td class="text-center py-5">
                                                            <span id="cancelled_amount"
                                                                class="label label-inline label-danger label-lg">11,180</span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        function moveToPreviousDateDashboard() {
            document.getElementById('date_prev_next').value = 'prev';
            document.getElementById('datefilter').submit();
        }

        function moveToNextDateDashboard() {
            document.getElementById('date_prev_next').value = 'next';
            document.getElementById('datefilter').submit();
        }

        var filterDate = '2025-11-06';
        var filterDateSalesData = [];
        // ... Any other ApexChart JS initialization goes here ...
    </script>
@endsection