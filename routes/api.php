<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api', 'bindings']], function () {
    Route::get('/conab/admins', 'ConabAdminController@index');
    Route::get('/conab/admins/{id}', 'ConabAdminController@show');
    Route::post('/conab/admins', 'ConabAdminController@store');
    Route::put('/conab/admins', 'ConabAdminController@update');

    Route::get('cooperatives', 'CooperativeController@index');
    Route::get('cooperatives/{cooperative}', 'CooperativeController@show');
    Route::post('cooperatives', 'CooperativeController@store');
    Route::delete('cooperatives/{cooperative}', 'CooperativeController@destroy');
    Route::put('cooperatives/{cooperative}', 'CooperativeController@update');
    Route::patch('cooperatives/{cooperative}', 'CooperativeController@updateDap');

    Route::post('/uploads', 'UploadController@store');

    Route::get('/cooperatives/{cooperative}/admins', 'CooperativeAdminController@index');
    Route::get('/cooperatives/{cooperative}/admins/{id}', 'CooperativeAdminController@show');
    Route::post('/cooperatives/{cooperative}/admins', 'CooperativeAdminController@store');
    Route::put('/cooperatives/{cooperative}/admins/{id}', 'CooperativeAdminController@update');

    Route::post('/categories', 'CategoryController@store');
    Route::get('/categories', 'CategoryController@index');
    Route::get('/categories/{category}', 'CategoryController@show');
    Route::put('/categories/{category}', 'CategoryController@update');
    Route::delete('/categories/{category}', 'CategoryController@destroy');

    Route::get('/products', 'ProductController@index');
    Route::get('/products/{product}', 'ProductController@show');
    Route::post('/products', 'ProductController@store');
    Route::delete('/products/{product}', 'ProductController@destroy');
    Route::put('/products/{product}', 'ProductController@update');

    Route::post('/product-carts', 'ProductCartController@store');
    Route::patch('/product-carts/{productCart}', 'ProductCartController@update');
    Route::delete('/product-carts/{productCart}', 'ProductCartController@destroy');

    Route::get('/carts', 'CartController@index');
    Route::get('/carts/{id}', 'CartController@show');

    Route::put('/users', 'UserController@update');
    Route::get('/users', 'UserController@show');
    Route::delete('/users/{user}', 'UserController@destroy');

    Route::put('/phones', 'PhoneController@update');
    Route::post('/phones', 'PhoneController@store');
    Route::get('/phones', 'PhoneController@index');

    Route::post('/addresses', 'AddressController@store');
    Route::put('/addresses', 'AddressController@update');
    Route::get('/addresses', 'AddressController@index');
});

Route::post('/users', 'UserController@store');
Route::post('/login', 'AuthController@login');
Route::post('/password/reset/request', 'AuthController@sendResetPasswordRequest');
Route::post('/password/reset', 'AuthController@resetPassword');
