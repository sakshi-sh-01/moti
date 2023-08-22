<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\UserActivities;
use Illuminate\Http\Request;
use App\Models\Follow;
use App\Models\UserInfo;
use App\Models\UserIntrest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActivityController extends Controller
{
    public function recently_played()
    {
        $user_ac = UserActivities::where('user_id', Auth::user()->id)->first();
        if (isset($user_ac)) {
            $user_act = $user_ac->latest()->get();
            return response()->json([
                "success" => true,
                "data" => $user_act
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "No activities"
            ], 400);
        }
    }

    public function popularPost()
    {
        $post_query = Post::query()->withCount('userActivities')
            ->orderBy('user_activities_count', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            "success" => true,
            "data" => $post_query
        ],200);
    }

    public function newPost()
    {
        $user_follow = Follow::where('follower_id', '1')->pluck('following_id');
        echo "....." . $user_follow;
        $posts = Post::whereIn('user_id', $user_follow)->get()->pluck('id');
        echo "....." . $posts;
        $post_query = Post::query();
        $post_query->whereHas('userActivities', function ($query) use ($posts) {
            $query->whereNot('user_id', '1')->whereIn('post_id', $posts);
        })->get();

        $posts = Post::latest()->take(5)->get();
        return response()->json([
            "success" => true,
            "data" => $post_query
        ],200);
    }

    public function suggestion_post()
    {
        $users = UserInfo::where('is_public', true)->get();
        echo "...." . $users;
        $userInterests = UserIntrest::where('user_id', Auth::user()->id)->pluck('intrest_id');
        $suggestedPost = Post::whereIn('intrest_id', $userInterests)->whereIn('user_id', $users)->first();
        echo "...." . Auth::user()->id;
        return response()->json([
            "success" => true,
            "data" => $suggestedPost
        ],200);
    }

    public function featured_post()
    {
        $users = UserInfo::where('is_public', true)->get();
        $post = Post::query()->whereIn('user_id', $users)->withCount('userActivities')->orderBy('user_activities_count', 'desc')->first();
        return response()->json([
            "success" => true,
            "data" => $post
        ],200);
    }

    public function popular_post_intrest($id)
    {
        $post = Post::query()->where('intrest_id', $id)->withCount('intrest')
            ->orderBy('intrest_count', 'desc')->take(5)->get();
        return response()->json([
            "success" => true,
            "data" => $post
        ],200);
    }

    public function new_post_intrest($id)
    {
        $post = Post::where('intrest_id', $id)->latest()->take(5)->get();
        return response()->json([
            "success" => true,
            "data" => $post
        ],200);
    }

    public function trending_post()
    {
        $trendingTimeframe = Carbon::now()->subDays(7);

        $trending_posts = Post::query()
            ->withCount([
                'userActivities' => function ($query) use ($trendingTimeframe) {
                    $query->where('created_at', '>=', $trendingTimeframe);
                }
            ])
            ->orderBy('user_activities_count', 'desc')
            ->take(5)
            ->get();

            return response()->json([
                "success" => true,
                "data" => $trending_posts
            ],200);
    }

    public function trending_post_intrest($id){
        $trendingTimeframe = Carbon::now()->subDays(7);

        $posts = Post::query()->where('intrest_id', $id)->withCount([
            'userActivities' => function ($query) use ($trendingTimeframe) {
                $query->where('created_at', '>=', $trendingTimeframe);
            }
        ])
        ->orderBy('user_activities_count', 'desc')
        ->take(5)
        ->get();

        return response()->json([
            "success" => true,
            "data" => $posts
        ], 200);
    }
}
