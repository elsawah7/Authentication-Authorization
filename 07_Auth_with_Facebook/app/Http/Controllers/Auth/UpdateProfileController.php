<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateProfileController extends Controller
{

    public function __invoke(UpdateProfileRequest $request)
    {
        $user = User::findOrFail(Auth::id());
        $user->update($request->validated());
        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }
}
