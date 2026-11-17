<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/articles/{article:slug}', [HomeController::class, 'show'])->name('articles.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/manage/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/manage/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/manage/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/manage/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/manage/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/manage/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');

    Route::post('/articles/{article}/comments', [CommentController::class, 'store'])->name('comments.store');

    // VULNERABLE LAB: authentication middleware only; no admin role middleware.
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
});
