@extends('layouts.master')

@section('title', 'Edit Coupon')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        {{-- Subheader --}}
        <div class="subheader subheader-solid" id="kt_subheader">
            <div class="container-fluid">
                <h4 class="text-danger font-weight-bold my-2 mr-5">Edit Coupon: {{ $coupon->coupon_code }}</h4>
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
                    <h3 class="card-title text-danger font-weight-bolder">Update Coupon Details</h3>
                </div>

                {{-- ফিক্সড: কুপন আপডেট ফর্ম --}}
                <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row">
                            {{-- কুপন কোড --}}
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Coupon Code <span class="text-danger">*</span></label>
                                <input type="text" name="coupon_code" class="form-control uppercase"
                                    value="{{ old('coupon_code', $coupon->coupon_code) }}" required>
                            </div>

                            {{-- অফারের নাম --}}
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Offer Name <span class="text-danger">*</span></label>
                                <input type="text" name="offer_name" class="form-control"
                                    value="{{ old('offer_name', $coupon->offer_name) }}" required>
                            </div>

                            {{-- ডিসকাউন্ট অ্যামাউন্ট --}}
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold">Discount Amount (৳) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="discount_amount" class="form-control"
                                    value="{{ old('discount_amount', $coupon->discount_amount) }}" required>
                            </div>

                            {{-- সর্বনিম্ন ভাড়া --}}
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold">Min Fare to Apply (৳) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="min_fare" class="form-control"
                                    value="{{ old('min_fare', $coupon->min_fare) }}" required>
                            </div>

                            {{-- স্ট্যাটাস --}}
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold">Status</label>
                                <select name="is_active" class="form-control">
                                    <option value="1" {{ $coupon->is_active ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$coupon->is_active ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            {{-- শুরুর তারিখ --}}
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ old('start_date', $coupon->start_date->format('Y-m-d')) }}" required>
                            </div>

                            {{-- শেষ তারিখ --}}
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ old('end_date', $coupon->end_date->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-danger font-weight-bold px-10 mr-2">Update Coupon</button>
                        <a href="{{ route('admin.coupons.index') }}"
                            class="btn btn-light-primary font-weight-bold">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // কোড বড় হাতের করার জাভাস্ক্রিপ্ট
        $('.uppercase').keyup(function () {
            this.value = this.value.toUpperCase();
        });
    </script>
@endsection