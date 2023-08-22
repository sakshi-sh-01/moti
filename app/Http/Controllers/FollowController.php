<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function follow($id){
        $user = User::where('id',$id)->where('status','Active')->exists();
        if($user){
            $follow = new Follow;
            $follow->following_id = $id;
            $follow->follower_id = Auth::user()->id;
            $follow->save();
    
            return response()->json([
                "success" => true,
                "message" => "Followed"
            ],200);
        }else{
            return response()->json([
                "success" => false,
                "message" => "No user exists"
            ],400);
        }
    }

    public function unfollow($id){

        $user = Follow::where('follower_id',Auth::user()->id)->where('following_id',$id)->first();
        if(isset($user->id)){
            $user->delete();
            return response()->json([
                "message" => "Unfollow"
            ],200);
        }
    }
}
