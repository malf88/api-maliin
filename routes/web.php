<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['prefix'=>'auth', 'middleware' =>['json.response'], 'namespace' => '\App\Http\Controllers\Api'],function (){
    Route::get('/login','ApiAuthController@login')->name('google.login');
    Route::get('/token','ApiAuthController@token')->name('google.token');
});
if(config('app.env') != 'production'){
    Route::get('/', function () {
        return view('app');
    });
}
