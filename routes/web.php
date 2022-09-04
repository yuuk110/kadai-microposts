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

Route::get('/', 'MicropostsController@index');

// ユーザ登録
Route::get('signup', 'Auth\RegisterController@showRegistrationForm')->name('signup.get');
Route::post('signup', 'Auth\RegisterController@register')->name('signup.post');

// ログイン認証
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login.post');
Route::get('logout', 'Auth\LoginController@logout')->name('logout.get');

// ユーザ機能
// userのindex showは、ログインしていないと見られないようにする
Route::group(['middleware' => ['auth']], function () {
    Route::resource('users', 'UsersController', ['only' => ['index', 'show', 'edit', 'update']]);

    Route::group(['prefix' => 'users/{id}'], function () {
        // users/{id}/follow
        Route::post('follow', 'UserFollowController@store')->name('user.follow');

        // users/{id}/delete
        Route::delete('unfollow', 'UserFollowController@destroy')->name('user.unfollow');

        // users/{id}/followings
        Route::get('followings', 'UsersController@followings')->name('users.followings');

        // users/{id}/followers
        Route::get('followers', 'UsersController@followers')->name('users.followers');

        // users/{id}/favorites
        Route::get('favorites', 'UsersController@favorites')->name('users.favorites');
    });

    Route::group(['prefix' => 'microposts/{id}'], function () {
        // microposts/{id}/favorite
        Route::post('favorite', 'FavoritesController@store')->name('favorites.favorite');

        // microposts/{id}/unfavorite
        Route::delete('unfavorite', 'FavoritesController@destroy')->name('favorites.unfavorite');
    });

    Route::resource('microposts', 'MicropostsController', ['only' => ['store', 'destroy']]);
});