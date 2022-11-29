<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NextController;
use App\Http\Middleware\CustomCors;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|NextController
*/


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware([CustomCors::class])->group(function () {
    Route::get('/index', [NextController::class, 'index']);
    Route::get('/getSearchOptions', [NextController::class, 'getSearchOptions'])->name('getSearchOptions');
    Route::get('/getData', [NextController::class, 'getData'])->name('getData');
    Route::get('/getDataWithText', [NextController::class, 'getDataWithText'])->name('getDataWithText');
});