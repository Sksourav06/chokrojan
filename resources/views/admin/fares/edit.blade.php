@extends('layouts.master')

@section('title', 'Edit Fare Rule: ' . $fare->name)

@section('content')
    <div class="container-fluid">
        <div class="card card-custom">
            <div class="card-header">
                <h3 class="card-title">Edit Fare Rule: {{ $fare->name }}</h3>
            </div>

            <form method="POST" action="{{ route('admin.fares.update', $fare->id) }}">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="row">
                        {{-- LEFT COLUMN --}}
                        <div class="col-md-6">
                            {{-- Fare Name --}}
                            <div class="form-group @error('name') has-error @enderror">
                                <label for="name" class="required">Fare Name</label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $fare->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Route --}}
                            <div class="form-group @error('route_id') has-error @enderror">
                                <label for="route_id" class="required">Route</label>
                                <select id="route_id" name="route_id"
                                    class="form-control @error('route_id') is-invalid @enderror" required>
                                    <option value="">Select Route...</option>
                                    @foreach($routes as $route)
                                        <option value="{{ $route->id }}" {{ old('route_id', $fare->route_id) == $route->id ? 'selected' : '' }}>
                                            {{ $route->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('route_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Seat Plan and Bus Type --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group @error('seat_layout_id') has-error @enderror">
                                        <label for="seat_layout_id" class="required">Seat Plan</label>
                                        <select id="seat_layout_id" name="seat_layout_id"
                                            class="form-control @error('seat_layout_id') is-invalid @enderror" required>
                                            <option value="">Select Seat Plan...</option>
                                            @foreach($seatLayouts as $layout)
                                                <option value="{{ $layout->id }}" {{ old('seat_layout_id', $fare->seat_layout_id) == $layout->id ? 'selected' : '' }}>
                                                    {{ $layout->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('seat_layout_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group @error('vehicle_type_id') has-error @enderror">
                                        <label for="vehicle_type_id" class="required">Bus Type</label>
                                        <select id="vehicle_type_id" name="vehicle_type_id"
                                            class="form-control @error('vehicle_type_id') is-invalid @enderror" required>
                                            <option value="">Select Bus Type...</option>
                                            @foreach($busTypes as $id => $name)
                                                <option value="{{ $id }}" {{ old('vehicle_type_id', $vehicleTypeId) == $id ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vehicle_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            {{-- From / To Date --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="from_date">From Date</label>
                                        <input type="date" id="from_date" name="from_date" class="form-control"
                                            value="{{ old('from_date', $fare->start_date) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="to_date">To Date</label>
                                        <input type="date" id="to_date" name="to_date" class="form-control"
                                            value="{{ old('to_date', $fare->end_date) }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="form-group">
                                <label for="status" class="required">Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    @foreach($availableStatuses as $status)
                                        <option value="{{ $status }}" {{ old('status', $fare->status) == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN --}}
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
                                    <button type="button" class="btn btn-block btn-outline-primary mt-8" id="add-sts-fare"
                                        disabled>
                                        <i class="fas fa-plus pr-0"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <div id="stsntostsn-div" class="form-group col-md-12 px-3 py-6 mb-0 rounded border"
                                        style="height:296px; overflow-y:scroll">
                                        <table class="table table-bordered table-striped">
                                            <tbody id="sts-fare-table">
                                                <tr>
                                                    <th class="text-left" width="30%">Station to Station</th>
                                                    <th class="text-left" width="60%">Fare</th>
                                                    <th class="text-center" width="10%">Action</th>
                                                </tr>
                                                @foreach($stationPrices as $farePrice)
                                                    <tr
                                                        id="stsdiv_{{ $farePrice->origin_station_id }}_{{ $farePrice->destination_station_id }}">
                                                        <td>{{ $farePrice->origin->name }} ⟹ {{ $farePrice->destination->name }}
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01"
                                                                class="form-control form-control-sm" name="stsfares[]"
                                                                value="{{ old('stsfares.' . $loop->index, $farePrice->price) }}"
                                                                required>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="hidden" name="stsids[]"
                                                                value="{{ $farePrice->origin_station_id }}-{{ $farePrice->destination_station_id }}">
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger del-sts-fare">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <span id="initial_fare_data" data-fares="{{ json_encode($stationPrices) }}"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-pill btn-primary">Update</button>
                    <a class="btn btn-pill btn-secondary" href="{{ route('admin.fares.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    {{-- --------------------------- --}}
    {{-- JS SCRIPT --}}
    {{-- --------------------------- --}}
    <span id="initial_fare_data" data-fares='@json($stationPrices)'></span>

<script>
$(document).ready(function () {
    // ---------------------------
    // FETCH EXISTING FARES DATA
    // ---------------------------
    const fareTable = $('#sts-fare-table');
    const stsSelect = $('#station_to_station');
    const stsAddBtn = $('#add-sts-fare');
    const stsLoading = $('#sts_loading');
    const routeSelect = $('#route_id');

    // Parse JSON from data-fares
    const stationPricesDataRaw = $('#initial_fare_data').attr('data-fares');
    const stationPricesData = stationPricesDataRaw ? JSON.parse(stationPricesDataRaw) : [];

    // FORCE ENABLE
    stsSelect.prop("disabled", false);
    stsAddBtn.prop("disabled", false);

    // ---------------------------
    // ROW BUILDER
    // ---------------------------
    function createFareRow(pair, text, amount = '') {
        return `
        <tr id="stsdiv_${pair.replace('-', '_')}">
            <td>${text}</td>
            <td>
                <input type="number" step="0.01" class="form-control form-control-sm" name="stsfares[]" value="${amount}" required>
            </td>
            <td class="text-center">
                <input type="hidden" name="stsids[]" value="${pair}">
                <button type="button" class="btn btn-sm btn-outline-danger del-sts-fare">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
        `;
    }

    // ---------------------------
    // LOAD EXISTING FARES
    // ---------------------------
    function loadExistingFares() {
        if (!stationPricesData.length) return;

        stationPricesData.forEach(fareData => {
            const pairKey = `${fareData.origin_station_id}-${fareData.destination_station_id}`;
            const stationText = `${fareData.origin.name} ⟹ ${fareData.destination.name}`;
            fareTable.append(createFareRow(pairKey, stationText, fareData.price));
        });
    }
    loadExistingFares();

    // ---------------------------
    // LOAD STATION PAIRS
    // ---------------------------
    function loadStationPairs() {
        const routeId = routeSelect.val();

        stsSelect.prop("disabled", true).html('<option>Loading...</option>');
        stsAddBtn.prop("disabled", true);
        stsLoading.show();

        fareTable.find("tr:gt(0)").remove();

        if (!routeId) {
            stsSelect.html('<option>Select Route First...</option>').prop("disabled", true);
            stsLoading.hide();
            return;
        }

        const url = `{{ url('admin/fares/get-station-pairs') }}/${routeId}`;

        $.getJSON(url, function(data) {
            const pairs = data.pairs ?? [];
            stsSelect.empty().append('<option value="">Select Station to Station...</option>');

            pairs.forEach(pair => {
                stsSelect.append(`<option value="${pair.value}">${pair.text}</option>`);
            });

            stsSelect.prop("disabled", false);
            stsAddBtn.prop("disabled", false);
            stsLoading.hide();

            // আবার existing fares add করবো যেগুলি এই route এর সাথে match করে
            stationPricesData.forEach(fareData => {
                const pairKey = `${fareData.origin_station_id}-${fareData.destination_station_id}`;
                if (pairs.find(p => p.value === pairKey)) {
                    const stationText = `${fareData.origin.name} ⟹ ${fareData.destination.name}`;
                    fareTable.append(createFareRow(pairKey, stationText, fareData.price));
                }
            });
        }).fail(function(xhr) {
            console.error(xhr.responseText);
            stsSelect.html('<option>Error loading pairs</option>').prop("disabled", true);
            stsAddBtn.prop("disabled", true);
            stsLoading.hide();
        });
    }

    // AUTO LOAD
    if (routeSelect.val()) loadStationPairs();
    routeSelect.on("change", loadStationPairs);

    // ADD NEW ROW
    stsAddBtn.on('click', function () {
        const selected = stsSelect.find(":selected");
        const value = selected.val();
        const text = selected.text();

        if (!value) { alert("Please select a Station → Station pair."); return; }
        if ($('#stsdiv_' + value.replace('-', '_')).length > 0) { alert(text + " already added."); return; }

        fareTable.append(createFareRow(value, text));
    });

    // DELETE ROW
    $(document).on('click', '.del-sts-fare', function () {
        $(this).closest('tr').remove();
    });
});
</script>

@endsection