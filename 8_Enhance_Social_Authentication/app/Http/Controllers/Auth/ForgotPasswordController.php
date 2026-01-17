<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendResetLinkMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //validate the email
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
        ]);

        $token = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert([
            'email' => $request->email,
        ], [
            'token' => $token,
            'created_at' => now(),
        ]);

        Mail::to($request->email)->send(new SendResetLinkMail($token));
        return back()->with('success', 'We have sent you a password reset link!');
    }
}
