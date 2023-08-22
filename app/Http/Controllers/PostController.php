<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\User;
use App\Models\UserActivities;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function create(Request $request){
        $this->validate($request,[
            "type" => "string|required",
            "file" => "file|required|mimes:png,jpg,jpeg,mp3,mp4",
            "title" => "string",
            "duration" => "string|required_if:type,video",
            "intrest_id" => "string"
        ]);

        $post = Post::where('user_id',Auth::user()->id)->where('file',$request->file)->exists();
        if($post){
            return response()->json([
                "success" => false,
                "message" => "Already exists"
            ],409);
        }else{
            $post = new Post;
            $post->type = $request->type;
            $post->file = $request->file;
            $post->title = $request->title;
            $post->duration = $request->duration;
            $post->intrest_id = $request->intrest_id;
            $post->user_id = Auth::user()->id;
            $post->save();

            return response()->json([
                "success" => true,
                "message" => "Post created",
                "data" => $post
            ],201);
        }
    }

    public function update(Request $request, $id){
        $this->validate($request,[
            "type" => "string",
            "file" => "file|mimes:png,jpg,jpeg,mp3,mp4",
            "title" => "string",
            "duration" => "string|required_if:type,video"
        ]);

        $post = Post::where('id',$id)->where('user_id',Auth::user()->id)->first();
        if(isset($post->id)){
            $post->type = $request->type ?? $post->type;
            $post->file = $request->file ?? $post->file;
            $post->title = $request->title ?? $post->title;
            $post->duration = $request->duration ?? $post->duration;
            $post->intrest_id = $request->intrest_id ?? $post->intrest_id;
            $post->save();

            return response()->json([
                "success" => true,
                "message" => "Updated and saved",
                "data" => $post
            ],200);
        }else{
            return response()->json([
                "success" => false,
                "message" => "No post exists for this user"
            ],400);
        }
    }

    public function delete($id){
        $post = Post::where('id',$id)->where('user_id',Auth::user()->id)->first();
        if(isset($post->id)){
            $post->delete();
            return response()->json([
                "success" => true,
                "message" => "Post deleted"
            ],200);
        }else{
            return response()->json([
                "success" => false,
                "message" => "No post exists for this user"
            ],400);
        }
    }

    public function feed(){
        $user_follow = Follow::where('follower_id',Auth::user()->id)->pluck('following_id');
        $posts = Post::whereIn('user_id', $user_follow)->get();
        return response()->json([
            "success" => true,
            "data" => $posts
        ],200);
    }

    public function details(Request $request,$id){
        
        $user = User::where('id',$id)->with('userInfo')->first();
        if($user && isset($user->id)){
            $post_query = Post::query();
            $post_query->where('user_id',$id)->with('comments')->withCount('comments');
            if(isset($request->title)){
                $post_query->where('title','LIKE','%'.$request->title.'%');
            }
            $posts = $post_query->get();
            if($user->userInfo->is_public === true){
                return response()->json([
                    
                    "success" => true,
                    "data" => $posts
                ],200);      
            }else{
                $user = Follow::where('following_id',$id)->where('follower_id',Auth::user()->id)->exists();
                if($user){
                    return response()->json([
                        "success" => true,
                        "data" => $posts
                    ],200);  
                }else{
                    return response()->json([
                        "success" => false,
                        "message" => "Private Account"
                    ],400);
                }
            }
        }else{
            return response()->json([
                "success" => false,
                "message" => "No user exists"
            ],400);
        }

    }

    public function postDetail( $id){
        $post = Post::where('id',$id)->first();
        if(isset($post)){
            $posts = Post::where('id',$id)->withCount('comments')->with('comments')->first();

            $user_activity = new UserActivities;
            $user_activity->user_id = Auth::user()->id;
            $user_activity->post_id = $post->id;
            $user_activity->save();

            return response()->json([
                "success" => true,
                "message" => "Post available",
                "data" => $posts
            ], 200);
        }else{
            return response()->json([
                "success" => false,
                "message" => "Unavailable post"
            ],400);
        }
    }

    public function createComment(Request $request, $id){
        $this->validate($request,[
            "comment" => "string"
        ]);

        $post = Post::where('id',$id)->first();
        $post_comment = new PostComment;
        $post_comment->post_id = $post->id;
        $post_comment->user_id = Auth::user()->id;
        $post_comment->comment = $request->comment;
        $post_comment->save();

        return response()->json([
            "success" => true,
            "message" => "Comment added",
            "data" => $post_comment
        ],200);
    }

    public function deleteComment($id){
        $comment = PostComment::where('post_id',$id)->exists();
        if($comment){
            $post_comment = PostComment::where('user_id',Auth::user()->id)->with('post')->first();
            if(isset($post_comment->id) || ($post_comment->post->user_id === Auth::user()->id)){
                $post_comment->delete();
                return response()->json([
                    "success" => true,
                    "message" => "Comment deleted"
                ],200);
            }else{
                return response()->json([
                    "success" => false,
                    "message" => "You can not delete this comment"
                ],400);
            }
        }else{
            return response()->json([
                "success" => false,
                "message" => "No comment exists"
            ],400);
        }
    }

    public function getComments($id){
        $post_comment = PostComment::where('post_id',$id)->exists();
        if($post_comment){
            $comments = PostComment::where('post_id',$id)->get();
            return response()->json([
                "success" => true,
                "message" => "Comments for this post",
                "data" => $comments
            ],200);
        }else{
            return response()->json([
                "success" => false,
                "message" => "No comments exists for this post"
            ],400);
        }

    }

    public function like($id){
        $post = Post::where('id',$id)->first();
        $post_like = new PostLike;
        $post_like->post_id = $post->id;
        $post_like->user_id = Auth::user()->id;
        $post_like->save();

        return response()->json([
            "success" => true,
            "message" => "Post liked"
        ],200);
    }

    public function unlike($id){
        $like = PostLike::where('user_id',Auth::user()->id)->where('post_id',$id)->first();
        if(isset($like)){
            $like->delete();
            return response()->json([
                "success" => true,
                "message" => "Post unliked"
            ],200);
        }
    }

}
