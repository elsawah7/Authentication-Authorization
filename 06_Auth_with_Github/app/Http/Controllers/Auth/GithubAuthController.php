<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GithubAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback(Request $request)
    {
        $githubUser = Socialite::driver('github')->user();

        $user = User::where('email', $githubUser->getEmail())->first();
        if (!$user) {
            $user = User::create([
                'name' => $githubUser->getName(),
                'email' => $githubUser->getEmail(),
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
