<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //validate the request
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string|confirmed|min:8',
        ]);
        //check token and email validity
        $result = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->firstOrFail();
        if (!$result) {
            return back()->with('error', 'Invalid token or email!');
        }
        //update password
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        //delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        return redirect('/login')->with('success', 'Password updated successfully!');
    }
}
