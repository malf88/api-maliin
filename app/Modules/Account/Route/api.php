<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' =>'auth:sanctum','namespace' => 'Account\Controllers'],function(){


    Route::group(['prefix'=>'account'],function (){
        Route::get('/','AccountController@index');
    });
});
