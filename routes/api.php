<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\IntrestController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TrainerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->group(function () {
    //updation 
    Route::patch('/update-users', [UserController::class, 'user_update']);
    Route::post('/edit-profile/{id}', [UserController::class, 'edit_profile']);

    //Get all category of intrests
    Route::get('/get-intrests', [IntrestController::class, 'get_intrests_details']);

    //Update user intrest
    Route::patch('/update-user-intrest', [UserController::class, 'update_user_intrest']);

    //User Trainer
    Route::post('/connect-trainer/{id}', [TrainerController::class, 'connect_trainer']);

    //Follow
    Route::post('/follow-up/{id}', [FollowController::class, 'follow_up']);
    Route::delete('/unfollow/{id}', [FollowController::class, 'unfollow_user']);

    //Post 
    Route::post('/create-post', [PostController::class, 'create_post']);
    Route::patch('/update-post/{id}', [PostController::class, 'update_post']);
    Route::delete('/delete-post/{id}', [PostController::class, 'delete_post']);
    Route::get('/get-post/{id}', [PostController::class, 'get_post_detail']);
    Route::get('/get-posts/{id}', [PostController::class, 'get_posts']);

    //Post feed
    Route::get('/post-feed', [PostController::class, 'get_post_feed']);

    //Post comments
    Route::post('/create-comment/{id}', [PostController::class, 'create_comment']);
    Route::delete('/delete-comment/{id}', [PostController::class, 'delete_comment']);
    Route::get('/get-comments/{id}', [PostController::class, 'get_comments']);

    //Post likes
    Route::post('/like-post/{id}', [PostController::class, 'like_post']);
    Route::delete('/unlike-post/{id}', [PostController::class, 'unlike_post']);

    //Global search
    Route::get('/globalSearch', [UserController::class, 'global_search']);

    //Recently played
    Route::get('recentlyplayed', [ActivityController::class, 'recently_played']);

    //Suggestions for you
    Route::get('suggestionPost', [ActivityController::class, 'suggestion_post']);

    //Admin middleware
    Route::middleware('admin')->group(function () {
        //Intrests
        Route::post('/create-intrests', [IntrestController::class, 'create_intrest']);
        Route::patch('/update-intrests/{id}', [IntrestController::class, 'update_intrest']);
        Route::get('/get-intrests/{id}', [IntrestController::class, 'get_intrest']);
        Route::delete('/delete-intrests/{id}', [IntrestController::class, 'delete_intrest']);
        Route::delete('/deleteIntrests', [IntrestController::class, 'delete_intrests']);

        //Users
        Route::get('/get-user-details/{id}', [UserController::class, 'get_user_details']);
        Route::get('/getUsers', [UserController::class, 'get_users']);
        Route::delete('/delete-user/{id}', [UserController::class, 'delete_user']);
        Route::delete('/delete-all-users', [UserController::class, 'delete_all_users']);
    });
});

//Signup and login
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);

// Forget password and reset password
Route::post('/forget-password', [AuthController::class, 'forget_password']);
Route::post('/reset-password', [AuthController::class, 'reset_password']);

//create role
Route::post('/create-roles', [RoleController::class, 'create_role']);

//popular (posts)
Route::get('popular-posts', [ActivityController::class, 'popular_post']);

//featured post for you
Route::get('featured-posts', [ActivityController::class, 'featured_post']);

//post details wrt to intrests
Route::get('popular-post-intrests/{id}',[ActivityController::class, 'popular_post_intrest']);
Route::get('new-post-intrests/{id}',[ActivityController::class,'new_post_intrest']);
Route::get('trending-post-intrests/{id}',[ActivityController::class,'trending_post_intrest']);

//post details wrt to users
Route::get('new-posts', [ActivityController::class, 'new_post']);
Route::get('trending-posts',[ActivityController::class, 'trending_post']);

//Dashboard
Route::get('dashBoard',[DashboardController::class, 'dashBoard']);

//popular or top trainers
Route::get('trainer-details/{id}',[DashboardController::class, 'trainer_details']);

Route::get('/roless',[RoleController::class,'role_details']);



