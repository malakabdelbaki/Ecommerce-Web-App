<?php

use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SessionController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;


Route::post('/admin/login', [SessionController::class, 'login']);
Route::post('/admin/logout', [SessionController::class, 'logout'])->middleware(AuthMiddleware::class, AdminMiddleware::class);


Route::post('/admin/categories', [CategoryController::class, 'store'])->middleware([AuthMiddleware::class, AdminMiddleware::class]);
Route::post('/admin/products', [ProductController::class, 'store'])->middleware([AuthMiddleware::class, AdminMiddleware::class]);

Route::get('/admin/orders', [OrdersController::class, 'index'])->middleware([AuthMiddleware::class, AdminMiddleware::class]);

Route::get('/csrf', function (){
    return response()->json([
        'csrfToken' => Session::token(),
    ]);
});
