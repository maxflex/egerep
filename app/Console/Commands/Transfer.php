<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Requests;
use App\Models\Tutor;
use App\Models\User;
use App\Models\Client;
use App\Models\Metro;
use App\Models\Api;
use App\Models\Comment;
use App\Models\RequestList;
use App\Models\Attachment;
use App\Models\Archive;
use App\Models\Review;
use App\Models\Account;
use App\Models\AccountData;
use App\Models\Marker;
use DB;

class Transfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:all {--clients} {--requests} {--request_comments} {--lists} {--attachments} {--accounts} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer data from old CRM';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('clients') || $this->option('all')) {
            $this->line('Transfering clients...');
			static::clients();
            $this->info('Clients transfered!');
		}
		if ($this->option('requests') || $this->option('all')) {
            $this->line('Transfering requests...');
			static::requests();
            $this->info('Requests transfered!');
		}
		if ($this->option('requestcomments') || $this->option('all')) {
            $this->line('Transfering request comments...');
			static::requestComments();
            $this->info('Request comments transfered!');
		}
		if ($this->option('lists') || $this->option('all')) {
            $this->line('Transfering lists...');
			static::lists();
            $this->info('Lists transfered!');
		}
		if ($this->option('attachments') || $this->option('all')) {
            $this->line('Transfering attachments...');
			static::attachments();
            $this->info('Attachments transfered!');
		}
		if ($this->option('accounts') || $this->option('all')) {
            $this->line('Transfering accounts...');
			static::accounts();
            $this->info('Accounts transfered!');
		}
    }

    /**
	 * Перенести всех клиентов
	 */
	public static function clients()
	{
		Client::truncate();
		DB::statement("ALTER TABLE `clients` AUTO_INCREMENT=1");
		Marker::where('markerable_type', 'App\Models\Client')->delete();

		$clients = DB::connection('egerep')->table('clients')->get();

		foreach($clients as $client) {
			$new_client = Client::create([
				'id_a_pers' 	=> $client->id,
				'address'		=> $client->description,
				'name'			=> $client->student_name,
				'grade'			=> static::_convertGrade($client->grade)
			]);

			// Создать метку
			$markers = DB::connection('egerep')->table('geo')
						->where('entity_type', 'client')->where('entity_id', $client->id)->get();

			if (count($markers)) {
				foreach($markers as $marker) {
					$new_marker = Marker::create([
						'markerable_id' 	=> $client->id,
						'markerable_type'	=> 'App\Models\Client',
						'lat'				=> $marker->lat,
						'lng'				=> $marker->lng,
						'type'				=> 'green',
					]);
					$new_marker->createMetros();
				}
			}
		}
	}

	/**
	 * Перенести все заявки
	 */
	public static function requests()
	{
		Request::truncate();
		DB::statement("ALTER TABLE `requests` AUTO_INCREMENT=1");

		$tasks = DB::connection('egerep')->table('tasks')->get();

		foreach ($tasks as $task) {
			\App\Models\Request::insert([
				'id_a_pers'       => $task->id,
				'comment'		  => $task->description,
				'user_id'	      => static::_userId($task->status_ico),
				'state'			  => static::_convertRequestStatus($task->status),
				'client_id'       => Client::where('id_a_pers', $task->client_id)->pluck('id')->first(),
				'created_at'	  => $task->begin,
				'user_id_created' => static::_userId($task->user_id),
			]);
		}
	}

	/**
	 * Перенести комментарии к заявке
	 */
	public static function requestComments()
	{
		Comment::where('entity_type', 'request')->delete();

		$comments = DB::connection('egerep')->table('task_comments')->get();

		$no_request = [];

		foreach ($comments as $comment) {
			$request_id = \App\Models\Request::where('id_a_pers', $comment->task_id)->pluck('id')->first();

			if ($request_id) {
				Comment::insert([
					'user_id' 		=> static::_userId($comment->user_id),
					'entity_type' 	=> 'request',
					'entity_id'		=> $request_id,
					'comment'		=> $comment->text,
					'created_at'	=> $comment->time,
					'updated_at'	=> $comment->time,
				]);
			} else {
				$no_request[] = $comment->id;
			}
		}
	}

	/**
	 * Перенести списки
	 * списки, которым не соответствующей заявки: 9, 3609, 3610, 3696, 14163, 14164
	 */
	public static function lists()
	{
		RequestList::truncate();
		DB::statement("ALTER TABLE `request_lists` AUTO_INCREMENT=1");

		$lists = DB::connection('egerep')->table('lists')->get();

		// списки, которым нет соответствующих заявок
		$no_request = [];

		foreach ($lists as $list) {
			$request_id = \App\Models\Request::where('id_a_pers', $list->task_id)->pluck('id')->first();

			if ($request_id) {
				$new_list = RequestList::insert([
					'request_id'	=> \App\Models\Request::where('id_a_pers', $list->task_id)->pluck('id')->first(),
					'subjects'		=> static::_subjects(explode('|', $list->subjects)),
					'tutor_ids'		=> static::_tutorIds(DB::connection('egerep')->table('list_repetitors')->where('list_id', $list->id)->pluck('repetitor_id')),
					'user_id'		=> static::_userId($list->user_id),
					'created_at' 	=> $list->time,
				]);
			} else {
				$no_request[] = $list->id;
			}
		}
	}

	/**
	 * Перенести стыковки
	 */
	public static function attachments()
	{
		DB::statement("DELETE FROM `attachments`");
		DB::statement("ALTER TABLE `attachments` AUTO_INCREMENT=1");
		DB::statement("DELETE FROM `reviews`");
		DB::statement("ALTER TABLE `reviews` AUTO_INCREMENT=1");
		DB::statement("DELETE FROM `archives`");
		DB::statement("ALTER TABLE `archives` AUTO_INCREMENT=1");


		$attachments = DB::connection('egerep')->table('repetitor_clients')->get();

		$no_tutor_ids = [];

		foreach ($attachments as $attachment) {
			$client_advanced = DB::connection('egerep')->table('client_advanced')
				->where('client_id', $attachment->client_id)
				->where('repetitor_id', $attachment->repetitor_id)
				->first();

/*
			if (! $client_advanced) {
				throw new \Exception("No client advanced model for {$attachment->client_id} + {$attachment->repetitor_id}");
			}
*/

			$new_crm_tutor_id = static::_tutorId($attachment->repetitor_id);

			if ($new_crm_tutor_id) {
				$request_list_id = RequestList::join('requests', 'requests.id', '=', 'request_lists.request_id')
									->where('requests.id_a_pers', $attachment->task_id)
									->whereRaw('FIND_IN_SET(' . $new_crm_tutor_id . ', request_lists.tutor_ids)')
									->pluck('request_lists.id')
									->first();

				if (! $request_list_id) {
					continue;
// 					throw new \Exception("No request_list_id for old_task_id: {$attachment->task_id} + new_crm_tutor_id: {$new_crm_tutor_id}");
				}

				$forecast = (($attachment->dohod == 0) ? ($attachment->summa * $attachment->num * 0.25) : ($attachment->num * $attachment->dohod));

				$new_attachment_id = Attachment::insertGetId([
					'user_id' 	=> static::_userId($attachment->user_id),
					'tutor_id'	=> $new_crm_tutor_id,
					'date'		=> $attachment->begin,
					'grade'		=> $client_advanced ? static::_convertGrade($client_advanced->client_group) : 0,
					'subjects'	=> $client_advanced ? implode(',', static::_subjects(explode(',', $client_advanced->subjects))) : '',
					'comment'	=> $attachment->description,
					'created_at'=> $attachment->created,
					'updated_at'=> $attachment->created,
					'forecast'	=> $forecast ? $forecast : null,
					'hide'		=> $attachment->hide,
					'request_list_id' => $request_list_id,
				]);

				// если заархивировано
				if ($attachment->archive) {
					Archive::insert([
						'attachment_id' 		=> $new_attachment_id,
						'date'					=> $attachment->end,
						'total_lessons_missing' => $attachment->archive == 1 ? 1 : null,
						'comment'				=> $attachment->archive_comment,
						'user_id'				=> static::_userId($attachment->archive_user_id),
						'created_at'			=> $attachment->archive_created,
						'updated_at'			=> $attachment->archive_created,
					]);
				}

				// если есть отзыв
				if ($attachment->opinion_user_id) {
					Review::insert([
						'attachment_id'		=> $new_attachment_id,
						'score'				=> static::_reviewScore($attachment->rating),
						'signature'			=> $attachment->opinion_signature,
						'comment'			=> $attachment->opinion,
						'user_id'			=> static::_userId($attachment->opinion_user_id),
						'state'				=> $attachment->opinion_public ? 'published' : 'unpublished',
						'created_at'		=> $attachment->opinion_created,
						'updated_at'		=> $attachment->opinion_created,
					]);
				}
			} else {
				$no_tutor_ids[] = $attachment->repetitor_id;
			}
		}
	}

	/**
	 * Перенести отчетность
	 */
	public static function accounts()
	{
		DB::statement("DELETE FROM `accounts`");
		DB::statement("ALTER TABLE `accounts` AUTO_INCREMENT=1");
		DB::statement("DELETE FROM `account_datas`");
		DB::statement("ALTER TABLE `account_datas` AUTO_INCREMENT=1");

		$periods = DB::connection('egerep')->table('periods')->get();

		foreach ($periods as $period) {
			$new_tutor_id = static::_tutorId($period->repetitor_id);

			if ($new_tutor_id) {
				$new_account_id = Account::insertGetId([
					'payment_method'	=> $period->money,
					'debt'				=> abs($period->zadol),
					'debt_type'			=> $period->zadol < 0 ? 1 : 0, // доплатил/переплатил
					'debt_before'		=> $period->was_in_debet,
					'received'			=> $period->summa,
					'comment'			=> $period->comments,
					'date_end'			=> $period->end,
					'tutor_id'			=> $new_tutor_id,
					'created_at'		=> $period->date_created,
					'updated_at'		=> $period->date_created,
				]);

				// данные таблицы отчетности
				$account_data = DB::connection('egerep')->table('lessons')
									->where('repetitor_id', $period->repetitor_id)
									->get();

				foreach ($account_data as $ad) {
					AccountData::insert([
						'tutor_id' 	=> $new_tutor_id,
						'client_id'	=> static::_clientId($ad->client_id),
						'date'		=> $ad->date,
						'sum'		=> $ad->summa,
						'commission'=> $ad->dohod,
					]);
				}
			}
		}
	}
}
