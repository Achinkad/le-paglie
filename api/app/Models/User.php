<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function camera() 
    {
        return $this->belongsToMany(Camera::class, 'camera_user', 'user_id', 'camera_id')
            ->withPivot('camera_id', 'user_id');
    }

    public function alert() 
    {
        return $this->belongsToMany(Alert::class, 'alert_user', 'user_id', 'alert_id')
            ->withPivot('alert_id', 'user_id');
    }

    public function recognition()
    {
        return $this->hasMany(Recognition::class, 'user_id', 'id');
    }
}
