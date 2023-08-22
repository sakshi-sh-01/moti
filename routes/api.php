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
    Route::patch('/update-users', [UserController::class, 'update']);
    Route::post('/edit-profile/{id}', [UserController::class, 'editProfile']);

    //Get all category of intrests
    Route::get('/get-intrests', [IntrestController::class, 'listing']);

    //Update user intrest
    Route::patch('/update-user-intrest', [UserController::class, 'updateUserIntrest']);

    //User Trainer
    Route::post('/connect-trainer/{id}', [TrainerController::class, 'connect']);

    //Follow
    Route::post('/follow-up/{id}', [FollowController::class, 'follow']);
    Route::delete('/unfollow/{id}', [FollowController::class, 'unfollow']);

    //Post 
    Route::post('/create-post', [PostController::class, 'create']);
    Route::patch('/update-post/{id}', [PostController::class, 'update']);
    Route::delete('/delete-post/{id}', [PostController::class, 'delete']);
    Route::get('/get-post/{id}', [PostController::class, 'postDetail']);
    Route::get('/get-posts/{id}', [PostController::class, 'details']);

    //Post feed
    Route::get('/post-feed', [PostController::class, 'feed']);

    //Post comments
    Route::post('/create-comment/{id}', [PostController::class, 'createComment']);
    Route::delete('/delete-comment/{id}', [PostController::class, 'deleteComment']);
    Route::get('/get-comments/{id}', [PostController::class, 'getComments']);

    //Post likes
    Route::post('/like-post/{id}', [PostController::class, 'like']);
    Route::delete('/unlike-post/{id}', [PostController::class, 'unlike']);

    //Global search
    Route::get('/globalSearch', [UserController::class, 'search']);

    //Recently played
    Route::get('recentlyplayed', [ActivityController::class, 'recentlyPlayed']);

    //Suggestions for you
    Route::get('suggestionPost', [ActivityController::class, 'suggestionPost']);

    //Admin middleware
    Route::middleware('admin')->group(function () {
        //Intrests
        Route::post('/create-intrests', [IntrestController::class, 'create']);
        Route::patch('/update-intrests/{id}', [IntrestController::class, 'update']);
        Route::get('/get-intrests/{id}', [IntrestController::class, 'detail']);
        Route::delete('/delete-intrests/{id}', [IntrestController::class, 'delete']);

        //Users
        Route::get('/get-user-details/{id}', [UserController::class, 'details']);
        Route::delete('/delete-user/{id}', [UserController::class, 'delete']);
    });
});

//Signup and login
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);

// Forget password and reset password
Route::post('/forget-password', [AuthController::class, 'forgetPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

//create role
Route::post('/create-roles', [RoleController::class, 'create']);

//popular (posts)
Route::get('popular-posts', [ActivityController::class, 'popularPost']);

//featured post for you
Route::get('featured-posts', [ActivityController::class, 'featuredPost']);

//post details wrt to intrests
Route::get('popular-post-intrests/{id}',[ActivityController::class, 'popularPostIntrest']);
Route::get('new-post-intrests/{id}',[ActivityController::class,'newPostIntrest']);
Route::get('trending-post-intrests/{id}',[ActivityController::class,'trendingPostIntrest']);

//post details wrt to users
Route::get('new-posts', [ActivityController::class, 'newPost']);
Route::get('trending-posts',[ActivityController::class, 'trendingPost']);

//Dashboard
Route::get('dashBoard',[DashboardController::class, 'dashBoard']);

//popular or top trainers
Route::get('trainer-details/{id}',[TrainerController::class, 'topTrainers']);
