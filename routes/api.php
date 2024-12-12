<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

//get all posts, no need to authenticate
Route::get('/all/posts',[PostController::class,'getAllPosts']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout',[AuthController::class,'logout']);

    //Blog api end point
    Route::post('/add/post', [PostController::class,'addNewPost']);
    //edit approach 1
    Route::post('/edit/post', [PostController::class,'editPost']);
    //edit approach 2
    Route::post('/edit/post/{post_id}', [PostController::class,'editPost2']);
});