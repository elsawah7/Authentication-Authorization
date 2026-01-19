<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'otp',
        'account_verified_at',
        'logout_other_devices',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'account_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions(): array
    {
        return $this->roles()->with('permissions')->get()
            ->pluck('permissions')->flatten()->pluck('name')->unique()->toArray();
    }
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions());
    }
}
