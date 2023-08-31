<?php

use App\Http\Controllers\KmlFileController;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('kmlFiles', [KmlFileController::class,'index'])->name('kmlFiles.index');
Route::post('kmlFiles', [KmlFileController::class,'store'])->name('kmlFiles.store');
Route::post('kmlFiles/delete', [KmlFileController::class,'destroy'])->name('kmlFiles.delete');
