@extends('layouts.master')

@section('title', 'System Settings')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        {{-- Subheader --}}
        <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                    <h4 class="text-success font-weight-bold my-2 mr-5">
                        User Manager
                        <small></small>
                    </h4>
                </div>
            </div>
        </div>

        {{-- Custom JS functions (Retained for completeness) --}}
        <script type="text/javascript">
            function moveToPreviousDate() {
                document.getElementById('date_prev_next').value = 'prev';
                document.getElementById('datefilterCP').submit();
            }
            function moveToNextDate() {
                document.getElementById('date_prev_next').value = 'next';
                document.getElementById('datefilterCP').submit();
            }
            function moveToPreviousDateDashboard() {
                document.getElementById('date_prev_next').value = 'prev';
                document.getElementById('datefilter').submit();
            }
            function moveToNextDateDashboard() {
                document.getElementById('date_prev_next').value = 'next';
                document.getElementById('datefilter').submit();
            }
        </script>

        {{-- Main Content --}}
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="card card-custom">
                    <div class="card-header">
                        <h3 class="card-title">
                            Create New User
                        </h3>
                    </div>
                    {{-- Form: Ensure action is correct (using route helper is better) --}}
                    <form method="POST" action="{{ route('admin.users.store') }}">
    @csrf
    {{-- Removed redundant <input type="hidden" name="_token"> since @csrf is used --}}

    <div class="card-body">
        <div class="row">

            {{-- === LEFT COLUMN: User Details, Status, and Counter === --}}
            <div class="col-md-6">

                {{-- Name --}}
                <div class="form-group">
                    <label for="name" class="required">
                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Name
                    </label>
                    <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter name"
                        name="name" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Mobile Number --}}
                <div class="form-group">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="text" id="mobile_number" class="form-control @error('mobile_number') is-invalid @enderror"
                        placeholder="Enter mobile number" name="mobile_number" value="{{ old('mobile_number') }}">
                    @error('mobile_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Username --}}
                <div class="form-group">
                    <label for="username" class="required">
                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Username
                    </label>
                    <input type="text" id="username" class="form-control @error('username') is-invalid @enderror" placeholder="Enter username"
                        name="username" value="{{ old('username') }}" required>
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label for="password" class="required">
                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Password
                    </label>
                    <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
                        placeholder="Enter password" name="password" required autocomplete="off">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Confirmed Password --}}
                <div class="form-group">
                    <label for="password_confirmation" class="required">
                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Confirmed Password
                    </label>
                    <input type="password" id="password_confirmation" class="form-control"
                        placeholder="Enter password again" name="password_confirmation" required autocomplete="off">
                </div>

                {{-- Status Dropdown (FIXED SPACING mb-5 and using old() helper) --}}
                <div class="form-group mb-5">
                    <label for="status" class="required">
                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Status
                    </label>
                    <select id="status" class="form-control selectpicker @error('status') is-invalid @enderror" name="status" required data-size="10" data-live-search="true">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="blocked" {{ old('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Counter Dropdown (FIXED Structure - assuming $counters is available) --}}
                <div class="form-group">
                    <label for="counter_id">Counter</label>
                    <select id="counter_id" class="form-control selectpicker @error('counter_id') is-invalid @enderror" name="counter_id" data-live-search="true">
                        <option value="">Select Counter...</option>
                        
                    </select>
                    @error('counter_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- === RIGHT COLUMN: Roles and Permissions (Spatie) === --}}
            <div class="col-md-6">

                {{-- Role Multi-Select (Dynamic) --}}
                <div class="form-group">
                    <label for="roles[]" class="required">
                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Role
                    </label>
                    <select id="roles[]" class="form-control @error('roles') is-invalid @enderror" name="roles[]" required multiple="multiple">
                        {{-- ⭐ Dynamic Role Options ⭐ --}}
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" 
                                    {{ in_array($role->name, old('roles', [])) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('roles')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Permissions Checkboxes (Dynamic and Corrected for Spatie Name) --}}
                <div class="form-group">
                    <label class="required">
                        <i class="far fa-star text-danger fa-sm" title="Required"></i> Permission
                    </label>
                    <div class="checkbox-list">
                        {{-- ⭐ Dynamic Permission Checkboxes ⭐ --}}
                        @foreach($permissions as $permission)
                            <label class="checkbox checkbox-outline checkbox-success" for="permissions[]-{{ $permission->id }}">
                                <input type="checkbox" id="permissions[]-{{ $permission->id }}" name="permissions[]" 
                                       value="{{ $permission->name }}"
                                       {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                <span></span>{{ $permission->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Footer --}}
    <div class="card-footer">
        <button type="submit" class="btn btn-pill btn-primary">Submit</button>
        <button type="reset" class="btn btn-pill btn-warning">Reset</button>
        <a class="btn btn-pill btn-secondary" href="{{ route('admin.users.index') }}">Cancel</a>
    </div>
</form>
                    {{-- end::Form --}}
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

    <script>
        $(document).ready(function () {
            // This function will now work because the library (Step 3) is loaded.
            $('.selectpicker').selectpicker();
        });
    </script>
@endsection