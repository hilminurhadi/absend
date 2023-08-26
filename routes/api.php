<?php

use App\Http\Controllers\AbonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/version_name', [AbonController::class, 'version_name'])->name('version_name');
Route::get('/isUseDeviceId/{deviceid}', [AbonController::class, 'isUseDeviceId'])->name('isUseDeviceId');
Route::post('/login', [AbonController::class, 'login'])->name('login');
Route::post('/insertDeviceId//', [AbonController::class, 'insertDeviceId'])->name('insertDeviceId');
Route::get('/checkConnection', [AbonController::class, 'checkConnection'])->name('checkConnection');
Route::post('/insertAbsenNew', [AbonController::class, 'insertAbsenNew'])->name('insertAbsenNew');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
