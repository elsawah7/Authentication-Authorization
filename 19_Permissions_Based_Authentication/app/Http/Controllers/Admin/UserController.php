<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\ChangeUsersRoleRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', User::class);
        $users = User::all();
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function changeRole(ChangeUsersRoleRequest $request, User $user)
    {
        Gate::authorize('changeRoles', $user);
        $user->roles()->sync($request->role_ids);
        return back()->with('success', 'Roles changed successfully');
    }
}
