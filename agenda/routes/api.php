<?php

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

// Usuário sem autenticação
Route::group(['prefix' => '/users', 'as' => 'users.'], function () {
    Route::post('/register', [
        'as'   => 'register',
        'uses' => 'UserController@register'
    ]);
    Route::post('/login', [
        'as'   => 'login',
        'uses' => 'UserController@login'
    ]);
});

// Usuário com autenticação
Route::group(['middleware' => ['api.token.user']], function () {
    //Contato
    Route::group(['prefix' => '/contacts', 'as' => 'contacts.'], function () {
        Route::get('/', [
            'as'   => 'index',
            'uses' => 'ContactController@index'
        ]);
        Route::get('/{id}', [
            'as'   => 'show',
            'uses' => 'ContactController@show'
        ]);
        Route::post('/', [
            'as'   => 'store',
            'uses' => 'ContactController@store'
        ]);
        Route::patch('/{id}', [
            'as'   => 'update',
            'uses' => 'ContactController@update'
        ]);
        Route::delete('/{id}', [
            'as'   => 'delete',
            'uses' => 'ContactController@delete'
        ]);
    });

    //Endereço
    Route::group(['prefix' => '/address', 'as' => 'address.'], function () {
        Route::patch('/{id}', [
            'as'   => 'update',
            'uses' => 'AddressController@update'
        ]);
        Route::delete('/{id}', [
            'as'   => 'delete',
            'uses' => 'AddressController@delete'
        ]);
        Route::get('/find-by-postal-code/{postalCode}', [
            'as'   => 'find-by-postal-code',
            'uses' => 'AddressController@findByPostalCode'
        ]);
    });

    //Tag
    Route::group(['prefix' => '/tags', 'as' => 'tags.'], function () {
        Route::get('/', [
            'as'   => 'index',
            'uses' => 'TagController@index'
        ]);
        Route::post('/', [
            'as'   => 'store',
            'uses' => 'TagController@store'
        ]);
        Route::patch('/{id}', [
            'as'   => 'update',
            'uses' => 'TagController@update'
        ]);
        Route::delete('/{id}', [
            'as'   => 'delete',
            'uses' => 'TagController@delete'
        ]);
        Route::post('/{id}/attach', [
            'as'   => 'attach',
            'uses' => 'TagController@attach'
        ]);
        Route::post('/{id}/detach', [
            'as'   => 'detach',
            'uses' => 'TagController@detach'
        ]);
    });

    //Usuário
    Route::group(['prefix' => '/users', 'as' => 'users.'], function () {
        Route::get('/', [
            'as'   => 'show',
            'uses' => 'UserController@show'
        ]);
        Route::patch('/', [
            'as'   => 'update',
            'uses' => 'UserController@update'
        ]);
        Route::get('/logout', [
            'as'   => 'logout',
            'uses' => 'UserController@logout'
        ]);
        Route::get('/change-password', [
            'as'   => 'change-password',
            'uses' => 'UserController@tokenToChangePassword'
        ]);
    });
});
