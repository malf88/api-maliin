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
Route::group(['middleware' =>['json.response','auth:sanctum'],'namespace' => '\App\Http\Controllers\Api'],function(){
    Route::get('/user', 'ApiAuthController@getUser');

    Route::group(['prefix'=>'account'],function (){
        Route::get('/');
    });
});
Route::group(['middleware' =>['api','json.response'],'namespace' => '\App\Http\Controllers\Api'],function() {
    Route::post('/token', 'ApiAuthController@login');


});
