<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\UserController;

Route::post('login', [ApiController::class, 'authenticate']);
Route::post('register', [ApiController::class, 'register']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('logout', [ApiController::class, 'logout']);
    Route::get('get_user', [UserController::class, 'get_user']);
    Route::post('edit_user', [UserController::class, 'edit_user']);
    Route::post('approve_user', [UserController::class, 'approve_user']);
    Route::post('delete_user', [UserController::class, 'delete_user']);
    
});