@extends('layouts.master')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        {{-- Subheader (Staff Manager Title) --}}
        <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                    <h4 class="text-success font-weight-bold my-2 mr-5">Staff Manager</h4>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="card card-custom">
                    <div class="card-header">
                        <h3 class="card-title">Create New Staff</h3>
                    </div>

                    {{-- 🚨 CRITICAL FIX 1: Add the FORM tag with proper action and method --}}
                    {{-- Assuming the route is 'staffs.store' for creation --}}
                    <form method="POST" action="{{ route('admin.staffs.store') }}" id="staff-form">
                        @csrf

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    {{-- Name Input --}}
                                    <div class="form-group @error('name') has-error @enderror">
                                        <label for="name" class="required">
                                            <i class="far fa-star text-danger fa-sm"></i> Name
                                        </label>
                                        <input type="text" id="name" class="form-control" placeholder="Enter name"
                                            name="name" value="{{ $staff->name ?? old('name') ?? '' }}" required>
                                        {{-- 🚨 FIX 2: Error Message Display --}}
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- ... (Other inputs follow the same pattern for error handling) ... --}}

                                    {{-- Father Name --}}
                                    <div class="form-group @error('father_name') has-error @enderror">
                                        <label for="father_name">Father Name</label>
                                        <input type="text" id="father_name" class="form-control"
                                            placeholder="Enter father's name" name="father_name"
                                            value="{{ $staff->father_name ?? old('father_name') ?? '' }}">
                                        @error('father_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Date of Birth --}}
                                    <div class="form-group @error('date_of_birth') has-error @enderror">
                                        <label for="date_of_birth">Date of Birth</label>
                                        <input type="text" id="date_of_birth" class="form-control datepicker"
                                            name="date_of_birth"
                                            value="{{ $staff->date_of_birth ?? old('date_of_birth') ?? '' }}" readonly>
                                        @error('date_of_birth')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Blood Group Dropdown --}}
                                    <div class="form-group @error('blood_group')  has-error @enderror">
                                        <label for="blood_group">Blood Group</label>
                                        <select id="blood_group" class="form-control selectpicker " data-live-search="true" name="blood_group" >
                                            <option value="">Select Blood Group...</option>
                                            @php $current_bg = $staff->blood_group ?? old('blood_group');
                                            $groups = ['a+', 'a-', 'b+', 'b-', 'ab+', 'ab-', 'o+', 'o-']; @endphp
                                            @foreach($groups as $group)
                                                <option value="{{ $group }}" {{ $current_bg == $group ? 'selected' : '' }}>
                                                    {{ strtoupper($group) }}</option>
                                            @endforeach
                                        </select>
                                        @error('blood_group')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- National ID --}}
                                    <div class="form-group @error('national_id') has-error @enderror">
                                        <label for="national_id">National ID</label>
                                        <input type="text" id="national_id" class="form-control"
                                            placeholder="Enter national id" name="national_id"
                                            value="{{ $staff->national_id ?? old('national_id') ?? '' }}">
                                        @error('national_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Present Address --}}
                                    <div class="form-group @error('present_address') has-error @enderror">
                                        <label for="present_address">Present Address</label>
                                        <textarea id="present_address" class="form-control"
                                            name="present_address">{{ $staff->present_address ?? old('present_address') ?? '' }}</textarea>
                                        @error('present_address')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Staff Designation Dropdown --}}
                                    <div class="form-group @error('staff_designation_id') has-error @enderror">
                                        <label for="staff_designation_id" class="required">Staff Designation</label>
                                        <select id="staff_designation_id" class="form-control selectpicker " data-live-search="true"
                                            name="staff_designation_id" required>
                                            @php $current_designation = $staff->staff_designation_id ?? old('staff_designation_id'); @endphp
                                            @foreach($designations as $designation)
                                                <option value="{{ $designation->id }}" {{ $current_designation == $designation->id ? 'selected' : '' }}>{{ $designation->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('staff_designation_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Joining Date --}}
                                    <div class="form-group @error('joining_date') has-error @enderror">
                                        <label for="joining_date">Joining Date</label>
                                        <input type="text" id="joining_date" class="form-control datepicker"
                                            name="joining_date"
                                            value="{{ $staff->joining_date ?? old('joining_date') ?? '' }}" readonly>
                                        @error('joining_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Right Column (Mobile Number, Mother Name, Gender, etc.) --}}
                                <div class="col-md-6">
                                    {{-- Mobile Number --}}
                                    <div class="form-group @error('mobile_number') has-error @enderror">
                                        <label for="mobile_number" class="required">Mobile Number</label>
                                        <input type="text" id="mobile_number" class="form-control"
                                            placeholder="Enter mobile number" name="mobile_number"
                                            value="{{ $staff->mobile_number ?? old('mobile_number') ?? '' }}" required>
                                        @error('mobile_number')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Alternative Mobile Number --}}
                                    <div class="form-group @error('alternative_mobile_number') has-error @enderror">
                                        <label for="alternative_mobile_number">Alternative Mobile Number</label>
                                        <input type="text" id="alternative_mobile_number" class="form-control"
                                            placeholder="Alternative Mobile Number" name="alternative_mobile_number"
                                            value="{{ $staff->alternative_mobile_number ?? old('alternative_mobile_number') ?? '' }}">
                                        @error('alternative_mobile_number')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Mother Name --}}
                                    <div class="form-group @error('mother_name') has-error @enderror">
                                        <label for="mother_name">Mother Name</label>
                                        <input type="text" id="mother_name" class="form-control"
                                            placeholder="Enter mother's name" name="mother_name"
                                            value="{{ $staff->mother_name ?? old('mother_name') ?? '' }}">
                                        @error('mother_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Gender Dropdown --}}
                                    <div class="form-group @error('gender') has-error @enderror">
                                        <label for="gender" class="required">Gender</label>
                                        <select id="gender" class="form-control selectpicker " data-live-search="true" name="gender" required>
                                            <option value="">Select Gender...</option>
                                            @php $current_gender = $staff->gender ?? old('gender'); @endphp
                                            <option value="male" {{ $current_gender == 'male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="female" {{ $current_gender == 'female' ? 'selected' : '' }}>Female
                                            </option>
                                        </select>
                                        @error('gender')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Religion Dropdown --}}
                                    <div class="form-group @error('religion') has-error @enderror">
                                        <label for="religion" class="required">Religion</label>
                                        <select id="religion" class="form-control selectpicker " data-live-search="true" name="religion" required>
                                            <option value="">Select Religion...</option>
                                            @php $current_religion = $staff->religion ?? old('religion'); @endphp
                                            @foreach(['buddhism', 'christianity', 'hinduism', 'islam', 'other'] as $r)
                                                <option value="{{ $r }}" {{ $current_religion == $r ? 'selected' : '' }}>
                                                    {{ ucfirst($r) }}</option>
                                            @endforeach
                                        </select>
                                        @error('religion')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Driving License Number --}}
                                    <div class="form-group @error('driving_license_number') has-error @enderror">
                                        <label for="driving_license_number">Driving License Number</label>
                                        <input type="text" id="driving_license_number" class="form-control"
                                            placeholder="Enter driving license number" name="driving_license_number"
                                            value="{{ $staff->driving_license_number ?? old('driving_license_number') ?? '' }}">
                                        @error('driving_license_number')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Permanent Address --}}
                                    <div class="form-group @error('permanent_address') has-error @enderror">
                                        <label for="permanent_address">Permanent Address</label>
                                        <textarea id="permanent_address" class="form-control"
                                            name="permanent_address">{{ $staff->permanent_address ?? old('permanent_address') ?? '' }}</textarea>
                                        @error('permanent_address')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Job Type Dropdown --}}
                                    <div class="form-group @error('job_type') has-error @enderror">
                                        <label for="job_type" class="required">Job Type</label>
                                        <select id="job_type" class="form-control selectpicker " data-live-search="true" name="job_type" required>
                                            @php $current_job_type = $staff->job_type ?? old('job_type'); @endphp
                                            @foreach(['permanent', 'temporary', 'other'] as $jt)
                                                <option value="{{ $jt }}" {{ $current_job_type == $jt ? 'selected' : '' }}>
                                                    {{ ucfirst($jt) }}</option>
                                            @endforeach
                                        </select>
                                        @error('job_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Status Dropdown --}}
                                    <div class="form-group @error('status') has-error @enderror">
                                        <label for="status" class="required">Status</label>
                                        <select id="status" class="form-control selectpicker " data-live-search="true" name="status" required>
                                            @php $current_status = $staff->status ?? old('status'); @endphp
                                            @foreach(['active', 'inactive', 'suspend'] as $s)
                                                <option value="{{ $s }}" {{ $current_status == $s ? 'selected' : '' }}>
                                                    {{ ucfirst($s) }}</option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card Footer --}}
                        <div class="card-footer">
                            <button type="submit" class="btn btn-pill btn-primary">Submit</button>
                            <button type="reset" class="btn btn-pill btn-warning">Reset</button>
                            {{-- Assuming the route for Cancel button is 'staffs.index' --}}
                            <a class="btn btn-pill btn-secondary" href="{{ route('admin.staffs.index') }}">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Selectpicker (Dropdowns)
            $('.selectpicker').selectpicker();

            // Initialize Datepicker
            $('.datepicker').datepicker();

            // Form Submission UI feedback
            
        });
    </script>
@endpush