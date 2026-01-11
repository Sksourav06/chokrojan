@extends('layouts.master')

@section('title', 'Create New Fare')

@section('content')
    <div class="container-fluid">
        <div class="card card-custom">
            <div class="card-header">
                <h3 class="card-title">Create New Fare</h3>
            </div>

            <form method="POST" action="{{ route('admin.fares.store') }}">
                @csrf
                <div class="card-body">
                    <div class="row">
                        {{-- ========================================== --}}
                        {{-- LEFT COLUMN: Main Fare Rules & Info --}}
                        {{-- ========================================== --}}
                        <div class="col-md-6">

                            {{-- Fare Name --}}
                            <div class="form-group @error('name') has-error @enderror">
                                <label for="name" class="required">Fare Name</label>
                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Enter name" name="name" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Route Dropdown --}}
                            <div class="form-group @error('route_id') has-error @enderror">
                                <label for="route_id" class="required">Route</label>
                                <select id="route_id" class="form-control @error('route_id') is-invalid @enderror"
                                    name="route_id" required>
                                    <option value="">Select Route...</option>
                                    @foreach($routes as $route)
                                        <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                            {{ $route->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('route_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="row">
                                {{-- Seat Plan --}}
                                <div class="col-md-6">
                                    <div class="form-group @error('seat_layout_id') has-error @enderror">
                                        <label for="seat_layout_id" class="required">Seat Plan</label>
                                        <select id="seat_layout_id"
                                            class="form-control @error('seat_layout_id') is-invalid @enderror"
                                            name="seat_layout_id" required>
                                            <option value="">Select Seat Plan...</option>
                                            @foreach($seatLayouts as $layout)
                                                <option value="{{ $layout->id }}" {{ old('seat_layout_id') == $layout->id ? 'selected' : '' }}>
                                                    {{ $layout->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('seat_layout_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                {{-- Bus Type --}}
                                <div class="col-md-6">
                                    <div class="form-group @error('vehicle_type_id') has-error @enderror">
                                        <label for="vehicle_type_id" class="required">Bus Type</label>
                                        <select id="vehicle_type_id"
                                            class="form-control @error('vehicle_type_id') is-invalid @enderror"
                                            name="vehicle_type_id" required>
                                            <option value="">Select Bus Type...</option>
                                            @foreach($busTypes as $id => $name)
                                                <option value="{{ $id }}" {{ old('vehicle_type_id') == $id ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vehicle_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- From Date --}}
                                <div class="col-md-6">
                                    <div class="form-group @error('from_date') has-error @enderror">
                                        <label for="from_date">From Date</label>
                                        <input type="date" id="from_date"
                                            class="form-control @error('from_date') is-invalid @enderror" name="from_date"
                                            value="{{ old('from_date') }}">
                                    </div>
                                </div>
                                {{-- To Date --}}
                                <div class="col-md-6">
                                    <div class="form-group @error('to_date') has-error @enderror">
                                        <label for="to_date">To Date</label>
                                        <input type="date" id="to_date"
                                            class="form-control @error('to_date') is-invalid @enderror" name="to_date"
                                            value="{{ old('to_date') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="form-group @error('status') has-error @enderror">
                                <label for="status" class="required">Status</label>
                                <select id="status" class="form-control @error('status') is-invalid @enderror" name="status"
                                    required>
                                    @foreach($availableStatuses as $status)
                                        <option value="{{ $status }}" {{ old('status', 'active') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- ========================================== --}}
                        {{-- RIGHT COLUMN: Dynamic Station-to-Station --}}
                        {{-- ========================================== --}}
                        <div class="col-md-6">
                            <div class="row">
                                <div class="form-group col-md-10">
                                    <label for="station_to_station">
                                        <i class="far fa-star text-danger fa-sm"></i> Station to Station
                                        <span id="sts_loading" style="display: none;">
                                            <i class="fas fa-spinner fa-spin text-info"></i>
                                        </span>
                                    </label>
                                    <select id="station_to_station" class="form-control" disabled>
                                        <option value="">Select Route First...</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-2">
                                    {{-- Fixed alignment: Label used for spacing --}}
                                    <label class="d-block">&nbsp;</label>
                                    <button type="button" class="btn btn-block btn-outline-primary" id="add-sts-fare"
                                        disabled>
                                        <i class="fas fa-plus pr-0"></i> Add
                                    </button>
                                </div>
                            </div>

                            {{-- Error Display for Dynamic Fields --}}
                            <div class="row">
                                <div class="col-md-12">
                                    @error('stsids')<div class="alert alert-danger">{{ $message }}</div>@enderror
                                    @error('stsfares')<div class="alert alert-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Dynamic Table Section --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="stsntostsn-div" class="form-group px-0 py-2 mb-0 rounded border"
                                        style="height:400px; overflow-y:scroll">

                                        <table class="table table-bordered table-striped table-hover" id="main-fare-table">
                                            <thead class="bg-light sticky-top" style="top:0; z-index:10;">
                                                <tr>
                                                    <th class="text-left" width="60%">Station to Station</th>
                                                    <th class="text-left" width="30%">Fare</th>
                                                    <th class="text-center" width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sts-fare-rows">
                                                {{-- Rows will be added here via jQuery --}}
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> {{-- End Row --}}
                </div> {{-- End Card Body --}}

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary px-5">Submit</button>
                    <a class="btn btn-secondary" href="{{ route('admin.fares.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    {{-- Script Section --}}
    <script>
        $(document).ready(function () {

            // ======================
            // 1️⃣ Route Selection Change
            // ======================
            $('#route_id').on('change', function () {
                const routeId = $(this).val();
                const stsSelect = $('#station_to_station');
                const stsLoading = $('#sts_loading');
                const stsAddBtn = $('#add-sts-fare');
                const fareTableBody = $('#sts-fare-rows');

                // Reset UI
                fareTableBody.empty();
                stsSelect.empty().append('<option value="">Loading...</option>').prop('disabled', true);
                stsAddBtn.prop('disabled', true);

                if (!routeId) {
                    stsSelect.empty().append('<option value="">Select Route First...</option>');
                    return;
                }

                stsLoading.show();

                // ✅ URL Generation Fixed
                // আমরা সরাসরি ব্লেড থেকে রাউট জেনারেট করছি এবং ':id' কে রিপ্লেস করছি
                let baseUrl = "{{ route('admin.fares.getStationPairs', ':id') }}";
                let finalUrl = baseUrl.replace(':id', routeId);

                console.log("Requesting URL:", finalUrl); // কনসোল চেক করুন

                $.ajax({
                    url: finalUrl,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        console.log("Data Received:", data);

                        const pairs = data.pairs ?? [];
                        stsSelect.empty().append('<option value="">Select Station to Station...</option>');

                        if (pairs.length > 0) {
                            $.each(pairs, function (index, pair) {
                                stsSelect.append(`<option value="${pair.value}">${pair.text}</option>`);
                            });
                            // ✅ ডাটা পেলে এনাবল হবে
                            stsSelect.prop('disabled', false);
                            stsAddBtn.prop('disabled', false);
                        } else {
                            // ⚠️ ডাটা না পেলেও ড্রপডাউন এনাবল হবে যাতে ইউজার মেসেজ দেখে
                            stsSelect.append('<option value="">No Stations Found</option>');
                            stsSelect.prop('disabled', true);
                        }

                        stsLoading.hide();
                    },
                    error: function (xhr) {
                        // ❌ যদি সার্ভার এরর দেয় (404/500)
                        console.error('AJAX Error:', xhr);
                        alert('Error: ' + xhr.status + ' ' + xhr.statusText + '\nCheck Console for details.');

                        stsSelect.empty().append('<option value="">Server Error</option>');
                        stsLoading.hide();
                    }
                });
            });

            // ======================
            // 2️⃣ Add Button Click
            // ======================
            $('#add-sts-fare').on('click', function () {
                const selectedPair = $('#station_to_station').val();
                const selectedText = $('#station_to_station option:selected').text();

                if (!selectedPair) return;

                const rowId = 'stsdiv_' + selectedPair.replace(/-/g, '_');

                if ($('#' + rowId).length > 0) {
                    alert('Already added!');
                    return;
                }

                const newRow = `
                    <tr id="${rowId}">
                        <td style="vertical-align: middle;">
                            ${selectedText}
                            <input type="hidden" name="stsids[]" value="${selectedPair}">
                        </td>
                        <td>
                            <input type="number" step="0.01" class="form-control form-control-sm" 
                                   placeholder="0.00" name="stsfares[]" required>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger del-sts-fare">
                                <i class="fa fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `;

                $('#sts-fare-rows').append(newRow);
            });

            // Delete Action
            $(document).on('click', '.del-sts-fare', function () {
                $(this).closest('tr').remove();
            });

            // Auto Trigger on Edit
            if ($('#route_id').val()) {
                $('#route_id').trigger('change');
            }
        });
    </script>
@endpush