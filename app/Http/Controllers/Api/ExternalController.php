<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Api;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Tutor;

class ExternalController extends Controller
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

    public function exec($function, Request $request)
    {
        $this->$function($request);
    }

    /**
     * Входящая заявка
     */
    public function request($data)
    {
        // создаем нового клиента
        $client = Client::create([
            'name'  => $data->name,
            'phone' => cleanNumber($data->phone, true),
        ]);

        $new_tutor_id = Tutor::newTutorId($data->repetitor_id);

        // $comment = breakLines([($data->repetitor_id ? "Репетитор " . Tutor::newTutorId($data->repetitor_id) : null)])

        $comment = ($data->repetitor_id ? "Репетитор " . Tutor::newTutorId($data->repetitor_id) . "

" : '') . $data->message . "

Метро: " . $data->metro_name;

        // создаем заявку клиента
        $client->requests()->create([
            'comment'           => $comment,
        ]);
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
            'birth_year'        => $requst->year_of_birth,
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
			$fh = fopen(public_path() . Tutor::UPLOAD_DIR . $new_tutor_id . '.' . $extension, 'w');
			fwrite($fh, $request->photo);
			fclose($fh);
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
}
