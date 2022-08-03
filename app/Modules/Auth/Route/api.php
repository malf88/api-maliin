<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'auth', 'middleware' =>['json.response'], 'namespace' => 'Auth\Controllers'],function (){
    Route::get('/google','AuthGoogleController@authenticate')->name('jwt.token');
});

Route::group(['prefix'=>'auth','middleware' =>['json.response','auth:sanctum'],'namespace' => 'Auth\Controllers'],function(){

    Route::put('/logout', 'AuthGoogleController@logout');
    Route::patch('/google/update', 'AuthGoogleController@updateEmail');

});
