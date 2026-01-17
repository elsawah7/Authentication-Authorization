<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyAcountRequest;
use App\Models\User;
use Illuminate\Http\Request;

class VerifyAcountController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(VerifyAcountRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user->otp === implode('', $request->otp)) {
            return back()->with('error', 'Invalid OTP Or Email Address. Please try again.');
        }
        $user->email_verified_at = now();
        $user->otp = null;
        $user->save();
        return redirect('/login')->with('success', 'Your account has been verified successfully. You can now log in.');
    }
}
