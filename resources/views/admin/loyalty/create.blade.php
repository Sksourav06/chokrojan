@extends('layouts.master')

@section('title', 'Create Discount Rule')

@section('content')
    <div class="container mt-5">

        <div class="card card-custom">
            <div class="card-header">
                <h3 class="card-title">Add Loyalty Discount Rule</h3>
            </div>

            <form method="POST" action="{{ route('admin.loyalty.store') }}">
                @csrf

                <div class="card-body">

                    <div class="form-group">
                        <label>Days Threshold <span class="text-danger">*</span></label>
                        <input type="number" name="days_threshold" class="form-control" placeholder="15 / 30 / 60 etc."
                            required>
                    </div>

                    <div class="form-group">
                        <label>Discount Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" name="discount_amount" class="form-control" required>
                    </div>

                    {{-- 🔥 NEW: Date Range Fields --}}
                    <div class="form-group">
                        <label>Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" required
                            value="{{ old('start_date', $discount->start_date ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control" required
                            value="{{ old('end_date', $discount->end_date ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="is_active" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                </div>

                <div class="card-footer">
                    <button class="btn btn-primary">Save</button>
                </div>

            </form>
        </div>

    </div>
    <script>
        $(document).ready(function () {
            $('#start_date').datepicker({
                format: 'dd-mm-yyyy', // format same as backend expects
                autoclose: true,
                todayHighlight: true
            });
        });
        $(document).ready(function () {
            $('#end_date').datepicker({
                format: 'dd-mm-yyyy', // format same as backend expects
                autoclose: true,
                todayHighlight: true
            });
        });
        $(document).ready(function () {
            $('.selectpicker').selectpicker();
        });
    </script>
@endsection