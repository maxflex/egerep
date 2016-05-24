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
    # Список предметов
	const MATH 		= 1;
	const PHYSICS	= 2;
	const CHEMISTRY	= 3;
	const BIOLOGY	= 4;
	const COMPUTER	= 5;
	const RUSSIAN	= 6;
	const LITERATURE= 7;
	const SOCIETY	= 8;
	const HISTORY	= 9;
	const ENGLISH	= 10;
	const UNKNOWN 	= 11;

	# Все предметы
	static $subjects = [
		self::MATH 		=> "математика",
		self::PHYSICS	=> "физика",
		self::RUSSIAN	=> "русский",
		self::LITERATURE=> "литература",
		self::ENGLISH	=> "английский",
		self::HISTORY	=> "история",
		self::SOCIETY	=> "обществознание",
		self::CHEMISTRY	=> "химия",
		self::BIOLOGY	=> "биология",
		self::COMPUTER	=> "информатика",
    ];


	// Соответствия пользователей в реальной базе
	// в старой базе => в новой базе
	const CO_USER_REAL = [12 => 1304,13 => 1305,20 => 1306,35 => 1307,26 => 1308,28 => 1309,30 => 1310,40 => 1311,46 => 1312,43 => 1313,48 => 1314,49 => 1315,50 => 1316,57 => 1317,58 => 1318,55 => 1319,60 => 1320,62 => 1321,63 => 1322,66 => 1323,70 => 1324,72 => 1325,75 => 1326,73 => 1327,67 => 1328,74 => 1329,76 => 1330,78 => 1331,82 => 1332,80 => 1333,84 => 1334,85 => 1335,81 => 1336,89 => 1337,91 => 1338,86 => 1339,87 => 1340,88 => 1341,100 => 1342,104 => 1343,95 => 1344,96 => 1345,97 => 1346,109 => 1347,102 => 1348,99 => 1349,98 => 1350,106 => 1351,108 => 1352,113 => 1353,115 => 1354,120 => 1355,119 => 106,118 => 1356,117 => 1357,125 => 1358,123 => 104,114 => 108,116 => 1359,121 => 1360,122 => 1361,124 => 102,139 => 1380,126 => 100,142 => 1383,140 => 1381,141 => 1382,130 => 1370,131 => 1371,132 => 1372,138 => 1379,136 => 1376,135 => 1377,137 => 1378,134 => 1373,133 => 1374,128 => 1368,127 => 1367,129 => 1369,143 => 1384,144 => 1385,145 => 1389];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:all {--clients} {--requests} {--request_comments} {--lists} {--attachments} {--attachment_comments} {--accounts} {--all}';

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
        $total_time = microtime(true);

        if ($this->option('clients') || $this->option('all')) {
            $t = microtime(true);
            $this->line('Transfering clients...');
			$this->clients();
            $this->info('Clients transfered!');
            $this->info('Time: ' . (microtime(true) - $t) . 's');
        }
		if ($this->option('requests') || $this->option('all')) {
            $t = microtime(true);
            $this->line('Transfering requests...');
			$this->requests();
            $this->info('Requests transfered!');
            $this->info('Time: ' . (microtime(true) - $t) . 's');
        }
		if ($this->option('request_comments') || $this->option('all')) {
            $t = microtime(true);
            $this->line('Transfering request comments...');
			$this->requestComments();
            $this->info('Request comments transfered!');
            $this->info('Time: ' . (microtime(true) - $t) . 's');
        }
		if ($this->option('lists') || $this->option('all')) {
            $t = microtime(true);
            $this->line('Transfering lists...');
			$this->lists();
            $this->info('Lists transfered!');
            $this->info('Time: ' . (microtime(true) - $t) . 's');
        }
		if ($this->option('attachments') || $this->option('all')) {
            $t = microtime(true);
            $this->line('Transfering attachments...');
			$this->attachments();
            $this->info('Attachments transfered!');
            $this->info('Time: ' . (microtime(true) - $t) . 's');
		}
		if ($this->option('attachment_comments') || $this->option('all')) {
            $t = microtime(true);
            $this->line('Transfering attachment comments...');
			$this->attachmentComments();
            $this->info('Attachment comments transfered!');
            $this->info('Time: ' . (microtime(true) - $t) . 's');
		}
		if ($this->option('accounts') || $this->option('all')) {
            $ac = microtime(true);
            $this->line('Transfering accounts...');
			$this->accounts();
            $this->info('Accounts transfered!');
            $this->info('Time: ' . (microtime(true) - $t) . 's');
		}

        $this->info('Total: ' . (microtime(true) - $total_time) . 's');
    }

    /**
	 * Перенести всех клиентов
	 */
	public function clients()
	{
		DB::statement("DELETE FROM `clients`");
		DB::statement("ALTER TABLE `clients` AUTO_INCREMENT=1");
		Marker::where('markerable_type', 'App\Models\Client')->delete();

		$clients = DB::connection('egerep')->table('clients')->get();
		$bar = $this->output->createProgressBar(count($clients));

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
						'markerable_id' 	=> $new_client->id,
						'markerable_type'	=> 'App\Models\Client',
						'lat'				=> $marker->lat,
						'lng'				=> $marker->lng,
						'type'				=> 'green',
					]);
					$new_marker->createMetros();
				}
			}
			$bar->advance();
		}

		$bar->finish();
	}

	/**
	 * Перенести все заявки
	 */
	public function requests()
	{
		DB::statement("DELETE FROM `requests`");
		DB::statement("ALTER TABLE `requests` AUTO_INCREMENT=1");

		$tasks = DB::connection('egerep')->table('tasks')->get();
		$bar = $this->output->createProgressBar(count($tasks));

		foreach ($tasks as $task) {
			\App\Models\Request::insert([
				'id_a_pers'       => $task->id,
				'comment'		  => static::_tutorIdsInText(static::_htmlReplace($task->description)),
				'user_id'	      => static::_userId($task->status_ico),
				'state'			  => static::_convertRequestStatus($task->status),
				'client_id'       => Client::where('id_a_pers', $task->client_id)->pluck('id')->first(),
				'created_at'	  => $task->begin,
				'user_id_created' => static::_userId($task->user_id),
			]);
			$bar->advance();
		}

		$bar->finish();
	}

	/**
	 * Перенести комментарии к заявке
	 */
	public function requestComments()
	{
		Comment::where('entity_type', 'request')->delete();

		$comments = DB::connection('egerep')->table('task_comments')->get();
		$bar = $this->output->createProgressBar(count($comments));

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
			$bar->advance();
		}

		$bar->finish();
	}

	/**
	 * Перенести комментарии к заявке
	 */
	public function attachmentComments()
	{
		Comment::where('entity_type', 'attachment')->delete();

		$comments = DB::connection('egerep')->table('client_comments')->get();
		$bar = $this->output->createProgressBar(count($comments));

		foreach ($comments as $comment) {
			$tutor_id = static::_tutorId($comment->repetitor_id);

			if ($tutor_id) {
				$attachment_id = Attachment::join('request_lists', 'request_lists.id', '=', 'attachments.request_list_id')
									->join('requests', 'requests.id', '=', 'request_lists.request_id')
									->where('requests.client_id', static::_clientId($comment->client_id))
									->where('attachments.tutor_id', $tutor_id)
									->pluck('attachments.id')->first();

				if ($attachment_id) {
					Comment::insert([
						'user_id' 		=> static::_userId($comment->user_id),
						'entity_type' 	=> 'attachment',
						'entity_id'		=> $attachment_id,
						'comment'		=> $comment->text,
						'created_at'	=> $comment->time,
						'updated_at'	=> $comment->time,
					]);
				}
			}
			$bar->advance();
		}

		$bar->finish();
	}


	/**
	 * Перенести списки
	 * списки, которым не соответствующей заявки: 9, 3609, 3610, 3696, 14163, 14164
	 */
	public function lists()
	{
		DB::statement("DELETE FROM `request_lists`");
		DB::statement("ALTER TABLE `request_lists` AUTO_INCREMENT=1");

		$lists = DB::connection('egerep')->table('lists')->get();
		$bar = $this->output->createProgressBar(count($lists));

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
			$bar->advance();
		}
		$bar->finish();
	}

	/**
	 * Перенести стыковки
	 */
	public function attachments()
	{
		DB::statement("DELETE FROM `attachments`");
		DB::statement("ALTER TABLE `attachments` AUTO_INCREMENT=1");
		DB::statement("DELETE FROM `reviews`");
		DB::statement("ALTER TABLE `reviews` AUTO_INCREMENT=1");
		DB::statement("DELETE FROM `archives`");
		DB::statement("ALTER TABLE `archives` AUTO_INCREMENT=1");


		$attachments = DB::connection('egerep')->table('repetitor_clients')->get();
		$bar = $this->output->createProgressBar(count($attachments));

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
					'subjects'	=> $client_advanced ? static::_subjects(explode(',', $client_advanced->subjects)) : '',
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
			$bar->advance();
		}

		$bar->finish();
	}

	/**
	 * Перенести отчетность
	 */
	public function accounts()
	{
		DB::statement("DELETE FROM `accounts`");
		DB::statement("ALTER TABLE `accounts` AUTO_INCREMENT=1");
		DB::statement("DELETE FROM `account_datas`");
		DB::statement("ALTER TABLE `account_datas` AUTO_INCREMENT=1");

		$periods = DB::connection('egerep')->table('periods')->get();
		$bar = $this->output->createProgressBar(count($periods));

		// ID преподавателей, по которым отчетность перенесена
		$transfered_tutor_ids = [];

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

				if (in_array($period->repetitor_id, $transfered_tutor_ids)) {
					continue;
				}

				$transfered_tutor_ids[] = $period->repetitor_id;

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
			$bar->advance();
		}

		$bar->finish();
	}





    private static function _getUserId($oldcrm_user_id)
	{
		if (@static::CO_USER_REAL[$oldcrm_user_id] !== null) {
			return static::CO_USER_REAL[$oldcrm_user_id];
		} else {
			return $oldcrm_user_id;
		}
	}

	/**
	 * Конвертировать класс
	 */
	private static function _convertGrade($grade)
	{
		if ($grade == 100) {
			return 12;
		} else
		if ($grade == 101) {
			return 13;
		}
		return $grade;
	}

	/**
	 * Конвертировать статус заявки
	 */
	private static function _convertRequestStatus($status)
	{
		switch ($status) {
			case 0: {
				return 'new';
			}
			case 1: {
				return 'finished';
			}
			case 2: {
				return 'awaiting';
			}
			case 3: {
				return 'deny';
			}
			case 9: {
				return 'spam';
			}
		}
	}

	/**
	 * Соответствие межу ID пользователя
	 */
	private static function _userId($old_crm_user_id)
	{
		if (array_key_exists($old_crm_user_id, static::CO_USER_REAL)) {
			return static::CO_USER_REAL[$old_crm_user_id];
		} else {
			return $old_crm_user_id;
		}
	}

	/**
	 * Соответствие между предметами
	 */
	private static function _subjects($subjects)
	{
		$new_subjects = [];
		if (count($subjects)) {
			foreach ($subjects as $subject_id) {
				switch($subject_id) {
					case 2: {
						$new_subjects[] = static::MATH;
						break;
					}
					case 3: {
						$new_subjects[] = static::PHYSICS;
						break;
					}
					case 7: {
						$new_subjects[] = static::CHEMISTRY;
						break;
					}
					case 4: {
						$new_subjects[] = static::BIOLOGY;
						break;
					}
					case 12: {
						$new_subjects[] = static::COMPUTER;
						break;
					}
					case 10: {
						$new_subjects[] = static::RUSSIAN;
						break;
					}
					case 13: {
						$new_subjects[] = static::LITERATURE;
						break;
					}
					case 1: {
						$new_subjects[] = static::SOCIETY;
						break;
					}
					case 5: {
						$new_subjects[] = static::HISTORY;
						break;
					}
					case 9: {
						$new_subjects[] = static::ENGLISH;
						break;
					}
					default: {
						$new_subjects[] = static::UNKNOWN;
						break;
					}
				}
			}
		}
		return implode(',', $new_subjects);
	}

	/**
	 * Соответствия межу ID преподавателей
	 */
	private static function _tutorIds($tutor_ids)
	{
		$new_tutor_ids = [];
		if (count($tutor_ids)) {
			foreach ($tutor_ids as $tutor_id) {
				$new_tutor_id = Tutor::where('id_a_pers', $tutor_id)->pluck('id')->first();
				if ($new_tutor_id) {
					$new_tutor_ids[] = $new_tutor_id;
				}
			}
		}
		return implode(',', $new_tutor_ids);
	}

	/**
	 * Соответствия межу ID преподавателей
	 */
	private static function _tutorId($tutor_id)
	{
		$new_tutor_id = Tutor::where('id_a_pers', $tutor_id)->pluck('id')->first();
		return $new_tutor_id ? $new_tutor_id : null;
	}

	/**
	 * Соответствия межу ID клиента
	 */
	private static function _clientId($client_id)
	{
		$new_client_id = Client::where('id_a_pers', $client_id)->pluck('id')->first();
		return $new_client_id ? $new_client_id : null;
	}

	/**
	 * Оценка в отзыве
	 */
	private static function _reviewScore($score)
	{
		if ($score < 0) {
			return 11;
		} else {
			return $score;
		}
	}

	/**
	 * Заменить html-entities
	 */
	private static function _htmlReplace($text)
	{
		$text = str_replace('&quot;', '"', $text);
		$text = str_replace('&nbsp;', ' ', $text);
		return $text;
	}

	/**
	 * Заменить старые ID репетиторов внутри текста
	 */
	private static function _tutorIdsInText($text)
	{
		preg_match_all('/(репетитор [\d]+)/ui', $text, $matches);

		$new_text = $text;

		if (count($matches[0])) {
			foreach ($matches[0] as $m) {
				$tutor_id     = filter_var($m, FILTER_SANITIZE_NUMBER_INT);
				$new_tutor_id = static::_tutorId($tutor_id);
				$new_m        = str_replace($tutor_id, $new_tutor_id, $m);
				$new_text	  = preg_replace("/{$m}\b/iu", $new_m, $new_text);
			}
		}

		return $new_text;
	}
}
