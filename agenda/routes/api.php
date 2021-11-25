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

// Auth
Route::group(['prefix' => '/auth', 'as' => 'auth.login.'], function () {
    Route::post('/auth/login', [
        'as'   => 'login',
        'uses' => 'LoginController@login'
    ]);
});

// Usuário
Route::group(['prefix' => '/users', 'as' => 'users.'], function () {
    Route::post('/register', [
        'as'   => 'register',
        'uses' => 'UserController@register'
    ]);
    Route::get('/', [
        'as'   => '',
        'uses' => 'UserController@index'
    ])->middleware('auth');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
