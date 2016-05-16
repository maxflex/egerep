<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\User;
use App\Models\Client;
use App\Models\Metro;
use App\Models\Api;
use App\Models\Comment;
use Carbon\Carbon;
use App\Models\Marker;
use DB;

class TransferController extends Controller
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


    // Соответствия пользователей локально
    const CO_USER_LOCAL = [
        12 => 1182,
        13 => 14,
        20 => 1183,
        35 => 1184,
        26 => 1185,
        28 => 1186,
        30 => 1187,
        40 => 1188,
        46 => 1189,
        43 => 1190,
        48 => 1191,
        49 => 1192,
        50 => 1193,
        57 => 1194,
        58 => 1195,
        55 => 1196,
        60 => 1197,
        62 => 1198,
        63 => 1199,
        66 => 1200,
        70 => 1201,
        72 => 1202,
        75 => 1203,
        73 => 1204,
        67 => 1205,
        74 => 1206,
        76 => 1207,
        78 => 1208,
        82 => 1209,
        80 => 1210,
        84 => 1211,
        85 => 1212,
        81 => 1213,
        89 => 1214,
        91 => 1215,
        86 => 1216,
        87 => 1217,
        88 => 1218,
    ];

	// Соответствия пользователей в реальной базе
	// в старой базе => в новой базе
	const CO_USER_REAL = [12 => 1304,13 => 1305,20 => 1306,35 => 1307,26 => 1308,28 => 1309,30 => 1310,40 => 1311,46 => 1312,43 => 1313,48 => 1314,49 => 1315,50 => 1316,57 => 1317,58 => 1318,55 => 1319,60 => 1320,62 => 1321,63 => 1322,66 => 1323,70 => 1324,72 => 1325,75 => 1326,73 => 1327,67 => 1328,74 => 1329,76 => 1330,78 => 1331,82 => 1332,80 => 1333,84 => 1334,85 => 1335,81 => 1336,89 => 1337,91 => 1338,86 => 1339,87 => 1340,88 => 1341,100 => 1342,104 => 1343,95 => 1344,96 => 1345,97 => 1346,109 => 1347,102 => 1348,99 => 1349,98 => 1350,106 => 1351,108 => 1352,113 => 1353,115 => 1354,120 => 1355,119 => 106,118 => 1356,117 => 1357,125 => 1358,123 => 104,114 => 108,116 => 1359,121 => 1360,122 => 1361,124 => 102,139 => 1380,126 => 100,142 => 1383,140 => 1381,141 => 1382,130 => 1370,131 => 1371,132 => 1372,138 => 1379,136 => 1376,135 => 1377,137 => 1378,134 => 1373,133 => 1374,128 => 1368,127 => 1367,129 => 1369,143 => 1384,144 => 1385];

	/**
	 * Перенести всех клиентов
	 */
	public function getClients(Request $request)
	{
		ini_set('max_execution_time', 0);
	    set_time_limit(0);

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
	 * @IMPORTANT: во время запуска добавить public $timestamps = false; в Request Model
	 */
	public function getRequests()
	{
		ini_set('max_execution_time', 0);
	    set_time_limit(0);

		$tasks = DB::connection('egerep')->table('tasks')->get();

		foreach ($tasks as $task) {
			$new_request = App\Models\Request::create([
				'id_a_pers'       => $task->id,
				'created_at'	  => $task->begin,
				'comment'		  => $task->description,
				'user_id'	      => static::CO_USER_REAL[$task->status_ico],
				'user_id_created' => static::CO_USER_REAL[$task->user_id],
				'state'			  => static::_convertRequestStatus($task->status),
				'client_id'       => Client::where('id_a_pers', $task->client_id)->pluck('id')->first(),
			]);
		}
	}

	/**
	 * Перенести поле контакты
	 */
	public function getContactsField(Request $request)
	{
		$tutors = Tutor::all();

		$updated = 0;

		foreach($tutors as $tutor) {
			$query = DB::connection('egerep')->table('repetitors')->select('contacts')->where('id', $tutor->id_a_pers);
			if ($query->exists()) {
				$updated++;
				$tutor->contacts = $query->first()->contacts;
				$tutor->save();
			}
		}

		dd($updated);
	}

	/**
	 * Есть ли клиенты у репетитора?
	 */
	public function getHasClients(Request $request)
	{
		$tutors = Tutor::all();

		$updated = 0;

		foreach($tutors as $tutor) {
			$query = DB::connection('egerep')->table('repetitor_clients')->where('repetitor_id', $tutor->id_a_pers);
			if ($query->exists()) {
				$updated++;
				$tutor->has_clients = true;
				$tutor->save();
			}
		}

		dd($updated);
	}


    /**
     * Перенести всех преподавателей (+фио)
     */
    public function getRepetitors(Request $request)
    {
        extract($request->input());
        $teachers = \DB::connection('egerep')->select("select * from repetitors limit {$limit} offset {$offset}");

        $transfered = 0;
        foreach ($teachers as $teacher) {
            $tutor = Tutor::where('id_a_pers', $teacher->id);
            // если преподавателя нет в базе
            if (!$tutor->exists()) {
                Tutor::create([
                    'id_a_pers'     => $teacher->id,
                    'first_name'    => $teacher->firstname,
                    'last_name'     => $teacher->lastname,
                    'middle_name'   => $teacher->middlename,
                ]);
                $transfered++;
            }
        }

        dd($transfered);
    }

	/**
	 * Перенести фотки
	 */
	public function getPhotos(Request $request)
	{
		$teachers = static::_getTeachers($request);

		$files_copied = 0;

		foreach ($teachers as $teacher) {
			$tutor = Tutor::where('id_a_pers', $teacher->id);
            if ($tutor->exists()) {
				$tutor_object = $tutor->first();

				$extension = @end(explode('.', $teacher->photo));

				if (!empty($teacher->photo)) {
					$files_copied++;
					static::_copyPhotos($extension, $teacher->id, $tutor_object->id);
					$tutor->update([
						'photo_extension' => $extension,
					]);
				}
			}
		}

		dd($files_copied);
	}

	private static function _copyPhotos($extension, $oldcrm_tutor_id, $newcrm_tutor_id)
	{
		@copy("/var/www/html/repetitors/htdocs/photo/photo_" . $oldcrm_tutor_id . "." . $extension,
			public_path() . Tutor::UPLOAD_DIR . $newcrm_tutor_id . '_original.' . $extension);

		@copy("/var/www/html/repetitors/htdocs/photo/photo_" . $oldcrm_tutor_id . ".r." . $extension,
			public_path() . Tutor::UPLOAD_DIR . $newcrm_tutor_id . '.' . $extension);

		@copy("/var/www/html/repetitors/htdocs/photo/photo_" . $oldcrm_tutor_id . ".r." . $extension,
			public_path() . Tutor::UPLOAD_DIR . $newcrm_tutor_id . '@2x.' . $extension);
	}

    /**
     * Обновить место для занятий, районы выезда.
     * @task    #775
     */
    public function getPlaceField(Request $request)
    {
        extract($request->input());
        $teachers = \DB::connection('egerep')->select("select * from repetitors limit {$limit} offset {$offset}");

        $transfered = 0;
        foreach ($teachers as $teacher) {
            $tutor = Tutor::where('id_a_pers', '=', $teacher->id)
                          ->whereNotIn('state', [4, 5]);
            if ($tutor->exists()) {
                // Контакты, места для занятий
                $contacts = $tutor->pluck('contacts')->first() . "
" . $teacher->place;

                $tutor = $tutor->update([
                    'contacts' => $contacts
                ]);
                $transfered++;
            }
        }
        dd($transfered);
        return view();
    }


    /**
     * Перенести все данные
     */
    public function getData(Request $request)
    {
		// @todo: delete all from markers, from metros
        extract($request->input());
        $teachers = \DB::connection('egerep')->select("select * from repetitors limit {$limit} offset {$offset}");

        $transfered = 0;
        foreach ($teachers as $teacher) {
            $tutor = Tutor::where('id_a_pers', $teacher->id);
            if ($tutor->exists()) {
                // $tutor = $tutor->first();
                /* ВЫСЧИТЫВАЕМЫЕ ВЕЛИЧИНЫ */

                // Общее впечатление
                $impression =  $teacher->advanced_info . "
" . $teacher->techbase_description . "
" . $teacher->commrate_description . "
" .$teacher->cooperation_description;

                // Средний возраст
                // $start_career_year = Carbon::now()->year - $teacher->experience + 500;

                // Опубликованная цена
                $public_price = round($teacher->min / 90 * $teacher->price_min); // приводим все цены к виду "за 90 минут"

                // Классы
                $grades = \DB::connection('egerep')->table('repetitor_client_groups')
                            ->where('repetitor_id', $teacher->id)->get();

                $new_grades = [];
                if (count($grades)) {
                    foreach ($grades as $grade) {
                        // студенты
                        if ($grade->group_id == 100) {
                            $new_grades[] = 11;
                        }
                        // остальные
                        else if ($grade->group_id == 101) {
                            $new_grades[] = 12;
                        } else {
                            $new_grades[] = $grade->group_id;
                        }
                    }
                }
                $grades = implode(',', $new_grades);

                // Перенос станций метро
                $new_places = [];
                $departure_price = 0;
                // ессли указано "выезд"
                if ($teacher->departure == "да") {
                    $places = \DB::connection('egerep')->table('repetitor_places')
                                ->where(['repetitor_id' => $teacher->id, 'type' => 'client'])->get();

                    if (count($places)) {
                        foreach ($places as $place) {
                            $new_places[] = $place->place_id;
                        }
                    }

                    switch($teacher->price_dep) {
                        case 1: {
                            $departure_price = 200;
                            break;
                        }
                        case 2: {
                            $departure_price = 300;
                            break;
                        }
                        case 3: {
                            $departure_price = 400;
                            break;
                        }
                        case 4: {
                            $departure_price = 500;
                            break;
                        }
                    }
                }

                $svg_map = implode(',', $new_places);

                // Предметы
                $subjects = \DB::connection('egerep')->table('repetitor_subjects')
                            ->where('repetitor_id', $teacher->id)->get();

                $new_subjects = [];
                if (count($subjects)) {
                    foreach ($subjects as $subject) {
                        switch($subject->subject_id) {
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
                        }
                    }
                }
                $subjects = implode(',', $new_subjects);

                // Метки
                $markers = \DB::connection('egerep')->table('geo')
                            ->where(['entity_id' => $teacher->id, 'entity_type' => 'repetitor'])->get();

                $new_markers = [];
                if (count($markers)) {
                    foreach ($markers as $marker) {
						$metros = Api::exec('metro', [
							'lat' => $marker->lat,
                            'lng' => $marker->lng,
						]);
                        $new_markers[] = [
                            'lat' => $marker->lat,
                            'lng' => $marker->lng,
                            'type' => $marker->type,
							'metros' => $metros,
                        ];
                    }
                }

                $tutor_object = $tutor->first();
                $tutor_object->markers = $new_markers;
                $tutor_object->save();

                // Контакты, места для занятий
                $contacts = $teacher->contacts . "
" . $teacher->place;

                $tutor = $tutor->update([
                    'education'         => $teacher->university_end,
                    'achievements'      => $teacher->degrees,
                    'preferences'       => $teacher->subjects_description,
                    'experience'        => $teacher->experience_work,
                    'current_work'      => $teacher->work,
                    'impression'        => $impression,
                    'schedule'          => $teacher->workload,
                    'email'             => $teacher->email,
                    'tb'                => $teacher->techbase,
                    'lk'                => $teacher->commrate,
                    'js'                => $teacher->cooperation,
                    'birth_year'        => $teacher->age,
                    'start_career_year' => $teacher->experience,
                    'public_price'      => $public_price,
                    'grades'            => $grades,
                    'gender'            => $teacher->sex == 1 ? 'male' : 'female',
                    'svg_map'           => $svg_map,
                    'subjects'          => $subjects,
                    'contacts'          => $contacts,
                    'price'             => $teacher->price,
                    'public_desc'       => $teacher->public_description,
                    'departure_price'   => $departure_price,
                    'tutoring_experience' => $teacher->experience_repetitor,
                    'students_category' => $teacher->clients_description,
                ]);

                $transfered++;
            }
        }

        dd($transfered);
        return view();
    }

    /**
     * Перенести пользователей и установить соответствия
     */
    public function getUsers()
    {
        $users = \DB::connection('egerep')->table('users')->select(['id', 'login', 'password', 'color'])->get();
        // dd($users);

        $correspondence = [];

        foreach ($users as $egerep_user) {
            $egecrm_user = User::where(['id' => $egerep_user->id, 'login' => $egerep_user->login])->first();

            // если пользователь не нашелся
            if ($egecrm_user === null) {
                // пытаемся найти пользователя по логину
                $egecrm_user = User::where('login', $egerep_user->login)->first();

                // если пользователь нашелся по логину
                if ($egecrm_user !== null) {
                    $correspondence[$egerep_user->id] = $egecrm_user->id;
                } else {
                    $new_user = User::create([
                        'login'     => $egerep_user->login,
                        'password'  => $egerep_user->password,
						'color'		=> $egerep_user->color,
                        'type'      => User::USER_TYPE,
                    ]);
                    $correspondence[$egerep_user->id] = $new_user->id;
                }
            }
        }

        // вывод
        echo '[';
        foreach ($correspondence as $egerep_user_id => $egecrm_user_id) {
            echo "$egerep_user_id => $egecrm_user_id,";
        }
        echo ']';
        // вывод

        dd($correspondence);
        return view();
    }

	/**
	 * Перенести комментарии преподавателей
	 * @important: перед переносом добавить:
	 * ...
	 * 		pulic $timestamps = false;
	 *		$fillable = [..., 'created_at', 'updated_at']
	 * ...
	 * в Comment.php
	 */
	public function getTeacherComments(Request $request)
	{
		extract($request->input());
		$comments = \DB::connection('egerep')->select("select * from repetitor_comments limit {$limit} offset {$offset}");

		$comments_transfered = 0;
		foreach ($comments as $comment) {
			$tutor_id = Tutor::where('id_a_pers', $comment->repetitor_id)->pluck('id')->first();
			if ($tutor_id > 0) {
				$comments_transfered++;
				Comment::create([
					'user_id' 		=> static::_getUserId($comment->user_id),
					'entity_id'		=> $tutor_id,
					'entity_type'	=> 'tutor',
					'comment'		=> $comment->text,
					'created_at'	=> $comment->time,
					'updated_at'	=> $comment->time,
				]);
			}
		}
		dd($comments_transfered);
		return view();
	}

	public function getTutorStatuses(Request $request)
	{
		extract($request->input());
 		$teachers = \DB::connection('egerep')->select("
			select id, fill_status, status_verified
			from repetitors
			where fill_status IN (3, 4, 6) and status_verified IN (1, 2, 4)
			limit {$limit} offset {$offset}
		");

		$updated = 0;
		foreach ($teachers as $teacher) {
			$tutor = Tutor::where('id_a_pers', $teacher->id);
			// если преподавателя нет в базе
			if ($tutor->exists()) {
				$state = null;

				if ($teacher->status_verified == 4 && ($teacher->fill_status == 3 || $teacher->fill_status == 6)) {
					$state = 4; // почти одобрено
				}

				if ($teacher->status_verified == 1 && $teacher->fill_status == 4) {
					$state = 3; // закрыто
				}

				if ($teacher->status_verified == 2 && $teacher->fill_status == 4) {
					$state = 2; // почти закрыто
				}

				if ($state !== null) {
					$updated++;
					$tutor->update(['state' => $state]);
				}
			}
		}
		dd($updated);
		return view();
	}

	private static function _getUserId($oldcrm_user_id)
	{
		if (@static::CO_USER_REAL[$oldcrm_user_id] !== null) {
			return static::CO_USER_REAL[$oldcrm_user_id];
		} else {
			return $oldcrm_user_id;
		}
	}

	private static function _getTeachers($request)
	{
		extract($request->input());
 		return \DB::connection('egerep')->select("select * from repetitors limit {$limit} offset {$offset}");
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
		if ($status == 9) {
			return 4;
		}
		if ($status == 1) {
			return 2;
		}
		if ($status == 2) {
			return 1;
		}
		return $status;
	}
}
