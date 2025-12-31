<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use Illuminate\Http\Request;
use App\Models\TicketIssue;
use App\Models\Counter;
use App\Models\Route;
use App\Models\User;
use App\Models\Schedule;
use App\Models\SeatSold;
use App\Models\TicketCancellation;
use App\Models\TicketIssueSeat;
use Carbon\Carbon; // ✅ FIX: Imported Carbon class
use Illuminate\Support\Facades\DB;
class ReportController extends Controller
{
    // Fetches sales data based on filters
    public function salesReport(Request $request)
    {
        // Filters
        $filterType = $request->input('filter_by', 'issue_date');
        $fromDate = $request->input('from_date', now()->format('Y-m-d'));  // Default from today's date
        $toDate = $request->input('to_date', now()->format('Y-m-d'));  // Default to today's date
        $counterId = $request->input('counter_id');
        $masterId = $request->input('master_id');

        // Base Query
        $tickets = TicketIssue::with([
            'schedule',
            'issuedBy',
            'boardingCounter',
            'fromStation',
            'toStation',
            'seats',   // seat_solds relation
        ])
            ->where('status_label', 'Sold')  // ✅ Correct status column
            ->whereDate('issue_date', '>=', $fromDate)
            ->whereDate('issue_date', '<=', $toDate);

        // Filter by counter
        if ($counterId) {
            $tickets->where('boarding_counter_id', $counterId);
        }

        // Filter by issued master
        if ($masterId) {
            $tickets->where('issued_by', $masterId);
        }

        // Fetch results
        $tickets = $tickets->orderBy('issue_date', 'DESC')->get();

        // Counter list
        $counters = Counter::all();

        // Master list (issuedBy users only)
        $masters = User::whereIn('id', $tickets->pluck('issued_by'))->get();

        // === TOTAL CALCULATION ===
        $totalSeats = $tickets->sum('seats_count');   // ✅ Seat count from ticket_issues
        $totalFare = $tickets->sum('grand_total');
        $totalDiscount = $tickets->sum('discount_amount');
        $totalCommission = $tickets->sum('callerman_commission');

        // Calculate the net total with a fallback for null values
        $netTotal = $totalFare - $totalDiscount - $totalCommission;

        // === Optionally: Calculate the net fare per ticket ===
        // You might want to calculate the net fare per ticket based on individual fare - discount + commission
        $tickets->each(function ($ticket) {
            $ticket->net_fare = max($ticket->grand_total - $ticket->discount_amount + $ticket->callerman_commission, 0);
        });

        return view('admin.reports.sales_report', compact(
            'tickets',
            'counters',
            'masters',
            'fromDate',
            'toDate',
            'counterId',
            'masterId',
            'totalSeats',
            'totalFare',
            'totalDiscount',
            'totalCommission',
            'netTotal',
            'filterType',
        ));
    }




    public function cancelReport(Request $request)
    {
        $filterType = $request->input('filter_by', 'journey_date');
        $fromDate = $request->input('from_date', now()->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));
        $counterId = $request->input('counter_id');
        $masterId = $request->input('master_id');

        $query = TicketCancellation::with([
            'ticket.schedule.startStation',
            'ticket.schedule.endStation',
            'ticket.schedule.bus',
            'cancelledByUser',
            'ticket.seats',
        ]);

        // Filter by Journey Date or Issue Date
        if ($filterType === 'journey_date') {
            $query->whereHas('ticket.schedule', function ($q) use ($fromDate, $toDate) {
                $q->whereDate('start_time', '>=', $fromDate)
                    ->whereDate('start_time', '<=', $toDate);
            });
        } else {
            $query->whereHas('ticket', function ($q) use ($fromDate, $toDate) {
                $q->whereDate('issue_date', '>=', $fromDate)
                    ->whereDate('issue_date', '<=', $toDate);
            });
        }

        if ($counterId) {
            $query->whereHas('ticket', function ($q) use ($counterId) {
                $q->where('boarding_counter_id', $counterId);
            });
        }

        if ($masterId) {
            $query->whereHas('ticket', function ($q) use ($masterId) {
                $q->where('issued_by', $masterId);
            });
        }

        $cancelledTickets = $query->orderBy('cancelled_at', 'DESC')->paginate(50);

        $counters = \App\Models\Counter::all();
        $masterIds = TicketIssue::select('issued_by')->distinct()->pluck('issued_by');
        $masters = \App\Models\User::whereIn('id', $masterIds)->select('id', 'name')->get();

        return view('admin.reports.cancel_report', compact(
            'cancelledTickets',
            'counters',
            'masters',
            'filterType',
            'fromDate',
            'toDate',
            'counterId',
            'masterId',
        ));
    }
    public function bookingReport(Request $request)
    {
        // Get filter inputs from request
        $filterType = $request->input('filter_by', 'issue_date');
        $fromDate = $request->input('from_date', now()->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));
        $counterId = $request->input('counter_id');
        $masterId = $request->input('master_id');

        // Base query for TicketIssue model
        $query = TicketIssue::with(['issuedBy', 'schedule', 'fromStation', 'toStation'])
            ->where('status_label', 'Booked')  // Filter for sold tickets
            ->whereHas('schedule', function ($query) use ($fromDate, $toDate, $filterType) {
                // Apply the date filter based on issue_date or journey_date
                if ($filterType === 'journey_date') {
                    $query->whereDate('start_time', '>=', $fromDate)
                        ->whereDate('start_time', '<=', $toDate);
                } else {
                    $query->whereDate('issue_date', '>=', $fromDate)
                        ->whereDate('issue_date', '<=', $toDate);
                }
            });

        // Filter by Counter if provided
        if ($counterId) {
            $query->where('boarding_counter_id', $counterId);
        }

        // Filter by Master if provided
        if ($masterId) {
            $query->where('issued_by', $masterId);
        }

        // Get the filtered data
        $tickets = $query->orderBy('issue_date', 'DESC')->get();

        // Prepare data for the report
        $counters = Counter::all();
        $masters = User::whereIn('id', $tickets->pluck('issued_by'))->get();

        // Calculate total values
        $totalSeats = $tickets->sum('seats_count');
        $totalGrand = $tickets->sum('grand_total');
        $totalDiscount = $tickets->sum('discount_amount');
        $totalCommission = $tickets->sum('callerman_commission');
        $netTotal = $totalGrand - $totalDiscount - $totalCommission;

        // Return the view with the necessary data
        return view('admin.reports.booking_report', compact(
            'tickets',
            'counters',
            'masters',
            'filterType',
            'fromDate',
            'toDate',
            'counterId',
            'masterId',
            'totalSeats',
            'totalGrand',
            'totalDiscount',
            'totalCommission',
            'netTotal',
        ));
    }

    public function loyaltyReport(Request $request)
    {
        // 1. বেস ক্যোয়ারী তৈরি: শুধুমাত্র লয়ালটি ডিসকাউন্ট প্রয়োগ করা হয়েছে এমন টিকিটগুলি নির্বাচন করা
        $query = TicketIssue::where('is_loyalty_discount_applied', true)
            ->where('status_label', 'Sold'); // শুধু বিক্রি হওয়া টিকিট

        // 2. ডেট রেঞ্জ ফিল্টার প্রয়োগ (Date Filtering)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            // Carbon ব্যবহার করে ডেট রেঞ্জ ঠিক করা
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();

            // issue_date কলাম ব্যবহার করা হলো
            $query->whereBetween('issue_date', [$startDate, $endDate]);
        }

        // 3. রিপোর্ট সামারি গণনা (Aggregation)
        // ক্যোয়ারীর চূড়ান্ত ডেটা লোড করার আগে সামারি গণনা করা প্রয়োজন
        $totalLoyaltyTickets = (clone $query)->count();
        $totalDiscountGiven = (clone $query)->sum('discount_amount');

        // সামারি ডেটা অ্যারেতে সংরক্ষণ
        $reportSummary = [
            'total_loyalty_tickets' => $totalLoyaltyTickets,
            'total_discount_given' => $totalDiscountGiven,
        ];

        // 4. বিস্তারিত টিকিট ডেটা লোড করা এবং পেজিনেট করা
        $tickets = $query->orderByDesc('issue_date')->paginate(50);

        // ভিউতে ডেটা পাঠানো
        return view('admin.reports.loyalty_report', compact('tickets', 'reportSummary'));
    }

    public function overallTripSheet(Request $request)
    {
        $query = Schedule::with(['route.startStation', 'route.endStation']);

        // Route filter
        if ($request->filled('route')) {
            $query->where('route_id', $request->route);
        }

        // Date filter
        if ($request->filled('date')) {
            $date = Carbon::createFromFormat('d-m-Y', $request->date);
            $query->whereDate('start_time', $date);
        }

        $schedules = $query->get();

        // Map to desired structure
        $schedules = $schedules->map(function ($schedule) {
            return (object) [
                'id' => $schedule->id,
                'name' => $schedule->name ?? 'N/A',
                'bus_type' => $schedule->bus_type ?? 'N/A',
                'seat_layout' => $schedule->seat_layout ?? '',
                'route_name' => $schedule->route->name ?? 'N/A',
                'start_station' => $schedule->startStation->name ?? 'N/A',
                'end_station' => $schedule->endStation->name ?? 'N/A',
                'start_time' => Carbon::parse($schedule->start_time)->format('h:i A'),
                'arrival_time' => Carbon::parse($schedule->end_time)->format('h:i A'),
                'status' => $schedule->status ?? 'Active',
            ];
        });

        return view('admin.reports.trip_sheet_report', compact('schedules'));
    }




    public function SalesSummary(Request $request)
    {
        // 1. Get Filters and Set Defaults

        // 🚨 Validation is set to nullable for initial load
        $request->validate([
            'from_date' => 'nullable|date_format:d-m-Y',
            'to_date' => 'nullable|date_format:d-m-Y|after_or_equal:from_date',
            'route' => 'nullable|integer',
        ]);

        // Set default dates if not provided
        $defaultDate = date('d-m-Y');
        $inputFromDate = $request->from_date ?? $defaultDate;
        $inputToDate = $request->to_date ?? $defaultDate;
        $routeId = $request->route;

        // Convert dates safely for the database query
        $fromDate = Carbon::createFromFormat('d-m-Y', $inputFromDate)->startOfDay();
        $toDate = Carbon::createFromFormat('d-m-Y', $inputToDate)->endOfDay();

        // 2. Fetch Sales Data (Grouped by Counter)
        $salesQuery = TicketIssue::query()
            // ✅ FIX 1: JOIN schedules table to access route_id (schedules.id = ticket_issues.schedule_id)
            ->join('schedules', 'ticket_issues.schedule_id', '=', 'schedules.id')
            ->select(
                // All selected SUM columns must be prefixed for clarity after join
                DB::raw('SUM(ticket_issues.seats_count) as sold_seats'),
                DB::raw('SUM(ticket_issues.grand_total) as amount'),
                DB::raw('SUM(ticket_issues.discount_amount) as discount'),
                DB::raw('SUM(ticket_issues.callerman_commission) as commission'),
                // Selecting the grouping column
                'ticket_issues.issue_counter_id',
            )
            // ✅ FIX 2: Using 'issue_date' for filtering, as this is the confirmed column.
            ->whereBetween('ticket_issues.issue_date', [$fromDate, $toDate])
            ->where('ticket_issues.status_label', 'Sold');

        // Apply Route Filter
        if ($routeId) {
            // ✅ Filtering based on the joined 'schedules' table
            $salesQuery->where('schedules.route_id', $routeId);
        }

        // Group and execute the query
        $salesQuery->groupBy('ticket_issues.issue_counter_id');
        $counterSales = $salesQuery->get();

        // 3. Enhance Data and Prepare for View
        $allRoutes = Route::orderBy('name')->get();

        // Load issueCounter relationship manually (since join returns stdClass)
        $counterSales->load('issueCounter');

        $counterSales = $counterSales->map(function ($sale) {
            $counter = $sale->issueCounter;

            // Set counter name and type safely
            $sale->counter_name = $counter->name ?? 'Online Sales';
            $sale->counter_type = $counter->counter_type;

            // Calculate Net Amount 
            $amount = $sale->amount ?? 0;
            $commission = $sale->commission ?? 0;
            $discount = $sale->discount ?? 0;

            $sale->net_amount = $amount - $commission - $discount;
            return $sale;
        });

        // Determine the selected route name for the report title
        $selectedRouteName = $routeId
            ? Route::find($routeId)->name
            : 'All Route';

        return view('admin.reports.route_sales_summary', [
            'counterSales' => $counterSales,
            'routes' => $allRoutes,
            'fromDate' => $inputFromDate,
            'toDate' => $inputToDate,
            'selectedRouteId' => $routeId,
            'allRoutesName' => $selectedRouteName,
        ]);
    }


    public function OverallSalesSummary(Request $request)
    {
        // 1. Get Filters and Set Defaults (Validation remains correct)
        $request->validate([
            'from_date' => 'nullable|date_format:d-m-Y',
            'to_date' => 'nullable|date_format:d-m-Y|after_or_equal:from_date',
            'route' => 'nullable|integer',
            'counter_id' => 'nullable|integer',
        ]);

        $defaultDate = date('d-m-Y');
        $inputFromDate = $request->from_date ?? $defaultDate;
        $inputToDate = $request->to_date ?? $defaultDate;
        $routeId = $request->route;
        $counterId = $request->counter_id;

        // Convert dates safely for the database query
        $fromDate = Carbon::createFromFormat('d-m-Y', $inputFromDate)->startOfDay();
        $toDate = Carbon::createFromFormat('d-m-Y', $inputToDate)->endOfDay();

        // 2. Fetch Sales Data (Grouped by Counter ONLY)
        $salesQuery = TicketIssue::query()
            ->join('schedules', 'ticket_issues.schedule_id', '=', 'schedules.id')
            ->join('buses', 'schedules.bus_id', '=', 'buses.id')
            ->select(
                DB::raw('SUM(ticket_issues.seats_count) as sold_seats'),
                DB::raw('SUM(ticket_issues.grand_total) as amount'),
                DB::raw('SUM(ticket_issues.discount_amount) as discount'),
                DB::raw('SUM(ticket_issues.callerman_commission) as callerman_commission_total'),
                DB::raw('SUM(ticket_issues.counter_commission_amount) as counter_commission_total'),
                'ticket_issues.issue_counter_id',
                'buses.registration_number',
            )
            // 🚨 CRITICAL FIX: Filter using JOURNEY_DATE instead of issue_date
            // This assumes the report needs to aggregate based on the travel day.
            ->whereBetween('ticket_issues.journey_date', [$fromDate, $toDate])
            ->where('ticket_issues.status_label', 'Sold');

        // Apply Route Filter (via schedules table)
        if ($routeId) {
            $salesQuery->where('schedules.route_id', $routeId);
        }

        // Apply Single Counter Filter
        if ($counterId) {
            $salesQuery->where('ticket_issues.issue_counter_id', $counterId);
        }

        $salesQuery->groupBy('ticket_issues.issue_counter_id');
        $counterSales = $salesQuery->get();

        // 3. Enhance Data and Prepare for View (Mapping logic remains correct)
        $allRoutes = Route::orderBy('name')->get();

        $counterIds = $counterSales->pluck('issue_counter_id')->unique();
        $counters = \App\Models\Counter::whereIn('id', $counterIds)->get()->keyBy('id');

        $counterSales = $counterSales->map(function ($sale) use ($counters) {
            $counter = $counters->get($sale->issue_counter_id);

            $sale->counter_name = $counter->name ?? 'Online Sales';
            $sale->counter_type = $counter->counter_type ?? 'Own';

            $totalCommission = ($sale->callerman_commission_total ?? 0) + ($sale->counter_commission_total ?? 0);
            $sale->net_amount = ($sale->amount ?? 0) - $totalCommission - ($sale->discount ?? 0);

            // Expose individual commission totals for the Blade footer fix
            $sale->callerman_commission_total = $sale->callerman_commission_total ?? 0;
            $sale->counter_commission_total = $sale->counter_commission_total ?? 0;

            return $sale;
        });
        $allCounters = \App\Models\Counter::orderBy('name')->get();
        $selectedRouteName = $routeId
            ? Route::find($routeId)->name
            : 'All Route';

        return view('admin.reports.overall_sales_summary', [
            'counterSales' => $counterSales,
            'routes' => $allRoutes,
            'fromDate' => $inputFromDate,
            'toDate' => $inputToDate,
            'selectedRouteId' => $routeId,
            'selectedCounterId' => $counterId,
            'allRoutesName' => $selectedRouteName,
            'allCounters' => $allCounters,
        ]);
    }


    public function BusSalesSummary(Request $request)
    {
        // 1. Validate Input
        $request->validate([
            'date' => 'nullable|date_format:d-m-Y',
            'bus_id' => 'nullable|integer',
        ]);

        $inputDate = $request->date ?? date('d-m-Y');
        $busId = $request->bus_id;

        // Convert journey_date (which is only Y-m-d)
        $targetDate = \Carbon\Carbon::createFromFormat('d-m-Y', $inputDate)->format('Y-m-d');


        // Base Query
        $salesQuery = \App\Models\TicketIssue::query()
            ->join('schedules', 'ticket_issues.schedule_id', '=', 'schedules.id')
            ->join('buses', 'schedules.bus_id', '=', 'buses.id')
            ->leftJoin('routes', 'schedules.route_id', '=', 'routes.id')

            ->select(
                \DB::raw('SUM(ticket_issues.seats_count) as sold_seats'),
                \DB::raw('SUM(ticket_issues.grand_total) as amount'),

                'schedules.id as schedule_id',
                'schedules.name as coach_number',
                'buses.registration_number',
                'routes.name as full_route_name',
            )

            ->whereDate('ticket_issues.journey_date', $targetDate)
            ->where('ticket_issues.status_label', 'Sold');

        // Apply Bus Filter
        if ($busId) {
            $salesQuery->where('buses.id', $busId);
        }

        // GROUP BY FIX (MOST IMPORTANT)
        $salesQuery->groupBy(
            'schedules.id',
            'schedules.name',
            'buses.registration_number',
            'routes.name',
        );

        $busSales = $salesQuery->get();

        // Additional Data
        $allBuses = \App\Models\Bus::orderBy('registration_number')->get();

        $selectedBusName = $busId
            ? optional(\App\Models\Bus::find($busId))->registration_number
            : 'All Bus';

        return view('admin.reports.bus_sales_summary', [
            'busSales' => $busSales,
            'allBuses' => $allBuses,
            'selectedBusId' => $busId,
            'inputDate' => $inputDate,
            'selectedBusName' => $selectedBusName,
        ]);
    }

}

