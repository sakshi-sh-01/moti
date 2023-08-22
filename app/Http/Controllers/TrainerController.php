<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserTrainer;
use Illuminate\Support\Facades\Auth;

class TrainerController extends Controller
{
    public function connect_trainer($id){

        $user_t = User::query()->where('id',$id)->whereHas('role', function($query){
            $query->where('name','trainer');
        })->first();

        if($user_t){
            $user_tr_exists = UserTrainer::where('user_id',Auth::user()->id)
            ->where('trainer_id',$user_t->id)->exists();
            if($user_tr_exists){
                return response()->json([
                    "message" => "Already connected"
                ],409);
            }else{
                $user = new UserTrainer;
                $user->user_id = Auth::user()->id;
                $user->trainer_id = $user_t->id;
                $user->save();
    
                return response()->json([
                    "message" => "Saved",
                    "data" => $user_t
                ],200);
            }
        }else{
            return response()->json([
                "message" => "No trainer id exists "
            ],400);
        }

     }

}
