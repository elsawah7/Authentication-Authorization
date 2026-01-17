<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateEmailRequest;
use App\Mail\SendMagicLinkMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class MagicLoginController extends Controller
{
    public function sendMagicLink(ValidateEmailRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $url =  URL::temporarySignedRoute(
            'login.magic.handler',
            now()->plus(seconds: 180),
            ['user' => $user->id]
        );
        Mail::to($request->email)->send(new SendMagicLinkMail($url));
        return back()->with('success', 'Magic link sent to your email!');
    }

    public function loginHandler(Request $request, User $user)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }
        Auth::login($user);
        return redirect()->intended('profile');
    }
}
