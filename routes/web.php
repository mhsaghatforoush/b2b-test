<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->controller(PaymentController::class)->prefix('payment')->name('payment.')->group(function () {

    Route::get('/{book_id}', 'pay')->whereNumber('book_id')->name('pay');
	Route::get('/result/{invoiceNumber}', 'paymentResult')->name('result');

});
