@extends('layouts.master')

@section('title', 'Edit Offer')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                    <h4 class="text-success font-weight-bold my-2 mr-5">Edit Offer: {{ $offer->offer_name }}</h4>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-5">
            {{-- ফিক্সড: এরর মেসেজ ডিসপ্লে --}}
            @if ($errors->any())
                <div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
                    <div class="alert-icon"><i class="flaticon-warning"></i></div>
                    <div class="alert-text">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title text-success font-weight-bolder">Update Discount Offer Details</h3>
                </div>

                <form action="{{ route('admin.offers.update', $offer->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row">
                            {{-- অফারের নাম --}}
                            <div class="col-md-6 form-group">
                                <label>Offer Name <span class="text-danger">*</span></label>
                                <input type="text" name="offer_name" class="form-control"
                                    value="{{ old('offer_name', $offer->offer_name) }}" required>
                            </div>

                            {{-- ফিক্সড: Select Schedule (Trip Code সহ) --}}
                            <div class="col-md-6 form-group">
                                <label>Select Specific Trip (Trip Code)</label>
                                <select name="schedule_id" class="form-control " id="schedule_select">
                                    <option value="">-- All Trips (Global Offer) --</option>
                                    @isset($schedules)
                                        @foreach($schedules as $schedule)
                                            <option value="{{ $schedule->id }}" 
                                                {{ old('schedule_id', $offer->schedule_id) == $schedule->id ? 'selected' : '' }}>
                                                {{ $schedule->trip_code }} - {{ $schedule->bus_type }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                                <small class="text-muted">নির্দিষ্ট ট্রিপ পরিবর্তন করতে ট্রিপ কোড সিলেক্ট করুন।</small>
                            </div>

                            {{-- রুট সিলেক্ট --}}
                            <div class="col-md-6 form-group">
                                <label>Select Route</label>
                                <select name="route_id" class="form-control ">
                                    <option value="">-- All Routes --</option>
                                    @foreach($routes as $route)
                                        <option value="{{ $route->id }}" {{ old('route_id', $offer->route_id) == $route->id ? 'selected' : '' }}>
                                            {{ $route->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- বাস টাইপ --}}
                            <div class="col-md-6 form-group">
                                <label>Bus Type</label>
                                <select name="bus_type" class="form-control ">
                                    <option value="">Both (AC & Non-AC)</option>
                                    <option value="AC" {{ old('bus_type', $offer->bus_type) == 'AC' ? 'selected' : '' }}>Only AC</option>
                                    <option value="Non-AC" {{ old('bus_type', $offer->bus_type) == 'Non-AC' ? 'selected' : '' }}>Only Non-AC</option>
                                </select>
                            </div>

                            {{-- ডিসকাউন্ট অ্যামাউন্ট --}}
                            <div class="col-md-4 form-group">
                                <label>Discount Amount (৳) <span class="text-danger">*</span></label>
                                <input type="number" name="discount_amount" class="form-control"
                                    value="{{ old('discount_amount', $offer->discount_amount) }}" required>
                            </div>

                            {{-- সর্বনিম্ন ভাড়া --}}
                            <div class="col-md-4 form-group">
                                <label>Minimum Fare (৳) <span class="text-danger">*</span></label>
                                <input type="number" name="min_fare" class="form-control"
                                    value="{{ old('min_fare', $offer->min_fare) }}" required>
                            </div>

                            {{-- সর্বোচ্চ ভাড়া --}}
                            <div class="col-md-4 form-group">
                                <label>Maximum Fare (৳) <span class="text-danger">*</span></label>
                                <input type="number" name="max_fare" class="form-control"
                                    value="{{ old('max_fare', $offer->max_fare) }}" required>
                            </div>

                            {{-- শুরুর তারিখ --}}
                            <div class="col-md-4 form-group">
                                <label>Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ old('start_date', is_string($offer->start_date) ? $offer->start_date : $offer->start_date->format('Y-m-d')) }}"
                                    required>
                            </div>

                            {{-- শেষ তারিখ --}}
                            <div class="col-md-4 form-group">
                                <label>End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ old('end_date', is_string($offer->end_date) ? $offer->end_date : $offer->end_date->format('Y-m-d')) }}"
                                    required>
                            </div>

                            {{-- স্ট্যাটাস --}}
                            <div class="col-md-4 form-group">
                                <label>Status</label>
                                <select name="is_active" class="form-control">
                                    <option value="1" {{ old('is_active', $offer->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active', $offer->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary font-weight-bold px-10 mr-2">Update Offer</button>
                        <a href="{{ route('admin.offers.index') }}" class="btn btn-light-primary font-weight-bold">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Select2 ইনিশিয়ালাইজেশন
            $('.select2').select2({
                placeholder: "Select an option",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection