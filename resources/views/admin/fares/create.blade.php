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
                        {{-- === LEFT COLUMN: Main Fare Rule === --}}
                        <div class="col-md-6">

                            {{-- Fare Name --}}
                            <div class="form-group @error('name') has-error @enderror">
                                <label for="name" class="required">Fare Name</label>
                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Enter name" name="name" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Route Dropdown (The Trigger) --}}
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
                                        {{-- 'selectpicker' ক্লাস রিমুভ করা হয়েছে --}}
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
                                        {{-- 'selectpicker' ক্লাস রিমুভ করা হয়েছে --}}
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
                                {{-- 'selectpicker' ক্লাস রিমুভ করা হয়েছে --}}
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

                        {{-- === RIGHT COLUMN: Dynamic Station-to-Station Fares === --}}
                        <div class="col-md-6">
                            <div class="row">
                                <div class="form-group col-md-10">
                                    <label for="station_to_station">
                                        <i class="far fa-star text-danger fa-sm"></i> Station to Station
                                        <span id="sts_loading" style="display: none;"><i
                                                class="fas fa-spinner fa-spin text-info"></i></span>
                                    </label>

                                    {{-- 'selectpicker' ক্লাস রিমুভ করা হয়েছে --}}
                                    <select id="station_to_station" class="form-control" disabled>
                                        <option value="">Select Route First...</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <button type="button" class="btn btn-block btn-outline-primary mt-8" id="add-sts-fare"
                                        disabled>
                                        <i class="fas fa-plus pr-0"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                    @error('stsids')<div class="alert alert-danger">{{ $message }}</div>@enderror
                                    <div id="stsntostsn-div" class="form-group col-md-12 px-3 py-6 mb-0 rounded border"
                                        style="height:296px; overflow-y:scroll">
                                        <table class="table table-bordered table-striped">
                                            <tbody id="sts-fare-table">
                                                <tr>
                                                    <th class="text-left" width="30%">Station to Station</th>
                                                    <th class="text-left" width="60%">Fare</th>
                                                    <th class="text-center" width="10%">Action</th>
                                                </tr>
                                                {{-- Rows will be added here by JavaScript --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-pill btn-primary">Submit</button>
                    <a class="btn btn-pill btn-secondary" href="{{ route('admin.fares.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function () {

            // ======================
            // 1️⃣ Route Selection
            // ======================
            $('#route_id').on('change', function () {
                const routeId = $(this).val();
                const stsSelect = $('#station_to_station');
                const stsLoading = $('#sts_loading');
                const stsAddBtn = $('#add-sts-fare');
                const fareTable = $('#sts-fare-table');

                // Reset before load
                fareTable.find("tr:gt(0)").remove();
                stsSelect.empty().append('<option value="">Loading...</option>').prop('disabled', true);
                stsAddBtn.prop('disabled', true);

                if (!routeId) {
                    stsSelect.empty().append('<option value="">Select Route First...</option>').prop('disabled', true);
                    return;
                }

                stsLoading.show();

                // ✅ correct Laravel route name
                let url = "{{ route('admin.fares.getStationPairs', ['route' => ':id']) }}";
                url = url.replace(':id', routeId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        console.log("✅ AJAX Response:", data);

                        const pairs = data.pairs ?? [];

                        stsSelect.empty().append('<option value="">Select Station to Station...</option>');
                        $.each(pairs, function (index, pair) {
                            stsSelect.append(`<option value="${pair.value}">${pair.text}</option>`);
                        });

                        stsSelect.prop('disabled', false);
                        stsAddBtn.prop('disabled', false);
                        stsLoading.hide();
                    },
                    error: function (xhr) {
                        console.error('❌ AJAX Error:', xhr.status, xhr.responseText);
                        alert('Failed to load station pairs.');
                        stsSelect.empty().append('<option value="">Error loading pairs</option>').prop('disabled', true);
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

                if (!selectedPair) {
                    alert('Please select a Station → Station pair first.');
                    return;
                }

                // prevent duplicate row
                if ($('#stsdiv_' + selectedPair.replace('-', '_')).length > 0) {
                    alert(selectedText + ' already added.');
                    return;
                }

                const newRow = `
                <tr id="stsdiv_${selectedPair.replace('-', '_')}">
                    <td style="text-align:left; vertical-align: middle;">${selectedText}</td>
                    <td class="text-left">
                        <input type="number" step="0.01" class="form-control form-control-sm" 
                               placeholder="Fare Amount" name="stsfares[]" required>
                    </td>
                    <td class="text-center">
                        <input type="hidden" name="stsids[]" value="${selectedPair}">
                        <button type="button" class="btn btn-sm btn-outline-danger px-3 py-2 del-sts-fare">
                            <i class="fa fa-times pr-0"></i>
                        </button>
                    </td>
                </tr>
            `;

                $('#sts-fare-table').append(newRow);
                console.log("✅ Row added for:", selectedText);
            });

            // ======================
            // 3️⃣ Delete Button Click
            // ======================
            $(document).on('click', '.del-sts-fare', function () {
                $(this).closest('tr').remove();
            });

            // ======================
            // 4️⃣ Auto trigger on load (edit mode)
            // ======================
            if ($('#route_id').val()) {
                $('#route_id').trigger('change');
            }

        });
    </script>


@endsection