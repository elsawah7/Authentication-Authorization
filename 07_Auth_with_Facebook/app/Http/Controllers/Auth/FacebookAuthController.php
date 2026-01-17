<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class FacebookAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }
    public function callback(Request $request)
    {
        $facebookUser = Socialite::driver('facebook')->stateless()->user();
        $email = $facebookUser->getEmail();
        $name = $facebookUser->getName();

        if (!$email) {
            return redirect()->route('login')->with('error', 'Unable to retrieve email from Facebook. Please use another login method.');
        }
        if (!$name) {
            $name = 'Facebook User';
        }
        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(uniqid()), // Generate a random password
                'email_verified_at' => now(),
            ]);
        } elseif (!$user->email_verified_at) {
            return redirect()->route('email.verify', ['email' => $user->email])->with('info', 'Please verify your email to continue.');;
        }
        Auth::login($user);
        return redirect()->intended('profile');
    }
}
