<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\{
    SystemSettingController,
    UserController,
    SeatLayoutController,
    BusController,
    StationController,
    CounterController,
    CouponController,
    ZoneController,
    RouteController,
    FareController,
    ScheduleController,
    ReportController,
    TicketIssueTripController,
    DashboardController,
    LoyaltyDiscountController,
    PrintController,
    StaffsController,
    TicketSegmentController
};
use App\Http\Controllers\Front\WebController;
use App\Http\Controllers\Admin\OfferController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Public / Auth Routes ---
// Route::get('/', [WebController::class, 'index'])->name('welcome');
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login']);
Route::post('/', [LoginController::class, 'logout'])->name('logout');
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// --- ADMIN Group ---
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth'], function () {

    // 1-6. Standard Resources (Users, Settings, Layouts, Buses, etc.)
    Route::resource('users', UserController::class)->except(['show']);
    Route::post('/users/{user}/logout', [UserController::class, 'forceLogout'])->name('users.logout');
    Route::get('/settings', [SystemSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SystemSettingController::class, 'update'])->name('settings.update');
    Route::resource('seat_layouts', SeatLayoutController::class);
    Route::resource('buses', BusController::class);
    Route::resource('zones', ZoneController::class);
    Route::resource('/fares', FareController::class);
    Route::get('fares/get-station-pairs/{route}', [FareController::class, 'getStationPairs'])
        ->name('fares.getStationPairs');

    Route::put('fares/{fare}', [FareController::class, 'update'])->name('fares.update');
    Route::get('fares', [FareController::class, 'index'])->name('fares.index');
    Route::resource('routes', RouteController::class);
    Route::get('routes/{id}/stations', [RouteController::class, 'getStations'])
        ->name('admin.routes.stations');

    Route::get('routes/{route}/stations-view', [RouteController::class, 'showStations'])->name('routes.stations-view');
    Route::resource('stations', StationController::class);
    Route::get('/counter/{counter}/users', [CounterController::class, 'getCounterUsers']);
    Route::get('counters/route-wise-commission', [CounterController::class, 'getRouteCommissions'])->name('counters.route-commission');
    Route::get('stations/{station}/counters', [StationController::class, 'getCounters'])->name('stations.counters.list');
    Route::post('stations/{station}/counters', [CounterController::class, 'store'])->name('stations.counters.store');
    Route::resource('counters', CounterController::class);
    Route::post('counters/{id}/update-time', [CounterController::class, 'updateCounterTime'])->name('counters.updateTime');



    // 7. SCHEDULES (Resource defined last, but AJAX routes must come before it)

    // --- Custom AJAX Routes (Tab 2, 3, etc.) ---

    // Tab 2 Update Counter Assignments
    Route::post('schedules/{id}/update-counters', [ScheduleController::class, 'updateCounters'])
        ->name('schedules.updateCounters');

    // Tab 3 Load Permissions
    Route::get('schedules/{scheduleId}/get-permissions', [ScheduleController::class, 'getCounterPermissions'])
        ->name('schedules.getCounterPermissions');

    // Tab 3 Update Permissions
    Route::post('schedules/{schedule}/update-permissions', [ScheduleController::class, 'updateCounterPermissions'])
        ->name('schedules.updateCounterPermissions');

    // Seat Blocking Modal (ScheduleController's old seat blocking logic)
    Route::get('get-seat-layout/{counterId}', [ScheduleController::class, 'getSeatLayout'])
        ->name('getSeatLayout');
    Route::post('save-blocked-seats', [ScheduleController::class, 'saveBlockedSeats'])
        ->name('saveBlockedSeats');
    Route::get('get-blocked-seats', [ScheduleController::class, 'getBlockedSeats']);
    Route::get('/schedule/{id}/platform-permissions', [ScheduleController::class, 'getPlatformPermissions']);
    Route::post('/schedule/{id}/platform-permissions', [ScheduleController::class, 'savePlatformPermissions']);

    Route::get('/{id}/calendar', [ScheduleController::class, 'loadCalendar']);
    Route::get('/{id}/onoff-list', [ScheduleController::class, 'loadOnOffList']);
    Route::post('/on-days/save', [ScheduleController::class, 'saveOnDays']);

    // --- Schedules Resource ---
    Route::resource('schedules', ScheduleController::class);

    // --- Segment Selling and Availability Routes ---

    // ✅ FIX: Missing route for AJAX call to fetch segment-aware seat status
    Route::post('get-seat-layout-segment', [TicketIssueTripController::class, 'getSeatLayout'])
        ->name('get.seat.layout');

    Route::post('/seat/engage', [TicketIssueTripController::class, 'engageSeat']);
    Route::post('/seat/release', [TicketIssueTripController::class, 'releaseSeat']);
    Route::get('/seats/locks/{scheduleId}', [TicketIssueTripController::class, 'getActiveLocks']);
    Route::post('/ticket-issue/convert-to-sale', [TicketIssueTripController::class, 'convertToSale'])->name('ticket.convertToSale');
    // Ticket Issue Index
    Route::get('/ticket-issue', [TicketIssueTripController::class, 'index'])
        ->name('ticket_issue.index');

    // Ticket Issue UI load (Initial)
    Route::get('ticket-issue-ui/{id}', [TicketIssueTripController::class, 'loadUI'])
        ->name('admin.ticket.issue.ui');
    Route::post(
        '/ticket-issue-ui/confirm-seat',
        [TicketIssueTripController::class, 'confirmSeat'],
    );
    Route::get(
        '/ticket-issue/load-seat-plan/{tripId}/{originStationId}/{destinationStationId}',
        [TicketIssueTripController::class, 'loadUI'],
    )->name('admin.ticket_issue.load_seat_plan');
    Route::post('/ticket-issue-trip/{trip}/reload-seats', [TicketIssueTripController::class, 'reloadSeats'])
        ->name('ticketIssueTrip.reloadSeats');
    Route::post('tickets/{id}/cancel', [TicketIssueTripController::class, 'cancel'])->name('ticket.cancel.selective');
    // Ticket Issue Store
    Route::post(
        '/ticket-issue/store',
        [\App\Http\Controllers\Admin\TicketIssueTripController::class, 'store'],
    )->name('ticket.issue.store');

    // View/Cancel/Reports
    Route::get('/ticket-issue/view/{id}', [TicketIssueTripController::class, 'view'])
        ->name('admin.ticket.issue.view');
    Route::get('ticket-issue/trip-sheet/{id}', [App\Http\Controllers\Admin\TicketIssueTripController::class, 'tripSheet']);
    Route::get('reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
    Route::get('reports/cancel', [ReportController::class, 'cancelReport'])
        ->name('reports.cancel');

    Route::post('/ticket/send-sms/{invoiceId}', [TicketIssueTripController::class, 'sendSms'])->name('ticket.send_sms');
    Route::post('/ticket/send-email/{invoiceId}', [TicketIssueTripController::class, 'sendEmail'])->name('ticket.send_email');
    Route::post('ticket-issue/cancel/{id}', [TicketIssueTripController::class, 'cancel'])
        ->name('ticket.cancel');
    Route::get('reports/booking', [ReportController::class, 'bookingReport'])
        ->name('reports.booking');
    Route::get('/reports/loyalty', [ReportController::class, 'loyaltyReport'])
        ->name('reports.loyalty_report');
    Route::get('/reports/trip-sheet', [ReportController::class, 'overallTripSheet'])
        ->name('reports.trip_sheet_report');

    Route::get('seats/data/{scheduleId}/{originId}/{destinationId}', [TicketIssueTripController::class, 'getSeatData'])
        ->name('admin.seats.data');
    // Legacy TicketSegmentController (if still used)
    Route::post('segment/sell', [TicketSegmentController::class, 'sellSegment'])->name('segment.sell');
    Route::post('segment/check', [TicketSegmentController::class, 'checkAvailability'])->name('segment.check');
    Route::post('get-seat-layout-segment', [TicketIssueTripController::class, 'getSeatLayout'])
        ->name('get.seat.layout');
    // শুধুমাত্র এই একটি রাউট রাখুন, আগের দুটি মুছে দিন
    Route::get('ticket-issue/seats/{fromTo}', [TicketIssueTripController::class, 'getSeatsByRoute'])
        ->name('admin.ticket-issue.seats');
    // যদি তুমি admin prefix use করো
    Route::get('/get-passenger-info/{mobile}', [TicketIssueTripController::class, 'search'])
        ->name('passengers.search');

    // Variant B (trip/schedule id)
    Route::get('ticket-issue/seats/{scheduleId}', [TicketIssueTripController::class, 'getSeatsBySchedule'])
        ->where('scheduleId', '[0-9]+');
    Route::get('passengers/check-loyalty', [TicketIssueTripController::class, 'checkLoyaltyDiscount']);
    // Variant C (schedule/from/to)
    Route::get('ticket-issue/seats/{scheduleId}/{from}/{to}', [TicketIssueTripController::class, 'getSeatsByScheduleFromTo']);
    Route::get('passengers/search', [TicketIssueTripController::class, 'search'])->name('admin.passengers.search');
    Route::prefix('loyalty')->name('loyalty.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\LoyaltyDiscountController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\LoyaltyDiscountController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\LoyaltyDiscountController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\LoyaltyDiscountController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [\App\Http\Controllers\Admin\LoyaltyDiscountController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [\App\Http\Controllers\Admin\LoyaltyDiscountController::class, 'destroy'])->name('delete');

    });
    Route::post('/print/non-ac-ticket', [PrintController::class, 'printNonAC'])->name('print.nonac');
    Route::get('/reports/route-sales-summary', [ReportController::class, 'SalesSummary'])->name('reports.route_sales_summary');
    Route::get('reports/overall-sales-summary', [ReportController::class, 'OverallSalesSummary'])
        ->name('reports.overall-sales-summary');
    Route::get('reports/bus-sales-summary', [ReportController::class, 'BusSalesSummary'])
        ->name('reports.bus-sales-summary');
    Route::resource('staffs', StaffsController::class);



    // ইনডেক্স পেজ (যেখানে লিস্ট দেখবেন)
    Route::get('offers', [OfferController::class, 'index'])->name('offers.index');

    // নতুন অফার তৈরির ফর্ম
    Route::get('offers/create', [OfferController::class, 'create'])->name('offers.create');

    // ডেটা সেভ করার রাউট
    Route::post('offers/store', [OfferController::class, 'store'])->name('offers.store');

    // এডিট রাউট
    Route::get('offers/edit/{offer}', [OfferController::class, 'edit'])->name('offers.edit');

    // আপডেট রাউট
    Route::put('offers/update/{offer}', [OfferController::class, 'update'])->name('offers.update');

    // ডিলিট রাউট
    Route::delete('offers/delete/{id}', [OfferController::class, 'destroy'])->name('offers.delete');
    Route::resource('coupons', CouponController::class);

    // Tab 4 এর জন্য রাউট
    Route::post(
        'schedules/{id}/update-platform-permissions',
        [App\Http\Controllers\Admin\ScheduleController::class, 'savePlatformPermissions'],
    )
        ->name('schedules.updatePlatformPermissions');

});