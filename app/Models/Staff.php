<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;


    protected $table = 'staffs';
    protected $fillable = [
        'name',
        'mobile_number',
        'alternative_contact_person',
        'alternative_mobile_number',
        'father_name',
        'mother_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'religion',
        'national_id',
        'driving_license_number',
        'present_address',
        'permanent_address',
        'staff_designation_id', // Foreign key to Designation table
        'job_type',
        'joining_date',
        'status',
    ];

    // Assuming you have a separate Designation Model
    public function designation()
    {
        return $this->belongsTo(StaffDesignation::class, 'staff_designation_id');
    }
}
