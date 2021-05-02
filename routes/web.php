<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
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

Route::group(['middleware' => ['guest']], function() {
    Route::get('/', [AuthController::class, 'login_show'])->name('login_show');
    Route::post('login', [AuthController::class, 'login'])->name('login');
});

Route::group(['middleware' => ['auth']], function() {
    Route::get('home', function() {
        return view('home');
    })->name('home');
});
