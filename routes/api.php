<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->group(function () {
    Route::get('/hello', function () {
        return 'hello';
    });
    Route::get('/conab/admins', 'AdminConabController@index');
    Route::get('/conab/admins/{id}', 'AdminConabController@show');
    Route::post('/conab/admins', 'AdminConabController@store');
    Route::put('/conab/admins', 'AdminConabController@update');
    Route::delete('/conab/admins/{id}', 'AdminConabController@destroy');

    Route::get('cooperatives', 'CooperativeController@index');
    Route::get('cooperatives/{id}', 'CooperativeController@show');
    Route::post('cooperatives', 'CooperativeController@store');
    Route::delete('cooperatives/{id}', 'CooperativeController@destroy');
    Route::put('cooperatives/{id}', 'CooperativeController@update');
    Route::patch('cooperatives/{id}', 'CooperativeController@updateDap');

    Route::post('/uploads', 'UploadController@store');
});

Route::post('/login', 'AuthController@login');

