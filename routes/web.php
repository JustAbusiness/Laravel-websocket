<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [App\Http\Controllers\IndexController::class, 'index']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat');
    Route::get('/userlist', [App\Http\Controllers\ChatController::class, 'user_list'])->name('user.list');
    Route::get('/usermessage/{id}', [App\Http\Controllers\ChatController::class, 'user_message'])->name('user.message');
    Route::post('/sendmessage', [App\Http\Controllers\ChatController::class, 'send_message'])->name('user.send_message');
    Route::get('/fetchmessage', [App\Http\Controllers\ChatController::class, 'fetch_messages'])->name('user.fecth_messages');
    Route::get('/deletesinglemessage/{id}', [App\Http\Controllers\ChatController::class, 'delete_single_message'])->name('user.delete_single_message');
    Route::get('/deleteallmessage/{id}', [App\Http\Controllers\ChatController::class, 'delete_all_message'])->name('user.delete_all_message');
    Route::get('/searchuser', [App\Http\Controllers\ChatController::class, 'search_user'])->name('user.search');
    Route::get('/logout',[App\Http\Controllers\ChatController::class, 'logout'] )->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
