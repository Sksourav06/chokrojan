@extends('layouts.master')

@section('title', 'Edit Route')

@section('content')
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Editing Route: {{ $route->name ?? 'N/A' }}</h3>
                </div>
                
                <form method="POST" action="{{ route('admin.routes.update', $route->id) }}" id="route_edit_form">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="row">
                            
                            {{-- === LEFT COLUMN: Route Details === --}}
                            <div class="col-md-6">
                                
                                {{-- Route Name --}}
                                <div class="form-group @error('name') has-error @enderror">
                                    <label for="name" class="required">Route Name</label>
                                    <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           placeholder="Enter route name" name="name" 
                                           value="{{ old('name', $route->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Zone Selection (Foreign Key) --}}
                                <div class="form-group @error('zone_id') has-error @enderror">
                                    <label for="zone_id" class="required">Zone</label>
                                    @php $currentZoneId = old('zone_id', $route->zone_id); @endphp
                                    <select id="zone_id" autocomplete="off" class="form-control  @error('zone_id') is-invalid @enderror" 
                                            name="zone_id" required data-size="10" data-live-search="true">
                                        <option value="">Select Zone...</option>
                                        @foreach($zones as $id => $name)
                                            <option value="{{ $id }}" {{ $currentZoneId == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('zone_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                {{-- Status --}}
                                <div class="form-group @error('status') has-error @enderror">
                                    <label for="status" class="required">Status</label>
                                    @php $currentStatus = old('status', $route->status); @endphp
                                    <select id="status" autocomplete="off" class="form-control selectpicker  @error('status') is-invalid @enderror" 
                                            name="status" required>
                                        @foreach($availableStatuses as $statusOption)
                                            <option value="{{ $statusOption }}" {{ $currentStatus == $statusOption ? 'selected' : '' }}>
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
                                            
                                            @php
                                                // Load stations ordered by the pivot table sequence_order (as set in controller's edit method)
                                                $orderedStations = $route->stations;
                                                $stationCount = $orderedStations->count();
                                                $allStationsArray = $allStations->pluck('name', 'id')->toArray();
                                            @endphp

                                            @foreach ($orderedStations as $index => $station)
                                                @php
                                                    $isStart = ($index === 0);
                                                    $isEnd = ($index === $stationCount - 1);
                                                    $markerIcon = $isStart ? 'fas fa-map-marker-alt text-success' : ($isEnd ? 'fas fa-map-marker-alt text-danger' : 'fas fa-map-marker-alt text-info');
                                                    $stationLabel = $isStart ? '**Start Station**' : ($isEnd ? '**End Station**' : 'Via Station');
                                                    
                                                    // Get the required time from the pivot table
                                                    $timeValue = $station->pivot->required_time ?? '00:00'; 
                                                @endphp

                                                {{-- Station Item --}}
                                                <div class="timeline-item pb-3 dynamic-station" 
                                                     data-station-type="{{ $isStart ? 'start' : ($isEnd ? 'end' : 'via') }}">
                                                    <div class="timeline-media">
                                                        <i class="{{ $markerIcon }}"></i>
                                                    </div>
                                                    <div class="timeline-content px-3 py-2 {{ $isEnd || $isStart ? '' : 'position-relative' }}">
                                                        <div class="row">
                                                            
                                                            {{-- Required Time Input --}}
                                                            <div class="form-group col-sm-5 mb-0 @error('required_times.'.$index) has-error @enderror">
                                                                <label><i class="far fa-star text-danger fa-sm"></i> Required Time (HH:MM)</label>
                                                                <input type="text" 
                                                                       class="form-control form-control-sm @if(!$isStart) timepicker @endif @error('required_times.'.$index) is-invalid @enderror" 
                                                                       name="required_times[]" 
                                                                       value="{{ old('required_times.'.$index, $timeValue) }}" 
                                                                       placeholder="01:30" 
                                                                       {{ $isStart ? 'readonly="readonly"' : 'required' }}>
                                                                @error('required_times.'.$index)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                            </div>
                                                            
                                                            {{-- Station Selection --}}
                                                            <div class="form-group col-sm-7 mb-0 @error('station_ids.'.$index) is-invalid @enderror">
                                                                <label><i class="far fa-star text-danger fa-sm"></i> {{ $stationLabel }}</label>
                                                                <select class="form-control form-control-sm station-select timepicker" name="station_ids[]" required>
                                                                    <option value="">Select Station...</option>
                                                                    @foreach($allStationsArray as $id => $name)
                                                                        <option value="{{ $id }}" {{ $station->id == $id ? 'selected' : '' }}>
                                                                            {{ $name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('station_ids.'.$index)<div class="invalid-feedback d-block ml-3">{{ $message }}</div>@enderror
                                                            </div>
                                                        </div>
                                                        
                                                        {{-- Remove button for Via Stations only --}}
                                                        @if (!$isStart && !$isEnd)
                                                            <i onclick="removeStation(this);" title="Remove" class="fa fa-fw fa-times text-danger cursor-pointer rounded border border-danger position-absolute" style="top: 8px; right: 8px;"></i>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Add PLUS button marker after non-end stations --}}
                                                @if (!$isEnd)
                                                    <div class="timeline-item pb-20 add-station-marker" data-index="{{ $index }}">
                                                        <div class="timeline-media cursor-pointer" data-toggle="tooltip" title="Add Via Station" onclick="addViaStation(this);">
                                                            <i class="fas fa-plus text-info"></i>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-pill btn-success">Update Route</button>
                        <a class="btn btn-pill btn-secondary" href="{{ route('admin.routes.index') }}">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
    // Note: $allStations is passed from the controller and must be accessible here.
    const availableStations = @json($allStations->pluck('name', 'id')->toArray());
    
    function initializeSelectPickers() {
        if (typeof $('.selectpicker').selectpicker === 'function') {
             $('.selectpicker').selectpicker('refresh'); 
        }
        initializeTimepickers();
    }

    function initializeTimepickers() {
        // Target dynamic time inputs that haven't been initialized yet
        $('.timepicker-input-dynamic').timepicker({
            minuteStep: 1, 
            showSeconds: false,
            showMeridian: false,
            defaultTime: false,
            template: false
        }).removeClass('timepicker-input-dynamic').addClass('timepicker-initialized'); 
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
        
        // Insert the new station element right BEFORE the clicked element
        element.closest('.add-station-marker').insertAdjacentHTML('beforebegin', template);
        
        initializeSelectPickers();
    }

    function removeStation(element) {
        const stationItem = element.closest('.timeline-item');
        if (stationItem) {
            stationItem.remove();
        }
        // Removing an element might change the sequence, so refresh pickers.
        initializeSelectPickers();
    }
    
    document.addEventListener('DOMContentLoaded', initializeSelectPickers);
</script>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Selectpicker (Dropdowns)
            $('.selectpicker').selectpicker();

            // Initialize Datepicker
            $('.datepicker').datepicker();

            $('.timepicker').timepicker();

            // Form Submission UI feedback
            
        });
    </script>
@endpush