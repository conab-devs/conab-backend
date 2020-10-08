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

Route::group(['middleware' => ['auth:api', 'bindings']], function () {
    Route::get('/conab/admins', 'AdminConabController@index');
    Route::get('/conab/admins/{id}', 'AdminConabController@show');
    Route::post('/conab/admins', 'AdminConabController@store');
    Route::put('/conab/admins', 'AdminConabController@update');
    Route::delete('/conab/admins/{user}', 'UserController');

    Route::get('cooperatives', 'CooperativeController@index');
    Route::get('cooperatives/{id}', 'CooperativeController@show');
    Route::post('cooperatives', 'CooperativeController@store');
    Route::delete('cooperatives/{id}', 'CooperativeController@destroy');
    Route::put('cooperatives/{id}', 'CooperativeController@update');
    Route::patch('cooperatives/{id}', 'CooperativeController@updateDap');

    Route::post('/uploads', 'UploadController@store');

    Route::get('/cooperatives/{cooperative}/admins', 'CooperativeAdminController@index');
    Route::get('/cooperatives/{cooperative}/admins/{id}', 'CooperativeAdminController@show');
    Route::post('/cooperatives/{cooperative}/admins', 'CooperativeAdminController@store');
    Route::put('/cooperatives/{cooperative}/admins/{id}', 'CooperativeAdminController@update');
    Route::delete('/users/{user}', 'UserController');

    Route::post('/categories', 'CategoryController@store');
    Route::get('/categories', 'CategoryController@index');
    Route::get('/categories/{category}', 'CategoryController@show');
    Route::put('/categories/{category}', 'CategoryController@update');
    Route::delete('/categories/{category}', 'CategoryController@destroy');
});

Route::post('/login', 'AuthController@login');
Route::post('/password/reset/request', 'AuthController@sendResetPasswordRequest');
Route::post('/password/reset', 'AuthController@resetPassword');


