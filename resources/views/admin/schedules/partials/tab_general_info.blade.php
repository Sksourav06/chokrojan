<div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
    <form method="POST" action="{{ route('admin.schedules.update', ['schedule' => $schedule->id]) }}">
        @csrf
        @method('PUT')

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h6 class="text-success border-bottom pb-2 mb-5">General Information</h6>
                    <div class="form-group @error('route_tagline') has-error @enderror"> <label
                            for="route_tagline">Route Tag Line</label> <input type="text" id="route_tagline"
                            class="form-control" name="route_tagline"
                            value="{{ old('route_tagline', $schedule->route_tagline) }}"> </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group @error('coach_number') has-error @enderror"> <label for="coach_number"
                            class="required">Coach Number</label> <input type="text" id="coach_number"
                            class="form-control" name="coach_number" value="{{ old('coach_number', $schedule->name) }}"
                            required> </div>
                    <div class="form-group @error('route_id') has-error @enderror"> <label for="route_id"
                            class="required">Route</label>
                        @php $currentRoute = old('route_id', $schedule->route_id); @endphp <select id="route_id"
                            class="form-control " name="route_id" required data-live-search="true">
                            <option value="">Select Route...</option> @foreach($routes as $id => $name) <option
                                value="{{ $id }}" {{ $currentRoute == $id ? 'selected' : '' }}> {{ $name }} </option>
                            @endforeach
                        </select> </div>
                    <div class="row">
                        <div class="col-md-4 col-8">
                            <div class="form-group"><label for="start_time" class="required">Start Time</label> <input
                                    type="text" id="start_time" class="form-control" name="start_time"
                                    value="{{ old('start_time', \Carbon\Carbon::parse($schedule->start_time)->format('h:i A')) }}"
                                    required> </div>
                        </div>
                        <div class="col-md-2 col-4">
                            <div class="form-group mt-10"><label class="checkbox checkbox-outline checkbox-success"
                                    for="start_time_nextday"> <input type="checkbox" id="start_time_nextday"
                                        name="start_time_nextday" value="1" {{ old('start_time_nextday', $schedule->start_time_nextday) ? 'checked' : '' }}><span></span>Nextday </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group @error('start_station_id') has-error @enderror"> <label
                                    for="start_station_id" class="required">Start Station</label>
                                @php $currentStartStation = old('start_station_id', $schedule->start_station_id); @endphp
                                <select id="start_station_id" class="form-control " name="start_station_id" required
                                    data-live-search="true">
                                    <option value="">Select Station...</option> @foreach($allStations as $id => $name)
                                    <option value="{{ $id }}" class="station-option" {{ $currentStartStation == $id ? 'selected' : '' }}>{{ $name }} </option> @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-6">
                            <div class="form-group @error('end_time') has-error @enderror"><label for="end_time"
                                    class="required">End Time</label> <input type="text" id="end_time"
                                    class="form-control" name="end_time"
                                    value="{{ old('end_time', \Carbon\Carbon::parse($schedule->end_time)->format('h:i A')) }}"
                                    required> </div>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="form-group @error('end_station_id') has-error @enderror"> <label
                                    for="end_station_id" class="required">End Station</label>
                                @php $currentEndStation = old('end_station_id', $schedule->end_station_id); @endphp
                                <select id="end_station_id" class="form-control " name="end_station_id" required
                                    data-live-search="true">
                                    <option value="">Select Station...</option> @foreach($allStations as $id => $name)
                                    <option value="{{ $id }}" class="station-option" {{ $currentEndStation == $id ? 'selected' : '' }}>{{ $name }} </option> @endforeach
                                </select>
                            </div>
                        </div>
                    </div> {{-- Bus, Seat Plan, Bus Type, Status Dropdowns --}} <div
                        class="form-group @error('bus_id') has-error @enderror"><label for="bus_id">Bus</label>
                        @php $currentBusId = old('bus_id', $schedule->bus_id); @endphp <select id="bus_id"
                            class="form-control " name="bus_id" data-live-search="true">
                            <option value="">Select Bus...</option> @foreach($buses as $id => $name) <option
                                value="{{ $id }}" {{ $currentBusId == $id ? 'selected' : '' }}> {{ $name }} </option>
                            @endforeach
                        </select> </div>
                    <div class="form-group @error('seat_layout_id') has-error @enderror"><label for="seat_layout_id"
                            class="required">Seat Plan</label>
                        @php $currentLayoutId = old('seat_layout_id', $schedule->seat_layout_id); @endphp <select
                            id="seat_layout_id" class="form-control " name="seat_layout_id" required
                            data-live-search="true">
                            <option value="">Select Seat Plan...</option> @foreach($seatLayouts as $id => $name) <option
                                value="{{ $id }}" {{ $currentLayoutId == $id ? 'selected' : '' }}> {{ $name }} </option>
                            @endforeach
                        </select> </div>
                    <div class="form-group @error('bus_type') has-error @enderror"><label for="bus_type"
                            class="required">Bus Type</label>
                        @php $currentBusType = old('bus_type', $schedule->bus_type); @endphp <select id="bus_type"
                            class="form-control " name="bus_type" required>
                            <option value="">Select Bus Type...</option> @foreach($busTypes as $id => $name) <option
                                value="{{ $name }}" {{ $currentBusType == $name ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select> </div>
                    <div class="form-group @error('status') has-error @enderror"><label for="status"
                            class="required">Status</label>
                        @php $currentStatus = old('status', $schedule->status); @endphp <select id="status"
                            class="form-control " name="status" required>
                            <option value="">Select Status...</option> @foreach($availableStatuses as $statusOption)
                                <option value="{{ $statusOption }}" {{ $currentStatus == $statusOption ? 'selected' : '' }}>
                                    {{ ucfirst($statusOption) }}
                            </option> @endforeach
                        </select> </div>
                </div> {{-- === RIGHT SIDE (Seat Map Preview) === --}} <div class="col-md-6">
                    <h4 class="card-title">Seat Map Preview</h4>
                    <div id="seatplan-div" class="col-md-12 py-3 mb-5 border min-h-350px">
                        <p class="text-muted text-center">Select Seat Plan and Bus Type to see preview.</p>
                    </div> <input type="hidden" id="hidden_seats" name="hidden_seats" value="{{ old('hidden_seats') }}">
                </div>
            </div>
        </div>
        <div class="card-footer"> <button type="submit" class="btn btn-pill btn-primary">Update Schedule</button> <a
                class="btn btn-pill btn-secondary" href="{{ route('admin.schedules.index') }}">Cancel</a> </div>
    </form>
</div>