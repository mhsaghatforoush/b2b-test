<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
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

// user routes
Route::middleware('auth:sanctum')->controller(UserController::class)->prefix('user')->name('user.')->group(function () {

    Route::post('/', 'show')->name('show');
    Route::post('/commision/report', 'commision_report')->name('commision.report');

});


// book routes
Route::middleware('auth:sanctum')->controller(BookController::class)->prefix('book')->name('book.')->group(function () {

    Route::post('/list', 'list')->name('list');
    Route::post('/{id}', 'show')->whereNumber('id')->name('show');

});