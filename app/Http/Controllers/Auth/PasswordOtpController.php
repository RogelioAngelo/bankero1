<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Carbon\Carbon;

class PasswordOtpController extends Controller
{
    public function showVerifyForm(Request $request)
    {
        return view('auth.password-otp-verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
            'token' => 'nullable|string',
        ]);

        // try verifying by email first (email + otp). If not found, try token lookup.
        $email = $request->input('email');
        $token = $request->input('token');

        $data = null;
        if (!empty($email)) {
            $data = Cache::get('password_otp:email:'.$email);
            if (empty($data)) {
                // fallback to token lookup if provided
                if (!empty($token)) {
                    $data = Cache::get('password_otp:'.$token);
                }
            }
        } else if (!empty($token)) {
            $data = Cache::get('password_otp:'.$token);
        }

        if (empty($data)) {
            return back()->withErrors(['email' => __('Invalid or expired OTP or token.')]);
        }

        if (!hash_equals((string)$data['otp'], (string)$request->input('otp'))) {
            return back()->withErrors(['otp' => __('The provided OTP is incorrect.')]);
        }

        // find user
        $emailToUse = $data['email'] ?? $email;
        $user = User::where('email', $emailToUse)->first();
        if (!$user) {
            return back()->withErrors(['email' => __('No account found for this email.')]);
        }

        // create a password broker token for the user
        $brokerToken = Password::broker()->createToken($user);

        // delete cache entries to prevent reuse (token key + email key)
        if (!empty($token) && Cache::has('password_otp:'.$token)) {
            Cache::forget('password_otp:'.$token);
        }
        // clear email-keyed cache using the request email or the cached email
        $emailKey = $emailToUse ?? $email;
        if (!empty($emailKey) && Cache::has('password_otp:email:'.$emailKey)) {
            Cache::forget('password_otp:email:'.$emailKey);
        }

    // redirect to standard reset form with the broker token and prefill email
    return redirect()->route('password.reset', ['token' => $brokerToken])->withInput(['email' => $user->email, 'token' => $brokerToken]);
    }

    public function showResetFromOtp($token)
    {
        // token here is the token we stored in cache earlier for cross-device link
        $data = Cache::get('password_otp:'.$token);
        if (empty($data)) {
            return redirect()->route('password.request')->withErrors(['token' => __('Invalid or expired token.')]);
        }

        // find user
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email' => __('No account found for this email.')]);
        }

    // create broker token and redirect to reset form (prefill email)
    $brokerToken = Password::broker()->createToken($user);
    Cache::forget('password_otp:'.$token);

    return redirect()->route('password.reset', ['token' => $brokerToken])->withInput(['email' => $user->email, 'token' => $brokerToken]);
    }
}
