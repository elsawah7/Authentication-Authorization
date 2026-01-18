<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Agent;


class Session extends Model
{
    protected $table = 'sessions';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'ip_address',
        'user_agent',
        'payload',
        'last_activity'
    ];
    public function getLastActivityAttribute($value)
    {
        return Carbon::createFromTimestamp($value)->diffForHumans();
    }
    public function getUserAgentAttribute($value)
    {
        $agent = new Agent();
        $agent->setUserAgent($value);
        return [
            'platform' => $agent->platform(),
            'browser' => $agent->browser(),
            'is_desktop' => $agent->isDesktop(),

        ];
    }

    public function getIsThisDeviceAttribute()
    {
        return $this->id == request()->session()->getId();
    }


    protected $hidden = [
        'payload'
    ];
    protected $appends = [
        'is_this_device'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
