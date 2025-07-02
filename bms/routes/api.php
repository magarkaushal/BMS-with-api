<?php
use Illuminate\Http\Request;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/public/posts', [PublicController::class, 'posts']);



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/categories/export', [CategoryController::class, 'export']);
    Route::get('posts/export', [PostController::class, 'export']);

    Route::apiResource('posts', PostController::class);
    Route::apiResource('categories', CategoryController::class);

    Route::apiResource('users', UserController::class);


    Route::post('categories/import', [CategoryController::class, 'import']);

});




