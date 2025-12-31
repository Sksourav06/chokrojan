@extends('layouts.master')

@section('title', 'Create New Coupon')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader subheader-solid" id="kt_subheader">
            <div class="container-fluid">
                <h4 class="text-danger font-weight-bold my-2 mr-5">Create New Coupon</h4>
            </div>
        </div>

        <div class="container-fluid mt-5">
            {{-- ভ্যালিডেশন এরর মেসেজ --}}
            @if ($errors->any())
                <div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
                    <div class="alert-text">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="card card-custom shadow-sm">
                <div class="card-header">
                    <h3 class="card-title text-danger font-weight-bolder">Coupon Details</h3>
                </div>

                <form action="{{ route('admin.coupons.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            {{-- কুপন কোড --}}
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Coupon Code <span class="text-danger">*</span></label>
                                <input type="text" name="coupon_code" class="form-control uppercase" 
                                    placeholder="e.g. SAVE200" value="{{ old('coupon_code') }}" required>
                                <small class="text-muted">এই কোডটি ইউজার চেকআউট পেজে ব্যবহার করবে।</small>
                            </div>

                            {{-- অফারের নাম --}}
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Offer Name <span class="text-danger">*</span></label>
                                <input type="text" name="offer_name" class="form-control" 
                                    placeholder="e.g. New Year Special" value="{{ old('offer_name') }}" required>
                            </div>

                            {{-- ডিসকাউন্ট অ্যামাউন্ট --}}
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold">Discount Amount (৳) <span class="text-danger">*</span></label>
                                <input type="number" name="discount_amount" class="form-control" 
                                    placeholder="200" value="{{ old('discount_amount') }}" required>
                            </div>

                            {{-- সর্বনিম্ন ভাড়া --}}
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold">Min Fare to Apply (৳) <span class="text-danger">*</span></label>
                                <input type="number" name="min_fare" class="form-control" 
                                    placeholder="500" value="{{ old('min_fare', 0) }}" required>
                            </div>

                            {{-- স্ট্যাটাস --}}
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold">Status</label>
                                <select name="is_active" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            {{-- শুরুর তারিখ --}}
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" 
                                    value="{{ old('start_date', date('Y-m-d')) }}" required>
                            </div>

                            {{-- শেষ তারিখ --}}
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" 
                                    value="{{ old('end_date') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-danger font-weight-bold px-10 mr-2">Save Coupon</button>
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // কুপন কোড ইনপুট দেওয়ার সময় অটোমেটিক বড় হাতের অক্ষর করা
        $('.uppercase').keyup(function() {
            this.value = this.value.toUpperCase();
        });
    </script>
@endsection