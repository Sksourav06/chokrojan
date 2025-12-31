<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class StaffDesignation extends Model
{
    use HasFactory;
    protected $table = 'staff_designations';

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    // Optional: Define the inverse relationship with the Staff model
    public function staffs(): HasMany
    {
        return $this->hasMany(Staff::class, 'staff_designation_id');
    }
}
