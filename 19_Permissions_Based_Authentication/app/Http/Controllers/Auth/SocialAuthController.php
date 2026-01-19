<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

        return Socialite::driver($driver)->redirect();
    }

    public function callback($driver)
    {
        if (!array_key_exists($driver, config('social.providers'))) {
            return redirect()->route('login')->with('error', 'Unsupported social login provider.');
        }

        try {
            $socialUser = Socialite::driver($driver)->user();

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
                    'password' => Hash::make(Str::random(32)),
                    'account_verified_at' => now(),
                ]);
            } elseif (!$user->account_verified_at) {
                $user->account_verified_at = now();
                $user->save();
            }

            Auth::login($user, true);

            request()->session()->regenerate();

            $url = [
                'student' => '/student',
                'teacher' => '/teacher',
                'admin' => '/admin',
            ];


            $defaultRedirect = $url[$user->role] ?? '/student';

            return redirect()->intended($defaultRedirect);
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Authentication failed, please try again.');
        }
    }
}
