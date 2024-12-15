<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ServiceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

//get all posts, no need to authenticate
Route::get('/posts',[PostController::class,'getAllPosts']);
Route::get('/abouts',[AboutController::class,'getAllAbouts']);
Route::get('/services',[ServiceController::class,'getAllServices']);
Route::get('/home',[HomeController::class,'getHome']);
Route::get('/single/post/{post_id}',[PostController::class,'getPost']);
Route::get('/service/{service_id}',[ServiceController::class,'getService']);

//Athorized Apis
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout',[AuthController::class,'logout']);

    //Blog api end point
    Route::post('/add/post', [PostController::class,'addNewPost']);
    //Home Create api end point
    Route::post('/home', [HomeController::class,'addNewHome']);
    //About Create api end point
    Route::post('/about', [AboutController::class,'addNewAbout']);
    //Service Create api end point
    Route::post('/service', [ServiceController::class,'addNewService']);
    
    //edit approach 1
    // Route::post('/edit/post', [PostController::class,'editPost']);
    //edit approach 2
    Route::put('/post/{post_id}', [PostController::class,'editPost']);
    //edit home
    Route::put('/home/{home_id}', [HomeController::class,'editHome']);
    //edit about
    Route::put('/about/{about_id}', [AboutController::class,'editAbout']);
    //edit about
    Route::put('/service/{service_id}', [ServiceController::class,'editService']);
    
    
    
     //delete post
    Route::delete('/post/{post_id}',[PostController::class,'deletePost']);
    //delete post
    Route::delete('/service/{service_id}',[ServiceController::class,'deleteService']);

    //Comment
    Route::post('/comment',[CommentController::class,'postComment']);
    Route::post('/like',[LikesController::class,'likePost']);
});