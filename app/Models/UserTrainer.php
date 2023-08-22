<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserTrainer extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "trainer_id"
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
