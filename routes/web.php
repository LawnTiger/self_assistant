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
Route::group(['namespace' => 'Auth'], function () {
    Route::get('/login', 'LoginController@index')->name('user.login');
    Route::post('/login', 'LoginController@login');
    Route::get('/logout', 'LoginController@logout');
    Route::get('/register', 'RegisterController@index');
    Route::post('/register', 'RegisterController@register');
});

// user
Route::group(['middleware' => 'auth'], function() {
    Route::get('/user', 'UserController@index');
    Route::post('/resetpwd', 'UserController@resetpwd');
    Route::post('/profiles', 'UserController@profiles');

    Route::resource('note', 'NoteController');

    Route::resource('friend', 'FriendController', ['only' => ['index', 'store', 'update', 'destroy']]);
    Route::get('friend/list', 'FriendController@get_list');

    Route::get('/chat', 'ChatController@getIndex');
    Route::resource('group', 'GroupController', ['only' => ['index', 'store', 'update', 'show']]);
});

// admin
Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {
    Route::get('login', 'LoginController@getIndex');
    Route::post('login', 'LoginController@postIndex');
    Route::get('out', 'LoginController@out');

    Route::group(['middleware' => 'auth.admin'], function () {
        Route::get('user', 'UserController@index');
    });
});
