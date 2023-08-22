<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserIntrest;
use App\Models\Post;

class Intrest extends Model
{
    use HasFactory;

    protected $fillable=[
        "name",
        "image",
    ];

    public function user(){
        return $this->hasMany(UserIntrest::class);
    }

    public function posts(){
        return $this->hasMany(Post::class, 'intrest_id');
    }
}
