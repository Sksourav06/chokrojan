@extends('layouts.master')

@section('title', 'System Settings')

@section('content')
<style>
    /* Safe scroll without breaking Metronic */
    .settings-scroll {
        height: calc(100vh - 70px);
        overflow-y: auto;
        overflow-x: hidden;
        padding: 20px;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid settings-scroll" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Update General Settings</h3>
                </div>

                <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        <h4>Common Settings</h4>
                        <div class="card border-0">
                            <div class="card-body card-rounded bg-danger-o-20 mb-7">
                                <div class="row">
                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label class="required">Site Name</label>
                                            <input type="text" class="form-control" name="site_name"
                                                value="{{ old('site_name', $settings->site_name ?? '') }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="required">Booking Auto Cancel Before Schedule Start Time (Minutes)</label>
                                            <input type="number" class="form-control" name="booking_cancel_time"
                                                value="{{ old('booking_cancel_time', $settings->booking_cancel_time ?? 60) }}" min="1" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="required">Permitted Block Seat Auto Release (Minutes)</label>
                                            <input type="number" class="form-control" name="permitted_seat_block_release_time"
                                                value="{{ old('permitted_seat_block_release_time', $settings->permitted_seat_block_release_time ?? '') }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="required">Advance Booking Allow (Days)</label>
                                            <input type="number" class="form-control" name="advance_booking"
                                                value="{{ old('advance_booking', $settings->advance_booking ?? '') }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="required">Selected Seat Lifetime (Seconds)</label>
                                            <input type="number" class="form-control" name="selected_seat_lifetime"
                                                value="{{ old('selected_seat_lifetime', $settings->selected_seat_lifetime ?? '') }}" required>
                                        </div>

                                        <div class="form-group">
                                            <p class="mb-5">Booked or Issued Ticket's Individual Seat Cancel Permission</p>
                                            <input type="hidden" name="seat_cancel_allow" value="0">
                                            <label class="checkbox checkbox-outline checkbox-success">
                                                <input type="checkbox" name="seat_cancel_allow" value="1"
                                                    {{ old('seat_cancel_allow', $settings->seat_cancel_allow ?? false) ? 'checked' : '' }}>
                                                <span></span>Individual Seat Cancel Allow
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label class="required">Previous Date View Allow (Days)</label>
                                            <input type="number" class="form-control" name="previous_date_view_allow"
                                                value="{{ old('previous_date_view_allow', $settings->previous_date_view_allow ?? '') }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="required">Passenger Star Rating</label>
                                            <input type="number" class="form-control" name="passenger_star_rating"
                                                value="{{ old('passenger_star_rating', $settings->passenger_star_rating ?? '') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label>Site Logo</label>
                                            @if(!empty($settings->logo))
                                                <div class="mb-3">
                                                    <img src="{{ asset('storage/'.$settings->logo) }}" width="120" class="border">
                                                </div>
                                            @endif
                                            <input type="file" name="logo" class="form-control">
                                        </div>

                                        @foreach ([
                                            'booking' => 'Seat Booking',
                                            'vip_booking' => 'VIP Booking',
                                            'goods_charge' => 'Goods Charge',
                                            'callerman_commission' => 'Callerman Commission',
                                            'discount' => 'Discount',
                                            'discount_show_in_ticket' => 'Discount Show in Ticket'
                                        ] as $name => $label)

                                            <input type="hidden" name="{{ $name }}" value="0">

                                            <div class="form-group">
                                                <label class="checkbox checkbox-outline checkbox-success">
                                                    <input type="checkbox" name="{{ $name }}" value="1"
                                                        {{ old($name, $settings->$name ?? false) ? 'checked' : '' }}>
                                                    <span></span>{{ $label }}
                                                </label>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-8">

                            <div class="col-md-6">
                                <h4>Counter Settings</h4>
                                <div class="card border-0">
                                    <div class="card-body card-rounded bg-success-o-20">

                                        <div class="form-group">
                                            <label>Counter Booking Lifetime (Minutes)</label>
                                            <input type="number" name="booking_lifetime" class="form-control"
                                                value="{{ old('booking_lifetime', $settings->booking_lifetime ?? 0) }}">
                                        </div>

                                        <div class="form-group">
                                            <label>Counter Sales Allow After Schedule Start Time (Minutes)</label>
                                            <input type="number" name="counter_sales_allow_time" class="form-control"
                                                value="{{ old('counter_sales_allow_time', $settings->counter_sales_allow_time ?? 0) }}">
                                        </div>

                                        <div class="form-group">
                                            <label>Maximum Seat Limit Per Counter Ticket</label>
                                            <input type="number" name="counter_max_seat_per_ticket" class="form-control"
                                                value="{{ old('counter_max_seat_per_ticket', $settings->counter_max_seat_per_ticket ?? 0) }}">
                                        </div>

                                        <input type="hidden" name="counter_cancel_allow" value="0">
                                        <div class="form-group">
                                            <label class="checkbox checkbox-outline checkbox-success">
                                                <input type="checkbox" name="counter_cancel_allow" value="1"
                                                    {{ old('counter_cancel_allow', $settings->counter_cancel_allow ?? false) ? 'checked' : '' }}>
                                                <span></span>Counter Cancel Allow
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label>Counter Cancel Fine (%)</label>
                                            <input type="number" step="0.01" name="counter_cancel_fine" class="form-control"
                                                value="{{ old('counter_cancel_fine', $settings->counter_cancel_fine ?? 0) }}">
                                        </div>

                                        <div class="form-group">
                                            <label>Counter Cancel Allow Time (Minutes)</label>
                                            <input type="number" name="counter_cancel_allow_time" class="form-control"
                                                value="{{ old('counter_cancel_allow_time', $settings->counter_cancel_allow_time ?? 0) }}">
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h4>Online Settings</h4>
                                <div class="card border-0">
                                    <div class="card-body card-rounded bg-warning-o-20">

                                        <div class="form-group">
                                            <label>Online Booking Lifetime (Minutes)</label>
                                            <input type="number" name="online_booking_lifetime" class="form-control"
                                                value="{{ old('online_booking_lifetime', $settings->online_booking_lifetime ?? 0) }}">
                                        </div>

                                        <div class="form-group">
                                            <label>Online Sales Disallow Before Schedule Start Time (Minutes)</label>
                                            <input type="number" name="online_sales_disallow_time" class="form-control"
                                                value="{{ old('online_sales_disallow_time', $settings->online_sales_disallow_time ?? 0) }}">
                                        </div>

                                        <div class="form-group">
                                            <label>Maximum Seat Limit Per Online Ticket</label>
                                            <input type="number" name="online_max_seat_per_ticket" class="form-control"
                                                value="{{ old('online_max_seat_per_ticket', $settings->online_max_seat_per_ticket ?? 0) }}">
                                        </div>

                                        <input type="hidden" name="online_cancel_allow" value="0">
                                        <div class="form-group">
                                            <label class="checkbox checkbox-outline checkbox-success">
                                                <input type="checkbox" name="online_cancel_allow" value="1"
                                                    {{ old('online_cancel_allow', $settings->online_cancel_allow ?? false) ? 'checked' : '' }}>
                                                <span></span>Online Cancel Allow
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label>Online Cancel Fine (%)</label>
                                            <input type="number" step="0.01" name="online_cancel_fine" class="form-control"
                                                value="{{ old('online_cancel_fine', $settings->online_cancel_fine ?? 0) }}">
                                        </div>

                                        <div class="form-group">
                                            <label>Online Cancel Allow Time (Hours)</label>
                                            <input type="number" name="online_cancel_allow_time" class="form-control"
                                                value="{{ old('online_cancel_allow_time', $settings->online_cancel_allow_time ?? 0) }}">
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-pill btn-primary">Submit</button>
                        <button type="reset" class="btn btn-pill btn-warning">Reset</button>
                        <a href="{{ url('/home') }}" class="btn btn-pill btn-secondary">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
