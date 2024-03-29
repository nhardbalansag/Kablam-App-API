<?php

use App\Http\Controllers\API\V1\MediaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\UsersController;
use App\Http\Controllers\API\V1\ContactController;
use App\Http\Controllers\API\V1\MediaAlbumController;

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

        Route::prefix('my-kablam')->group(function () {
            Route::get('/upload', [MediaController::class, 'getAllUserUploadedMedias']);
        });

        Route::prefix('media')->group(function () {
            Route::post('/upload', [MediaController::class, 'uploadMedia']);
            Route::get('/list', [MediaController::class, 'getAllUpload']);
            Route::get('/premiere/{id}', [MediaController::class, 'getUploadMediaByCalendarId']);
            Route::delete('/delete', [MediaController::class, 'deleteMedia']);
        });

        Route::prefix('album')->group(function () {
            Route::post('/create-and-move-media', [MediaAlbumController::class, 'CreateAlbumAndMoveMediaToAlbum']);
            Route::post('/move-media', [MediaAlbumController::class, 'MoveToAlbum']);
            Route::get('/get-related-albums/{id}', [MediaAlbumController::class, 'getRelatedAlbums']);
            Route::get('/get-user-album', [MediaAlbumController::class, 'getUserAlbums']);
        });

        Route::prefix('contact-us')->group(function () {
            Route::post('/email', [ContactController::class, 'SendEmail']);
        });

    });
});

