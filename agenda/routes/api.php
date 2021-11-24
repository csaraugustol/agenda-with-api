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

Route::get('/test', [
    'as'   => 'test',
    'uses' => 'TestController@test'
]);

// Usuário
Route::group(['prefix' => '/users', 'as' => 'users.'], function () {
    Route::post('/register', [
        'as'   => 'register',
        'uses' => 'UserController@register'
    ]);
    Route::patch('/{id}', [
        'as' => 'update',
        'uses' => 'UserController@update'
    ]);
    Route::get('/{id}', [
        'as' => 'show',
        'uses' => 'UserController@show'
    ]);
    Route::delete('/{id}', [
        'as' => 'destroy',
        'uses' => 'UserController@destroy'
    ]);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
