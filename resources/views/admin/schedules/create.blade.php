@extends('layouts.master')

@section('title', 'Create New Schedule')

@section('content')
    <div class="container-fluid">
        <div class="card card-custom">
            <div class="card-header">
                <h3 class="card-title">Create New Schedule</h3>
            </div>
            
            <form method="POST" action="{{ route('admin.schedules.store') }}">
                @csrf
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-md-12">
                            <div class="form-group @error('route_tagline') has-error @enderror">
                                <label for="route_tagline">Route Tag Line</label>
                                <input type="text" id="route_tagline" class="form-control @error('route_tagline') is-invalid @enderror" 
                                       placeholder="Enter route tag line" name="route_tagline" value="{{ old('route_tagline') }}">
                                @error('route_tagline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- === LEFT SIDE (Bus, Route, Time) === --}}
                        <div class="col-md-6">
                            
                            <div class="form-group @error('coach_number') has-error @enderror">
                                <label for="coach_number" class="required">Coach Number</label>
                                <input type="text" id="coach_number" class="form-control @error('coach_number') is-invalid @enderror" 
                                       placeholder="Enter coach number" name="coach_number" value="{{ old('coach_number') }}" required>
                                @error('coach_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Route Dropdown (The Main Trigger) --}}
                            <div class="form-group @error('route_id') has-error @enderror">
                                <label for="route_id" class="required">Route</label>
                                <select id="route_id" class="form-control  @error('route_id') is-invalid @enderror" 
                                        name="route_id" required data-size="10" data-live-search="true">
                                    <option value="">Select Route...</option>
                                    @foreach($routes as $id => $name)
                                        <option value="{{ $id }}" {{ old('route_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('route_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="row">
                                {{-- Start Time --}}
                                <div class="col-md-4 col-8">
                                    <div class="form-group @error('start_time') has-error @enderror">
                                        <label for="start_time" class="required">Start Time</label>
                                        <input type="time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" 
                                               placeholder="HH:MM" name="start_time" value="{{ old('start_time') }}" required>
                                        @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                {{-- Nextday Checkbox --}}
                                <div class="col-md-2 col-4">
                                    <div class="form-group mt-10">
                                        <label class="checkbox checkbox-outline checkbox-success" for="start_time_nextday">
                                            <input type="checkbox" id="start_time_nextday" class="form-control" name="start_time_nextday" value="1" {{ old('start_time_nextday') ? 'checked' : '' }}>
                                            <span></span>Nextday
                                        </label>
                                    </div>
                                </div>
                                
                                {{-- Start Station --}}
                                <div class="col-md-6">
                                    <div class="form-group @error('start_station_id') has-error @enderror">
                                        <label for="start_station_id" class="required">Start Station</label>
                                        {{-- This will be filtered by JS based on route_id --}}
                                        <select id="start_station_id" class="form-control  @error('start_station_id') is-invalid @enderror" 
                                                name="start_station_id" required data-live-search="true" >
                                            <option value="">Select Route First...</option>
                                            @foreach($allStations as $id => $name)
                                                <option value="{{ $id }}" class="station-option" style="display: none;">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('start_station_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- End Time --}}
                                <div class="col-md-6 col-6">
                                    <div class="form-group @error('end_time') has-error @enderror">
                                        <label for="end_time" class="required">End Time</label>
                                        <input type="time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" 
                                               placeholder="HH:MM" name="end_time" value="{{ old('end_time') }}" required>
                                        @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                
                                {{-- End Station --}}
                                <div class="col-md-6 col-6">
                                    <div class="form-group @error('end_station_id') has-error @enderror">
                                        <label for="end_station_id" class="required">End Station</label>
                                        {{-- This will be filtered by JS based on route_id --}}
                                        <select id="end_station_id" class="form-control  @error('end_station_id') is-invalid @enderror" 
                                                name="end_station_id" required data-live-search="true" disabled>
                                            <option value="">Select Route First...</option>
                                             @foreach($allStations as $id => $name)
                                                <option value="{{ $id }}" class="station-option" style="display: none;">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('end_station_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Bus Selection --}}
                            <div class="form-group @error('bus_id') has-error @enderror">
                                <label for="bus_id">Bus</label>
                                <select id="bus_id" class="form-control  @error('bus_id') is-invalid @enderror" 
                                        name="bus_id" data-size="10" data-live-search="true">
                                    <option value="">Select Bus...</option>
                                    @foreach($buses as $id => $name)
                                        <option value="{{ $id }}" {{ old('bus_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('bus_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            {{-- Seat Plan --}}
                            <div class="form-group @error('seat_layout_id') has-error @enderror">
                                <label for="seat_layout_id" class="required">Seat Plan</label>
                                <select id="seat_layout_id" class="form-control  @error('seat_layout_id') is-invalid @enderror" 
                                        name="seat_layout_id" required data-size="10" data-live-search="true">
                                    <option value="">Select Seat Plan...</option>
                                    @foreach($seatLayouts as $id => $name)
                                        <option value="{{ $id }}" {{ old('seat_layout_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('seat_layout_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                             {{-- Bus Type --}}
                            <div class="form-group @error('bus_type') has-error @enderror">
                                <label for="bus_type" class="required">Bus Type</label>
                                <select id="bus_type" class="form-control  @error('bus_type') is-invalid @enderror" 
                                        name="bus_type" required>
                                    <option value="">Select Bus Type...</option>
                                    @foreach($busTypes as $id => $name)
                                        <option value="{{ $name }}" {{ old('bus_type') == $name ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('bus_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                             {{-- Status --}}
                            <div class="form-group @error('status') has-error @enderror">
                                <label for="status" class="required">Status</label>
                                <select id="status" class="form-control  @error('status') is-invalid @enderror" 
                                        name="status" required>
                                    <option value="">Select Status...</option>
                                    @foreach($availableStatuses as $statusOption)
                                        <option value="{{ $statusOption }}" {{ old('status', 'active') == $statusOption ? 'selected' : '' }}>{{ ucfirst($statusOption) }}</option>
                                    @endforeach
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                        </div>
                        
                        {{-- === RIGHT SIDE (Seat Map Preview) === --}}
                        <div class="col-md-6">
                            <h4 class="card-title">Seat Map Preview</h4>
                            <div id="seatplan-div" class="col-md-12 py-3 mb-5 border min-h-350px">
                                <p class="text-muted text-center">Select Seat Plan and Bus Type to see preview.</p>
                                {{-- Seat map content will be loaded here via AJAX --}}
                            </div>
                            
                            {{-- Hidden field to store complex data if needed --}}
                            <input type="hidden" id="hidden_seats" name="hidden_seats" value="{{ old('hidden_seats') }}">

                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-pill btn-primary">Submit</button>
                    <button type="reset" class="btn btn-pill btn-warning">Reset</button>
                    <a class="btn btn-pill btn-secondary" href="{{ route('admin.schedules.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
  <script>
document.addEventListener("DOMContentLoaded", function () {
    const routeSelect = document.getElementById("route_id");
    const startSelect = document.getElementById("start_station_id");
    const endSelect = document.getElementById("end_station_id");

    // 1. Store old selected IDs for persistence after validation error
    const oldStartId = "{{ old('start_station_id') }}";
    const oldEndId = "{{ old('end_station_id') }}";

    // --- 1. Handle Route Change to Load Stations ---
    const loadStations = function () {
        const routeId = routeSelect.value;
        
        // Reset and disable before fetching
        startSelect.disabled = true;
        endSelect.disabled = true;
        startSelect.innerHTML = '<option value="">Loading stations...</option>';
        endSelect.innerHTML = '<option value="">Loading stations...</option>';

        if (!routeId) {
            startSelect.innerHTML = '<option value="">Select Route First...</option>';
            endSelect.innerHTML = '<option value="">Select Route First...</option>';
            return;
        }

        fetch(`/admin/routes/${routeId}/stations`)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                // Clear dropdowns for new options
                startSelect.innerHTML = '<option value="">Select Start Station</option>';
                endSelect.innerHTML = '<option value="">Select End Station</option>';

                if (data.stations && data.stations.length > 0) {
                    data.stations.forEach(station => {
                        // Create option element for Start Station
                        let startOption = document.createElement('option');
                        startOption.value = station.id;
                        startOption.textContent = station.name;
                        // 3. Check and set selected for old value
                        if (oldStartId && String(station.id) === oldStartId) {
                            startOption.selected = true;
                        }
                        startSelect.appendChild(startOption);

                        // Create option element for End Station
                        let endOption = document.createElement('option');
                        endOption.value = station.id;
                        endOption.textContent = station.name;
                        // 3. Check and set selected for old value
                        if (oldEndId && String(station.id) === oldEndId) {
                            endOption.selected = true;
                        }
                        endSelect.appendChild(endOption);
                    });

                    startSelect.disabled = false;
                    endSelect.disabled = false;
                } else {
                    startSelect.innerHTML = '<option value="">No stations found</option>';
                    endSelect.innerHTML = '<option value="">No stations found</option>';
                }
                
                // Re-initialize any custom select libraries (like bootstrap-select) if you are using them
                // Example: $('.form-control').selectpicker('refresh'); 
            })
            .catch(err => {
                console.error("Station loading error:", err);
                startSelect.innerHTML = '<option value="">Error loading stations</option>';
                endSelect.innerHTML = '<option value="">Error loading stations</option>';
            });
    };

    routeSelect.addEventListener("change", loadStations);

    // --- 2. Handle Seat Layout/Bus Type Changes (Kept original logic) ---
    $('#seat_layout_id, #bus_type').on('change', function() {
        const layoutId = $('#seat_layout_id').val();
        const busType = $('#bus_type').val(); 
        const previewDiv = $('#seatplan-div');

        if (!layoutId || !busType) {
            previewDiv.html('<p class="text-muted text-center">Select Seat Plan and Bus Type to see preview.</p>');
            return;
        }

        previewDiv.html('<p class="text-info text-center"><i class="fas fa-spinner fa-spin"></i> Loading Seat Map...</p>');

        const url = `/admin/schedules/seat-map/${layoutId}/${busType}`;

        $.get(url, function(res) {
            previewDiv.html(res.html);
        }).fail(function() {
            previewDiv.html('<p class="text-danger text-center">Failed to load seat map.</p>');
        });
    });

    // --- 4. Trigger initial load if form has old values (essential for validation errors) ---
    if (routeSelect.value) {
        loadStations(); // Use the dedicated function for initial load
    }
    
    // Trigger Seat Map load if values exist (original logic)
    if ($('#seat_layout_id').val() && $('#bus_type').val()) $('#seat_layout_id').trigger('change');

});

</script>


@endsection

