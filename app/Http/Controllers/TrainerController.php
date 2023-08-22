<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\UserTrainer;
use Illuminate\Support\Facades\Auth;

class TrainerController extends Controller
{
    public function connect($id){

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

     public function topTrainers($id)
    {
        $posts = Post::query()
            ->where('user_id', $id)
            ->whereHas('user', function ($query) {
                $query->where('status', 'Active');
            })
            ->with(['user' => function ($query) {
                $query->select('id', 'about');
                $query->with('Intrest', function ($query) {
                    $query->take(2)->get();
                });
            }])
            ->get();

        return response()->json([
            "success" => true,
            "data" => $posts
        ]);
    }

}
