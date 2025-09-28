<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AdminLoginVerificationNotification;
use App\Models\User;

class AdminLoginVerificationController extends Controller
{
    public function pending()
    {
        return view('auth.admin-login-pending');
    }

    public function resendPage(Request $request)
    {
        return view('auth.admin-login-resend');
    }

    public function resend(Request $request)
    {
        $user = $request->user();
        $pendingId = session('admin_login_pending_id');

        if (! $user && $pendingId) {
            $user = User::find($pendingId);
        }

        if (! $user || $user->utype !== 'ADM') {
            abort(403);
        }

        $key = 'admin_resend_info_' . $user->id;
        $info = session($key, ['sends' => 0, 'penalty_level' => 0, 'blocked_until' => null]);

        $now = now()->timestamp;
        $blockedUntil = $info['blocked_until'] ?? null;
        if ($blockedUntil && $blockedUntil > $now) {
            $remaining = $blockedUntil - $now;
            return back()->withErrors(['resend' => 'You must wait before resending. Try again in ' . gmdate('i\:s', $remaining) . '.']);
        }

        // send verification link
        $signed = URL::temporarySignedRoute('admin.verify-login', now()->addMinutes(10), ['id' => $user->id]);
        Notification::sendNow($user, new AdminLoginVerificationNotification($signed));

        // set fixed cooldown 30 seconds after this send
        $info['blocked_until'] = $now + 30;

        // increment sends counter
        $info['sends'] = ($info['sends'] ?? 0) + 1;

        // if reached 5 sends, apply penalty: 5 minutes then double on subsequent penalties
        if ($info['sends'] >= 5) {
            $info['penalty_level'] = ($info['penalty_level'] ?? 0) + 1;
            $waitSeconds = 300 * (2 ** ($info['penalty_level'] - 1)); // 5min * 2^(level-1)
            $info['blocked_until'] = $now + $waitSeconds;
            // reset sends after applying the penalty
            $info['sends'] = 0;
            session([$key => $info]);
            return back()->with('status', 'Verification email resent. You have reached the limit and must wait ' . gmdate('i\:s', $waitSeconds) . ' before resending again.');
        }

        // store updated info
        session([$key => $info]);

        return back()->with('status', 'Verification email resent. Please check your inbox. You may resend after 00:30.');
    }

    public function verify(Request $request, $id)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired verification link.');
        }

        $user = User::findOrFail($id);

        // log the user in for the admin session
        Auth::loginUsingId($user->id);

        // clear pending session key
        session()->forget('admin_login_pending_id');

        return redirect()->route('admin.index')->with('status', 'Admin login verified.');
    }

    public function status(Request $request)
    {
        // return whether the current session is authenticated as an admin
        $ok = $request->user() && $request->user()->utype === 'ADM';
        return response()->json(['admin_logged_in' => $ok]);
    }
}
