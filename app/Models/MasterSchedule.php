<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterSchedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * এই অ্যারেতে সেই কলামগুলির নাম থাকতে হবে যেগুলিতে আপনি MasterSchedule::create() 
     * বা $master->update() ব্যবহার করে ডেটা সেভ করবেন।
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'trip_code',            // ট্রিপ কোড (যেমন: 401)
        'bus_id',
        'route_id',
        'start_station_id',
        'end_station_id',
        'start_time_only',      // শুধুমাত্র সময় অংশ (TIME)
        'end_time_only',        // শুধুমাত্র শেষের সময় অংশ (TIME)
        'bus_type',
        'start_time_nextday',
        'status',
    ];

    /**
     * The attributes that should be cast.
     * * @var array
     */
    protected $casts = [
        'start_time_nextday' => 'boolean',

    ];

    // Master Schedule এর সাথে যুক্ত সকল দৈনিক শিডিউল (Daily Schedules)
    public function dailySchedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'master_schedule_id');
    }

    // Master Schedule এর সাথে যুক্ত বাস, রুট ইত্যাদি সম্পর্ক এখানে যোগ করা যেতে পারে
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
    public function startStation()
    {
        return $this->belongsTo(Station::class, 'start_station_id');
    }

    // End Station relationship (MasterSchedule belongs to Station)
    public function endStation()
    {
        return $this->belongsTo(Station::class, 'end_station_id');
    }

    // * @return \Carbon\Carbon|null
    //  */
    public function getStartTimeOnlyAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value) : null;
    }

    /**
     * Get the end time as a Carbon instance.
     *
     * @return \Carbon\Carbon|null
     */
    public function getEndTimeOnlyAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value) : null;
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'master_schedule_id'); // Assuming foreign key is 'master_schedule_id' in the 'schedules' table
    }

}