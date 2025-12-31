<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffDesignation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffsController extends Controller
{

    public function index()
    {
        $staffs = Staff::orderBy('name')->get();

        return view('admin.staffs.index', compact('staffs')); // আপনার ভিউ পাথ অনুযায়ী পরিবর্তন করুন
    }
    public function create()
    {
        // Load necessary lookup data for dropdowns
        $designations = StaffDesignation::all();

        return view('admin.staffs.create', compact('designations'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff)
    {
        $designations = StaffDesignation::all();

        // Convert dates to the format expected by the input fields (d-m-Y)
        if ($staff->date_of_birth) {
            $staff->date_of_birth = Carbon::parse($staff->date_of_birth)->format('d-m-Y');
        }
        if ($staff->joining_date) {
            $staff->joining_date = Carbon::parse($staff->joining_date)->format('d-m-Y');
        }

        return view('admin.staffs.edit', compact('staff', 'designations'));
    }

    public function store(Request $request)
    {
        // 1. Validation (CRITICAL STEP)
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'mobile_number' => ['required', 'string', 'max:15', 'unique:staffs,mobile_number'], // Must be unique
            'alternative_contact_person' => ['nullable', 'string', 'max:100'],
            'alternative_mobile_number' => ['nullable', 'string', 'max:15'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'mother_name' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date_format:d-m-Y'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'religion' => ['required', 'string', 'max:20'],
            'national_id' => ['nullable', 'string', 'max:20', 'unique:staffs,national_id'], // Must be unique
            'driving_license_number' => ['nullable', 'string', 'max:50'],
            'present_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],
            'staff_designation_id' => ['required', 'integer', 'exists:staff_designations,id'], // Must exist in designation table
            'job_type' => ['required', Rule::in(['permanent', 'temporary', 'other'])],
            'joining_date' => ['nullable', 'date_format:d-m-Y'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspend'])],
        ]);

        // 2. Prepare Data for Database (Format Dates)
        if (isset($validatedData['date_of_birth'])) {
            $validatedData['date_of_birth'] = Carbon::createFromFormat('d-m-Y', $validatedData['date_of_birth']);
        }
        if (isset($validatedData['joining_date'])) {
            $validatedData['joining_date'] = Carbon::createFromFormat('d-m-Y', $validatedData['joining_date']);
        }

        // 3. Create the Staff Record
        try {
            Staff::create($validatedData);

            // 4. Redirect with Success Message
            return redirect()->route('admin.staffs.index')->with('success', 'Staff member created successfully.');

        } catch (\Exception $e) {
            // Handle database or other errors
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to create staff member.']);
        }
    }

    public function update(Request $request, Staff $staff)
    {
        // 1. Validation (CRITICAL: Excluding current record's mobile_number and national_id)
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:100'],

            // 🚨 FIX 1: UNIQUE validation exclusion (Allows staff member to keep their current number)
            'mobile_number' => ['required', 'string', 'max:15', Rule::unique('staffs', 'mobile_number')->ignore($staff->id)],
            'national_id' => ['nullable', 'string', 'max:20', Rule::unique('staffs', 'national_id')->ignore($staff->id)],

            'alternative_contact_person' => ['nullable', 'string', 'max:100'],
            'alternative_mobile_number' => ['nullable', 'string', 'max:15'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'mother_name' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date_format:d-m-Y'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'religion' => ['required', 'string', 'max:20'],
            'driving_license_number' => ['nullable', 'string', 'max:50'],
            'present_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],
            'staff_designation_id' => ['required', 'integer', 'exists:staff_designations,id'],
            'job_type' => ['required', Rule::in(['permanent', 'temporary', 'other'])],
            'joining_date' => ['nullable', 'date_format:d-m-Y'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspend'])],
        ]);

        // 2. Prepare Data for Database (Format Dates)
        if (isset($validatedData['date_of_birth'])) {
            $validatedData['date_of_birth'] = Carbon::createFromFormat('d-m-Y', $validatedData['date_of_birth']);
        }
        if (isset($validatedData['joining_date'])) {
            $validatedData['joining_date'] = Carbon::createFromFormat('d-m-Y', $validatedData['joining_date']);
        }

        // 3. Update the Staff Record
        try {
            // Use the $staff model instance bound by route model binding to update the record
            $staff->update($validatedData);

            // 4. Redirect with Success Message
            return redirect()->route('admin.staffs.index')->with('success', 'Staff member updated successfully.');

        } catch (\Exception $e) {
            // Handle database or other errors
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update staff member.']);
        }
    }
}
