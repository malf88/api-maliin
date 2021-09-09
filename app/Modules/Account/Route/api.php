<?php
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
    Route::group(['prefix'=>'creditcard'],function (){
        Route::get('/account/{accountId}','CreditCardController@index')->name('api.creditcard.list');
        Route::post('/account/{accountId}','CreditCardController@insert')->name('api.creditcard.insert');
        Route::get('/{id}','CreditCardController@show')->name('api.creditcard.show');
        Route::put('/{id}','CreditCardController@update')->name('api.creditcard.update');
        Route::delete('/{id}','CreditCardController@delete')->name('api.creditcard.delete');
        Route::get('/{id}/invoices','CreditCardController@invoices')->name('api.creditcard.invoices');

    });
});
