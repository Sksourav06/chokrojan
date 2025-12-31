@extends('layouts.master')

@section('title', 'Create New Station')

@section('content')
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Create New Station</h3>
                </div>
                
                <form method="POST" action="{{ route('admin.stations.store') }}">
                    @csrf
                    
                    <div class="card-body">
                        <div class="row">
                            
                            {{-- Station Name --}}
                            <div class="col-md-6">
                                <div class="form-group @error('name') has-error @enderror">
                                    <label for="name" class="required">
                                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Name
                                    </label>
                                    <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           placeholder="Enter Station Name (e.g., Dhaka)" name="name" value="{{ old('name') }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">
                                <div class="form-group @error('status') has-error @enderror">
                                    <label for="status" class="required">
                                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Status
                                    </label>
                                    <select id="status" class="form-control  @error('status') is-invalid @enderror" 
                                            name="status" required>
                                        @foreach($availableStatuses as $status)
                                            <option value="{{ $status }}" {{ old('status', 'active') == $status ? 'selected' : '' }}>
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
                        <a class="btn btn-pill btn-secondary" href="{{ route('admin.stations.index') }}">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
{{-- Assuming selectpicker initialization script is loaded here --}}
@endpush