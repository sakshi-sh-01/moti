<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Intrest;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        "type",
        "file",
        "title",
        "duration",
        "user_id",
        "intrest_id"
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(){
        return $this->hasMany(PostComment::class,'post_id');
    }

    public function likes(){
        return $this->hasMany(PostLike::class,'post_id');
    }

    public function intrest(){
        return $this->belongsTo(Intrest::class,'intrest_id');
    }

    public function userActivities(){
        return $this->hasMany(UserActivities::class,'post_id');
    }

}
