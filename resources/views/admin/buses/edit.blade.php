@extends('layouts.master')

@section('title', 'Edit Bus')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        {{-- Subheader --}}
        <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                    <h4 class="text-success font-weight-bold my-2 mr-5">
                        Bus Manager
                        <small></small>
                    </h4>
                </div>
            </div>
        </div>
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="card card-custom">
                    <div class="card-header">
                        <h3 class="card-title">Editing Bus: {{ $bus->registration_number }}</h3>
                    </div>

                    {{-- Form: ACTION points to the update route, using PUT method --}}
                    <form method="POST" action="{{ route('admin.buses.update', $bus->id) }}">
                        @csrf
                        @method('PUT') {{-- Required for the RESTful UPDATE method --}}

                        <div class="card-body">
                            <div class="row">

                                {{-- === LEFT COLUMN: Details === --}}
                                <div class="col-md-6">

                                    {{-- Registration Number --}}
                                    <div class="form-group @error('registration_number') has-error @enderror">
                                        <label for="registration_number" class="required">
                                            <i class="far fa-star text-danger fa-sm" title="Required"></i> Registration
                                            Number
                                        </label>
                                        <input type="text" id="registration_number"
                                            class="form-control @error('registration_number') is-invalid @enderror"
                                            name="registration_number"
                                            value="{{ old('registration_number', $bus->registration_number) }}" required>
                                        @error('registration_number')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Make Year --}}
                                    <div class="form-group @error('make_year') has-error @enderror">
                                        <label for="make_year">Making Year</label>
                                        <input type="number" id="make_year"
                                            class="form-control @error('make_year') is-invalid @enderror" name="make_year"
                                            value="{{ old('make_year', $bus->make_year) }}" placeholder="Enter making year">
                                        @error('make_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    {{-- Model Name --}}
                                    <div class="form-group @error('model_name') has-error @enderror">
                                        <label for="model_name">Model</label>
                                        <input type="text" id="model_name"
                                            class="form-control @error('model_name') is-invalid @enderror" name="model_name"
                                            value="{{ old('model_name', $bus->model_name) }}" placeholder="Enter model">
                                        @error('model_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- === RIGHT COLUMN: Type, Layout, Status === --}}
                                <div class="col-md-6">

                                    {{-- Vehicle Type --}}
                                    <div class="form-group @error('bus_type') has-error @enderror">
                                        <label for="bus_type" class="required">
                                            <i class="far fa-star text-danger fa-sm" title="Required"></i> Vehicle Type
                                        </label>
                                        @php $currentBusType = old('bus_type', $bus->bus_type); @endphp
                                        <select id="bus_type" class="form-control  @error('bus_type') is-invalid @enderror"
                                            name="bus_type" required data-size="10" data-live-search="true">
                                            <option value="">Select Vehicle Type</option>
                                            @foreach($availableBusTypes as $type)
                                                <option value="{{ $type }}" {{ $currentBusType == $type ? 'selected' : '' }}>
                                                    {{ $type }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('bus_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    {{-- Seat Layout Assignment --}}
                                    <div class="form-group @error('seat_layout_id') has-error @enderror">
                                        <label for="seat_layout_id" class="required">
                                            <i class="far fa-star text-danger fa-sm" title="Required"></i> Seat Layout
                                        </label>
                                        @php $currentLayoutId = old('seat_layout_id', $bus->seat_layout_id); @endphp
                                        <select id="seat_layout_id"
                                            class="form-control  @error('seat_layout_id') is-invalid @enderror"
                                            name="seat_layout_id" required data-size="10" data-live-search="true">
                                            <option value="">Select Seat Layout</option>
                                            @foreach($seatLayouts as $id => $name)
                                                <option value="{{ $id }}" {{ $currentLayoutId == $id ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('seat_layout_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    {{-- Status --}}
                                    <div class="form-group @error('status') has-error @enderror">
                                        <label for="status" class="required">
                                            <i class="far fa-star text-danger fa-sm" title="Required"></i> Status
                                        </label>
                                        @php $currentStatus = old('status', $bus->status); @endphp
                                        <select id="status" class="form-control  @error('status') is-invalid @enderror"
                                            name="status" required data-size="10" data-live-search="true">
                                            @foreach($availableStatuses as $status)
                                                <option value="{{ $status }}" {{ $currentStatus == $status ? 'selected' : '' }}>
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
                            <button type="submit" class="btn btn-pill btn-success">Update Bus</button>
                            <a class="btn btn-pill btn-secondary" href="{{ route('admin.buses.index') }}">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection