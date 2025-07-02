<?php

use Illuminate\Support\Facades\Route;


Route::view('/login', 'login')->name('login');
Route::view('/register', 'register')->name('register');




Route::view('/posts', 'posts.index')->name('posts.index'); 
Route::view('/posts/create', 'posts.create')->name('posts.create'); 
Route::view('/posts/{id}/edit', 'posts.edit')->name('posts.edit'); 


Route::view('/users', 'users.index')->name('users.index'); 
Route::view('/users/create', 'users.create')->name('users.create'); 
Route::view('/users/{id}/edit', 'users.edit')->name('users.edit'); 

Route::view('/categories', 'categories.index')->name('categories.index'); 
Route::view('/categories/create', 'categories.create')->name('categories.create'); 
Route::view('/categories/{id}/edit', 'categories.edit')->name('categories.edit'); 

