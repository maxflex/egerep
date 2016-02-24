<?php

Route::post('login', 'LoginController@login');
Route::get('logout', 'LoginController@logout');

Route::group(['middleware' => ['web']], function () {
    Route::resource('tutors', 'TutorsController');
    Route::resource('requests', 'RequestsController');
    Route::resource('clients', 'ClientsController');

    Route::controllers([
        'transfer' => 'TransferController'
    ]);

    Route::group(['namespace' => 'Api', 'prefix' => 'api'], function () {
        Route::get('tutors/list', 'TutorsController@lists');
        Route::resource('tutors', 'TutorsController');

        Route::resource('requests', 'RequestsController');
        Route::resource('lists', 'RequestListsController');
        Route::resource('attachments', 'AttachmentsController');
        Route::resource('archives', 'ArchivesController');
        Route::resource('reviews', 'ReviewsController');
        Route::resource('clients', 'ClientsController');
        Route::resource('users', 'UsersController');
        Route::resource('comments', 'CommentsController');
        Route::resource('sms', 'SmsController');

        Route::post('external/{function}', 'ExternalController@exec'); // external API controller

        // Route::controllers([
        //     'external' => 'ExternalController', // external API controller
        // ]);
    });


    # Templates for angular directives
    Route::get('directives/{directive}', function($directive) {
        return view("directives.{$directive}");
    });
});
