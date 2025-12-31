<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Platform;
use App\Models\SchedulePlatformPermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SchedulePermissionController extends Controller
{
    /**
     * শিডিউল আইডি অনুযায়ী প্ল্যাটফর্ম পারমিশন ডাটা গেট করা
     */
    public function getPlatformPermissions($scheduleId)
    {
        try {
            $platforms = Platform::all();

            $saved = SchedulePlatformPermission::where('schedule_id', $scheduleId)
                ->get()
                ->keyBy('platform_id');

            $data = $platforms->map(function ($p) use ($saved) {
                $row = $saved->get($p->id);

                return [
                    'platform_id' => $p->id,
                    'name' => $p->name,
                    'logo' => $p->logo,
                    'from_date' => $row->from_date ?? now()->toDateString(),
                    'to_date' => $row->to_date ?? '2099-12-31',
                    'blocked_seats' => $row->blocked_seats ?? [],
                    'status' => $row->status ?? 0,
                ];
            });

            return response()->json([
                'status' => 'success',
                'platforms' => $data,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * পারমিশন ডাটা সেভ বা আপডেট করা
     */
    public function savePlatformPermissions(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'platforms' => 'required|array',
            'platforms.*.platform_id' => 'required|numeric',
            'platforms.*.status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // আগের ডাটা ডিলিট করে নতুন করে ইনসার্ট করা
            SchedulePlatformPermission::where('schedule_id', $id)->delete();

            foreach ($request->platforms as $item) {
                SchedulePlatformPermission::create([
                    'schedule_id' => $id,
                    'platform_id' => $item['platform_id'],
                    'from_date' => $item['from_date'] ?? now()->toDateString(),
                    'to_date' => $item['to_date'] ?? '2099-12-31',
                    'status' => $item['status'],
                    'blocked_seats' => $item['blocked_seats'] ?? [],
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Permissions updated!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}