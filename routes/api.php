<?php

use App\Http\Controllers\api\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('stk/callback', [PaymentController::class, 'stkCallback'])->name('stk.callback');