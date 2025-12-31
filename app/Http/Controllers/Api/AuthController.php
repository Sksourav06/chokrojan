<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Passenger; // User এর বদলে Passenger মডেল ব্যবহার
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Mail\LoginOtpMail;
use Illuminate\Support\Facades\Mail;
class AuthController extends Controller
{
    /**
     * ওটিপি কোড জেনারেট এবং সেন্ড করা
     */
    public function sendLoginCode(Request $request)
    {
        // ... আপনার আগের লজিক ...
        $code = rand(1000, 9999);

        // ক্যাশে সেভ করা
        cache()->put('login_otp_' . $request->identifier, $code, now()->addMinutes(5));

        // যদি ইউজার ইমেইল দিয়ে ট্রাই করে, তবে ইমেইল পাঠানো
        if (filter_var($request->identifier, FILTER_VALIDATE_EMAIL)) {
            Mail::to($request->identifier)->send(new LoginOtpMail($code));
        }

        return response()->json([
            'status' => true,
            'message' => 'ওটিপি কোডটি আপনার ইমেইলে পাঠানো হয়েছে।',
            'otp_preview' => $code // টেস্টিংয়ের জন্য
        ]);
    }

    /**
     * প্যাসেঞ্জার রেজিস্ট্রেশন (Sign Up)
     */
    public function register(Request $request)
    {
        // প্রোফাইলের সব ফিল্ড ভ্যালিডেশন
        $request->validate([
            'first_name' => 'required|string|max:100',
            'mobile_number' => 'nullable|string|unique:passengers,mobile_number',
            'email' => 'nullable|email|unique:passengers,email',
        ]);

        try {
            // সরাসরি Passengers টেবিলে ডাটা সেভ
            $passenger = Passenger::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'mobile_number' => $request->mobile_number,
                'gender' => $request->gender ?? 'MALE',
                'email' => $request->email,
                'password' => Hash::make('123456'), // ডিফল্ট পাসওয়ার্ড
                'street_address' => $request->street_address,
                'city' => $request->city,
                'ticket_issue_id' => null,
                'zip_code' => $request->zip_code,
                'status' => 'active',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Registration Successful! Please Login.',
                'passenger' => $passenger,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'রেজিস্ট্রেশন করতে সমস্যা হয়েছে: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function verifyLoginCode(Request $request)
    {
        // ১. ইনপুট ভ্যালিডেশন
        $request->validate([
            'identifier' => 'required', // মোবাইল নম্বর বা ইমেইল
            'otp' => 'required|numeric',
        ]);

        $identifier = $request->identifier;
        $userOtp = $request->otp;

        // ২. ক্যাশ থেকে সেভ করা ওটিপি উদ্ধার করা
        $cachedOtp = cache()->get('login_otp_' . $identifier);

        // ৩. ওটিপি ম্যাচিং চেক করা
        if (!$cachedOtp || $cachedOtp != $userOtp) {
            return response()->json([
                'status' => false,
                'message' => 'ওটিপি কোডটি সঠিক নয় অথবা মেয়াদ শেষ হয়ে গেছে।',
            ], 401);
        }

        // ৪. ওটিপি সঠিক হলে ক্যাশ থেকে মুছে ফেলা
        cache()->forget('login_otp_' . $identifier);

        // ৫. প্যাসেঞ্জার ডাটাবেজ থেকে খুঁজে বের করা
        $passenger = \App\Models\Passenger::where('mobile_number', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if (!$passenger) {
            return response()->json([
                'status' => false,
                'message' => 'ইউজার প্রোফাইল পাওয়া যায়নি।',
            ], 404);
        }

        // ৬. স্যানক্টাম টোকেন (Sanctum Token) জেনারেট করা
        // আপনার Passenger মডেলটিতে অবশ্যই "HasApiTokens" ব্যবহার করতে হবে
        $token = $passenger->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'লগইন সফল হয়েছে',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $passenger,
        ]);
    }
}