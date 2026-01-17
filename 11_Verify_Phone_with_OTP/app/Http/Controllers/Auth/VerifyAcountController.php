<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendVerficationOtpRequest;
use App\Http\Requests\Auth\VerifyAcountRequest;
use App\Mail\VerifyAcountMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;


class VerifyAcountController extends Controller
{

    public function sendOtp(SendVerficationOtpRequest $request)
    {
        $type  = filter_var($request->input('identifier'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::where($type, $request->identifier)->first();
        if ($user->account_verified_at) {
            return redirect()->to('/login')->with('success', 'You Are Already Vreified');
        }
        if ($request->method == 'email') {
            Mail::to($user->email)->send(new VerifyAcountMail($user->otp, $user->email));
            return redirect()->route('account.verify', ['email' => $user->email])->with('info', 'Please verify your email before logging in. An OTP has been sent to your email address.');
        } elseif ($request->method == 'phone') {
            if (!$user->phone || $user->phone == '') {
                return back()->with('error', 'You Do Not Have Any Phone Number');
            }
            try {
                // Twilio Client
                $twilio = new Client(
                    env('TWILIO_SID'),
                    env('TWILIO_AUTH_TOKEN')
                );
                $to = 'whatsapp:' . ltrim($user->phone, '+');
                $from = env('TWILIO_PHONE');
                $twilio->messages->create(
                    $to,
                    [
                        'from' => $from,
                        'body' => "Your OTP code is: {$user->otp}"
                    ]
                );
                return redirect()->route('account.verify', $request->method  === 'phone' ? $user->phone : $user->email);
            } catch (\Throwable $th) {
                return back()->with('error', $th->getMessage());
            }
        }
    }
    public function verifyOtp(VerifyAcountRequest $request)
    {
        $type  = filter_var($request->input('identifier'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::where($type, $request->identifier)->first();

        if ($user->otp !== implode('', $request->otp)) {
            return back()->with('error', 'Invalid OTP Or Account Data. Please try again.');
        }
        $user->account_verified_at = now();
        $user->otp = null;
        $user->save();
        return redirect('/login')->with('success', 'Your account has been verified successfully. You can now log in.');
    }
}
