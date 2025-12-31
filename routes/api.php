<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CouponApiController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\SchedulePermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BusSearchController;
use App\Http\Controllers\Api\StationControlle; // নামের বানান ঠিক করা হয়েছে
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes for Chokrojan Next.js Frontend
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // --- পাবলিক এপিআই (সবার জন্য) ---

    // স্টেশনের লিস্ট (হোম পেজ ড্রপডাউন)
    Route::get('/stations', [StationControlle::class, 'index']);

    // ট্রিপ/বাস সার্চ
    Route::get('/search-trips', [BusSearchController::class, 'search']);
    Route::get('/schedule-permissions/{scheduleId}', [SchedulePermissionController::class, 'getPlatformPermissions']);
    Route::post('/schedule-permissions-save/{id}', [SchedulePermissionController::class, 'savePlatformPermissions']);
    Route::get('/active-offers', [OfferController::class, 'getActiveOffers']);
    Route::post('/validate-coupon', [CouponApiController::class, 'validateCoupon']);
    // সিট লেআউট ডাটা (ইউজার যখন বাসের 'সিট দেখুন' ক্লিক করবে)
    Route::get('/trips/{id}/seats/{fromId}/{toId}', [BusSearchController::class, 'getSeatsResponse']);

    // মোবাইল নম্বর দিয়ে প্যাসেঞ্জার প্রোফাইল খোঁজা
    Route::get('/passengers/search/{mobile}', [BookingController::class, 'searchPassenger']);


    // --- সিট লকিং এপিআই (Real-time seat lock) ---

    // সিট সিলেক্ট/লক করা (Engage)
    Route::post('/seats/lock', [BookingController::class, 'lockSeat']);

    // সিট আনলক করা (Release)
    Route::post('/seats/unlock', [BookingController::class, 'unlockSeat']);


    // --- প্রোটেক্টেড এপিআই (লগইন করা ইউজার বা টোকেন প্রয়োজন) ---
    // যদি আপনি Sanctum ব্যবহার করেন তবে middleware('auth:sanctum') যোগ করবেন
    Route::group(['middleware' => ['auth:sanctum']], function () {

        // টিকিট বুকিং বা স্টোর করা


        Route::get('/passenger-search/{mobile}', [BookingController::class, 'searchPassenger']);

        // ইউজারের বুকিং হিস্ট্রি


        // টিকিট ক্যানসেল এবং রিশিডিউল
        Route::post('/ticket-cancel/{id}', [BookingController::class, 'cancel']);
        Route::post('/ticket-reschedule/{id}', [BookingController::class, 'reschedule']);

        // কাউন্টার অনুযায়ী মাস্টার লিস্ট
        Route::get('/counters/{id}/masters', [BookingController::class, 'getCounterMasters']);
    });
    Route::post('/ticket-booking', [BookingController::class, 'store']);
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/user-profile', [UserController::class, 'profile']);
    Route::post('/user-update', [UserController::class, 'update']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::get('/my-bookings', [UserController::class, 'myBookings']);
});
Route::group(['prefix' => 'v1'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    // এটি পাবলিক রাউট হতে হবে (auth মিডলওয়্যারের বাইরে)
    Route::post('/send-login-code', [AuthController::class, 'sendLoginCode']);
    Route::post('/verify-login-code', [AuthController::class, 'verifyLoginCode']);
    // অন্যান্য প্রোটেক্টেড রাউট এখানে থাকবে
    // Route::group(['middleware' => ['auth:sanctum']], function () {
    //     Route::get('/user-profile', [UserController::class, 'profile']);
    // });
});