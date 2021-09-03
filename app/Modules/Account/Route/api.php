<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' =>['auth:sanctum','json.response'],'namespace' => 'Account\Controllers'],function(){


    Route::group(['prefix'=>'account'],function (){
        Route::get('/','AccountController@index')->name('api.account.list');
        Route::post('/','AccountController@insert')->name('api.account.insert');
        Route::get('/{id}','AccountController@show')->name('api.account.show');
        Route::put('/{id}','AccountController@update')->name('api.account.update');
        Route::delete('/{id}','AccountController@delete')->name('api.account.delete');

    });
});
