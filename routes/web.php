<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', function () {
    return view('welcome');
});

// login and register
Route::get('/login', 'Auth\LoginController@index')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/register', 'Auth\RegisterController@index');
Route::post('/register', 'Auth\RegisterController@register');

// user
Route::group(['middleware' => 'auth'], function() {
    Route::get('/user', 'UserController@index');
    Route::post('/resetpwd', 'UserController@resetpwd');
    Route::post('/profiles', 'UserController@profiles');

    Route::resource('note', 'NoteController');

    Route::resource('friend', 'FriendController', ['only' => ['index', 'store', 'update', 'destroy']]);
    Route::get('friend/list', 'FriendController@get_list');

    Route::get('/chat', 'ChatController@getIndex');
    Route::resource('group', 'GroupController', ['only' => ['index', 'store', 'update', 'destroy']]);
});
