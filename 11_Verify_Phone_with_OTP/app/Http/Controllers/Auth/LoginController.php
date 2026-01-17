<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyAcountMail;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        $type  = filter_var($request->input('identifier'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::where($type, $request->identifier)->first();
        if (!$user) {
            return back()->with('error', 'Invalid credentials provided.');
        }
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Invalid credentials provided.');
        }

        if (!$user->account_verified_at) {
            Mail::to($user->email)->send(new VerifyAcountMail($user->otp, $user->email));
            return redirect()->route('account.verify', ['email' => $user->email])->with('info', 'Please verify your email before logging in. An OTP has been sent to your email address.');
        }
        Auth::login($user);
        return redirect()->intended('/profile')->with('success', 'Logged in successfully.');
    }
}
