<?php

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

// login and register
Route::post('/register', 'Api\RegisterController@index');
Route::post('/login', 'Api\LoginController@index');

Route::group(['middleware' => ['auth:api'], 'namespace' => 'Api'], function () {
    Route::resource('friends', 'FriendController', ['only' => ['index', 'store', 'update', 'destroy']]);
    Route::resource('groups', 'GroupController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::resource('moments', 'MomentController', ['only' => ['index', 'store', 'destroy']]);

    Route::get('data/qiniu/token', 'DataController@qiniu_token');
});
