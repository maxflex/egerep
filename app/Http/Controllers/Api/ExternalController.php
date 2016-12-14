<?php

namespace App\Http\Controllers\Api;

use App\Events\SmsStatusUpdate;
use Illuminate\Http\Request;

use App\Models\Api;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Tutor;
use Illuminate\Support\Facades\Redis;

class ExternalController extends Controller
{
    const URL = "http://web.ege-repetitor.ru:8086/uploads/";

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

    public function exec($function, Request $request)
    {
        $this->$function($request);
    }

    public function requestNew($data)
    {
        $phone = cleanNumber($data->phone, true);

		// Если в заявке номер телефона совпадает с номером телефона,
		// указанным в другой невыполненной заявке, то такие заявки нужно сливать
		$client = Client::findByPhone($phone);

		if (! $client->exists()) {
			// создаем нового клиента
	        $client = Client::create(compact('phone'));
		} else {
			$client = $client->orderBy('id', 'desc')->first();
		}

		// @todo: нужно сделать поиск среди всех заявок в списке "невыполненные"
		//		  а не только по заявкам клиента
		$new_request = $client->requests()->where('state', 'new');

		if ($new_request->exists()) {
			$new_request = $new_request->orderBy('created_at', 'desc')->first();
			$new_request->comment = "Репетитор " . $data->tutor_id . " " . $new_request->comment;
			$new_request->save();
		} else {
			$comment = breakLines([($data->tutor_id ? "Репетитор " . $data->tutor_id : null), $data->comment, "Имя: " . $data->name]);
			// создаем заявку клиента
	        $client->requests()->create([
	            'comment' 	=> $comment,
				'google_id'	=> $data->google_id,
	        ]);
		}
    }

    /**
     * Входящая заявка
     * удалить позже!!
     */
    public function request($data)
    {
		$phone = cleanNumber($data->phone, true);

		// Если в заявке номер телефона совпадает с номером телефона,
		// указанным в другой невыполненной заявке, то такие заявки нужно сливать
		$client = Client::findByPhone($phone);

		// ID преподавателя в новой базе
		if ($data->repetitor_id) {
            $new_tutor_id = Tutor::newTutorId($data->repetitor_id);
        }

		if (! $client->exists()) {
			// создаем нового клиента
	        $client = Client::create(compact('phone'));
		} else {
			$client = $client->orderBy('id', 'desc')->first();
		}

		// @todo: нужно сделать поиск среди всех заявок в списке "невыполненные"
		//		  а не только по заявкам клиента
		$new_request = $client->requests()->where('state', 'new');

		if ($new_request->exists()) {
			$new_request = $new_request->orderBy('created_at', 'desc')->first();
            if ($data->repetitor_id) {
                $new_request->comment = "Репетитор {$new_tutor_id} " . $new_request->comment;
            }
			$new_request->save();
		} else {
			$comment = breakLines([(@$new_tutor_id ? "Репетитор " . $new_tutor_id : null), $data->message, "Метро: " . $data->metro_name, "Имя: " . $data->name]);
			// создаем заявку клиента
	        $client->requests()->create([
	            'comment' 	=> $comment,
				'google_id'	=> $data->google_id,
	        ]);
		}
        Redis::incr('request_count'); // кол-во заявок должно равняться значению на сервере
    }

    /**
     * Входящий препод
     */
    public function tutor($request)
    {
        $new_tutor_id = Tutor::insertGetId([
            'first_name'        => $request->name,
            'last_name'         => $request->surname,
            'middle_name'       => $request->patronym,
            'contacts'          => breakLines([$request->mobile_phone, $request->city_phone, $request->contact]),
            'subjects'          => static::_subjects($request->subjects),
            'birth_year'        => $request->year_of_birth,
            'start_career_year' => $request->experience ? (date("Y") - $request->experience) : null,
            'education'         => $request->education,
            'achievements'      => $request->degrees,
            'price'             => $request->price,
        ]);

        if ($request->photo && $request->photo_meta)
		{
			$photo_meta = json_decode($request->photo_meta);
			if (!strpos($photo_meta->name, '.')) $photo_meta->name .= '.jpg';
			$extension = substr(strrchr($photo_meta->name, '.'), 1);
            Tutor::where('id', $new_tutor_id)->update([
                'photo_extension' => $extension,
            ]);
			$fh = fopen(public_path() . Tutor::UPLOAD_DIR . $new_tutor_id . '_original.' . $extension, 'w');
			fwrite($fh, $request->photo);
			fclose($fh);
		}
    }

    /**
     * Входящий препод новый
     */
    public function tutorNew($request)
    {
        $data = $request->input();

        if ($request->has('experience_years')) {
            $data['start_career_year'] = date('Y') - $data['experience_years'];
        }

        // согласие размещать анкету в психпортрет
        $data['impression'] = @$request->agree_to_publish ? ' я хотел бы получать учеников и даю согласие на публикацию анкеты на сайте ege-repetitor.ru' : 'я хотел бы получать учеников, но не хотел бы размещать анкету на сайте ege-repetitor.ru';

        if ($request->has('filename')) {
            $ext = @end(explode('.', $request->filename));
            $data['photo_extension'] = $ext;
        }

        $new_tutor = Tutor::create($data);

        // загружаем фото
        if ($request->has('filename')) {
            $file = file_get_contents(self::URL . $request->filename);
            file_put_contents(public_path() . Tutor::UPLOAD_DIR . $new_tutor->id . '_original.' . $ext, $file);
        }
    }

    /**
	 * Соответствие между предметами
	 */
	private static function _subjects($subjects)
	{
        $subjects = explode(',', $subjects);
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
     * Обновить статусы СМС. На самом деле запускается не кроном, а сервисом sms.ru
     *
     */
    public function updateSmsStatus($request)
    {
        foreach ($request->data as $entry) {
            $lines = explode("\n",$entry);
            if ($lines[0] == "sms_status") {

                $sms_id 	= $lines[1];
                $sms_status = $lines[2];

                \App\Models\Sms::where('id_smsru', $sms_id)->update([
                    'id_status' => $sms_status
                ]);

				event(new SmsStatusUpdate($sms_id, $sms_status));

                // "Изменение статуса. Сообщение: $sms_id. Новый статус: $sms_status";
                // Здесь вы можете уже выполнять любые действия над этими данными.
            }
        }
        exit("100"); /* Важно наличие этого блока, иначе наша система посчитает, что в вашем обработчике сбой */
    }
}
