<?php

use App\Http\Controllers\API\V1\MediaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\UsersController;

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

Route::post('/register', [UsersController::class, 'register']);
Route::post('/login', [UsersController::class, 'Login']);
Route::post('/social', [UsersController::class, 'socialLoginRegister']);

// Route::post('/media/upload', [MediaController::class, 'uploadMedia']);


//AUTH
Route::group(['middleware' => 'auth:api'], function() {
    Route::middleware('ValidateUser:client')->group(function(){

        Route::prefix('me')->group(function () {
            Route::get('/details', [UsersController::class, 'GetUserInformation']);
        });

        Route::prefix('media')->group(function () {
            Route::post('/upload', [MediaController::class, 'uploadMedia']);
            Route::get('/list', [MediaController::class, 'getAllUpload']);
        });

    });
});
