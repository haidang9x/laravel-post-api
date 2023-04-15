<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

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
use App\Http\Controllers\API\AuthController;
//register
Route::post('/register', [AuthController::class, 'register']);
//login
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/profile', function(Request $request) { 
        return auth()->user();
    });
    // logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::group(['middleware' => ['api']], function () {

    Route::resource('post', PostController::class);
});