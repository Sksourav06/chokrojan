@extends('layouts.master')

@section('title', 'Create New Route')

@section('content')
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Create New Route</h3>
                </div>
                
                {{-- Form action points to the store route --}}
                <form method="POST" action="{{ route('admin.routes.store') }}" id="route_create_form">
                    @csrf
                    
                    <div class="card-body">
                        <div class="row">
                            
                            {{-- === LEFT COLUMN: Route Details === --}}
                            <div class="col-md-6">
                                
                                {{-- Route Name --}}
                                <div class="form-group @error('name') has-error @enderror">
                                    <label for="name" class="required">
                                        <i class="far fa-star text-danger fa-sm"></i> Route Name
                                    </label>
                                    <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           placeholder="Enter route name (e.g., Dhaka - Chittagong)" name="name" 
                                           value="{{ old('name') }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Zone Selection (Foreign Key) --}}
                                <div class="form-group @error('zone_id') has-error @enderror">
                                    <label for="zone_id" class="required">
                                        <i class="far fa-star text-danger fa-sm"></i> Zone
                                    </label>
                                    <select id="zone_id" autocomplete="off" class="form-control  @error('zone_id') is-invalid @enderror" 
                                            name="zone_id" required data-size="10" data-live-search="true">
                                        <option value="">Select Zone...</option>
                                        @foreach($zones as $id => $name)
                                            <option value="{{ $id }}" {{ old('zone_id') == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('zone_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                {{-- Status --}}
                                <div class="form-group @error('status') has-error @enderror">
                                    <label for="status" class="required">
                                        <i class="far fa-star text-danger fa-sm"></i> Status
                                    </label>
                                    <select id="status" autocomplete="off" class="form-control  @error('status') is-invalid @enderror" 
                                            name="status" required>
                                        @foreach($availableStatuses as $statusOption)
                                            <option value="{{ $statusOption }}" {{ old('status', 'active') == $statusOption ? 'selected' : '' }}>
                                                {{ ucfirst($statusOption) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                            </div>

                            {{-- === RIGHT COLUMN: Route Stations (Dynamic Section) === --}}
                            <div class="col-md-6">
                                <h4 class="mb-2">Route Stations (Sequence)</h4>
                                <div id="route_stations_div" class="rounded border p-3">
                                    <div class="timeline timeline-3">
                                        <div class="timeline-items" id="station_timeline">
                                            
                                            {{-- ⭐ 1. START STATION (Index 0) ⭐ --}}
                                            <div class="timeline-item pb-3" data-station-type="start">
                                                <div class="timeline-media">
                                                    <i class="fas fa-map-marker-alt text-success"></i>
                                                </div>
                                                <div class="timeline-content px-3 py-2">
                                                    <div class="row">
                                                        <div class="form-group col-sm-5 mb-0">
                                                            <label>Required Time (HH:MM)</label>
                                                            <input type="text" readonly="readonly" class="form-control form-control-sm" name="required_times[]" value="{{ old('required_times.0', '0:00') }}">
                                                        </div>
                                                        <div class="form-group col-sm-7 mb-0 @error('station_ids.0') is-invalid @enderror">
                                                            <label><i class="far fa-star text-danger fa-sm"></i> **Start Station**</label>
                                                            <select class="form-control form-control-sm station-select" name="station_ids[]" required>
                                                                <option value="" selected="selected">Select Station...</option>
                                                                @foreach($allStations as $station)
                                                                    <option value="{{ $station->id }}" {{ old('station_ids.0') == $station->id ? 'selected' : '' }}>
                                                                        {{ $station->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('station_ids.0')<div class="invalid-feedback d-block ml-3">{{ $message }}</div>@enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- ⭐ 2. PLUS BUTTON / ADD VIA STATION MARKER ⭐ --}}
                                            <div class="timeline-item pb-20 add-station-marker">
                                                <div class="timeline-media cursor-pointer" data-toggle="tooltip" title="Add Via Station" onclick="addViaStation(this);">
                                                    <i class="fas fa-plus text-info"></i>
                                                </div>
                                            </div>

                                            {{-- ⭐ 3. END STATION (Final Station, Index 1 if no via stations added) ⭐ --}}
                                            <div class="timeline-item pb-3" data-station-type="end">
                                                <div class="timeline-media">
                                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                                </div>
                                                <div class="timeline-content px-3 py-2">
                                                    <div class="row">
                                                        {{-- Time for End Station --}}
                                                        <div class="form-group col-sm-5 mb-0 @error('required_times.1') has-error @enderror"> 
                                                            <label>
                                                                <i class="far fa-star text-danger fa-sm" title="Required"></i> Required Time (HH:MM)
                                                            </label>
                                                            <input type="text" 
                                                                   class="form-control form-control-sm @error('required_times.1') is-invalid @enderror timepicker-input-dynamic" 
                                                                   name="required_times[]" 
                                                                   value="{{ old('required_times.1') }}" 
                                                                   placeholder="01:30" 
                                                                   required>
                                                            @error('required_times.1')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        {{-- End Station Selection --}}
                                                        <div class="form-group col-sm-7 mb-0 @error('station_ids.1') is-invalid @enderror">
    <label><i class="far fa-star text-danger fa-sm"></i> **End Station**</label>
    <select class="form-control form-control-sm station-select" name="station_ids[]" required>
        <option value="" selected="selected">Select Station...</option>
        @foreach($allStations as $station)
            <option value="{{ $station->id }}" {{ old('station_ids.1') == $station->id ? 'selected' : '' }}>
                {{ $station->name }}
            </option>
        @endforeach
    </select>
    @error('station_ids.1')<div class="invalid-feedback d-block ml-3">{{ $message }}</div>@enderror
</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-pill btn-primary">Submit</button>
                        <button type="reset" class="btn btn-pill btn-warning">Reset</button>
                        <a class="btn btn-pill btn-secondary" href="{{ route('admin.routes.index') }}">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
    // Note: $allStations is passed from the controller and must be accessible here.
    const availableStations = @json($allStations->pluck('name', 'id')->toArray());
    
    /**
     * Initializes all Bootstrap Select pickers and timepicker inputs on the page.
     */
    function initializeSelectPickers() {
        if (typeof $('.selectpicker').selectpicker === 'function') {
            $('.selectpicker').selectpicker('refresh'); 
        }
        initializeTimepickers();
    }
    
    /**
     * Initializes the Timepicker widget for relevant inputs.
     */
    function initializeTimepickers() {
        $('.timepicker-input-dynamic').each(function() {
            const $input = $(this);
            if ($input.hasClass('timepicker-initialized')) return;

            $input.timepicker({
                minuteStep: 1, 
                showSeconds: false,
                showMeridian: false,
                defaultTime: false,
                template: `
                    <div class="bootstrap-timepicker-widget dropdown-menu">
                        <table>
                            <tbody>
                                <tr>
                                    <td><a href="#" data-action="incrementHour"><span class="ki ki-arrow-up"></span></a></td>
                                    <td class="separator">&nbsp;</td>
                                    <td><a href="#" data-action="incrementMinute"><span class="ki ki-arrow-up"></span></a></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="bootstrap-timepicker-hour" maxlength="2"></td>
                                    <td class="separator">:</td>
                                    <td><input type="text" class="bootstrap-timepicker-minute" maxlength="2"></td>
                                </tr>
                                <tr>
                                    <td><a href="#" data-action="decrementHour"><span class="ki ki-arrow-down"></span></a></td>
                                    <td class="separator"></td>
                                    <td><a href="#" data-action="decrementMinute"><span class="ki ki-arrow-down"></span></a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                `
            });

            $input.removeClass('timepicker-input-dynamic').addClass('timepicker-initialized');
        });
    }

    function addViaStation(element) {
        const stationOptions = Object.keys(availableStations).map(id => 
            `<option value="${id}">${availableStations[id]}</option>`
        ).join('');

        const template = `
            <div class="timeline-item pb-3 dynamic-station">
                <div class="timeline-media">
                    <i class="fas fa-map-marker-alt text-info"></i>
                </div>
                <div class="timeline-content px-3 py-2 position-relative">
                    <div class="row">
                        <div class="form-group col-sm-5 mb-0">
                            <label><i class="far fa-star text-danger fa-sm"></i> Required Time (HH:MM)</label>
                            <input type="text" class="form-control form-control-sm timepicker-input-dynamic" name="required_times[]" required placeholder="00:30">
                        </div>
                        <div class="form-group col-sm-7 mb-0">
                            <label><i class="far fa-star text-danger fa-sm"></i> Via Station</label>
                            <select class="form-control form-control-sm station-select" name="station_ids[]" required>
                                <option value="" selected="selected">Select Station...</option>
                                ${stationOptions}
                            </select>
                        </div>
                    </div>
                    <i onclick="removeStation(this);" title="Remove" class="fa fa-fw fa-times text-danger cursor-pointer rounded border border-danger position-absolute" style="top: 8px; right: 8px;"></i>
                </div>
            </div>
        `;
        
        const endStationItem = document.querySelector('[data-station-type="end"]');
        if (endStationItem) {
            endStationItem.insertAdjacentHTML('beforebegin', template);
        } else {
            document.getElementById('station_timeline').insertAdjacentHTML('beforeend', template);
        }
        initializeSelectPickers();
    }

    function removeStation(element) {
        const stationItem = element.closest('.timeline-item');
        if (stationItem && stationItem.classList.contains('dynamic-station')) {
            stationItem.remove();
        }
        initializeSelectPickers();
    }
    
    // Initialize all selectpickers and timepickers on page load
    document.addEventListener('DOMContentLoaded', initializeSelectPickers);
</script>

@endsection