@extends('layouts.master')

@section('title', 'Edit Discount Rule')

@section('content')
    <div class="container mt-5">

        <div class="card card-custom">
            <div class="card-header">
                <h3 class="card-title">Edit Loyalty Discount </h3>
            </div>

            <form method="POST" action="{{ route('admin.loyalty.update', $discount->id) }}">
                @csrf
                @method('POST') {{-- যদি PUT/RPATCH route use করা হয়, তাহলে এখানে method('PUT') করতে হবে --}}

                <div class="card-body">

                    <div class="form-group">
                        <label>Days Threshold <span class="text-danger">*</span></label>
                        <input type="number" name="days_threshold" class="form-control" placeholder="15 / 30 / 60 etc."
                            value="{{ old('days_threshold', $discount->days_threshold) }}" required>
                        @error('days_threshold')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Discount Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="discount_amount" class="form-control" step="0.01"
                            value="{{ old('discount_amount', $discount->discount_amount) }}" required>
                        @error('discount_amount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control"
                            value="{{ old('start_date', $discount->start_date) }}" required>
                        @error('start_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control"
                            value="{{ old('end_date', $discount->end_date) }}" required>
                        @error('end_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="is_active" class="form-control">
                            <option value="1" {{ old('is_active', $discount->is_active) == 1 ? 'selected' : '' }}>Active
                            </option>
                            <option value="0" {{ old('is_active', $discount->is_active) == 0 ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Rule</button>
                    <a href="{{ route('admin.loyalty.index') }}" class="btn btn-secondary">Cancel</a>
                </div>

            </form>
        </div>

    </div>
@endsection