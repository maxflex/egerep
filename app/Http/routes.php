<?php

use App\Http\Middleware\LogUrlOpen;

Route::post('login', 'LoginController@login');
Route::get('logout', 'LoginController@logout');
Route::get('auth', 'AuthController@index');

Route::group(['middleware' => ['web', LogUrlOpen::class]], function () {
    Route::get('/', 'RequestsController@index');
    Route::get('auth/continue-session', 'AuthController@continueSession');

    Route::get('generate-pdf', function() {
       $data = \DB::connection('egecrm')->select("SELECT s.id as student_id, s.id_representative, c.*, r.first_name, r.last_name, r.middle_name
           from students s
           join representatives r on r.id = s.id_representative
           join contract_info ci on s.id = ci.id_student
           join contracts c on c.id_contract = ci.id_contract
           where ci.year=2017 and
               c.current_version=1 and
               exists(select 1 from contract_subjects cs where cs.id_contract = c.id and (cs.status in (2, 3)))
           group by s.id
           order by last_name asc, r.first_name asc, r.middle_name asc
       ");

       $all_conditions = [];
       $some_conditions = [];

       echo "<table>";
       foreach($data as $d) {
           // сумма платежей и возвратов по категории "обучение", отмеченных 2017-2018 гг. 
           // точно равняется сумме, указанной последней версии договора 2017-2018 гг. (условие 3)
           $query = dbEgecrm('payments')
               ->where('category', 1)
               ->where('year', 2017)
               ->where('entity_type', 'STUDENT')
               ->where('entity_id', $d->student_id);

           $payments = cloneQuery($query)->where('id_type', 1)->sum('sum');
           $returns  = cloneQuery($query)->where('id_type', 2)->sum('sum');
           // dump([($payments - $returns), $d->sum]);
           if (($payments - $returns) == $d->sum) {
               $all_conditions[] = $d;
// 				echo "<tr><td>{$d->student_id}</td><td>{$d->sum}</td></tr>";
// 				echo "<tr><td>{$d->last_name} {$d->first_name} {$d->middle_name}</td></tr>";
           } else {
               $some_conditions[] = $d;
//                 echo "<tr><td>{$d->student_id}</td><td>" . ($payments - $returns) . "</td></tr>";
               echo "<tr><td>{$d->last_name} {$d->first_name} {$d->middle_name}</td></tr>";
           }
       }
       echo "</table>";
       die;
// 		dd(count($all_conditions));

       // $dompdf->loadHtml(view('actprint', ['student_id' => 1]));
       // $dompdf->loadHtml('<html>
       // <body>
       // <h1>test</h1>
       // </body>
       // </html>');

       // instantiate and use the dompdf class
       $html = '';
//         dd(count($all_conditions));
       $all_conditions = array_slice($all_conditions, 600, 200);
       foreach($all_conditions as $d) {
           $d->first = dbEgecrm('contracts')->where('id_contract', $d->id_contract)->orderBy('id', 'asc')->first();
           $html .= view('actprint', compact('d'));
       }

       $dompdf = new Dompdf\Dompdf;
       $dompdf->loadHtml($html);
       $dompdf->setPaper('A4', 'portrait');
       $dompdf->render();
       $dompdf->stream();
   });


    Route::get('temp/{year}', 'TempController@index');

    Route::get('emergency', 'EmergencyController@index');

    Route::get('activity', 'ActivityController@index');

    Route::get('stream', 'StreamController@index');
    Route::get('stream/configurations', 'StreamController@configurations');

    Route::get('calls', 'CallsController@index');
    Route::get('calls/missed', 'CallsController@missed');
    Route::resource('calls', 'CallsController');

    Route::resource('sms', 'SmsController');

    Route::get('background', 'BackgroundController@index');
    Route::get('background/preview/{id}', 'BackgroundController@preview');

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

    Route::get('attendance', 'AttendanceController@index');

    Route::get('tutors/select', 'TutorsController@select');
    Route::resource('tutors', 'TutorsController');
    Route::resource('logs', 'LogsController');

    Route::group(['namespace' => 'Payments', 'prefix' => 'payments'], function() {
        Route::resource('expenditures', 'ExpendituresController');
        Route::resource('sources', 'SourcesController');
    });

    Route::get('payments/remainders', 'PaymentsController@remainders');
    Route::get('payments/export', 'PaymentsController@export');
    Route::post('payments/import', 'PaymentsController@import');
    Route::resource('payments', 'PaymentsController');

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
});

/**
 * API ROUTES
 */
Route::group(['namespace' => 'Api', 'prefix' => 'api', 'middleware' => ['web']], function () {
    Route::get('continue-session', function() {
        event(new App\Events\ContinueSession(\App\Models\User::id()));
    });
    Route::get('activity', 'ActivityController@index');
    Route::resource('markers', 'MarkersController');
    Route::get('notifications/get', 'NotificationsController@get');
    Route::resource('notifications', 'NotificationsController');
    Route::resource('logs', 'LogsController');
    Route::get('tutors/list', 'TutorsController@lists');
    Route::post('tutors/filtered', 'TutorsController@filtered');
    Route::post('tutors/select', 'TutorsController@select');
    Route::post('tutors/counts', 'TutorsController@counts');
    Route::post('tutors/merge', 'TutorsController@merge');
    Route::delete('tutors/photo/{id}', 'TutorsController@deletePhoto');
    Route::resource('tutors', 'TutorsController');
    Route::resource('background', 'BackgroundController');

    Route::post('attendance', 'AttendanceController@get');

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
    Route::get('sms/list', 'SmsController@listt');
    Route::resource('sms', 'SmsController');
    Route::resource('periods', 'PeriodsController');
    Route::resource('stream', 'StreamController');

    Route::resource('calls', 'CallsController');

    Route::post('summary/users', 'SummaryController@users');
    Route::post('summary/users/explain/tutors', 'SummaryController@explainTutors');
    Route::post('summary/users/explain', 'SummaryController@explain');
    Route::post('summary/payments', 'SummaryController@payments');
    Route::post('summary/debtors', 'SummaryController@debtors');
    Route::post('summary', 'SummaryController@index');
    Route::controllers([
        'command'  => 'CommandsController',
    ]);

    Route::post('payments/remainders', 'PaymentsController@remainders');
    Route::post('payments/stats', 'PaymentsController@stats');
    Route::post('payments/delete', 'PaymentsController@delete');

    Route::group(['namespace' => 'Payments', 'prefix' => 'payments'], function() {
        Route::resource('sources', 'SourcesController');
        Route::resource('source/remainders', 'SourceRemaindersController');
        Route::resource('expendituregroups', 'ExpenditureGroupsController');
        Route::resource('expenditures', 'ExpendituresController');
    });

    Route::resource('payments', 'PaymentsController');

    # шаблоны смс
    Route::get('template/{id}', 'TemplatesController@getTemplatesByType');
});

# API unprotected
Route::group(['namespace' => 'Api', 'prefix' => 'api'], function () {
    Route::controller('metro', 'MetroController');
    Route::post('external/{function}', 'ExternalController@exec'); // external API controller | DEPRICATED?
    Route::post('search', 'SearchController@search'); // external API controller | DEPRICATED?
});

# Templates for angular directives
Route::get('directives/{directive}', function($directive) {
    return view("directives.{$directive}");
})->middleware('web');
