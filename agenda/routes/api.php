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
    });

    //Tag
    Route::group(['prefix' => '/tags', 'as' => 'tags.'], function () {
        Route::post('/', [
            'as'   => 'store',
            'uses' => 'TagController@store'
        ]);
        Route::patch('/{id}', [
            'as'   => 'update',
            'uses' => 'TagController@update'
        ]);
    });
});
