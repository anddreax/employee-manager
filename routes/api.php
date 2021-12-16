<?php

use App\Http\Controllers\usersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['api-auth', 'check-role'])->group(function(){
    Route::put('/new', [usersController::class, 'new']);
    Route::post('/login', [usersController::class, 'login'])->withoutMiddleware(['api-auth', 'check-role']);
    Route::get('/list', [usersController::class, 'list']);
    Route::get('/listbyID/{id}', [usersController::class, 'listbyID']);
    Route::get('/profile', [usersController::class, 'profile'])->withoutMiddleware(['check-role']);
    Route::post('/changeuser/{id}', [usersController::class, 'changeuser']);
    Route::post('/recoverypass', [usersController::class, 'recoverypass'])->withoutMiddleware(['api-auth', 'check-role']);
});




