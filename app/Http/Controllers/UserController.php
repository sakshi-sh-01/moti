<?php

namespace App\Http\Controllers;

use App\Models\Intrest;
use App\Models\UserNotifications;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\UserIntrest;
use App\Models\UserTrainer;
use App\Models\Role;
use App\Models\Follow;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function user_update(Request $request){
        $this->validate($request,[
            "resource" => "string",
            "experience" => "string",
            "shift" => "string|required",
            "scheduled_time" => "string|required",
            "is_public" => "boolean",
            "push_notification" => "boolean",
            "tips" => "boolean",
            "reminders" => "boolean",
            "intrests" => "array"
        ]);

        
        $user = User::where('id',Auth::user()->id)->where('status','Active')->exists();
        if($user){

            $user_in = new UserInfo;
            $user_in->resource = $request->resource ?? $user_in->resource;
            $user_in->experience = $request->experience ?? $user_in->experience;
            $user_in->shift = $request->shift ?? $user_in->shift;
            $user_in->scheduled_time = $request->scheduled_time ?? $user_in->scheduled_time;
            $user_in->is_public = $request->is_public ?? $user_in->is_public;
            $user_in->user_id = Auth::user()->id;
            $user_in->save();

            $notify = new UserNotifications;
            $notify->push_notification = $request->push_notification ?? false;
            $notify->tips = $request->tips ?? $notify->tips;
            $notify->reminders = $request->reminders ?? $notify->reminders;
            $notify->user_id = Auth::user()->id;
            $notify->save();

            foreach($request->intrests as $value){
                $intrest = Intrest::where('id',$value)->first();
                if(isset($intrest->id)){
                    $intrestcheck = UserIntrest::where('intrest_id',$intrest->id)->where('user_id',Auth::user()->id)->first();
                    if(isset($intrestcheck->id)){
                        return response()->json([
                            "message" => "Intrest ". $intrest->name . " is already selected "
                        ],409);
                    }
                    else{
                        $intrest_in = new UserIntrest;
                        $intrest_in->intrest_id = $intrest->id;
                        $intrest_in->user_id = Auth::user()->id;
                        $intrest_in->save();
                    }
                } else{
                    return response()->json([
                        "message" => "Intrest not exists"
                    ],404);
                }
            }

            return response()->json([
                "message" => "Saved",
                "user_info" => $user_in,
                "notify" => $notify,
                "intrests" => $intrest_in
            ],200);
            

        }else{
            return response()->json([
                "message" => "User not exists"
            ,400]);
        }
    }

    public function get_user_details($id){
        $user = User::where('id',$id)->with('userInfo')->with('usernotification')->with('userIntrest')->withCount('userIntrests')->with('posts')->first();
        if(isset($user->id)){
            return response()->json([
                "success" => true,
                "message" => "User found",
                "data" => $user
                ],200);
        }else{
            return response()->json([
                "success" => false,
                "message" => "User not found"
            ],400);
        }
    }

    public function edit_profile(Request $request, $id){
        $this->validate($request,[
         "first_name" => "string",
         "last_name" => "string",
         "dob" => "date",
         "gender" => "string",
         "email" => "string",
         "password" => "string",
         "image" => "image|mimes:jpeg,png,jpg|nullable",
         "cover" => "image|mimes:jpeg,png,jpg",
         "about" => "string"
        ]);

        $user = User::find($id);
        if(isset($user->id)){
            if($request->hasFile('image')){
                $file = $request->file('image');
                $custom_imagefile = time().'-'.$file->getClientOriginalName();
                $path_img = $file->storeAs('images', $custom_imagefile);
            }
            
            if($request->hasFile('cover')){
                $file = $request->file('cover');
                $custom_coverfile = time().'-'.$file->getClientOriginalName();
                $path_cover = $file->storeAs('covers', $custom_coverfile);
            }

            $user->first_name = $request->first_name ?? $user->first_name;
            $user->last_name = $request->last_name ?? $user->last_name;
            $user->dob = $request->dob ?? $user->dob;
            $user->gender = $request->gender ?? $user->gender;
            $user->email = $request->email ?? $user->email;
            $user->password = $request->password ?? $user->password;
            $user->profile = $path_img ?? $user->profile;
            $user->cover_image = $path_cover ?? $user->cover_image;
            $user->about = $request->about ?? $user->about;
            $user->save();
            return response()->json([
                "message" => "Updated",
                "data" => $user
            ],200);
        }else{
            return response()->json([
                "message" => "No user found"
            ],400);
        }
     }

     public function delete($id){
        $user = User::find($id);
        if(isset($user->id)){
            $user->delete();
            return response()->json([
                "message" => "User deleted"
            ],200);
        }else{
            return response()->json([
                "message" => "User not exists"
            ],400);
        }
     }
     
     public function update_user_intrest(Request $request){
        $this->validate($request,[
            "intrests" => "array",
        ]);
        if($request->intrests === null){
            return response()->json([
                "message" => "Updated"
            ],200);
        }
        foreach($request->intrests as $value){
            $intrest = Intrest::where('id',$value)->first();
            if(isset($intrest->id)){
                $intrestcheck = UserIntrest::where('intrest_id',$intrest->id)->where('user_id',Auth::user()->id)->first();
                if(isset($intrestcheck->id)){
                    return response()->json([
                        "message" => "Intrest (". $intrest->name . ") is already selected "
                    ],409);
                }
                else{
                    $intrest_in = new UserIntrest;
                    $intrest_in->intrest_id = $intrest->id;
                    $intrest_in->user_id = Auth::user()->id;
                    $intrest_in->save();
                }
            } else{
                return response()->json([
                    "message" => "Intrest not exists"
                ],404);
            }
        }

        return response()->json([
            "message" => "Updated and saved",
            "data" => $intrest_in
        ],200);
     }


     public function search(Request $request){
        $this->validate($request,[
            "search" => "string|required"
        ]);

        $user_query = User::query()->with('role');
        $user_query->whereHas('role', function($query){
            $query->where('name','Trainer');
        })->where('status','Active')->where(function($query) use ($request){
            $query->where('first_name','like','%'. $request->search .'%')->orwhere('last_name','like','%'. $request->search .'%')->orwhere('email','like','%'. $request->search .'%');
        });
        

        $post_query = Post::query()->with('user');
        $post_query->whereHas('user', function($query){
            $query->where('status','Active');
        })->where('title','like','%'. $request->search. '%');

        $intrest_query = Intrest::query()->where('name','like','%'. $request->search .'%');

        $users = $user_query->get()->toArray();
        $posts = $post_query->get()->toArray();
        $intrests = $intrest_query->get()->toArray();
        
        $users = array_map(function ($v){
            $v['type'] = 'User';
            return $v;
        }, $users);

        $posts = array_map(function ($v){
            $v['type'] = 'Post';
            return $v;
        }, $posts);

        $intrests = array_map(function ($v){
            $v['type'] = 'Intrest';
            return $v;
        }, $intrests);

        $records = array_merge($users, $posts, $intrests);
        $collection = new Collection($records);

        $countCollection = clone $collection;
        $totalcount = $countCollection->count();

        $results = $collection->all();

        return response()->json([
            "success" => true,
            "count" => $totalcount,
            "data" => $results
        ],200);

     }

}


