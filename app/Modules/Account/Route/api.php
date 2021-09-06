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
    Route::group(['prefix'=>'category'],function (){
        Route::get('/','CategoryController@index')->name('api.category.list');
        Route::post('/','CategoryController@insert')->name('api.category.insert');
        Route::get('/{id}','CategoryController@show')->name('api.category.show');
        Route::put('/{id}','CategoryController@update')->name('api.category.update');
        Route::delete('/{id}','CategoryController@delete')->name('api.category.delete');

    });
});
