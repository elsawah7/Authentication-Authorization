<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirect($driver)
    {
        if (!array_key_exists($driver, config('social.providers'))) {
            return redirect()->route('login')->with('error', 'Unsupported social login provider.');
        }
        return Socialite::driver($driver)->stateless()->redirect();
    }
    public function callback($driver)
    {
        if (!array_key_exists($driver, config('social.providers'))) {
            return redirect()->route('login')->with('error', 'Unsupported social login provider.');
        }
        try {
            $socialUser = Socialite::driver($driver)->stateless()->user();
            $email = $socialUser->getEmail();
            $name = $socialUser->getName();
            if (!$email) {
                return redirect()->route('login')->with('error', 'Unable to retrieve email. Please use another login method.');
            }
            if (!$name) {
                $name = 'Social User';
            }
            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' =>  Hash::make(Str::random(32)), // Generate a random password
                    'email_verified_at' => now(),
                ]);
            } elseif (!$user->email_verified_at) {
                return redirect()->route('email.verify', ['email' => $user->email])->with('info', 'Please verify your email to continue.');;
            }
            Auth::login($user);
            return redirect()->intended('profile');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Authentication failed, please try again.');
        }
    }
}
