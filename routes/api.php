<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function() {
    Route::apiResource(
        'categories', 
        \App\Http\Controllers\API\CategoryController::class
    );
    Route::apiResource(
        'products',
        \App\Http\Controllers\API\ProductController::class
    );
});
