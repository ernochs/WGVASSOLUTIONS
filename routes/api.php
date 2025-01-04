<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\USSD\UssdController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/ussd', [UssdController::class, 'handleUssd']);
Route::post('/ussd-cid', [UssdController::class, 'handleUssdCid']);