<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'camera_id'
    ];

    public function user() {
        return $this->belongsToMany(User::class, 'alert_user', 'alert_id', 'user_id')
            ->withPivot('alert_id', 'user_id');
    }
}
