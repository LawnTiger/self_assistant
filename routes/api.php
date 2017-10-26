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

// test
Route::post('/register1', 'Api\RegisterController@index1');
Route::post('/login1', 'Api\LoginController@index1');

Route::group(['middleware' => ['auth:api'], 'namespace' => 'Api'], function () {
    Route::resource('friends', 'FriendController', ['only' => ['index', 'store', 'update', 'destroy']]);
    Route::resource('groups', 'GroupController', ['only' => ['index', 'store', 'update', 'destroy']]);
});
