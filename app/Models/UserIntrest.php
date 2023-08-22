<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Casts\Attribute;

class UserIntrest extends Model
{
    use HasFactory;    

    protected $fillable=[
        "user_id",
        "intrest_id"
    ]; 

    public function user(){
        return $this->belongsToMany(User::class);
    }

    public function intrest(){
        return $this->belongsToMany(Intrest::class);
    }
}
