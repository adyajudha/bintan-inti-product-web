<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('products', [ProductController::class, 'index']);
Route::get('products/Edit/{id}/', [ProductController::class, 'edit']);
Route::post('products/Store', [ProductController::class, 'store']);
Route::get('products/Delete/{id}', [ProductController::class, 'destroy']);
