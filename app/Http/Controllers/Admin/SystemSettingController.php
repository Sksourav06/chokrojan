<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SystemSettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $settings = SystemSetting::first();
        return view('admin.settings.index', compact('settings', 'user'));
    }

    public function update(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            // Ensuring core numeric fields are validated
            'booking_cancel_time' => 'nullable|integer|min:0',
            'site_name' => 'nullable|string|max:255',
            // ... (Add other necessary validations here)
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 2. Load Settings (Ensure an instance exists)
        $settings = SystemSetting::first() ?? new SystemSetting();

        // 3. Data Preparation
        $data = $request->except(['logo']);

        // 4. Checkbox Handling (Unchecked = 0)
        $checkboxes = [
            'seat_cancel_allow',
            'booking',
            'vip_booking',
            'goods_charge',
            'callerman_commission',
            'discount',
            'discount_show_in_ticket',
            'counter_cancel_allow',
            'online_cancel_allow',
        ];

        foreach ($checkboxes as $box) {
            $data[$box] = $request->has($box) ? 1 : 0;
        }

        // 5. ✅ FINAL FIX: Sanitize ALL numeric fields dynamically

        // Loop through the data array derived from the request
        foreach ($data as $key => $value) {
            // Check if the setting instance has this property AND it's not a logo/checkbox (already handled)
            if (isset($settings->{$key}) && !in_array($key, $checkboxes)) {

                // If the input is null, an empty string, or zero, ensure it's saved as integer 0.
                if (empty($value) && $value !== 0) {
                    $data[$key] = 0;
                } else {
                    // Force casting to the correct type (integer or float)
                    $data[$key] = is_float($settings->{$key}) ? (float) $value : (int) $value;
                }
            }
        }

        // 6. Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $path;
        } else {
            $data['logo'] = $settings->logo;
        }

        // 7. Save Data
        try {
            $settings->fill($data)->save();
            Log::info('Settings saved successfully. Booking Cancel Time: ' . $settings->booking_cancel_time);

            return redirect()->back()->with('success', 'Settings updated successfully!');

        } catch (\Exception $e) {
            Log::error('Settings Save Failed: ' . $e->getMessage() . ' Data: ' . print_r($data, true));
            return redirect()->back()->with('error', 'Database save failed. Check logs.');
        }
    }
}