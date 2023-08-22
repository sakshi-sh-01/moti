<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotifications extends Model
{
    use HasFactory;

    protected $fillable=[
        "push_notification",
        "tips",
        "reminders",
        "user_id"
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
