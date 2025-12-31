@extends('layouts.master')

@section('title', 'Add New Offer')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader subheader-solid" id="kt_subheader">
            <div class="container-fluid">
                <h4 class="text-success font-weight-bold my-2 mr-5">Add New Offer</h4>
            </div>
        </div>

        <div class="container-fluid mt-5">
            {{-- ফিক্সড: এরর মেসেজ স্টাইলিং --}}
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
                    <div class="alert-close">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="ki ki-close"></i></span>
                        </button>
                    </div>
                </div>
            @endif

            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title text-success font-weight-bolder">Create Discount Offer</h3>
                </div>

                <form action="{{ route('admin.offers.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            {{-- Offer Name --}}
                            <div class="col-md-6 form-group">
                                <label>Offer Name <span class="text-danger">*</span></label>
                                <input type="text" name="offer_name" class="form-control" placeholder="e.g. Eid Sale"
                                    value="{{ old('offer_name') }}" required>
                            </div>

                            {{-- ফিক্সড: Select Schedule (Trip Code সহ) --}}
                            <div class="col-md-6 form-group">
                                <label>Select Specific Trip (Trip Code)</label>
                                <select name="schedule_id" class="form-control select2" id="schedule_select">
                                    <option value="">-- All Trips (Global Offer) --</option>
                                    @isset($schedules)
                                        @foreach($schedules as $sch)
                                            <option value="{{ $sch->id }}" {{ old('schedule_id') == $sch->id ? 'selected' : '' }}>
                                                #{{ $sch->trip_code }} - {{ $sch->bus_type }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                                <small class="text-muted">নির্দিষ্ট ট্রিপে অফার দিতে ট্রিপ কোড সিলেক্ট করুন।</small>
                            </div>

                            {{-- Select Route --}}
                            <div class="col-md-6 form-group">
                                <label>Select Route</label>
                                <select name="route_id" class="form-control select2">
                                    <option value="">-- All Routes --</option>
                                    @isset($routes)
                                        @foreach($routes as $route)
                                            <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                                {{ $route->name }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>

                            {{-- Bus Type --}}
                            <div class="col-md-6 form-group">
                                <label>Bus Type</label>
                                <select name="bus_type" class="form-control select2">
                                    <option value="">Both (AC & Non-AC)</option>
                                    <option value="AC" {{ old('bus_type') == 'AC' ? 'selected' : '' }}>Only AC</option>
                                    <option value="Non-AC" {{ old('bus_type') == 'Non-AC' ? 'selected' : '' }}>Only Non-AC
                                    </option>
                                </select>
                            </div>

                            {{-- Discount Amount --}}
                            <div class="col-md-4 form-group">
                                <label>Discount Amount (৳) <span class="text-danger">*</span></label>
                                <input type="number" name="discount_amount" class="form-control" placeholder="200"
                                    value="{{ old('discount_amount') }}" required>
                            </div>

                            {{-- Min Fare --}}
                            <div class="col-md-4 form-group">
                                <label>Min Fare (৳) <span class="text-danger">*</span></label>
                                <input type="number" name="min_fare" class="form-control" placeholder="300"
                                    value="{{ old('min_fare') }}" required>
                            </div>

                            {{-- Max Fare --}}
                            <div class="col-md-4 form-group">
                                <label>Max Fare (৳) <span class="text-danger">*</span></label>
                                <input type="number" name="max_fare" class="form-control" placeholder="5000"
                                    value="{{ old('max_fare') }}" required>
                            </div>

                            {{-- Start Date --}}
                            <div class="col-md-4 form-group">
                                <label>Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}"
                                    required>
                            </div>

                            {{-- End Date --}}
                            <div class="col-md-4 form-group">
                                <label>End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}"
                                    required>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-4 form-group">
                                <label>Status</label>
                                <select name="is_active" class="form-control">
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-success font-weight-bold px-10 mr-2">Save Offer</button>
                        <a href="{{ route('admin.offers.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Select2 ইনিশিয়ালাইজেশন (ক্লাস ব্যবহার করা হয়েছে)
            $('.select2').select2({
                placeholder: "Select an option",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection