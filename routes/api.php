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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('cooperatives', 'CooperativeController@index');
Route::get('cooperatives/{id}', 'CooperativeController@show');
Route::post('cooperatives', 'CooperativeController@store');
Route::delete('cooperatives/{id}', 'CooperativeController@destroy');
Route::put('cooperatives/{id}', 'CooperativeController@update');
Route::patch('cooperatives/{id}', 'CooperativeController@updateDap');
