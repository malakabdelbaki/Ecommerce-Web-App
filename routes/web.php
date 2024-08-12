<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

///////////////////////
///


Route::post('/admin/login', [SessionController::class, 'login']);
Route::post('/admin/logout', [SessionController::class, 'logout'])->middleware('auth');


Route::post('/admin/register', [RegisteredUserController::class, 'store'])->middleware(['auth', AdminMiddleware::class]);
Route::post('/admin/categories', [CategoryController::class, 'store'])->middleware(['auth', AdminMiddleware::class]);
Route::get('/admin/categories/{category}/products', [ProductController::class, 'showByCategory'])->middleware(['auth', AdminMiddleware::class]);

Route::get('/csrf', function (){
    return response()->json([
        'csrfToken' => Session::token(),
    ]);
});
