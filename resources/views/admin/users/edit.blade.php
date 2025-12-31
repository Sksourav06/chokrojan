@extends('layouts.master')

@section('title', 'Edit User: ' . $user->name)

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    {{-- Subheader --}}
    <div class="subheader subheader-solid" style="background:#d3f9ee;">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <h4 class="text-success font-weight-bold my-2">
                User Manager
                <small>Edit: {{ $user->username }}</small>
            </h4>
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">

            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Edit User Details</h3>
                </div>

                {{-- Update Form --}}
                <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row">

                            {{-- LEFT COLUMN --}}
                            <div class="col-md-6">

                                {{-- Name --}}
                                <div class="form-group">
                                    <label class="required">Name</label>
                                    <input type="text" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $user->name) }}"
                                           required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Mobile Number --}}
                                <div class="form-group">
                                    <label>Mobile Number</label>
                                    <input type="text" name="mobile_number"
                                           class="form-control @error('mobile_number') is-invalid @enderror"
                                           value="{{ old('mobile_number', $user->mobile_number) }}">
                                    @error('mobile_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Username --}}
                                <div class="form-group">
                                    <label class="required">Username</label>
                                    <input type="text" name="username"
                                           class="form-control @error('username') is-invalid @enderror"
                                           value="{{ old('username', $user->username) }}"
                                           required>
                                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Password --}}
                                <div class="form-group">
                                    <label>Password (Leave blank to keep the same)</label>
                                    <input type="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror">
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Password Confirmation --}}
                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input type="password" name="password_confirmation"
                                           class="form-control">
                                </div>

                                {{-- Status --}}
                                <div class="form-group">
                                    <label class="required">Status</label>
                                    @php $status = old('status', $user->status); @endphp
                                    <select class="form-control @error('status') is-invalid @enderror"
                                            name="status" required>
                                        <option value="active"   {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="blocked"  {{ $status == 'blocked' ? 'selected' : '' }}>Blocked</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Counter --}}
                                <div class="form-group">
    <label for="counter_id">Counter</label>
    <select id="counter_id" class="form-control selectpicker" name="counter_id" data-live-search="true" title="Select Counter...">
        @foreach($counters as $counter)
            <option value="{{ $counter->id }}" {{ old('counter_id', $user->counter_id) == $counter->id ? 'selected' : '' }}>
                {{ $counter->name }}
            </option>
        @endforeach
    </select>
</div>


                            </div>

                            {{-- RIGHT COLUMN --}}
                            <div class="col-md-6">

                                {{-- Roles --}}
                                <div class="form-group">
                                    <label class="required">Role</label>
                                    @php $selectedRoles = old('roles', $userRoles); @endphp
                                    <select name="roles[]" multiple
                                            class="form-control @error('roles') is-invalid @enderror"
                                            required>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ in_array($role->name, $selectedRoles) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('roles') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Permissions --}}
                                <div class="form-group">
                                    <label class="required">Permissions</label>
                                    @php $selectedPermissions = old('permissions', $userPermissions); @endphp

                                    <div class="checkbox-list">

                                        @foreach($permissions as $permission)
                                            <label class="checkbox checkbox-outline checkbox-success">
                                                <input type="checkbox"
                                                       name="permissions[]"
                                                       value="{{ $permission->name }}"
                                                       {{ in_array($permission->name, $selectedPermissions) ? 'checked' : '' }}>
                                                <span></span>{{ $permission->name }}
                                            </label>
                                        @endforeach

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-pill">Update</button>
                        <a href="{{ route('admin.users.index') }}"
                           class="btn btn-secondary btn-pill">
                            Cancel
                        </a>
                    </div>

                </form>

            </div>

        </div>
    </div>

</div>

@endsection
