<?php
use Illuminate\Support\Facades\Route;
Route::group(['prefix'=>'version', 'middleware' =>['json.response'], 'namespace' => 'Account\Controllers'],function () {
    Route::get('/', 'UtilController@index');
});
Route::group(['prefix'=>'auth', 'middleware' =>['json.response'], 'namespace' => '\App\Http\Controllers\Api'],function (){
    Route::get('/token','ApiAuthController@jwtToken')->name('jwt.token');
});
Route::group(['middleware' =>['auth:sanctum','json.response'],'namespace' => 'Account\Controllers'],function(){


    Route::group(['prefix'=>'account'],function (){
        Route::get('/','AccountController@index')->name('api.account.list');
        Route::put('/{account_id}/user/{user_id}','AccountController@addUserToAccount')->name('api.account.addUserToAccount');
        Route::delete('/{account_id}/user/{user_id}','AccountController@removeUserToAccount')->name('api.account.deleteUserToAccount');
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
        //Route::get('/{id}/invoices','CreditCardController@invoices')->name('api.creditcard.invoices');
        Route::get('/{id}/invoices','CreditCardController@invoices')->name('api.creditcard.invoices');

    });

    Route::group(['prefix'=>'bill'],function (){
        Route::get('/account/{accountId}','BillController@index')->name('api.bill.list');
        Route::post('/account/{accountId}','BillController@insert')->name('api.bill.insert');
        Route::get('/account/{accountId}/between/{startDate}/{endDate}/pdf','BillController@generatePDFWithBillsBetween')->name('api.bill.between.pdf');

        Route::get('/account/{accountId}/between/{startDate}/{endDate}','BillController@between')->name('api.bill.between');
        Route::get('/account/{accountId}/periods','BillController@periods')->name('api.bill.period');

        Route::get('/{id}','BillController@show')->name('api.bill.show');
        Route::put('/{id}','BillController@update')->name('api.bill.update');
        Route::put('/{id}/pay','BillController@pay')->name('api.bill.pay');
        Route::delete('/{id}','BillController@delete')->name('api.bill.delete');
        Route::get('/{id}/invoices','BillController@invoices')->name('api.bill.invoices');


    });

    Route::group(['prefix'=>'invoice'],function (){
        Route::patch('/pay/{invoiceId}','InvoiceController@pay')->name('api.invoice.pay');
        Route::get('/{invoiceId}/pdf','InvoiceController@indexPdf')->name('api.invoice.index.pdf');
        Route::get('/{invoiceId}','InvoiceController@index')->name('api.invoice.index');
    });


});
