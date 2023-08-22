<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    protected $fillable = [
        "following_id",
        "follower_id"
    ];

    public function user()
    {
        return $this->hasMany(User::class, 'following_id', 'follower_id');
    }
}
