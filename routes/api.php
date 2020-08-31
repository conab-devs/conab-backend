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

Route::get('cooperatives', 'Api\\CooperativeController@index');
Route::get('cooperative/{id}', 'Api\\CooperativeController@show');
Route::delete('cooperative/{id}', 'Api\\CooperativeController@destroy');
Route::post('cooperative', 'Api\\CooperativeController@store');
Route::put('cooperative/{id}', 'Api\\CooperativeController@update');
