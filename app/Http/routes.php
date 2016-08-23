<?php

Route::post('login', 'LoginController@login');
Route::get('logout', 'LoginController@logout');

# API unprotected
Route::group(['namespace' => 'Api', 'prefix' => 'api'], function () {
    Route::controller('metro', 'MetroController');
    Route::post('external/{function}', 'ExternalController@exec'); // external API controller | DEPRICATED?
    Route::post('_external/{function}', 'ApiExternalController@exec'); // external API controller | DEPRICATED?
});

Route::group(['middleware' => ['web']], function () {
    Route::resource('reviews', 'ReviewsController', ['only' => 'index']);

    Route::get('client/{id}', function($id) {
        $request_id = \App\Models\Request::where('client_id', $id)->value('id');
        if ($request_id) {
            return redirect()->to("https://lk.ege-repetitor.ru/requests/{$request_id}/edit");
        } else {
            return redirect()->to('/');
        }
    });
    Route::get('tutors/old/{id}', function($id) {
        $tutor = \App\Models\Tutor::where('id_a_pers', $id)->first();
        return redirect()->to("https://lk.ege-repetitor.ru/tutors/{$tutor->id}/edit");
    });
    Route::get('/', 'TutorsController@index');
    Route::get('tutors/select', 'TutorsController@select');
    Route::resource('tutors', 'TutorsController');
    Route::resource('logs', 'LogsController');
    Route::resource('requests', 'RequestsController', ['except' => ['index', 'show']]);
    Route::get('requests/{state_id?}', 'RequestsController@index');
    Route::resource('clients', 'ClientsController');
    Route::resource('periods', 'PeriodsController');
    Route::get('attachments', 'AttachmentsController@index');
    Route::get('attachments/stats/{month?}', 'AttachmentsController@stats');
    // Route::resource('debt', 'DebtController', ['only' => 'index']);
    Route::get('debt/map', 'DebtController@map');

    Route::get('summary/payments/{filter?}', 'SummaryController@payments');
    Route::get('summary/debtors/{filter?}', 'SummaryController@debtors');
    Route::get('summary/{filter?}', 'SummaryController@index');

    Route::controllers([
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
        Route::resource('markers', 'MarkersController');
        Route::resource('logs', 'LogsController');
        Route::get('tutors/list', 'TutorsController@lists');
        Route::post('tutors/filtered', 'TutorsController@filtered');
        Route::post('tutors/select', 'TutorsController@select');
        Route::post('tutors/counts', 'TutorsController@counts');
        Route::post('tutors/merge', 'TutorsController@merge');
        Route::delete('tutors/photo/{id}', 'TutorsController@deletePhoto');
        Route::resource('tutors', 'TutorsController');

        Route::post('requests/counts', 'RequestsController@counts');
        Route::post('requests/transfer/{id}', 'RequestsController@transfer');
        Route::resource('requests', 'RequestsController');
        Route::resource('lists', 'RequestListsController');
        Route::post('attachments/stats', 'AttachmentsController@stats');
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
        Route::resource('periods', 'PeriodsController');

        Route::post('summary/payments', 'SummaryController@payments');
        Route::post('summary/debtors', 'SummaryController@debtors');
        Route::post('summary', 'SummaryController@index');
        Route::controllers([
            'command'  => 'CommandsController',
        ]);
    });


    # Templates for angular directives
    Route::get('directives/{directive}', function($directive) {
        return view("directives.{$directive}");
    });
});
