<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\User;
use App\Models\Metro;
use App\Models\Api;
use Carbon\Carbon;

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
     * Перенести все данные
     */
    public function getData(Request $request)
    {
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
        $users = \DB::connection('egerep')->table('users')->select(['id', 'login', 'password'])->get();
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
}
