@extends('layouts.master')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    {{-- Subheader / Page Title --}}
    <div class="subheader subheader-solid" id="kt_subheader" style="background: #d3f9ee;">
        <div class=" container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap container-fluid px-0">
                <h4 class="text-success font-weight-bold my-2 mr-5">Staff Manager</h4>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Edit Staff: {{ $staff->name ?? 'Record' }}</h3>
                </div>

                <form method="POST" action="{{ route('admin.staffs.update', $staff->id) }}" id="staff-edit-form">
                    @csrf 
                    @method('PUT') 

                    <div class="card-body">
                        <div class="row">
                            {{-- LEFT COLUMN --}}
                            <div class="col-md-6">
                                
                                {{-- Name Input --}}
                                <div class="form-group @error('name') has-error @enderror">
                                    <label for="name" class="required"><i class="far fa-star text-danger fa-sm"></i> Name</label>
                                    <input type="text" id="name" class="form-control" placeholder="Enter name" name="name" 
                                        value="{{ old('name', $staff->name) }}" required>
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Alternative Contact Person --}}
                                <div class="form-group @error('alternative_contact_person') has-error @enderror">
                                    <label for="alternative_contact_person">Alternative Contact Person</label>
                                    <input type="text" id="alternative_contact_person" class="form-control" placeholder="Contact Person Full Name" name="alternative_contact_person" 
                                        value="{{ old('alternative_contact_person', $staff->alternative_contact_person) }}">
                                    @error('alternative_contact_person') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Father Name --}}
                                <div class="form-group @error('father_name') has-error @enderror">
                                    <label for="father_name">Father Name</label>
                                    <input type="text" id="father_name" class="form-control" placeholder="Enter father's name" name="father_name" 
                                        value="{{ old('father_name', $staff->father_name) }}">
                                    @error('father_name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Date of Birth --}}
                                <div class="form-group @error('date_of_birth') has-error @enderror">
                                    <label for="date_of_birth">Date of Birth</label>
                                    <input type="text" id="date_of_birth" class="form-control datepicker" name="date_of_birth" 
                                        value="{{ old('date_of_birth', $staff->date_of_birth) }}" readonly>
                                    @error('date_of_birth') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Blood Group Dropdown --}}
                                <div class="form-group @error('blood_group') has-error @enderror">
                                    <label for="blood_group">Blood Group</label>
                                    <select id="blood_group" class="form-control selectpicker" name="blood_group" data-live-search="true"> 
                                        <option value="">Select Blood Group...</option>
                                        @php $groups = ['a+', 'a-', 'b+', 'b-', 'ab+', 'ab-', 'o+', 'o-']; @endphp
                                        @foreach($groups as $group)
                                            <option value="{{ $group }}" {{ old('blood_group', $staff->blood_group) == $group ? 'selected' : '' }}>{{ strtoupper($group) }}</option>
                                        @endforeach
                                    </select>
                                    @error('blood_group') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- National ID --}}
                                <div class="form-group @error('national_id') has-error @enderror">
                                    <label for="national_id">National ID</label>
                                    <input type="text" id="national_id" class="form-control" placeholder="Enter national id" name="national_id" 
                                        value="{{ old('national_id', $staff->national_id) }}">
                                    @error('national_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Present Address --}}
                                <div class="form-group @error('present_address') has-error @enderror">
                                    <label for="present_address">Present Address</label>
                                    <textarea id="present_address" class="form-control" name="present_address">{{ old('present_address', $staff->present_address) }}</textarea>
                                    @error('present_address') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Staff Designation Dropdown --}}
                                <div class="form-group @error('staff_designation_id') has-error @enderror">
                                    <label for="staff_designation_id" class="required"><i class="far fa-star text-danger fa-sm"></i> Staff Designation</label>
                                    <select id="staff_designation_id" class="form-control selectpicker" name="staff_designation_id" required data-live-search="true"> 
                                        <option value="">Select Designation...</option>
                                        @foreach($designations as $designation)
                                            <option value="{{ $designation->id }}" {{ old('staff_designation_id', $staff->staff_designation_id) == $designation->id ? 'selected' : '' }}>
                                                {{ $designation->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('staff_designation_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Joining Date --}}
                                <div class="form-group @error('joining_date') has-error @enderror">
                                    <label for="joining_date">Joining Date</label>
                                    <input type="text" id="joining_date" class="form-control datepicker" name="joining_date" 
                                        value="{{ old('joining_date', $staff->joining_date) }}" readonly>
                                    @error('joining_date') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- RIGHT COLUMN --}}
                            <div class="col-md-6">
                                
                                {{-- Mobile Number --}}
                                <div class="form-group @error('mobile_number') has-error @enderror">
                                    <label for="mobile_number" class="required"><i class="far fa-star text-danger fa-sm"></i> Mobile Number</label>
                                    <input type="text" id="mobile_number" class="form-control" placeholder="Enter mobile number" name="mobile_number" 
                                        value="{{ old('mobile_number', $staff->mobile_number) }}" required>
                                    @error('mobile_number') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                {{-- Alternative Mobile Number --}}
                                <div class="form-group @error('alternative_mobile_number') has-error @enderror">
                                    <label for="alternative_mobile_number">Alternative Mobile Number</label>
                                    <input type="text" id="alternative_mobile_number" class="form-control" placeholder="Alternative Mobile Number" name="alternative_mobile_number" 
                                        value="{{ old('alternative_mobile_number', $staff->alternative_mobile_number) }}">
                                    @error('alternative_mobile_number') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                {{-- Mother Name --}}
                                <div class="form-group @error('mother_name') has-error @enderror">
                                    <label for="mother_name">Mother Name</label>
                                    <input type="text" id="mother_name" class="form-control" placeholder="Enter mother's name" name="mother_name" 
                                        value="{{ old('mother_name', $staff->mother_name) }}">
                                    @error('mother_name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                {{-- Gender Dropdown (From image) --}}
                                <div class="form-group @error('gender') has-error @enderror">
                                    <label for="gender" class="required"><i class="far fa-star text-danger fa-sm"></i> Gender</label>
                                    <select id="gender" class="form-control selectpicker" name="gender" required data-live-search="true">
                                        <option value="">Select Gender...</option>
                                        @php $g = old('gender', $staff->gender); @endphp
                                        <option value="male" {{ $g == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ $g == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('gender') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                {{-- Religion Dropdown (From image) --}}
                                <div class="form-group @error('religion') has-error @enderror">
                                    <label for="religion" class="required"><i class="far fa-star text-danger fa-sm"></i> Religion</label>
                                    <select id="religion" class="form-control selectpicker" name="religion" required data-live-search="true">
                                        <option value="">Select Religion...</option>
                                        @php $r = old('religion', $staff->religion); @endphp
                                        @foreach(['buddhism', 'christianity', 'hinduism', 'islam', 'other'] as $religion)
                                            <option value="{{ $religion }}" {{ $r == $religion ? 'selected' : '' }}>{{ ucfirst($religion) }}</option>
                                        @endforeach
                                    </select>
                                    @error('religion') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                {{-- Driving License Number --}}
                                <div class="form-group @error('driving_license_number') has-error @enderror">
                                    <label for="driving_license_number">Driving License Number</label>
                                    <input type="text" id="driving_license_number" class="form-control" placeholder="Enter driving license number" name="driving_license_number" 
                                        value="{{ old('driving_license_number', $staff->driving_license_number) }}">
                                    @error('driving_license_number') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                {{-- Permanent Address --}}
                                <div class="form-group @error('permanent_address') has-error @enderror">
                                    <label for="permanent_address">Permanent Address</label>
                                    <textarea id="permanent_address" class="form-control" name="permanent_address">{{ old('permanent_address', $staff->permanent_address) }}</textarea>
                                    @error('permanent_address') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                {{-- Job Type Dropdown --}}
                                <div class="form-group @error('job_type') has-error @enderror">
                                    <label for="job_type" class="required"><i class="far fa-star text-danger fa-sm"></i> Job Type</label>
                                    <select id="job_type" class="form-control selectpicker" name="job_type" required data-live-search="true">
                                        <option value="">Select Job Type...</option>
                                        @php $jt = old('job_type', $staff->job_type); @endphp
                                        @foreach(['permanent', 'temporary', 'other'] as $job_type)
                                            <option value="{{ $job_type }}" {{ $jt == $job_type ? 'selected' : '' }}>{{ ucfirst($job_type) }}</option>
                                        @endforeach
                                    </select>
                                    @error('job_type') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                {{-- Status Dropdown --}}
                                <div class="form-group @error('status') has-error @enderror">
                                    <label for="status" class="required"><i class="far fa-star text-danger fa-sm"></i> Status</label>
                                    <select id="status" class="form-control selectpicker" data-live-search="true" name="status" required>
                                        <option value="">Select Status...</option>
                                        @php $s = old('status', $staff->status); @endphp
                                        @foreach(['active', 'inactive', 'suspend'] as $status_option)
                                            <option value="{{ $status_option }}" {{ $s == $status_option ? 'selected' : '' }}>{{ ucfirst($status_option) }}</option>
                                        @endforeach
                                    </select>
                                    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Card Footer --}}
                    <div class="card-footer">
                        <button type="submit" class="btn btn-pill btn-primary">Update</button>
                        <button type="reset" class="btn btn-pill btn-warning">Reset</button>
                        <a class="btn btn-pill btn-secondary" href="{{ route('admin.staffs.index') }}">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->
<script>
        $(document).ready(function () {
            // These calls will now find the functions because the files are loaded above.
            $('.selectpicker').selectpicker(); 
            // etc...
        });
    </script>
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