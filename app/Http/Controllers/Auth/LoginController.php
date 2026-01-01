<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // your Blade login file
    }

    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt login using username (or mobile_number)
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate(); // prevent session fixation
            return redirect()->intended('dashboard')->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function sendLoginCode(Request $request)
    {
        // ১. ভ্যালিডেশন: ফোন নম্বর বা ইমেইল ইনপুট হিসেবে নেওয়া
        $request->validate([
            'identifier' => 'required',
        ]);

        $identifier = $request->identifier;

        // ২. প্যাসেঞ্জার টেবিলে ইউজারকে খুঁজে বের করা
        // আপনি যেহেতু User টেবিল ব্যবহার করবেন না, তাই এখানে Passenger মডেল ব্যবহার করা হয়েছে
        $passenger = \App\Models\Passenger::where('mobile_number', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if (!$passenger) {
            return response()->json([
                'status' => false,
                'message' => 'ইউজার খুঁজে পাওয়া যায়নি',
            ], 404);
        }

        // ৩. ওটিপি কোড জেনারেট করা (৪ ডিজিট)
        $code = rand(1000, 9999);

        // ৪. কোডটি ক্যাশে সেভ করা (৫ মিনিটের জন্য)
        // ভেরিফিকেশনের সময় যাতে সহজেই খুঁজে পাওয়া যায় তাই identifier ব্যবহার করা হয়েছে
        cache()->put('login_otp_' . $identifier, $code, now()->addMinutes(5));

        // ৫. ওটিপি পাঠানো (টেস্টিং এর জন্য প্রিভিউ দেওয়া হয়েছে)
        return response()->json([
            'status' => true,
            'message' => 'লগইন কোড সফলভাবে পাঠানো হয়েছে',
            'otp_preview' => $code // ডেভেলপমেন্ট শেষে এটি রিমুভ করে দেবেন
        ]);
    }
}
