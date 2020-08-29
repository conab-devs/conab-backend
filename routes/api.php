<?php

use Illuminate\Http\Request;
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
    Route::get('/conab/admins', 'AdminConabController@index');
    Route::post('/conab/admins', 'AdminConabController@store');
    Route::put('/conab/admins/{id}', 'AdminConabController@update');
    Route::delete('/conab/admins/{id}', 'AdminConabController@destroy');
});


