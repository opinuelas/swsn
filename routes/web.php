<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PictureController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PictureController::class, 'index'])->name('dashboard');
    Route::post('/pictures', [PictureController::class, 'store'])->name('pictures.store');
    Route::get('/pictures/{picture}', [PictureController::class, 'show'])->name('pictures.show');
    Route::post('/pictures/{picture}/like', [LikeController::class, 'toggle'])->name('pictures.like');
    Route::post('/pictures/{picture}/comments', [CommentController::class, 'store'])->name('pictures.comments.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/picture/{picture}', [ProfileController::class, 'updatePicture'])->name('profile.picture.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'send'])->name('messages.send');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}', [MessageController::class, 'store'])->name('messages.store');
});

require __DIR__.'/auth.php';
