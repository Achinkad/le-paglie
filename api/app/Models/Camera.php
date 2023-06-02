<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'location',
        'authorization'
    ];

    public function user() 
    {
        return $this->belongsToMany(User::class, 'camera_user', 'camera_id', 'user_id')
            ->withPivot('camera_id', 'user_id');
    }

    public function recognition()
    {
        return $this->hasMany(Recognition::class, 'camera_id', 'id');
    }
}
