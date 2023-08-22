<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Role;
use App\Models\Post;
use App\Models\UserTrainer;
use App\Models\Intrest;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'dob',
        'gender',
        'email',
        'password',
        'status',
        'role_id',
        'about',
        'profile',
        'cover_image'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }


    public function UserTrainer()
    {
        return $this->hasMany(UserTrainer::class);
    }

    // public function Users(){
    //     return $this->hasMany(UserTrainer::class,'trainer_id');
    // }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function following()
    {
        return $this->belongsToMany(Follow::class, 'following_id', 'follower_id');
    }

    public function userInfo()
    {
        return $this->hasOne(UserInfo::class, 'user_id');
    }

    public function userNotification()
    {
        return $this->hasOne(UserNotification::class, 'user_id');
    }

    public function Intrest(){
        return $this->belongsToMany(Intrest::class, 'user_intrests', 'user_id', 'intrest_id');
    }

    public function comments(){
        return $this->hasMany(PostComment::class,'user_id');
    }

    public function likes(){
        return $this->hasMany(PostLike::class,'user_id');
    }

    public function userActivities(){
        return $this->hasOne(UserActivities::class, 'user_id');
    }
}
