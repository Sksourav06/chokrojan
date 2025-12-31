@extends('layouts.master')

@section('title', 'Create New Bus')

@section('content')
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Create New Bus</h3>
                </div>

                {{-- Form: ACTION points to the store route --}}
                <form method="POST" action="{{ route('admin.buses.store') }}">
                    @csrf

                    <div class="card-body">
                        <div class="row">

                            {{-- === LEFT COLUMN: Details === --}}
                            <div class="col-md-6">
                                
                                {{-- Registration Number --}}
                                <div class="form-group @error('registration_number') has-error @enderror">
                                    <label for="registration_number" class="required">
                                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Registration Number
                                    </label>
                                    <input type="text" id="registration_number" class="form-control @error('registration_number') is-invalid @enderror" 
                                           placeholder="Enter registration number" name="registration_number" value="{{ old('registration_number') }}" required>
                                    @error('registration_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Making Year (FIXED NAME: make_year) --}}
                                <div class="form-group @error('make_year') has-error @enderror">
                                    <label for="make_year">Making Year</label>
                                    <input type="number" id="make_year" class="form-control @error('make_year') is-invalid @enderror" 
                                           placeholder="Enter making year" name="make_year" value="{{ old('make_year') }}">
                                    @error('make_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Model Name (FIXED NAME: model_name) --}}
                                <div class="form-group @error('model_name') has-error @enderror">
                                    <label for="model_name">Model</label>
                                    <input type="text" id="model_name" class="form-control @error('model_name') is-invalid @enderror" 
                                           placeholder="Enter model" name="model_name" value="{{ old('model_name') }}">
                                    @error('model_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- === RIGHT COLUMN: Type, Layout, Status === --}}
                            <div class="col-md-6">

                                {{-- Vehicle Type (FIXED NAME: bus_type, assuming options are 'AC', 'Non AC') --}}
                                <div class="form-group @error('bus_type') has-error @enderror">
                                    <label for="bus_type" class="required">
                                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Vehicle Type
                                    </label>
                                    {{-- NOTE: Assuming $availableBusTypes contains ['AC', 'Non AC', 'Sleeper'] --}}
                                    <select id="bus_type" class="form-control  @error('bus_type') is-invalid @enderror" 
                                            name="bus_type" required data-size="10" data-live-search="true">
                                        <option value="">Select Vehicle Type</option>
                                        @foreach($availableBusTypes as $type)
                                            <option value="{{ $type }}" {{ old('bus_type') == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bus_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Seat Layout --}}
                                <div class="form-group @error('seat_layout_id') has-error @enderror">
                                    <label for="seat_layout_id" class="required">
                                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Seat Layout
                                    </label>
                                    {{-- NOTE: $seatLayouts should be a key/value array: [id => name] --}}
                                    <select id="seat_layout_id" class="form-control  @error('seat_layout_id') is-invalid @enderror" 
                                            name="seat_layout_id" required data-size="10" data-live-search="true">
                                        <option value="">Select Seat Layout</option>
                                        @foreach($seatLayouts as $id => $name)
                                            <option value="{{ $id }}" {{ old('seat_layout_id') == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('seat_layout_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Status (Used 'status' name which matches controller) --}}
                                <div class="form-group @error('status') has-error @enderror">
                                    <label for="status" class="required">
                                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Status
                                    </label>
                                     {{-- NOTE: Assuming $availableStatuses contains ['running', 'broken', 'retired'] --}}
                                    <select id="status" class="form-control  @error('status') is-invalid @enderror" 
                                            name="status" required data-size="10" data-live-search="true">
                                        @foreach($availableStatuses as $status)
                                            <option value="{{ $status }}" {{ old('status', 'running') == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-pill btn-success">Submit</button>
                        <button type="reset" class="btn btn-pill btn-warning">Reset</button>
                        <a class="btn btn-pill btn-secondary" href="{{ route('admin.buses.index') }}">Cancel</a>
                    </div>
                </form>
                </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Note: The toggleUpperDeck function is no longer needed here as the Bus creation form
    // handles fixed inputs. However, we keep the selectpicker classes for styling.
</script>
@endpush