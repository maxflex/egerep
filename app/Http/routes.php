<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::resource('tutors', 'TutorsController');
Route::resource('requests', 'RequestsController');
Route::resource('clients', 'ClientsController');

Route::group(['namespace' => 'Api', 'prefix' => 'api'], function () {
    Route::get('tutors/list', 'TutorsController@lists');
    Route::resource('tutors', 'TutorsController');
    Route::resource('requests', 'RequestsController');
    Route::resource('clients', 'ClientsController');
    Route::resource('users', 'UsersController');
});


# Templates for angular directives
Route::get('directives/{directive}', function($directive) {
    return view("directives.{$directive}");
});

// Route::group('prefix' => 'directives', function() {
//     Route::get
// });

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
