<?php

Route::post('login', 'LoginController@login');
Route::get('logout', 'LoginController@logout');

# API unprotected
Route::group(['namespace' => 'Api', 'prefix' => 'api'], function () {
    Route::controller('metro', 'MetroController');
    Route::post('external/{function}', 'ExternalController@exec'); // external API controller | DEPRICATED?
    Route::post('search', 'SearchController@search'); // external API controller | DEPRICATED?
});

Route::group(['middleware' => ['web']], function () {
    Route::get('/', 'RequestsController@index');
    Route::get('/fingerscan', function() {
        return \App\Models\Service\Fingerscan::get();
    });
    Route::get('temp/{year}', 'TempController@index');

    Route::get('emergency', 'EmergencyController@index');

    Route::get('stream', 'StreamController@index');
    Route::get('stream/configurations', 'StreamController@configurations');

    Route::get('calls/missed', 'CallsController@missed');
    Route::resource('calls', 'CallsController');

    Route::get('reviews/{id}', 'ReviewsController@tutor');
    Route::resource('reviews', 'ReviewsController', ['only' => 'index']);

    Route::get('client/{id}', function($id) {
        $request_id = \App\Models\Request::where('client_id', $id)->value('id');
        if ($request_id) {
            return redirect()->to("https://lk.ege-repetitor.ru/requests/{$request_id}/edit");
        } else {
            return redirect()->to('/');
        }
    });

    Route::get('attachment/{id}', function($id) {
        $attachment = \App\Models\Attachment::find($id);
        if ($attachment === null) {
            return redirect()->to('/');
        } else {
            return redirect()->to("https://lk.ege-repetitor.ru/" . $attachment->link);
        }
    });

    Route::get('archive/{id}', function($id) {
        $archive = \App\Models\Archive::find($id);
        if ($archive === null) {
            return redirect()->to('/');
        } else {
            return redirect()->to("https://lk.ege-repetitor.ru/" . $archive->attachment->link);
        }
    });

    Route::get('request-list/{id}', function($id) {
        $request_list = \App\Models\RequestList::find($id);
        if ($request_list === null) {
            return redirect()->to('/');
        } else {
            return redirect()->to("https://lk.ege-repetitor.ru/requests/" . $request_list->request_id . '/edit#' . $request_list->id);
        }
    });

    Route::get('tutors/select', 'TutorsController@select');
    Route::resource('tutors', 'TutorsController');
    Route::resource('logs', 'LogsController');
    Route::resource('requests', 'RequestsController', ['except' => ['index', 'show']]);
    Route::get('requests/errors', 'RequestsController@errors');
    Route::get('requests/{state_id?}', 'RequestsController@index');
    Route::resource('clients', 'ClientsController');
    Route::get('periods/planned', 'PeriodsController@planned');
    Route::get('periods/payments', 'PeriodsController@payments')->name('periods.payments');
    Route::resource('periods', 'PeriodsController');
    Route::get('archives', 'ArchivesController@index');
    Route::get('attachments', 'AttachmentsController@index');
    Route::get('attachments/new', 'AttachmentsController@newest');
    Route::get('attachments/stats/{month?}', 'AttachmentsController@stats');
    // Route::resource('debt', 'DebtController', ['only' => 'index']);
    Route::get('debt/map', 'DebtController@map');

    Route::get('notifications', 'NotificationsController@index');

    Route::get('summary/users', 'SummaryController@users');
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

    # Шаблоны
	Route::get('templates', 'TemplatesController@index');
	Route::post('templates', 'TemplatesController@save');

    Route::get('contract/edit', 'ContractController@edit');
    Route::resource('contract', 'ContractController');

    Route::group(['namespace' => 'Api', 'prefix' => 'api'], function () {
        Route::resource('markers', 'MarkersController');
        Route::get('notifications/get', 'NotificationsController@get');
        Route::resource('notifications', 'NotificationsController');
        Route::get('logs/graph', 'LogsController@graph');
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
        Route::get('attachments/new', 'AttachmentsController@newest');
        Route::resource('attachments', 'AttachmentsController');
        Route::post('debt/map', 'DebtController@map');
        Route::resource('debt', 'DebtController');
        Route::resource('archives', 'ArchivesController');
        Route::resource('reviews', 'ReviewsController');
        Route::resource('clients', 'ClientsController');
        Route::resource('users', 'UsersController');
        Route::resource('comments', 'CommentsController');
        Route::resource('account/payments', 'AccountPaymentsController');
        Route::resource('accounts', 'AccountsController');
        Route::resource('periods/planned', 'PlannedAccountsController');
        Route::resource('periods/payments', 'AccountPaymentsController');
        Route::resource('sms', 'SmsController');
        Route::resource('periods', 'PeriodsController');
        Route::resource('stream', 'StreamController');

        Route::post('summary/users', 'SummaryController@users');
        Route::post('summary/users/explain', 'SummaryController@explain');
        Route::post('summary/payments', 'SummaryController@payments');
        Route::post('summary/debtors', 'SummaryController@debtors');
        Route::post('summary', 'SummaryController@index');
        Route::controllers([
            'command'  => 'CommandsController',
        ]);

        # шаблоны смс
        Route::get('template/{id}', 'TemplatesController@getTemplatesByType');

    });


    # Templates for angular directives
    Route::get('directives/{directive}', function($directive) {
        return view("directives.{$directive}");
    });
});
