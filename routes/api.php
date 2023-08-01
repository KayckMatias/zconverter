<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DownloadController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\ConvertController;
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

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    Route::post('broadcasting/auth', function (Request $request) {
        $key = config('broadcasting.connections.pusher.key');
        $secret = config('broadcasting.connections.pusher.secret');
        $channel_name = $request->channel_name;
        $socket_id = $request->socket_id;
        $string_to_sign = $socket_id . ':' . $channel_name;
        $signature = hash_hmac('sha256', $string_to_sign, $secret);

        return response()->json(['auth' => $key . ':' . $signature]);
    });

    Route::group(['prefix' => 'downloader'], function () {
        Route::get('list', [DownloadController::class, 'list']);
        Route::post('new', [DownloadController::class, 'new']);
        Route::post('retry/{id}', [DownloadController::class, 'retry']);
        Route::delete('delete/{id}', [DownloadController::class, 'delete']);
    });

    Route::group(['prefix' => 'converter'], function () {
        Route::get('list', [ConvertController::class, 'list']);
        Route::post('convert/{id}/to/{extension}', [ConvertController::class, 'new']);
        Route::post('retry/{id}', [ConvertController::class, 'retry']);
        Route::delete('delete/{id}', [ConvertController::class, 'delete']);
        Route::get('options/{id}/to/{extension}', [ConvertController::class, 'getOptions']);
    });

    Route::group(['prefix' => 'file'], function () {
        Route::get('convert/available/{id}', [FileController::class, 'availableConvert']);
        Route::get('download/{id}/conversion/{convert_id}/generate', [FileController::class, 'generateDownloadLink']);
    });
});
