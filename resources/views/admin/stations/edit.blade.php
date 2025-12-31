@extends('layouts.master')

@section('title', 'Edit Station')

@section('content')
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Editing Station: {{ $station->name ?? 'N/A' }}</h3>
                </div>
                
                {{-- Form: ACTION points to the update route, using PUT method --}}
                <form method="POST" action="{{ route('admin.stations.update', $station->id) }}">
                    @csrf
                    @method('PUT') {{-- CRITICAL: Required for Laravel's Route::resource update method --}}
                    
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        <div class="row">
                            
                            {{-- Station Name --}}
                            <div class="col-md-6">
                                <div class="form-group @error('name') has-error @enderror">
                                    <label for="name" class="required">
                                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Name
                                    </label>
                                    <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           placeholder="Enter Station Name" 
                                           name="name" 
                                           {{-- ⭐ Pre-populate with existing data ⭐ --}}
                                           value="{{ old('name', $station->name) }}" 
                                           required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">
                                <div class="form-group @error('status') has-error @enderror">
                                    <label for="status" class="required">
                                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Status
                                    </label>
                                    @php $currentStatus = old('status', $station->status); @endphp
                                    <select id="status" class="form-control  @error('status') is-invalid @enderror" 
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

                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-pill btn-success">Update Station</button>
                        <a class="btn btn-pill btn-secondary" href="{{ route('admin.stations.index') }}">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
{{-- JavaScript needed to initialize selectpicker (if used for styling) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $('.selectpicker').selectpicker === 'function') {
             $('.selectpicker').selectpicker();
        }
    });
</script>
@endpush