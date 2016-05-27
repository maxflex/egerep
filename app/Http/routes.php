<?php

Route::post('login', 'LoginController@login');
Route::get('logout', 'LoginController@logout');

# API unprotected
Route::group(['namespace' => 'Api', 'prefix' => 'api'], function () {
    Route::controller('metro', 'MetroController');
    Route::post('external/{function}', 'ExternalController@exec'); // external API controller | DEPRICATED?
});

Route::group(['middleware' => ['web']], function () {
    Route::get('/', 'TutorsController@index');
    Route::resource('tutors', 'TutorsController');
    Route::get('requests/{state_id?}', 'RequestsController@index');
    Route::resource('requests', 'RequestsController', ['except' => ['index']]);
    Route::resource('clients', 'ClientsController');
    Route::get('attachments/{state?}', 'AttachmentsController@index');
    // Route::resource('debt', 'DebtController', ['only' => 'index']);
    Route::get('debt/map', 'DebtController@map');

    Route::get('summary', 'SummaryController@index');

    Route::resource('debt', 'DebtController');


    Route::get('summary', 'SummaryController@index');

    Route::controllers([
        'transfer'  => 'TransferController',
        'upload'    => 'UploadController',
        'command'   => 'CommandsController',
        'graph'     => 'GraphController',
    ]);

    # Добавление из списка
    Route::get('tutors/add/{id}', 'TutorsController@addToList');

    Route::get('tutors/{id}/accounts', 'AccountsController@index');
    Route::get('tutors/{id}/accounts/hidden', 'AccountsController@hidden');

	# Поиск по преподам
	Route::post('search', 'TutorsController@index');

    Route::group(['namespace' => 'Api', 'prefix' => 'api'], function () {
        Route::get('tutors/list', 'TutorsController@lists');
        Route::post('tutors/filtered', 'TutorsController@filtered');
        Route::post('tutors/counts', 'TutorsController@counts');
        Route::delete('tutors/photo/{id}', 'TutorsController@deletePhoto');
        Route::resource('tutors', 'TutorsController');

        Route::post('requests/counts', 'RequestsController@counts');
        Route::resource('requests', 'RequestsController');
        Route::resource('lists', 'RequestListsController');
        Route::resource('attachments', 'AttachmentsController');
        Route::post('debt/map', 'DebtController@map');
        Route::resource('debt', 'DebtController');
        Route::resource('archives', 'ArchivesController');
        Route::resource('reviews', 'ReviewsController');
        Route::resource('clients', 'ClientsController');
        Route::resource('users', 'UsersController');
        Route::resource('comments', 'CommentsController');
        Route::resource('accounts', 'AccountsController');
        Route::resource('sms', 'SmsController');

        Route::post('summary', 'SummaryController@index');
    });


    # Templates for angular directives
    Route::get('directives/{directive}', function($directive) {
        return view("directives.{$directive}");
    });
});
